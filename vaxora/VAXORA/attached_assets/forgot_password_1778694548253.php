<?php
require_once '../config/database.php';
require_once '../config/config.php';

$db = (new Database())->getConnection();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = sanitizeInput($_POST['email']);

    $stmt = $db->prepare("SELECT user_id FROM users WHERE email=:email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {

        $token = bin2hex(random_bytes(32));

        $stmt = $db->prepare("INSERT INTO password_resets(user_id, token, expires_at)
        VALUES(:uid,:token,DATE_ADD(NOW(), INTERVAL 15 MINUTE))");

        $stmt->bindParam(':uid', $user['user_id']);
        $stmt->bindParam(':token', $token);
        $stmt->execute();

        $success = "Reset link created: auth/reset_password.php?token=$token";

    } else {
        $error = "Email not found";
    }
}
?>