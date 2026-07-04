<?php
/**
 * Page shell helpers. Every public page calls render_head() and
 * render_foot() so <head> markup exists in exactly one place.
 *
 * $opts:
 *   title        — explicit page <title> (else resolved from seo_pages)
 *   description  — meta description (else resolved from seo_pages/default)
 *   page_key     — seo_pages key driving title/description/og_image
 *   canonical    — canonical URL (defaults to the current absolute URL)
 *   og_image     — social image (else fallback chain in seo_meta())
 *   og_type      — Open Graph type (default 'website'; 'article' for posts)
 *   robots       — robots meta (e.g. 'noindex' for the 404 page)
 *   jsonld       — array of JSON-LD blocks (each an associative array)
 *   css          — optional page-specific stylesheet path
 *   js           — optional page-specific script path
 *   js_module    — load the page script as an ES module (tutorials SPA)
 *   preload_font — preload the primary IRANSans woff2 (above-the-fold pages)
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/nav.php';
require_once __DIR__ . '/seo.php';

function render_head(array $opts = []): void
{
    $meta = seo_meta($opts);
    if (!empty($opts['og_type'])) {
        $meta['og_type'] = $opts['og_type'];
    }
    ?><!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($meta['title']) ?></title>
<meta name="description" content="<?= e($meta['description']) ?>">
<link rel="icon" type="image/png" href="/assets/images/Logo01-NEW.png">
<?php if (!empty($opts['preload_font'])): ?>
<link rel="preload" href="/assets/fonts/IRANSansWeb.woff2" as="font" type="font/woff2" crossorigin>
<?php endif; ?>
<link rel="stylesheet" href="/assets/css/site.css">
<?php if (!empty($opts['css'])): ?>
<link rel="stylesheet" href="<?= e($opts['css']) ?>">
<?php endif; ?>
<script defer src="/assets/js/site.js"></script>
<?php if (!empty($opts['js'])): ?>
<script <?= !empty($opts['js_module']) ? 'type="module"' : 'defer' ?> src="<?= e($opts['js']) ?>"></script>
<?php endif;
    seo_emit_head($meta, $opts['jsonld'] ?? []);
    ?>
</head>
<body>
<?php
}

function render_foot(): void
{
    seo_emit_body_scripts();
    ?></body>
</html>
<?php
}
