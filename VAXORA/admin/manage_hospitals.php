<?php
require_once '../includes/auth_check.php';
checkRole(['admin']);
require_once '../config/config.php';
require_once '../config/database.php';

$db = (new Database())->getConnection();
$pageTitle = 'Manage Hospitals';
$error = ''; $success = '';

// Toggle status
if (isset($_POST['toggle_status'])) {
    $hid    = (int)$_POST['hospital_id'];
    $nstatus = $_POST['new_status'];
    if (in_array($nstatus, ['active','inactive'])) {
        $db->prepare("UPDATE hospitals SET status=? WHERE hospital_id=?")->execute([$nstatus,$hid]);
        $uid_row = $db->prepare("SELECT user_id FROM hospitals WHERE hospital_id=?");
        $uid_row->execute([$hid]);
        $uid_row = $uid_row->fetchColumn();
        if ($uid_row) $db->prepare("UPDATE users SET status=? WHERE user_id=?")->execute([$nstatus,$uid_row]);
        $success = "Hospital status updated.";
    }
}

// Delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $hid = (int)$_GET['delete'];
    $chk = $db->prepare("SELECT COUNT(*) FROM appointments WHERE hospital_id=?");
    $chk->execute([$hid]);
    if ($chk->fetchColumn() > 0) {
        $db->prepare("UPDATE hospitals SET status='inactive' WHERE hospital_id=?")->execute([$hid]);
        $success = "Hospital has appointments — marked as inactive instead of deleted.";
    } else {
        $uid_row = $db->prepare("SELECT user_id FROM hospitals WHERE hospital_id=?");
        $uid_row->execute([$hid]);
        $uid = $uid_row->fetchColumn();
        $db->prepare("DELETE FROM hospitals WHERE hospital_id=?")->execute([$hid]);
        if ($uid) $db->prepare("DELETE FROM users WHERE user_id=?")->execute([$uid]);
        $success = "Hospital deleted successfully.";
    }
}

$hospitals = $db->query("SELECT h.*, u.status as user_status FROM hospitals h LEFT JOIN users u ON h.user_id=u.user_id ORDER BY h.created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
include '../includes/admin_header.php';
?>
<?php if ($success): ?><div class="alert alert-success"><i class="fas fa-check-circle me-1"></i><?= htmlspecialchars($success) ?></div><?php endif; ?>
<?php if ($error):   ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <span class="text-muted small"><?= count($hospitals) ?> hospital(s) registered</span>
    </div>
    <div class="d-flex gap-2">
        <input type="text" id="adminSearch" class="form-control form-control-sm" placeholder="Search hospitals..." style="width:220px;">
        <a href="/admin/add_hospital.php" class="btn btn-primary btn-sm"><i class="fas fa-plus me-1"></i>Add Hospital</a>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead><tr><th>#</th><th>Hospital Name</th><th>City</th><th>Phone</th><th>Email</th><th>Status</th><th class="text-center">Actions</th></tr></thead>
                <tbody>
                <?php foreach ($hospitals as $h): ?>
                <tr>
                    <td class="text-muted small"><?= $h['hospital_id'] ?></td>
                    <td><div class="fw-semibold"><?= htmlspecialchars($h['hospital_name']) ?></div><div class="text-muted small"><?= htmlspecialchars($h['address'] ?? '') ?></div></td>
                    <td><?= htmlspecialchars($h['city'] ?? '') ?></td>
                    <td class="small"><?= htmlspecialchars($h['phone'] ?? '') ?></td>
                    <td class="small text-muted"><?= htmlspecialchars($h['email'] ?? '') ?></td>
                    <td>
                        <form method="POST" class="d-inline">
                            <input type="hidden" name="hospital_id" value="<?= $h['hospital_id'] ?>">
                            <input type="hidden" name="new_status" value="<?= ($h['status'] ?? 'active') === 'active' ? 'inactive' : 'active' ?>">
                            <button class="btn btn-sm <?= ($h['status'] ?? 'active') === 'active' ? 'btn-success' : 'btn-secondary' ?>" name="toggle_status">
                                <?= ($h['status'] ?? 'active') === 'active' ? 'Active' : 'Inactive' ?>
                            </button>
                        </form>
                    </td>
                    <td class="text-center">
                        <a href="/admin/edit_hospital.php?id=<?= $h['hospital_id'] ?>" class="btn btn-sm btn-outline-primary me-1"><i class="fas fa-edit"></i></a>
                        <a href="?delete=<?= $h['hospital_id'] ?>" onclick="return confirmDelete('<?= addslashes($h['hospital_name']) ?>')" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($hospitals)): ?><tr><td colspan="7" class="text-center py-4 text-muted">No hospitals registered yet.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include '../includes/admin_footer.php'; ?>
