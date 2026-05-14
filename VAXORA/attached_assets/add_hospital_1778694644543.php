<?php
require_once '../includes/auth_check.php';
// Assuming only admins can add hospitals
checkRole(['admin']); 

require_once '../config/config.php';
require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$success = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Get data from form
    $hospital_name = trim($_POST['name']);
    $address = trim($_POST['address']);
    $city = trim($_POST['city']);
    $phone = trim($_POST['phone']);

    if (!empty($hospital_name) && !empty($address) && !empty($city)) {
        try {
            $query = "INSERT INTO hospitals (hospital_name, address, city, phone, status, created_at)
                      VALUES (:name, :address, :city, :phone, 'active', NOW())";

            $stmt = $db->prepare($query);
            $stmt->bindParam(':name', $hospital_name);
            $stmt->bindParam(':address', $address);
            $stmt->bindParam(':city', $city);
            $stmt->bindParam(':phone', $phone);

            if ($stmt->execute()) {
                $success = "Hospital '$hospital_name' has been added successfully!";
            } else {
                $error = "Unable to add hospital. Please check your data.";
            }
        } catch (PDOException $e) {
            error_log("Add Hospital Error: " . $e->getMessage());
            $error = "Database error: " . $e->getMessage();
        }
    } else {
        $error = "Please fill in all required fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Add Hospital</title>
    <!-- Using the same Bootstrap and FontAwesome as your other pages -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/dist/css/all.min.css">
    <style>
        body { background-color: #f8f9fc; }
        .card { border: none; border-radius: 15px; }
        .btn-success { background-color: #1cc88a; border: none; }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            
            <div class="text-center mb-4">
                <h3 class="fw-bold text-primary">VaxTrack Admin</h3>
                <p class="text-muted">Register a new healthcare partner</p>
            </div>

            <div class="card shadow-sm">
                <div class="card-body p-4">
                    
                    <?php if($success): ?>
                        <div class="alert alert-success border-0 small"><?= $success ?></div>
                    <?php endif; ?>

                    <?php if($error): ?>
                        <div class="alert alert-danger border-0 small"><?= $error ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Hospital Name</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="fas fa-hospital text-muted"></i></span>
                                <input type="text" name="name" class="form-control border-start-0" placeholder="e.g. City General" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Street Address</label>
                            <input type="text" name="address" class="form-control" placeholder="123 Medical Way" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold">City</label>
                                <input type="text" name="city" class="form-control" placeholder="Karachi" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold">Phone</label>
                                <input type="text" name="phone" class="form-control" placeholder="021-XXXXXXX" required>
                            </div>
                        </div>

                        <div class="d-grid mt-3">
                            <button type="submit" class="btn btn-success btn-lg shadow-sm">
                                <i class="fas fa-plus-circle me-1"></i> Register Hospital
                            </button>
                        </div>
                        
                        <div class="text-center mt-3">
                            <a href="dashboard.php" class="text-decoration-none small text-muted">
                                <i class="fas fa-arrow-left"></i> Back to Admin Dashboard
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>