<?php
/**
 * Page shell helpers. Every public page calls render_head() and
 * render_foot() so <head> markup exists in exactly one place.
 *
 * $opts:
 *   title        — page <title>
 *   description  — meta description
 *   css          — optional page-specific stylesheet path
 *   js           — optional page-specific script path
 *   js_module    — load the page script as an ES module (tutorials SPA)
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/nav.php';

function render_head(array $opts = []): void
{
    $title = $opts['title'] ?? 'دوختک — سامانه ابری مدیریت خیاطی و مزون';
    $description = $opts['description'] ?? 'دوختک همه کارهای مدیریتی خیاطی را ساده می‌کند: سفارش، مشتری، گالری و حسابداری، همه روی یک میز.';
    ?><!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($title) ?></title>
<meta name="description" content="<?= e($description) ?>">
<link rel="icon" type="image/png" href="/assets/images/Logo01-NEW.png">
<link rel="stylesheet" href="/assets/css/site.css">
<?php if (!empty($opts['css'])): ?>
<link rel="stylesheet" href="<?= e($opts['css']) ?>">
<?php endif; ?>
<script defer src="/assets/js/site.js"></script>
<?php if (!empty($opts['js'])): ?>
<script <?= !empty($opts['js_module']) ? 'type="module"' : 'defer' ?> src="<?= e($opts['js']) ?>"></script>
<?php endif; ?>
</head>
<body>
<?php
}

function render_foot(): void
{
    ?></body>
</html>
<?php
}
