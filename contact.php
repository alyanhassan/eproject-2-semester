<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/includes/mailer.php';

$pageTitle  = 'Contact';
$activePage = 'contact';

$db             = getDB();
$msgSuccess     = $msgError     = '';
$reviewSuccess  = $reviewError  = '';

// Contact form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['form_type'] ?? '') === 'contact') {
    $name    = trim($_POST['name']    ?? '');
    $email   = trim($_POST['email']   ?? '');
    $phone   = trim($_POST['phone']   ?? '');
    $city    = trim($_POST['city']    ?? '');
    $message = trim($_POST['message'] ?? '');

    if (!$name || !$email || !$message) {
        $msgError = 'Please fill in all required fields.';
    } else {
        $db->prepare('INSERT INTO contacts (name,email,phone,city,message) VALUES (?,?,?,?,?)')->execute([$name,$email,$phone,$city,$message]);

        // 1. Notify admin about new contact message
        $adminHtml = '<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;background:#F8FAF2;font-family:Arial,Helvetica,sans-serif">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#F8FAF2;padding:40px 20px">
<tr><td align="center">
<table width="560" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 8px 32px rgba(28,39,6,0.1);max-width:560px;width:100%">
  <tr><td style="background:#1C2706;padding:32px;text-align:center">
    <div style="font-size:26px;font-weight:800;color:#90E300;letter-spacing:-0.5px">VAXORA</div>
    <div style="color:rgba(255,255,255,0.55);font-size:13px;margin-top:4px">New Contact Message</div>
  </td></tr>
  <tr><td style="padding:28px 36px">
    <p style="font-size:15px;font-weight:700;color:#1C2706;margin:0 0 20px">You have received a new message from the contact form.</p>
    <table width="100%" cellpadding="0" cellspacing="0" style="font-size:13px;border-collapse:collapse">
      <tr style="border-bottom:1px solid #D5DEC5">
        <td style="padding:10px 0;color:#888;width:30%">Name</td>
        <td style="padding:10px 0;font-weight:600;color:#1C2706">' . htmlspecialchars($name) . '</td>
      </tr>
      <tr style="border-bottom:1px solid #D5DEC5">
        <td style="padding:10px 0;color:#888">Email</td>
        <td style="padding:10px 0;font-weight:600;color:#1C2706">' . htmlspecialchars($email) . '</td>
      </tr>
      <tr style="border-bottom:1px solid #D5DEC5">
        <td style="padding:10px 0;color:#888">Phone</td>
        <td style="padding:10px 0;font-weight:600;color:#1C2706">' . htmlspecialchars($phone ?: '—') . '</td>
      </tr>
      <tr style="border-bottom:1px solid #D5DEC5">
        <td style="padding:10px 0;color:#888">City</td>
        <td style="padding:10px 0;font-weight:600;color:#1C2706">' . htmlspecialchars($city ?: '—') . '</td>
      </tr>
      <tr>
        <td style="padding:10px 0;color:#888;vertical-align:top">Message</td>
        <td style="padding:10px 0;font-weight:600;color:#1C2706;line-height:1.6">' . nl2br(htmlspecialchars($message)) . '</td>
      </tr>
    </table>
  </td></tr>
  <tr><td style="background:#f8faf2;padding:20px 36px;border-top:1px solid #D5DEC5;text-align:center">
    <p style="color:#aaa;font-size:11px;margin:0">VAXORA &mdash; Protecting Lives, One Vaccine at a Time</p>
  </td></tr>
</table>
</td></tr>
</table>
</body>
</html>';
        sendVaxoraEmail(MAIL_USERNAME, 'VAXORA Admin', 'New Contact Message from ' . $name, $adminHtml);

        // 2. Send confirmation email to the sender
        $confirmHtml = '<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;background:#F8FAF2;font-family:Arial,Helvetica,sans-serif">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#F8FAF2;padding:40px 20px">
<tr><td align="center">
<table width="560" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 8px 32px rgba(28,39,6,0.1);max-width:560px;width:100%">
  <tr><td style="background:#1C2706;padding:32px;text-align:center">
    <div style="font-size:26px;font-weight:800;color:#90E300;letter-spacing:-0.5px">VAXORA</div>
    <div style="color:rgba(255,255,255,0.55);font-size:13px;margin-top:4px">Message Received</div>
  </td></tr>
  <tr><td style="padding:28px 36px">
    <div style="background:#f0ffe6;border-left:4px solid #90E300;padding:14px 18px;border-radius:8px;margin-bottom:24px">
      <div style="font-weight:700;font-size:15px;color:#1C2706">Thank you for contacting us!</div>
      <div style="font-size:13px;color:#555;margin-top:5px">We have received your message and will get back to you within 24 hours.</div>
    </div>
    <p style="font-size:14px;color:#333;margin:0 0 20px">Dear <strong>' . htmlspecialchars($name) . '</strong>,</p>
    <p style="font-size:13px;color:#555;line-height:1.7;margin:0 0 20px">
      Thank you for reaching out to VAXORA. Our team has received your message and will respond to you at <strong>' . htmlspecialchars($email) . '</strong> as soon as possible.
    </p>
    <div style="background:#1C2706;border-radius:10px;padding:16px 20px;margin-top:8px">
      <div style="color:#90E300;font-weight:700;font-size:13px;margin-bottom:6px">Your Message</div>
      <div style="color:rgba(255,255,255,0.75);font-size:12px;line-height:1.7">' . nl2br(htmlspecialchars($message)) . '</div>
    </div>
  </td></tr>
  <tr><td style="background:#f8faf2;padding:20px 36px;border-top:1px solid #D5DEC5;text-align:center">
    <p style="color:#aaa;font-size:11px;margin:0">VAXORA &mdash; Protecting Lives, One Vaccine at a Time</p>
    <p style="color:#bbb;font-size:11px;margin:6px 0 0">hello@vaxora.pk &nbsp;&bull;&nbsp; 0800-VAXORA</p>
  </td></tr>
</table>
</td></tr>
</table>
</body>
</html>';
        sendVaxoraEmail($email, $name, 'VAXORA — We received your message!', $confirmHtml);

        $msgSuccess = 'Thank you! We will respond within 24 hours.';
    }
}

// Review form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['form_type'] ?? '') === 'review') {
    $rName   = trim($_POST['reviewer_name'] ?? '');
    $rEmail  = trim($_POST['reviewer_email'] ?? '');
    $rLoc    = trim($_POST['reviewer_location'] ?? '');
    $rRating = (int)($_POST['rating'] ?? 5);
    $rText   = trim($_POST['review_text'] ?? '');
    $pId     = isLoggedIn() && $_SESSION['user']['role']==='parent' ? $_SESSION['user_id'] : null;

    if (!$rName || !$rEmail || !$rText || $rRating < 1 || $rRating > 5) {
        $reviewError = 'Please fill in all review fields.';
    } else {
        $db->prepare('INSERT INTO reviews (parent_id,name,email,location,rating,review_text,status) VALUES (?,?,?,?,?,?,"pending")')
           ->execute([$pId, $rName, $rEmail, $rLoc, $rRating, $rText]);
        $reviewSuccess = 'Thank you for your review! It will appear on the site after moderation.';
    }
}

require __DIR__ . '/includes/header.php';
?>

<section class="hero" style="min-height:42vh">
  <div class="hero-bg">
    <img src="https://images.unsplash.com/photo-1551601651-2a8555f1a136?auto=format&fit=crop&w=1920&q=80" alt="Clinic reception">
    <div class="hero-overlay" style="background:linear-gradient(135deg,rgba(144,227,0,0.45) 0%,rgba(28,39,6,0.82) 100%)"></div>
  </div>
  <div class="hero-content text-center" style="padding:60px 5%">
    <h1 style="color:#fff;font-size:clamp(1.8rem,4vw,3rem);margin-bottom:14px">Get in Touch With VAXORA</h1>
    <p style="color:rgba(255,255,255,0.82);font-size:1rem;max-width:480px;margin:0 auto">
      We're here to help with vaccine information, hospital bookings, and any questions you have.
    </p>
  </div>
</section>

<section class="section bg-white">
  <div class="container">
    <div class="grid-2" style="gap:48px">

      <!-- Contact Form -->
      <div class="card">
        <h2 style="font-size:1.2rem;margin-bottom:6px">Send Us a Message</h2>
        <p class="text-muted" style="font-size:0.88rem;margin-bottom:24px">We typically respond within 24 hours on business days.</p>

        <?php if ($msgSuccess): ?><div class="alert alert-success alert-auto"><?= e($msgSuccess) ?></div><?php endif; ?>
        <?php if ($msgError):   ?><div class="alert alert-error"><?= e($msgError) ?></div><?php endif; ?>

        <form method="POST">
          <input type="hidden" name="form_type" value="contact">
          <div class="form-row">
            <div class="form-group"><label class="form-label">Full Name *</label><input type="text" name="name" class="form-control" value="<?= e($_POST['name']??'') ?>" required></div>
            <div class="form-group"><label class="form-label">Email *</label><input type="email" name="email" class="form-control" value="<?= e($_POST['email']??'') ?>" required></div>
          </div>
          <div class="form-row">
            <div class="form-group"><label class="form-label">Phone</label><input type="tel" name="phone" class="form-control" value="<?= e($_POST['phone']??'') ?>" placeholder="0300-0000000"></div>
            <div class="form-group"><label class="form-label">City</label><input type="text" name="city" class="form-control" value="<?= e($_POST['city']??'') ?>"></div>
          </div>
          <div class="form-group"><label class="form-label">Message *</label><textarea name="message" class="form-control" rows="5" required><?= e($_POST['message']??'') ?></textarea></div>
          <button type="submit" class="btn btn-primary">Send Message</button>
        </form>
      </div>

      <!-- Contact Info -->
      <div>
        <div class="card mb-20">
          <h3 style="font-size:1rem;margin-bottom:20px">Contact Information</h3>
          <?php
          $contactInfo = [
            ['label'=>'Office',    'value'=>'Plot 12, Block 5, Clifton, Karachi, Pakistan',
             'icon'=>'<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>'],
            ['label'=>'Helpline',  'value'=>'0800-VAXORA — Mon–Sat, 9am–6pm',
             'icon'=>'<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"/></svg>'],
            ['label'=>'Email',     'value'=>'hello@vaxora.pk',
             'icon'=>'<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>'],
            ['label'=>'WhatsApp',  'value'=>'+92-300-VAXORA',
             'icon'=>'<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>'],
          ];
          foreach ($contactInfo as $ci): ?>
          <div style="display:flex;gap:14px;margin-bottom:18px">
            <div style="width:40px;height:40px;border-radius:10px;background:rgba(144,227,0,0.12);display:flex;align-items:center;justify-content:center;color:#4a7c00;flex-shrink:0"><?= $ci['icon'] ?></div>
            <div>
              <div style="font-size:0.75rem;color:var(--muted);font-weight:600;text-transform:uppercase;letter-spacing:0.06em;margin-bottom:2px"><?= $ci['label'] ?></div>
              <div style="font-size:0.9rem;color:var(--text)"><?= $ci['value'] ?></div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>

        <div class="card" style="padding:24px">
          <h3 style="font-size:1rem;margin-bottom:18px">Common Questions</h3>
          <?php
          $faqs = [
            ['q'=>'How do I book an appointment?',        'a'=>'Register or log in, go to "Book Hospital", select your child, vaccine and preferred hospital, then pick a date. Admin approves within 24 hours.'],
            ['q'=>'Can I cancel or reschedule?',          'a'=>'Yes — you can cancel pending appointments from your dashboard at any time. Contact us to reschedule.'],
            ['q'=>'Is the platform free to use?',         'a'=>'Yes, VAXORA is completely free for patients. There are no booking or platform fees.'],
            ['q'=>'How are hospitals verified?',          'a'=>'All hospitals on VAXORA are reviewed by our team and must be registered with PMDC before being listed.'],
          ];
          foreach ($faqs as $f): ?>
          <div class="accordion-item">
            <div class="accordion-header" style="font-size:0.9rem"><?= $f['q'] ?> <span class="accordion-icon">+</span></div>
            <div class="accordion-body" style="font-size:0.87rem"><?= $f['a'] ?></div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- LEAVE A REVIEW -->
<section class="section bg-sage" id="leave-review">
  <div class="container" style="max-width:700px">
    <div class="text-center" style="margin-bottom:40px">
      <span class="badge badge-lime" style="margin-bottom:14px">Share Your Experience</span>
      <h2 class="section-title">Leave a Review</h2>
      <p class="section-subtitle" style="margin-bottom:0">Your feedback helps other families choose trusted vaccination centers</p>
    </div>

    <div class="card">
      <?php if ($reviewSuccess): ?><div class="alert alert-success alert-auto"><?= e($reviewSuccess) ?></div><?php endif; ?>
      <?php if ($reviewError):   ?><div class="alert alert-error"><?= e($reviewError) ?></div><?php endif; ?>

      <form method="POST">
        <input type="hidden" name="form_type" value="review">
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Your Name *</label>
            <input type="text" name="reviewer_name" class="form-control" value="<?= e($_POST['reviewer_name']??'') ?>" required
              <?= (isLoggedIn() ? 'value="'.e($_SESSION['user']['name']).'"' : '') ?>>
          </div>
          <div class="form-group">
            <label class="form-label">Email *</label>
            <input type="email" name="reviewer_email" class="form-control" required
              value="<?= isLoggedIn() ? e($_SESSION['user']['email']) : e($_POST['reviewer_email']??'') ?>">
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">City / Location</label>
          <input type="text" name="reviewer_location" class="form-control" placeholder="e.g. Karachi" value="<?= e($_POST['reviewer_location']??'') ?>">
        </div>

        <!-- Star Rating -->
        <div class="form-group">
          <label class="form-label">Rating *</label>
          <div id="starRating" style="display:flex;gap:6px;margin-top:6px">
            <?php for ($i=1;$i<=5;$i++): ?>
            <label style="cursor:pointer;font-size:2rem;line-height:1;color:#e2e8f0;transition:color 0.12s" data-star="<?= $i ?>">
              <input type="radio" name="rating" value="<?= $i ?>" style="display:none" <?= $i===5?'checked':'' ?>>
              &#9733;
            </label>
            <?php endfor; ?>
          </div>
          <div style="font-size:0.78rem;color:var(--muted);margin-top:6px" id="starLabel">5 out of 5 stars</div>
        </div>

        <div class="form-group">
          <label class="form-label">Your Review *</label>
          <textarea name="review_text" class="form-control" rows="5" required placeholder="Tell us about your vaccination experience with VAXORA..."><?= e($_POST['review_text']??'') ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Submit Review</button>
        <p class="text-muted mt-12" style="font-size:0.82rem">Reviews are published after a brief moderation check — usually within 24 hours.</p>
      </form>
    </div>
  </div>
</section>

<script>
(function(){
  var labels  = document.querySelectorAll('#starRating label');
  var lbl     = document.getElementById('starLabel');
  var chosen  = 5;
  var words   = ['','Terrible','Poor','Average','Good','Excellent'];

  function paint(n) {
    labels.forEach(function(l){
      l.style.color = parseInt(l.dataset.star) <= n ? '#f59e0b' : '#e2e8f0';
    });
    if (lbl) lbl.textContent = n + ' out of 5 stars — ' + words[n];
  }

  paint(chosen);

  labels.forEach(function(l){
    l.addEventListener('mouseenter', function(){ paint(parseInt(l.dataset.star)); });
    l.addEventListener('mouseleave', function(){ paint(chosen); });
    l.addEventListener('click', function(){
      chosen = parseInt(l.dataset.star);
      l.querySelector('input').checked = true;
      paint(chosen);
    });
  });
})();
</script>

<?php require __DIR__ . '/includes/footer.php'; ?>
