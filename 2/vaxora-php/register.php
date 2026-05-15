<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';

if (isLoggedIn()) redirect(dashboardUrl($_SESSION['user']['role']));

$error = $success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';
    $role     = $_POST['role'] ?? 'parent';
    $phone    = trim($_POST['phone'] ?? '');
    $city     = trim($_POST['city'] ?? '');
    $address  = trim($_POST['address'] ?? '');

    if (!$name || !$email || !$password || !$confirm) {
        $error = 'Please fill in all required fields.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } else {
        $db = getDB();
        $check = $db->prepare('SELECT id FROM users WHERE email = ?');
        $check->execute([$email]);
        if ($check->fetch()) {
            $error = 'An account with this email already exists.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $db->prepare('INSERT INTO users (name,email,password,role,phone,city,address) VALUES (?,?,?,?,?,?,?)');
            $stmt->execute([$name, $email, $hash, $role, $phone, $city, $address]);
            $userId = $db->lastInsertId();

            // If hospital, create hospital record
            if ($role === 'hospital') {
                $hname = trim($_POST['hospital_name'] ?? $name);
                $db->prepare('INSERT INTO hospitals (user_id,name,address,city,phone,email,status) VALUES (?,?,?,?,?,?,"inactive")')
                   ->execute([$userId, $hname, $address, $city, $phone, $email]);
            }

            setFlash('success', 'Registration successful! Please log in.');
            redirect(SITE_URL . '/login.php');
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Register — VAXORA</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/style.css">
</head>
<body>
<div class="auth-page">
  <div class="auth-card" style="max-width:520px">
    <div class="auth-logo">VAX<span>ORA</span></div>
    <h2 class="auth-title">Create Account</h2>
    <p class="auth-subtitle">Join VAXORA — Pakistan's vaccination platform</p>

    <?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>

    <form method="POST" id="regForm">
      <div class="form-group">
        <label class="form-label">Register As *</label>
        <select name="role" class="form-control" id="roleSelect" required>
          <option value="parent">Parent / Guardian</option>
          <option value="hospital">Hospital / Clinic</option>
        </select>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Full Name *</label>
          <input type="text" name="name" class="form-control" value="<?= e($_POST['name'] ?? '') ?>" required>
        </div>
        <div class="form-group">
          <label class="form-label">Phone</label>
          <input type="tel" name="phone" class="form-control" value="<?= e($_POST['phone'] ?? '') ?>" placeholder="0300-0000000">
        </div>
      </div>
      <div class="form-group" id="hospitalNameGroup" style="display:none">
        <label class="form-label">Hospital / Clinic Name *</label>
        <input type="text" name="hospital_name" class="form-control" value="<?= e($_POST['hospital_name'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label class="form-label">Email Address *</label>
        <input type="email" name="email" class="form-control" value="<?= e($_POST['email'] ?? '') ?>" required>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">City</label>
          <input type="text" name="city" class="form-control" value="<?= e($_POST['city'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label class="form-label">Address</label>
          <input type="text" name="address" class="form-control" value="<?= e($_POST['address'] ?? '') ?>">
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Password *</label>
          <input type="password" name="password" class="form-control" required minlength="6">
        </div>
        <div class="form-group">
          <label class="form-label">Confirm Password *</label>
          <input type="password" name="confirm_password" class="form-control" required>
        </div>
      </div>
      <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;border-radius:10px">Create Account</button>
    </form>
    <div class="divider"></div>
    <p class="text-center text-muted" style="font-size:0.9rem">
      Already have an account? <a href="<?= SITE_URL ?>/login.php" style="color:#4a7c00;font-weight:600">Sign in</a>
    </p>
    <p class="text-center mt-8"><a href="<?= SITE_URL ?>/" style="color:#5a6a40;font-size:0.85rem">&#8592; Back to website</a></p>
  </div>
</div>
<script>
document.getElementById('roleSelect').addEventListener('change', function(){
  document.getElementById('hospitalNameGroup').style.display = this.value === 'hospital' ? '' : 'none';
});
</script>
</body>
</html>
