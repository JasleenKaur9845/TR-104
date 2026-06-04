<?php
// ============================================================
// api_logs.php — Paginated logs with search + status filter
// ============================================================

header('Content-Type: application/json');
require_once 'auth.php';
require_once 'config.php';

$db    = getDB();
$page  = max(1, intval($_GET['page']  ?? 1));
$limit = max(5, min(100, intval($_GET['limit'] ?? 20)));
$offset = ($page - 1) * $limit;

$status = $_GET['status'] ?? '';
$search = trim($_GET['search'] ?? '');

$allowed = ['RUNNING', 'SUCCESS', 'FAILED', ''];
if (!in_array($status, $allowed)) $status = '';

// Build WHERE
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

// Count total matching records
$count_stmt = $db->prepare("SELECT COUNT(*) AS total FROM backups $where_sql");
if ($types) $count_stmt->bind_param($types, ...$params);
$count_stmt->execute();
$total = (int) $count_stmt->get_result()->fetch_assoc()['total'];
$count_stmt->close();

// Fetch page of records
$data_params = array_merge($params, [$limit, $offset]);
$data_types  = $types . 'ii';
$stmt = $db->prepare(
    "SELECT id, source_ip, destination_ip, source_path, destination_path,
            status, percentage, reason, created_at
     FROM backups
     $where_sql
     ORDER BY created_at DESC
     LIMIT ? OFFSET ?"
);
$stmt->bind_param($data_types, ...$data_params);
$stmt->execute();
$result = $stmt->get_result();

$logs = [];
while ($row = $result->fetch_assoc()) {
    $logs[] = $row;
}
$stmt->close();
$db->close();

echo json_encode([
    'logs'        => $logs,
    'total'       => $total,
    'page'        => $page,
    'limit'       => $limit,
    'total_pages' => (int) ceil($total / $limit),
]);
?>
