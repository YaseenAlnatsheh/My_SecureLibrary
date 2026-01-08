<?php
// Library/public/admin/book_add.php
declare(strict_types=1);

require_once __DIR__ . '/../../app/helpers/security.php';
require_once __DIR__ . '/../../app/middleware/admin_auth.php';
require_once __DIR__ . '/../../app/middleware/csrf.php';
require_once __DIR__ . '/../../app/helpers/validator.php';
require_once __DIR__ . '/../../app/db/db.php';
require_once __DIR__ . '/../../app/helpers/audit.php';

require_admin_login();

$pdo = db();

$error = '';
$success = '';

$title = '';
$author = '';
$category = '';
$description = '';
$is_active = 1;

if (is_post()) {
    csrf_validate_or_die();

    $title = v_clean($_POST['title'] ?? '');
    $author = v_clean($_POST['author'] ?? '');
    $category = v_clean($_POST['category'] ?? '');
    $description = v_clean($_POST['description'] ?? '');
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    if (!v_book_title($title)) {
        $error = 'Title must be between 2 and 150 characters.';
    } elseif (!v_short_optional($author, 80)) {
        $error = 'Author must be max 80 characters.';
    } elseif (!v_short_optional($category, 80)) {
        $error = 'Category must be max 80 characters.';
    } elseif (!v_description_optional($description, 1000)) {
        $error = 'Description must be max 1000 characters.';
    } else {
        $stmt = $pdo->prepare("
            INSERT INTO books (title, author, category, description, is_active)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([$title, $author, $category, $description, $is_active]);

        audit_log('admin', (int)($_SESSION['admin_id'] ?? 0), 'BOOK_ADD', 'Added book: ' . $title);

        $success = 'Book added successfully.';
        $title = $author = $category = $description = '';
        $is_active = 1;
    }
}

$token = csrf_token();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Add Book</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
  <div class="container" style="max-width: 720px;">
    <h1>Add Book</h1>

    <p class="muted">
      <a href="books_list.php">← Back to Manage Books</a>
      • <a href="dashboard.php">Dashboard</a>
      • <a href="logout.php">Logout</a>
    </p>

    <?php if ($error): ?>
      <div class="alert"><?= e($error) ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
      <div class="success"><?= e($success) ?></div>
    <?php endif; ?>

    <form method="post" autocomplete="off">
      <input type="hidden" name="csrf_token" value="<?= e($token) ?>">

      <label>Title *</label>
      <input type="text" name="title" value="<?= e($title) ?>" required />

      <label>Author</label>
      <input type="text" name="author" value="<?= e($author) ?>" />

      <label>Category</label>
      <input type="text" name="category" value="<?= e($category) ?>" />

      <label>Description</label>
      <textarea name="description" rows="5" style="width:100%; padding:10px; border:1px solid #dfe3ea; border-radius:8px; font-size:14px;"><?= e($description) ?></textarea>

      <label style="display:flex; gap:8px; align-items:center; margin-top:12px;">
        <input type="checkbox" name="is_active" <?= $is_active ? 'checked' : '' ?> />
        Active (visible to users)
      </label>

      <button type="submit">Save Book</button>
    </form>
  </div>
</body>
</html>
