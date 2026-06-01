<?php
// Dashboard header — renders sidebar + topbar
// Requires: $dashRole, $dashNav (array of [label, icon_svg, url, key]), $dashTitle, $activeKey
$siteUrl = SITE_URL;
$user    = currentUser();
$initial = strtoupper(substr($user['name'] ?? 'U', 0, 1));
$roleLabelMap = ['admin' => 'Administrator', 'parent' => 'Parent Portal', 'hospital' => 'Hospital Portal'];
$roleLabel = $roleLabelMap[$dashRole] ?? 'Dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($dashTitle) ?> — VAXORA</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= $siteUrl ?>/assets/css/style.css">
<style>
  @font-face { font-family:'Satoshi'; src:url('https://framerusercontent.com/third-party-assets/fontshare/wf/LAFFD4SDUCDVQEXFPDC7C53EQ4ZELWQI/PXCT3G6LO6ICM5I3NTYENYPWJAECAWDD/GHM6WVH6MILNYOOCXHXB5GTSGNTMGXZR.woff2') format('woff2'); font-weight:700; }
</style>
</head>
<body>
<div class="dash-wrapper">
  <!-- SIDEBAR -->
  <aside class="sidebar">
    <div class="sidebar-logo">
      <svg width="22" height="22" viewBox="0 0 28 28" fill="none"><path d="M14 2L4 7v7c0 5.5 4.3 10.7 10 12 5.7-1.3 10-6.5 10-12V7L14 2z" fill="#90E300" opacity="0.25" stroke="#90E300" stroke-width="1.5"/><path d="M14 8v12M8 14h12" stroke="#90E300" stroke-width="2" stroke-linecap="round"/></svg>
      <span>VAXORA</span>
    </div>
    <div class="sidebar-role"><?= e($roleLabel) ?></div>
    <nav>
      <?php foreach ($dashNav as $item): ?>
        <a href="<?= $siteUrl . $item['url'] ?>" class="<?= ($activeKey === $item['key']) ? 'active' : '' ?>">
          <?= $item['icon'] ?>
          <?= e($item['label']) ?>
        </a>
      <?php endforeach; ?>
    </nav>
    <div class="sidebar-footer">
      <a href="<?= $siteUrl ?>/logout.php">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9"/></svg>
        Logout
      </a>
    </div>
  </aside>

  <!-- MAIN -->
  <main class="dash-main">
    <div class="dash-topbar">
      <div style="display:flex;align-items:center;gap:12px">
        <button id="sidebarToggle" style="background:none;border:none;cursor:pointer;display:none" class="hamburger">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none"><path d="M3 6h18M3 12h18M3 18h18" stroke="#1C2706" stroke-width="2" stroke-linecap="round"/></svg>
        </button>
        <span class="topbar-title"><?= e($dashTitle) ?></span>
      </div>
      <div class="topbar-user">
        <span style="font-size:0.85rem;color:#5a6a40"><?= e($user['name'] ?? '') ?></span>
        <div class="topbar-avatar"><?= e($initial) ?></div>
        <a href="<?= $siteUrl ?>" style="font-size:0.8rem;color:#5a6a40">&#8592; Public Site</a>
      </div>
    </div>
    <div class="dash-content">
<?php
// Show flash
echo showFlash();
?>
