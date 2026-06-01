<?php
// ============================================================
// VAXORA — Database & Site Configuration
// Auto-detects site URL — no manual editing needed for local dev
// ============================================================

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'vaxora_db');
define('SITE_NAME', 'VAXORA');

// Auto-detect SITE_URL from the server environment
// Works on any port (80, 82, 8080, etc.) and any subfolder name
if (!defined('SITE_URL')) {
    $protocol   = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host       = $_SERVER['HTTP_HOST'] ?? 'localhost'; // includes :port automatically
    $docRoot    = realpath($_SERVER['DOCUMENT_ROOT'] ?? '');
    $thisDir    = realpath(__DIR__);

    if ($docRoot && strpos($thisDir, $docRoot) === 0) {
        // Derive subfolder path relative to document root
        $relative = str_replace($docRoot, '', $thisDir);
        $relative = str_replace('\\', '/', $relative); // Windows backslash fix
        $relative = '/' . ltrim($relative, '/');
    } else {
        // Fallback: just use the folder name
        $relative = '/' . basename(__DIR__);
    }

    define('SITE_URL', rtrim($protocol . '://' . $host . $relative, '/'));
}

// ============================================================
// PDO Database Connection
// ============================================================
function getDB() {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $dsn     = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            die('
<div style="font-family:Inter,sans-serif;padding:48px;background:#fff0f0;color:#c00;border-left:5px solid #c00;margin:48px auto;max-width:620px;border-radius:12px">
  <h2 style="margin-bottom:12px">&#10060; Database Connection Failed</h2>
  <p style="margin-bottom:8px"><b>Error:</b> ' . htmlspecialchars($e->getMessage()) . '</p>
  <hr style="border:none;border-top:1px solid #fcc;margin:16px 0">
  <p style="font-size:0.9rem">Check your <code>config.php</code>:</p>
  <ul style="font-size:0.9rem;margin-top:8px;line-height:2">
    <li>DB_HOST = <b>localhost</b></li>
    <li>DB_USER = <b>root</b></li>
    <li>DB_PASS = <b>(blank for XAMPP default)</b></li>
    <li>DB_NAME = <b>vaxora_db</b> — make sure you imported <b>vaxora.sql</b> in phpMyAdmin</li>
  </ul>
</div>');
        }
    }
    return $pdo;
}

// ============================================================
// Session
// ============================================================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ============================================================
// Helpers
// ============================================================
function redirect($url) {
    header('Location: ' . $url);
    exit;
}

function e($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

function setFlash($type, $msg) {
    $_SESSION['flash'] = ['type' => $type, 'msg' => $msg];
}

function getFlash() {
    if (isset($_SESSION['flash'])) {
        $f = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $f;
    }
    return null;
}

function showFlash() {
    $f = getFlash();
    if (!$f) return '';
    $styles = [
        'success' => 'background:#f0ffe6;border-left:4px solid #90E300;color:#2d5000',
        'error'   => 'background:#fff5f5;border-left:4px solid #e53e3e;color:#c00',
        'info'    => 'background:#f0f7ff;border-left:4px solid #2467E3;color:#0d3b8a',
    ];
    $s = $styles[$f['type']] ?? $styles['info'];
    return '<div class="flash-msg" style="' . $s . ';padding:12px 18px;margin-bottom:20px;border-radius:8px;font-weight:500">'
         . e($f['msg']) . '</div>';
}
