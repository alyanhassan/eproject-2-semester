<?php
define('SITE_NAME', 'E-Vaccination Management System');
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

// ── Auto-detect base URL ──────────────────────────────────────────────────────
// Works on PHP built-in server (Replit) AND XAMPP/Apache in any subfolder
if (!defined('BASE_URL')) {
    $dr = @realpath($_SERVER['DOCUMENT_ROOT'] ?? '');
    $ar = @realpath(__DIR__ . '/..');
    if ($dr && $ar) {
        $docRoot = rtrim(str_replace('\\', '/', $dr), '/');
        $appRoot = rtrim(str_replace('\\', '/', $ar), '/');
        $base    = ($docRoot !== '' && strpos($appRoot, $docRoot) === 0)
                   ? str_replace($docRoot, '', $appRoot)
                   : '';
    } else {
        $base = '';
    }
    define('BASE_URL', rtrim($base, '/'));
}

// ── Output-buffer: auto-fix every href/src/action on every page ──────────────
// IMPORTANT: Always call ob_start() — on XAMPP, output_buffering is On by
// default (ob_get_level() > 0), so we must NOT guard with ob_get_level() === 0.
if (!function_exists('_vaxoraFixUrls')) {
    function _vaxoraFixUrls($buffer) {
        $base = BASE_URL;
        if ($base === '') return $buffer;
        return preg_replace(
            '/\b(href|src|action)="\//i',
            '$1="' . $base . '/',
            $buffer
        );
    }
    ob_start('_vaxoraFixUrls');
}

// ── Helpers ───────────────────────────────────────────────────────────────────
function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function redirect($path) {
    // Prepend BASE_URL for absolute paths so redirects work in any subfolder
    if ($path !== '' && $path[0] === '/') {
        $path = BASE_URL . $path;
    }
    header("Location: $path");
    exit();
}

function generateUniqueId($prefix = 'ID') {
    return $prefix . '-' . strtoupper(substr(uniqid(), -6));
}

define('SITE_URL', BASE_URL . '/');
