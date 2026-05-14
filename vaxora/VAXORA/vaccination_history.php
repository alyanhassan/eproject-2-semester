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

$stmt = $db->prepare("
    SELECT vr.*, c.full_name as child_name, v.vaccine_name, h.hospital_name
    FROM vaccination_records vr
    JOIN children c ON vr.child_id = c.child_id
    JOIN vaccines v ON vr.vaccine_id = v.vaccine_id
    JOIN hospitals h ON vr.hospital_id = h.hospital_id
    WHERE c.parent_id = ?
    ORDER BY vr.administration_date DESC
");
$stmt->execute([$parent_id]);
$records = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Also get pending/upcoming appointments
$apptStmt = $db->prepare("
    SELECT a.*, c.full_name as child_name, v.vaccine_name, h.hospital_name
    FROM appointments a
    JOIN children c ON a.child_id = c.child_id
    JOIN vaccines v ON a.vaccine_id = v.vaccine_id
    JOIN hospitals h ON a.hospital_id = h.hospital_id
    WHERE c.parent_id = ? AND a.status != 'completed'
    ORDER BY a.appointment_date ASC
");
$apptStmt->execute([$parent_id]);
$appointments = $apptStmt->fetchAll(PDO::FETCH_ASSOC);

include 'includes/header.php';
?>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-0">Vaccination History</h3>
            <p class="text-muted small mb-0"><?= count($records) ?> completed vaccination(s)</p>
        </div>
        <a href="/book_appointment.php" class="btn btn-primary"><i class="fas fa-calendar-plus me-2"></i>New Appointment</a>
    </div>

    <!-- Pending Appointments -->
    <?php if ($appointments): ?>
    <div class="card mb-4">
        <div class="card-header"><i class="fas fa-clock text-warning me-2"></i>Upcoming & Pending Appointments</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead><tr><th>Child</th><th>Vaccine</th><th>Hospital</th><th>Date</th><th>Status</th></tr></thead>
                    <tbody>
                    <?php foreach ($appointments as $a): ?>
                        <tr>
                            <td class="fw-semibold"><?= htmlspecialchars($a['child_name']) ?></td>
                            <td><?= htmlspecialchars($a['vaccine_name']) ?></td>
                            <td class="text-muted small"><?= htmlspecialchars($a['hospital_name']) ?></td>
                            <td><?= date('d M Y', strtotime($a['appointment_date'])) ?></td>
                            <td>
                                <?php
                                $cls = ['pending' => 'bg-warning text-dark', 'approved' => 'bg-success', 'cancelled' => 'bg-danger'];
                                $c = $cls[$a['status']] ?? 'bg-secondary';
                                ?>
                                <span class="badge <?= $c ?>"><?= ucfirst($a['status']) ?></span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Completed Records -->
    <div class="card" id="printArea">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-notes-medical text-success me-2"></i>Immunization Records</span>
            <button onclick="printSection('printArea')" class="btn btn-sm btn-outline-secondary no-print"><i class="fas fa-print me-1"></i>Print</button>
        </div>
        <div class="card-body p-0">
            <?php if ($records): ?>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead><tr><th>Child</th><th>Vaccine</th><th>Hospital</th><th>Date Administered</th><th>Status</th></tr></thead>
                    <tbody>
                    <?php foreach ($records as $r): ?>
                        <tr>
                            <td class="fw-semibold"><?= htmlspecialchars($r['child_name']) ?></td>
                            <td><span class="badge bg-info bg-opacity-15 text-info border border-info border-opacity-25"><?= htmlspecialchars($r['vaccine_name']) ?></span></td>
                            <td class="text-muted small"><?= htmlspecialchars($r['hospital_name']) ?></td>
                            <td><?= date('d M Y', strtotime($r['administration_date'])) ?></td>
                            <td><span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Completed</span></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-notes-medical fa-4x text-muted opacity-25 mb-3 d-block"></i>
                <h6 class="text-muted">No completed vaccination records yet</h6>
                <a href="/book_appointment.php" class="btn btn-primary btn-sm mt-2">Book First Appointment</a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>
