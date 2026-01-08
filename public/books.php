<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/helpers/security.php';
require_once __DIR__ . '/../app/middleware/auth.php';
require_once __DIR__ . '/../app/db/db.php';

require_user_login();

$q = trim($_GET['q'] ?? '');

$pdo = db();
if ($q !== '') {
    $stmt = $pdo->prepare("
        SELECT id, title, author, category
        FROM books
        WHERE is_active = 1
          AND (title LIKE ? OR author LIKE ? OR category LIKE ?)
        ORDER BY created_at DESC
    ");
    $like = "%{$q}%";
    $stmt->execute([$like, $like, $like]);
} else {
    $stmt = $pdo->query("
        SELECT id, title, author, category
        FROM books
        WHERE is_active = 1
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
  <title>Books</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <div class="container" style="max-width: 720px;">
    <h1>Books</h1>

    <p class="muted">
      Logged in as <b><?= e($_SESSION['full_name'] ?? '') ?></b>
      • <a href="logout.php">Logout</a>
      <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
        • <a href="admin/dashboard.php">Admin Dashboard</a>
      <?php endif; ?>
    </p>

    <form method="get" style="display:flex; gap:10px; margin:12px 0;">
      <input type="text" name="q" placeholder="Search title/author/category..." value="<?= e($q) ?>" />
      <button type="submit" style="width:auto; padding:10px 14px;">Search</button>
    </form>

    <?php if (!$books): ?>
      <p class="muted">No books found.</p>
    <?php else: ?>
      <div style="display:flex; flex-direction:column; gap:10px;">
        <?php foreach ($books as $b): ?>
          <div style="border:1px solid #dfe3ea; border-radius:10px; padding:12px;">
            <div style="font-weight:700; font-size:16px;"><?= e($b['title']) ?></div>
            <div class="muted">
              <?= e($b['author'] ?? 'Unknown') ?> • <?= e($b['category'] ?? 'Uncategorized') ?>
            </div>
            <div style="margin-top:8px;">
              <a href="book_view.php?id=<?= (int)$b['id'] ?>">View Details</a>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</body>
</html>
