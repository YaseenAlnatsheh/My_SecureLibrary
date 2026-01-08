<?php
// Library/public/register.php
declare(strict_types=1);

require_once __DIR__ . '/../app/helpers/security.php';
require_once __DIR__ . '/../app/middleware/csrf.php';
require_once __DIR__ . '/../app/db/db.php';
require_once __DIR__ . '/../app/helpers/crypto_aes.php';
require_once __DIR__ . '/../app/helpers/validator.php';

start_secure_session();

$error = '';
$success = '';

$full_name = '';
$email = '';
$phone = '';

if (is_post()) {
    csrf_validate_or_die();

    $full_name = v_clean($_POST['full_name'] ?? '');
    $email     = v_clean($_POST['email'] ?? '');
    $phone     = v_clean($_POST['phone'] ?? '');
    $password  = (string)($_POST['password'] ?? '');

    if (!v_len_between($full_name, 2, 60)) {
        $error = 'Full name must be between 2 and 60 characters.';
    } elseif (!v_email($email)) {
        $error = 'Invalid email format.';
    } elseif (!v_phone_optional($phone)) {
        $error = 'Phone must contain only digits/spaces/+ and be 7–20 characters.';
    } elseif (!v_password($password)) {
        $error = 'Password must be at least 8 characters and include letters and numbers.';
    } else {
        $pdo = db();

        $check = $pdo->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
        $check->execute([$email]);

        if ($check->fetch()) {
            $error = 'Email already registered.';
        } else {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $phoneEnc = aes_encrypt($phone);

            $stmt = $pdo->prepare("
                INSERT INTO users (full_name, email, phone_enc, password_hash, is_active, created_at)
                VALUES (?, ?, ?, ?, 1, NOW())
            ");
            $stmt->execute([$full_name, $email, $phoneEnc, $hash]);

            $success = 'Account created successfully. You can login now.';
            $full_name = $email = $phone = '';
        }
    }
}

$token = csrf_token();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>User Register</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <div class="container">
    <h1>User Register</h1>

    <?php if ($error): ?>
      <div class="alert"><?= e($error) ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
      <div class="success"><?= e($success) ?></div>
    <?php endif; ?>

    <form method="post" autocomplete="off">
      <input type="hidden" name="csrf_token" value="<?= e($token) ?>">

      <label>Full Name</label>
      <input type="text" name="full_name" value="<?= e($full_name) ?>" required />

      <label>Email</label>
      <input type="email" name="email" value="<?= e($email) ?>" required />

      <label>Phone (encrypted at rest)</label>
      <input type="text" name="phone" value="<?= e($phone) ?>" placeholder="Optional" />

      <label>Password</label>
      <input type="password" name="password" required />

      <button type="submit">Register</button>
    </form>

    <p class="muted"><a href="login.php">Back to Login</a></p>
  </div>
</body>
</html>
