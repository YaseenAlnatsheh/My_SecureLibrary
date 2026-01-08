<?php
// Library/public/login.php
declare(strict_types=1);

require_once __DIR__ . '/../app/helpers/security.php';
require_once __DIR__ . '/../app/middleware/csrf.php';
require_once __DIR__ . '/../app/middleware/ratelimit.php';
require_once __DIR__ . '/../app/middleware/captcha.php';
require_once __DIR__ . '/../app/middleware/mfa.php';
require_once __DIR__ . '/../app/helpers/validator.php';
require_once __DIR__ . '/../app/db/db.php';

start_secure_session();

$scope = 'user_login';
$error = '';

rl_init($scope);

$locked = rl_is_locked($scope);
$secondsLeft = rl_seconds_left($scope);
$fails = rl_fails($scope);

$showCaptcha = ($fails >= 2);
$captchaQuestion = $showCaptcha ? captcha_question($scope) : '';

if (is_post()) {
    csrf_validate_or_die();

    if (rl_is_locked($scope)) {
        $error = "Too many attempts. Try again in " . rl_seconds_left($scope) . " seconds.";
    } else {
        $email = v_clean($_POST['email'] ?? '');
        $password = (string)($_POST['password'] ?? '');

        if ($showCaptcha) {
            $cap = (string)($_POST['captcha_answer'] ?? '');
            if (!captcha_validate($scope, $cap)) {
                rl_register_fail($scope);
                $error = 'CAPTCHA is incorrect.';
                $captchaQuestion = captcha_refresh($scope);
            }
        }

        if ($error === '') {
            if (!v_email($email)) {
                rl_register_fail($scope);
                $error = 'Invalid email format.';
            } elseif ($password === '' || strlen($password) > 72) {
                rl_register_fail($scope);
                $error = 'Invalid password.';
            } else {
                $pdo = db();
                $stmt = $pdo->prepare("SELECT id, full_name, email, password_hash, is_active FROM users WHERE email = ? LIMIT 1");
                $stmt->execute([$email]);
                $user = $stmt->fetch();

                if (!$user || (int)$user['is_active'] !== 1) {
                    rl_register_fail($scope);
                    $error = 'Invalid credentials.';
                } elseif (!password_verify($password, $user['password_hash'])) {
                    rl_register_fail($scope);
                    $error = 'Invalid credentials.';
                } else {
                    rl_reset($scope);
                    captcha_clear($scope);

                    $_SESSION['pending_user'] = [
                        'id' => (int)$user['id'],
                        'full_name' => (string)$user['full_name'],
                        'email' => (string)$user['email']
                    ];

                    $otp = mfa_generate_otp('user', 300);
                    $_SESSION['pending_user_otp_debug'] = $otp;

                    redirect('/verify_otp.php');
                }
            }
        }
    }

    $fails = rl_fails($scope);
    $showCaptcha = ($fails >= 2);
    $captchaQuestion = $showCaptcha ? captcha_question($scope) : '';
}

$token = csrf_token();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>User Login</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <div class="container">
    <h1>User Login</h1>

    <?php if ($locked): ?>
      <div class="alert">
        Too many attempts. Try again in <?= e((string)$secondsLeft) ?> seconds.
      </div>
    <?php endif; ?>

    <?php if ($error): ?>
      <div class="alert"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="post" autocomplete="off">
      <input type="hidden" name="csrf_token" value="<?= e($token) ?>">

      <label>Email</label>
      <input type="email" name="email" required <?= $locked ? 'disabled' : '' ?> />

      <label>Password</label>
      <input type="password" name="password" required <?= $locked ? 'disabled' : '' ?> />

      <?php if ($showCaptcha): ?>
        <label>CAPTCHA: <?= e($captchaQuestion) ?></label>
        <input type="text" name="captcha_answer" placeholder="Answer" required <?= $locked ? 'disabled' : '' ?> />
      <?php endif; ?>

      <button type="submit" <?= $locked ? 'disabled' : '' ?>>Login</button>
    </form>

    <p class="muted">No account? <a href="register.php">Register</a></p>
    <p class="muted">Admin? <a href="admin/login.php">Admin Login</a></p>
  </div>
</body>
</html>
