<footer class="footer mt-auto">
    <div class="container">
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="footer-brand mb-2">Vaxora<span class="dot">.</span></div>
                <p style="color:#a78bfa;font-size:0.88rem;">Pakistan's trusted vaccination management platform — keeping children healthy, one dose at a time.</p>
                <div class="footer-social mt-3 d-flex gap-2">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
            <div class="col-md-2">
                <h6 class="mb-3">My Account</h6>
                <ul class="list-unstyled" style="font-size:0.85rem;">
                    <li class="mb-2"><a href="/dashboard.php" class="text-decoration-none" style="color:#a78bfa;">Dashboard</a></li>
                    <li class="mb-2"><a href="/children.php" class="text-decoration-none" style="color:#a78bfa;">My Children</a></li>
                    <li class="mb-2"><a href="/book_appointment.php" class="text-decoration-none" style="color:#a78bfa;">Book Appointment</a></li>
                    <li class="mb-2"><a href="/vaccination_history.php" class="text-decoration-none" style="color:#a78bfa;">History</a></li>
                </ul>
            </div>
            <div class="col-md-3">
                <h6 class="mb-3">Contact Support</h6>
                <div style="font-size:0.85rem;color:#a78bfa;line-height:2;">
                    <div><i class="fas fa-envelope me-2" style="color:#7c3aed;"></i>support@vaxora.pk</div>
                    <div><i class="fas fa-phone me-2" style="color:#7c3aed;"></i>+92-21-111-829-672</div>
                    <div><i class="fas fa-clock me-2" style="color:#7c3aed;"></i>Mon–Fri, 9AM–5PM PKT</div>
                </div>
            </div>
            <div class="col-md-3">
                <h6 class="mb-3">Powered By</h6>
                <div style="font-size:0.82rem;color:#6b6b9a;line-height:1.8;">
                    <div>EPI National Programme</div>
                    <div>WHO Immunization Guidelines</div>
                    <div>Ministry of Health, Pakistan</div>
                </div>
            </div>
        </div>
        <hr style="border-color:rgba(124,58,237,0.2);">
        <div class="d-flex flex-wrap justify-content-between align-items-center" style="font-size:0.8rem;color:#6b6b9a;padding-top:12px;">
            <span>&copy; <?= date('Y') ?> Vaxora Pakistan. All rights reserved.</span>
            <span><a href="/auth/logout.php" style="color:#fca5a5;text-decoration:none;"><i class="fas fa-sign-out-alt me-1"></i>Logout</a></span>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="/assets/js/main.js"></script>
</body>
</html>
