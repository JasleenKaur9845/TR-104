<?php
// ============================================================
// login.php — Admin Login Page
// ============================================================
session_start();

if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: index.php');
    exit;
}

require_once 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($username) || empty($password)) {
        $error = 'Both username and password are required.';
    } else {
        $db   = getDB();
        $stmt = $db->prepare("SELECT id, username, password_hash FROM admins WHERE username = ?");
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $admin = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        $db->close();

        if ($admin && password_verify($password, $admin['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id']        = $admin['id'];
            $_SESSION['admin_username']  = $admin['username'];
            header('Location: index.php');
            exit;
        } else {
            $error = 'Invalid username or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>BackupVault — Login</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;500;600&family=IBM+Plex+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<script>
// Apply saved theme before render to prevent flash
(function(){
  if (localStorage.getItem('bv_theme') === 'light') {
    document.documentElement.classList.add('lm');
  }
})();
</script>
<style>
:root {
  --bg:#0d0f14; --bg2:#13161e; --bg3:#1a1e2a;
  --border:#252a38; --border2:#2e3448;
  --text:#c8cfe0; --text-dim:#5a647a; --text-mid:#8892a4;
  --accent:#3b82f6; --red:#ef4444; --red-dim:#7f1d1d;
  --font-mono:'IBM Plex Mono',monospace;
  --font-sans:'IBM Plex Sans',sans-serif;
}
html.lm body, body.light-mode {
  --bg:#f0f2f7; --bg2:#ffffff; --bg3:#e8ebf2;
  --border:#d1d7e4; --border2:#b8c0d4;
  --text:#1a2035; --text-dim:#8892a4; --text-mid:#4a5568;
  --accent:#2563eb; --red:#dc2626; --red-dim:#fecaca;
}
*,*::before,*::after { box-sizing:border-box; margin:0; padding:0; }
body {
  min-height:100vh; background:var(--bg); color:var(--text);
  font-family:var(--font-sans); display:flex;
  align-items:center; justify-content:center;
  position:relative; overflow:hidden;
}
body::before {
  content:''; position:fixed; inset:0;
  background-image:linear-gradient(var(--border) 1px,transparent 1px),
    linear-gradient(90deg,var(--border) 1px,transparent 1px);
  background-size:40px 40px; opacity:.3; pointer-events:none;
}
body::after {
  content:''; position:fixed; top:30%; left:50%;
  transform:translate(-50%,-50%); width:600px; height:600px;
  background:radial-gradient(circle,rgba(59,130,246,.07) 0%,transparent 70%);
  pointer-events:none;
}
.wrap {
  position:relative; z-index:10; width:100%;
  max-width:400px; padding:24px;
  animation:fadeUp .4s ease both;
}
@keyframes fadeUp { from{opacity:0;transform:translateY(20px);} to{opacity:1;transform:translateY(0);} }
.brand { text-align:center; margin-bottom:32px; }
.brand-icon {
  width:52px; height:52px; background:var(--accent); border-radius:12px;
  display:inline-flex; align-items:center; justify-content:center;
  font-size:24px; margin-bottom:14px;
  box-shadow:0 0 32px rgba(59,130,246,.3);
}
.brand-title { font-family:var(--font-mono); font-size:20px; font-weight:600; color:var(--text); letter-spacing:.04em; }
.brand-sub   { font-family:var(--font-mono); font-size:11px; color:var(--text-dim); margin-top:4px; letter-spacing:.1em; text-transform:uppercase; }
.card { background:var(--bg2); border:1px solid var(--border); border-radius:12px; padding:32px; box-shadow:0 24px 64px rgba(0,0,0,.5); }
.card-title { font-family:var(--font-mono); font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:.1em; color:var(--text-dim); margin-bottom:24px; }
.error-box {
  background:rgba(239,68,68,.08); border:1px solid var(--red-dim);
  border-radius:6px; padding:10px 14px; font-family:var(--font-mono);
  font-size:11px; color:var(--red); margin-bottom:16px;
}
.fg { display:flex; flex-direction:column; gap:6px; margin-bottom:16px; }
.fl { font-family:var(--font-mono); font-size:10px; font-weight:500; text-transform:uppercase; letter-spacing:.08em; color:var(--text-dim); }
.fi {
  background:var(--bg3); border:1px solid var(--border); border-radius:6px;
  padding:10px 12px; font-family:var(--font-mono); font-size:13px;
  color:var(--text); outline:none; width:100%;
  transition:border-color .15s,box-shadow .15s;
}
.fi:focus { border-color:var(--accent); box-shadow:0 0 0 3px rgba(59,130,246,.12); }
.fi::placeholder { color:var(--text-dim); }
.btn-submit {
  width:100%; padding:11px; background:var(--accent); color:#fff;
  border:none; border-radius:6px; font-family:var(--font-mono);
  font-size:13px; font-weight:600; cursor:pointer;
  transition:background .15s,transform .1s; margin-top:4px;
}
.btn-submit:hover { background:#2563eb; transform:translateY(-1px); }
.footer { text-align:center; margin-top:20px; font-family:var(--font-mono); font-size:10px; color:var(--text-dim); line-height:1.8; }
</style>
</head>
<body>
<div class="wrap">
  <div class="brand">
    <div class="brand-icon">🗄️</div>
    <div class="brand-title">BackupVault</div>
    <div class="brand-sub">Centralised Backup Monitor</div>
  </div>
  <div class="card">
    <div class="card-title">Admin Login</div>
    <?php if ($error): ?>
      <div class="error-box">⚠ <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="POST" action="login.php">
      <div class="fg">
        <label class="fl">Username</label>
        <input class="fi" type="text" name="username"
          value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
          placeholder="admin" autocomplete="username" autofocus required>
      </div>
      <div class="fg">
        <label class="fl">Password</label>
        <input class="fi" type="password" name="password"
          placeholder="••••••••" autocomplete="current-password" required>
      </div>
      <button type="submit" class="btn-submit">→ Sign In</button>
    </form>
  </div>
  <div class="footer">
    <div>Centralised Backup Monitoring System</div>
    <div>Unauthorized access is prohibited</div>
  </div>
</div>
<script>
if (localStorage.getItem('bv_theme') === 'light') {
  document.body.classList.add('light-mode');
}
</script>
</body>
</html>
