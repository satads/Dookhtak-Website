<?php
/**
 * CLI-ONLY admin account creator.
 * Usage: php database/create_admin.php
 * DELETE THIS FILE from the server after creating the production admin.
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    die('این اسکریپت فقط از خط فرمان قابل اجراست.');
}

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';

function prompt(string $label, bool $hidden = false): string
{
    echo $label;
    if ($hidden && function_exists('shell_exec') && stripos(PHP_OS, 'WIN') === false) {
        shell_exec('stty -echo');
        $value = trim(fgets(STDIN) ?: '');
        shell_exec('stty echo');
        echo PHP_EOL;
    } else {
        $value = trim(fgets(STDIN) ?: '');
    }
    return $value;
}

$username = prompt('Admin username: ');
if ($username === '' || mb_strlen($username) > 64) {
    die("Invalid username.\n");
}

$password = prompt('Admin password (min 10 chars): ', true);
if (mb_strlen($password) < 10) {
    die("Password too short (min 10 characters).\n");
}
$confirm = prompt('Repeat password: ', true);
if ($password !== $confirm) {
    die("Passwords do not match.\n");
}

$hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = db()->prepare(
    'INSERT INTO admin_users (username, password_hash) VALUES (?, ?)
     ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash)'
);
$stmt->execute([$username, $hash]);

echo "Admin user '{$username}' created/updated successfully.\n";
echo "IMPORTANT: delete this file (database/create_admin.php) from the server now.\n";
