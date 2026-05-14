<?php
require_once '../includes/auth_check.php';
checkRole(['admin']);
require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

/* ================= DELETE HOSPITAL ================= */
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {

    $hospital_id = (int) $_GET['delete'];

    try {
        // check appointments
        $check = $db->prepare("SELECT COUNT(*) as total FROM appointments WHERE hospital_id = :id");
        $check->bindParam(':id', $hospital_id);
        $check->execute();
        $count = $check->fetch(PDO::FETCH_ASSOC)['total'];

        if ($count > 0) {
            $error = "❌ Cannot delete hospital with appointments.";
        } else {

            $db->beginTransaction();

            // get user_id
            $q = $db->prepare("SELECT user_id FROM hospitals WHERE hospital_id = :id");
            $q->bindParam(':id', $hospital_id);
            $q->execute();
            $user = $q->fetch(PDO::FETCH_ASSOC);

            if ($user) {

                // delete hospital
                $del = $db->prepare("DELETE FROM hospitals WHERE hospital_id = :id");
                $del->bindParam(':id', $hospital_id);
                $del->execute();

                // delete user
                $delUser = $db->prepare("DELETE FROM users WHERE user_id = :uid");
                $delUser->bindParam(':uid', $user['user_id']);
                $delUser->execute();
            }

            $db->commit();
            $success = "✅ Hospital deleted successfully.";
        }

    } catch(Exception $e) {
        $db->rollBack();
        $error = "❌ Error deleting hospital.";
    }
}

/* ================= STATUS TOGGLE ================= */
if (isset($_POST['toggle_status'])) {

    $hospital_id = $_POST['hospital_id'];
    $new_status = $_POST['new_status'];

    try {
        $q = $db->prepare("UPDATE hospitals SET status = :status WHERE hospital_id = :id");
        $q->bindParam(':status', $new_status);
        $q->bindParam(':id', $hospital_id);
        $q->execute();

        $q2 = $db->prepare("UPDATE users SET status = :status WHERE user_id = (SELECT user_id FROM hospitals WHERE hospital_id = :id)");
        $q2->bindParam(':status', $new_status);
        $q2->bindParam(':id', $hospital_id);
        $q2->execute();

        $success = "✅ Status updated successfully.";

    } catch(Exception $e) {
        $error = "❌ Status update failed.";
    }
}

/* ================= FETCH HOSPITALS ================= */
$stmt = $db->prepare("
    SELECT h.*, u.status as user_status 
    FROM hospitals h 
    JOIN users u ON h.user_id = u.user_id 
    ORDER BY h.created_at DESC
");
$stmt->execute();
$hospitals = $stmt->fetchAll(PDO::FETCH_ASSOC);

include '../includes/admin_header.php';
?>
<link rel="stylesheet" href="../admin.css">
<div class="container-fluid">

    <div class="card shadow">

        <div class="card-header d-flex justify-content-between">
            <h5>🏥 Manage Hospitals</h5>
            <a href="add_hospital.php" class="btn btn-primary btn-sm">+ Add Hospital</a>
        </div>

        <div class="card-body">

            <!-- ALERTS -->
            <?php if(isset($success)){ ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php } ?>

            <?php if(isset($error)){ ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php } ?>

            <table class="table table-bordered table-hover">

                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Reg No</th>
                        <th>City</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>

                <?php foreach($hospitals as $h){ ?>

                    <tr>
                        <td><?php echo $h['hospital_id']; ?></td>
                        <td><?php echo $h['hospital_name']; ?></td>
                        <td><?php echo $h['registration_number']; ?></td>
                        <td><?php echo $h['city']; ?></td>
                        <td><?php echo $h['phone']; ?></td>

                        <!-- STATUS -->
                        <td>
                            <form method="POST">
                                <input type="hidden" name="hospital_id" value="<?php echo $h['hospital_id']; ?>">
                                <input type="hidden" name="new_status" 
                                       value="<?php echo $h['user_status']=='active'?'inactive':'active'; ?>">

                                <button class="btn btn-sm btn-<?php echo $h['user_status']=='active'?'success':'secondary'; ?>" 
                                        name="toggle_status">
                                    <?php echo ucfirst($h['user_status']); ?>
                                </button>
                            </form>
                        </td>

                        <!-- ACTIONS -->
                        <td>

                            <a href="edit_hospital.php?id=<?php echo $h['hospital_id']; ?>" 
                               class="btn btn-info btn-sm">
                               Edit
                            </a>

                            <a href="manage_hospitals.php?delete=<?php echo $h['hospital_id']; ?>" 
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('Delete this hospital?');">
                               Delete
                            </a>

                            <a href="hospital_vaccines.php?id=<?php echo $h['hospital_id']; ?>" 
                               class="btn btn-primary btn-sm">
                               Vaccines
                            </a>

                        </td>

                    </tr>

                <?php } ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

<?php include '../includes/admin_footer.php'; ?>