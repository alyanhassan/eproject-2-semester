<?php
require_once '../includes/auth_check.php';
checkRole(['admin']);
require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();
$id = (int)$_GET['id'];

// 1. Fetch current data
$stmt = $db->prepare("SELECT * FROM hospitals WHERE hospital_id = :id");
$stmt->execute([':id' => $id]);
$h = $stmt->fetch(PDO::FETCH_ASSOC);

// 2. Update logic
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $sql = "UPDATE hospitals SET 
            hospital_name = :name, address = :addr, city = :city, 
            pincode = :pin, phone = :phone, email = :email 
            WHERE hospital_id = :id";
    $upd = $db->prepare($sql);
    $upd->execute([
        ':name' => $_POST['name'], ':addr' => $_POST['address'],
        ':city' => $_POST['city'], ':pin' => $_POST['pincode'],
        ':phone' => $_POST['contact'], ':email' => $_POST['email'],
        ':id' => $id
    ]);
    header("Location: manage_hospitals.php?updated=1");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Hospital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="card shadow col-md-6 mx-auto">
        <div class="card-body">
            <h4 class="mb-4">✏️ Edit Hospital Profile</h4>
            <form method="POST">
                <label>Hospital Name</label>
                <input type="text" name="name" class="form-control mb-3" value="<?= htmlspecialchars($h['hospital_name']) ?>" required>
                
                <label>Address</label>
                <textarea name="address" class="form-control mb-3" required><?= htmlspecialchars($h['address']) ?></textarea>
                
                <div class="row">
                    <div class="col-md-6"><label>City</label><input type="text" name="city" class="form-control mb-3" value="<?= htmlspecialchars($h['city']) ?>"></div>
                    <div class="col-md-6"><label>Pincode</label><input type="text" name="pincode" class="form-control mb-3" value="<?= htmlspecialchars($h['pincode']) ?>"></div>
                </div>

                <label>Contact Number</label>
                <input type="text" name="contact" class="form-control mb-3" value="<?= htmlspecialchars($h['phone']) ?>">

                <label>Email</label>
                <input type="email" name="email" class="form-control mb-4" value="<?= htmlspecialchars($h['email']) ?>">

                <button type="submit" class="btn btn-primary w-100">Save Changes</button>
                <a href="manage_hospitals.php" class="btn btn-link w-100 mt-2 text-muted">Cancel</a>
            </form>
        </div>
    </div>
</div>
</body>
</html>