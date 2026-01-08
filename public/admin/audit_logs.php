<?php
// Library/public/admin/audit_logs.php
declare(strict_types=1);

require_once __DIR__ . '/../../app/helpers/security.php';
require_once __DIR__ . '/../../app/middleware/admin_auth.php';
require_once __DIR__ . '/../../app/db/db.php';

require_admin_login();

$pdo = db();

$q = trim($_GET['q'] ?? '');

if ($q !== '') {
    $like = "%{$q}%";
    $stmt = $pdo->prepare("
        SELECT actor_type, actor_id, action, details, created_at
        FROM audit_logs
        WHERE action LIKE ? OR details LIKE ? OR actor_type LIKE ?
        ORDER BY created_at DESC
        LIMIT 500
    ");
    $stmt->execute([$like, $like, $like]);
} else {
    $stmt = $pdo->query("
        SELECT actor_type, actor_id, action, details, created_at
        FROM audit_logs
        ORDER BY created_at DESC
        LIMIT 500
    ");
}

$logs = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Audit Logs</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
  <div class="container" style="max-width: 1100px;">
    <h1>Audit Logs</h1>

    <p class="muted">
      <a href="dashboard.php">Dashboard</a>
      • <a href="books_list.php">Books</a>
      • <a href="users_list.php">Users</a>
      • <a href="logout.php">Logout</a>
    </p>

    <form method="get" style="display:flex; gap:10px; margin:12px 0;">
      <input type="text" name="q" placeholder="Search action/details..." value="<?= e($q) ?>" />
      <button type="submit" style="width:auto; padding:10px 14px;">Search</button>
    </form>

    <?php if (!$logs): ?>
      <p class="muted">No audit logs found.</p>
    <?php else: ?>
      <div style="overflow:auto;">
        <table style="width:100%; border-collapse:collapse;">
          <thead>
            <tr>
              <th style="padding:10px; border-bottom:1px solid #dfe3ea; text-align:left;">Time</th>
              <th style="padding:10px; border-bottom:1px solid #dfe3ea; text-align:left;">Actor</th>
              <th style="padding:10px; border-bottom:1px solid #dfe3ea; text-align:left;">Action</th>
              <th style="padding:10px; border-bottom:1px solid #dfe3ea; text-align:left;">Details</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($logs as $log): ?>
              <tr>
                <td style="padding:10px; border-bottom:1px solid #eef1f6;">
                  <?= e((string)$log['created_at']) ?>
                </td>
                <td style="padding:10px; border-bottom:1px solid #eef1f6;">
                  <?= e((string)$log['actor_type']) ?> #<?= (int)$log['actor_id'] ?>
                </td>
                <td style="padding:10px; border-bottom:1px solid #eef1f6;">
                  <?= e((string)$log['action']) ?>
                </td>
                <td style="padding:10px; border-bottom:1px solid #eef1f6;">
                  <?= e((string)($log['details'] ?? '')) ?>
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
