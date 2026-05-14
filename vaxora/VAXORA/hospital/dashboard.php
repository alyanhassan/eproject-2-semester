<?php
require_once '../includes/auth_check.php';
checkRole(['hospital']);
require_once '../config/config.php';
require_once '../config/database.php';

$db = (new Database())->getConnection();

$hStmt = $db->prepare("SELECT * FROM hospitals WHERE user_id=? LIMIT 1");
$hStmt->execute([$_SESSION['user_id']]);
$hospital = $hStmt->fetch(PDO::FETCH_ASSOC);
if (!$hospital) { session_destroy(); redirect('/auth/login.php'); }
$hid = $hospital['hospital_id'];

$stats = [
    'pending'   => $db->prepare("SELECT COUNT(*) FROM appointments WHERE hospital_id=? AND status='pending'"),
    'approved'  => $db->prepare("SELECT COUNT(*) FROM appointments WHERE hospital_id=? AND status='approved'"),
    'completed' => $db->prepare("SELECT COUNT(*) FROM vaccination_records WHERE hospital_id=?"),
    'total'     => $db->prepare("SELECT COUNT(*) FROM appointments WHERE hospital_id=?"),
];
foreach ($stats as $k => $s) { $s->execute([$hid]); $stats[$k] = $s->fetchColumn(); }

$recent = $db->prepare("
    SELECT a.*, c.full_name as child_name, v.vaccine_name, u.email as parent_email
    FROM appointments a
    JOIN children c ON a.child_id = c.child_id
    JOIN vaccines v ON a.vaccine_id = v.vaccine_id
    JOIN parents p ON c.parent_id = p.parent_id
    JOIN users u ON p.user_id = u.user_id
    WHERE a.hospital_id = ? AND a.status IN ('pending','approved')
    ORDER BY a.appointment_date ASC LIMIT 6
");
$recent->execute([$hid]);
$upcoming = $recent->fetchAll(PDO::FETCH_ASSOC);

include '../includes/header.php';
?>
<div class="container-fluid py-4 px-4">
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
        <div>
            <h3 class="fw-bold mb-0"><?= htmlspecialchars($hospital['hospital_name']) ?></h3>
            <p class="text-muted small mb-0"><i class="fas fa-map-marker-alt me-1"></i><?= htmlspecialchars($hospital['city'] ?? '') ?> — Hospital Dashboard</p>
        </div>
        <a href="/hospital/appointments.php" class="btn btn-primary"><i class="fas fa-calendar-check me-2"></i>Manage Appointments</a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3"><div class="card stat-card sc-amber p-3"><div class="stat-num"><?= $stats['pending'] ?></div><div class="stat-label">Pending Requests</div><i class="fas fa-clock"></i></div></div>
        <div class="col-6 col-md-3"><div class="card stat-card sc-blue p-3"><div class="stat-num"><?= $stats['approved'] ?></div><div class="stat-label">Approved</div><i class="fas fa-calendar-check"></i></div></div>
        <div class="col-6 col-md-3"><div class="card stat-card sc-green p-3"><div class="stat-num"><?= $stats['completed'] ?></div><div class="stat-label">Vaccinations Done</div><i class="fas fa-check-double"></i></div></div>
        <div class="col-6 col-md-3"><div class="card stat-card sc-purple p-3"><div class="stat-num"><?= $stats['total'] ?></div><div class="stat-label">Total Appointments</div><i class="fas fa-calendar-alt"></i></div></div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-calendar-check text-warning me-2"></i>Upcoming Appointments</span>
            <a href="/hospital/appointments.php" class="btn btn-sm btn-outline-primary">View All</a>
        </div>
        <div class="card-body p-0">
            <?php if ($upcoming): ?>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead><tr><th>Child</th><th>Parent</th><th>Vaccine</th><th>Date</th><th>Status</th><th class="text-center">Actions</th></tr></thead>
                    <tbody>
                    <?php foreach ($upcoming as $a): ?>
                        <tr>
                            <td class="fw-semibold"><?= htmlspecialchars($a['child_name']) ?></td>
                            <td class="small text-muted"><?= htmlspecialchars($a['parent_email']) ?></td>
                            <td><?= htmlspecialchars($a['vaccine_name']) ?></td>
                            <td><?= date('d M Y', strtotime($a['appointment_date'])) ?></td>
                            <td><span class="badge <?= $a['status']==='approved' ? 'bg-info' : 'bg-warning text-dark' ?>"><?= ucfirst($a['status']) ?></span></td>
                            <td class="text-center">
                                <?php if ($a['status']==='pending'): ?>
                                    <a href="/hospital/appointments.php?approve=<?= $a['appointment_id'] ?>" class="btn btn-sm btn-success me-1"><i class="fas fa-check"></i> Approve</a>
                                    <a href="/hospital/appointments.php?reject=<?= $a['appointment_id'] ?>" class="btn btn-sm btn-outline-danger"><i class="fas fa-times"></i></a>
                                <?php elseif ($a['status']==='approved'): ?>
                                    <a href="/hospital/appointments.php?complete=<?= $a['appointment_id'] ?>" onclick="return confirm('Mark as completed?')" class="btn btn-sm btn-primary"><i class="fas fa-check-double me-1"></i>Complete</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-calendar-times fa-3x text-muted opacity-25 mb-3 d-block"></i>
                <p class="text-muted">No pending appointments</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
