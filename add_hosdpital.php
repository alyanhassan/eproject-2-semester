<?php

$host = "localhost";
$user = "root";
$password = "";
$database = "hospital_db";

$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

/* INSERT DATA */
if (isset($_POST['register'])) {

    $hospital_name = $_POST['hospital_name'];
    $registration_number = $_POST['registration_number'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $hospital_type = $_POST['hospital_type'];
    $beds = $_POST['beds'];

    /* FILE UPLOAD */
    $license_file = "";

    if (isset($_FILES['license_file']) && $_FILES['license_file']['error'] == 0) {

        $upload_dir = "uploads/";

        // create folder if not exists
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $license_file = time() . "_" . basename($_FILES["license_file"]["name"]);
        $target_file = $upload_dir . $license_file;

        move_uploaded_file($_FILES["license_file"]["tmp_name"], $target_file);
    }

    /* INSERT QUERY */
    $sql = "INSERT INTO hospitals 
    (hospital_name, registration_number, address, city, state, phone, email, hospital_type, beds, license_file)
    
    VALUES 
    
    ('$hospital_name', '$registration_number', '$address', '$city', '$state', '$phone', '$email', '$hospital_type', '$beds', '$license_file')";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Hospital Registered Successfully');</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Hospital Registration Form</title>

  <!-- Bootstrap CSS -->
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet"
  />
</head>

<body class="bg-light">

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-8">

      <div class="card shadow-lg border-0">

        <div class="card-header bg-primary text-white text-center">
          <h3>Hospital Registration Form</h3>
        </div>

        <div class="card-body">

          <form method="POST" enctype="multipart/form-data">

            <div class="row mb-3">

              <div class="col-md-6">
                <label class="form-label">Hospital Name</label>
                <input 
                  type="text" 
                  name="hospital_name"
                  class="form-control" 
                  placeholder="Enter hospital name"
                  required
                >
              </div>

              <div class="col-md-6">
                <label class="form-label">Registration Number</label>
                <input 
                  type="text" 
                  name="registration_number"
                  class="form-control" 
                  placeholder="Enter registration number"
                  required
                >
              </div>

            </div>

            <div class="mb-3">
              <label class="form-label">Address</label>
              <textarea 
                name="address"
                class="form-control" 
                rows="3" 
                placeholder="Enter hospital address"
                required
              ></textarea>
            </div>

            <div class="row mb-3">

              <div class="col-md-6">
                <label class="form-label">City</label>
                <input 
                  type="text" 
                  name="city"
                  class="form-control" 
                  placeholder="Enter city"
                  required
                >
              </div>

              <div class="col-md-6">
                <label class="form-label">State</label>
                <input 
                  type="text" 
                  name="state"
                  class="form-control" 
                  placeholder="Enter state"
                  required
                >
              </div>

            </div>

            <div class="row mb-3">

              <div class="col-md-6">
                <label class="form-label">Phone Number</label>
                <input 
                  type="tel" 
                  name="phone"
                  class="form-control" 
                  placeholder="Enter phone number"
                  required
                >
              </div>

              <div class="col-md-6">
                <label class="form-label">Email</label>
                <input 
                  type="email" 
                  name="email"
                  class="form-control" 
                  placeholder="Enter email"
                  required
                >
              </div>

            </div>

            <div class="row mb-3">

              <div class="col-md-6">
                <label class="form-label">Hospital Type</label>

                <select 
                  name="hospital_type"
                  class="form-select"
                  required
                >
                  <option value="">Select Type</option>
                  <option>General</option>
                  <option>Specialized</option>
                  <option>Clinic</option>
                  <option>Emergency</option>
                </select>

              </div>

              <div class="col-md-6">
                <label class="form-label">Number of Beds</label>
                <input 
                  type="number" 
                  name="beds"
                  class="form-control" 
                  placeholder="Enter number of beds"
                  required
                >
              </div>

            </div>

            <div class="mb-3">
              <label class="form-label">Upload License</label>
              <input 
                type="file" 
                name="license_file"
                class="form-control"
                required
              >
            </div>

            <div class="form-check mb-4">
              <input class="form-check-input" type="checkbox" required>
              <label class="form-check-label">
                I agree to the terms and conditions
              </label>
            </div>

            <div class="text-center">
              <button 
                type="submit" 
                name="register"
                class="btn btn-primary px-5"
              >
                Register Hospital
              </button>
            </div>

          </form>

        </div>
      </div>

    </div>
  </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>