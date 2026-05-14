<?php
require_once '../includes/auth_check.php';
checkRole(['parent']);

require_once '../config/config.php';
require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$user_id = (int)$_SESSION['user_id'];
$success_msg = "";
$error_msg = "";

/* ==============================================
   1. HANDLE CHILD REGISTRATION (POST)
============================================== */
if (isset($_POST['add_child'])) {
    $name = trim($_POST['child_name']);
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $blood = $_POST['blood_group'];
    // Generate a Unique ID like VAX-2024-XXXX
    $unique_id = "VAX-" . date('Y') . "-" . strtoupper(substr(md5(uniqid()), 0, 6));

    try {
        $insert = "INSERT INTO children (parent_id, full_name, dob, gender, blood_group, unique_reg_id, created_at) 
                   VALUES ((SELECT parent_id FROM parents WHERE user_id = :u_id), :name, :dob, :gender, :blood, :reg_id, NOW())";
        
        $stmt = $db->prepare($insert);
        $stmt->execute([
            ':u_id' => $user_id,
            ':name' => $name,
            ':dob'  => $dob,
            ':gender' => $gender,
            ':blood' => $blood,
            ':reg_id' => $unique_id
        ]);
        $success_msg = "Child registered successfully! ID: $unique_id";
    } catch (PDOException $e) {
        $error_msg = "Error registering child: " . $e->getMessage();
    }
}

/* ==============================================
   2. FETCH CHILDREN DATA
============================================== */
$child_query = "SELECT * FROM children WHERE parent_id = (SELECT parent_id FROM parents WHERE user_id = :u_id)";
$child_stmt = $db->prepare($child_query);
$child_stmt->execute([':u_id' => $user_id]);
$children = $child_stmt->fetchAll(PDO::FETCH_ASSOC);

/* ==============================================
   3. FETCH APPOINTMENTS
============================================== */
$appt_query = "SELECT a.*, c.full_name as child_name, v.vaccine_name, h.hospital_name 
               FROM appointments a 
               JOIN children c ON a.child_id = c.child_id
               JOIN vaccines v ON a.vaccine_id = v.vaccine_id
               JOIN hospitals h ON a.hospital_id = h.hospital_id
               WHERE c.parent_id = (SELECT parent_id FROM parents WHERE user_id = :u_id)
               ORDER BY a.appointment_date DESC";
$appt_stmt = $db->prepare($appt_query);
$appt_stmt->execute([':u_id' => $user_id]);
$appointments = $appt_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Parent Dashboard | VaxTrack</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f7f6; }
        .navbar { background: #4e73df; color: white; }
        .dashboard-card { background: white; padding: 20px; border-radius: 15px; border: none; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .btn-premium { background: #4e73df; color: white; border-radius: 10px; transition: 0.3s; }
        .btn-premium:hover { background: #224abe; color: white; }
        .table-premium thead { background: #f8f9fc; color: #4e73df; }
    </style>
</head>
<body>

<nav class="navbar py-3 shadow-sm mb-4">
    <div class="container">
        <span class="navbar-brand fw-bold"><i class="fas fa-hand-holding-heart me-2"></i> Parent Portal</span>
        <div class="d-flex align-items-center">
            <span class="me-3 d-none d-md-inline">Welcome, <strong><?= htmlspecialchars($_SESSION['name']); ?></strong></span>
            <a href="../auth/logout.php" class="btn btn-outline-light btn-sm">Logout</a>
        </div>
    </div>
</nav>

<div class="container">
    <?php if($success_msg): ?>
        <div class="alert alert-success border-0 shadow-sm"><?= $success_msg ?></div>
    <?php endif; ?>

    <div class="row g-4">
        <!-- Sidebar: Registration & Booking -->
        <div class="col-lg-4">
            <div class="dashboard-card mb-4">
                <h5 class="fw-bold mb-3"><i class="fas fa-plus-circle text-primary me-2"></i>Register Child</h5>
                <form method="POST">
                    <input type="text" name="child_name" class="form-control mb-2" placeholder="Full Name" required>
                    <input type="date" name="dob" class="form-control mb-2" required>
                    <div class="row g-2 mb-2">
                        <div class="col"><select name="gender" class="form-select"><option>Male</option><option>Female</option></select></div>
                        <div class="col"><select name="blood_group" class="form-select">
                            <option>A+</option><option>B+</option><option>O+</option><option>AB+</option>
                            <option>A-</option><option>B-</option><option>O-</option><option>AB-</option>
                        </select></div>
                    </div>
                    <button type="submit" name="add_child" class="btn btn-premium w-100">Add to Profile</button>
                </form>
            </div>
            
            <div class="dashboard-card">
                <h5 class="fw-bold mb-3"><i class="fas fa-calendar-plus text-success me-2"></i>Book Vaccine</h5>
                <form action="book_appointment.php" method="GET">
                    <select name="child_id" class="form-select mb-3" required>
                        <option value="">Select Child</option>
                        <?php foreach($children as $child): ?>
                            <option value="<?= $child['child_id'] ?>"><?= htmlspecialchars($child['full_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btn btn-success w-100 shadow-sm">Start Booking</button>
                </form>
            </div>
        </div>
        
        <!-- Main Content: Lists -->
        <div class="col-lg-8">
            <div class="dashboard-card mb-4">
                <h5 class="fw-bold mb-3"><i class="fas fa-user-friends text-info me-2"></i>Children Profiles</h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light"><tr><th>Name</th><th>DOB</th><th>Unique ID</th></tr></thead>
                        <tbody>
                            <?php foreach($children as $row): ?>
                                <tr>
                                    <td class="fw-bold"><?= htmlspecialchars($row['full_name']) ?></td>
                                    <td><?= date('M d, Y', strtotime($row['dob'])) ?></td>
                                    <td><span class="badge bg-light text-dark border"><?= $row['unique_reg_id'] ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="dashboard-card">
                <h5 class="fw-bold mb-3"><i class="fas fa-clock text-warning me-2"></i>Appointments Status</h5>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead><tr><th>Child</th><th>Vaccine</th><th>Date</th><th>Status</th></tr></thead>
                        <tbody>
                            <?php foreach($appointments as $row): ?>
                                <?php $badge = ($row['status']=='pending') ? 'warning' : (($row['status']=='completed') ? 'success' : 'info'); ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['child_name']) ?></td>
                                    <td><?= htmlspecialchars($row['vaccine_name']) ?></td>
                                    <td><?= date('M d, Y', strtotime($row['appointment_date'])) ?></td>
                                    <td><span class="badge bg-<?= $badge ?> text-uppercase"><?= $row['status'] ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>