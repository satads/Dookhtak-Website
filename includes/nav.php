<?php
/**
 * THE single navigation config. Desktop navbar, mobile drawer and the
 * footer quick-links column are all built from this array.
 */

function site_nav(): array
{
    return [
        ['label' => 'صفحه اصلی', 'href' => '/'],
        ['label' => 'امکانات', 'href' => '/features'],
        ['label' => 'تعرفه‌ها', 'href' => '/pricing'],
        ['label' => 'آموزش', 'href' => '/tutorials'],
        ['label' => 'بلاگ', 'href' => '/blog'],
        ['label' => 'درباره ما', 'href' => '/about'],
        ['label' => 'تماس با ما', 'href' => '/contact'],
    ];
}

/** Extra links shown only in the footer quick-links column. */
function footer_extra(): array
{
    return [
        ['label' => 'سؤالات متداول', 'href' => '/#faq'],
    ];
}

/**
 * Auto-detect the active nav item from the request URI
 * (exact match first, then prefix match — same logic as the design).
 */
function nav_current(): string
{
    $p = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
    $p = preg_replace('#/index\.(html|php)$#', '/', $p);
    $p = rtrim($p, '/');
    if ($p === '') {
        $p = '/';
    }
    foreach (site_nav() as $m) {
        if ($m['href'] === $p) {
            return $m['href'];
        }
    }
    foreach (site_nav() as $m) {
        if ($m['href'] !== '/' && str_starts_with($p, $m['href'] . '/')) {
            return $m['href'];
        }
    }
    return '/';
}
