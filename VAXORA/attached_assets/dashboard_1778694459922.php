<?php
require_once 'includes/auth_check.php';
checkRole(['parent']);

require_once 'config/config.php';
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

// Get parent info with error handling
$parentQuery = "SELECT parent_id FROM parents WHERE user_id = :user_id LIMIT 1";
$parentStmt = $db->prepare($parentQuery);
$parentStmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
$parentStmt->execute();
$parent = $parentStmt->fetch(PDO::FETCH_ASSOC);

if (!$parent) {
    $_SESSION['error'] = "Parent profile not found. Please contact support.";
    header("Location: logout.php");
    exit();
}

$parent_id = (int)$parent['parent_id'];

/* ==============================================
   1. CHILDREN COUNT
============================================== */
try {
    $childrenQuery = "SELECT COUNT(*) as total FROM children WHERE parent_id = :parent_id";
    $stmt = $db->prepare($childrenQuery);
    $stmt->bindParam(':parent_id', $parent_id, PDO::PARAM_INT);
    $stmt->execute();
    $childrenCount = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
} catch (PDOException $e) {
    error_log("Dashboard Count Error: " . $e->getMessage());
    $childrenCount = 0;
}

/* ==============================================
   2. UPCOMING APPOINTMENTS (JOINS FIXED)
============================================== */
try {
    $appointmentsQuery = "
        SELECT a.*, c.full_name as child_name, h.hospital_name, v.vaccine_name
        FROM appointments a
        JOIN children c ON a.child_id = c.child_id
        JOIN hospitals h ON a.hospital_id = h.hospital_id
        JOIN vaccines v ON a.vaccine_id = v.vaccine_id
        WHERE c.parent_id = :parent_id
        AND a.status IN ('pending', 'approved')
        AND a.appointment_date >= CURDATE()
        ORDER BY a.appointment_date ASC
        LIMIT 5
    ";

    $stmt = $db->prepare($appointmentsQuery);
    $stmt->bindParam(':parent_id', $parent_id, PDO::PARAM_INT);
    $stmt->execute();
    $upcomingAppointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Dashboard Appointments Error: " . $e->getMessage());
    $upcomingAppointments = [];
}

/* ==============================================
   3. RECENT VACCINATIONS
============================================== */
try {
    $recordsQuery = "
        SELECT vr.*, c.full_name as child_name, v.vaccine_name, h.hospital_name
        FROM vaccination_records vr
        JOIN children c ON vr.child_id = c.child_id
        JOIN vaccines v ON vr.vaccine_id = v.vaccine_id
        JOIN hospitals h ON vr.hospital_id = h.hospital_id
        WHERE c.parent_id = :parent_id
        ORDER BY vr.administration_date DESC
        LIMIT 5
    ";

    $stmt = $db->prepare($recordsQuery);
    $stmt->bindParam(':parent_id', $parent_id, PDO::PARAM_INT);
    $stmt->execute();
    $recentVaccinations = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Dashboard Records Error: " . $e->getMessage());
    $recentVaccinations = [];
}

include 'includes/header.php';
?>

<div class="container-fluid py-4">

    <!-- DASHBOARD HEADER -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Parent Dashboard</h1>
        <div class="text-primary font-weight-bold">
            Total Children Registered: <?= $childrenCount ?>
        </div>
    </div>

    <div class="row">

        <!-- UPCOMING APPOINTMENTS -->
        <div class="col-xl-6 col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Upcoming Appointments</h6>
                    <a href="appointments.php" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    <?php if (!empty($upcomingAppointments)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Child</th>
                                        <th>Vaccine</th>
                                        <th>Hospital</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($upcomingAppointments as $app): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($app['child_name']) ?></td>
                                            <td><?= htmlspecialchars($app['vaccine_name']) ?></td>
                                            <td><?= htmlspecialchars($app['hospital_name']) ?></td>
                                            <td><?= date('d M Y', strtotime($app['appointment_date'])) ?></td>
                                            <td>
                                                <span class="badge bg-<?= ($app['status'] == 'approved') ? 'success' : 'warning' ?>">
                                                    <?= ucfirst($app['status']) ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <p class="text-muted">No pending appointments.</p>
                            <a href="book_appointment.php" class="btn btn-primary btn-sm">Book Now</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- RECENT VACCINATIONS -->
        <div class="col-xl-6 col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-success">Recent Vaccination History</h6>
                    <a href="reports.php" class="btn btn-sm btn-outline-success">View Records</a>
                </div>
                <div class="card-body">
                    <?php if (!empty($recentVaccinations)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Child</th>
                                        <th>Vaccine</th>
                                        <th>Date</th>
                                        <th>Hospital</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentVaccinations as $record): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($record['child_name']) ?></td>
                                            <td><?= htmlspecialchars($record['vaccine_name']) ?></td>
                                            <td><?= date('d M Y', strtotime($record['administration_date'])) ?></td>
                                            <td><?= htmlspecialchars($record['hospital_name']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <p class="text-muted">No vaccination history found.</p>
                            <i class="fas fa-syringe fa-2x text-light"></i>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>
</div>

<?php include 'includes/footer.php'; ?>