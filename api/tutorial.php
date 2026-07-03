<?php
/**
 * Single-tutorial API for the public SPA: server-rendered fragment
 * (single source of truth: includes/tutorial_renderer.php) + meta.
 * Drafts and unknown slugs return 404.
 */
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/aparat.php';
require_once __DIR__ . '/../includes/tutorial_renderer.php';

header('Content-Type: application/json; charset=utf-8');

$slug = trim((string) ($_GET['slug'] ?? ''));
if ($slug === '') {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'slug لازم است.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$stmt = db()->prepare(
    "SELECT t.*, c.slug AS cat_slug, c.title AS cat_title
     FROM tutorials t LEFT JOIN tutorial_categories c ON c.id = t.category_id
     WHERE t.slug = ? AND t.status = 'published'"
);
$stmt->execute([$slug]);
$tut = $stmt->fetch();

if (!$tut) {
    http_response_code(404);
    echo json_encode(['ok' => false, 'error' => 'آموزش پیدا نشد.'], JSON_UNESCAPED_UNICODE);
    exit;
}

echo json_encode([
    'ok'   => true,
    'meta' => [
        'slug'     => $tut['slug'],
        'title'    => $tut['title'],
        'category' => $tut['cat_slug'] ?? '',
        'duration' => (int) $tut['duration_minutes'],
        'hasVideo' => $tut['video_type'] === 'aparat',
        'updated'  => jalali_date($tut['updated_at']),
        'seo_title'       => $tut['seo_title'],
        'seo_description' => $tut['seo_description'],
    ],
    'html' => tutorial_render_fragment($tut),
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
