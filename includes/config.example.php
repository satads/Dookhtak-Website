<?php
/**
 * Dookhtak website configuration — EXAMPLE file.
 * Copy to config.php (git-ignored) and fill in real values.
 */

// ---- Database (cPanel MySQL) ----
define('DB_HOST', 'localhost');
define('DB_NAME', 'dookhtak_site');
define('DB_USER', 'dookhtak_user');
define('DB_PASS', 'CHANGE-ME');
define('DB_CHARSET', 'utf8mb4');

// ---- Site ----
// Canonical base URL, no trailing slash. Canonical host policy: non-www.
define('BASE_URL', 'https://dookhtak.ir');

// ---- Session ----
define('SESSION_NAME', 'dookhtak_admin');

// ---- Page cache (anonymous visitors; flushed on every admin save) ----
define('CACHE_ENABLED', true);
define('CACHE_DIR', __DIR__ . '/../cache');

// ---- Mother API (filled in Phase 5 from mother API docs) ----
// TODO: fill from mother API docs
define('MOTHER_API', [
    'enabled'     => false,
    'base_url'    => '',
    'endpoint'    => '',
    'api_key'     => '',
    'instance_id' => '',
]);

// ---- Environment ----
// 'production' hides PHP errors; anything else shows them.
define('APP_ENV', 'production');
