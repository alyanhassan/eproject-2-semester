<?php
require_once '../includes/auth_check.php';
checkRole(['admin']); 

require_once '../config/config.php';
require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$success = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $vaccine_name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $doses_required = (int)$_POST['doses_required'];
    $age_group = trim($_POST['age_group']);

    if (!empty($vaccine_name) && !empty($age_group)) {
        try {
            $query = "INSERT INTO vaccines (vaccine_name, description, doses_required, age_group, status, created_at)
                      VALUES (:name, :description, :doses, :age, 'active', NOW())";

            $stmt = $db->prepare($query);
            $stmt->bindParam(':name', $vaccine_name);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':doses', $doses_required, PDO::PARAM_INT);
            $stmt->bindParam(':age', $age_group);

            if ($stmt->execute()) {
                $success = "Vaccine '$vaccine_name' added to the system!";
            } else {
                $error = "Failed to add vaccine record.";
            }
        } catch (PDOException $e) {
            error_log("Add Vaccine Error: " . $e->getMessage());
            $error = "Database Error: " . $e->getMessage();
        }
    } else {
        $error = "Vaccine name and age group are required.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Add Vaccine</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/dist/css/all.min.css">
    <style>
        body { background-color: #f8f9fc; }
        .card { border: none; border-radius: 12px; }
        .form-label { font-weight: 600; color: #4e73df; font-size: 0.9rem; }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            
            <div class="text-center mb-4">
                <i class="fas fa-syringe fa-3x text-primary mb-3"></i>
                <h3 class="fw-bold">Manage Vaccines</h3>
                <p class="text-muted">Define new immunization types and schedules</p>
            </div>

            <div class="card shadow">
                <div class="card-body p-4">
                    
                    <?php if($success): ?>
                        <div class="alert alert-success alert-dismissible fade show border-0 small">
                            <i class="fas fa-check-circle me-1"></i> <?= $success ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if($error): ?>
                        <div class="alert alert-danger border-0 small"><?= $error ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Vaccine Name</label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Polio, COVID-19" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Doses Required</label>
                            <input type="number" name="doses_required" class="form-control" placeholder="1" min="1" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Target Age Group</label>
                            <input type="text" name="age_group" class="form-control" placeholder="e.g. 0-12 months" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Short Description</label>
                            <textarea name="description" class="form-control" rows="3" placeholder="Explain the vaccine purpose..."></textarea>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                Save Vaccine
                            </button>
                            <a href="manage_vaccines.php" class="btn btn-outline-secondary">
                                View All Vaccines
                            </a>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="text-center mt-4">
                <a href="dashboard.php" class="text-secondary text-decoration-none small">
                    <i class="fas fa-tachometer-alt me-1"></i> Admin Dashboard
                </a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>