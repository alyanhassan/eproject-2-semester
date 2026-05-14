<?php
require_once '../includes/auth_check.php';
checkRole(['admin']);
require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

// Fetch Counts
$stats = [
    'children' => $db->query("SELECT COUNT(*) FROM children")->fetchColumn(),
    'hospitals' => $db->query("SELECT COUNT(*) FROM hospitals")->fetchColumn(),
    'pending' => $db->query("SELECT COUNT(*) FROM appointments WHERE status = 'pending'")->fetchColumn()
];

include '../includes/header.php';
?>
<div class="container mt-4">
    <div class="row">
        <div class="col-md-4">
            <div class="card bg-primary text-white p-3 shadow border-0">
                <h5>Registered Children</h5>
                <h3><?= $stats['children'] ?></h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white p-3 shadow border-0">
                <h5>Partner Hospitals</h5>
                <h3><?= $stats['hospitals'] ?></h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-warning text-dark p-3 shadow border-0">
                <h5>Pending Appointments</h5>
                <h3><?= $stats['pending'] ?></h3>
            </div>
        </div>
    </div>

    <div class="mt-5">
        <h4>Recent Activity</h4>
        <table class="table table-hover bg-white shadow-sm">
            <thead>
                <tr>
                    <th>Child</th>
                    <th>Hospital</th>
                    <th>Vaccine</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $stmt = $db->query("SELECT a.*, c.full_name, h.hospital_name, v.vaccine_name 
                                   FROM appointments a 
                                   JOIN children c ON a.child_id = c.child_id 
                                   JOIN hospitals h ON a.hospital_id = h.hospital_id 
                                   JOIN vaccines v ON a.vaccine_id = v.vaccine_id 
                                   ORDER BY a.appointment_date DESC LIMIT 5");
                while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<tr>
                            <td>{$row['full_name']}</td>
                            <td>{$row['hospital_name']}</td>
                            <td>{$row['vaccine_name']}</td>
                            <td><span class='badge bg-secondary'>{$row['status']}</span></td>
                          </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>