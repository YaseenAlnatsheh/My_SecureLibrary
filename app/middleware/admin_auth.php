<?php
// Library/app/middleware/admin_auth.php
declare(strict_types=1);

require_once __DIR__ . '/../helpers/security.php';

function require_admin_login(): void
{
    start_secure_session();
    if (empty($_SESSION['admin_id'])) {
        redirect('/admin/login.php');
    }
}
