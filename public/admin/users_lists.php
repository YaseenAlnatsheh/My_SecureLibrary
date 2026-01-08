<?php
// Library/public/admin/users_list.php
declare(strict_types=1);

require_once __DIR__ . '/../../app/helpers/security.php';
require_once __DIR__ . '/../../app/middleware/admin_auth.php';
require_once __DIR__ . '/../../app/middleware/csrf.php';
require_once __DIR__ . '/../../app/middleware/ratelimit.php';
require_once __DIR__ . '/../../app/db/db.php';
require_once __DIR__ . '/../../app/helpers/audit.php';
require_once __DIR__ . '/../../app/helpers/crypto_aes.php';

require_admin_login();

$pdo = db();

$actionMsg = '';
$actionErr = '';

if (is_post()) {
    csrf_validate_or_die();

    $action = trim($_POST['action'] ?? '');
    $userId = (int)($_POST['user_id'] ?? 0);

    if ($userId <= 0) {
        $actionErr = 'Invalid user.';
    } else {
        if ($action === 'toggle_active') {
            $stmt = $pdo->prepare("UPDATE users SET is_active = IF(is_active=1,0,1) WHERE id = ?");
            $stmt->execute([$userId]);

            audit_log('admin', (int)($_SESSION['admin_id'] ?? 0), 'USER_STATUS_CHANGE', 'User ID: ' . $userId);
            $actionMsg = 'User status updated.';
        } elseif ($action === 'delete_user') {
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$userId]);

            audit_log('admin', (int)($_SESSION['admin_id'] ?? 0), 'USER_DELETE', 'Deleted user ID: ' . $userId);
            $actionMsg = 'User deleted.';
        } else {
            $actionErr = 'Unknown action.';
        }
    }
}

$q = trim($_GET['q'] ?? '');

if ($q !== '') {
    $like = "%{$q}%";
    $stmt = $pdo->prepare("
        SELECT id, full_name, email, phone_enc, is_active, created_at
        FROM users
        WHERE full_name LIKE ? OR email LIKE ?
        ORDER BY created_at DESC
    ");
    $stmt->execute([$like, $like]);
} else {
    $stmt = $pdo->query("
        SELECT id, full_name, email, phone_enc, is_active, created_at
        FROM users
        ORDER BY created_at DESC
    ");
}

$users = $stmt->fetchAll();

$token = csrf_token();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Manage Users</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
  <div class="container" style="max-width: 1100px;">
    <h1>Manage Users</h1>

    <p class="muted">
      Admin: <b><?= e($_SESSION['admin_name'] ?? 'Admin') ?></b>
      • <a href="dashboard.php">Dashboard</a>
      • <a href="books_list.php">Books</a>
      • <a href="audit_logs.php">Audit Logs</a>
      • <a href="logout.php">Logout</a>
    </p>

    <?php if ($actionErr): ?>
      <div class="alert"><?= e($actionErr) ?></div>
    <?php endif; ?>

    <?php if ($actionMsg): ?>
      <div class="success"><?= e($actionMsg) ?></div>
    <?php endif; ?>

    <div style="display:flex; justify-content:space-between; gap:10px; align-items:center; margin: 12px 0;">
      <form method="get" style="display:flex; gap:10px; flex:1;">
        <input type="text" name="q" placeholder="Search by name or email..." value="<?= e($q) ?>" />
        <button type="submit" style="width:auto; padding:10px 14px;">Search</button>
      </form>
    </div>

    <?php if (!$users): ?>
      <p class="muted">No users found.</p>
    <?php else: ?>
      <div style="overflow:auto;">
        <table style="width:100%; border-collapse:collapse;">
          <thead>
            <tr>
              <th style="text-align:left; border-bottom:1px solid #dfe3ea; padding:10px;">Name</th>
              <th style="text-align:left; border-bottom:1px solid #dfe3ea; padding:10px;">Email</th>
              <th style="text-align:left; border-bottom:1px solid #dfe3ea; padding:10px;">Phone (Decrypted)</th>
              <th style="text-align:left; border-bottom:1px solid #dfe3ea; padding:10px;">Status</th>
              <th style="text-align:left; border-bottom:1px solid #dfe3ea; padding:10px;">Created</th>
              <th style="text-align:left; border-bottom:1px solid #dfe3ea; padding:10px;">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($users as $u): ?>
              <?php
                $isActive = ((int)$u['is_active'] === 1);
                $phone = aes_decrypt($u['phone_enc'] ?? null);
              ?>
              <tr>
                <td style="border-bottom:1px solid #eef1f6; padding:10px;"><?= e((string)$u['full_name']) ?></td>
                <td style="border-bottom:1px solid #eef1f6; padding:10px;"><?= e((string)$u['email']) ?></td>
                <td style="border-bottom:1px solid #eef1f6; padding:10px;"><?= e((string)($phone ?? '')) ?></td>
                <td style="border-bottom:1px solid #eef1f6; padding:10px;">
                  <?= $isActive ? 'Active' : 'Disabled' ?>
                </td>
                <td style="border-bottom:1px solid #eef1f6; padding:10px;"><?= e((string)$u['created_at']) ?></td>
                <td style="border-bottom:1px solid #eef1f6; padding:10px; white-space:nowrap;">

                  <form method="post" style="display:inline;">
                    <input type="hidden" name="csrf_token" value="<?= e($token) ?>">
                    <input type="hidden" name="action" value="toggle_active">
                    <input type="hidden" name="user_id" value="<?= (int)$u['id'] ?>">
                    <button type="submit" style="width:auto; padding:8px 10px;">
                      <?= $isActive ? 'Disable' : 'Activate' ?>
                    </button>
                  </form>

                  <form method="post" style="display:inline;" onsubmit="return confirm('Delete this user? This cannot be undone.');">
                    <input type="hidden" name="csrf_token" value="<?= e($token) ?>">
                    <input type="hidden" name="action" value="delete_user">
                    <input type="hidden" name="user_id" value="<?= (int)$u['id'] ?>">
                    <button type="submit" style="width:auto; padding:8px 10px; background:#d93025;">
                      Delete
                    </button>
                  </form>

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
