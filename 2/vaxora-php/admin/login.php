<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/auth.php';

if (isLoggedIn() && $_SESSION['user']['role'] === 'admin') redirect(SITE_URL . '/admin/index.php');

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $user = loginUser($email, $password, 'admin');
    if ($user) {
        redirect(SITE_URL . '/admin/index.php');
    } else {
        $error = 'Invalid admin credentials.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Admin Login — VAXORA</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/style.css">
</head>
<body style="background:#1C2706">
<div class="auth-page" style="background:transparent">
  <div class="auth-card">
    <div class="auth-logo">VAX<span>ORA</span></div>
    <h2 class="auth-title">Admin Portal</h2>
    <p class="auth-subtitle">Restricted access — authorised personnel only</p>
    <?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>
    <?php echo showFlash(); ?>
    <form method="POST">
      <div class="form-group">
        <label class="form-label">Admin Email</label>
        <input type="email" name="email" class="form-control" value="admin@vaxora.pk" required>
      </div>
      <div class="form-group">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" placeholder="••••••••" required>
      </div>
      <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;border-radius:10px">Sign In as Admin</button>
    </form>
    <div class="divider"></div>
    <p class="text-center" style="font-size:0.82rem;color:#888">
      Demo credentials: <b>admin@vaxora.pk</b> / <b>password</b>
    </p>
    <p class="text-center mt-8"><a href="<?= SITE_URL ?>/" style="color:#5a6a40;font-size:0.85rem">&#8592; Back to website</a></p>
  </div>
</div>
</body>
</html>
