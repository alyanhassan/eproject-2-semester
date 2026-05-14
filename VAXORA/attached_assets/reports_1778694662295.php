<?php
session_start();
include('../config/db.php');

if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header("Location: ../auth/login.php");
    exit();
}

/* ================= REAL DATA ================= */
$parents = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role='parent'"))['total'];
$children = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM children"))['total'];
$hospitals = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM hospitals"))['total'];
$vaccines = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM vaccines"))['total'];
$appointments = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM appointments"))['total'];
$completed = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM appointments WHERE status='completed'"))['total'];

/* ================= FAKE DEMO DATA ================= */
$fake_success_rate = 87;
$fake_coverage = 72;
$fake_monthly_growth = 15;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reports</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../admin.css">
</head>
<body>

<div class="container mt-4">

    <h3>📊 System Reports Dashboard</h3>

    <!-- REAL STATS -->
    <div class="row mt-4">

        <div class="col-md-3">
            <div class="card p-3 bg-primary text-white">
                <h5>Parents</h5>
                <h3><?php echo $parents; ?></h3>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card p-3 bg-success text-white">
                <h5>Children</h5>
                <h3><?php echo $children; ?></h3>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card p-3 bg-info text-white">
                <h5>Hospitals</h5>
                <h3><?php echo $hospitals; ?></h3>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card p-3 bg-warning text-dark">
                <h5>Vaccines</h5>
                <h3><?php echo $vaccines; ?></h3>
            </div>
        </div>

    </div>

    <!-- SECOND ROW -->
    <div class="row mt-3">

        <div class="col-md-6">
            <div class="card p-3 bg-dark text-white">
                <h5>Total Appointments</h5>
                <h3><?php echo $appointments; ?></h3>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card p-3 bg-success text-white">
                <h5>Completed Vaccinations</h5>
                <h3><?php echo $completed; ?></h3>
            </div>
        </div>

    </div>

    <!-- FAKE ANALYTICS -->
    <div class="row mt-4">

        <div class="col-md-4">
            <div class="card p-3 shadow">
                <h5>📈 Success Rate</h5>
                <h2><?php echo $fake_success_rate; ?>%</h2>
                <small>Fake analytics data</small>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card p-3 shadow">
                <h5>🧒 Vaccination Coverage</h5>
                <h2><?php echo $fake_coverage; ?>%</h2>
                <small>Estimated coverage</small>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card p-3 shadow">
                <h5>📊 Monthly Growth</h5>
                <h2><?php echo $fake_monthly_growth; ?>%</h2>
                <small>System growth rate</small>
            </div>
        </div>

    </div>

    <!-- SUMMARY -->
    <div class="card mt-4 p-3">
        <h5>📄 Summary Report</h5>
        <p>
            System currently manages <b><?php echo $children; ?></b> children across 
            <b><?php echo $hospitals; ?></b> hospitals with 
            <b><?php echo $vaccines; ?></b> vaccines available. 
            Total vaccination appointments processed: 
            <b><?php echo $appointments; ?></b>.
        </p>
    </div>

</div>

</body>
</html>