<?php
/**
 * Media upload endpoint (JSON). Auth + CSRF required.
 * Also consumed by the Phase-3 editor image insert.
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
if (empty($_FILES['image'])) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'فایلی انتخاب نشده است.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$result = media_store_upload($_FILES['image']);
if (!$result['ok']) {
    http_response_code(422);
}
echo json_encode($result, JSON_UNESCAPED_UNICODE);
