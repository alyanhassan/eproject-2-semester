<?php
session_start();

require_once '../config/database.php';
require_once '../config/config.php';

$db = (new Database())->getConnection();

$error = '';
$success = '';

$token = $_GET['token'] ?? '';

if (empty($token)) {
    die("Invalid reset link");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    }
    elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters!";
    }
    else {

        try {

            // validate token
            $stmt = $db->prepare("
                SELECT user_id 
                FROM password_resets 
                WHERE token = :token 
                AND expires_at > NOW()
                LIMIT 1
            ");

            $stmt->bindParam(':token', $token);
            $stmt->execute();

            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row) {

                $hashed = password_hash($password, PASSWORD_DEFAULT);

                // update password
                $update = $db->prepare("
                    UPDATE users 
                    SET password = :pass 
                    WHERE user_id = :uid
                ");

                $update->bindParam(':pass', $hashed);
                $update->bindParam(':uid', $row['user_id']);
                $update->execute();

                // delete used token
                $del = $db->prepare("
                    DELETE FROM password_resets 
                    WHERE token = :token
                ");

                $del->bindParam(':token', $token);
                $del->execute();

                $success = "Password reset successful! You can now login.";

            } else {
                $error = "Invalid or expired reset link!";
            }

        } catch (Exception $e) {
            error_log($e->getMessage());
            $error = "Server error. Try again later.";
        }
    }
}
?>