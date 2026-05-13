<?php
require_once 'includes/auth_check.php';
checkRole(['parent']);

require_once 'config/config.php';
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

/* ==============================================
   1. GET PARENT & DATA FOR SELECTS
============================================== */
// Get parent_id securely
$parentQuery = "SELECT parent_id FROM parents WHERE user_id = :user_id LIMIT 1";
$stmt = $db->prepare($parentQuery);
$stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
$stmt->execute();
$parent = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$parent) {
    die("Error: Parent profile not found.");
}

$parent_id = $parent['parent_id'];

// Get children belonging to this parent
$childQuery = "SELECT child_id, full_name FROM children WHERE parent_id = :parent_id";
$stmt = $db->prepare($childQuery);
$stmt->bindParam(':parent_id', $parent_id, PDO::PARAM_INT);
$stmt->execute();
$children = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get active hospitals
$hospitalQuery = "SELECT hospital_id, hospital_name FROM hospitals WHERE status = 'active'";
$stmt = $db->prepare($hospitalQuery);
$stmt->execute();
$hospitals = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get active vaccines
$vaccineQuery = "SELECT vaccine_id, vaccine_name FROM vaccines WHERE status = 'active'";
$stmt = $db->prepare($vaccineQuery);
$stmt->execute();
$vaccines = $stmt->fetchAll(PDO::FETCH_ASSOC);

$success = "";
$error = "";

/* ==============================================
   2. HANDLE BOOKING FORM SUBMISSION
============================================== */
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $child_id = $_POST['child_id'];
    $hospital_id = $_POST['hospital_id'];
    $vaccine_id = $_POST['vaccine_id'];
    $appointment_date = $_POST['appointment_date'];

    if ($child_id && $hospital_id && $vaccine_id && $appointment_date) {
        
        // Basic Date Validation (Ensure not in the past)
        if (strtotime($appointment_date) < strtotime(date('Y-m-d'))) {
            $error = "Appointment date cannot be in the past.";
        } else {
            try {
                $insert = "INSERT INTO appointments 
                          (child_id, hospital_id, vaccine_id, appointment_date, status, created_at)
                          VALUES (:child_id, :hospital_id, :vaccine_id, :appointment_date, 'pending', NOW())";

                $stmt = $db->prepare($insert);
                $stmt->bindParam(':child_id', $child_id, PDO::PARAM_INT);
                $stmt->bindParam(':hospital_id', $hospital_id, PDO::PARAM_INT);
                $stmt->bindParam(':vaccine_id', $vaccine_id, PDO::PARAM_INT);
                $stmt->bindParam(':appointment_date', $appointment_date);

                if ($stmt->execute()) {
                    $success = "Appointment booked successfully! Please wait for hospital approval.";
                } else {
                    $error = "Failed to process booking.";
                }
            } catch (PDOException $e) {
                error_log("Booking Error: " . $e->getMessage());
                $error = "A database error occurred.";
            }
        }
    } else {
        $error = "Please fill in all fields.";
    }
}

include 'includes/header.php';
?>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-7 mx-auto">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0"><i class="fas fa-calendar-plus me-2"></i>Schedule Vaccination</h5>
                </div>
                <div class="card-body p-4">

                    <?php if($success): ?>
                        <div class="alert alert-success border-0 shadow-sm"><?= htmlspecialchars($success) ?></div>
                    <?php endif; ?>

                    <?php if($error): ?>
                        <div class="alert alert-danger border-0 shadow-sm"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>

                    <form method="POST">

                        <!-- Child Selection -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Select Child</label>
                            <select name="child_id" class="form-select" required>
                                <option value="">-- Choose Child --</option>
                                <?php foreach($children as $child): ?>
                                    <option value="<?= $child['child_id']; ?>">
                                        <?= htmlspecialchars($child['full_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Hospital Selection -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Select Hospital</label>
                            <select name="hospital_id" class="form-select" required>
                                <option value="">-- Choose Hospital --</option>
                                <?php foreach($hospitals as $h): ?>
                                    <option value="<?= $h['hospital_id']; ?>">
                                        <?= htmlspecialchars($h['hospital_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Vaccine Selection -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Select Vaccine</label>
                            <select name="vaccine_id" class="form-select" required>
                                <option value="">-- Choose Vaccine --</option>
                                <?php foreach($vaccines as $v): ?>
                                    <option value="<?= $v['vaccine_id']; ?>">
                                        <?= htmlspecialchars($v['vaccine_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Appointment Date -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Appointment Date</label>
                            <input type="date" name="appointment_date" class="form-control" 
                                   min="<?= date('Y-m-d') ?>" required>
                            <div class="form-text">Choose a date from today onwards.</div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-check-circle me-1"></i> Confirm Booking
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>