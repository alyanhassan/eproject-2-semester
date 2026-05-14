<?php
require_once '../includes/auth_check.php';
checkRole(['admin']); // Security check
require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

if ($id) {
    try {
        // Safety Check: Don't delete if hospital has appointments
        $check = $db->prepare("SELECT COUNT(*) FROM appointments WHERE hospital_id = :id");
        $check->execute([':id' => $id]);
        
        if ($check->fetchColumn() > 0) {
            // "Soft Delete" - just make it inactive instead of deleting data
            $stmt = $db->prepare("UPDATE hospitals SET status = 'inactive' WHERE hospital_id = :id");
            $stmt->execute([':id' => $id]);
        } else {
            // Real Delete - safe because no linked records exist
            $stmt = $db->prepare("DELETE FROM hospitals WHERE hospital_id = :id");
            $stmt->execute([':id' => $id]);
        }
        header("Location: manage_hospitals.php?success=1");
    } catch (PDOException $e) {
        header("Location: manage_hospitals.php?error=1");
    }
}
exit();