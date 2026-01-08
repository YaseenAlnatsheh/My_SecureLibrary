<?php
// Library/public/admin/verify_otp.php
declare(strict_types=1);

require_once __DIR__ . '/../../app/helpers/security.php';
require_once __DIR__ . '/../../app/middleware/csrf.php';
require_once __DIR__ . '/../../app/middleware/mfa.php';
require_once __DIR__ . '/../../app/middleware/ratelimit.php';
require_once __DIR__ . '/../../app/helpers/audit.php';

start_secure_session();

$scope = 'admin';
$rlScope = 'admin_otp';

rl_init($rlScope);

if (empty($_SESSION['pending_admin']) || !is_array($_SESSION['pending_admin'])) {
    redirect('/admin/login.php');
}

$pending = $_SESSION['pending_admin'];

$error = '';

if (!mfa_has_pending($scope)) {
    $error = 'OTP expired. Please login again.';
}

$locked = rl_is_locked($rlScope);
$secondsLeft = rl_seconds_left($rlScope);

if (is_post()) {
    csrf_validate_or_die();

    if ($locked) {
        $error = "Too many attempts. Try again in " . rl_seconds_left($rlScope) . " seconds.";
    } else {
        if (!mfa_has_pending($scope)) {
            $error = 'OTP expired. Please login again.';
        } else {
            $otp = trim($_POST['otp'] ?? '');

            if ($otp === '') {
                $error = 'OTP is required.';
            } else {
                if (mfa_validate_otp($scope, $otp)) {
                    rl_reset($rlScope);
                    mfa_clear($scope);

                    $_SESSION['admin_id'] = (int)$pending['id'];
                    $_SESSION['admin_name'] = (string)$pending['full_name'];
                    $_SESSION['admin_email'] = (string)$pending['email'];

                    unset($_SESSION['pending_admin']);
                    unset($_SESSION['pending_admin_otp_debug']);

                    audit_log('admin', (int)$pending['id'], 'MFA_SUCCESS', 'Admin OTP verified');

                    redirect('/admin/dashboard.php');
                } else {
                    rl_register_fail($rlScope, 5, 5);
                    audit_log('admin', (int)$pending['id'], 'MFA_FAIL', 'Admin OTP failed');
                    $error = 'Invalid OTP.';
                }
            }
        }
    }
}

$token = csrf_token();
$demoOtp = $_SESSION['pending_admin_otp_debug'] ?? '';
$secondsOtpLeft = mfa_seconds_left($scope);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Verify Admin OTP</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
  <div class="container">
    <h1>Verify OTP</h1>

    <p class="muted">
      OTP expires in <b><?= e((string)$secondsOtpLeft) ?></b> seconds.
    </p>

    <?php if ($demoOtp !== ''): ?>
      <div class="success">
        <b>Demo OTP (localhost):</b> <?= e((string)$demoOtp) ?><br>
        <span class="muted">In production, this OTP is sent via email/SMS.</span>
      </div>
    <?php endif; ?>

    <?php if ($locked): ?>
      <div class="alert">Too many attempts. Try again in <?= e((string)$secondsLeft) ?> seconds.</div>
    <?php endif; ?>

    <?php if ($error): ?>
      <div class="alert"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="post" autocomplete="off">
      <input type="hidden" name="csrf_token" value="<?= e($token) ?>">
      <label>Enter OTP</label>
      <input type="text" name="otp" placeholder="6-digit code" required <?= $locked ? 'disabled' : '' ?> />
      <button type="submit" <?= $locked ? 'disabled' : '' ?>>Verify</button>
    </form>

    <p class="muted"><a href="login.php">Back to Admin Login</a></p>
  </div>
</body>
</html>
