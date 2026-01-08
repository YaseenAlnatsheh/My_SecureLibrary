<?php
// Library/public/admin/books_list.php
declare(strict_types=1);

require_once __DIR__ . '/../../app/helpers/security.php';
require_once __DIR__ . '/../../app/middleware/admin_auth.php';
require_once __DIR__ . '/../../app/db/db.php';

require_admin_login();

$pdo = db();

$q = trim($_GET['q'] ?? '');

if ($q !== '') {
    $stmt = $pdo->prepare("
        SELECT id, title, author, category, is_active, created_at
        FROM books
        WHERE title LIKE ? OR author LIKE ? OR category LIKE ?
        ORDER BY created_at DESC
    ");
    $like = "%{$q}%";
    $stmt->execute([$like, $like, $like]);
} else {
    $stmt = $pdo->query("
        SELECT id, title, author, category, is_active, created_at
        FROM books
        ORDER BY created_at DESC
    ");
}

$books = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Manage Books</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
  <div class="container" style="max-width: 980px;">
    <h1>Manage Books</h1>

    <p class="muted">
      Admin: <b><?= e($_SESSION['admin_name'] ?? 'Admin') ?></b>
      • <a href="dashboard.php">Dashboard</a>
      • <a href="logout.php">Logout</a>
      • <a href="../books.php">User View</a>
    </p>

    <div style="display:flex; justify-content:space-between; gap:10px; align-items:center; margin: 12px 0;">
      <form method="get" style="display:flex; gap:10px; flex:1;">
        <input type="text" name="q" placeholder="Search by title/author/category..." value="<?= e($q) ?>" />
        <button type="submit" style="width:auto; padding:10px 14px;">Search</button>
      </form>
      <a href="book_add.php" style="white-space:nowrap;">+ Add Book</a>
    </div>

    <?php if (!$books): ?>
      <p class="muted">No books found.</p>
    <?php else: ?>
      <div style="overflow:auto;">
        <table style="width:100%; border-collapse:collapse;">
          <thead>
            <tr>
              <th style="text-align:left; border-bottom:1px solid #dfe3ea; padding:10px;">Title</th>
              <th style="text-align:left; border-bottom:1px solid #dfe3ea; padding:10px;">Author</th>
              <th style="text-align:left; border-bottom:1px solid #dfe3ea; padding:10px;">Category</th>
              <th style="text-align:left; border-bottom:1px solid #dfe3ea; padding:10px;">Status</th>
              <th style="text-align:left; border-bottom:1px solid #dfe3ea; padding:10px;">Created</th>
              <th style="text-align:left; border-bottom:1px solid #dfe3ea; padding:10px;">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($books as $b): ?>
              <tr>
                <td style="border-bottom:1px solid #eef1f6; padding:10px;"><?= e($b['title']) ?></td>
                <td style="border-bottom:1px solid #eef1f6; padding:10px;"><?= e($b['author'] ?? '') ?></td>
                <td style="border-bottom:1px solid #eef1f6; padding:10px;"><?= e($b['category'] ?? '') ?></td>
                <td style="border-bottom:1px solid #eef1f6; padding:10px;">
                  <?= ((int)$b['is_active'] === 1) ? 'Active' : 'Hidden' ?>
                </td>
                <td style="border-bottom:1px solid #eef1f6; padding:10px;"><?= e((string)$b['created_at']) ?></td>
                <td style="border-bottom:1px solid #eef1f6; padding:10px; white-space:nowrap;">
                  <a href="book_edit.php?id=<?= (int)$b['id'] ?>">Edit</a>
                  •
                  <a href="book_delete.php?id=<?= (int)$b['id'] ?>">Delete</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</body>
</html>
