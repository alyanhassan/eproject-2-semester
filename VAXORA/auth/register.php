<?php
require_once '../config/config.php';
require_once '../config/database.php';

if (isset($_SESSION['user_id'])) redirect('/dashboard.php');

$database = new Database();
$db = $database->getConnection();

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = sanitizeInput($_POST['full_name'] ?? '');
    $email     = sanitizeInput($_POST['email'] ?? '');
    $phone     = sanitizeInput($_POST['phone'] ?? '');
    $address   = sanitizeInput($_POST['address'] ?? '');
    $password  = $_POST['password'] ?? '';
    $confirm   = $_POST['confirm_password'] ?? '';

    if (empty($full_name) || empty($email) || empty($password)) {
        $error = 'Full name, email, and password are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } else {
        try {
            $db->beginTransaction();
            $hashed = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $db->prepare("INSERT INTO users (email, password, role, status) VALUES (?, ?, 'parent', 'active')");
            $stmt->execute([$email, $hashed]);
            $user_id = $db->lastInsertId();

            $stmt2 = $db->prepare("INSERT INTO parents (user_id, full_name, phone, address) VALUES (?, ?, ?, ?)");
            $stmt2->execute([$user_id, $full_name, $phone, $address]);

            $db->commit();
            $success = 'Account created successfully! You can now log in.';
        } catch (Exception $e) {
            $db->rollBack();
            if (strpos($e->getMessage(), 'UNIQUE') !== false) {
                $error = 'This email address is already registered.';
            } else {
                $error = 'Registration failed. Please try again.';
                error_log($e->getMessage());
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register — Vaxora</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<div class="auth-wrapper">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">

                <div class="text-center mb-4">
                    <a href="/index.php" class="text-decoration-none">
                        <div class="auth-logo">
                            <span class="auth-logo-icon"><i class="fas fa-syringe text-white"></i></span>
                            Vaxora<span class="dot">.</span>
                        </div>
                    </a>
                    <p class="text-white-50 mt-1" style="font-size:0.9rem;">Create your free parent account</p>
                </div>

                <div class="auth-card card p-4">
                    <h4 class="fw-bold mb-1">Create Account</h4>
                    <p class="text-muted small mb-4">Join thousands of parents tracking their children's health</p>

                    <?php if ($success): ?>
                        <div class="alert alert-success border-0">
                            <i class="fas fa-check-circle me-1"></i><?= htmlspecialchars($success) ?>
                            <div class="mt-2"><a href="/auth/login.php" class="btn btn-success btn-sm">Login Now</a></div>
                        </div>
                    <?php endif; ?>

                    <?php if ($error): ?>
                        <div class="alert alert-danger border-0 py-2 small"><i class="fas fa-exclamation-circle me-1"></i><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>

                    <?php if (!$success): ?>
                    <form method="POST" novalidate>
                        <div class="mb-3">
                            <label class="form-label">Full Name *</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-user text-muted"></i></span>
                                <input type="text" name="full_name" class="form-control" placeholder="Ahmed Ali"
                                       value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email Address *</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-envelope text-muted"></i></span>
                                <input type="email" name="email" class="form-control" placeholder="you@example.com"
                                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <label class="form-label">Phone</label>
                                <input type="text" name="phone" class="form-control" placeholder="0300-1234567"
                                       value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
                            </div>
                            <div class="col-6">
                                <label class="form-label">City</label>
                                <input type="text" name="address" class="form-control" placeholder="Karachi"
                                       value="<?= htmlspecialchars($_POST['address'] ?? '') ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password *</label>
                            <input type="password" name="password" class="form-control" placeholder="Min. 6 characters" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Confirm Password *</label>
                            <input type="password" name="confirm_password" class="form-control" placeholder="Repeat password" required>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">
                            <i class="fas fa-user-plus me-2"></i>Create Account
                        </button>
                    </form>
                    <?php endif; ?>

                    <hr class="my-3">
                    <p class="text-center small mb-0">
                        Already have an account? <a href="/auth/login.php" class="text-primary fw-semibold text-decoration-none">Sign in</a>
                    </p>
                </div>

                <p class="text-center text-white-50 small mt-4">
                    <a href="/index.php" class="text-white-50 text-decoration-none"><i class="fas fa-arrow-left me-1"></i>Back to Home</a>
                </p>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
