<?php
/**
 * Admin login — standalone minimal page per the approved admin design
 * (centered card, logo, username/password, coral submit).
 * Generic error text; lockout message after 5 failed attempts/IP/15min.
 */
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/auth.php';

auth_session_start();

if (auth_check()) {
    header('Location: /admin/dashboard.php');
    exit;
}

$error = '';
$locked = auth_is_locked();

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    if (!csrf_verify($_POST['csrf_token'] ?? null)) {
        $error = 'نشست شما منقضی شده؛ دوباره تلاش کن.';
    } elseif ($locked) {
        $error = 'تلاش بیش از حد — چند دقیقه دیگر امتحان کن.';
    } elseif (auth_login(trim($_POST['username'] ?? ''), $_POST['password'] ?? '')) {
        header('Location: /admin/dashboard.php');
        exit;
    } else {
        $locked = auth_is_locked();
        $error = $locked
            ? 'تلاش بیش از حد — چند دقیقه دیگر امتحان کن.'
            : 'نام کاربری یا رمز عبور اشتباه است.';
    }
}
?><!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title>ورود — پنل مدیریت دوختک</title>
<link rel="icon" type="image/png" href="/assets/images/Logo01-NEW.png">
<link rel="stylesheet" href="/assets/css/site.css">
<link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body class="admin">
<div dir="rtl" lang="fa" style="min-height:100vh;display:flex;align-items:center;justify-content:center;padding:24px;background-color:#F7F6F3;background-image:linear-gradient(#EDEAE3 1px,transparent 1px),linear-gradient(90deg,#EDEAE3 1px,transparent 1px);background-size:46px 46px;">
  <form method="post" action="/admin/" style="width:100%;max-width:380px;background:#fff;border:1px solid #E8E6E1;border-radius:14px;padding:32px 28px;box-shadow:0 18px 44px -28px rgba(31,42,68,.35);">
    <?= csrf_field() ?>
    <div style="display:flex;align-items:center;justify-content:center;gap:9px;margin-bottom:6px;">
      <img src="/assets/images/Logo01-NEW.png" alt="" style="height:34px;">
      <b style="font-size:20px;color:#1F2A44;">دوختک</b>
    </div>
    <div style="text-align:center;font-size:13px;color:#6B7280;margin-bottom:24px;">پنل مدیریت سایت</div>
    <label for="lg-user" style="display:block;font-size:13px;font-weight:700;color:#1F2A44;margin-bottom:6px;">نام کاربری</label>
    <input id="lg-user" name="username" type="text" dir="ltr" autocomplete="username" class="a-input a-input-lg" style="margin-bottom:16px;" value="<?= e($_POST['username'] ?? '') ?>">
    <label for="lg-pass" style="display:block;font-size:13px;font-weight:700;color:#1F2A44;margin-bottom:6px;">رمز عبور</label>
    <input id="lg-pass" name="password" type="password" dir="ltr" autocomplete="current-password" class="a-input a-input-lg">
    <?php if ($error !== ''): ?>
    <div role="alert" class="a-alert"><?= e($error) ?></div>
    <?php endif; ?>
    <button type="submit" <?= $locked ? 'disabled' : '' ?> class="a-btn a-btn-primary" style="width:100%;min-height:46px;margin-top:20px;font-size:15px;<?= $locked ? 'opacity:.6;' : '' ?>">ورود</button>
  </form>
</div>
</body>
</html>
