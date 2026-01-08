<?php
// Library/app/middleware/csrf.php
declare(strict_types=1);

require_once __DIR__ . '/../helpers/security.php';

function csrf_token(): string
{
    start_secure_session();

    if (empty($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_validate_or_die(): void
{
    start_secure_session();

    $token = $_POST['csrf_token'] ?? '';

    if (!is_string($token) || $token === '' || empty($_SESSION['csrf_token'])) {
        http_response_code(403);
        echo "Forbidden (CSRF token missing).";
        exit;
    }

    if (!hash_equals((string)$_SESSION['csrf_token'], $token)) {
        http_response_code(403);
        echo "Forbidden (CSRF token invalid).";
        exit;
    }
}
