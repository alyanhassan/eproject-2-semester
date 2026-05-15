<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';
$pageTitle  = 'Blog';
$activePage = 'blog';
require __DIR__ . '/includes/header.php';
?>

<section class="hero" style="min-height:40vh">
  <div class="hero-bg">
    <img src="https://images.unsplash.com/photo-1576671081837-49000212a370?auto=format&fit=crop&w=1920&q=80" alt="Doctor with tablet">
    <div class="hero-overlay" style="background:rgba(248,250,242,0.82)"></div>
  </div>
  <div class="hero-content text-center" style="padding:60px 5%">
    <h1 style="color:var(--dark);font-size:clamp(1.8rem,4vw,3rem);margin-bottom:16px">Health News & Vaccination Insights</h1>
    <p style="color:var(--muted);font-size:1.05rem;max-width:520px;margin:0 auto">
      Expert-written articles on vaccines, public health, and staying protected in Pakistan.
    </p>
  </div>
</section>

<section class="section bg-white">
  <div class="container">
    <div class="grid-3">
      <?php
      $posts = [
        ['tag'=>'Health Tips','color'=>'badge-lime','title'=>'Why Annual Flu Shots Are More Important Than You Think','date'=>'15 May 2025','min'=>5,'summary'=>'As winter approaches, many Pakistanis skip their annual flu shot — a decision that can have serious consequences for the elderly and immunocompromised.'],
        ['tag'=>'Policy','color'=>'badge-blue','title'=>"Pakistan's National Immunization Program: What Parents Need to Know",'date'=>'20 April 2025','min'=>7,'summary'=>'The EPI program protects Pakistani children from 10 deadly diseases. Here\'s everything parents need to know about the schedule, vaccines, and where to get them.'],
        ['tag'=>'COVID-19','color'=>'badge-orange','title'=>'COVID-19 Booster Guide: Who Needs It and When','date'=>'10 April 2025','min'=>4,'summary'=>'With new variants emerging, COVID-19 boosters remain important for high-risk groups. Learn who qualifies and where to get your booster in Pakistan.'],
        ['tag'=>'Travel Health','color'=>'badge-purple','title'=>"Travel to Hajj? Here's Your Meningococcal Vaccine Guide",'date'=>'22 March 2025','min'=>6,'summary'=>'Meningococcal vaccination is mandatory for Hajj pilgrims. Find out which vaccine you need, the schedule, and where to get vaccinated before your journey.'],
        ['tag'=>"Women's Health",'color'=>'badge-red','title'=>'HPV Vaccine: Myths vs. Facts','date'=>'10 March 2025','min'=>5,'summary'=>'Despite being one of the most effective cancer-prevention tools, HPV vaccine is surrounded by myths. We separate fact from fiction with expert guidance.'],
        ['tag'=>'Pediatric','color'=>'badge-teal','title'=>'Childhood Immunization Schedule: A Complete Pakistan Guide','date'=>'28 Feb 2025','min'=>8,'summary'=>'A complete, easy-to-follow guide to Pakistan\'s childhood immunization schedule from birth to age 5 — with vaccine names, timings, and where to go.'],
      ];
      foreach ($posts as $p): ?>
      <div class="card reveal" style="display:flex;flex-direction:column">
        <div style="height:140px;background:linear-gradient(135deg,var(--bg),var(--border));border-radius:10px;margin-bottom:16px;display:flex;align-items:center;justify-content:center;font-size:3rem">
          <?= $p['tag']==='Health Tips'?'&#128137;':($p['tag']==='Policy'?'&#128196;':($p['tag']==='COVID-19'?'&#128165;':($p['tag']==='Travel Health'?'&#9992;':($p['tag']==="Women's Health"?'&#128167;':'&#128100;')))) ?>
        </div>
        <span class="badge <?= $p['color'] ?>" style="margin-bottom:12px;width:fit-content"><?= $p['tag'] ?></span>
        <h3 style="font-size:0.95rem;margin-bottom:10px;line-height:1.45;flex:1"><?= $p['title'] ?></h3>
        <p class="text-muted" style="font-size:0.83rem;line-height:1.6;margin-bottom:14px"><?= $p['summary'] ?></p>
        <div style="display:flex;justify-content:space-between;align-items:center">
          <span class="text-muted" style="font-size:0.78rem"><?= $p['date'] ?> · <?= $p['min'] ?> min read</span>
          <a href="#" style="color:#4a7c00;font-weight:600;font-size:0.82rem">Read More &#8594;</a>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
