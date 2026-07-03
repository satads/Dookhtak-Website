<?php
/**
 * PDO MySQL singleton (utf8mb4). Every query in the project must use
 * prepared statements through this connection.
 */

require_once __DIR__ . '/config.php';

function db(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    }
    return $pdo;
}

/**
 * Read one value from the settings table with a safe fallback.
 * Values are cached per-request; DB failure returns the fallback so
 * public pages never fatal on a settings read.
 */
function setting(string $key, string $default = ''): string
{
    static $cache = null;
    if ($cache === null) {
        $cache = [];
        try {
            foreach (db()->query('SELECT `key`, `value` FROM settings') as $row) {
                $cache[$row['key']] = $row['value'];
            }
        } catch (Throwable $e) {
            // DB unavailable — fall back to defaults, log for the admin.
            error_log('settings read failed: ' . $e->getMessage());
        }
    }
    return array_key_exists($key, $cache) ? $cache[$key] : $default;
}
