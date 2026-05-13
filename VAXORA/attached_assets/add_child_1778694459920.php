<?php
require_once 'includes/auth_check.php';
checkRole(['parent']);

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';

$database = new Database();
$db = $database->getConnection();

$error = '';
$success = '';

// Get parent info safely
$parent_id = null;

$parentQuery = "SELECT parent_id FROM parents WHERE user_id = :user_id";
$parentStmt = $db->prepare($parentQuery);
$parentStmt->bindParam(':user_id', $_SESSION['user_id']);
$parentStmt->execute();
$parent = $parentStmt->fetch(PDO::FETCH_ASSOC);

if ($parent) {
    $parent_id = $parent['parent_id'];
} else {
    die("Parent record not found.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $full_name = sanitizeInput($_POST['full_name']);
    $date_of_birth = sanitizeInput($_POST['date_of_birth']);
    $gender = sanitizeInput($_POST['gender']);
    $birth_weight = sanitizeInput($_POST['birth_weight']);
    $blood_group = sanitizeInput($_POST['blood_group']);
    $allergies = sanitizeInput($_POST['allergies']);
    $medical_conditions = sanitizeInput($_POST['medical_conditions']);

    // Better age calculation
    $dob = new DateTime($date_of_birth);
    $today = new DateTime();
    $age = $today->diff($dob)->y;

    if ($age > 18) {
        $error = "Child must be under 18 years of age.";
    } else {

        try {
            $unique_child_id = generateUniqueId('CHD');

            $query = "INSERT INTO children 
            (parent_id, unique_child_id, full_name, date_of_birth, gender, birth_weight, blood_group, allergies, medical_conditions)
            VALUES 
            (:parent_id, :unique_child_id, :full_name, :date_of_birth, :gender, :birth_weight, :blood_group, :allergies, :medical_conditions)";

            $stmt = $db->prepare($query);

            $stmt->bindParam(':parent_id', $parent_id);
            $stmt->bindParam(':unique_child_id', $unique_child_id);
            $stmt->bindParam(':full_name', $full_name);
            $stmt->bindParam(':date_of_birth', $date_of_birth);
            $stmt->bindParam(':gender', $gender);
            $stmt->bindParam(':birth_weight', $birth_weight);
            $stmt->bindParam(':blood_group', $blood_group);
            $stmt->bindParam(':allergies', $allergies);
            $stmt->bindParam(':medical_conditions', $medical_conditions);

            if ($stmt->execute()) {
                $success = "Child added successfully!";
                echo "<script>setTimeout(()=>window.location.href='children.php',2000);</script>";
            } else {
                $error = "Failed to add child.";
            }

        } catch (PDOException $e) {
            error_log($e->getMessage());
            $error = "Database error occurred.";
        }
    }
}

include __DIR__ . '/includes/header.php';
?>