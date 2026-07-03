<?php
/**
 * Tutorials index API for the public SPA.
 * Returns categories (with self-hosted lucide inner-SVG markup),
 * PUBLISHED tutorials in display order, and the quick-start slugs.
 */
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';

header('Content-Type: application/json; charset=utf-8');

$icons = require __DIR__ . '/../includes/lucide_icons.php';

$categories = [];
foreach (db()->query('SELECT slug, title, lucide_icon FROM tutorial_categories ORDER BY sort_order, id') as $c) {
    $categories[] = [
        'id'    => $c['slug'],
        'title' => $c['title'],
        'icon'  => $icons[trim($c['lucide_icon'])] ?? '<circle cx="12" cy="12" r="9"/>',
    ];
}

$tutorials = [];
$q = db()->query(
    "SELECT t.slug, t.title, t.duration_minutes, t.video_type, t.updated_at, c.slug AS cat_slug
     FROM tutorials t LEFT JOIN tutorial_categories c ON c.id = t.category_id
     WHERE t.status = 'published'
     ORDER BY t.sort_order ASC, t.id ASC"
);
foreach ($q as $t) {
    $tutorials[] = [
        'id'       => $t['slug'],
        'category' => $t['cat_slug'] ?? '',
        'title'    => $t['title'],
        'duration' => (int) $t['duration_minutes'],
        'hasVideo' => $t['video_type'] === 'aparat',
        'updated'  => jalali_date($t['updated_at']),
    ];
}

$published_slugs = array_column($tutorials, 'id');
$quick = json_decode(setting('tutorials_quick_start', '[]'), true) ?: [];
$quick = array_values(array_intersect($quick, $published_slugs));

echo json_encode(
    ['categories' => $categories, 'tutorials' => $tutorials, 'quick_start' => $quick],
    JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
);
