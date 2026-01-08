<?php
// Library/app/middleware/auth.php
declare(strict_types=1);

require_once __DIR__ . '/../helpers/security.php';

function require_user_login(): void
{
    start_secure_session();
    if (empty($_SESSION['user_id'])) {
        redirect('/login.php');
    }
}
