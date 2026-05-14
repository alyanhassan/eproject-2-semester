<?php
require_once '../config/config.php';
require_once '../config/database.php';

$db = (new Database())->getConnection();
$error = ''; $success = '';
$token = sanitizeInput($_GET['token'] ?? '');
if (empty($token)) { header('Location: /auth/forgot_password.php'); exit(); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';
    if (strlen($password) < 6)    { $error = 'Password must be at least 6 characters.'; }
    elseif ($password !== $confirm){ $error = 'Passwords do not match.'; }
    else {
        try {
            $stmt = $db->prepare("SELECT user_id FROM password_resets WHERE token = ? AND expires_at > datetime('now') LIMIT 1");
            $stmt->execute([$token]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                $db->prepare("UPDATE users SET password = ? WHERE user_id = ?")->execute([password_hash($password, PASSWORD_DEFAULT), $row['user_id']]);
                $db->prepare("DELETE FROM password_resets WHERE token = ?")->execute([$token]);
                $success = 'Password reset successfully! You can now log in.';
            } else { $error = 'Invalid or expired reset link.'; }
        } catch (Exception $e) { $error = 'Server error. Please try again.'; }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password — VaxTrack</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<div class="auth-wrapper">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4">
                <div class="text-center mb-4"><div class="auth-logo"><i class="fas fa-syringe me-2"></i>VaxTrack</div></div>
                <div class="auth-card card p-4">
                    <h4 class="fw-bold mb-1">Reset Password</h4>
                    <p class="text-muted small mb-4">Enter your new password below</p>
                    <?php if ($success): ?>
                        <div class="alert alert-success border-0"><?= htmlspecialchars($success) ?></div>
                        <a href="/auth/login.php" class="btn btn-primary w-100">Login Now</a>
                    <?php else: ?>
                        <?php if ($error): ?><div class="alert alert-danger border-0 py-2 small"><?= htmlspecialchars($error) ?></div><?php endif; ?>
                        <form method="POST">
                            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
                            <div class="mb-3"><label class="form-label">New Password</label><input type="password" name="password" class="form-control" placeholder="Min. 6 characters" required></div>
                            <div class="mb-4"><label class="form-label">Confirm Password</label><input type="password" name="confirm_password" class="form-control" placeholder="Repeat password" required></div>
                            <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">Reset Password</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
