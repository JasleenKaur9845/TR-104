<?php
// ============================================================
// api_backups.php — Returns backup jobs + stats as JSON
// Used by dashboard for live auto-refresh (every 2s)
// ============================================================

header('Content-Type: application/json');
require_once 'auth.php';
require_once 'config.php';

$db     = getDB();
$limit  = max(1, min(200, intval($_GET['limit'] ?? 50)));
$status = $_GET['status'] ?? '';

$allowed = ['RUNNING', 'SUCCESS', 'FAILED', ''];
if (!in_array($status, $allowed)) $status = '';

// ── Fetch jobs ──────────────────────────────────────────────
if ($status !== '') {
    $stmt = $db->prepare(
        "SELECT id, source_ip, destination_ip, source_path, destination_path,
                status, percentage, reason, created_at
         FROM backups
         WHERE status = ?
         ORDER BY created_at DESC
         LIMIT ?"
    );
    $stmt->bind_param('si', $status, $limit);
} else {
    $stmt = $db->prepare(
        "SELECT id, source_ip, destination_ip, source_path, destination_path,
                status, percentage, reason, created_at
         FROM backups
         ORDER BY created_at DESC
         LIMIT ?"
    );
    $stmt->bind_param('i', $limit);
}

$stmt->execute();
$result = $stmt->get_result();
$jobs   = [];
while ($row = $result->fetch_assoc()) {
    $jobs[] = $row;
}
$stmt->close();

// ── Stats summary ───────────────────────────────────────────
$stats_result = $db->query(
    "SELECT
        COUNT(*)                        AS total,
        SUM(status = 'RUNNING')         AS running,
        SUM(status = 'SUCCESS')         AS success,
        SUM(status = 'FAILED')          AS failed
     FROM backups"
);
$stats = $stats_result->fetch_assoc();
$db->close();

echo json_encode([
    'jobs'         => $jobs,
    'stats'        => $stats,
    'generated_at' => date('Y-m-d H:i:s'),
]);
?>
