<?php
require_once '../includes/auth_check.php';
checkRole(['admin', 'hospital']);
require_once '../config/database.php';

$id = $_GET['id'];
$status = $_GET['status'];

$database = new Database();
$db = $database->getConnection();

if ($status == 'completed') {
    try {
        $db->beginTransaction();

        // 1. Mark appointment as completed
        $stmt = $db->prepare("UPDATE appointments SET status = 'completed' WHERE appointment_id = :id");
        $stmt->execute([':id' => $id]);

        // 2. Get data to move to records
        $info = $db->prepare("SELECT * FROM appointments WHERE appointment_id = :id");
        $info->execute([':id' => $id]);
        $data = $info->fetch();

        // 3. Create permanent medical record
        $rec = $db->prepare("INSERT INTO vaccination_records (child_id, vaccine_id, hospital_id, administration_date) 
                            VALUES (?, ?, ?, NOW())");
        $rec->execute([$data['child_id'], $data['vaccine_id'], $data['hospital_id']]);

        $db->commit();
        header("Location: appointments.php?msg=Success");
    } catch (Exception $e) {
        $db->rollBack();
        die("Error: " . $e->getMessage());
    }
}