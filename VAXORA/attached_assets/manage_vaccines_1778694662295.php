<?php
session_start();
include('../config/db.php');

if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header("Location: ../auth/login.php");
    exit();
}

/* ================= ADD VACCINE ================= */
if(isset($_POST['add_vaccine'])) {

    $name = $_POST['name'];
    $description = $_POST['description'];
    $age = $_POST['age'];

    mysqli_query($conn, "INSERT INTO vaccines (name, description, recommended_age)
                          VALUES ('$name', '$description', '$age')");

    echo "<script>alert('Vaccine added successfully');</script>";
}

/* ================= DELETE VACCINE ================= */
if(isset($_GET['delete']) && is_numeric($_GET['delete'])) {

    $id = (int) $_GET['delete'];

    mysqli_query($conn, "DELETE FROM vaccines WHERE id=$id");

    header("Location: manage_vaccines.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Vaccines</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../admin.css">
</head>
<body>

<div class="container mt-4">

    <div class="row">

        <!-- ADD VACCINE -->
        <div class="col-md-4">

            <div class="card p-3 shadow">

                <h5>💉 Add Vaccine</h5>

                <form method="POST">

                    <input type="text" name="name" class="form-control mb-2" placeholder="Vaccine Name" required>

                    <textarea name="description" class="form-control mb-2" placeholder="Description"></textarea>

                    <input type="text" name="age" class="form-control mb-2" placeholder="Recommended Age">

                    <button type="submit" name="add_vaccine" class="btn btn-primary w-100">
                        Add Vaccine
                    </button>

                </form>

            </div>

        </div>

        <!-- LIST VACCINES -->
        <div class="col-md-8">

            <div class="card p-3 shadow">

                <h5>📋 Vaccine List</h5>

                <table class="table table-bordered mt-2">

                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Age</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php
                    $res = mysqli_query($conn, "SELECT * FROM vaccines ORDER BY id DESC");

                    while($row = mysqli_fetch_assoc($res)){
                    ?>

                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo $row['name']; ?></td>
                            <td><?php echo $row['recommended_age']; ?></td>

                            <td>

                                <a href="edit_vaccine.php?id=<?php echo $row['id']; ?>" 
                                   class="btn btn-warning btn-sm">
                                   Edit
                                </a>

                                <a href="manage_vaccines.php?delete=<?php echo $row['id']; ?>" 
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('Delete this vaccine?');">
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