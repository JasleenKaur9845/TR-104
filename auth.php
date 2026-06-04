<?php
// ============================================================
// auth.php — Session Guard
// Include at the TOP of every protected page.
// Redirects to login.php if not authenticated.
// ============================================================

session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Admin username available to all protected pages
$admin_username = $_SESSION['admin_username'] ?? 'Admin';
?>
