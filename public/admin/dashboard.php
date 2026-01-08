<?php
declare(strict_types=1);

require_once __DIR__ . '/../../app/helpers/security.php';
require_once __DIR__ . '/../../app/middleware/admin_auth.php';

require_admin_login();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
  <div class="container" style="max-width: 720px;">
    <h1>Admin Dashboard</h1>

    <p class="muted">
      Admin: <b><?= e($_SESSION['full_name'] ?? '') ?></b>
      • <a href="logout.php">Logout</a>
      • <a href="../books.php">User View</a>
    </p>

    <div style="display:flex; flex-direction:column; gap:10px; margin-top:14px;">
      <a href="books_list.php">Manage Books</a>
      <a href="users_lists.php">View Users</a>
      <a href="audit_logs.php">Audit Logs</a>
    </div>
  </div>
</body>
</html>
