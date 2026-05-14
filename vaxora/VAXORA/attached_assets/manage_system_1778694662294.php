<?php
session_start();
include('../config/db.php');

if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header("Location: ../auth/login.php");
    exit();
}

/* ================= ADD HOSPITAL ================= */
if(isset($_POST['add_hospital'])) {

    $name = $_POST['name'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $pincode = $_POST['pincode'];
    $contact = $_POST['contact'];
    $email = $_POST['email'];

    $query = "INSERT INTO hospitals (name, address, city, pincode, contact, email)
              VALUES ('$name', '$address', '$city', '$pincode', '$contact', '$email')";

    mysqli_query($conn, $query);

    echo "<script>alert('Hospital added successfully');</script>";
}

/* ================= DELETE HOSPITAL ================= */
if(isset($_GET['delete']) && is_numeric($_GET['delete'])) {

    $id = (int) $_GET['delete'];

    mysqli_query($conn, "DELETE FROM hospitals WHERE id=$id");

    header("Location: manage_hospitals.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Hospitals</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../admin.css">
</head>
<body>

<div class="container mt-4">

    <div class="row">

        <!-- ADD HOSPITAL -->
        <div class="col-md-4">

            <div class="card p-3 shadow">

                <h5>🏥 Add Hospital</h5>

                <form method="POST">

                    <input type="text" name="name" class="form-control mb-2" placeholder="Hospital Name" required>

                    <textarea name="address" class="form-control mb-2" placeholder="Address" required></textarea>

                    <input type="text" name="city" class="form-control mb-2" placeholder="City" required>

                    <input type="text" name="pincode" class="form-control mb-2" placeholder="Pincode">

                    <input type="text" name="contact" class="form-control mb-2" placeholder="Contact" required>

                    <input type="email" name="email" class="form-control mb-2" placeholder="Email">

                    <button type="submit" name="add_hospital" class="btn btn-primary w-100">
                        Add Hospital
                    </button>

                </form>

            </div>

        </div>

        <!-- LIST -->
        <div class="col-md-8">

            <div class="card p-3 shadow">

                <h5>📋 Hospital List</h5>

                <table class="table table-bordered mt-2">

                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>City</th>
                            <th>Contact</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php
                    $res = mysqli_query($conn, "SELECT * FROM hospitals ORDER BY id DESC");

                    while($row = mysqli_fetch_assoc($res)){
                    ?>

                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo $row['name']; ?></td>
                            <td><?php echo $row['city']; ?></td>
                            <td><?php echo $row['contact']; ?></td>

                            <td>

                                <a href="edit_hospital.php?id=<?php echo $row['id']; ?>" 
                                   class="btn btn-warning btn-sm">
                                   Edit
                                </a>

                                <a href="manage_hospitals.php?delete=<?php echo $row['id']; ?>" 
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('Delete this hospital?');">
                                   Delete
                                </a>

                            </td>
                        </tr>

                    <?php } ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

</body>
</html>