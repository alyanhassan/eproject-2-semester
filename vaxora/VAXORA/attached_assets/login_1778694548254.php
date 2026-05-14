<?php
require_once '../config/database.php';
require_once '../config/config.php';

$database = new Database();
$db = $database->getConnection();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitizeInput($_POST['email']);
    $password = $_POST['password'];
    $selected_role = sanitizeInput($_POST['role']);

    $stmt = $db->prepare("SELECT * FROM users WHERE email = :email AND status='active'");
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        if ($user['role'] !== $selected_role) {
            $error = "Access Denied: You do not have the permissions for this role.";
        } else {
            // SET PROFESSIONAL SESSION DATA
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['role']    = $user['role'];
            $_SESSION['email']   = $user['email'];
            
            // Log the login for security audit
            $log = $db->prepare("INSERT INTO system_logs (user_id, action, ip_address) VALUES (?, 'login', ?)");
            $log->execute([$user['user_id'], $_SERVER['REMOTE_ADDR']]);

            // Redirect based on role
            switch($user['role']) {
                case 'admin':    header("Location: ../admin/dashboard.php"); break;
                case 'hospital': header("Location: ../hospital/dashboard.php"); break;
                default:         header("Location: ../dashboard.php"); // Parents
            }
            exit();
        }
    } else {
        $error = "Invalid email or password.";
    }
}
?>