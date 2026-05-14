<?php
require_once '../includes/auth_check.php';
// Admin or Hospital staff can manage appointments
checkRole(['admin', 'hospital']); 

require_once '../config/config.php';
require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

/* ==============================================
   FETCH APPOINTMENTS WITH FULL DETAILS (PDO)
============================================== */
try {
    $query = "SELECT a.appointment_id, a.appointment_date, a.status,
                     c.full_name as child_name, 
                     v.vaccine_name, 
                     h.hospital_name
              FROM appointments a
              INNER JOIN children c ON a.child_id = c.child_id
              INNER JOIN vaccines v ON a.vaccine_id = v.vaccine_id
              INNER JOIN hospitals h ON a.hospital_id = h.hospital_id
              ORDER BY a.appointment_date DESC, a.appointment_id DESC";

    $stmt = $db->prepare($query);
    $stmt->execute();
    $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Fetch Appointments Error: " . $e->getMessage());
    $appointments = [];
}

include '../includes/header.php'; // Using your standard dashboard header
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0 text-gray-800"><i class="fas fa-calendar-check text-primary me-2"></i>Manage Appointments</h2>
        <span class="badge bg-primary px-3 py-2"><?= count($appointments) ?> Total Requests</span>
    </div>

    <div class="card shadow border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-dark">
                        <tr>
                            <th class="ps-4">Child Name</th>
                            <th>Vaccine Type</th>
                            <th>Hospital</th>
                            <th>Scheduled Date</th>
                            <th>Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($appointments)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fas fa-calendar-times fa-3x mb-3"></i><br>
                                    No appointments found in the system.
                                </td>
                            </tr>
                        <?php endif; ?>

                        <?php foreach ($appointments as $row): ?>
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold"><?= htmlspecialchars($row['child_name']) ?></div>
                            </td>
                            <td>
                                <span class="badge bg-info-subtle text-info border border-info-subtle px-2">
                                    <?= htmlspecialchars($row['vaccine_name']) ?>
                                </span>
                            </td>
                            <td><small class="text-muted"><?= htmlspecialchars($row['hospital_name']) ?></small></td>
                            <td><?= date('M d, Y', strtotime($row['appointment_date'])) ?></td>
                            <td>
                                <?php 
                                    $statusClass = 'bg-secondary';
                                    if($row['status'] == 'pending') $statusClass = 'bg-warning text-dark';
                                    if($row['status'] == 'completed') $statusClass = 'bg-success';
                                    if($row['status'] == 'cancelled') $statusClass = 'bg-danger';
                                ?>
                                <span class="badge <?= $statusClass ?> text-uppercase" style="font-size: 0.7rem;">
                                    <?= $row['status'] ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <?php if ($row['status'] == 'pending'): ?>
                                    <div class="btn-group">
                                        <a href="update_status.php?id=<?= $row['appointment_id'] ?>&status=completed" 
                                           class="btn btn-sm btn-success" 
                                           onclick="return confirm('Confirm vaccination completion?')">
                                            <i class="fas fa-check"></i>
                                        </a>
                                        <a href="update_status.php?id=<?= $row['appointment_id'] ?>&status=cancelled" 
                                           class="btn btn-sm btn-outline-danger"
                                           onclick="return confirm('Are you sure you want to cancel?')">
                                            <i class="fas fa-times"></i>
                                        </a>
                                    </div>
                                <?php else: ?>
                                    <span class="text-muted small italic">Processed</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>