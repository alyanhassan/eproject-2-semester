<?php
require_once 'includes/auth_check.php';
checkRole(['parent']);
require_once 'config/config.php';
require_once 'config/database.php';

$db = (new Database())->getConnection();
$pStmt = $db->prepare("SELECT parent_id FROM parents WHERE user_id = ? LIMIT 1");
$pStmt->execute([$_SESSION['user_id']]);
$parent = $pStmt->fetch(PDO::FETCH_ASSOC);
if (!$parent) redirect('/auth/logout.php');
$parent_id = (int)$parent['parent_id'];

$child_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$child_id) redirect('/children.php');

$cStmt = $db->prepare("SELECT * FROM children WHERE child_id = ? AND parent_id = ? LIMIT 1");
$cStmt->execute([$child_id, $parent_id]);
$child = $cStmt->fetch(PDO::FETCH_ASSOC);
if (!$child) redirect('/children.php');

$error = ''; $success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name   = sanitizeInput($_POST['full_name'] ?? '');
    $dob         = sanitizeInput($_POST['date_of_birth'] ?? '');
    $gender      = sanitizeInput($_POST['gender'] ?? '');
    $blood_group = sanitizeInput($_POST['blood_group'] ?? '');
    $allergies   = sanitizeInput($_POST['allergies'] ?? '');
    $medical     = sanitizeInput($_POST['medical_conditions'] ?? '');

    if (empty($full_name) || empty($dob) || empty($gender)) {
        $error = 'Name, date of birth and gender are required.';
    } else {
        $upd = $db->prepare("UPDATE children SET full_name=?,date_of_birth=?,gender=?,blood_group=?,allergies=?,medical_conditions=? WHERE child_id=? AND parent_id=?");
        $upd->execute([$full_name,$dob,$gender,$blood_group,$allergies,$medical,$child_id,$parent_id]);
        $success = 'Child details updated successfully!';
        $child = array_merge($child, compact('full_name','dob','gender','blood_group','allergies','medical'));
        $child['date_of_birth'] = $dob;
    }
}
include 'includes/header.php';
?>
<div class="container py-4" style="max-width:640px;">
    <div class="mb-4">
        <a href="/children.php" class="text-muted text-decoration-none small"><i class="fas fa-arrow-left me-1"></i>Back to Children</a>
        <h3 class="fw-bold mt-2 mb-0">Edit Child Profile</h3>
    </div>

    <?php if ($success): ?><div class="alert alert-success"><i class="fas fa-check-circle me-1"></i><?= htmlspecialchars($success) ?></div><?php endif; ?>
    <?php if ($error):   ?><div class="alert alert-danger"><i class="fas fa-exclamation-circle me-1"></i><?= htmlspecialchars($error) ?></div><?php endif; ?>

    <div class="card">
        <div class="card-body p-4">
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Full Name *</label>
                    <input type="text" name="full_name" class="form-control" value="<?= htmlspecialchars($child['full_name']) ?>" required>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Date of Birth *</label>
                        <input type="date" name="date_of_birth" class="form-control" value="<?= htmlspecialchars($child['date_of_birth']) ?>" max="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Gender *</label>
                        <select name="gender" class="form-select" required>
                            <?php foreach (['male','female','other'] as $g): ?>
                                <option value="<?= $g ?>" <?= $child['gender'] === $g ? 'selected' : '' ?>><?= ucfirst($g) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Blood Group</label>
                    <select name="blood_group" class="form-select">
                        <option value="">Unknown</option>
                        <?php foreach (['A+','A-','B+','B-','O+','O-','AB+','AB-'] as $bg): ?>
                            <option value="<?= $bg ?>" <?= ($child['blood_group'] ?? '') === $bg ? 'selected' : '' ?>><?= $bg ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Allergies</label>
                    <input type="text" name="allergies" class="form-control" value="<?= htmlspecialchars($child['allergies'] ?? '') ?>">
                </div>
                <div class="mb-4">
                    <label class="form-label">Medical Conditions</label>
                    <textarea name="medical_conditions" class="form-control" rows="2"><?= htmlspecialchars($child['medical_conditions'] ?? '') ?></textarea>
                </div>
                <div class="d-flex gap-3">
                    <button type="submit" class="btn btn-primary px-4"><i class="fas fa-save me-2"></i>Save Changes</button>
                    <a href="/children.php" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>
