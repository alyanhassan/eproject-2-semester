<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';
$pageTitle  = 'Find a Hospital';
$activePage = 'hospitals';

$db = getDB();
$hospitals = $db->query('SELECT * FROM hospitals WHERE status="active" ORDER BY city, name')->fetchAll();
require __DIR__ . '/includes/header.php';
?>

<section class="hero" style="min-height:50vh">
  <div class="hero-bg">
    <img src="https://images.unsplash.com/photo-1538108149393-fbbd81895907?auto=format&fit=crop&w=1920&q=80" alt="Hospital building">
    <div class="hero-overlay" style="background:rgba(28,39,6,0.55)"></div>
  </div>
  <div class="hero-blob" style="width:260px;height:260px;background:rgba(144,227,0,0.10);top:20px;right:60px"></div>
  <div class="hero-content text-center" style="padding:60px 5%">
    <h1 style="color:#fff;font-size:clamp(2rem,4vw,3rem);margin-bottom:16px">Find a Certified Vaccination Center</h1>
    <p style="color:rgba(255,255,255,0.8);font-size:1.05rem;max-width:560px;margin:0 auto 36px">
      <?= count($hospitals) ?>+ partner hospitals across Pakistan offering verified, safe vaccination services.
    </p>
    <div class="search-bar" style="max-width:500px;margin:0 auto">
      <input type="text" id="hospitalSearch" placeholder="Search by city or hospital name...">
      <button type="button">Search</button>
    </div>
  </div>
</section>

<section class="section bg-white">
  <div class="container">
    <div class="filter-tabs">
      <button class="filter-tab active" data-filter="all" data-group="city">All Cities</button>
      <button class="filter-tab" data-filter="Karachi" data-group="city">Karachi</button>
      <button class="filter-tab" data-filter="Lahore" data-group="city">Lahore</button>
      <button class="filter-tab" data-filter="Islamabad" data-group="city">Islamabad</button>
    </div>

    <div class="grid-2">
      <?php foreach ($hospitals as $h): ?>
      <div class="card reveal" data-city="<?= e($h['city']) ?>" data-hospital-name="<?= e($h['name']) ?>">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:10px">
          <div>
            <h3 style="font-size:1rem;margin-bottom:4px"><?= e($h['name']) ?></h3>
            <span class="badge badge-blue"><?= e($h['city']) ?></span>
          </div>
          <span class="badge badge-lime"><span class="stars">&#9733;</span> <?= $h['rating'] ?></span>
        </div>
        <?php if ($h['address']): ?><div class="text-muted" style="font-size:0.82rem;margin-bottom:6px">&#128205; <?= e($h['address']) ?></div><?php endif; ?>
        <?php if ($h['phone']): ?><div class="text-muted" style="font-size:0.82rem;margin-bottom:6px">&#128222; <?= e($h['phone']) ?></div><?php endif; ?>
        <?php if ($h['hours']): ?><div class="text-muted" style="font-size:0.82rem;margin-bottom:12px">&#8987; <?= e($h['hours']) ?></div><?php endif; ?>
        <?php if ($h['services']): ?>
        <div style="display:flex;gap:6px;flex-wrap:wrap;margin-bottom:16px">
          <?php foreach (explode(',', $h['services']) as $svc): ?>
          <span class="pill-tag"><?= e(trim($svc)) ?></span>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
        <a href="<?= SITE_URL ?>/login.php" class="btn btn-primary btn-sm">Book Appointment &#8594;</a>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Pakistan Map Placeholder -->
<section class="section bg-dark text-center">
  <div class="container" style="max-width:700px">
    <h2 style="color:#fff;margin-bottom:8px">Find Hospitals Near You</h2>
    <p style="color:rgba(255,255,255,0.6);margin-bottom:40px">Partner hospitals across major cities in Pakistan</p>
    <svg viewBox="0 0 600 500" style="width:100%;max-width:500px;margin:0 auto" fill="none" xmlns="http://www.w3.org/2000/svg">
      <path d="M180,60 L200,40 L240,35 L280,45 L320,38 L380,50 L420,70 L460,90 L480,130 L490,170 L480,210 L500,240 L510,280 L490,320 L460,350 L430,370 L400,390 L370,410 L340,420 L310,430 L280,440 L250,420 L220,400 L200,370 L180,340 L160,310 L150,280 L140,250 L130,220 L120,190 L125,160 L140,130 L155,100 L170,80 Z"
        fill="rgba(213,222,197,0.12)" stroke="rgba(144,227,0,0.4)" stroke-width="1.5"/>
      <!-- Karachi pin -->
      <g style="cursor:pointer">
        <circle cx="220" cy="390" r="22" fill="rgba(144,227,0,0.15)" style="animation:ripple 2s ease-out infinite"/>
        <circle cx="220" cy="390" r="9" fill="#90E300"/>
        <circle cx="220" cy="390" r="5" fill="#fff"/>
        <text x="220" y="418" text-anchor="middle" fill="rgba(255,255,255,0.8)" font-size="11" font-family="Inter,sans-serif">Karachi</text>
      </g>
      <!-- Lahore pin -->
      <g style="cursor:pointer">
        <circle cx="340" cy="200" r="22" fill="rgba(144,227,0,0.15)" style="animation:ripple 2s ease-out infinite 0.7s"/>
        <circle cx="340" cy="200" r="9" fill="#90E300"/>
        <circle cx="340" cy="200" r="5" fill="#fff"/>
        <text x="340" y="228" text-anchor="middle" fill="rgba(255,255,255,0.8)" font-size="11" font-family="Inter,sans-serif">Lahore</text>
      </g>
      <!-- Islamabad pin -->
      <g style="cursor:pointer">
        <circle cx="300" cy="155" r="22" fill="rgba(144,227,0,0.15)" style="animation:ripple 2s ease-out infinite 1.4s"/>
        <circle cx="300" cy="155" r="9" fill="#90E300"/>
        <circle cx="300" cy="155" r="5" fill="#fff"/>
        <text x="300" y="135" text-anchor="middle" fill="rgba(255,255,255,0.8)" font-size="11" font-family="Inter,sans-serif">Islamabad</text>
      </g>
    </svg>
    <style>@keyframes ripple { 0%{r:9;opacity:0.8} 100%{r:24;opacity:0} }</style>
    <div style="display:flex;justify-content:center;gap:32px;margin-top:24px">
      <div style="color:rgba(255,255,255,0.7);font-size:0.88rem"><span style="color:#90E300;font-weight:700">5</span> hospitals in Karachi</div>
      <div style="color:rgba(255,255,255,0.7);font-size:0.88rem"><span style="color:#90E300;font-weight:700">3</span> hospitals in Lahore</div>
      <div style="color:rgba(255,255,255,0.7);font-size:0.88rem"><span style="color:#90E300;font-weight:700">2</span> hospitals in Islamabad</div>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
