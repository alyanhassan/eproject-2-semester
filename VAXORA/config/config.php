<?php
define('SITE_NAME', 'E-Vaccination Management System');
define('SITE_URL', '/');
define('ADMIN_EMAIL', 'admin@evaccination.com');

date_default_timezone_set('Asia/Karachi');

define('UPLOAD_PATH', __DIR__ . '/../uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024);
define('ITEMS_PER_PAGE', 10);
define('MIN_ADVANCE_DAYS', 1);
define('MAX_ADVANCE_DAYS', 30);
define('WORKING_HOURS_START', '09:00');
define('WORKING_HOURS_END', '17:00');

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function generateUniqueId($prefix = 'ID') {
    return $prefix . '-' . strtoupper(substr(uniqid(), -6));
}
