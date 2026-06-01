<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';
$pageTitle  = 'About Us';
$activePage = 'about';
require __DIR__ . '/includes/header.php';
?>

<section class="hero" style="min-height:48vh">
  <div class="hero-bg">
    <img src="https://images.unsplash.com/photo-1576091160550-2173dba999ef?auto=format&fit=crop&w=1920&q=80" alt="Healthcare team">
    <div class="hero-overlay" style="background:linear-gradient(135deg,rgba(28,39,6,0.7) 0%,rgba(28,39,6,0.45) 100%)"></div>
  </div>
  <div class="hero-content text-center" style="padding:70px 5%">
    <span class="badge badge-lime" style="margin-bottom:16px">Our Story</span>
    <h1 style="color:#fff;font-size:clamp(2rem,4vw,3rem);margin-bottom:16px">Our Mission: A Vaccinated Pakistan</h1>
    <p style="color:rgba(255,255,255,0.82);font-size:1rem;max-width:580px;margin:0 auto;line-height:1.75">
      VAXORA bridges the gap between families and trusted vaccination centers — making immunization easy, transparent, and accessible for every Pakistani.
    </p>
  </div>
</section>

<!-- Story -->
<section class="section bg-white">
  <div class="container">
    <div class="grid-2" style="align-items:center;gap:60px">
      <div>
        <span class="badge badge-lime" style="margin-bottom:16px">Founded 2023</span>
        <h2 class="section-title">Founded with Purpose,<br>Driven by Impact</h2>
        <p style="color:#555;line-height:1.8;margin-bottom:16px">Founded by a team of public health professionals and technology entrepreneurs in Karachi, VAXORA was born from a clear observation: millions of Pakistani families struggled to find accurate vaccine information and trusted vaccination centers.</p>
        <p style="color:#555;line-height:1.8;margin-bottom:28px">We partnered with WHO-approved hospitals and the National EPI program to build Pakistan's first comprehensive vaccination booking platform — connecting families, hospitals, and vaccines in one trusted place.</p>
        <div style="display:flex;gap:32px;flex-wrap:wrap">
          <div><div style="font-size:2rem;font-weight:800;color:var(--accent)">2023</div><div class="text-muted" style="font-size:0.84rem">Year Founded</div></div>
          <div><div style="font-size:2rem;font-weight:800;color:var(--accent)">120+</div><div class="text-muted" style="font-size:0.84rem">Partner Hospitals</div></div>
          <div><div style="font-size:2rem;font-weight:800;color:var(--accent)">50K+</div><div class="text-muted" style="font-size:0.84rem">Vaccinations Done</div></div>
        </div>
      </div>
      <div style="background:#f8faf2;border-radius:16px;padding:48px;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:20px;min-height:280px">
        <div style="width:72px;height:72px;border-radius:50%;background:var(--dark);display:flex;align-items:center;justify-content:center">
          <svg width="32" height="32" viewBox="0 0 28 28" fill="none"><path d="M14 2L4 7v7c0 5.5 4.3 10.7 10 12 5.7-1.3 10-6.5 10-12V7L14 2z" fill="#90E300" opacity="0.25" stroke="#90E300" stroke-width="1.5"/><path d="M14 8v12M8 14h12" stroke="#90E300" stroke-width="2" stroke-linecap="round"/></svg>
        </div>
        <div class="text-center">
          <h3 style="margin-bottom:6px">Karachi, Pakistan</h3>
          <p class="text-muted" style="font-size:0.88rem">Headquartered in Pakistan's largest city, serving families nationwide.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Team -->
<section class="section bg-sage">
  <div class="container">
    <div class="text-center" style="margin-bottom:48px">
      <span class="badge badge-lime" style="margin-bottom:12px">Leadership</span>
      <h2 class="section-title">Our Leadership Team</h2>
      <p class="section-subtitle" style="margin-bottom:0">Public health experts and technology leaders united by a single mission</p>
    </div>
    <div class="grid-4">
      <?php
      $team = [
        ['name'=>'Dr. Fatima Malik', 'role'=>'Chief Medical Officer', 'creds'=>'MBBS, MPH (Johns Hopkins)',  'initial'=>'F', 'color'=>'#90E300', 'dark'=>true],
        ['name'=>'Ahmed Raza',       'role'=>'CEO & Co-Founder',      'creds'=>'Health-tech entrepreneur',   'initial'=>'A', 'color'=>'#1C2706', 'dark'=>false],
        ['name'=>'Dr. Bilal Hussain','role'=>'Head of Vaccine Research','creds'=>'Epidemiologist, 15+ years','initial'=>'B', 'color'=>'#2467E3', 'dark'=>false],
        ['name'=>'Nadia Siddiqui',   'role'=>'Head of Partnerships',  'creds'=>'120+ hospital network',      'initial'=>'N', 'color'=>'#8b5cf6', 'dark'=>false],
      ];
      foreach ($team as $m): ?>
      <div class="card reveal text-center">
        <div style="width:68px;height:68px;border-radius:50%;background:<?= $m['color'] ?>;color:<?= $m['dark']?'#1C2706':'#fff' ?>;display:flex;align-items:center;justify-content:center;font-size:1.5rem;font-weight:800;margin:0 auto 16px"><?= $m['initial'] ?></div>
        <h3 style="font-size:0.93rem;margin-bottom:4px"><?= $m['name'] ?></h3>
        <div style="color:var(--accent);font-size:0.8rem;font-weight:600;margin-bottom:6px"><?= $m['role'] ?></div>
        <div class="text-muted" style="font-size:0.79rem"><?= $m['creds'] ?></div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Values -->
<section class="section bg-dark">
  <div class="container">
    <div class="text-center" style="margin-bottom:48px">
      <h2 style="color:#fff;margin-bottom:8px">Our Core Values</h2>
      <p style="color:rgba(255,255,255,0.55);margin-bottom:0">The principles that guide everything we do</p>
    </div>
    <div class="grid-4">
      <?php
      $values = [
        ['title'=>'Safety',        'desc'=>'Every hospital and vaccine on our platform is verified and certified by national health authorities.',
         'icon'=>'<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#90E300" stroke-width="1.8"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>'],
        ['title'=>'Transparency',  'desc'=>'Full information about vaccines, hospitals, dosages, and processes — no hidden details, ever.',
         'icon'=>'<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#90E300" stroke-width="1.8"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>'],
        ['title'=>'Accessibility', 'desc'=>'Making vaccination easy and available for every Pakistani family, regardless of location or background.',
         'icon'=>'<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#90E300" stroke-width="1.8"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>'],
        ['title'=>'Community',     'desc'=>'Building a healthier, stronger Pakistan together — one family, one vaccination at a time.',
         'icon'=>'<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#90E300" stroke-width="1.8"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/></svg>'],
      ];
      foreach ($values as $v): ?>
      <div class="reveal text-center" style="padding:28px 16px">
        <div style="width:60px;height:60px;border-radius:14px;background:rgba(144,227,0,0.1);display:flex;align-items:center;justify-content:center;margin:0 auto 16px"><?= $v['icon'] ?></div>
        <h3 style="color:var(--accent);margin-bottom:8px;font-size:0.97rem"><?= $v['title'] ?></h3>
        <p style="color:rgba(255,255,255,0.55);font-size:0.85rem;line-height:1.7"><?= $v['desc'] ?></p>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Certifications -->
<section class="section bg-white">
  <div class="container text-center">
    <span class="badge badge-lime" style="margin-bottom:16px">Trusted By</span>
    <h2 class="section-title">Certifications &amp; Partners</h2>
    <p class="section-subtitle">Accredited by Pakistan's leading health organisations</p>
    <div class="grid-4">
      <?php
      $certs = [
        ['name'=>'WHO Partner Program',  'desc'=>'World Health Organization',          'icon'=>'<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z"/></svg>'],
        ['name'=>'Pakistan EPI',         'desc'=>'National Immunization Program',      'icon'=>'<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>'],
        ['name'=>'UNICEF Initiative',    'desc'=>'Immunization Initiative Partner',    'icon'=>'<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>'],
        ['name'=>'PMDC Registered',      'desc'=>'Pakistan Medical & Dental Council',  'icon'=>'<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>'],
      ];
      foreach ($certs as $c): ?>
      <div class="card reveal text-center">
        <div style="width:56px;height:56px;border-radius:50%;background:rgba(144,227,0,0.1);display:flex;align-items:center;justify-content:center;margin:0 auto 16px;color:#4a7c00"><?= $c['icon'] ?></div>
        <h3 style="font-size:0.93rem;margin-bottom:4px"><?= $c['name'] ?></h3>
        <div class="text-muted" style="font-size:0.8rem"><?= $c['desc'] ?></div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
