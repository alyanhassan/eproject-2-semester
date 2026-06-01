<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';

if (isLoggedIn()) redirect(dashboardUrl($_SESSION['user']['role']));

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role     = $_POST['role'] ?? 'parent';
    if (!$email || !$password) {
        $error = 'Please fill in all fields.';
    } else {
        $user = loginUser($email, $password, $role);
        if ($user) {
            setFlash('success', 'Welcome back, ' . $user['name'] . '!');
            redirect(dashboardUrl($user['role']));
        } else {
            $error = 'Invalid email, password, or account type.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Login — VAXORA</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/style.css">
</head>
<body>
<div class="auth-page">
  <div class="auth-card">
    <div class="auth-logo">VAX<span>ORA</span></div>
    <h2 class="auth-title">Welcome Back</h2>
    <p class="auth-subtitle">Sign in to your VAXORA account</p>

    <?php if ($error): ?>
      <div class="alert alert-error"><?= e($error) ?></div>
    <?php endif; ?>
    <?php echo showFlash(); ?>

    <form method="POST">
      <div class="form-group">
        <label class="form-label">Account Type</label>
        <select name="role" class="form-control" required>
          <option value="parent">Parent</option>
          <option value="hospital">Hospital</option>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Email Address</label>
        <input type="email" name="email" class="form-control" placeholder="you@example.com" value="<?= e($_POST['email'] ?? '') ?>" required>
      </div>
      <div class="form-group">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" placeholder="••••••••" required>
      </div>
      <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;border-radius:10px">Sign In</button>
    </form>

    <div class="divider"></div>
    <p class="text-center text-muted" style="font-size:0.9rem">
      Don't have an account? <a href="<?= SITE_URL ?>/register.php" style="color:#4a7c00;font-weight:600">Register here</a>
    </p>
    <p class="text-center mt-12" style="font-size:0.85rem">
      <a href="<?= SITE_URL ?>/admin/login.php" style="color:#2467E3">Admin Login</a>
    </p>
    <div class="divider"></div>
    <p class="text-center" style="font-size:0.82rem;color:#888">
      Demo: <b>ayesha@example.com</b> / <b>password</b><br>
      Hospital: <b>agakhan@hospital.pk</b> / <b>password</b>
    </p>
    <p class="text-center mt-8"><a href="<?= SITE_URL ?>/" style="color:#5a6a40;font-size:0.85rem">&#8592; Back to website</a></p>
  </div>
</div>
</body>
</html>
