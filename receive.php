
<?php
// ============================================================
// receive.php — Receives backup status updates from backup.sh
// Called via curl POST from the shell script on source servers.
// ============================================================

header('Content-Type: application/json');
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Collect and sanitize input
$job_id           = trim($_POST['job_id']           ?? '');
$source_ip        = trim($_POST['source_ip']        ?? '');
$destination_ip   = trim($_POST['destination_ip']   ?? '');
$source_path      = trim($_POST['source_path']      ?? '');
$destination_path = trim($_POST['destination_path'] ?? '/backups');
$status           = trim($_POST['status']           ?? '');
$percentage       = intval($_POST['percentage']     ?? 0);
$reason           = trim($_POST['reason']           ?? '');

// Validate required fields
if (empty($source_ip) || empty($destination_ip) || empty($source_path) || empty($status)) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required fields: source_ip, destination_ip, source_path, status']);
    exit;
}

$allowed_statuses = ['RUNNING', 'SUCCESS', 'FAILED'];
if (!in_array($status, $allowed_statuses)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid status. Must be RUNNING, SUCCESS, or FAILED']);
    exit;
}

$percentage = max(0, min(100, $percentage));

$db = getDB();

// ── Check source_ip is authorized ──────────────────────────
$stmt = $db->prepare("SELECT id FROM servers WHERE server_ip = ?");
$stmt->bind_param('s', $source_ip);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    $stmt->close();
    $db->close();
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized server IP: ' . $source_ip]);
    exit;
}
$stmt->close();

// ── Insert backup record ────────────────────────────────────
//$stmt = $db->prepare(
//    "INSERT INTO backups

        //(source_ip, destination_ip, source_path, destination_path, status, percentage, reason)
     //VALUES (?, ?, ?, ?, ?, ?, ?)"
//);

$check = $db->prepare("SELECT job_id FROM backups WHERE job_id = ?");
$check->bind_param("s", $job_id);
$check->execute();
$res = $check->get_result();

if ($res->num_rows > 0) {

    $stmt = $db->prepare("
        UPDATE backups
        SET
            source_ip=?,
            destination_ip=?,
            source_path=?,
            destination_path=?,
            status=?,
            percentage=?,
            reason=?
        WHERE job_id=?
    ");

    $stmt->bind_param(
        "sssssiss",
        $source_ip,
        $destination_ip,
        $source_path,
        $destination_path,
        $status,
        $percentage,
        $reason,
        $job_id
    );

} else {

    $stmt = $db->prepare("
        INSERT INTO backups
        (job_id, source_ip, destination_ip, source_path, destination_path, status, percentage, reason)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "ssssssis",
        $job_id,
        $source_ip,
        $destination_ip,
        $source_path,
        $destination_path,
        $status,
        $percentage,
        $reason
    );
}

if ($stmt->execute()) {
    $id = $stmt->insert_id;
    $stmt->close();
    $db->close();
    echo json_encode(['success' => true, 'id' => $id]);
} else {
    $err = $stmt->error;
    $stmt->close();
    $db->close();
    http_response_code(500);
    echo json_encode(['error' => 'Database insert failed: ' . $err]);
}
?>

