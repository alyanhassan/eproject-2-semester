<?php
require_once '../includes/auth_check.php';
checkRole(['admin']);
require_once '../config/config.php';
require_once '../config/database.php';

$db = (new Database())->getConnection();
$pageTitle = 'Manage Appointments';
$success = ''; $error = '';

// Complete an appointment
if (isset($_GET['complete']) && is_numeric($_GET['complete'])) {
    $aid = (int)$_GET['complete'];
    try {
        $db->beginTransaction();
        $appt = $db->prepare("SELECT * FROM appointments WHERE appointment_id=? AND status!='completed'");
        $appt->execute([$aid]);
        $data = $appt->fetch(PDO::FETCH_ASSOC);
        if ($data) {
            $db->prepare("UPDATE appointments SET status='completed' WHERE appointment_id=?")->execute([$aid]);
            $db->prepare("INSERT INTO vaccination_records (child_id,vaccine_id,hospital_id,administration_date) VALUES (?,?,?,?)")
               ->execute([$data['child_id'],$data['vaccine_id'],$data['hospital_id'],$data['appointment_date']]);
            $db->commit();
            $success = "Appointment marked as completed and vaccination record created.";
        }
    } catch (Exception $e) { $db->rollBack(); $error = "Error completing appointment."; }
}

// Cancel
if (isset($_GET['cancel']) && is_numeric($_GET['cancel'])) {
    $db->prepare("UPDATE appointments SET status='cancelled' WHERE appointment_id=?")->execute([(int)$_GET['cancel']]);
    $success = "Appointment cancelled.";
}

$filter = $_GET['filter'] ?? 'all';
$where = $filter !== 'all' ? "WHERE a.status = '$filter'" : '';

$appointments = $db->query("
    SELECT a.*, c.full_name as child_name, v.vaccine_name, h.hospital_name, u.email as parent_email
    FROM appointments a
    JOIN children c ON a.child_id = c.child_id
    JOIN vaccines v ON a.vaccine_id = v.vaccine_id
    JOIN hospitals h ON a.hospital_id = h.hospital_id
    JOIN parents p ON c.parent_id = p.parent_id
    JOIN users u ON p.user_id = u.user_id
    $where
    ORDER BY a.created_at DESC
")->fetchAll(PDO::FETCH_ASSOC);

include '../includes/admin_header.php';
?>
<?php if ($success): ?><div class="alert alert-success"><i class="fas fa-check-circle me-1"></i><?= htmlspecialchars($success) ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div class="d-flex gap-2 flex-wrap">
        <?php foreach (['all','pending','approved','completed','cancelled'] as $f): ?>
            <a href="?filter=<?= $f ?>" class="btn btn-sm <?= $filter===$f ? 'btn-primary' : 'btn-outline-secondary' ?>"><?= ucfirst($f) ?></a>
        <?php endforeach; ?>
    </div>
    <input type="text" id="adminSearch" class="form-control form-control-sm" placeholder="Search..." style="width:200px;">
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr><th>Child</th><th>Parent</th><th>Vaccine</th><th>Hospital</th><th>Date</th><th>Status</th><th class="text-center">Actions</th></tr>
                </thead>
                <tbody>
                <?php foreach ($appointments as $a): ?>
                <tr>
                    <td class="fw-semibold"><?= htmlspecialchars($a['child_name']) ?></td>
                    <td class="text-muted small"><?= htmlspecialchars($a['parent_email']) ?></td>
                    <td><span class="vax-pill-badge vax-pill-appts"><?= htmlspecialchars($a['vaccine_name']) ?></span></td>
                    <td class="small"><?= htmlspecialchars($a['hospital_name']) ?></td>
                    <td><?= date('d M Y', strtotime($a['appointment_date'])) ?></td>
                    <td>
                        <?php $cls=['pending'=>'bg-warning text-dark','approved'=>'bg-info','completed'=>'bg-success','cancelled'=>'bg-danger']; ?>
                        <span class="badge <?= $cls[$a['status']] ?? 'bg-secondary' ?>"><?= ucfirst($a['status']) ?></span>
                    </td>
                    <td class="text-center">
                        <?php if ($a['status'] === 'pending' || $a['status'] === 'approved'): ?>
                            <a href="?complete=<?= $a['appointment_id'] ?>&filter=<?= $filter ?>" onclick="return confirm('Mark this vaccination as completed?')" class="btn btn-sm btn-success me-1" title="Complete"><i class="fas fa-check"></i></a>
                            <a href="?cancel=<?= $a['appointment_id'] ?>&filter=<?= $filter ?>" onclick="return confirm('Cancel this appointment?')" class="btn btn-sm btn-outline-danger" title="Cancel"><i class="fas fa-times"></i></a>
                        <?php else: ?>
                            <span class="text-muted small">—</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($appointments)): ?><tr><td colspan="7" class="text-center py-5 text-muted">No appointments found for this filter.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include '../includes/admin_footer.php'; ?>
