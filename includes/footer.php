<?php $siteUrl = SITE_URL; ?>
<footer class="site-footer">
  <div class="footer-watermark">VAXORA</div>
  <div class="footer-grid">
    <div>
      <div class="footer-logo">
        <svg width="26" height="26" viewBox="0 0 28 28" fill="none"><path d="M14 2L4 7v7c0 5.5 4.3 10.7 10 12 5.7-1.3 10-6.5 10-12V7L14 2z" fill="#90E300" opacity="0.2" stroke="#90E300" stroke-width="1.5"/><path d="M14 8v12M8 14h12" stroke="#90E300" stroke-width="2" stroke-linecap="round"/></svg>
        VAXORA
      </div>
      <p class="footer-tagline">Healthy People, Stronger Communities.<br>Pakistan's trusted vaccination booking platform.</p>
      <div class="footer-socials">
      <a href="https://www.facebook.com/vaxora" target="_blank" title="Facebook">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg>
        </a>
        <a href="https://www.instagram.com/vaxora" target="_blank" title="Instagram">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/></svg>
        </a>
        <a href="https://twitter.com/vaxora" target="_blank" title="Twitter/X">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
        </a>
        <a href="https://www.linkedin.com/company/vaxora" target="_blank" title="LinkedIn">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-2-2 2 2 0 00-2 2v7h-4v-7a6 6 0 016-6zM2 9h4v12H2z"/><circle cx="4" cy="4" r="2"/></svg>
        </a>
      </div>
    </div>
    <div class="footer-col">
      <h4>Quick Links</h4>
      <a href="<?= $siteUrl ?>/">Home</a>
      <a href="<?= $siteUrl ?>/vaccines.php">Vaccines</a>
      <a href="<?= $siteUrl ?>/hospitals.php">Hospitals</a>
      <a href="<?= $siteUrl ?>/about.php">About Us</a>
    </div>
    <div class="footer-col">
      <h4>Support</h4>
      <a href="<?= $siteUrl ?>/blog.php">Blog</a>
      <a href="<?= $siteUrl ?>/contact.php">Contact</a>
      <!-- <a href="#">Privacy Policy</a>
      <a href="#">Terms of Service</a> -->
    </div>
    <div class="footer-col">
      <h4>Contact</h4>
      <a href="#">Plot 12, Block 5, Clifton, Karachi</a>
      <a href="tel:0800829672">0800-VAXORA</a>
      <a href="mailto:hello@vaxora.pk">hello@vaxora.pk</a>
      <a href="#">WhatsApp: +92-300-VAXORA</a>
    </div>
  </div>
  <div class="footer-bottom">
    <span>&copy; <?= date('Y') ?> VAXORA. All rights reserved.</span>
    <span>Pakistan's Trusted Vaccination Platform</span>
  </div>
</footer>

<div class="mobile-booking-bar">
  <a href="<?= $siteUrl ?>/login.php" class="btn btn-primary">Book Vaccination</a>
</div>

<script src="<?= $siteUrl ?>/assets/js/main.js"></script>
</body>
</html>
