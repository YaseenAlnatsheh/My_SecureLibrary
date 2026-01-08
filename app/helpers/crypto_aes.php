<?php
// Library/app/helpers/crypto_aes.php
declare(strict_types=1);

/**
 * AES-256-GCM encryption helper
 * - Confidentiality + Integrity (GCM authentication tag)
 * - Stores base64 payload: iv|tag|ciphertext
 *
 * IMPORTANT:
 * Put your AES key in environment or config file.
 * For local project we will read from config constant.
 */

function aes_key(): string
{
    // 32 bytes key for AES-256
    // Change this to a random 32-byte string in production.
    // Example generator: bin2hex(random_bytes(32)) then store securely.
    return defined('AES_SECRET_KEY') ? AES_SECRET_KEY : 'CHANGE_ME_CHANGE_ME_CHANGE_ME_32BYTES!!';
}

function aes_encrypt(?string $plaintext): ?string
{
    if ($plaintext === null) return null;
    $plaintext = (string)$plaintext;
    if ($plaintext === '') return null;

    $key = aes_key();
    $keyBin = hash('sha256', $key, true); // 32 bytes

    $iv = random_bytes(12); // 96-bit IV for GCM
    $tag = '';
    $cipher = openssl_encrypt(
        $plaintext,
        'aes-256-gcm',
        $keyBin,
        OPENSSL_RAW_DATA,
        $iv,
        $tag,
        '',
        16
    );

    if ($cipher === false) return null;

    $payload = $iv . $tag . $cipher;
    return base64_encode($payload);
}

function aes_decrypt(?string $payloadB64): ?string
{
    if ($payloadB64 === null) return null;
    $payloadB64 = (string)$payloadB64;
    if ($payloadB64 === '') return null;

    $raw = base64_decode($payloadB64, true);
    if ($raw === false || strlen($raw) < (12 + 16 + 1)) return null;

    $iv = substr($raw, 0, 12);
    $tag = substr($raw, 12, 16);
    $cipher = substr($raw, 28);

    $key = aes_key();
    $keyBin = hash('sha256', $key, true);

    $plain = openssl_decrypt(
        $cipher,
        'aes-256-gcm',
        $keyBin,
        OPENSSL_RAW_DATA,
        $iv,
        $tag
    );

    return ($plain === false) ? null : $plain;
}
