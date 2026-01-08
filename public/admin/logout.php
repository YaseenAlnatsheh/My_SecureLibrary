<?php
// Library/public/admin/logout.php
declare(strict_types=1);

require_once __DIR__ . '/../../app/helpers/security.php';
require_once __DIR__ . '/../../app/helpers/audit.php';

start_secure_session();

if (!empty($_SESSION['admin_id'])) {
    audit_log('admin', (int)$_SESSION['admin_id'], 'ADMIN_LOGOUT', 'Admin logged out');
}

$_SESSION = [];
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
}
session_destroy();

redirect('/admin/login.php');
