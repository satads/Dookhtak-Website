<?php
/**
 * XML sitemap — static pages + published blog posts. Served at
 * /sitemap.xml (see .htaccess / router.php). Output is cached to disk for
 * one hour; admin saves that publish/unpublish content flush the page
 * cache but not this file, so a freshly published post appears within the
 * cache window (max 1h) — acceptable for sitemaps.
 */

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/helpers.php';

header('Content-Type: application/xml; charset=utf-8');

$cache_file = rtrim(CACHE_DIR, '/') . '/sitemap.xml';
if (is_file($cache_file) && (time() - filemtime($cache_file)) < 3600) {
    readfile($cache_file);
    exit;
}

$base = rtrim(BASE_URL, '/');

// Static pages: [path, changefreq, priority].
$static = [
    ['/',          'weekly',  '1.0'],
    ['/features',  'monthly', '0.8'],
    ['/pricing',   'monthly', '0.8'],
    ['/tutorials', 'weekly',  '0.7'],
    ['/blog',      'daily',   '0.7'],
    ['/about',     'yearly',  '0.5'],
    ['/contact',   'yearly',  '0.5'],
];

$urls = [];
foreach ($static as [$path, $freq, $prio]) {
    $urls[] = ['loc' => $base . $path, 'changefreq' => $freq, 'priority' => $prio];
}

// Published blog posts with lastmod from updated_at.
try {
    $rows = db()->query(
        "SELECT slug, updated_at, published_at FROM blog_posts WHERE status = 'published' ORDER BY published_at DESC"
    )->fetchAll();
    foreach ($rows as $r) {
        $lastmod = $r['updated_at'] ?: $r['published_at'];
        $urls[] = [
            'loc'        => $base . '/blog/' . $r['slug'],
            'lastmod'    => date('Y-m-d', strtotime($lastmod)),
            'changefreq' => 'monthly',
            'priority'   => '0.6',
        ];
    }
} catch (Throwable $e) {
    error_log('sitemap posts query failed: ' . $e->getMessage());
}

ob_start();
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
foreach ($urls as $u) {
    echo "  <url>\n";
    echo '    <loc>' . e($u['loc']) . "</loc>\n";
    if (!empty($u['lastmod'])) {
        echo '    <lastmod>' . e($u['lastmod']) . "</lastmod>\n";
    }
    echo '    <changefreq>' . e($u['changefreq']) . "</changefreq>\n";
    echo '    <priority>' . e($u['priority']) . "</priority>\n";
    echo "  </url>\n";
}
echo "</urlset>\n";
$xml = ob_get_clean();

if (!is_dir(dirname($cache_file))) {
    @mkdir(dirname($cache_file), 0755, true);
}
@file_put_contents($cache_file, $xml, LOCK_EX);

echo $xml;
