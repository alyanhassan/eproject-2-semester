<?php
require_once '../includes/auth_check.php';
checkRole(['hospital']);
require_once '../config/config.php';
require_once '../config/database.php';

$db = (new Database())->getConnection();

$hStmt = $db->prepare("SELECT * FROM hospitals WHERE user_id=? LIMIT 1");
$hStmt->execute([$_SESSION['user_id']]);
$hospital = $hStmt->fetch(PDO::FETCH_ASSOC);
if (!$hospital) redirect('/auth/login.php');
$hid = $hospital['hospital_id'];

$success = ''; $error = '';

// Approve
if (isset($_GET['approve']) && is_numeric($_GET['approve'])) {
    $db->prepare("UPDATE appointments SET status='approved' WHERE appointment_id=? AND hospital_id=?")->execute([(int)$_GET['approve'], $hid]);
    $success = "Appointment approved.";
}

// Reject/Cancel
if (isset($_GET['reject']) && is_numeric($_GET['reject'])) {
    $db->prepare("UPDATE appointments SET status='cancelled' WHERE appointment_id=? AND hospital_id=?")->execute([(int)$_GET['reject'], $hid]);
    $success = "Appointment rejected.";
}

// Complete — creates vaccination record
if (isset($_GET['complete']) && is_numeric($_GET['complete'])) {
    $aid = (int)$_GET['complete'];
    try {
        $db->beginTransaction();
        $appt = $db->prepare("SELECT * FROM appointments WHERE appointment_id=? AND hospital_id=? AND status='approved'");
        $appt->execute([$aid, $hid]);
        $data = $appt->fetch(PDO::FETCH_ASSOC);
        if ($data) {
            $db->prepare("UPDATE appointments SET status='completed' WHERE appointment_id=?")->execute([$aid]);
            $db->prepare("INSERT INTO vaccination_records (child_id,vaccine_id,hospital_id,administration_date) VALUES (?,?,?,?)")
               ->execute([$data['child_id'],$data['vaccine_id'],$hid,$data['appointment_date']]);
            $db->commit();
            $success = "Vaccination completed and record saved!";
        } else { $db->rollBack(); $error = "Appointment not found or not approved."; }
    } catch (Exception $e) { $db->rollBack(); $error = "Error completing appointment."; }
}

$filter = $_GET['filter'] ?? 'active';
if ($filter === 'active') {
    $where = "AND a.status IN ('pending','approved')";
} elseif ($filter === 'completed') {
    $where = "AND a.status = 'completed'";
} else {
    $where = '';
}

$appointments = $db->prepare("
    SELECT a.*, c.full_name as child_name, v.vaccine_name, u.email as parent_email
    FROM appointments a
    JOIN children c ON a.child_id = c.child_id
    JOIN vaccines v ON a.vaccine_id = v.vaccine_id
    JOIN parents p ON c.parent_id = p.parent_id
    JOIN users u ON p.user_id = u.user_id
    WHERE a.hospital_id = ? $where
    ORDER BY a.appointment_date ASC
");
$appointments->execute([$hid]);
$appointments = $appointments->fetchAll(PDO::FETCH_ASSOC);

include '../includes/header.php';
?>
<div class="container-fluid py-4 px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-0">Appointments</h3>
            <p class="text-muted small mb-0"><?= htmlspecialchars($hospital['hospital_name']) ?></p>
        </div>
        <div class="d-flex gap-2">
            <a href="?filter=active" class="btn btn-sm <?= $filter==='active' ? 'btn-primary' : 'btn-outline-secondary' ?>">Active</a>
            <a href="?filter=completed" class="btn btn-sm <?= $filter==='completed' ? 'btn-success' : 'btn-outline-secondary' ?>">Completed</a>
            <a href="?filter=all" class="btn btn-sm <?= $filter==='all' ? 'btn-dark' : 'btn-outline-secondary' ?>">All</a>
        </div>
    </div>

    <?php if ($success): ?><div class="alert alert-success"><i class="fas fa-check-circle me-1"></i><?= htmlspecialchars($success) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead><tr><th>Child Name</th><th>Parent Email</th><th>Vaccine</th><th>Date</th><th>Status</th><th class="text-center">Actions</th></tr></thead>
                    <tbody>
                    <?php foreach ($appointments as $a): ?>
                    <tr>
                        <td class="fw-semibold"><?= htmlspecialchars($a['child_name']) ?></td>
                        <td class="small text-muted"><?= htmlspecialchars($a['parent_email']) ?></td>
                        <td><span class="badge bg-info bg-opacity-15 text-info border border-info border-opacity-25"><?= htmlspecialchars($a['vaccine_name']) ?></span></td>
                        <td><?= date('d M Y', strtotime($a['appointment_date'])) ?></td>
                        <td>
                            <?php $cls=['pending'=>'bg-warning text-dark','approved'=>'bg-info','completed'=>'bg-success','cancelled'=>'bg-danger']; ?>
                            <span class="badge <?= $cls[$a['status']] ?? 'bg-secondary' ?>"><?= ucfirst($a['status']) ?></span>
                        </td>
                        <td class="text-center">
                            <?php if ($a['status'] === 'pending'): ?>
                                <a href="?approve=<?= $a['appointment_id'] ?>&filter=<?= $filter ?>" class="btn btn-sm btn-success me-1"><i class="fas fa-check me-1"></i>Approve</a>
                                <a href="?reject=<?= $a['appointment_id'] ?>&filter=<?= $filter ?>" onclick="return confirm('Reject this appointment?')" class="btn btn-sm btn-outline-danger"><i class="fas fa-times"></i></a>
                            <?php elseif ($a['status'] === 'approved'): ?>
                                <a href="?complete=<?= $a['appointment_id'] ?>&filter=<?= $filter ?>" onclick="return confirm('Mark vaccination as complete?')" class="btn btn-sm btn-primary"><i class="fas fa-check-double me-1"></i>Complete</a>
                            <?php else: ?>
                                <span class="text-muted small">—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($appointments)): ?><tr><td colspan="6" class="text-center py-5 text-muted">No appointments found.</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
