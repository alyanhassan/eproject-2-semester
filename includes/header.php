<?php
// Public site header — include at top of every public page
// $pageTitle and $activePage must be set before including this file
$pageTitle  = $pageTitle  ?? 'VAXORA';
$activePage = $activePage ?? '';
$siteUrl    = SITE_URL;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($pageTitle) ?> — VAXORA Pakistan</title>
<meta name="description" content="VAXORA — Pakistan's trusted vaccination booking platform connecting families to verified hospitals and WHO-approved vaccines.">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= $siteUrl ?>/assets/css/style.css">
<style>
  @font-face {
    font-family: 'Satoshi';
    src: url('https://framerusercontent.com/third-party-assets/fontshare/wf/P2LQKHE6KA6ZP4AAGN72KDWMHH6ZH3TA/ZC32TK2P7FPS5GFTL46EU6KQJA24ZYDB/7AHDUZ4A7LFLVFUIFSARGIWCRQJHISQP.woff2') format('woff2');
    font-weight: 500; font-style: normal;
  }
  @font-face {
    font-family: 'Satoshi';
    src: url('https://framerusercontent.com/third-party-assets/fontshare/wf/LAFFD4SDUCDVQEXFPDC7C53EQ4ZELWQI/PXCT3G6LO6ICM5I3NTYENYPWJAECAWDD/GHM6WVH6MILNYOOCXHXB5GTSGNTMGXZR.woff2') format('woff2');
    font-weight: 700; font-style: normal;
  }
</style>
</head>
<body>
<div id="scroll-progress"></div>

<nav class="navbar" id="mainNav">
  <a href="<?= $siteUrl ?>/" class="logo">
    <svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
      <path d="M14 2L4 7v7c0 5.5 4.3 10.7 10 12 5.7-1.3 10-6.5 10-12V7L14 2z" fill="#90E300" opacity="0.2" stroke="#90E300" stroke-width="1.5"/>
      <path d="M14 8v12M8 14h12" stroke="#90E300" stroke-width="2" stroke-linecap="round"/>
    </svg>
    VAXORA
  </a>

  <button class="hamburger" aria-label="Menu">
    <svg width="22" height="22" viewBox="0 0 22 22" fill="none"><path d="M3 6h16M3 11h16M3 16h16" stroke="#1C2706" stroke-width="2" stroke-linecap="round"/></svg>
  </button>

  <div class="nav-links">
    <a href="<?= $siteUrl ?>/" class="<?= $activePage==='home' ? 'active' : '' ?>">Home</a>
    <a href="<?= $siteUrl ?>/vaccines.php" class="<?= $activePage==='vaccines' ? 'active' : '' ?>">Vaccines</a>
    <a href="<?= $siteUrl ?>/hospitals.php" class="<?= $activePage==='hospitals' ? 'active' : '' ?>">Hospitals</a>
    <a href="<?= $siteUrl ?>/about.php" class="<?= $activePage==='about' ? 'active' : '' ?>">About</a>
    <a href="<?= $siteUrl ?>/blog.php" class="<?= $activePage==='blog' ? 'active' : '' ?>">Blog</a>
    <a href="<?= $siteUrl ?>/contact.php" class="<?= $activePage==='contact' ? 'active' : '' ?>">Contact</a>
    <?php if (isLoggedIn()): ?>
      <a href="<?= dashboardUrl($_SESSION['user']['role']) ?>">My Dashboard</a>
    <?php else: ?>
      <a href="<?= $siteUrl ?>/login.php">Login</a>
    <?php endif; ?>
    <a href="<?= $siteUrl ?>/login.php" class="nav-cta">Book Vaccination</a>
  </div>
</nav>
