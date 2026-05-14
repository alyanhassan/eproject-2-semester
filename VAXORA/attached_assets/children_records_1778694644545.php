<?php
require_once '../includes/auth_check.php';
checkRole(['admin']);

require_once '../config/config.php';
require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

/* ==============================================
   FETCH CHILDREN (Switching to PDO)
============================================== */
$query = "SELECT * FROM children ORDER BY child_id DESC";
$stmt = $db->prepare($query);
$stmt->execute();
$records = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Children Records | Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #f8f9fc; }
        .table-container { background: white; border-radius: 10px; padding: 20px; box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1); }
        .badge-id { font-family: 'Courier New', Courier, monospace; font-weight: bold; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="text-gray-800"><i class="fas fa-baby me-2 text-primary"></i>Children Records</h3>
        <div class="no-print">
            <button onclick="window.print()" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-print me-1"></i> Print Report
            </button>
            <a href="dashboard.php" class="btn btn-primary btn-sm ms-2">Back to Dashboard</a>
        </div>
    </div>

    <div class="table-container">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Child Name</th>
                        <th>DOB</th>
                        <th>Gender</th>
                        <th>Blood</th>
                        <th>Unique ID</th>
                        <th>Parent ID</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(count($records) > 0): ?>
                        <?php foreach($records as $row): ?>
                        <tr>
                            <td><?= $row['child_id']; ?></td>
                            <td class="fw-bold"><?= htmlspecialchars($row['full_name']); ?></td>
                            <td><?= date('M d, Y', strtotime($row['dob'])); ?></td>
                            <td><?= $row['gender']; ?></td>
                            <td><span class="text-danger fw-bold"><?= $row['blood_group']; ?></span></td>
                            <td><span class="badge bg-light text-primary border badge-id"><?= $row['unique_reg_id']; ?></span></td>
                            <td><span class="text-muted">User #<?= $row['parent_id']; ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No children records found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>