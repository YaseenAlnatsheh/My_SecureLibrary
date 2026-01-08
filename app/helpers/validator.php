<?php
// Library/app/helpers/validator.php
declare(strict_types=1);

/**
 * Simple validation + sanitization helpers.
 * - Use trim + remove control chars
 * - Validate lengths and formats
 */

function v_clean(?string $s): string
{
    $s = (string)($s ?? '');
    $s = trim($s);

    // Remove ASCII control characters (except \n \r \t)
    $s = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $s) ?? $s;

    return $s;
}

function v_len_between(string $s, int $min, int $max): bool
{
    $n = mb_strlen($s, 'UTF-8');
    return $n >= $min && $n <= $max;
}

function v_email(string $email): bool
{
    if (!v_len_between($email, 5, 120)) return false;
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function v_phone_optional(string $phone): bool
{
    if ($phone === '') return true;

    // allow + and digits and spaces
    if (!preg_match('/^[0-9+\s]{7,20}$/', $phone)) return false;

    // ensure at least 7 digits
    preg_match_all('/\d/', $phone, $m);
    return count($m[0]) >= 7;
}

function v_password(string $pw): bool
{
    if (strlen($pw) < 8 || strlen($pw) > 72) return false; // bcrypt safe range
    if (!preg_match('/[A-Za-z]/', $pw)) return false;
    if (!preg_match('/[0-9]/', $pw)) return false;
    return true;
}

function v_book_title(string $title): bool
{
    return v_len_between($title, 2, 150);
}

function v_short_optional(string $s, int $max): bool
{
    if ($s === '') return true;
    return v_len_between($s, 1, $max);
}

function v_description_optional(string $s, int $max): bool
{
    if ($s === '') return true;
    return mb_strlen($s, 'UTF-8') <= $max;
}
