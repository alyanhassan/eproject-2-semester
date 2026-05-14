<?php
require_once 'includes/auth_check.php';
checkRole(['parent']);

require_once 'config/config.php';
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

$message = "";

// GET PARENT ID
$parent_id = $_SESSION['user_id'];

// HANDLE BOOKING SUBMIT
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $child_id = $_POST['child_id'];
    $hospital_id = $_POST['hospital_id'];
    $vaccine_id = $_POST['vaccine_id'];
    $booking_date = $_POST['booking_date'];

    $query = "INSERT INTO bookings 
              (parent_id, child_id, hospital_id, vaccine_id, booking_date, status)
              VALUES 
              (:parent_id, :child_id, :hospital_id, :vaccine_id, :booking_date, 'pending')";

    $stmt = $db->prepare($query);

    $stmt->bindParam(':parent_id', $parent_id);
    $stmt->bindParam(':child_id', $child_id);
    $stmt->bindParam(':hospital_id', $hospital_id);
    $stmt->bindParam(':vaccine_id', $vaccine_id);
    $stmt->bindParam(':booking_date', $booking_date);

    if ($stmt->execute()) {
        $message = "Appointment booked successfully!";
    } else {
        $message = "Failed to book appointment.";
    }
}

// GET CHILDREN OF PARENT
$childQuery = "SELECT * FROM children WHERE parent_id = :parent_id";
$stmt = $db->prepare($childQuery);
$stmt->bindParam(':parent_id', $parent_id);
$stmt->execute();
$children = $stmt->fetchAll(PDO::FETCH_ASSOC);

// GET HOSPITALS
$hospitalQuery = "SELECT * FROM hospitals WHERE status = 'active'";
$hstmt = $db->prepare($hospitalQuery);
$hstmt->execute();
$hospitals = $hstmt->fetchAll(PDO::FETCH_ASSOC);

// GET VACCINES
$vaccineQuery = "SELECT * FROM vaccines WHERE availability = 'available'";
$vstmt = $db->prepare($vaccineQuery);
$vstmt->execute();
$vaccines = $vstmt->fetchAll(PDO::FETCH_ASSOC);

include 'includes/header.php';
?>

<div class="container py-5">

    <h2 class="mb-4">Book Appointment</h2>

    <?php if($message != ""): ?>
        <div class="alert alert-info"><?php echo $message; ?></div>
    <?php endif; ?>

    <form method="POST" class="card p-4 shadow">

        <!-- CHILD -->
        <div class="mb-3">
            <label>Child</label>
            <select name="child_id" class="form-control" required>
                <option value="">Select Child</option>
                <?php foreach($children as $child): ?>
                    <option value="<?php echo $child['id']; ?>">
                        <?php echo $child['name']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- HOSPITAL -->
        <div class="mb-3">
            <label>Hospital</label>
            <select name="hospital_id" class="form-control" required>
                <option value="">Select Hospital</option>
                <?php foreach($hospitals as $hospital): ?>
                    <option value="<?php echo $hospital['id']; ?>">
                        <?php echo $hospital['name']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- VACCINE -->
        <div class="mb-3">
            <label>Vaccine</label>
            <select name="vaccine_id" class="form-control" required>
                <option value="">Select Vaccine</option>
                <?php foreach($vaccines as $vaccine): ?>
                    <option value="<?php echo $vaccine['id']; ?>">
                        <?php echo $vaccine['name']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- DATE -->
        <div class="mb-3">
            <label>Booking Date</label>
            <input type="date" name="booking_date" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">
            Book Appointment
        </button>

    </form>
</div>

<?php include 'includes/footer.php'; ?>