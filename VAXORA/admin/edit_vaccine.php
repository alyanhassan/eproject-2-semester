<?php
require_once '../includes/auth_check.php';
checkRole(['admin']);
require_once '../config/config.php';
require_once '../config/database.php';

$db = (new Database())->getConnection();
$pageTitle = 'Edit Vaccine';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) redirect('/admin/manage_vaccines.php');

$stmt = $db->prepare("SELECT * FROM vaccines WHERE vaccine_id=?");
$stmt->execute([$id]);
$v = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$v) redirect('/admin/manage_vaccines.php');

$error = ''; $success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name   = sanitizeInput($_POST['vaccine_name'] ?? '');
    $desc   = sanitizeInput($_POST['description'] ?? '');
    $doses  = (int)($_POST['doses_required'] ?? 1);
    $age    = sanitizeInput($_POST['age_group'] ?? '');
    $status = sanitizeInput($_POST['status'] ?? 'active');
    if (empty($name)) { $error = 'Vaccine name required.'; }
    else {
        $db->prepare("UPDATE vaccines SET vaccine_name=?,description=?,doses_required=?,age_group=?,status=? WHERE vaccine_id=?")->execute([$name,$desc,$doses,$age,$status,$id]);
        $success = 'Vaccine updated.';
        $v = array_merge($v, ['vaccine_name'=>$name,'description'=>$desc,'doses_required'=>$doses,'age_group'=>$age,'status'=>$status]);
    }
}
include '../includes/admin_header.php';
?>
<div style="max-width:600px;">
    <a href="/admin/manage_vaccines.php" class="text-muted text-decoration-none small"><i class="fas fa-arrow-left me-1"></i>Back</a>
    <h4 class="fw-bold mt-2 mb-4">Edit Vaccine</h4>
    <?php if ($success): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <div class="card">
        <div class="card-body p-4">
            <form method="POST">
                <div class="mb-3"><label class="form-label">Vaccine Name *</label><input type="text" name="vaccine_name" class="form-control" value="<?= htmlspecialchars($v['vaccine_name']) ?>" required></div>
                <div class="row g-3 mb-3">
                    <div class="col-md-6"><label class="form-label">Doses Required</label><input type="number" name="doses_required" class="form-control" min="1" value="<?= $v['doses_required'] ?>"></div>
                    <div class="col-md-6"><label class="form-label">Age Group</label><input type="text" name="age_group" class="form-control" value="<?= htmlspecialchars($v['age_group'] ?? '') ?>"></div>
                </div>
                <div class="mb-3"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($v['description'] ?? '') ?></textarea></div>
                <div class="mb-4"><label class="form-label">Status</label><select name="status" class="form-select"><option value="active" <?= $v['status']==='active'?'selected':'' ?>>Active</option><option value="inactive" <?= $v['status']==='inactive'?'selected':'' ?>>Inactive</option></select></div>
                <div class="d-flex gap-3"><button type="submit" class="btn btn-primary px-4"><i class="fas fa-save me-2"></i>Save</button><a href="/admin/manage_vaccines.php" class="btn btn-outline-secondary">Cancel</a></div>
            </form>
        </div>
    </div>
</div>
<?php include '../includes/admin_footer.php'; ?>
