<?php
// Library/app/middleware/ratelimit.php
declare(strict_types=1);

require_once __DIR__ . '/../helpers/security.php';

function rl_key(string $scope): string
{
    return 'rl_' . $scope;
}

function rl_init(string $scope): void
{
    start_secure_session();
    $k = rl_key($scope);

    if (empty($_SESSION[$k]) || !is_array($_SESSION[$k])) {
        $_SESSION[$k] = [
            'fails' => 0,
            'lock_until' => 0,
        ];
    }
}

function rl_is_locked(string $scope): bool
{
    rl_init($scope);
    $k = rl_key($scope);
    $lockUntil = (int)($_SESSION[$k]['lock_until'] ?? 0);
    return time() < $lockUntil;
}

function rl_seconds_left(string $scope): int
{
    rl_init($scope);
    $k = rl_key($scope);
    $lockUntil = (int)($_SESSION[$k]['lock_until'] ?? 0);
    return max(0, $lockUntil - time());
}

function rl_register_fail(string $scope, int $maxFails = 5, int $lockMinutes = 1): void
{
    rl_init($scope);
    $k = rl_key($scope);

    $_SESSION[$k]['fails'] = (int)($_SESSION[$k]['fails'] ?? 0) + 1;

    if ((int)$_SESSION[$k]['fails'] >= $maxFails) {
        $_SESSION[$k]['lock_until'] = time() + ($lockMinutes * 60);
    }
}

function rl_reset(string $scope): void
{
    rl_init($scope);
    $k = rl_key($scope);
    $_SESSION[$k] = [
        'fails' => 0,
        'lock_until' => 0,
    ];
}

function rl_fails(string $scope): int
{
    rl_init($scope);
    $k = rl_key($scope);
    return (int)($_SESSION[$k]['fails'] ?? 0);
}
