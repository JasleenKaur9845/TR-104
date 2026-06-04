<?php
// ============================================================
// api_servers.php — Manage authorized server IPs
// GET: list all | POST: add | DELETE: remove by id
// ============================================================

header('Content-Type: application/json');
require_once 'auth.php';
require_once 'config.php';

$db     = getDB();
$method = $_SERVER['REQUEST_METHOD'];

// ── GET: list all servers ───────────────────────────────────
if ($method === 'GET') {
    $result  = $db->query("SELECT id, server_ip, server_name, ssh_username, created_at FROM servers ORDER BY created_at DESC");
    $servers = [];
    while ($row = $result->fetch_assoc()) $servers[] = $row;
    $db->close();
    echo json_encode(['servers' => $servers]);
    exit;
}

// ── POST: add a new server ──────────────────────────────────
if ($method === 'POST') {
    $input        = json_decode(file_get_contents('php://input'), true) ?? $_POST;
    $server_ip    = trim($input['server_ip']    ?? '');
    $server_name  = trim($input['server_name']  ?? '');
    $ssh_username = trim($input['ssh_username'] ?? 'root');

    if (empty($server_ip) || empty($server_name)) {
        http_response_code(400);
        echo json_encode(['error' => 'server_ip and server_name are required']);
        exit;
    }
    if (!filter_var($server_ip, FILTER_VALIDATE_IP)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid IP address format']);
        exit;
    }
    // Validate ssh_username: alphanumeric, dash, underscore only
    if (!preg_match('/^[a-zA-Z0-9_\-]{1,64}$/', $ssh_username)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid SSH username (alphanumeric, dash, underscore only)']);
        exit;
    }

    $stmt = $db->prepare("INSERT INTO servers (server_ip, server_name, ssh_username) VALUES (?, ?, ?)");
    $stmt->bind_param('sss', $server_ip, $server_name, $ssh_username);

    if ($stmt->execute()) {
        $id = $stmt->insert_id;
        $stmt->close(); $db->close();
        echo json_encode(['success' => true, 'id' => $id]);
    } else {
        $err = $stmt->error;
        $stmt->close(); $db->close();
        if (strpos($err, 'Duplicate') !== false) {
            http_response_code(409);
            echo json_encode(['error' => 'This IP is already registered']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to add server: ' . $err]);
        }
    }
    exit;
}

// ── DELETE: remove a server ─────────────────────────────────
if ($method === 'DELETE') {
    $input = json_decode(file_get_contents('php://input'), true) ?? [];
    $id    = intval($input['id'] ?? 0);

    if ($id <= 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Valid server id required']);
        exit;
    }

    $stmt = $db->prepare("DELETE FROM servers WHERE id = ?");
    $stmt->bind_param('i', $id);

    if ($stmt->execute() && $stmt->affected_rows > 0) {
        $stmt->close(); $db->close();
        echo json_encode(['success' => true]);
    } else {
        $err = $stmt->error;
        $stmt->close(); $db->close();
        http_response_code(404);
        echo json_encode(['error' => 'Server not found or already deleted']);
    }
    exit;
}

http_response_code(405);
echo json_encode(['error' => 'Method not allowed']);
$db->close();
?>
