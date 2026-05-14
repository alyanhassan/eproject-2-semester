<?php
session_start();
include('../config/db.php');

if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header("Location: ../auth/login.php");
    exit();
}

// fetch inquiries
$res = mysqli_query($conn, "SELECT * FROM inquiries ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Inquiries</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../admin.css">
</head>
<body>

<div class="container mt-4">

    <h3>📩 User Inquiries</h3>

    <table class="table table-bordered table-striped mt-3">

        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Subject</th>
                <th>Message</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>

        <tbody>

        <?php if(mysqli_num_rows($res) > 0){ ?>

            <?php while($row = mysqli_fetch_assoc($res)){ ?>

            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['name']; ?></td>
                <td><?php echo $row['email']; ?></td>
                <td><?php echo $row['subject']; ?></td>
                <td><?php echo substr($row['message'],0,50); ?>...</td>

                <td>
                    <?php if($row['status'] == 'pending'){ ?>
                        <span class="badge bg-warning">Pending</span>
                    <?php } else { ?>
                        <span class="badge bg-success">Replied</span>
                    <?php } ?>
                </td>

                <td>

                    <?php if($row['status'] == 'pending'){ ?>
                        <a href="view_inquiry.php?id=<?php echo $row['id']; ?>" 
                           class="btn btn-primary btn-sm">
                           Respond
                        </a>
                    <?php } else { ?>
                        <span class="text-success">Done</span>
                    <?php } ?>

                </td>

            </tr>

            <?php } ?>

        <?php } else { ?>

            <tr>
                <td colspan="7" class="text-center">No inquiries found</td>
            </tr>

        <?php } ?>

        </tbody>

    </table>

</div>

</body>
</html>