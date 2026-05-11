<?php

$host = "localhost";
$user = "root";
$password = "";
$database = "hospital_db";

/* DATABASE CONNECTION */
$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

/* SELECT QUERY */
$sql = "SELECT * FROM hospitals";

$result = mysqli_query($conn, $sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hospital Records</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container py-5">

    <div class="card shadow">

        <div class="card-header bg-primary text-white">
            <h3 class="text-center">Hospital Records</h3>
        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-bordered table-striped">

                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Hospital Name</th>
                            <th>Registration No</th>
                            <th>Address</th>
                            <th>City</th>
                            <th>State</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Hospital Type</th>
                            <th>Beds</th>
                            <th>License File</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php

                    if (mysqli_num_rows($result) > 0) {

                        while ($row = mysqli_fetch_assoc($result)) {

                    ?>

                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo $row['hospital_name']; ?></td>
                            <td><?php echo $row['registration_number']; ?></td>
                            <td><?php echo $row['address']; ?></td>
                            <td><?php echo $row['city']; ?></td>
                            <td><?php echo $row['state']; ?></td>
                            <td><?php echo $row['phone']; ?></td>
                            <td><?php echo $row['email']; ?></td>
                            <td><?php echo $row['hospital_type']; ?></td>
                            <td><?php echo $row['beds']; ?></td>

                            <td>
                                <a 
                                    href="uploads/<?php echo $row['license_file']; ?>" 
                                    target="_blank"
                                    class="btn btn-success btn-sm"
                                >
                                    View
                                </a>
                            </td>
                        </tr>

                    <?php

                        }

                    } else {

                    ?>

                        <tr>
                            <td colspan="11" class="text-center text-danger">
                                No Records Found
                            </td>
                        </tr>

                    <?php
                    }

                    ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

</body>
</html>