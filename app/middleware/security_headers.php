<?php
// Library/app/middleware/security_headers.php
declare(strict_types=1);

function apply_security_headers(): void
{
    // Basic hardening headers
    header('X-Frame-Options: DENY'); // prevent clickjacking
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: geolocation=(), microphone=(), camera=()');

    // CSP (safe simple CSP for PHP pages)
    // If you use inline JS/CSS later, we can adjust.
    header("Content-Security-Policy: default-src 'self'; img-src 'self' data:; style-src 'self'; script-src 'self'; base-uri 'self'; frame-ancestors 'none'; form-action 'self'");

    // Optional: stricter for HTTPS only (keep off for localhost http)
    // header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
}
