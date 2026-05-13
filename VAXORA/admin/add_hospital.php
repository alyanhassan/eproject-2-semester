<?php
require_once '../includes/auth_check.php';
checkRole(['admin']);
require_once '../config/config.php';
require_once '../config/database.php';

$db = (new Database())->getConnection();
$pageTitle = 'Add Hospital';
$error = ''; $success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = sanitizeInput($_POST['hospital_name'] ?? '');
    $address = sanitizeInput($_POST['address'] ?? '');
    $city    = sanitizeInput($_POST['city'] ?? '');
    $phone   = sanitizeInput($_POST['phone'] ?? '');
    $email   = sanitizeInput($_POST['email'] ?? '');

    if (empty($name)) { $error = 'Hospital name is required.'; }
    else {
        try {
            $db->beginTransaction();
            // Create login for hospital
            $pw = password_hash('hospital123', PASSWORD_DEFAULT);
            $loginEmail = $email ?: strtolower(str_replace(' ', '', $name)) . '@hospital.com';
            $userStmt = $db->prepare("INSERT INTO users (email,password,role,status) VALUES (?,?,'hospital','active')");
            $userStmt->execute([$loginEmail, $pw]);
            $uid = $db->lastInsertId();

            $hStmt = $db->prepare("INSERT INTO hospitals (user_id,hospital_name,address,city,phone,email,status) VALUES (?,?,?,?,?,?,'active')");
            $hStmt->execute([$uid, $name, $address, $city, $phone, $email]);
            $db->commit();
            $success = "Hospital <strong>$name</strong> added! Login: <strong>$loginEmail</strong> / hospital123";
        } catch (Exception $e) {
            $db->rollBack();
            $error = strpos($e->getMessage(), 'UNIQUE') !== false ? 'This email is already in use.' : 'Failed to add hospital.';
            error_log($e->getMessage());
        }
    }
}
include '../includes/admin_header.php';
?>
<div style="max-width:640px;">
    <a href="/admin/manage_hospitals.php" class="text-muted text-decoration-none small"><i class="fas fa-arrow-left me-1"></i>Back to Hospitals</a>
    <h4 class="fw-bold mt-2 mb-4">Register New Hospital</h4>

    <?php if ($success): ?><div class="alert alert-success"><i class="fas fa-check-circle me-1"></i><?= $success ?><div class="mt-2"><a href="/admin/manage_hospitals.php" class="btn btn-success btn-sm">View All Hospitals</a></div></div><?php endif; ?>
    <?php if ($error):   ?><div class="alert alert-danger"><i class="fas fa-exclamation-circle me-1"></i><?= htmlspecialchars($error) ?></div><?php endif; ?>

    <div class="card">
        <div class="card-body p-4">
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Hospital Name *</label>
                    <input type="text" name="hospital_name" class="form-control" placeholder="e.g. Civil Hospital Karachi" value="<?= htmlspecialchars($_POST['hospital_name'] ?? '') ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Street Address</label>
                    <input type="text" name="address" class="form-control" placeholder="123 Medical Way" value="<?= htmlspecialchars($_POST['address'] ?? '') ?>">
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label">City</label>
                        <input type="text" name="city" class="form-control" placeholder="Karachi" value="<?= htmlspecialchars($_POST['city'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control" placeholder="021-XXXXXXX" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
                    </div>
                </div>
                <div class="mb-4">
                    <label class="form-label">Email <span class="text-muted small">(used as login)</span></label>
                    <input type="email" name="email" class="form-control" placeholder="info@hospital.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                    <div class="form-text">Default login password will be: <strong>hospital123</strong></div>
                </div>
                <div class="d-flex gap-3">
                    <button type="submit" class="btn btn-primary px-4"><i class="fas fa-hospital me-2"></i>Register Hospital</button>
                    <a href="/admin/manage_hospitals.php" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
<?php include '../includes/admin_footer.php'; ?>
