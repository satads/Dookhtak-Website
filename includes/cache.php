<?php
/**
 * File-based full-page cache for anonymous visitors.
 * Infrastructure only in Phase 1 — CACHE_ENABLED stays false until Phase 6.
 */

require_once __DIR__ . '/config.php';

/** Cache file path for a request URI. */
function cache_path(string $uri): string
{
    return rtrim(CACHE_DIR, '/') . '/page_' . sha1($uri) . '.html';
}

/**
 * Serve the cached copy of the current request and exit, when available.
 * Only GET requests without an admin session cookie are cacheable.
 */
function cache_serve(): void
{
    if (!CACHE_ENABLED || ($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'GET') {
        return;
    }
    if (!empty($_COOKIE[SESSION_NAME])) {
        return; // logged-in admin sees live pages
    }
    $file = cache_path($_SERVER['REQUEST_URI'] ?? '/');
    if (is_file($file) && (time() - filemtime($file)) < 3600) {
        readfile($file);
        exit;
    }
}

/** Begin capturing output for the cache (call after cache_serve()). */
function cache_start(): void
{
    if (!CACHE_ENABLED || ($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'GET' || !empty($_COOKIE[SESSION_NAME])) {
        return;
    }
    ob_start(function (string $html): string {
        $file = cache_path($_SERVER['REQUEST_URI'] ?? '/');
        if (!is_dir(dirname($file))) {
            @mkdir(dirname($file), 0755, true);
        }
        @file_put_contents($file, $html, LOCK_EX);
        return $html;
    });
}

/** Delete every cached page. Wired to all admin saves in later phases. */
function cache_flush(): int
{
    $n = 0;
    foreach (glob(rtrim(CACHE_DIR, '/') . '/page_*.html') ?: [] as $f) {
        if (@unlink($f)) {
            $n++;
        }
    }
    return $n;
}
