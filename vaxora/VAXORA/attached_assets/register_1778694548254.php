<?php
// ... after validation ...
try {
    $db->beginTransaction();

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // 1. Create Login Account
    $stmt = $db->prepare("INSERT INTO users (email, password, role, status) VALUES (?, ?, 'parent', 'active')");
    $stmt->execute([$email, $hashed_password]);
    $user_id = $db->lastInsertId();

    // 2. Create Parent Profile
    $stmt2 = $db->prepare("INSERT INTO parents (user_id, full_name, phone, address) VALUES (?, ?, ?, ?)");
    $stmt2->execute([$user_id, $full_name, $phone, $address]);

    $db->commit();
    $success = "Account created! You can now log in.";
} catch (Exception $e) {
    $db->rollBack();
    $error = "Registration failed. Email might already be in use.";
}