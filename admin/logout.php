<?php
/** Logout (POST + CSRF only). */
require_once __DIR__ . '/../includes/auth.php';

auth_session_start();

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST' || !csrf_verify($_POST['csrf_token'] ?? null)) {
    header('Location: /admin/');
    exit;
}

auth_logout();
header('Location: /admin/');
exit;
