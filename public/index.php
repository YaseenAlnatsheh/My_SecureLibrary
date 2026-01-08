<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/helpers/security.php';
start_secure_session();

$isLoggedIn = !empty($_SESSION['user_id']);
$role = $_SESSION['role'] ?? 'guest';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Secure Digital Library</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <div class="container">
    <h1>Secure Digital Library</h1>

    <?php if (!$isLoggedIn): ?>
      <p class="muted">Welcome. Please login to access the library.</p>
      <p>
        <a href="login.php">User Login</a> •
        <a href="register.php">Register</a> •
        <a href="admin/login.php">Admin Login</a>
      </p>
    <?php else: ?>
      <p class="muted">Logged in as: <b><?= e($_SESSION['full_name'] ?? '') ?></b> (<?= e($role) ?>)</p>
      <p>
        <a href="books.php">Browse Books</a>
        <?php if ($role === 'admin'): ?>
          • <a href="admin/dashboard.php">Admin Dashboard</a>
        <?php endif; ?>
        • <a href="logout.php">Logout</a>
      </p>
    <?php endif; ?>
  </div>
</body>
</html>
