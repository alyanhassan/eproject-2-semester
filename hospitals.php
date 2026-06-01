<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';
$pageTitle  = 'Find a Hospital';
$activePage = 'hospitals';

$db        = getDB();
$hospitals = $db->query('SELECT * FROM hospitals WHERE status="active" ORDER BY city, name')->fetchAll();
require __DIR__ . '/includes/header.php';
?>

<section class="hero" style="min-height:48vh">
  <div class="hero-bg">
    <img src="https://images.unsplash.com/photo-1538108149393-fbbd81895907?auto=format&fit=crop&w=1920&q=80" alt="Hospital building">
    <div class="hero-overlay" style="background:rgba(28,39,6,0.58)"></div>
  </div>
  <div class="hero-content text-center" style="padding:64px 5%">
    <span class="badge badge-lime" style="margin-bottom:16px"><?= count($hospitals) ?>+ Verified Centers</span>
    <h1 style="color:#fff;font-size:clamp(2rem,4vw,3rem);margin-bottom:16px">Find a Certified Vaccination Center</h1>
    <p style="color:rgba(255,255,255,0.82);font-size:1rem;max-width:520px;margin:0 auto 36px;line-height:1.7">
      Every hospital on VAXORA is verified by our team and registered with the Pakistan Medical &amp; Dental Council.
    </p>
    <div class="search-bar" style="max-width:480px;margin:0 auto">
      <input type="text" id="hospitalSearch" placeholder="Search by city or hospital name...">
      <button type="button">Search</button>
    </div>
  </div>
</section>

<section class="section bg-white">
  <div class="container">
    <div class="filter-tabs">
      <button class="filter-tab active" data-filter="all"       data-group="city">All Cities</button>
      <button class="filter-tab"        data-filter="Karachi"   data-group="city">Karachi</button>
      <button class="filter-tab"        data-filter="Lahore"    data-group="city">Lahore</button>
      <button class="filter-tab"        data-filter="Islamabad" data-group="city">Islamabad</button>
    </div>

    <div class="grid-2">
      <?php foreach ($hospitals as $h): ?>
      <div class="card reveal" data-city="<?= e($h['city']) ?>" data-hospital-name="<?= e($h['name']) ?>">

        <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:10px">
          <div>
            <h3 style="font-size:0.98rem;margin-bottom:4px"><?= e($h['name']) ?></h3>
            <span class="badge badge-blue"><?= e($h['city']) ?></span>
          </div>
          <span class="badge badge-lime" style="flex-shrink:0;margin-left:8px">
            <span class="stars">&#9733;</span> <?= $h['rating'] ?>
          </span>
        </div>

        <div style="display:flex;flex-direction:column;gap:6px;margin-bottom:14px">
          <?php if ($h['address']): ?>
          <div style="display:flex;align-items:center;gap:8px;font-size:0.83rem;color:var(--muted)">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
            <?= e($h['address']) ?>
          </div>
          <?php endif; ?>
          <?php if ($h['phone']): ?>
          <div style="display:flex;align-items:center;gap:8px;font-size:0.83rem;color:var(--muted)">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6A2 2 0 014.11 2h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"/></svg>
            <?= e($h['phone']) ?>
          </div>
          <?php endif; ?>
          <?php if ($h['hours']): ?>
          <div style="display:flex;align-items:center;gap:8px;font-size:0.83rem;color:var(--muted)">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            <?= e($h['hours']) ?>
          </div>
          <?php endif; ?>
        </div>

        <?php if ($h['services']): ?>
        <div style="display:flex;gap:6px;flex-wrap:wrap;margin-bottom:18px">
          <?php foreach (explode(',', $h['services']) as $svc): ?>
          <span class="pill-tag"><?= e(trim($svc)) ?></span>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <a href="<?= SITE_URL ?>/login.php" class="btn btn-primary btn-sm">Book Appointment &rarr;</a>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Map section -->
<section class="section bg-dark text-center">
  <div class="container" style="max-width:680px">
    <h2 style="color:#fff;margin-bottom:8px">Partner Hospitals Across Pakistan</h2>
    <p style="color:rgba(255,255,255,0.55);margin-bottom:40px">Serving major cities with more locations coming soon</p>
    <svg viewBox="0 0 600 500" style="width:100%;max-width:480px;margin:0 auto" fill="none">
      <path d="M180,60 L200,40 L240,35 L280,45 L320,38 L380,50 L420,70 L460,90 L480,130 L490,170 L480,210 L500,240 L510,280 L490,320 L460,350 L430,370 L400,390 L370,410 L340,420 L310,430 L280,440 L250,420 L220,400 L200,370 L180,340 L160,310 L150,280 L140,250 L130,220 L120,190 L125,160 L140,130 L155,100 L170,80 Z"
        fill="rgba(213,222,197,0.08)" stroke="rgba(144,227,0,0.35)" stroke-width="1.5"/>
      <?php
      $pins = [
        ['city'=>'Karachi',   'cx'=>220,'cy'=>390],
        ['city'=>'Lahore',    'cx'=>340,'cy'=>200],
        ['city'=>'Islamabad', 'cx'=>300,'cy'=>155],
      ];
      foreach ($pins as $p): ?>
      <circle cx="<?= $p['cx'] ?>" cy="<?= $p['cy'] ?>" r="22" fill="rgba(144,227,0,0.12)"/>
      <circle cx="<?= $p['cx'] ?>" cy="<?= $p['cy'] ?>" r="9"  fill="#90E300"/>
      <circle cx="<?= $p['cx'] ?>" cy="<?= $p['cy'] ?>" r="4"  fill="#fff"/>
      <text x="<?= $p['cx'] ?>" y="<?= $p['cy'] + ($p['city']==='Islamabad'?-14:26) ?>" text-anchor="middle" fill="rgba(255,255,255,0.75)" font-size="11" font-family="Inter,sans-serif"><?= $p['city'] ?></text>
      <?php endforeach; ?>
    </svg>
    <div style="display:flex;justify-content:center;gap:40px;margin-top:28px;flex-wrap:wrap">
      <?php
      $cityCounts = [];
      foreach ($hospitals as $h) { $cityCounts[$h['city']] = ($cityCounts[$h['city']] ?? 0) + 1; }
      foreach ($cityCounts as $city => $count): ?>
      <div style="color:rgba(255,255,255,0.65);font-size:0.88rem">
        <span style="color:#90E300;font-weight:700"><?= $count ?></span> in <?= e($city) ?>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
