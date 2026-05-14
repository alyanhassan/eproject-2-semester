<?php

// ================= SITE CONFIG =================
define('SITE_NAME', 'E-Vaccination Management System');
define('SITE_URL', 'http://localhost/evaccination-system/');
define('ADMIN_EMAIL', 'admin@evaccination.com');

// ================= TIMEZONE =================
date_default_timezone_set('Asia/Karachi');

// ================= UPLOAD SETTINGS =================
define('UPLOAD_PATH', __DIR__ . '/../uploads/');

// 5MB limit
define('MAX_FILE_SIZE', 5 * 1024 * 1024);

// ================= PAGINATION =================
define('ITEMS_PER_PAGE', 10);

// ================= APPOINTMENT RULES =================
define('MIN_ADVANCE_DAYS', 1);
define('MAX_ADVANCE_DAYS', 30);
define('WORKING_HOURS_START', '09:00');
define('WORKING_HOURS_END', '17:00');

// ================= ERROR REPORTING =================
// DEV mode only (production me OFF karna hai)
error_reporting(E_ALL);
ini_set('display_errors', 1);