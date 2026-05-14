<?php
require_once '../config/config.php';
require_once '../config/database.php';

$db = (new Database())->getConnection();
$error = ''; $success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitizeInput($_POST['email'] ?? '');
    if (empty($email)) { $error = 'Please enter your email address.'; }
    else {
        $stmt = $db->prepare("SELECT user_id FROM users WHERE email = :email AND status = 'active'");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            $token = bin2hex(random_bytes(32));
            $db->prepare("DELETE FROM password_resets WHERE user_id = ?")->execute([$user['user_id']]);
            $stmt2 = $db->prepare("INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, datetime('now', '+30 minutes'))");
            $stmt2->execute([$user['user_id'], $token]);
            $success = "Password reset link generated. <strong>For demo purposes:</strong><br><a href='/auth/reset_password.php?token=$token' class='alert-link'>Click here to reset password</a>";
        } else {
            $error = 'No active account found with that email address.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password — VaxTrack</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<div class="auth-wrapper">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4">
                <div class="text-center mb-4">
                    <div class="auth-logo"><i class="fas fa-syringe me-2"></i>VaxTrack</div>
                </div>
                <div class="auth-card card p-4">
                    <h4 class="fw-bold mb-1">Forgot Password</h4>
                    <p class="text-muted small mb-4">Enter your email to receive a reset link</p>
                    <?php if ($success): ?><div class="alert alert-success border-0 small"><?= $success ?></div><?php endif; ?>
                    <?php if ($error): ?><div class="alert alert-danger border-0 py-2 small"><?= htmlspecialchars($error) ?></div><?php endif; ?>
                    <form method="POST">
                        <div class="mb-4">
                            <label class="form-label">Email Address</label>
                            <input type="email" name="email" class="form-control" placeholder="you@example.com" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">Send Reset Link</button>
                    </form>
                    <hr class="my-3">
                    <p class="text-center small mb-0"><a href="/auth/login.php" class="text-primary text-decoration-none"><i class="fas fa-arrow-left me-1"></i>Back to Login</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
