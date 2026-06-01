<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';

$pageTitle  = 'Home';
$activePage = 'home';

$db = getDB();

// Load approved reviews from DB
$reviews = $db->query('SELECT * FROM reviews WHERE status="approved" ORDER BY created_at DESC LIMIT 6')->fetchAll();

require __DIR__ . '/includes/header.php';
?>

<!-- HERO -->
<section class="hero">
  <div class="hero-bg">
    <img src="https://images.unsplash.com/photo-1584820927498-cfe5211fd8bf?auto=format&fit=crop&w=1920&q=80" alt="Doctor vaccinating child">
    <div class="hero-overlay" style="background:linear-gradient(135deg,rgba(28,39,6,0.75) 0%,rgba(28,39,6,0.35) 100%)"></div>
  </div>
  <div class="hero-blob" style="width:320px;height:320px;background:rgba(144,227,0,0.08);top:60px;right:80px"></div>
  <div class="hero-blob" style="width:200px;height:200px;background:rgba(144,227,0,0.06);bottom:40px;left:40px"></div>
  <div class="hero-content">
    <div style="max-width:640px">
      <span class="badge badge-lime" style="margin-bottom:20px;display:inline-block">WHO-Approved · Pakistan EPI Partner</span>
      <h1 style="font-size:clamp(2.2rem,5vw,3.6rem);color:#fff;margin-bottom:20px;line-height:1.1">
        Protecting Lives,<br>One Vaccine at a Time
      </h1>
      <p style="font-size:1.05rem;color:rgba(255,255,255,0.82);margin-bottom:36px;max-width:520px;line-height:1.75">
        VAXORA connects you to trusted hospitals and verified vaccines — making immunization easy, transparent, and accessible for every family in Pakistan.
      </p>
      <div style="display:flex;gap:16px;flex-wrap:wrap">
        <a href="<?= SITE_URL ?>/hospitals.php" class="btn btn-primary">Find a Hospital</a>
        <a href="<?= SITE_URL ?>/vaccines.php" class="btn btn-outline">View Vaccines</a>
      </div>
    </div>
  </div>
</section>

<!-- STATS BANNER -->
<div class="stats-banner">
  <div class="stat-item"><div class="stat-number" data-countup="50000" data-suffix="+">50,000+</div><div class="stat-label">Doses Administered</div></div>
  <div class="stat-item"><div class="stat-number" data-countup="120" data-suffix="+">120+</div><div class="stat-label">Partner Hospitals</div></div>
  <div class="stat-item"><div class="stat-number" data-countup="25" data-suffix="+">25+</div><div class="stat-label">Vaccines Available</div></div>
  <div class="stat-item"><div class="stat-number" data-countup="98" data-suffix="%">98%</div><div class="stat-label">Patient Satisfaction</div></div>
</div>

<!-- VACCINES OVERVIEW -->
<section class="section bg-white">
  <div class="container">
    <div style="display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:48px;flex-wrap:wrap;gap:16px">
      <div>
        <span class="badge badge-lime" style="margin-bottom:12px">Vaccine Directory</span>
        <h2 class="section-title">Our Complete Vaccine Portfolio</h2>
        <p class="section-subtitle" style="margin-bottom:0">WHO-approved vaccines with full dosage &amp; age information</p>
      </div>
      <a href="<?= SITE_URL ?>/vaccines.php" class="btn btn-outline-dark">See All Vaccines &rarr;</a>
    </div>
    <div class="grid-4">
      <?php
      $vaxList = [
        ['name'=>'COVID-19 Vaccine',  'desc'=>'mRNA based · 2-dose schedule · Age 12+',       'type'=>'mRNA',            'color'=>'badge-blue'],
        ['name'=>'Flu (Influenza)',    'desc'=>'Annual shot · All ages · Seasonal protection',  'type'=>'Inactivated',     'color'=>'badge-teal'],
        ['name'=>'MMR Vaccine',        'desc'=>'Measles, Mumps & Rubella · Age 12 months+',    'type'=>'Live-attenuated', 'color'=>'badge-orange'],
        ['name'=>'Hepatitis B',        'desc'=>'3-dose series · Newborns & Adults · Lifelong',  'type'=>'Recombinant',     'color'=>'badge-purple'],
      ];
      foreach ($vaxList as $v): ?>
      <div class="card reveal" style="border-top:3px solid var(--accent)">
        <span class="badge <?= $v['color'] ?>" style="margin-bottom:14px"><?= $v['type'] ?></span>
        <h3 style="font-size:0.98rem;margin-bottom:8px"><?= $v['name'] ?></h3>
        <p class="text-muted" style="font-size:0.84rem;line-height:1.65;flex:1;margin-bottom:16px"><?= $v['desc'] ?></p>
        <a href="<?= SITE_URL ?>/vaccines.php" style="color:#4a7c00;font-weight:600;font-size:0.84rem">View Details &rarr;</a>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- TRUSTED HOSPITALS -->
<section class="section bg-sage">
  <div class="container">
    <div style="display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:48px;flex-wrap:wrap;gap:16px">
      <div>
        <span class="badge badge-lime" style="margin-bottom:12px">Partner Network</span>
        <h2 class="section-title">Trusted Hospital Network</h2>
        <p class="section-subtitle" style="margin-bottom:0">Certified partner hospitals across Karachi, Lahore &amp; Islamabad</p>
      </div>
      <a href="<?= SITE_URL ?>/hospitals.php" class="btn btn-outline-dark">View All Hospitals &rarr;</a>
    </div>
    <?php
    $db = getDB();
    $featuredHospitals = $db->query('SELECT * FROM hospitals WHERE status="active" ORDER BY rating DESC LIMIT 3')->fetchAll();
    ?>
    <div class="grid-3">
      <?php foreach ($featuredHospitals as $h): ?>
      <div class="card reveal">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:12px">
          <h3 style="font-size:0.97rem;line-height:1.35;max-width:200px"><?= e($h['name']) ?></h3>
          <span class="badge badge-lime" style="flex-shrink:0;margin-left:8px">
            <span class="stars">&#9733;</span> <?= $h['rating'] ?>
          </span>
        </div>
        <div class="text-muted" style="font-size:0.82rem;margin-bottom:6px"><?= e($h['city']) ?><?= $h['address'] ? ' &middot; ' . e($h['address']) : '' ?></div>
        <?php if ($h['hours']): ?>
        <div class="text-muted" style="font-size:0.82rem;margin-bottom:12px"><?= e($h['hours']) ?></div>
        <?php endif; ?>
        <?php if ($h['services']): ?>
        <div style="display:flex;gap:6px;flex-wrap:wrap;margin-bottom:18px">
          <?php foreach (array_slice(explode(',', $h['services']), 0, 2) as $svc): ?>
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

<!-- HOW IT WORKS -->
<section class="section bg-white">
  <div class="container text-center">
    <span class="badge badge-lime" style="margin-bottom:16px">Simple Process</span>
    <h2 class="section-title">Get Vaccinated in 3 Steps</h2>
    <p class="section-subtitle">Quick, easy, and safe vaccination booking — from home to hospital</p>
    <div class="grid-3" style="margin-top:12px">
      <?php
      $steps = [
        ['num'=>'01','title'=>'Find Your Vaccine','desc'=>'Browse our verified list of 25+ WHO-approved vaccines with full details on dosage, age groups, and side effects.'],
        ['num'=>'02','title'=>'Choose a Hospital','desc'=>'Pick from 120+ certified partner hospitals. Filter by city, rating, or available services to find the right fit.'],
        ['num'=>'03','title'=>'Book & Get Protected','desc'=>'Select your preferred date, submit your request, and receive admin confirmation within 24 hours.'],
      ];
      foreach ($steps as $step): ?>
      <div class="card reveal text-center" style="border-top:3px solid var(--accent)">
        <div style="width:56px;height:56px;border-radius:50%;background:var(--dark);color:var(--accent);display:flex;align-items:center;justify-content:center;margin:0 auto 20px;font-size:1rem;font-weight:800;letter-spacing:0.02em"><?= $step['num'] ?></div>
        <h3 style="margin-bottom:10px;font-size:1rem"><?= $step['title'] ?></h3>
        <p class="text-muted" style="font-size:0.87rem;line-height:1.7"><?= $step['desc'] ?></p>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- REVIEWS SLIDER -->
<?php if ($reviews): ?>
<section class="section bg-sage" style="overflow:hidden">
  <div class="container">
    <div style="display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:48px;flex-wrap:wrap;gap:16px">
      <div>
        <span class="badge badge-lime" style="margin-bottom:12px">Patient Stories</span>
        <h2 class="section-title">What Our Patients Say</h2>
        <p class="section-subtitle" style="margin-bottom:0">Real experiences from real families across Pakistan</p>
      </div>
      <div style="display:flex;align-items:center;gap:12px">
        <button id="reviewPrev" aria-label="Previous" style="width:40px;height:40px;border-radius:50%;border:2px solid var(--border);background:#fff;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all 0.2s;flex-shrink:0">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
        </button>
        <button id="reviewNext" aria-label="Next" style="width:40px;height:40px;border-radius:50%;border:none;background:var(--dark);cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all 0.2s;flex-shrink:0">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#90E300" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
        </button>
        <a href="<?= SITE_URL ?>/contact.php#leave-review" class="btn btn-outline-dark">Leave a Review &rarr;</a>
      </div>
    </div>

    <!-- Slider track -->
    <div style="overflow:hidden;position:relative">
      <div id="reviewTrack" style="display:flex;gap:24px;transition:transform 0.5s cubic-bezier(0.25,0.46,0.45,0.94);will-change:transform">
        <?php foreach ($reviews as $r): ?>
        <div class="review-slide" style="min-width:calc(33.333% - 16px);max-width:calc(33.333% - 16px);flex-shrink:0;background:#fff;border-radius:16px;padding:28px;border:1px solid var(--border);box-shadow:0 2px 12px rgba(28,39,6,0.06)">
          <!-- Stars -->
          <div style="display:flex;gap:3px;margin-bottom:16px">
            <?php for ($i=1;$i<=5;$i++): ?>
            <svg width="15" height="15" viewBox="0 0 24 24" fill="<?= $i<=$r['rating']?'#f59e0b':'#e2e8f0' ?>"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            <?php endfor; ?>
          </div>
          <!-- Review text -->
          <p style="font-size:0.9rem;line-height:1.75;color:#333;margin-bottom:20px;font-style:italic;flex:1">"<?= e($r['review_text']) ?>"</p>
          <!-- Author -->
          <div style="display:flex;align-items:center;gap:12px;padding-top:16px;border-top:1px solid var(--border)">
            <div style="width:40px;height:40px;border-radius:50%;background:var(--dark);color:var(--accent);display:flex;align-items:center;justify-content:center;font-weight:800;font-size:0.9rem;flex-shrink:0"><?= strtoupper(substr($r['name'],0,1)) ?></div>
            <div>
              <div style="font-weight:700;font-size:0.9rem"><?= e($r['name']) ?></div>
              <?php if ($r['location']): ?><div class="text-muted" style="font-size:0.78rem"><?= e($r['location']) ?></div><?php endif; ?>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Dots -->
    <div id="reviewDots" style="display:flex;justify-content:center;gap:8px;margin-top:28px"></div>
  </div>
</section>

<script>
(function(){
  var track    = document.getElementById('reviewTrack');
  var dots     = document.getElementById('reviewDots');
  var prevBtn  = document.getElementById('reviewPrev');
  var nextBtn  = document.getElementById('reviewNext');
  var slides   = track ? track.querySelectorAll('.review-slide') : [];
  var total    = slides.length;
  if (!total) return;

  var perView  = window.innerWidth < 640 ? 1 : window.innerWidth < 1024 ? 2 : 3;
  var maxIdx   = Math.max(0, total - perView);
  var current  = 0;
  var autoTimer;

  // Build dots
  for (var d = 0; d <= maxIdx; d++) {
    var dot = document.createElement('button');
    dot.style.cssText = 'width:8px;height:8px;border-radius:50%;border:none;cursor:pointer;transition:all 0.25s;padding:0';
    dot.setAttribute('data-idx', d);
    dot.addEventListener('click', function(){ goTo(parseInt(this.getAttribute('data-idx'))); resetAuto(); });
    dots.appendChild(dot);
  }

  function getSlideWidth() {
    if (!slides[0]) return 0;
    return slides[0].offsetWidth + 24; // card width + gap
  }

  function paintDots() {
    var dotEls = dots.querySelectorAll('button');
    dotEls.forEach(function(d, i){
      d.style.background   = i === current ? '#1C2706' : '#D5DEC5';
      d.style.width        = i === current ? '24px'    : '8px';
      d.style.borderRadius = '100px';
    });
  }

  function goTo(idx) {
    current = Math.max(0, Math.min(idx, maxIdx));
    track.style.transform = 'translateX(-' + (getSlideWidth() * current) + 'px)';
    paintDots();
  }

  function next() { goTo(current >= maxIdx ? 0 : current + 1); }
  function prev() { goTo(current <= 0 ? maxIdx : current - 1); }

  function resetAuto() {
    clearInterval(autoTimer);
    autoTimer = setInterval(next, 5000);
  }

  if (nextBtn) nextBtn.addEventListener('click', function(){ next(); resetAuto(); });
  if (prevBtn) prevBtn.addEventListener('click', function(){ prev(); resetAuto(); });

  // Touch / swipe
  var startX = 0;
  track.addEventListener('touchstart', function(e){ startX = e.touches[0].clientX; }, {passive:true});
  track.addEventListener('touchend', function(e){
    var diff = startX - e.changedTouches[0].clientX;
    if (Math.abs(diff) > 40) { diff > 0 ? next() : prev(); resetAuto(); }
  }, {passive:true});

  // Responsive recalc
  window.addEventListener('resize', function(){
    perView = window.innerWidth < 640 ? 1 : window.innerWidth < 1024 ? 2 : 3;
    maxIdx  = Math.max(0, total - perView);
    goTo(Math.min(current, maxIdx));
  });

  goTo(0);
  resetAuto();
})();
</script>
<?php endif; ?>

<!-- BLOG PREVIEW -->
<section class="section bg-white">
  <div class="container">
    <div style="display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:48px;flex-wrap:wrap;gap:16px">
      <div>
        <span class="badge badge-lime" style="margin-bottom:12px">Latest Articles</span>
        <h2 class="section-title">Vaccination News &amp; Insights</h2>
        <p class="section-subtitle" style="margin-bottom:0">Expert-written health updates from our medical team</p>
      </div>
      <a href="<?= SITE_URL ?>/blog.php" class="btn btn-outline-dark">All Articles &rarr;</a>
    </div>
    <div class="grid-3">
      <?php
      $posts = [
        ['tag'=>'Health Tips', 'color'=>'badge-lime',   'title'=>'Why Annual Flu Shots Are More Important Than You Think',    'date'=>'May 2025',   'min'=>5],
        ['tag'=>'Policy',      'color'=>'badge-blue',   'title'=>"Pakistan's EPI Program: What Every Parent Needs to Know",   'date'=>'April 2025', 'min'=>7],
        ['tag'=>'COVID-19',    'color'=>'badge-orange', 'title'=>'COVID-19 Booster Guide: Who Needs It and When',             'date'=>'April 2025', 'min'=>4],
      ];
      foreach ($posts as $p): ?>
      <div class="card reveal" style="display:flex;flex-direction:column">
        <div style="height:6px;background:var(--accent);border-radius:4px;margin-bottom:20px"></div>
        <span class="badge <?= $p['color'] ?>" style="width:fit-content;margin-bottom:14px"><?= $p['tag'] ?></span>
        <h3 style="font-size:0.97rem;margin-bottom:12px;line-height:1.45;flex:1"><?= $p['title'] ?></h3>
        <div style="display:flex;justify-content:space-between;align-items:center;padding-top:14px;border-top:1px solid var(--border)">
          <span class="text-muted" style="font-size:0.78rem"><?= $p['date'] ?> &middot; <?= $p['min'] ?> min read</span>
          <a href="<?= SITE_URL ?>/blog.php" style="color:#4a7c00;font-weight:600;font-size:0.82rem">Read &rarr;</a>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- NEWSLETTER -->
<section class="section" style="background:var(--dark)">
  <div class="container text-center" style="max-width:580px">
    <h2 style="color:#fff;margin-bottom:8px">Stay Protected. Stay Informed.</h2>
    <p style="color:rgba(255,255,255,0.6);margin-bottom:32px;font-size:0.95rem">Get vaccine reminders, health tips, and hospital updates delivered to your inbox.</p>
    <form style="display:flex;gap:0;border-radius:100px;overflow:hidden;box-shadow:0 8px 32px rgba(0,0,0,0.3);max-width:480px;margin:0 auto">
      <input type="email" placeholder="Enter your email address" style="flex:1;padding:14px 22px;border:none;outline:none;font-size:0.92rem;font-family:inherit;min-width:0">
      <button type="submit" style="padding:14px 28px;background:var(--accent);color:var(--dark);font-weight:700;border:none;cursor:pointer;font-family:inherit;font-size:0.92rem;white-space:nowrap">Subscribe</button>
    </form>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
