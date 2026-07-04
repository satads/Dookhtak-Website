<?php
/**
 * SEO layer + request middleware (Phase 6).
 *
 * - seo_settings() / seo_page(): cached reads of the seo_settings and
 *   seo_pages tables.
 * - seo_meta(): resolves the effective title/description/canonical/og
 *   image for a page from opts + DB, with fallbacks.
 * - seo_emit_head() / seo_emit_body_scripts(): print the <head> SEO block
 *   (called by layout.php) and the trailing body scripts.
 * - seo_jsonld(): render one JSON-LD block.
 * - seo_redirect_check(): 301/302 middleware over the redirects table.
 * - public_boot(): redirect check + page-cache serve/capture; called at
 *   the very top of every public entry script.
 *
 * head_scripts / body_scripts are stored by the admin and injected raw
 * (trusted admin HTML, same trust model as blog body_html / enamad_code).
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/cache.php';

/** All seo_settings as key=>value, memoised per request. */
function seo_settings(): array
{
    static $cache = null;
    if ($cache === null) {
        $cache = [];
        try {
            foreach (db()->query('SELECT `key`, `value` FROM seo_settings') as $r) {
                $cache[$r['key']] = $r['value'];
            }
        } catch (Throwable $e) {
            error_log('seo_settings load failed: ' . $e->getMessage());
        }
    }
    return $cache;
}

/** One seo_settings value with default. */
function seo_setting(string $key, string $default = ''): string
{
    $s = seo_settings();
    return isset($s[$key]) && $s[$key] !== '' ? (string) $s[$key] : $default;
}

/** One seo_pages row by page_key, or null. Memoised per request. */
function seo_page(string $page_key): ?array
{
    static $cache = [];
    if (!array_key_exists($page_key, $cache)) {
        $cache[$page_key] = null;
        try {
            $stmt = db()->prepare('SELECT meta_title, meta_description, og_image FROM seo_pages WHERE page_key = ?');
            $stmt->execute([$page_key]);
            $cache[$page_key] = $stmt->fetch() ?: null;
        } catch (Throwable $e) {
            error_log('seo_page load failed: ' . $e->getMessage());
        }
    }
    return $cache[$page_key];
}

/** Absolute URL for the current request (canonical default). */
function seo_current_url(): string
{
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    return rtrim(BASE_URL, '/') . $path;
}

/**
 * Resolve effective meta for a page.
 * $opts keys: page_key, title, description, canonical, og_image, robots.
 * Returns [title, description, canonical, og_image, robots].
 */
function seo_meta(array $opts): array
{
    $suffix = seo_setting('site_title_suffix', ' | دوختک');
    $row = !empty($opts['page_key']) ? seo_page($opts['page_key']) : null;

    // Title: explicit opt > per-page meta_title > (bare title + suffix) > default.
    if (!empty($opts['title'])) {
        $title = $opts['title'];
    } elseif ($row && !empty($row['meta_title'])) {
        $title = $row['meta_title'];
    } else {
        $title = 'دوختک — سامانه ابری مدیریت خیاطی و مزون';
    }

    $description = $opts['description']
        ?? ($row['meta_description'] ?? null)
        ?? seo_setting('default_meta_description', '');

    $canonical = $opts['canonical'] ?? seo_current_url();

    // og:image fallback chain: page opt > per-page > default setting > logo.
    $og = $opts['og_image']
        ?? ($row['og_image'] ?? null)
        ?: seo_setting('default_og_image', '');
    if ($og === '') {
        $og = rtrim(BASE_URL, '/') . '/assets/images/Logo01-NEW.png';
    } elseif (!preg_match('#^https?://#', $og)) {
        $og = rtrim(BASE_URL, '/') . '/' . ltrim($og, '/');
    }

    return [
        'title'       => $title,
        'description' => (string) $description,
        'canonical'   => $canonical,
        'og_image'    => $og,
        'robots'      => $opts['robots'] ?? '',
    ];
}

/** Render one JSON-LD <script> block from an associative array. */
function seo_jsonld(array $data): string
{
    $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if ($json === false) {
        return '';
    }
    // Escape only the sequence that could break out of the script element.
    $json = str_replace('</', '<\/', $json);
    return '<script type="application/ld+json">' . $json . '</script>';
}

/**
 * Emit the SEO portion of <head>: description, canonical, OG, twitter,
 * robots, admin head_scripts, and any JSON-LD blocks.
 * (The <title> and <meta description> are printed by layout.php from the
 * same resolved values to keep a single source.)
 */
function seo_emit_head(array $meta, array $jsonld = []): void
{
    $og_type = $meta['og_type'] ?? 'website';
    ?>
<link rel="canonical" href="<?= e($meta['canonical']) ?>">
<?php if (!empty($meta['robots'])): ?>
<meta name="robots" content="<?= e($meta['robots']) ?>">
<?php endif; ?>
<meta property="og:type" content="<?= e($og_type) ?>">
<meta property="og:site_name" content="دوختک">
<meta property="og:title" content="<?= e($meta['title']) ?>">
<meta property="og:description" content="<?= e($meta['description']) ?>">
<meta property="og:url" content="<?= e($meta['canonical']) ?>">
<meta property="og:image" content="<?= e($meta['og_image']) ?>">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?= e($meta['title']) ?>">
<meta name="twitter:description" content="<?= e($meta['description']) ?>">
<meta name="twitter:image" content="<?= e($meta['og_image']) ?>">
<?php
    foreach ($jsonld as $block) {
        if (is_array($block) && $block) {
            echo "\n" . seo_jsonld($block);
        }
    }
    $head = seo_setting('head_scripts', '');
    if (trim($head) !== '') {
        echo "\n" . $head; // trusted admin HTML
    }
}

/** Emit admin body_scripts just before </body>. */
function seo_emit_body_scripts(): void
{
    $body = seo_setting('body_scripts', '');
    if (trim($body) !== '') {
        echo "\n" . $body; // trusted admin HTML
    }
}

/** Organization JSON-LD (site-wide identity). */
function seo_org_jsonld(): array
{
    $base = rtrim(BASE_URL, '/');
    return [
        '@context' => 'https://schema.org',
        '@type'    => 'Organization',
        'name'     => 'دوختک',
        'url'      => $base,
        'logo'     => $base . '/assets/images/Logo01-NEW.png',
    ];
}

/** WebSite JSON-LD. */
function seo_website_jsonld(): array
{
    $base = rtrim(BASE_URL, '/');
    return [
        '@context' => 'https://schema.org',
        '@type'    => 'WebSite',
        'name'     => 'دوختک',
        'url'      => $base,
    ];
}

/**
 * FAQPage JSON-LD from [['q'=>..,'a'=>..], ...]. Answers are plain text.
 * Returns [] when there are no questions (caller skips the empty block).
 */
function seo_faq_jsonld(array $faqs): array
{
    $items = [];
    foreach ($faqs as $f) {
        $q = trim((string) ($f['q'] ?? ''));
        $a = trim((string) ($f['a'] ?? ''));
        if ($q === '') {
            continue;
        }
        $items[] = [
            '@type'          => 'Question',
            'name'           => $q,
            'acceptedAnswer' => ['@type' => 'Answer', 'text' => $a],
        ];
    }
    if (!$items) {
        return [];
    }
    return ['@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => $items];
}

/** BreadcrumbList JSON-LD from [[name, url], ...] (url may be relative). */
function seo_breadcrumb_jsonld(array $crumbs): array
{
    $base = rtrim(BASE_URL, '/');
    $list = [];
    $pos = 1;
    foreach ($crumbs as [$name, $url]) {
        if ($url !== '' && !preg_match('#^https?://#', $url)) {
            $url = $base . '/' . ltrim($url, '/');
        }
        $list[] = ['@type' => 'ListItem', 'position' => $pos++, 'name' => $name, 'item' => $url];
    }
    return ['@context' => 'https://schema.org', '@type' => 'BreadcrumbList', 'itemListElement' => $list];
}

/**
 * Redirect middleware. If the current path matches a redirects.from_path,
 * send the configured 301/302 and exit. Runs before any output.
 */
function seo_redirect_check(): void
{
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    if ($path === '' || $path === '/') {
        return;
    }
    $candidates = [$path];
    // Match with and without a trailing slash so admins can enter either.
    if (str_ends_with($path, '/')) {
        $candidates[] = rtrim($path, '/');
    } else {
        $candidates[] = $path . '/';
    }
    try {
        $in = implode(',', array_fill(0, count($candidates), '?'));
        $stmt = db()->prepare("SELECT to_url, status_code FROM redirects WHERE from_path IN ($in) LIMIT 1");
        $stmt->execute($candidates);
        $row = $stmt->fetch();
    } catch (Throwable $e) {
        return; // never let a redirect lookup break the site
    }
    if (!$row) {
        return;
    }
    $to = $row['to_url'];
    // Relative targets are resolved against the site root.
    if (!preg_match('#^https?://#', $to) && $to !== '' && $to[0] !== '/') {
        $to = '/' . $to;
    }
    $code = in_array((int) $row['status_code'], [301, 302], true) ? (int) $row['status_code'] : 301;
    header('Location: ' . $to, true, $code);
    exit;
}

/**
 * Public request bootstrap — call first thing in every public entry script.
 * 1) redirect middleware, 2) serve a cached copy if fresh, 3) start capture.
 */
function public_boot(): void
{
    seo_redirect_check();
    cache_serve();
    cache_start();
}
