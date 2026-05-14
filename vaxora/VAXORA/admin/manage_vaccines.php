<?php
require_once '../includes/auth_check.php';
checkRole(['admin']);
require_once '../config/config.php';
require_once '../config/database.php';

$db = (new Database())->getConnection();
$pageTitle = 'Manage Vaccines';
$error = ''; $success = '';

if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $vid = (int)$_GET['delete'];
    $chk = $db->prepare("SELECT COUNT(*) FROM appointments WHERE vaccine_id=?");
    $chk->execute([$vid]);
    if ($chk->fetchColumn() > 0) {
        $db->prepare("UPDATE vaccines SET status='inactive' WHERE vaccine_id=?")->execute([$vid]);
        $success = "Vaccine has records — marked as inactive.";
    } else {
        $db->prepare("DELETE FROM vaccines WHERE vaccine_id=?")->execute([$vid]);
        $success = "Vaccine deleted.";
    }
}

if (isset($_GET['toggle']) && is_numeric($_GET['toggle'])) {
    $vid = (int)$_GET['toggle'];
    $db->query("UPDATE vaccines SET status = CASE WHEN status='active' THEN 'inactive' ELSE 'active' END WHERE vaccine_id=$vid");
    $success = "Vaccine status updated.";
}

$vaccines = $db->query("SELECT * FROM vaccines ORDER BY status DESC, vaccine_name ASC")->fetchAll(PDO::FETCH_ASSOC);
include '../includes/admin_header.php';
?>
<?php if ($success): ?><div class="alert alert-success"><i class="fas fa-check-circle me-1"></i><?= htmlspecialchars($success) ?></div><?php endif; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <span class="text-muted small"><?= count($vaccines) ?> vaccine(s) in system</span>
    <div class="d-flex gap-2">
        <input type="text" id="adminSearch" class="form-control form-control-sm" placeholder="Search vaccines..." style="width:200px;">
        <a href="/admin/add_vaccine.php" class="btn btn-primary btn-sm"><i class="fas fa-plus me-1"></i>Add Vaccine</a>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead><tr><th>#</th><th>Vaccine Name</th><th>Age Group</th><th>Doses</th><th>Description</th><th>Status</th><th class="text-center">Actions</th></tr></thead>
                <tbody>
                <?php foreach ($vaccines as $v): ?>
                <tr>
                    <td class="text-muted small"><?= $v['vaccine_id'] ?></td>
                    <td class="fw-semibold"><?= htmlspecialchars($v['vaccine_name']) ?></td>
                    <td><?= htmlspecialchars($v['age_group'] ?? 'N/A') ?></td>
                    <td><span class="badge bg-primary bg-opacity-15 text-primary border border-primary border-opacity-25"><?= $v['doses_required'] ?> dose(s)</span></td>
                    <td class="text-muted small"><?= htmlspecialchars(substr($v['description'] ?? '', 0, 50)) ?><?= strlen($v['description'] ?? '') > 50 ? '...' : '' ?></td>
                    <td>
                        <a href="?toggle=<?= $v['vaccine_id'] ?>" class="badge <?= $v['status'] === 'active' ? 'bg-success' : 'bg-secondary' ?> text-decoration-none">
                            <?= ucfirst($v['status']) ?>
                        </a>
                    </td>
                    <td class="text-center">
                        <a href="/admin/edit_vaccine.php?id=<?= $v['vaccine_id'] ?>" class="btn btn-sm btn-outline-primary me-1"><i class="fas fa-edit"></i></a>
                        <a href="?delete=<?= $v['vaccine_id'] ?>" onclick="return confirmDelete('<?= addslashes($v['vaccine_name']) ?>')" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($vaccines)): ?><tr><td colspan="7" class="text-center py-4 text-muted">No vaccines added yet.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include '../includes/admin_footer.php'; ?>
