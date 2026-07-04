<?php
/**
 * robots.txt — content managed in admin » سئو (seo_settings.robots_txt).
 * Served at /robots.txt (see .htaccess / router.php).
 */

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/seo.php';

header('Content-Type: text/plain; charset=utf-8');

$default = "User-agent: *\nDisallow: /admin\nDisallow: /includes\nDisallow: /api\nDisallow: /cache\n\nSitemap: " . rtrim(BASE_URL, '/') . "/sitemap.xml\n";

$robots = seo_setting('robots_txt', $default);
echo rtrim($robots) . "\n";
