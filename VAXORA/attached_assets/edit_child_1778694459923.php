<?php
require_once 'includes/auth_check.php';
checkRole(['parent']);

require_once 'config/config.php';
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

// Get parent_id from user session
$parentQuery = "SELECT parent_id FROM parents WHERE user_id = :user_id LIMIT 1";
$parentStmt = $db->prepare($parentQuery);
$parentStmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
$parentStmt->execute();
$parent = $parentStmt->fetch(PDO::FETCH_ASSOC);

if (!$parent) {
    header("Location: logout.php");
    exit();
}

$parent_id = (int)$parent['parent_id'];

// Get and validate child ID from URL
$child_id = isset($_GET['id']) ? (int)$_GET['id'] : null;

if (!$child_id) {
    header("Location: children.php");
    exit();
}

/* ==============================================
   FETCH CHILD DATA (SECURITY CHECK)
============================================== */
$query = "SELECT * FROM children WHERE child_id = :child_id AND parent_id = :parent_id LIMIT 1";
$stmt = $db->prepare($query);
$stmt->bindParam(':child_id', $child_id, PDO::PARAM_INT);
$stmt->bindParam(':parent_id', $parent_id, PDO::PARAM_INT);
$stmt->execute();
$child = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$child) {
    $_SESSION['error'] = "Child record not found or access denied.";
    header("Location: children.php");
    exit();
}

$success = "";
$error = "";

/* ==============================================
   HANDLE UPDATE REQUEST
============================================== */
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $full_name = trim($_POST['full_name']);
    $date_of_birth = $_POST['date_of_birth'];
    $gender = $_POST['gender'];

    if (!empty($full_name) && !empty($date_of_birth) && !empty($gender)) {

        try {
            $update = "UPDATE children 
                       SET full_name = :full_name,
                           date_of_birth = :date_of_birth,
                           gender = :gender
                       WHERE child_id = :child_id 
                       AND parent_id = :parent_id";

            $stmt = $db->prepare($update);
            $stmt->bindParam(':full_name', $full_name);
            $stmt->bindParam(':date_of_birth', $date_of_birth);
            $stmt->bindParam(':gender', $gender);
            $stmt->bindParam(':child_id', $child_id, PDO::PARAM_INT);
            $stmt->bindParam(':parent_id', $parent_id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                $success = "Child details updated successfully!";
                
                // Update the local $child array to reflect new values in the form
                $child['full_name'] = $full_name;
                $child['date_of_birth'] = $date_of_birth;
                $child['gender'] = $gender;
            } else {
                $error = "Failed to save changes. Please try again.";
            }
        } catch (PDOException $e) {
            error_log("Update Child Error: " . $e->getMessage());
            $error = "A database error occurred.";
        }

    } else {
        $error = "All fields are required.";
    }
}

include 'includes/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Edit Child Profile</h5>
                    <i class="fas fa-user-edit"></i>
                </div>
                <div class="card-body p-4">

                    <?php if($success): ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            <?= htmlspecialchars($success) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <?= htmlspecialchars($error) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Full Name</label>
                            <input type="text" name="full_name" class="form-control"
                                   value="<?= htmlspecialchars($child['full_name']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Date of Birth</label>
                            <input type="date" name="date_of_birth" class="form-control"
                                   value="<?= htmlspecialchars($child['date_of_birth']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Gender</label>
                            <select name="gender" class="form-select" required>
                                <option value="male" <?= $child['gender'] == 'male' ? 'selected' : '' ?>>Male</option>
                                <option value="female" <?= $child['gender'] == 'female' ? 'selected' : '' ?>>Female</option>
                                <option value="other" <?= $child['gender'] == 'other' ? 'selected' : '' ?>>Other</option>
                            </select>
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Save Changes
                            </button>
                            <a href="children.php" class="btn btn-outline-secondary">
                                Cancel
                            </a>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>