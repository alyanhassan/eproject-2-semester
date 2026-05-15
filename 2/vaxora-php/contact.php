<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';
$pageTitle  = 'Contact';
$activePage = 'contact';

$success = $error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $phone   = trim($_POST['phone'] ?? '');
    $city    = trim($_POST['city'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (!$name || !$email || !$message) {
        $error = 'Please fill in all required fields.';
    } else {
        $db = getDB();
        $db->prepare('INSERT INTO contacts (name,email,phone,city,message) VALUES (?,?,?,?,?)')
           ->execute([$name, $email, $phone, $city, $message]);
        $success = 'Thank you! Your message has been sent. We will get back to you within 24 hours.';
    }
}
require __DIR__ . '/includes/header.php';
?>

<section class="hero" style="min-height:40vh">
  <div class="hero-bg">
    <img src="https://images.unsplash.com/photo-1551601651-2a8555f1a136?auto=format&fit=crop&w=1920&q=80" alt="Clinic reception">
    <div class="hero-overlay" style="background:linear-gradient(135deg,rgba(144,227,0,0.5) 0%,rgba(28,39,6,0.8) 100%)"></div>
  </div>
  <div class="hero-content text-center" style="padding:60px 5%">
    <h1 style="color:#fff;font-size:clamp(1.8rem,4vw,3rem);margin-bottom:16px">Get in Touch With VAXORA</h1>
    <p style="color:rgba(255,255,255,0.85);font-size:1.05rem;max-width:480px;margin:0 auto">
      We're here to help with vaccine information, hospital bookings, and any questions you have.
    </p>
  </div>
</section>

<section class="section bg-white">
  <div class="container">
    <div class="grid-2" style="gap:48px">
      <!-- Form -->
      <div class="card">
        <h2 style="font-size:1.3rem;margin-bottom:6px">Send Us a Message</h2>
        <p class="text-muted" style="font-size:0.9rem;margin-bottom:24px">We typically respond within 24 hours.</p>

        <?php if ($success): ?><div class="alert alert-success alert-auto"><?= e($success) ?></div><?php endif; ?>
        <?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>

        <form method="POST">
          <div class="form-row">
            <div class="form-group"><label class="form-label">Full Name *</label><input type="text" name="name" class="form-control" value="<?= e($_POST['name']??'') ?>" required></div>
            <div class="form-group"><label class="form-label">Email Address *</label><input type="email" name="email" class="form-control" value="<?= e($_POST['email']??'') ?>" required></div>
          </div>
          <div class="form-row">
            <div class="form-group"><label class="form-label">Phone</label><input type="tel" name="phone" class="form-control" value="<?= e($_POST['phone']??'') ?>" placeholder="0300-0000000"></div>
            <div class="form-group"><label class="form-label">City</label><input type="text" name="city" class="form-control" value="<?= e($_POST['city']??'') ?>" placeholder="Karachi"></div>
          </div>
          <div class="form-group"><label class="form-label">Message *</label><textarea name="message" class="form-control" rows="5" required placeholder="Tell us how we can help..."><?= e($_POST['message']??'') ?></textarea></div>
          <button type="submit" class="btn btn-primary">Send Message</button>
        </form>
      </div>

      <!-- Contact Info -->
      <div>
        <div class="card mb-16">
          <h3 style="margin-bottom:20px;font-size:1.05rem">Contact Information</h3>
          <?php
          $info=[
            ['icon'=>'&#128205;','label'=>'Office','value'=>'Plot 12, Block 5, Clifton, Karachi, Pakistan'],
            ['icon'=>'&#128222;','label'=>'Helpline','value'=>'0800-VAXORA (0800-829672) — Mon–Sat, 9am–6pm'],
            ['icon'=>'&#128140;','label'=>'Email','value'=>'hello@vaxora.pk'],
            ['icon'=>'&#128172;','label'=>'WhatsApp','value'=>'+92-300-VAXORA'],
          ];
          foreach ($info as $i): ?>
          <div style="display:flex;gap:14px;margin-bottom:18px">
            <div style="width:40px;height:40px;border-radius:10px;background:rgba(144,227,0,0.12);display:flex;align-items:center;justify-content:center;font-size:1.2rem;flex-shrink:0"><?= $i['icon'] ?></div>
            <div><div style="font-size:0.78rem;color:var(--muted);font-weight:600;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:2px"><?= $i['label'] ?></div>
            <div style="font-size:0.9rem"><?= $i['value'] ?></div></div>
          </div>
          <?php endforeach; ?>
        </div>

        <!-- FAQ -->
        <div class="card" style="padding:24px">
          <h3 style="margin-bottom:20px;font-size:1.05rem">Common Questions</h3>
          <?php
          $faqs=[
            ['q'=>'How do I book an appointment?','a'=>'Register or log in, go to "Book Hospital", select your child, vaccine and preferred hospital, then pick a date. Admin approves within 24 hours.'],
            ['q'=>'Can I cancel or reschedule?','a'=>'Yes — you can cancel pending appointments from your dashboard. Contact us to reschedule.'],
            ['q'=>'Do you accept walk-ins?','a'=>'Walk-ins depend on each hospital. We recommend booking in advance to guarantee availability.'],
            ['q'=>'Is vaccination covered by insurance?','a'=>'Many private hospitals work with insurance providers. Contact the hospital directly to confirm coverage.'],
            ['q'=>'How do I know if a hospital is certified?','a'=>'All hospitals on VAXORA are verified by our team and registered with PMDC. Look for the green "Active" status badge on each listing.'],
          ];
          foreach ($faqs as $f): ?>
          <div class="accordion-item">
            <div class="accordion-header" style="font-size:0.92rem"><?= $f['q'] ?> <span class="accordion-icon">+</span></div>
            <div class="accordion-body" style="font-size:0.88rem"><?= $f['a'] ?></div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
