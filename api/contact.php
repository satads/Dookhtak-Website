<?php
/**
 * Contact/consultation form endpoint. JSON in (the design's contract:
 * {name, phone, business_type, subject, message}) -> JSON out driving
 * the existing form states. Persian-digit normalization, validation,
 * honeypot, 5/IP/hour rate limit. Save locally FIRST, then forward to
 * the mother system when enabled.
 */
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/MotherApiClient.php';

header('Content-Type: application/json; charset=utf-8');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'فقط POST.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$raw = file_get_contents('php://input');
$in = json_decode($raw, true);
if (!is_array($in)) {
    $in = $_POST; // form-encoded fallback
}

// Honeypot: bots fill it — pretend success, store nothing.
// ('company' is the public form's hidden field name.)
if (trim((string) ($in['company'] ?? $in['hp'] ?? $in['website'] ?? '')) !== '') {
    echo json_encode(['ok' => true], JSON_UNESCAPED_UNICODE);
    exit;
}

$ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

// Rate limit: 5 submissions per IP per hour.
$stmt = db()->prepare('SELECT COUNT(*) AS n FROM submissions WHERE ip_address = ? AND created_at > (NOW() - INTERVAL 1 HOUR)');
$stmt->execute([$ip]);
if ((int) $stmt->fetch()['n'] >= 5) {
    http_response_code(429);
    echo json_encode(['ok' => false, 'error' => 'تعداد درخواست‌ها زیاد است؛ کمی بعد دوباره تلاش کن.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$name = trim((string) ($in['name'] ?? ''));
$phone = preg_replace('/[\s-]+/', '', en_digits(trim((string) ($in['phone'] ?? ''))));
$biz = mb_substr(trim((string) ($in['business_type'] ?? '')), 0, 120);
$subject = mb_substr(trim((string) ($in['subject'] ?? '')), 0, 160);
$message = mb_substr(trim((string) ($in['message'] ?? '')), 0, 5000);

if ($name === '') {
    http_response_code(422);
    echo json_encode(['ok' => false, 'field' => 'name', 'error' => 'نام و نام خانوادگی را بنویس.'], JSON_UNESCAPED_UNICODE);
    exit;
}
if (!preg_match('/^09\d{9}$/', $phone)) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'field' => 'phone', 'error' => 'شماره موبایل باید با ۰۹ شروع شود و ۱۱ رقم باشد.'], JSON_UNESCAPED_UNICODE);
    exit;
}

// Save locally first — delivery to the mother system must never lose a lead.
db()->prepare(
    'INSERT INTO submissions (name, phone, business_type, subject, message, ip_address) VALUES (?,?,?,?,?,?)'
)->execute([mb_substr($name, 0, 160), $phone, $biz, $subject, $message, $ip]);
$id = (int) db()->lastInsertId();

// Forward if the adapter is enabled; failures are recorded, never surfaced.
$client = new MotherApiClient();
if ($client->enabled()) {
    mother_forward_submission($id);
}

echo json_encode(['ok' => true], JSON_UNESCAPED_UNICODE);
