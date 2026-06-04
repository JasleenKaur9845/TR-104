<?php
// ============================================================
// export_csv.php — Export backup logs as CSV download
// Reflects current filter/search state from Logs tab
// ============================================================

require_once 'auth.php';
require_once 'config.php';

$status = trim($_GET['status'] ?? '');
$search = trim($_GET['search'] ?? '');
$limit  = min((int)($_GET['limit'] ?? 9999), 50000);

$allowed = ['RUNNING', 'SUCCESS', 'FAILED', ''];
if (!in_array($status, $allowed)) $status = '';

$db     = getDB();
$where  = [];
$params = [];
$types  = '';

if ($status !== '') {
    $where[]  = 'status = ?';
    $params[] = $status;
    $types   .= 's';
}
if ($search !== '') {
    $like     = '%' . $search . '%';
    $where[]  = '(source_ip LIKE ? OR destination_ip LIKE ? OR source_path LIKE ? OR destination_path LIKE ?)';
    $params[] = $like; $params[] = $like; $params[] = $like; $params[] = $like;
    $types   .= 'ssss';
}

$where_sql = $where ? 'WHERE ' . implode(' AND ', $where) : '';
$params[]  = $limit;
$types    .= 'i';

$stmt = $db->prepare(
    "SELECT id, source_ip, destination_ip, source_path, destination_path,
            status, percentage, reason, created_at
     FROM backups $where_sql ORDER BY created_at DESC LIMIT ?"
);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

// ── Stream CSV headers ──────────────────────────────────────
$filename = 'backup_logs_' . date('Y-m-d_H-i-s') . '.csv';
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Pragma: no-cache');
header('Expires: 0');

$out = fopen('php://output', 'w');
fwrite($out, "\xEF\xBB\xBF"); // UTF-8 BOM for Excel compatibility

// Header row
fputcsv($out, ['#', 'Source IP', 'Destination IP', 'Source Path', 'Destination Path', 'Status', 'Progress (%)', 'Reason / Message', 'Timestamp']);

// Data rows
$i = 1;
while ($row = $result->fetch_assoc()) {
    fputcsv($out, [
        $i++,
        $row['source_ip'],
        $row['destination_ip'],
        $row['source_path'],
        $row['destination_path'] ?? '',
        $row['status'],
        $row['percentage'],
        $row['reason'] ?? '',
        $row['created_at'],
    ]);
}

fclose($out);
$stmt->close();
$db->close();
exit;
?>
