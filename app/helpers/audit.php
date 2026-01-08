<?php
// Library/app/helpers/audit.php
declare(strict_types=1);

require_once __DIR__ . '/../db/db.php';

function audit_log(string $actorType, int $actorId, string $action, ?string $details = null): void
{
    try {
        $pdo = db();
        $stmt = $pdo->prepare("
            INSERT INTO audit_logs (actor_type, actor_id, action, details)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$actorType, $actorId, $action, $details]);
    } catch (Throwable $e) {
        // Never break the app because of logging
    }
}
