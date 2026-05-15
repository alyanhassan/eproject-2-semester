<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';
$pageTitle  = 'Home';
$activePage = 'home';
require __DIR__ . '/includes/header.php';
?>

<!-- HERO -->
<section class="hero">
  <div class="hero-bg">
    <img src="https://images.unsplash.com/photo-1584820927498-cfe5211fd8bf?auto=format&fit=crop&w=1920&q=80" alt="Doctor vaccinating child">
    <div class="hero-overlay" style="background:linear-gradient(135deg,rgba(28,39,6,0.72) 0%,rgba(28,39,6,0.35) 100%)"></div>
  </div>
  <div class="hero-blob" style="width:320px;height:320px;background:rgba(144,227,0,0.10);top:60px;right:80px"></div>
  <div class="hero-blob" style="width:200px;height:200px;background:rgba(144,227,0,0.07);bottom:40px;left:40px"></div>
  <div class="hero-content">
    <div style="max-width:640px">
      <span class="badge badge-lime" style="margin-bottom:20px;display:inline-block">WHO-Approved · Pakistan EPI Partner</span>
      <h1 style="font-size:clamp(2.2rem,5vw,3.6rem);color:#fff;margin-bottom:20px;line-height:1.1">
        Protecting Lives, One Vaccine at a Time
      </h1>
      <p style="font-size:1.1rem;color:rgba(255,255,255,0.85);margin-bottom:36px;max-width:520px">
        VAXORA connects you to trusted hospitals and verified vaccines — making immunization easy, transparent, and accessible for every family.
      </p>
      <div style="display:flex;gap:16px;flex-wrap:wrap">
        <a href="<?= SITE_URL ?>/hospitals.php" class="btn btn-primary">Find a Hospital</a>
        <a href="<?= SITE_URL ?>/vaccines.php" class="btn btn-outline">View Vaccines</a>
      </div>
    </div>
  </div>
</section>

<!-- TRUST BADGES -->
<div class="stats-banner">
  <div class="stat-item"><div class="stat-number" data-countup="50000" data-suffix="+">50000+</div><div class="stat-label">Doses Administered</div></div>
  <div class="stat-item"><div class="stat-number" data-countup="120" data-suffix="+">120+</div><div class="stat-label">Partner Hospitals</div></div>
  <div class="stat-item"><div class="stat-number" data-countup="25" data-suffix="+">25+</div><div class="stat-label">Vaccines Available</div></div>
  <div class="stat-item"><div class="stat-number" data-countup="98" data-suffix="%">98%</div><div class="stat-label">Patient Satisfaction</div></div>
</div>

<!-- VACCINES OVERVIEW -->
<section class="section bg-white">
  <div class="container">
    <div style="display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:40px;flex-wrap:wrap;gap:12px">
      <div>
        <h2 class="section-title">Our Vaccine Portfolio</h2>
        <p class="section-subtitle" style="margin-bottom:0">WHO-approved vaccines with full dosage & age information</p>
      </div>
      <a href="<?= SITE_URL ?>/vaccines.php" class="btn btn-outline-dark">See All 25+ Vaccines &#8594;</a>
    </div>
    <div class="grid-4">
      <?php
      $vaxList=[
        ['icon'=>'&#128165;','name'=>'COVID-19 Vaccine','desc'=>'mRNA based, 2-dose schedule, age 12+','color'=>'badge-blue'],
        ['icon'=>'&#129440;','name'=>'Flu (Influenza)','desc'=>'Annual shot, all ages, seasonal','color'=>'badge-teal'],
        ['icon'=>'&#128286;','name'=>'MMR Vaccine','desc'=>'Measles, Mumps & Rubella, age 1+','color'=>'badge-orange'],
        ['icon'=>'&#128138;','name'=>'Hepatitis B','desc'=>'3-dose series, newborns & adults','color'=>'badge-purple'],
      ];
      foreach ($vaxList as $v): ?>
      <div class="card reveal">
        <div style="font-size:2.2rem;margin-bottom:12px"><?= $v['icon'] ?></div>
        <span class="badge <?= $v['color'] ?>" style="margin-bottom:12px"><?= $v['color']==='badge-blue'?'mRNA':($v['color']==='badge-teal'?'Inactivated':($v['color']==='badge-orange'?'Live-attenuated':'Recombinant')) ?></span>
        <h3 style="font-size:1rem;margin-bottom:8px"><?= $v['name'] ?></h3>
        <p class="text-muted" style="font-size:0.85rem;flex:1"><?= $v['desc'] ?></p>
        <a href="<?= SITE_URL ?>/vaccines.php" style="color:#4a7c00;font-weight:600;font-size:0.85rem;margin-top:12px;display:block">Learn More &#8594;</a>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- HOSPITALS -->
<section class="section bg-sage">
  <div class="container">
    <div style="display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:40px;flex-wrap:wrap;gap:12px">
      <div>
        <h2 class="section-title">Trusted Hospital Network</h2>
        <p class="section-subtitle" style="margin-bottom:0">Certified partner hospitals across Pakistan</p>
      </div>
      <a href="<?= SITE_URL ?>/hospitals.php" class="btn btn-outline-dark">View All 120+ Hospitals &#8594;</a>
    </div>
    <div class="grid-3">
      <?php
      $hospitals=[
        ['name'=>'Aga Khan University Hospital','city'=>'Karachi','services'=>'24/7 Vaccination Center','rating'=>4.9],
        ['name'=>'Liaquat National Hospital','city'=>'Karachi','services'=>'Pediatric & Adult Vaccines','rating'=>4.7],
        ['name'=>'Shaukat Khanum Memorial','city'=>'Lahore','services'=>'Cancer Prevention Vaccines','rating'=>4.8],
      ];
      foreach ($hospitals as $h): ?>
      <div class="card reveal">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:12px">
          <h3 style="font-size:0.98rem"><?= $h['name'] ?></h3>
          <span class="badge badge-lime"><span class="stars">&#9733;</span> <?= $h['rating'] ?></span>
        </div>
        <div class="text-muted" style="font-size:0.82rem;margin-bottom:10px">&#127979; <?= $h['city'] ?></div>
        <div class="pill-tag" style="margin-bottom:16px"><?= $h['services'] ?></div>
        <a href="<?= SITE_URL ?>/login.php" class="btn btn-primary btn-sm">Book Here &#8594;</a>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- HOW IT WORKS -->
<section class="section bg-white">
  <div class="container text-center">
    <h2 class="section-title">Get Vaccinated in 3 Simple Steps</h2>
    <p class="section-subtitle">Quick, easy, and safe vaccination booking</p>
    <div class="grid-3" style="margin-top:20px">
      <?php
      $steps=[
        ['num'=>'01','icon'=>'&#128269;','title'=>'Find Your Vaccine','desc'=>'Browse our verified list of 25+ WHO-approved vaccines with full details on dosage and age groups.'],
        ['num'=>'02','icon'=>'&#127968;','title'=>'Choose a Hospital','desc'=>'Pick from 120+ certified partner hospitals near you — filter by city, rating, or services.'],
        ['num'=>'03','icon'=>'&#128197;','title'=>'Book Your Appointment','desc'=>'Select a date, confirm your booking, and get protected. Approval within 24 hours.'],
      ];
      foreach ($steps as $step): ?>
      <div class="card reveal text-center">
        <div style="width:64px;height:64px;background:rgba(144,227,0,0.12);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-size:1.8rem"><?= $step['icon'] ?></div>
        <div style="font-size:0.72rem;font-weight:800;letter-spacing:0.1em;color:var(--accent);margin-bottom:8px">STEP <?= $step['num'] ?></div>
        <h3 style="margin-bottom:10px;font-size:1rem"><?= $step['title'] ?></h3>
        <p class="text-muted" style="font-size:0.88rem"><?= $step['desc'] ?></p>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- TESTIMONIALS -->
<section class="section bg-sage">
  <div class="container">
    <h2 class="section-title text-center">What Our Patients Say</h2>
    <p class="section-subtitle text-center">Real stories from real families</p>
    <div class="grid-3">
      <?php
      $testimonials=[
        ['name'=>'Ayesha Khan','role'=>'Mother of 2','quote'=>'VAXORA made scheduling my children\'s MMR vaccines so easy. The hospital was clean, staff was kind, and the whole process took under 30 minutes.','initial'=>'A'],
        ['name'=>'Dr. Usman Tariq','role'=>'General Practitioner','quote'=>'As a doctor, I recommend VAXORA to all my patients. The vaccine information is accurate, up-to-date, and the hospital network is excellent.','initial'=>'U'],
        ['name'=>'Sara Ahmed','role'=>'University Student','quote'=>'Got my Hepatitis B booster through VAXORA. Booked online in 2 minutes and walked into the clinic with zero wait time. Incredible service.','initial'=>'S'],
      ];
      foreach ($testimonials as $t): ?>
      <div class="card reveal">
        <div class="stars" style="margin-bottom:12px">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
        <p style="font-size:0.9rem;line-height:1.7;color:#333;margin-bottom:20px;font-style:italic">"<?= $t['quote'] ?>"</p>
        <div style="display:flex;align-items:center;gap:12px">
          <div style="width:44px;height:44px;border-radius:50%;background:var(--dark);color:var(--accent);display:flex;align-items:center;justify-content:center;font-weight:800;font-size:1rem;flex-shrink:0"><?= $t['initial'] ?></div>
          <div><strong style="font-size:0.9rem"><?= $t['name'] ?></strong><div class="text-muted" style="font-size:0.8rem"><?= $t['role'] ?></div></div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- BLOG PREVIEW -->
<section class="section bg-white">
  <div class="container">
    <div style="display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:40px;flex-wrap:wrap;gap:12px">
      <div>
        <h2 class="section-title">Latest in Vaccination News</h2>
        <p class="section-subtitle" style="margin-bottom:0">Stay informed with health updates from our experts</p>
      </div>
      <a href="<?= SITE_URL ?>/blog.php" class="btn btn-outline-dark">View All Articles &#8594;</a>
    </div>
    <div class="grid-3">
      <?php
      $posts=[
        ['tag'=>'Health Tips','title'=>'Why Annual Flu Shots Are More Important Than You Think','date'=>'May 2025','min'=>5],
        ['tag'=>'Policy','title'=>"Pakistan's National Immunization Program: What Parents Need to Know",'date'=>'April 2025','min'=>7],
        ['tag'=>'COVID-19','title'=>'COVID-19 Booster Guide: Who Needs It and When','date'=>'April 2025','min'=>4],
      ];
      foreach ($posts as $p): ?>
      <div class="card reveal">
        <span class="badge badge-lime" style="margin-bottom:12px"><?= $p['tag'] ?></span>
        <h3 style="font-size:0.98rem;margin-bottom:12px;line-height:1.4"><?= $p['title'] ?></h3>
        <div class="text-muted" style="font-size:0.82rem;margin-bottom:16px"><?= $p['date'] ?> · <?= $p['min'] ?> min read</div>
        <a href="<?= SITE_URL ?>/blog.php" style="color:#4a7c00;font-weight:600;font-size:0.85rem">Read More &#8594;</a>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- NEWSLETTER -->
<section class="section" style="background:linear-gradient(135deg,#90E300,#6db300)">
  <div class="container text-center" style="max-width:600px">
    <h2 style="color:var(--dark);margin-bottom:8px">Stay Protected. Stay Informed.</h2>
    <p style="color:rgba(28,39,6,0.75);margin-bottom:32px">Get vaccine reminders, health tips, and hospital updates right in your inbox.</p>
    <form style="display:flex;gap:0;border-radius:100px;overflow:hidden;box-shadow:0 8px 32px rgba(28,39,6,0.2)">
      <input type="email" placeholder="Enter your email address" style="flex:1;padding:14px 22px;border:none;outline:none;font-size:0.95rem;font-family:inherit">
      <button type="submit" style="padding:14px 28px;background:var(--dark);color:var(--accent);font-weight:700;border:none;cursor:pointer;font-family:inherit;font-size:0.95rem">Subscribe</button>
    </form>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
