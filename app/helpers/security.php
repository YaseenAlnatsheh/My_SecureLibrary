<?php
// Library/app/helpers/security.php
declare(strict_types=1);

require_once __DIR__ . '/../middleware/security_headers.php';
apply_security_headers();

/**
 * BASE_URL must match your project folder name inside htdocs.
 * Your URL shows: http://localhost/Library/public/...
 * So BASE_URL should be: /Library/public
 */
function base_url(): string
{
    return '/Library/public';
}

function start_secure_session(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');

        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            'domain' => '',
            'secure' => $isHttps,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);

        session_start();

        // session fixation protection
        if (empty($_SESSION['__regenerated'])) {
            session_regenerate_id(true);
            $_SESSION['__regenerated'] = 1;
        }
    }
}

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function is_post(): bool
{
    return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
}

/**
 * redirect('login.php')            -> /Library/public/login.php
 * redirect('/books.php')           -> /Library/public/books.php
 * redirect('admin/login.php')      -> /Library/public/admin/login.php
 * redirect('/admin/dashboard.php') -> /Library/public/admin/dashboard.php
 */
function redirect(string $path): void
{
    $base = base_url();

    if (preg_match('#^https?://#', $path)) {
        header("Location: {$path}");
        exit;
    }

    if ($path === '') {
        header("Location: {$base}/index.php");
        exit;
    }

    if ($path[0] === '/') {
        header("Location: {$base}{$path}");
        exit;
    }

    header("Location: {$base}/{$path}");
    exit;
}
