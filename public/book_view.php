<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/helpers/security.php';
require_once __DIR__ . '/../app/middleware/auth.php';
require_once __DIR__ . '/../app/db/db.php';

require_user_login();

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    redirect('/books.php');
}

$pdo = db();
$stmt = $pdo->prepare("SELECT * FROM books WHERE id = ? AND is_active = 1 LIMIT 1");
$stmt->execute([$id]);
$book = $stmt->fetch();

if (!$book) {
    redirect('/books.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?= e($book['title']) ?></title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <div class="container" style="max-width: 720px;">
    <h1><?= e($book['title']) ?></h1>
    <p class="muted"><?= e($book['author'] ?? 'Unknown') ?> • <?= e($book['category'] ?? 'Uncategorized') ?></p>

    <div style="margin-top:14px; line-height:1.6;">
      <?= nl2br(e($book['description'] ?? 'No description.')) ?>
    </div>

    <p style="margin-top:16px;">
      <a href="books.php">← Back to Books</a>
    </p>
  </div>
</body>
</html>
