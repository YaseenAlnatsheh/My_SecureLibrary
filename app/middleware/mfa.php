<?php
// Library/app/middleware/mfa.php
declare(strict_types=1);

require_once __DIR__ . '/../helpers/security.php';

function mfa_generate_otp(string $scope, int $ttlSeconds = 300): string
{
    start_secure_session();

    $otp = (string)random_int(100000, 999999);

    $_SESSION['mfa_' . $scope] = [
        'otp' => $otp,
        'expires_at' => time() + $ttlSeconds,
        'attempts' => 0
    ];

    return $otp;
}

function mfa_has_pending(string $scope): bool
{
    start_secure_session();
    if (empty($_SESSION['mfa_' . $scope]) || !is_array($_SESSION['mfa_' . $scope])) return false;

    $expires = (int)($_SESSION['mfa_' . $scope]['expires_at'] ?? 0);
    return time() <= $expires;
}

function mfa_seconds_left(string $scope): int
{
    start_secure_session();
    $expires = (int)($_SESSION['mfa_' . $scope]['expires_at'] ?? 0);
    return max(0, $expires - time());
}

function mfa_validate_otp(string $scope, string $input, int $maxAttempts = 5): bool
{
    start_secure_session();

    if (!mfa_has_pending($scope)) return false;

    $_SESSION['mfa_' . $scope]['attempts'] = (int)($_SESSION['mfa_' . $scope]['attempts'] ?? 0) + 1;

    if ((int)$_SESSION['mfa_' . $scope]['attempts'] > $maxAttempts) {
        return false;
    }

    $expected = (string)($_SESSION['mfa_' . $scope]['otp'] ?? '');
    $in = trim($input);

    return ($expected !== '' && hash_equals($expected, $in));
}

function mfa_clear(string $scope): void
{
    start_secure_session();
    unset($_SESSION['mfa_' . $scope]);
}
