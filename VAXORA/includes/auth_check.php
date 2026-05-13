<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function checkRole($allowed_roles) {
    if (!isset($_SESSION['user_id'])) {
        header("Location: /auth/login.php");
        exit();
    }
    if (!in_array($_SESSION['role'], $allowed_roles)) {
        header("Location: /auth/login.php?error=access_denied");
        exit();
    }
}
