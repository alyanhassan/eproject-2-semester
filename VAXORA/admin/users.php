<?php
require_once '../includes/auth_check.php';
checkRole(['admin']);
require_once '../config/config.php';
require_once '../config/database.php';

$db = (new Database())->getConnection();
$pageTitle = 'Parent Accounts';
$success = '';

// Toggle status
if (isset($_GET['toggle']) && is_numeric($_GET['toggle'])) {
    $uid = (int)$_GET['toggle'];
    $db->query("UPDATE users SET status = CASE WHEN status='active' THEN 'inactive' ELSE 'active' END WHERE user_id=$uid AND role='parent'");
    $success = "User status updated.";
}

$users = $db->query("
    SELECT u.user_id, u.email, u.status, u.created_at,
           p.full_name, p.phone,
           (SELECT COUNT(*) FROM children c WHERE c.parent_id = p.parent_id) as children_count,
           (SELECT COUNT(*) FROM appointments a JOIN children c2 ON a.child_id=c2.child_id WHERE c2.parent_id=p.parent_id) as total_appts
    FROM users u
    LEFT JOIN parents p ON u.user_id = p.user_id
    WHERE u.role = 'parent'
    ORDER BY u.created_at DESC
")->fetchAll(PDO::FETCH_ASSOC);

include '../includes/admin_header.php';
?>
<?php if ($success): ?><div class="alert alert-success"><i class="fas fa-check-circle me-1"></i><?= htmlspecialchars($success) ?></div><?php endif; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <span class="text-muted small"><?= count($users) ?> parent account(s)</span>
    <input type="text" id="adminSearch" class="form-control form-control-sm" placeholder="Search parents..." style="width:220px;">
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Children</th><th>Appointments</th><th>Joined</th><th>Status</th><th class="text-center">Action</th></tr></thead>
                <tbody>
                <?php foreach ($users as $u): ?>
                <tr>
                    <td class="fw-semibold"><?= htmlspecialchars($u['full_name'] ?? 'N/A') ?></td>
                    <td class="small"><?= htmlspecialchars($u['email']) ?></td>
                    <td class="small text-muted"><?= htmlspecialchars($u['phone'] ?? '—') ?></td>
                    <td class="text-center"><span class="vax-pill-badge vax-pill-children"><?= $u['children_count'] ?> <?= $u['children_count'] == 1 ? 'child' : 'children' ?></span></td>
                    <td class="text-center"><span class="vax-pill-badge vax-pill-appts"><?= $u['total_appts'] ?> <?= $u['total_appts'] == 1 ? 'appt' : 'appts' ?></span></td>
                    <td class="small text-muted"><?= date('d M Y', strtotime($u['created_at'])) ?></td>
                    <td><span class="badge <?= $u['status']==='active' ? 'bg-success' : 'bg-secondary' ?>"><?= ucfirst($u['status']) ?></span></td>
                    <td class="text-center">
                        <a href="?toggle=<?= $u['user_id'] ?>" class="btn btn-sm <?= $u['status']==='active' ? 'btn-outline-warning' : 'btn-outline-success' ?>">
                            <?= $u['status']==='active' ? 'Disable' : 'Enable' ?>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($users)): ?><tr><td colspan="8" class="text-center py-4 text-muted">No parent accounts yet.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include '../includes/admin_footer.php'; ?>
