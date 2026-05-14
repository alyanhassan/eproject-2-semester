<?php
require_once '../includes/auth_check.php';
checkRole(['admin']);
require_once '../config/config.php';
require_once '../config/database.php';

$db = (new Database())->getConnection();
$pageTitle = 'Edit Hospital';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) redirect('/admin/manage_hospitals.php');

$stmt = $db->prepare("SELECT * FROM hospitals WHERE hospital_id=?");
$stmt->execute([$id]);
$h = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$h) redirect('/admin/manage_hospitals.php');

$error = ''; $success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = sanitizeInput($_POST['hospital_name'] ?? '');
    $address = sanitizeInput($_POST['address'] ?? '');
    $city    = sanitizeInput($_POST['city'] ?? '');
    $phone   = sanitizeInput($_POST['phone'] ?? '');
    $email   = sanitizeInput($_POST['email'] ?? '');

    if (empty($name)) { $error = 'Hospital name is required.'; }
    else {
        $upd = $db->prepare("UPDATE hospitals SET hospital_name=?,address=?,city=?,phone=?,email=? WHERE hospital_id=?");
        $upd->execute([$name,$address,$city,$phone,$email,$id]);
        if ($email && $h['user_id']) {
            $db->prepare("UPDATE users SET email=? WHERE user_id=?")->execute([$email,$h['user_id']]);
        }
        $success = 'Hospital updated successfully!';
        $h = array_merge($h, ['hospital_name'=>$name,'address'=>$address,'city'=>$city,'phone'=>$phone,'email'=>$email]);
    }
}
include '../includes/admin_header.php';
?>
<div style="max-width:640px;">
    <a href="/admin/manage_hospitals.php" class="text-muted text-decoration-none small"><i class="fas fa-arrow-left me-1"></i>Back to Hospitals</a>
    <h4 class="fw-bold mt-2 mb-4">Edit Hospital Profile</h4>

    <?php if ($success): ?><div class="alert alert-success"><i class="fas fa-check-circle me-1"></i><?= htmlspecialchars($success) ?></div><?php endif; ?>
    <?php if ($error):   ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>

    <div class="card">
        <div class="card-body p-4">
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Hospital Name *</label>
                    <input type="text" name="hospital_name" class="form-control" value="<?= htmlspecialchars($h['hospital_name']) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Address</label>
                    <input type="text" name="address" class="form-control" value="<?= htmlspecialchars($h['address'] ?? '') ?>">
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label">City</label>
                        <input type="text" name="city" class="form-control" value="<?= htmlspecialchars($h['city'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($h['phone'] ?? '') ?>">
                    </div>
                </div>
                <div class="mb-4">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($h['email'] ?? '') ?>">
                </div>
                <div class="d-flex gap-3">
                    <button type="submit" class="btn btn-primary px-4"><i class="fas fa-save me-2"></i>Save Changes</button>
                    <a href="/admin/manage_hospitals.php" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
<?php include '../includes/admin_footer.php'; ?>
