<?php
// ============================================================
// start_backup.php — Triggers backup.sh from the UI form
// All values (SSH user, dashboard IP, dest user) come from DB
// ============================================================

header('Content-Type: application/json');
require_once 'auth.php';
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$source_ip        = trim($_POST['source_ip']        ?? '');
$destination_ip   = trim($_POST['destination_ip']   ?? '');
$source_path      = trim($_POST['source_path']      ?? '');
$destination_path = trim($_POST['destination_path'] ?? '');

// ── Validate inputs ─────────────────────────────────────────
if (empty($source_ip) || empty($destination_ip) || empty($source_path) || empty($destination_path)) {
    http_response_code(400);
    echo json_encode(['error' => 'All four fields are required']);
    exit;
}
if (!filter_var($source_ip, FILTER_VALIDATE_IP)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid source IP address']);
    exit;
}
if (!filter_var($destination_ip, FILTER_VALIDATE_IP)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid destination IP address']);
    exit;
}
if (!preg_match('/^[a-zA-Z0-9\/\.\-\_]+$/', $source_path)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid characters in source path']);
    exit;
}
if (!preg_match('/^[a-zA-Z0-9\/\.\-\_]+$/', $destination_path)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid characters in destination path']);
    exit;
}

$db = getDB();

// ── Fetch ssh_username from servers table (dynamic, not hardcoded) ──
$stmt = $db->prepare("SELECT id, ssh_username FROM servers WHERE server_ip = ?");
$stmt->bind_param('s', $source_ip);
$stmt->execute();
$server = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$server) {
    $db->close();
    http_response_code(403);
    echo json_encode(['error' => 'Source IP is not an authorized server. Register it in the Servers tab first.']);
    exit;
}

$ssh_username = $server['ssh_username'];  // e.g. "ubuntu", "root", "deploy"

// Dashboard IP from settings
$settings_result = $db->query(
    "SELECT setting_key, setting_value
     FROM settings
     WHERE setting_key IN ('dashboard_ip','dest_username')"
);
$settings = [];

while ($row = $settings_result->fetch_assoc()) {

    error_log("ROW = " . print_r($row, true));

    $settings[$row['setting_key']] = $row['setting_value'];

    error_log("AFTER INSERT = " . print_r($settings, true));
}

error_log("FINAL SETTINGS = " . print_r($settings, true));



$dashboard_ip = $settings['dashboard_ip'] ?? '';

// Destination username from destination server
$stmt = $db->prepare("SELECT ssh_username FROM servers WHERE server_ip = ?");
$stmt->bind_param('s', $destination_ip);
$stmt->execute();
$dest_server = $stmt->get_result()->fetch_assoc();
$stmt->close();

$dest_username = $dest_server['ssh_username'] ?? '';



error_log("DASHBOARD_IP=[" . $dashboard_ip . "]");
if (empty($dashboard_ip) || !filter_var($dashboard_ip, FILTER_VALIDATE_IP)) {
    http_response_code(500);
    echo json_encode(['error' => 'Dashboard IP is not configured or invalid. Set it in Settings → System Configuration.']);
    exit;
}
if (empty($dest_username) || !preg_match('/^[a-zA-Z0-9_\-]{1,64}$/', $dest_username)) {
    http_response_code(500);
    echo json_encode(['error' => 'Destination SSH username is not configured or invalid. Set it in Settings → System Configuration.']);
    exit;
}

// ── Build shell command ──────────────────────────────────────
// backup.sh is executed LOCALLY on the dashboard system.
// It then uses SCP to pull data from source to destination.
// No SSH into remote servers here — scp handles that internally.
//
// Arguments passed to backup.sh:
//   $1 source_ip      $2 dest_ip         $3 source_path
//   $4 dest_path      $5 ssh_username    $6 dest_username
//   $7 dashboard_ip
$script = __DIR__ . '/backup.sh';
if (!file_exists($script)) {
    http_response_code(500);
    echo json_encode(['error' => 'backup.sh not found at: ' . $script]);
    exit;
}

$safe_src_ip       = escapeshellarg($source_ip);
$safe_dst_ip       = escapeshellarg($destination_ip);
$safe_src_path     = escapeshellarg($source_path);
$safe_dst_path     = escapeshellarg($destination_path);
$safe_ssh_user     = escapeshellarg($ssh_username);
$safe_dest_user    = escapeshellarg($dest_username);
$safe_dashboard_ip = escapeshellarg($dashboard_ip);

$log_file = __DIR__ . '/backup_debug.log';

$job_id = time();

error_log("SRC USER = " . $ssh_username);
error_log("DEST USER = " . $dest_username);

$cmd = '"C:\\Program Files\\Git\\bin\\bash.exe" ' .
    escapeshellarg($script) . ' ' .
    $safe_src_ip . ' ' .
    $safe_dst_ip . ' ' .
    $safe_src_path . ' ' .
    $safe_dst_path . ' ' .
    $safe_ssh_user . ' ' .
    $safe_dest_user . ' ' .
    $safe_dashboard_ip . ' ' .
    $job_id . " > " . escapeshellarg($log_file) . " 2>&1";

error_log("CMD = " . $cmd);
$output = shell_exec($cmd);


echo json_encode([
    'success'          => true,
    'message'          => 'Backup job started',
    'source_ip'        => $source_ip,
    'destination_ip'   => $destination_ip,
    'source_path'      => $source_path,
    'destination_path' => $destination_path,
    'ssh_username'     => $ssh_username,
    'dest_username'    => $dest_username,
    'dashboard_ip'     => $dashboard_ip,
]);
?>
