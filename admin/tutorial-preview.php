<?php
/**
 * Tutorial preview — server-renders the fragment from the CURRENT
 * (unsaved) builder form using the single-source renderer, styled with
 * the real central site CSS. Admin + CSRF only; nothing is persisted.
 */
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/aparat.php';
require_once __DIR__ . '/../includes/tutorial_renderer.php';

auth_session_start();
if (!auth_check()) {
    http_response_code(401);
    exit('وارد نشده‌ای.');
}
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST' || !csrf_verify($_POST['csrf_token'] ?? null)) {
    http_response_code(403);
    exit('درخواست نامعتبر (CSRF).');
}

// Reuse the builder's collection/sanitization logic.
require_once __DIR__ . '/../includes/tutorial_form.php';
$tut = tutorial_collect_post($_POST, [
    'video_type' => 'none', 'video_embed' => '', 'intro_text' => '',
    'prerequisites_html' => '', 'content_json' => '[]', 'troubleshooting_json' => '[]',
]);
if ($tut['video_type'] === 'aparat' && $tut['video_embed'] !== '') {
    $tut['video_embed'] = aparat_sanitize($tut['video_embed']) ?? '';
}

$fragment = tutorial_render_fragment($tut);
?><!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title>پیش‌نمایش: <?= e($tut['title'] ?: 'آموزش') ?> — دوختک</title>
<link rel="stylesheet" href="/assets/css/site.css">
<link rel="stylesheet" href="/assets/css/page-tutorials.css">
</head>
<body>
<div dir="rtl" lang="fa" style="min-height:100vh;background-color:#FAF8F4;background-image:linear-gradient(#EDEAE3 1px,transparent 1px),linear-gradient(90deg,#EDEAE3 1px,transparent 1px);background-size:46px 46px;line-height:1.6;">
  <div style="max-width:760px;margin-inline:auto;padding:28px 24px 60px;">
    <div style="display:flex;align-items:center;gap:9px;background:#FDF6E7;border:1px solid #F3E2B8;border-radius:10px;padding:10px 14px;font-size:12.5px;color:#8a6d1f;margin-bottom:20px;">
      این پیش‌نمایش است — چیزی ذخیره نشده.
    </div>
    <h1 style="font-size:28px;line-height:1.5;font-weight:700;color:#1F2A44;margin:0 0 20px;"><?= e($tut['title'] ?: '[بدون عنوان]') ?></h1>
    <div id="tutContent"><?= $fragment ?></div>
  </div>
</div>
<script>
/* Same delegated behaviors the public SPA gives fragments. */
document.addEventListener('click', function (e) {
  var cover = e.target.closest && e.target.closest('[data-video-cover]');
  if (cover) {
    var tpl = cover.parentElement.querySelector('template[data-video-embed]');
    if (tpl) cover.replaceWith(tpl.content.cloneNode(true));
    return;
  }
  var btn = e.target.closest && e.target.closest('[data-acc-btn]');
  if (btn) {
    var panel = btn.parentElement.querySelector('[data-acc-panel]');
    var icon = btn.querySelector('[data-acc-icon]');
    if (panel) {
      var open = panel.style.gridTemplateRows === '1fr';
      panel.style.gridTemplateRows = open ? '0fr' : '1fr';
      if (icon) icon.style.transform = open ? 'rotate(0deg)' : 'rotate(45deg)';
    }
  }
});
</script>
</body>
</html>
