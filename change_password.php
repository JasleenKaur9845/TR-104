<?php
// ============================================================
// change_password.php — Update admin password
// ============================================================

header('Content-Type: application/json');
require_once 'auth.php';
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$input   = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$current = trim($input['current_password'] ?? '');
$new     = trim($input['new_password']     ?? '');
$confirm = trim($input['confirm_password'] ?? '');

if (empty($current) || empty($new) || empty($confirm)) {
    http_response_code(400);
    echo json_encode(['error' => 'All three password fields are required']);
    exit;
}
if ($new !== $confirm) {
    http_response_code(400);
    echo json_encode(['error' => 'New passwords do not match']);
    exit;
}
if (strlen($new) < 6) {
    http_response_code(400);
    echo json_encode(['error' => 'New password must be at least 6 characters']);
    exit;
}

$db   = getDB();
$id   = (int) $_SESSION['admin_id'];
$stmt = $db->prepare("SELECT password_hash FROM admins WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$row || !password_verify($current, $row['password_hash'])) {
    $db->close();
    http_response_code(403);
    echo json_encode(['error' => 'Current password is incorrect']);
    exit;
}

$new_hash = password_hash($new, PASSWORD_BCRYPT);
$stmt = $db->prepare("UPDATE admins SET password_hash = ? WHERE id = ?");
$stmt->bind_param('si', $new_hash, $id);

if ($stmt->execute()) {
    $stmt->close(); $db->close();
    echo json_encode(['success' => true, 'message' => 'Password updated successfully']);
} else {
    $err = $stmt->error;
    $stmt->close(); $db->close();
    http_response_code(500);
    echo json_encode(['error' => 'Failed to update password: ' . $err]);
}
?>
