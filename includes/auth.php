<?php
/**
 * Admin authentication: hardened sessions, login with rate limiting
 * (5 failed attempts per IP per 15 minutes via login_attempts table).
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';

const LOGIN_MAX_ATTEMPTS = 5;
const LOGIN_WINDOW_MINUTES = 15;

/** Start the hardened admin session (idempotent). */
function auth_session_start(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }
    session_name(SESSION_NAME);
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
        'secure'   => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    ]);
    session_start();
}

/** Client IP as stored for rate limiting. */
function auth_client_ip(): string
{
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

/** True when this IP is locked out of the login form. */
function auth_is_locked(): bool
{
    $stmt = db()->prepare(
        'SELECT COUNT(*) AS n FROM login_attempts
         WHERE ip_address = ? AND attempted_at > (NOW() - INTERVAL ' . LOGIN_WINDOW_MINUTES . ' MINUTE)'
    );
    $stmt->execute([auth_client_ip()]);
    return (int) $stmt->fetch()['n'] >= LOGIN_MAX_ATTEMPTS;
}

/** Record a failed login attempt for this IP. */
function auth_record_failure(): void
{
    $stmt = db()->prepare('INSERT INTO login_attempts (ip_address, attempted_at) VALUES (?, NOW())');
    $stmt->execute([auth_client_ip()]);
    // Opportunistic cleanup of stale rows.
    db()->prepare('DELETE FROM login_attempts WHERE attempted_at < (NOW() - INTERVAL 1 DAY)')->execute();
}

/**
 * Attempt a login. Returns true on success (session is regenerated),
 * false on bad credentials or lockout.
 */
function auth_login(string $username, string $password): bool
{
    auth_session_start();
    if (auth_is_locked()) {
        return false;
    }
    $stmt = db()->prepare('SELECT id, username, password_hash FROM admin_users WHERE username = ?');
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    if (!$user || !password_verify($password, $user['password_hash'])) {
        auth_record_failure();
        return false;
    }
    session_regenerate_id(true);
    $_SESSION['admin_id'] = (int) $user['id'];
    $_SESSION['admin_username'] = $user['username'];
    unset($_SESSION['csrf_token']); // fresh token after privilege change
    db()->prepare('UPDATE admin_users SET last_login_at = NOW() WHERE id = ?')->execute([$user['id']]);
    return true;
}

/** Log the admin out and destroy the session. */
function auth_logout(): void
{
    auth_session_start();
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
}

/** True when an admin is logged in. */
function auth_check(): bool
{
    auth_session_start();
    return !empty($_SESSION['admin_id']);
}

/** Require a logged-in admin; redirects to the login page otherwise. */
function require_admin(): void
{
    if (!auth_check()) {
        header('Location: /admin/');
        exit;
    }
}
