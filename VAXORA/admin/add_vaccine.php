<?php
require_once '../includes/auth_check.php';
checkRole(['admin']);
require_once '../config/config.php';
require_once '../config/database.php';

$db = (new Database())->getConnection();
$pageTitle = 'Add Vaccine';
$error = ''; $success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name   = sanitizeInput($_POST['vaccine_name'] ?? '');
    $desc   = sanitizeInput($_POST['description'] ?? '');
    $doses  = (int)($_POST['doses_required'] ?? 1);
    $age    = sanitizeInput($_POST['age_group'] ?? '');

    if (empty($name)) { $error = 'Vaccine name is required.'; }
    else {
        $stmt = $db->prepare("INSERT INTO vaccines (vaccine_name,description,doses_required,age_group,status) VALUES (?,?,?,?,'active')");
        $stmt->execute([$name,$desc,$doses,$age]);
        $success = "Vaccine <strong>$name</strong> added successfully!";
    }
}
include '../includes/admin_header.php';
?>
<div style="max-width:600px;">
    <a href="/admin/manage_vaccines.php" class="text-muted text-decoration-none small"><i class="fas fa-arrow-left me-1"></i>Back to Vaccines</a>
    <h4 class="fw-bold mt-2 mb-4">Add New Vaccine</h4>

    <?php if ($success): ?><div class="alert alert-success"><?= $success ?><div class="mt-2"><a href="/admin/manage_vaccines.php" class="btn btn-success btn-sm">View All Vaccines</a></div></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>

    <div class="card">
        <div class="card-body p-4">
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Vaccine Name *</label>
                    <input type="text" name="vaccine_name" class="form-control" placeholder="e.g. BCG, Polio, MMR" value="<?= htmlspecialchars($_POST['vaccine_name'] ?? '') ?>" required>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Doses Required</label>
                        <input type="number" name="doses_required" class="form-control" min="1" max="10" value="<?= (int)($_POST['doses_required'] ?? 1) ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Target Age Group</label>
                        <input type="text" name="age_group" class="form-control" placeholder="e.g. 0-12 months" value="<?= htmlspecialchars($_POST['age_group'] ?? '') ?>">
                    </div>
                </div>
                <div class="mb-4">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="3" placeholder="Brief description of what this vaccine prevents..."><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                </div>
                <div class="d-flex gap-3">
                    <button type="submit" class="btn btn-primary px-4"><i class="fas fa-vial me-2"></i>Add Vaccine</button>
                    <a href="/admin/manage_vaccines.php" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
<?php include '../includes/admin_footer.php'; ?>
