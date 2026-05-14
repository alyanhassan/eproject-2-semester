<?php
require_once '../includes/auth_check.php';
// Only Admin or Hospital staff can complete vaccinations
checkRole(['admin', 'hospital']); 

require_once '../config/config.php';
require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

// 1. Get and Validate ID
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

if ($id) {
    try {
        // Start transaction for data safety
        $db->beginTransaction();

        // 2. Fetch appointment details before updating (to log in history)
        $fetchQuery = "SELECT child_id, vaccine_id, hospital_id, appointment_date 
                       FROM appointments WHERE appointment_id = :id AND status != 'completed' LIMIT 1";
        $fetchStmt = $db->prepare($fetchQuery);
        $fetchStmt->bindParam(':id', $id, PDO::PARAM_INT);
        $fetchStmt->execute();
        $appt = $fetchStmt->fetch(PDO::FETCH_ASSOC);

        if ($appt) {
            // 3. Update the appointment status
            $updateQuery = "UPDATE appointments SET status = 'completed' WHERE appointment_id = :id";
            $updateStmt = $db->prepare($updateQuery);
            $updateStmt->bindParam(':id', $id, PDO::PARAM_INT);
            $updateStmt->execute();

            // 4. Create a permanent record in vaccination_records
            $historyQuery = "INSERT INTO vaccination_records 
                            (child_id, vaccine_id, hospital_id, administration_date, created_at)
                            VALUES (:c_id, :v_id, :h_id, :a_date, NOW())";
            
            $historyStmt = $db->prepare($historyQuery);
            $historyStmt->execute([
                ':c_id'   => $appt['child_id'],
                ':v_id'   => $appt['vaccine_id'],
                ':h_id'   => $appt['hospital_id'],
                ':a_date' => $appt['appointment_date'] // Or use date('Y-m-d') for today
            ]);

            $db->commit();
            $_SESSION['success'] = "Vaccination marked as completed and recorded in history!";
        } else {
            // Already completed or not found
            $db->rollBack();
        }

    } catch (PDOException $e) {
        $db->rollBack();
        error_log("Completion Error: " . $e->getMessage());
        $_SESSION['error'] = "Critical error during completion process.";
    }
}

// 5. Smart Redirect based on Role
$redirect = ($_SESSION['role'] == 'admin') ? "appointments.php" : "dashboard.php";
header("Location: " . $redirect);
exit();