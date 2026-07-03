<?php
/**
 * Media actions endpoint (JSON): update alt text / delete.
 * Auth + CSRF required.
 */
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/media_lib.php';
require_once __DIR__ . '/../includes/helpers.php';

header('Content-Type: application/json; charset=utf-8');

auth_session_start();
if (!auth_check()) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'error' => 'وارد نشده‌ای.'], JSON_UNESCAPED_UNICODE);
    exit;
}
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST' || !csrf_verify($_POST['csrf_token'] ?? null)) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'error' => 'درخواست نامعتبر (CSRF).'], JSON_UNESCAPED_UNICODE);
    exit;
}

$action = $_POST['action'] ?? '';
$id = (int) ($_POST['id'] ?? 0);

if ($action === 'alt') {
    $stmt = db()->prepare('UPDATE media SET alt_text = ? WHERE id = ?');
    $stmt->execute([trim((string) ($_POST['alt'] ?? '')), $id]);
    echo json_encode(['ok' => true], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($action === 'delete') {
    $ok = media_delete($id);
    if (!$ok) {
        http_response_code(404);
    }
    echo json_encode(['ok' => $ok], JSON_UNESCAPED_UNICODE);
    exit;
}

http_response_code(400);
echo json_encode(['ok' => false, 'error' => 'اکشن ناشناخته.'], JSON_UNESCAPED_UNICODE);
