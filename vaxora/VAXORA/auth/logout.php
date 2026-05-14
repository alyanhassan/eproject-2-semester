<?php
require_once '../config/config.php';
require_once '../config/database.php';

try {
    $db = (new Database())->getConnection();
    if (isset($_SESSION['user_id'])) {
        $stmt = $db->prepare("INSERT INTO system_logs (user_id, action, ip_address) VALUES (?, 'logout', ?)");
        $stmt->execute([$_SESSION['user_id'], $_SERVER['REMOTE_ADDR'] ?? '']);
    }
} catch (Exception $e) {}

$_SESSION = [];

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

session_destroy();
redirect('/auth/login.php');
