<?php
require_once 'includes/auth_check.php';
checkRole(['parent']);

require_once 'config/config.php';
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

/* ==============================================
   1. SECURELY GET PARENT ID
============================================== */
$parentQuery = "SELECT parent_id FROM parents WHERE user_id = :user_id LIMIT 1";
$stmt = $db->prepare($parentQuery);
$stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
$stmt->execute();
$parent = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$parent) {
    die("Access Denied: Parent profile not found.");
}

$parent_id = $parent['parent_id'];

/* ==============================================
   2. FETCH VACCINATION HISTORY (COMPLETED ONLY)
============================================== */
$query = "SELECT vr.*, 
          c.full_name as child_name,
          v.vaccine_name,
          h.hospital_name
          FROM vaccination_records vr
          INNER JOIN children c ON vr.child_id = c.child_id
          INNER JOIN vaccines v ON vr.vaccine_id = v.vaccine_id
          INNER JOIN hospitals h ON vr.hospital_id = h.hospital_id
          WHERE c.parent_id = :parent_id
          ORDER BY vr.administration_date DESC";

$stmt = $db->prepare($query);
$stmt->bindParam(':parent_id', $parent_id, PDO::PARAM_INT);
$stmt->execute();
$records = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'includes/header.php';
?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0 text-gray-800">Vaccination History</h2>
        <a href="book_appointment.php" class="btn btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50 me-1"></i> New Appointment
        </a>
    </div>

    <div class="card shadow border-0">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 font-weight-bold text-primary">Immunization Records</h6>
        </div>
        <div class="card-body">

            <?php if(count($records) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="historyTable">
                        <thead class="table-light text-secondary uppercase font-weight-bold">
                            <tr>
                                <th>Child Name</th>
                                <th>Vaccine</th>
                                <th>Hospital</th>
                                <th>Date Administered</th>
                                <th>Status</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($records as $r): ?>
                                <tr>
                                    <td class="fw-bold"><?= htmlspecialchars($r['child_name']); ?></td>
                                    <td>
                                        <span class="badge bg-light text-dark border">
                                            <?= htmlspecialchars($r['vaccine_name']); ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($r['hospital_name']); ?></td>
                                    <td><?= date('M d, Y', strtotime($r['administration_date'])); ?></td>
                                    <td>
                                        <span class="badge rounded-pill bg-success-soft text-success border border-success px-3">
                                            <i class="fas fa-check-circle me-1"></i> Completed
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-outline-primary" onclick="window.print()">
                                            <i class="fas fa-print"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <!-- EMPTY STATE (Cleaner than demo data) -->
                <div class="text-center py-5">
                    <div class="mb-3">
                        <i class="fas fa-notes-medical fa-4x text-gray-300"></i>
                    </div>
                    <h5 class="text-secondary">No records found</h5>
                    <p class="text-muted mb-4">You haven't completed any vaccination appointments yet.</p>
                    <a href="book_appointment.php" class="btn btn-primary px-4">Book Your First Appointment</a>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<style>
    /* Styling the success badge for a more professional look */
    .bg-success-soft {
        background-color: #eafaf1;
    }
    .text-secondary {
        color: #6c757d !important;
    }
    .uppercase {
        text-transform: uppercase;
        letter-spacing: 1px;
        font-size: 0.75rem;
    }
</style>

<?php include 'includes/footer.php'; ?>