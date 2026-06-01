<?php
require_once __DIR__ . '/config.php';

// ============================================================
// Authentication helpers
// ============================================================

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function currentUser() {
    return $_SESSION['user'] ?? null;
}

function requireLogin($role = null) {
    if (!isLoggedIn()) {
        setFlash('error', 'Please log in to access this page.');
        redirect(SITE_URL . '/login.php');
    }
    if ($role && $_SESSION['user']['role'] !== $role) {
        redirect(SITE_URL . '/login.php?unauthorized=1');
    }
}

function requireAdmin() {
    if (!isLoggedIn() || $_SESSION['user']['role'] !== 'admin') {
        redirect(SITE_URL . '/admin/login.php');
    }
}

function requireParent() {
    if (!isLoggedIn() || $_SESSION['user']['role'] !== 'parent') {
        redirect(SITE_URL . '/login.php');
    }
}

function requireHospital() {
    if (!isLoggedIn() || $_SESSION['user']['role'] !== 'hospital') {
        redirect(SITE_URL . '/login.php');
    }
}

function loginUser($email, $password, $role = null) {
    $db = getDB();
    $sql = 'SELECT * FROM users WHERE email = ? AND status = "active"';
    $params = [$email];
    if ($role) {
        $sql .= ' AND role = ?';
        $params[] = $role;
    }
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user']    = $user;
        return $user;
    }
    return false;
}

function logoutUser() {
    session_destroy();
    session_start();
}

function dashboardUrl($role) {
    switch ($role) {
        case 'admin':    return SITE_URL . '/admin/index.php';
        case 'parent':   return SITE_URL . '/parent/index.php';
        case 'hospital': return SITE_URL . '/hospital/index.php';
        default:         return SITE_URL . '/';
    }
}
