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

$error = ''; $success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name    = sanitizeInput($_POST['full_name'] ?? '');
    $dob          = sanitizeInput($_POST['date_of_birth'] ?? '');
    $gender       = sanitizeInput($_POST['gender'] ?? '');
    $birth_weight = sanitizeInput($_POST['birth_weight'] ?? '');
    $blood_group  = sanitizeInput($_POST['blood_group'] ?? '');
    $allergies    = sanitizeInput($_POST['allergies'] ?? '');
    $medical      = sanitizeInput($_POST['medical_conditions'] ?? '');

    if (empty($full_name) || empty($dob) || empty($gender)) {
        $error = 'Name, date of birth, and gender are required.';
    } else {
        $age = (new DateTime())->diff(new DateTime($dob))->y;
        if ($age > 18) { $error = 'Child must be under 18 years of age.'; }
        else {
            try {
                $uid = 'CHD-' . strtoupper(substr(uniqid(), -6));
                $stmt = $db->prepare("INSERT INTO children (parent_id,unique_child_id,full_name,date_of_birth,gender,birth_weight,blood_group,allergies,medical_conditions) VALUES (?,?,?,?,?,?,?,?,?)");
                $stmt->execute([$parent_id,$uid,$full_name,$dob,$gender,$birth_weight,$blood_group,$allergies,$medical]);
                $success = "Child <strong>$full_name</strong> added! ID: $uid";
            } catch (PDOException $e) {
                $error = 'Failed to add child. Please try again.';
                error_log($e->getMessage());
            }
        }
    }
}
include 'includes/header.php';
?>
<div class="container py-4" style="max-width:680px;">
    <div class="mb-4">
        <a href="/children.php" class="text-muted text-decoration-none small"><i class="fas fa-arrow-left me-1"></i>Back to Children</a>
        <h3 class="fw-bold mt-2 mb-0">Add New Child</h3>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success"><i class="fas fa-check-circle me-1"></i><?= $success ?>
            <div class="mt-2 d-flex gap-2">
                <a href="/children.php" class="btn btn-success btn-sm">View Children</a>
                <a href="/add_child.php" class="btn btn-outline-success btn-sm">Add Another</a>
            </div>
        </div>
    <?php endif; ?>
    <?php if ($error): ?><div class="alert alert-danger"><i class="fas fa-exclamation-circle me-1"></i><?= htmlspecialchars($error) ?></div><?php endif; ?>

    <div class="card">
        <div class="card-body p-4">
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Full Name *</label>
                    <input type="text" name="full_name" class="form-control" placeholder="Child's full name" value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>" required>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Date of Birth *</label>
                        <input type="date" name="date_of_birth" class="form-control" max="<?= date('Y-m-d') ?>" value="<?= htmlspecialchars($_POST['date_of_birth'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Gender *</label>
                        <select name="gender" class="form-select" required>
                            <option value="">Select gender</option>
                            <option value="male" <?= ($_POST['gender'] ?? '') === 'male' ? 'selected' : '' ?>>Male</option>
                            <option value="female" <?= ($_POST['gender'] ?? '') === 'female' ? 'selected' : '' ?>>Female</option>
                            <option value="other" <?= ($_POST['gender'] ?? '') === 'other' ? 'selected' : '' ?>>Other</option>
                        </select>
                    </div>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Birth Weight (kg)</label>
                        <input type="text" name="birth_weight" class="form-control" placeholder="e.g. 3.2" value="<?= htmlspecialchars($_POST['birth_weight'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Blood Group</label>
                        <select name="blood_group" class="form-select">
                            <option value="">Unknown</option>
                            <?php foreach (['A+','A-','B+','B-','O+','O-','AB+','AB-'] as $bg): ?>
                                <option value="<?= $bg ?>" <?= ($_POST['blood_group'] ?? '') === $bg ? 'selected' : '' ?>><?= $bg ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Known Allergies</label>
                    <input type="text" name="allergies" class="form-control" placeholder="e.g. Penicillin, Latex (leave blank if none)" value="<?= htmlspecialchars($_POST['allergies'] ?? '') ?>">
                </div>
                <div class="mb-4">
                    <label class="form-label">Medical Conditions</label>
                    <textarea name="medical_conditions" class="form-control" rows="2" placeholder="Any ongoing conditions or notes..."><?= htmlspecialchars($_POST['medical_conditions'] ?? '') ?></textarea>
                </div>
                <div class="d-flex gap-3">
                    <button type="submit" class="btn btn-primary px-4"><i class="fas fa-save me-2"></i>Save Child</button>
                    <a href="/children.php" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>
