<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Resolve base URL for redirects (same logic as config.php)
function _authGetBaseUrl() {
    if (defined('BASE_URL')) return BASE_URL;
    $docRoot = isset($_SERVER['DOCUMENT_ROOT'])
        ? rtrim(str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT'])), '/')
        : '';
    $appRoot = rtrim(str_replace('\\', '/', realpath(__DIR__ . '/..')), '/');
    if ($docRoot !== '' && strpos($appRoot, $docRoot) === 0) {
        return str_replace($docRoot, '', $appRoot);
    }
    return '';
}

function checkRole($allowed_roles) {
    if (!isset($_SESSION['user_id'])) {
        $base = _authGetBaseUrl();
        header("Location: " . $base . "/auth/login.php");
        exit();
    }
    if (!in_array($_SESSION['role'], $allowed_roles)) {
        $base = _authGetBaseUrl();
        header("Location: " . $base . "/auth/login.php?error=access_denied");
        exit();
    }
}
