<?php
session_start();
include('../config/db.php');

if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header("Location: ../auth/login.php");
    exit();
}

/* ================= FETCH PARENTS ================= */
$res = mysqli_query($conn, "SELECT * FROM users WHERE role='parent' ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Parent Records</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../admin.css">
</head>
<body>

<div class="container mt-4">

    <h3>👨‍👩‍👧‍👦 Parent Records</h3>

    <div class="card p-3 shadow mt-3">

        <table class="table table-bordered">

            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>

            <?php while($row = mysqli_fetch_assoc($res)) { ?>

                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['email']; ?></td>

                    <td>
                        <?php if($row['status'] == 'active'){ ?>
                            <span class="badge bg-success">Active</span>
                        <?php } else { ?>
                            <span class="badge bg-secondary">Inactive</span>
                        <?php } ?>
                    </td>

                    <td>

                        <!-- STATUS TOGGLE -->
                        <a href="toggle_parent.php?id=<?php echo $row['id']; ?>" 
                           class="btn btn-sm btn-warning">
                           Toggle
                        </a>

                        <!-- DELETE -->
                        <a href="delete_parent.php?id=<?php echo $row['id']; ?>" 
                           class="btn btn-sm btn-danger"
                           onclick="return confirm('Delete this parent?');">
                           Delete
                        </a>

                    </td>

                </tr>

            <?php } ?>

            </tbody>

        </table>

    </div>

</div>

</body>
</html>