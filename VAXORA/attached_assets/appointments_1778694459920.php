<?php
require_once 'includes/auth_check.php';
checkRole(['hospital']);

require_once 'config/config.php';
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

/* =========================
   GET HOSPITAL INFO
========================= */
$query = "SELECT * FROM hospitals WHERE email = :email LIMIT 1";
$stmt = $db->prepare($query);
$stmt->bindParam(':email', $_SESSION['email']);
$stmt->execute();
$hospital = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$hospital) {
    die("Hospital not found!");
}

$hospital_id = $hospital['hospital_id'];

/* =========================
   UPDATE STATUS (SAFE)
========================= */
if (isset($_GET['id'], $_GET['status'])) {

    $booking_id = (int) $_GET['id'];
    $status = $_GET['status'];

    $allowed = ['approved', 'rejected', 'completed'];

    if (in_array($status, $allowed)) {

        $updateQuery = "UPDATE bookings 
                        SET status = :status 
                        WHERE booking_id = :id 
                        AND hospital_id = :hospital_id";

        $updateStmt = $db->prepare($updateQuery);
        $updateStmt->bindParam(':status', $status);
        $updateStmt->bindParam(':id', $booking_id);
        $updateStmt->bindParam(':hospital_id', $hospital_id);

        $updateStmt->execute();

        header("Location: appointments.php");
        exit;
    }
}

/* =========================
   GET APPOINTMENTS
========================= */
$query = "SELECT b.*, 
                 c.full_name AS child_name,
                 u.email AS parent_email,
                 v.vaccine_name
          FROM bookings b
          LEFT JOIN children c ON b.child_id = c.child_id
          LEFT JOIN users u ON b.parent_id = u.user_id
          LEFT JOIN vaccines v ON b.vaccine_id = v.vaccine_id
          WHERE b.hospital_id = :hospital_id
          AND b.status IN ('pending','approved')
          ORDER BY b.booking_date ASC";

$stmt = $db->prepare($query);
$stmt->bindParam(':hospital_id', $hospital_id);
$stmt->execute();
$appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'includes/header.php';
?>

<div class="container py-4">

    <h2 class="mb-3">Appointments</h2>

    <?php if (count($appointments) > 0): ?>

        <div class="card shadow">
            <div class="card-body table-responsive">

                <table class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Child</th>
                            <th>Parent</th>
                            <th>Vaccine</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($appointments as $a): ?>
                            <tr>
                                <td><?= htmlspecialchars($a['child_name'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($a['parent_email'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($a['vaccine_name'] ?? 'N/A') ?></td>
                                <td>
                                    <?= !empty($a['booking_date']) 
                                        ? date('d M Y', strtotime($a['booking_date'])) 
                                        : 'Not set'; ?>
                                </td>

                                <td>
                                    <span class="badge bg-<?= 
                                        $a['status'] == 'approved' ? 'success' : 
                                        ($a['status'] == 'pending' ? 'warning' : 'secondary') ?>">
                                        <?= ucfirst($a['status']) ?>
                                    </span>
                                </td>

                                <td>
                                    <?php if ($a['status'] == 'pending'): ?>
                                        <a href="?id=<?= $a['booking_id'] ?>&status=approved" class="btn btn-sm btn-success">Approve</a>
                                        <a href="?id=<?= $a['booking_id'] ?>&status=rejected" class="btn btn-sm btn-danger">Reject</a>

                                    <?php elseif ($a['status'] == 'approved'): ?>
                                        <a href="?id=<?= $a['booking_id'] ?>&status=completed" class="btn btn-sm btn-primary">
                                            Complete
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>

                </table>

            </div>
        </div>

    <?php else: ?>
        <div class="text-center py-5">
            <h5>No Appointments Found</h5>
        </div>
    <?php endif; ?>

</div>

<?php include 'includes/footer.php'; ?>