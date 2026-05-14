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

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <span class="text-muted small"><?= count($vaccines) ?> vaccine(s) in system</span>
    <div class="d-flex gap-2">
        <input type="text" id="adminSearch" class="form-control form-control-sm" placeholder="Search vaccines..." style="width:200px;">
        <a href="/admin/add_vaccine.php" class="btn btn-primary btn-sm"><i class="fas fa-plus me-1"></i>Add Vaccine</a>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead>
                    <tr>
                        <th style="min-width:180px;">Vaccine Name</th>
                        <th style="min-width:110px;">Age Group</th>
                        <th style="min-width:90px;">Doses</th>
                        <th style="min-width:90px;">Status</th>
                        <th class="text-center" style="min-width:100px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($vaccines as $v): ?>
                <tr>
                    <td>
                        <div class="fw-semibold" style="font-size:0.92rem;"><?= htmlspecialchars($v['vaccine_name']) ?></div>
                        <?php if (!empty($v['description'])): ?>
                        <div class="text-muted" style="font-size:0.75rem;line-height:1.3;max-width:280px;">
                            <?= htmlspecialchars(mb_substr($v['description'], 0, 60)) ?><?= mb_strlen($v['description']) > 60 ? '…' : '' ?>
                        </div>
                        <?php endif; ?>
                    </td>
                    <td style="font-size:0.83rem;"><?= htmlspecialchars($v['age_group'] ?? '—') ?></td>
                    <td>
                        <span style="display:inline-block;background:#F4F0E6;color:#4A6120;border:1.5px solid rgba(74,97,32,0.35);border-radius:20px;padding:2px 12px;font-size:0.78rem;font-weight:600;white-space:nowrap;">
                            <?= (int)$v['doses_required'] ?> dose<?= $v['doses_required'] > 1 ? 's' : '' ?>
                        </span>
                    </td>
                    <td>
                        <a href="?toggle=<?= $v['vaccine_id'] ?>" class="badge text-decoration-none"
                           style="background:<?= $v['status']==='active' ? '#4A7C59' : '#6c757d' ?>;color:#fff;padding:4px 10px;border-radius:20px;font-size:0.75rem;">
                            <?= $v['status']==='active' ? '✓ Active' : '✗ Inactive' ?>
                        </a>
                    </td>
                    <td class="text-center">
                        <a href="/admin/edit_vaccine.php?id=<?= $v['vaccine_id'] ?>" class="btn btn-sm btn-outline-primary me-1" title="Edit"><i class="fas fa-edit"></i></a>
                        <a href="?delete=<?= $v['vaccine_id'] ?>" onclick="return confirmDelete('<?= addslashes($v['vaccine_name']) ?>')" class="btn btn-sm btn-outline-danger" title="Delete"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($vaccines)): ?>
                <tr><td colspan="5" class="text-center py-4 text-muted">No vaccines added yet.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include '../includes/admin_footer.php'; ?>
