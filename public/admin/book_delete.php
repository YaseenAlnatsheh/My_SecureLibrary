<?php
// Library/public/admin/book_delete.php
declare(strict_types=1);

require_once __DIR__ . '/../../app/helpers/security.php';
require_once __DIR__ . '/../../app/middleware/admin_auth.php';
require_once __DIR__ . '/../../app/middleware/csrf.php';
require_once __DIR__ . '/../../app/db/db.php';
require_once __DIR__ . '/../../app/helpers/audit.php';

require_admin_login();

$pdo = db();

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    redirect('/admin/books_list.php');
}

$stmt = $pdo->prepare("SELECT id, title FROM books WHERE id = ? LIMIT 1");
$stmt->execute([$id]);
$book = $stmt->fetch();

if (!$book) {
    redirect('/admin/books_list.php');
}

if (is_post()) {
    csrf_validate_or_die();

    if (($_POST['confirm'] ?? '') === 'yes') {
        $del = $pdo->prepare("DELETE FROM books WHERE id = ?");
        $del->execute([$id]);

        audit_log('admin', (int)($_SESSION['admin_id'] ?? 0), 'BOOK_DELETE', 'Deleted book ID: ' . $id);

        redirect('/admin/books_list.php');
    }
    redirect('/admin/books_list.php');
}

$token = csrf_token();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Delete Book</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
  <div class="container" style="max-width: 680px;">
    <h1>Delete Book</h1>

    <p class="muted">
      <a href="books_list.php">← Back to Manage Books</a>
      • <a href="dashboard.php">Dashboard</a>
      • <a href="logout.php">Logout</a>
    </p>

    <div class="alert" style="background:#fff7e6; border-color:#ffe1a6; color:#7a4b00;">
      You are about to delete: <b><?= e($book['title']) ?></b><br>
      This action cannot be undone.
    </div>

    <form method="post">
      <input type="hidden" name="csrf_token" value="<?= e($token) ?>">
      <button type="submit" name="confirm" value="yes" style="background:#d93025;">Yes, Delete</button>
      <button type="submit" name="confirm" value="no" style="margin-top:10px; background:#6b7280;">Cancel</button>
    </form>
  </div>
</body>
</html>
