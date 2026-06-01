<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';
$pageTitle  = 'Vaccines';
$activePage = 'vaccines';

$db       = getDB();
$vaccines = $db->query('SELECT * FROM vaccines ORDER BY name')->fetchAll();
require __DIR__ . '/includes/header.php';
?>

<section class="hero" style="min-height:45vh">
  <div class="hero-bg">
    <img src="https://images.unsplash.com/photo-1563213126-a4273aed2016?auto=format&fit=crop&w=1920&q=80" alt="Vaccine vials">
    <div class="hero-overlay" style="background:rgba(28,39,6,0.65)"></div>
  </div>
  <div class="hero-blob" style="width:240px;height:240px;background:rgba(144,227,0,0.10);top:30px;right:60px"></div>
  <div class="hero-content text-center" style="padding:60px 5%">
    <h1 style="color:#fff;font-size:clamp(1.8rem,4vw,3rem);margin-bottom:16px">Our Complete Vaccine Directory</h1>
    <p style="color:rgba(255,255,255,0.8);font-size:1.05rem;max-width:520px;margin:0 auto">
      Browse <?= count($vaccines) ?>+ WHO-approved vaccines with dosage schedules, age groups, and availability information.
    </p>
  </div>
</section>

<section class="section bg-white">
  <div class="container">
    <div class="grid-3">
      <?php
      $typeColors = ['mRNA'=>'badge-blue','Inactivated'=>'badge-teal','Live-attenuated'=>'badge-orange','Conjugate'=>'badge-purple','Recombinant'=>'badge-green'];
      foreach ($vaccines as $v):
        $colorClass = $typeColors[$v['type']] ?? 'badge-gray';
      ?>
      <div class="card reveal" style="display:flex;flex-direction:column;<?= $v['status']==='unavailable'?'opacity:0.7':'' ?>">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:12px">
          <span class="badge <?= $colorClass ?>"><?= e($v['type']) ?></span>
          <span class="badge <?= $v['status']==='available'?'badge-lime':'badge-red' ?>"><?= ucfirst($v['status']) ?></span>
        </div>
        <h3 style="font-size:1rem;margin-bottom:10px"><?= e($v['name']) ?></h3>
        <p class="text-muted" style="font-size:0.85rem;line-height:1.6;flex:1;margin-bottom:14px"><?= e($v['description']) ?></p>
        <div style="display:flex;flex-direction:column;gap:6px;padding-top:14px;border-top:1px solid var(--border);margin-bottom:16px">
          <div style="display:flex;justify-content:space-between;font-size:0.82rem">
            <span class="text-muted">Doses</span><strong><?= $v['doses'] ?></strong>
          </div>
          <div style="display:flex;justify-content:space-between;font-size:0.82rem">
            <span class="text-muted">Age Group</span><strong><?= e($v['age_group']) ?></strong>
          </div>
          <?php if ($v['duration']): ?>
          <div style="display:flex;justify-content:space-between;font-size:0.82rem">
            <span class="text-muted">Duration</span><strong><?= e($v['duration']) ?></strong>
          </div>
          <?php endif; ?>
          <?php if ($v['side_effects']): ?>
          <div style="font-size:0.80rem;color:#888;margin-top:4px">Side effects: <?= e($v['side_effects']) ?></div>
          <?php endif; ?>
        </div>
        <a href="<?= SITE_URL ?>/login.php" class="btn btn-primary btn-sm" <?= $v['status']==='unavailable'?'style="opacity:0.5;pointer-events:none"':'' ?>>Find a Hospital &#8594;</a>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- FAQ -->
<section class="section bg-sage">
  <div class="container" style="max-width:760px">
    <h2 class="section-title text-center">Frequently Asked Questions</h2>
    <p class="section-subtitle text-center">Everything you need to know about vaccines</p>
    <div class="accordion-item">
      <div class="accordion-header">How do I know which vaccine I need? <span class="accordion-icon">+</span></div>
      <div class="accordion-body">Consult your doctor for a personalised vaccination schedule. Generally, children follow the Pakistan EPI schedule, while adults need vaccines based on age, health status, and travel plans. VAXORA's hospital staff can guide you on the right vaccines.</div>
    </div>
    <div class="accordion-item">
      <div class="accordion-header">Are vaccines safe for pregnant women? <span class="accordion-icon">+</span></div>
      <div class="accordion-body">Many vaccines are safe and recommended during pregnancy, including the flu vaccine and Tdap. However, live-attenuated vaccines (like MMR and varicella) are generally avoided. Always consult your OB/GYN before vaccination.</div>
    </div>
    <div class="accordion-item">
      <div class="accordion-header">What is the recommended immunization schedule for children in Pakistan? <span class="accordion-icon">+</span></div>
      <div class="accordion-body">Pakistan's EPI schedule includes: BCG and OPV at birth, DTP/OPV/Hep-B/Hib/PCV at 6, 10, 14 weeks, Measles and Vitamin A at 9 months, MMR and DTP booster at 18 months, and DTP/OPV boosters at 4 years.</div>
    </div>
    <div class="accordion-item">
      <div class="accordion-header">Can I get multiple vaccines on the same day? <span class="accordion-icon">+</span></div>
      <div class="accordion-body">Yes, most vaccines can be administered on the same day. This is a standard practice for children following the EPI schedule. Your healthcare provider will advise based on the specific vaccines and your health condition.</div>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
