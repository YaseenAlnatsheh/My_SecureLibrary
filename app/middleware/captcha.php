<?php
// Library/app/middleware/captcha.php
declare(strict_types=1);

require_once __DIR__ . '/../helpers/security.php';

function captcha_refresh(string $scope): string
{
    start_secure_session();

    $a = random_int(1, 9);
    $b = random_int(1, 9);

    $_SESSION['captcha_' . $scope] = [
        'a' => $a,
        'b' => $b,
        'answer' => $a + $b,
        'question' => "{$a} + {$b} = ?"
    ];

    return $_SESSION['captcha_' . $scope]['question'];
}

function captcha_question(string $scope): string
{
    start_secure_session();

    if (empty($_SESSION['captcha_' . $scope]) || !is_array($_SESSION['captcha_' . $scope])) {
        return captcha_refresh($scope);
    }

    return (string)($_SESSION['captcha_' . $scope]['question'] ?? captcha_refresh($scope));
}

function captcha_validate(string $scope, string $answer): bool
{
    start_secure_session();

    if (empty($_SESSION['captcha_' . $scope]) || !is_array($_SESSION['captcha_' . $scope])) {
        return false;
    }

    $expected = (int)($_SESSION['captcha_' . $scope]['answer'] ?? -999999);
    $ans = (int)trim($answer);

    return $ans === $expected;
}

function captcha_clear(string $scope): void
{
    start_secure_session();
    unset($_SESSION['captcha_' . $scope]);
}
