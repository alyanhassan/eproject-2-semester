<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';
$pageTitle  = 'About Us';
$activePage = 'about';
require __DIR__ . '/includes/header.php';
?>

<section class="hero" style="min-height:50vh">
  <div class="hero-bg">
    <img src="https://images.unsplash.com/photo-1576091160550-2173dba999ef?auto=format&fit=crop&w=1920&q=80" alt="Healthcare team">
    <div class="hero-overlay" style="background:rgba(28,39,6,0.55)"></div>
  </div>
  <div class="hero-content text-center" style="padding:70px 5%">
    <h1 style="color:#fff;font-size:clamp(2rem,4vw,3rem);margin-bottom:16px">Our Mission: A Vaccinated Pakistan</h1>
    <p style="color:rgba(255,255,255,0.8);font-size:1.05rem;max-width:600px;margin:0 auto">
      VAXORA was founded to bridge the gap between patients and trusted vaccination centers. We believe no one should miss a life-saving vaccine due to lack of information or access.
    </p>
  </div>
</section>

<!-- Story -->
<section class="section bg-white">
  <div class="container">
    <div class="grid-2" style="align-items:center;gap:60px">
      <div>
        <span class="badge badge-lime" style="margin-bottom:16px">Our Story</span>
        <h2 class="section-title">Founded with Purpose, Driven by Impact</h2>
        <p style="color:#555;line-height:1.8;margin-bottom:16px">Founded in 2023 by a team of public health professionals and tech entrepreneurs in Karachi, VAXORA was born from a simple observation: millions of Pakistani families struggled to find accurate vaccine information and trusted vaccination centers.</p>
        <p style="color:#555;line-height:1.8;margin-bottom:24px">We partnered with WHO-approved hospitals and the National EPI program to build Pakistan's first comprehensive vaccination booking platform — connecting families, hospitals, and vaccines in one trusted place.</p>
        <div style="display:flex;gap:24px;flex-wrap:wrap">
          <div><div style="font-size:1.8rem;font-weight:800;color:var(--accent)">2023</div><div class="text-muted" style="font-size:0.85rem">Year Founded</div></div>
          <div><div style="font-size:1.8rem;font-weight:800;color:var(--accent)">120+</div><div class="text-muted" style="font-size:0.85rem">Partner Hospitals</div></div>
          <div><div style="font-size:1.8rem;font-weight:800;color:var(--accent)">50K+</div><div class="text-muted" style="font-size:0.85rem">Vaccinations Done</div></div>
        </div>
      </div>
      <div style="background:#f8faf2;border-radius:16px;padding:40px;text-align:center">
        <div style="font-size:5rem;margin-bottom:16px">&#127973;</div>
        <h3 style="margin-bottom:12px">Karachi, Pakistan</h3>
        <p class="text-muted">Headquartered in Pakistan's largest city, serving families across the nation.</p>
      </div>
    </div>
  </div>
</section>

<!-- Team -->
<section class="section bg-sage">
  <div class="container">
    <h2 class="section-title text-center">Our Leadership Team</h2>
    <p class="section-subtitle text-center">Public health experts and technology leaders united by a common mission</p>
    <div class="grid-4">
      <?php
      $team = [
        ['name'=>'Dr. Fatima Malik','role'=>'Chief Medical Officer','creds'=>'MBBS, MPH (Johns Hopkins)','initial'=>'F','color'=>'#90E300'],
        ['name'=>'Ahmed Raza','role'=>'CEO & Co-Founder','creds'=>'Health-tech entrepreneur','initial'=>'A','color'=>'#2467E3'],
        ['name'=>'Dr. Bilal Hussain','role'=>'Head of Vaccine Research','creds'=>'Epidemiologist, 15+ years','initial'=>'B','color'=>'#f59e0b'],
        ['name'=>'Nadia Siddiqui','role'=>'Head of Partnerships','creds'=>'120+ hospital network','initial'=>'N','color'=>'#8b5cf6'],
      ];
      foreach ($team as $m): ?>
      <div class="card reveal text-center">
        <div style="width:72px;height:72px;border-radius:50%;background:<?= $m['color'] ?>;color:<?= $m['color']==='#90E300'?'#1C2706':'#fff' ?>;display:flex;align-items:center;justify-content:center;font-size:1.6rem;font-weight:800;margin:0 auto 16px"><?= $m['initial'] ?></div>
        <h3 style="font-size:0.95rem;margin-bottom:4px"><?= $m['name'] ?></h3>
        <div style="color:var(--accent);font-size:0.82rem;font-weight:600;margin-bottom:6px"><?= $m['role'] ?></div>
        <div class="text-muted" style="font-size:0.8rem"><?= $m['creds'] ?></div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Values -->
<section class="section bg-dark">
  <div class="container">
    <h2 style="color:#fff;text-align:center;margin-bottom:8px">Our Core Values</h2>
    <p style="color:rgba(255,255,255,0.6);text-align:center;margin-bottom:48px">The principles that guide everything we do</p>
    <div class="grid-4">
      <?php
      $values=[
        ['icon'=>'&#128737;','title'=>'Safety','desc'=>'Every hospital and vaccine on our platform is verified and certified by health authorities.'],
        ['icon'=>'&#128270;','title'=>'Transparency','desc'=>'Full information about vaccines, hospitals, and processes — no hidden details.'],
        ['icon'=>'&#127968;','title'=>'Accessibility','desc'=>'Making vaccination easy and available for every Pakistani family, regardless of location.'],
        ['icon'=>'&#129309;','title'=>'Community','desc'=>'Building a healthier Pakistan together, one vaccination at a time.'],
      ];
      foreach ($values as $v): ?>
      <div class="reveal" style="text-align:center;padding:24px">
        <div style="font-size:2.4rem;margin-bottom:12px"><?= $v['icon'] ?></div>
        <h3 style="color:var(--accent);margin-bottom:8px"><?= $v['title'] ?></h3>
        <p style="color:rgba(255,255,255,0.6);font-size:0.88rem;line-height:1.6"><?= $v['desc'] ?></p>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Certifications -->
<section class="section bg-white">
  <div class="container text-center">
    <h2 class="section-title">Certifications & Partners</h2>
    <p class="section-subtitle">Trusted by Pakistan's leading health organisations</p>
    <div class="grid-4">
      <?php
      $certs=[
        ['icon'=>'&#127758;','name'=>'WHO Partner Program','desc'=>'World Health Organization'],
        ['icon'=>'&#127973;','name'=>'Pakistan EPI','desc'=>'National Immunization Program'],
        ['icon'=>'&#127971;','name'=>'UNICEF Initiative','desc'=>'Immunization Initiative Partner'],
        ['icon'=>'&#9877;','name'=>'PMDC Registered','desc'=>'Pakistan Medical & Dental Council'],
      ];
      foreach ($certs as $c): ?>
      <div class="card reveal text-center">
        <div style="font-size:2.4rem;margin-bottom:12px"><?= $c['icon'] ?></div>
        <h3 style="font-size:0.95rem;margin-bottom:4px"><?= $c['name'] ?></h3>
        <div class="text-muted" style="font-size:0.8rem"><?= $c['desc'] ?></div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
