<?php
/**
 * Blog data access + shared card rendering (single source of truth for
 * post-card markup used by the archive grid, related posts and the home
 * blog section). All queries are prepared statements; published-only
 * helpers are used by the public site.
 */

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';

/** Cover gradient fallback palette — the approved design's nine card
    gradients in seed order, so the seeded posts keep their exact look. */
const BLOG_GRADIENTS = [
    ['#F6E0D6', '#EEC9B9'],
    ['#EEF1F7', '#D9DFEC'],
    ['#EFEBE2', '#E0D8C6'],
    ['#F0CDBD', '#E5B2A0'],
    ['#E9EDE2', '#D5DCC9'],
    ['#F6E0D6', '#E5B2A0'],
    ['#EEF1F7', '#C9D2E4'],
    ['#EFEBE2', '#D8CDB4'],
    ['#F0CDBD', '#EEC9B9'],
];

/** Deterministic gradient pair for a post without a cover image. */
function blog_gradient(array $post): array
{
    return BLOG_GRADIENTS[max(0, (int) $post['id'] - 1) % count(BLOG_GRADIENTS)];
}

/** All blog categories ordered for display. */
function blog_categories(): array
{
    return db()->query('SELECT * FROM blog_categories ORDER BY sort_order, id')->fetchAll();
}

/** Category row by slug (or null). */
function blog_category_by_slug(string $slug): ?array
{
    $stmt = db()->prepare('SELECT * FROM blog_categories WHERE slug = ?');
    $stmt->execute([$slug]);
    return $stmt->fetch() ?: null;
}

/**
 * Published posts, newest first, optional category filter + pagination.
 * Returns [rows, total].
 */
function blog_published(?int $category_id = null, int $page = 1, int $per_page = 9): array
{
    $where = "p.status = 'published'";
    $args = [];
    if ($category_id !== null) {
        $where .= ' AND p.category_id = ?';
        $args[] = $category_id;
    }
    $stmt = db()->prepare("SELECT COUNT(*) AS n FROM blog_posts p WHERE $where");
    $stmt->execute($args);
    $total = (int) $stmt->fetch()['n'];

    $offset = max(0, ($page - 1) * $per_page);
    $stmt = db()->prepare(
        "SELECT p.*, c.title AS cat_title, c.slug AS cat_slug
         FROM blog_posts p LEFT JOIN blog_categories c ON c.id = p.category_id
         WHERE $where ORDER BY p.published_at DESC, p.id DESC LIMIT $per_page OFFSET $offset"
    );
    $stmt->execute($args);
    return [$stmt->fetchAll(), $total];
}

/** Latest N published posts (home section, featured). */
function blog_latest(int $n = 3): array
{
    $stmt = db()->prepare(
        "SELECT p.*, c.title AS cat_title, c.slug AS cat_slug
         FROM blog_posts p LEFT JOIN blog_categories c ON c.id = p.category_id
         WHERE p.status = 'published' ORDER BY p.published_at DESC, p.id DESC LIMIT " . (int) $n
    );
    $stmt->execute();
    return $stmt->fetchAll();
}

/** One published post by slug (public view), or null. */
function blog_post_by_slug(string $slug): ?array
{
    $stmt = db()->prepare(
        "SELECT p.*, c.title AS cat_title, c.slug AS cat_slug
         FROM blog_posts p LEFT JOIN blog_categories c ON c.id = p.category_id
         WHERE p.slug = ? AND p.status = 'published'"
    );
    $stmt->execute([$slug]);
    return $stmt->fetch() ?: null;
}

/** Related posts: same category first, then newest others — N items. */
function blog_related(array $post, int $n = 3): array
{
    $stmt = db()->prepare(
        "SELECT p.*, c.title AS cat_title FROM blog_posts p
         LEFT JOIN blog_categories c ON c.id = p.category_id
         WHERE p.status = 'published' AND p.id != ?
         ORDER BY (p.category_id = ?) DESC, p.published_at DESC LIMIT " . (int) $n
    );
    $stmt->execute([(int) $post['id'], (int) ($post['category_id'] ?? 0)]);
    return $stmt->fetchAll();
}

/** Previous/next published posts by publish date. Returns [prev, next]. */
function blog_prev_next(array $post): array
{
    $prev = db()->prepare(
        "SELECT slug, title FROM blog_posts
         WHERE status = 'published' AND (published_at < ? OR (published_at = ? AND id < ?))
         ORDER BY published_at DESC, id DESC LIMIT 1"
    );
    $prev->execute([$post['published_at'], $post['published_at'], $post['id']]);
    $next = db()->prepare(
        "SELECT slug, title FROM blog_posts
         WHERE status = 'published' AND (published_at > ? OR (published_at = ? AND id > ?))
         ORDER BY published_at ASC, id ASC LIMIT 1"
    );
    $next->execute([$post['published_at'], $post['published_at'], $post['id']]);
    return [$prev->fetch() ?: null, $next->fetch() ?: null];
}

/** Reading-time line, e.g. "۵ دقیقه مطالعه". */
function blog_read_label(array $post): string
{
    return fa_digits((int) ($post['reading_minutes'] ?? 5)) . ' دقیقه مطالعه';
}

/**
 * Cover area markup for cards (image when set, design gradient otherwise).
 * $height: css height (e.g. '168px') matching the target card.
 */
function blog_card_cover(array $post, string $height = '168px'): string
{
    $badge = '<span style="position:absolute;bottom:14px;inset-inline-end:14px;background:rgba(255,255,255,.94);color:#D45A3D;font-size:11.5px;font-weight:700;padding:5px 13px;border-radius:999px;box-shadow:0 4px 12px rgba(31,42,68,.14);">'
        . e($post['cat_title'] ?? 'بدون دسته') . '</span>';
    if (!empty($post['cover_image'])) {
        return '<div style="position:relative;height:' . e($height) . ';overflow:hidden;">'
            . '<img src="/' . e($post['cover_image']) . '" alt="' . e($post['cover_alt'] ?? $post['title']) . '" loading="lazy" style="width:100%;height:100%;object-fit:cover;display:block;">'
            . $badge . '</div>';
    }
    [$g1, $g2] = blog_gradient($post);
    return '<div style="position:relative;height:' . e($height) . ';background:linear-gradient(150deg,' . e($g1) . ',' . e($g2) . ');overflow:hidden;">'
        . '<span style="position:absolute;inset:0;background-image:repeating-linear-gradient(-45deg,rgba(255,255,255,.14) 0 2px,transparent 2px 14px);"></span>'
        . '<span style="position:absolute;bottom:-26px;inset-inline-start:-26px;width:120px;height:120px;border-radius:50%;background:rgba(255,255,255,.22);"></span>'
        . $badge . '</div>';
}

/**
 * One archive/home post card — byte-faithful to the approved design card.
 * $delay: data-rv-delay for the reveal stagger.
 */
function blog_card(array $post, int $delay = 0): string
{
    $href = '/blog/' . e($post['slug']);
    $date = jalali_date($post['published_at'] ?? 'now');
    ob_start();
    ?>
        <div data-rv="y" data-rv-delay="<?= $delay ?>" style="opacity:0;transform:translateY(18px);transition:opacity .5s ease,transform .5s ease;">
          <a class="hv04e05c" href="<?= $href ?>" style="position:relative;display:flex;flex-direction:column;height:100%;background:#fff;border:1px solid #E8E6E1;border-radius:20px;overflow:hidden;box-shadow:0 2px 5px rgba(31,42,68,.05),0 16px 40px -28px rgba(31,42,68,.45);transition:transform .3s cubic-bezier(.2,.7,.3,1),box-shadow .3s ease,border-color .3s ease;">
            <?= blog_card_cover($post) ?>
            <div style="display:flex;flex-direction:column;flex:1;padding:22px;text-align:start;">
              <h3 style="font-size:17.5px;font-weight:700;color:#1F2A44;margin:0 0 12px;line-height:1.75;"><?= e($post['title']) ?></h3>
              <div style="display:flex;align-items:center;gap:8px;font-size:12.5px;color:#6B7280;">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M16 2v4"/><path d="M8 2v4"/><path d="M3 10h18"/></svg>
                <?= e($date) ?>
                <span style="width:3px;height:3px;border-radius:50%;background:#c9c3b6;"></span>
                <?= e(blog_read_label($post)) ?>
              </div>
              <div style="display:flex;align-items:center;justify-content:space-between;margin-top:auto;padding-top:16px;border-top:2px dashed #F0EBE1;margin-top:16px;">
                <span style="color:#E76F51;font-weight:700;font-size:14px;">ادامه مطلب</span>
                <span style="display:inline-flex;width:32px;height:32px;align-items:center;justify-content:center;background:#FCEFEA;border-radius:50%;color:#E76F51;"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="transform:scaleX(-1);"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg></span>
              </div>
            </div>
          </a>
        </div>
    <?php
    return ob_get_clean();
}
