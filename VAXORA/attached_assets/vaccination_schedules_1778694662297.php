<?php
session_start();
include('../config/db.php');

if(!isset($_SESSION['role'])){
    header("Location: ../auth/login.php");
    exit();
}

/* ================= FETCH SCHEDULE ================= */
$query = "
SELECT 
    c.name AS child_name,
    v.name AS vaccine_name,
    s.scheduled_date,
    s.status,
    h.name AS hospital_name
FROM vaccination_schedule s
JOIN children c ON s.child_id = c.id
JOIN vaccines v ON s.vaccine_id = v.id
JOIN hospitals h ON s.hospital_id = h.id
ORDER BY s.scheduled_date ASC
";

$res = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Vaccination Schedule</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../admin.css">
</head>
<body>

<div class="container mt-4">

    <h3>📅 Vaccination Schedule</h3>

    <div class="card p-3 mt-3 shadow">

        <table class="table table-bordered">

            <thead class="table-dark">
                <tr>
                    <th>Child</th>
                    <th>Vaccine</th>
                    <th>Hospital</th>
                    <th>Scheduled Date</th>
                    <th>Status</th>
                </tr>
            </thead>

            <tbody>

            <?php if(mysqli_num_rows($res) > 0){ ?>

                <?php while($row = mysqli_fetch_assoc($res)){ ?>

                    <tr>
                        <td><?php echo $row['child_name']; ?></td>
                        <td><?php echo $row['vaccine_name']; ?></td>
                        <td><?php echo $row['hospital_name']; ?></td>
                        <td><?php echo $row['scheduled_date']; ?></td>

                        <td>
                            <?php if($row['status'] == 'pending'){ ?>
                                <span class="badge bg-warning">Pending</span>
                            <?php } else { ?>
                                <span class="badge bg-success">Completed</span>
                            <?php } ?>
                        </td>
                    </tr>

                <?php } ?>

            <?php } else { ?>

                <tr>
                    <td colspan="5" class="text-center">No schedule found</td>
                </tr>

            <?php } ?>

            </tbody>

        </table>

    </div>

</div>

</body>
</html>