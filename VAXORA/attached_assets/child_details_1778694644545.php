<?php
require_once '../includes/auth_check.php';
checkRole(['admin']);

require_once '../config/config.php';
require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

/* ==============================================
   FETCH ALL CHILDREN WITH PARENT DETAILS (PDO)
============================================== */
try {
    // We join 'children' with 'parents' then 'users' to get the parent's actual name
    $query = "SELECT c.*, u.name as parent_name, u.email as parent_email
              FROM children c
              JOIN parents p ON c.parent_id = p.parent_id
              JOIN users u ON p.user_id = u.user_id
              ORDER BY c.created_at DESC";

    $stmt = $db->prepare($query);
    $stmt->execute();
    $children = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Admin Child Fetch Error: " . $e->getMessage());
    $children = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin | Child Registry</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f8f9fc; font-family: 'Inter', sans-serif; }
        .navbar { background: #4e73df; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .card { border: none; border-radius: 12px; }
        .table thead { background-color: #f8f9fc; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 1px; }
        .search-box { border-radius: 20px; padding-left: 40px; }
        .search-icon { position: absolute; left: 15px; top: 10px; color: #aaa; }
    </style>
</head>
<body>

<nav class="navbar navbar-dark py-3">
    <div class="container-fluid px-4">
        <span class="navbar-brand fw-bold"><i class="fas fa-database me-2"></i> VaxTrack Master Registry</span>
        <div>
            <a href="dashboard.php" class="btn btn-light btn-sm me-2">Dashboard</a>
            <a href="../auth/logout.php" class="btn btn-danger btn-sm">Logout</a>
        </div>
    </div>
</nav>

<div class="container-fluid mt-4 px-4">
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h3 class="fw-bold text-gray-800">Registered Children</h3>
            <p class="text-muted small">Total: <?= count($children) ?> children in system</p>
        </div>
        <div class="col-md-6">
            <div class="position-relative">
                <i class="fas fa-search search-icon"></i>
                <input type="text" id="childSearch" class="form-control search-box shadow-sm" placeholder="Search by name, ID or parent...">
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="childrenTable">
                    <thead>
                        <tr>
                            <th class="ps-4">Unique ID</th>
                            <th>Child Name</th>
                            <th>DOB</th>
                            <th>Gender / Blood</th>
                            <th>Parent Details</th>
                            <th>Registration Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($children as $row): ?>
                        <tr>
                            <td class="ps-4">
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                                    <?= $row['unique_reg_id'] ?>
                                </span>
                            </td>
                            <td>
                                <div class="fw-bold"><?= htmlspecialchars($row['full_name']) ?></div>
                                <small class="text-muted">ID: #<?= $row['child_id'] ?></small>
                            </td>
                            <td><?= date('M d, Y', strtotime($row['dob'])) ?></td>
                            <td>
                                <span class="text-capitalize"><?= $row['gender'] ?></span> 
                                <span class="mx-1 text-muted">|</span> 
                                <span class="text-danger fw-bold"><?= $row['blood_group'] ?: '??' ?></span>
                            </td>
                            <td>
                                <div class="small fw-bold"><?= htmlspecialchars($row['parent_name']) ?></div>
                                <div class="text-muted" style="font-size: 0.75rem;"><?= htmlspecialchars($row['parent_email']) ?></div>
                            </td>
                            <td class="small text-muted">
                                <?= date('Y-m-d H:i', strtotime($row['created_at'])) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        
                        <?php if(empty($children)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" width="80" class="mb-3 opacity-50">
                                <p class="text-muted">No children records found in the database.</p>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Simple JS for Real-time Search -->
<script>
document.getElementById('childSearch').addEventListener('keyup', function() {
    let filter = this.value.toLowerCase();
    let rows = document.querySelectorAll('#childrenTable tbody tr');
    
    rows.forEach(row => {
        let text = row.innerText.toLowerCase();
        row.style.display = text.includes(filter) ? '' : 'none';
    });
});
</script>

</body>
</html>