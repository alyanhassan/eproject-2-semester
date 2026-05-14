<?php
session_start();
include('../config/db.php');

if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header("Location: ../auth/login.php");
    exit();
}

// safe ID
if(!isset($_GET['id']) || !is_numeric($_GET['id'])){
    header("Location: manage_hospitals.php");
    exit();
}

$id = (int) $_GET['id'];

// fetch hospital
$res = mysqli_query($conn, "SELECT * FROM hospitals WHERE id=$id");
$hospital = mysqli_fetch_assoc($res);

if(!$hospital){
    header("Location: manage_hospitals.php");
    exit();
}

// update
if(isset($_POST['update'])){

    $name = $_POST['name'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $pincode = $_POST['pincode'];
    $contact = $_POST['contact'];
    $email = $_POST['email'];

    mysqli_query($conn, "
        UPDATE hospitals 
        SET name='$name',
            address='$address',
            city='$city',
            pincode='$pincode',
            contact='$contact',
            email='$email'
        WHERE id=$id
    ");

    header("Location: manage_hospitals.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Hospital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../admin.css">
</head>
<body>

<div class="container mt-5">

    <div class="col-md-6 mx-auto">

        <div class="card p-4 shadow">

            <h4 class="mb-3">✏️ Edit Hospital</h4>

            <form method="POST">

                <input type="text" name="name" class="form-control mb-2"
                       value="<?php echo $hospital['name']; ?>" required>

                <textarea name="address" class="form-control mb-2" rows="2" required><?php echo $hospital['address']; ?></textarea>

                <input type="text" name="city" class="form-control mb-2"
                       value="<?php echo $hospital['city']; ?>" required>

                <input type="text" name="pincode" class="form-control mb-2"
                       value="<?php echo $hospital['pincode']; ?>">

                <input type="text" name="contact" class="form-control mb-2"
                       value="<?php echo $hospital['contact']; ?>" required>

                <input type="email" name="email" class="form-control mb-2"
                       value="<?php echo $hospital['email']; ?>">

                <button type="submit" name="update" class="btn btn-primary w-100">
                    Update Hospital
                </button>

                <a href="manage_hospitals.php" class="btn btn-secondary w-100 mt-2">
                    Cancel
                </a>

            </form>

        </div>

    </div>

</div>

</body>
</html>