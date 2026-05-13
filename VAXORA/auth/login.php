<?php
require_once '../config/config.php';
require_once '../config/database.php';

if (isset($_SESSION['user_id'])) {
    switch ($_SESSION['role']) {
        case 'admin':    redirect('/admin/dashboard.php');
        case 'hospital': redirect('/hospital/dashboard.php');
        default:         redirect('/dashboard.php');
    }
}

$database = new Database();
$db = $database->getConnection();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = sanitizeInput($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role     = sanitizeInput($_POST['role'] ?? 'parent');

    if (empty($email) || empty($password)) {
        $error = 'Please fill in all fields.';
    } else {
        $stmt = $db->prepare("SELECT * FROM users WHERE email = :email AND status = 'active'");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            if ($user['role'] !== $role) {
                $error = 'Access Denied: Incorrect role selected for this account.';
            } else {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['role']    = $user['role'];
                $_SESSION['email']   = $user['email'];

                try {
                    $log = $db->prepare("INSERT INTO system_logs (user_id, action, ip_address) VALUES (?, 'login', ?)");
                    $log->execute([$user['user_id'], $_SERVER['REMOTE_ADDR'] ?? '']);
                } catch (Exception $e) {}

                switch ($user['role']) {
                    case 'admin':    redirect('/admin/dashboard.php');
                    case 'hospital': redirect('/hospital/dashboard.php');
                    default:         redirect('/dashboard.php');
                }
            }
        } else {
            $error = 'Invalid email or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Vaxora</title>
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
                    <a href="/index.php" class="text-decoration-none">
                        <div class="auth-logo">
                            <span class="auth-logo-icon"><i class="fas fa-syringe text-white"></i></span>
                            Vaxora<span class="dot">.</span>
                        </div>
                    </a>
                    <p class="text-white-50 mt-1" style="font-size:0.9rem;">Pakistan's E-Vaccination Platform</p>
                </div>

                <div class="auth-card card p-4">
                    <h4 class="fw-bold mb-1">Welcome back</h4>
                    <p class="text-muted small mb-4">Sign in to your account</p>

                    <?php if ($error): ?>
                        <div class="alert alert-danger border-0 py-2 small"><i class="fas fa-exclamation-circle me-1"></i><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>

                    <?php if (isset($_GET['error']) && $_GET['error'] === 'access_denied'): ?>
                        <div class="alert alert-warning border-0 py-2 small">You don't have permission to access that page.</div>
                    <?php endif; ?>

                    <form method="POST" novalidate>
                        <div class="mb-3">
                            <label class="form-label">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-envelope text-muted"></i></span>
                                <input type="email" name="email" class="form-control" placeholder="you@example.com"
                                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required autofocus>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-lock text-muted"></i></span>
                                <input type="password" name="password" id="pwdField" class="form-control" placeholder="••••••••" required>
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePwd()"><i class="fas fa-eye" id="eyeIcon"></i></button>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Login As</label>
                            <select name="role" class="form-select">
                                <option value="parent">Parent</option>
                                <option value="hospital">Hospital Staff</option>
                                <option value="admin">Administrator</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">
                            <i class="fas fa-sign-in-alt me-2"></i>Sign In
                        </button>
                    </form>

                    <hr class="my-4">
                    <div class="text-center small">
                        <a href="/auth/forgot_password.php" class="text-muted text-decoration-none">Forgot password?</a>
                        <span class="mx-2 text-muted">·</span>
                        <a href="/auth/register.php" class="text-primary text-decoration-none fw-semibold">Create account</a>
                    </div>

                    <div class="mt-4 p-3 bg-light rounded-3 small text-muted">
                        <strong>Demo Accounts:</strong><br>
                        Admin: admin@vaxora.pk / admin123<br>
                        Parent: parent@demo.com / parent123<br>
                        Hospital: info@civilhospital.pk / hospital123
                    </div>
                </div>

                <p class="text-center text-white-50 small mt-4">
                    <a href="/index.php" class="text-white-50 text-decoration-none"><i class="fas fa-arrow-left me-1"></i>Back to Home</a>
                </p>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function togglePwd() {
    const f = document.getElementById('pwdField');
    const i = document.getElementById('eyeIcon');
    f.type = f.type === 'password' ? 'text' : 'password';
    i.className = f.type === 'password' ? 'fas fa-eye' : 'fas fa-eye-slash';
}
</script>
</body>
</html>
