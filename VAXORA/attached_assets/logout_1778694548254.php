<?php
session_start();

try {
    require_once '../config/database.php';
    $db = (new Database())->getConnection();

    if (isset($_SESSION['user_id'])) {
        $stmt = $db->prepare("INSERT INTO system_logs(user_id, action, ip_address)
        VALUES(:uid,'logout',:ip)");

        $ip = $_SERVER['REMOTE_ADDR'];

        $stmt->bindParam(':uid', $_SESSION['user_id']);
        $stmt->bindParam(':ip', $ip);
        $stmt->execute();
    }

} catch (Exception $e) {
    error_log($e->getMessage());
}

session_destroy();
header("Location: login.php");
exit();
?>