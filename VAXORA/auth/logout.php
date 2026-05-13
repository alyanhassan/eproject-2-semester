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

session_destroy();
header("Location: /auth/login.php");
exit();
