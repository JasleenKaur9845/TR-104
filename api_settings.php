<?php
// ============================================================
// api_settings.php — Read and update settings table
// GET: returns all settings as key→value map
// POST: upserts one or more settings
// ============================================================

header('Content-Type: application/json');
require_once 'auth.php';
require_once 'config.php';

$db     = getDB();
$method = $_SERVER['REQUEST_METHOD'];

// ── GET: return all settings ────────────────────────────────
if ($method === 'GET') {
    $result   = $db->query("SELECT setting_key, setting_value FROM settings");
    $settings = [];
    while ($row = $result->fetch_assoc()) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
    $db->close();
    echo json_encode(['settings' => $settings]);
    exit;
}

// ── POST: upsert one or more settings ──────────────────────
if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;

    $allowed_keys = ['auto_refresh_interval', 'dashboard_ip', 'dest_username'];
    $errors = [];

    foreach ($allowed_keys as $key) {
        if (!array_key_exists($key, $input)) continue;
        $value = trim($input[$key]);

        // Per-key validation
        if ($key === 'auto_refresh_interval') {
            $value = (int)$value;
            if ($value < 2 || $value > 60) { $errors[] = 'auto_refresh_interval must be 2–60'; continue; }
            $value = (string)$value;
        }
        if ($key === 'dashboard_ip') {
            if (!filter_var($value, FILTER_VALIDATE_IP)) { $errors[] = 'dashboard_ip must be a valid IP address'; continue; }
        }
        if ($key === 'dest_username') {
            if (!preg_match('/^[a-zA-Z0-9_\-]{1,64}$/', $value)) { $errors[] = 'dest_username: only letters, numbers, dash, underscore'; continue; }
        }

        $stmt = $db->prepare(
            "INSERT INTO settings (setting_key, setting_value)
             VALUES (?, ?)
             ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)"
        );
        $stmt->bind_param('ss', $key, $value);
        if (!$stmt->execute()) $errors[] = "Failed to save {$key}: " . $stmt->error;
        $stmt->close();
    }

    $db->close();

    if ($errors) {
        http_response_code(400);
        echo json_encode(['error' => implode('; ', $errors)]);
    } else {
        echo json_encode(['success' => true, 'message' => 'Settings saved']);
    }
    exit;
}

http_response_code(405);
echo json_encode(['error' => 'Method not allowed']);
$db->close();
?>
