<?php
/**
 * Development router for `php -S localhost:8080 router.php`.
 * Mirrors the clean-URL rewrites in .htaccess (which the PHP built-in
 * server does not read). NOT used on Apache/cPanel.
 */

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';

// Block sensitive directories like .htaccess does on Apache
// (must run BEFORE static-file serving).
if (preg_match('#^/(includes|database|cache)(/|$)#', $uri)) {
    http_response_code(403);
    echo 'Forbidden';
    return true;
}

// Serve real files (assets, content fragments, uploads) directly.
if ($uri !== '/' && file_exists(__DIR__ . $uri) && !is_dir(__DIR__ . $uri)) {
    return false;
}

// Directory index for /admin (Apache DirectoryIndex handles this in prod).
if (preg_match('#^/admin/?$#', $uri)) {
    require __DIR__ . '/admin/index.php';
    return true;
}

// SEO endpoints (Apache maps these via .htaccess rewrites).
if ($uri === '/sitemap.xml') {
    require __DIR__ . '/sitemap.php';
    return true;
}
if ($uri === '/robots.txt') {
    require __DIR__ . '/robots.php';
    return true;
}

// /blog/{slug}
if (preg_match('#^/blog/([a-z0-9-]+)/?$#', $uri, $m)) {
    $_GET['slug'] = $m[1];
    require __DIR__ . '/blog-post.php';
    return true;
}

$routes = [
    '/'          => 'index.php',
    '/features'  => 'features.php',
    '/pricing'   => 'pricing.php',
    '/tutorials' => 'tutorials.php',
    '/blog'      => 'blog.php',
    '/about'     => 'about.php',
    '/contact'   => 'contact.php',
];

$path = rtrim($uri, '/');
if ($path === '') {
    $path = '/';
}
if (isset($routes[$path])) {
    require __DIR__ . '/' . $routes[$path];
    return true;
}

http_response_code(404);
require __DIR__ . '/404.php';
return true;
