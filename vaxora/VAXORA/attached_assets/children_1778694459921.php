<?php
require_once 'includes/auth_check.php';
checkRole(['parent']);

require_once 'config/config.php';
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

$parent_id = $_SESSION['user_id'];

$error = '';
$success = '';

/* =========================
   GET CHILDREN (FIXED)
========================= */
$query = "SELECT c.*,
    (SELECT COUNT(*) FROM bookings b 
     WHERE b.child_id = c.child_id AND b.status = 'pending') as pending_appointments,

    (SELECT COUNT(*) FROM vaccination_records v 
     WHERE v.child_id = c.child_id) as completed_vaccinations

FROM children c
WHERE c.parent_id = :parent_id
ORDER BY c.created_at DESC";

$stmt = $db->prepare($query);
$stmt->bindParam(':parent_id', $parent_id);
$stmt->execute();
$children = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* =========================
   DELETE CHILD (SAFE)
========================= */
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {

    $child_id = (int) $_GET['delete'];

    try {
        $checkQuery = "SELECT COUNT(*) as total FROM bookings WHERE child_id = :child_id";
        $checkStmt = $db->prepare($checkQuery);
        $checkStmt->bindParam(':child_id', $child_id);
        $checkStmt->execute();
        $result = $checkStmt->fetch(PDO::FETCH_ASSOC);

        if ($result['total'] > 0) {
            $error = "Cannot delete child with existing bookings.";
        } else {

            $deleteQuery = "DELETE FROM children 
                            WHERE child_id = :child_id 
                            AND parent_id = :parent_id";

            $deleteStmt = $db->prepare($deleteQuery);
            $deleteStmt->bindParam(':child_id', $child_id);
            $deleteStmt->bindParam(':parent_id', $parent_id);

            if ($deleteStmt->execute()) {
                $success = "Child deleted successfully.";
            } else {
                $error = "Failed to delete child.";
            }
        }

    } catch (PDOException $e) {
        $error = "Error deleting child.";
    }
}

include 'includes/header.php';
?>

<div class="container py-4">

    <div class="card shadow">

        <div class="card-header d-flex justify-content-between">
            <h5 class="m-0">My Children</h5>
            <a href="add_child.php" class="btn btn-primary btn-sm">+ Add Child</a>
        </div>

        <div class="card-body">

            <?php if ($success): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <?php if (count($children) > 0): ?>

                <div class="row">

                    <?php foreach ($children as $child): ?>

                        <div class="col-md-6 col-lg-4 mb-4">

                            <div class="card shadow-sm h-100">

                                <div class="card-body text-center">

                                    <i class="fas fa-child fa-3x text-primary mb-3"></i>

                                    <h5><?= htmlspecialchars($child['full_name']) ?></h5>

                                    <p class="text-muted">
                                        ID: <?= $child['child_id'] ?>
                                    </p>

                                    <p>
                                        <strong>DOB:</strong>
                                        <?= date('d M Y', strtotime($child['date_of_birth'])) ?>
                                    </p>

                                    <p>
                                        <strong>Age:</strong>
                                        <?php
                                        $age = floor((time() - strtotime($child['date_of_birth'])) / (365.25*24*60*60));
                                        echo $age . " years";
                                        ?>
                                    </p>

                                    <p>
                                        <strong>Gender:</strong>
                                        <?= ucfirst($child['gender']) ?>
                                    </p>

                                    <div class="mt-2">
                                        <span class="badge bg-warning">
                                            Pending: <?= $child['pending_appointments'] ?>
                                        </span>

                                        <span class="badge bg-success">
                                            Done: <?= $child['completed_vaccinations'] ?>
                                        </span>
                                    </div>

                                </div>

                                <div class="card-footer d-flex gap-2">

                                    <a href="edit_child.php?id=<?= $child['child_id'] ?>" class="btn btn-info btn-sm w-100">Edit</a>

                                    <a href="child_vaccinations.php?id=<?= $child['child_id'] ?>" class="btn btn-primary btn-sm w-100">Records</a>

                                    <a href="?delete=<?= $child['child_id'] ?>"
                                       onclick="return confirm('Delete this child?')"
                                       class="btn btn-danger btn-sm w-100">
                                       Delete
                                    </a>

                                </div>

                            </div>

                        </div>

                    <?php endforeach; ?>

                </div>

            <?php else: ?>

                <div class="text-center py-5">
                    <h5>No children found</h5>
                    <a href="add_child.php" class="btn btn-primary mt-2">Add First Child</a>
                </div>

            <?php endif; ?>

        </div>

    </div>

</div>

<?php include 'includes/footer.php'; ?>