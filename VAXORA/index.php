<?php
require_once 'config/config.php';
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

$stats = ['children'=>0,'vaccinations'=>0,'hospitals'=>0,'vaccines'=>0];
try {
    $stats['children']     = $db->query("SELECT COUNT(*) FROM children")->fetchColumn() ?: 0;
    $stats['vaccinations'] = $db->query("SELECT COUNT(*) FROM vaccination_records")->fetchColumn() ?: 0;
    $stats['hospitals']    = $db->query("SELECT COUNT(*) FROM hospitals WHERE status='active'")->fetchColumn() ?: 0;
    $stats['vaccines']     = $db->query("SELECT COUNT(*) FROM vaccines WHERE status='active'")->fetchColumn() ?: 0;
} catch (PDOException $e) {}

$isLoggedIn = isset($_SESSION['user_id']);
include 'includes/home_header.php';
?>

<!-- ===== HERO CAROUSEL ===== -->
<div id="heroCarousel" class="carousel slide carousel-fade hero-carousel" data-bs-ride="carousel">
    <div class="carousel-indicators">
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active"></button>
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1"></button>
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2"></button>
    </div>
    <div class="carousel-inner">
        <!-- Slide 1 -->
        <div class="carousel-item active">
            <img src="https://images.unsplash.com/photo-1584515933487-779824d29309?w=1400&q=80" alt="Vaccination">
            <div class="carousel-caption">
                <div class="hero-badge"><i class="fas fa-shield-alt me-1"></i> Pakistan's #1 Vaccination Platform</div>
                <h1 class="fw-bold">Protect Your Child's<br>Health Today</h1>
                <p class="mb-4">Book vaccinations at top Karachi hospitals, track records digitally, and never miss a dose again.</p>
                <div class="d-flex flex-wrap gap-3">
                    <?php if ($isLoggedIn): ?>
                        <a href="/dashboard.php" class="btn btn-primary btn-lg px-5 fw-bold">Go to Dashboard <i class="fas fa-arrow-right ms-2"></i></a>
                    <?php else: ?>
                        <a href="/auth/register.php" class="btn btn-primary btn-lg px-5 fw-bold">Get Started Free <i class="fas fa-arrow-right ms-2"></i></a>
                        <a href="/auth/login.php" class="btn btn-outline-light btn-lg px-4">Login</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <!-- Slide 2 -->
        <div class="carousel-item">
            <img src="https://images.unsplash.com/photo-1576091160550-2173dba999ef?w=1400&q=80" alt="Doctor">
            <div class="carousel-caption">
                <div class="hero-badge"><i class="fas fa-hospital me-1"></i> 10 Partner Hospitals in Karachi</div>
                <h1 class="fw-bold">World-Class Hospitals.<br>One Platform.</h1>
                <p class="mb-4">From Civil Hospital to Aga Khan — choose your nearest hospital and book in minutes.</p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="/auth/register.php" class="btn btn-primary btn-lg px-5 fw-bold">Find a Hospital <i class="fas fa-search ms-2"></i></a>
                </div>
            </div>
        </div>
        <!-- Slide 3 -->
        <div class="carousel-item">
            <img src="https://images.unsplash.com/photo-1559757148-5c350d0d3c56?w=1400&q=80" alt="Child Health">
            <div class="carousel-caption">
                <div class="hero-badge"><i class="fas fa-certificate me-1"></i> Digital Vaccination Certificates</div>
                <h1 class="fw-bold">Digital Records.<br>Instant Certificates.</h1>
                <p class="mb-4">Download verified PDF vaccination certificates for school admissions, travel, and health records.</p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="/auth/register.php" class="btn btn-primary btn-lg px-5 fw-bold">Start Tracking <i class="fas fa-file-pdf ms-2"></i></a>
                </div>
            </div>
        </div>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
        <i class="fas fa-chevron-left"></i>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
        <i class="fas fa-chevron-right"></i>
    </button>
</div>

<!-- ===== FLOATING STATS BAR ===== -->
<div class="container" style="margin-top:-70px; position:relative; z-index:10;">
    <div class="stats-bar">
        <div class="row g-3 align-items-center">
            <div class="col-6 col-md-3">
                <div class="stat-item">
                    <h3 class="count-up" data-target="<?= $stats['children'] ?>"><?= number_format($stats['children']) ?>+</h3>
                    <p>Children Protected</p>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-item">
                    <h3 class="count-up" data-target="<?= $stats['vaccinations'] ?>"><?= number_format($stats['vaccinations']) ?>+</h3>
                    <p>Doses Administered</p>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-item">
                    <h3 class="count-up" data-target="<?= $stats['hospitals'] ?>"><?= number_format($stats['hospitals']) ?>+</h3>
                    <p>Partner Hospitals</p>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-item">
                    <h3 class="count-up" data-target="<?= $stats['vaccines'] ?>"><?= number_format($stats['vaccines']) ?>+</h3>
                    <p>Vaccine Types</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ===== VACCINE SCHEDULE TICKER ===== -->
<div class="vaccine-ticker-wrap">
    <div class="vaccine-ticker-label"><i class="fas fa-syringe me-2"></i>EPI Schedule</div>
    <div class="vaccine-ticker-track">
        <div class="vaccine-ticker-inner">
            <span class="vt-item"><i class="fas fa-circle-dot"></i> BCG &mdash; At Birth</span>
            <span class="vt-item"><i class="fas fa-circle-dot"></i> Hepatitis B &mdash; At Birth</span>
            <span class="vt-item"><i class="fas fa-circle-dot"></i> OPV-0 (Polio) &mdash; At Birth</span>
            <span class="vt-item"><i class="fas fa-circle-dot"></i> DPT + HepB + Hib &mdash; 6 Weeks</span>
            <span class="vt-item"><i class="fas fa-circle-dot"></i> OPV-1 (Polio) &mdash; 6 Weeks</span>
            <span class="vt-item"><i class="fas fa-circle-dot"></i> Rotavirus &mdash; 6 Weeks</span>
            <span class="vt-item"><i class="fas fa-circle-dot"></i> Pneumococcal (PCV) &mdash; 6 Weeks</span>
            <span class="vt-item"><i class="fas fa-circle-dot"></i> DPT + HepB + Hib &mdash; 10 Weeks</span>
            <span class="vt-item"><i class="fas fa-circle-dot"></i> OPV-2 (Polio) &mdash; 10 Weeks</span>
            <span class="vt-item"><i class="fas fa-circle-dot"></i> Rotavirus &mdash; 10 Weeks</span>
            <span class="vt-item"><i class="fas fa-circle-dot"></i> DPT + HepB + Hib &mdash; 14 Weeks</span>
            <span class="vt-item"><i class="fas fa-circle-dot"></i> OPV-3 (Polio) &mdash; 14 Weeks</span>
            <span class="vt-item"><i class="fas fa-circle-dot"></i> Pneumococcal (PCV) &mdash; 14 Weeks</span>
            <span class="vt-item"><i class="fas fa-circle-dot"></i> MMR &mdash; 9 Months</span>
            <span class="vt-item"><i class="fas fa-circle-dot"></i> Typhoid (TCV) &mdash; 9 Months</span>
            <span class="vt-item"><i class="fas fa-circle-dot"></i> Vitamin A &mdash; 9 Months</span>
            <span class="vt-item"><i class="fas fa-circle-dot"></i> Pneumococcal (PCV) Booster &mdash; 9 Months</span>
            <span class="vt-item"><i class="fas fa-circle-dot"></i> Measles 2nd Dose &mdash; 15 Months</span>
            <span class="vt-item"><i class="fas fa-circle-dot"></i> DPT Booster &mdash; 15-18 Months</span>
            <span class="vt-item"><i class="fas fa-circle-dot"></i> BCG &mdash; At Birth</span>
            <span class="vt-item"><i class="fas fa-circle-dot"></i> Hepatitis B &mdash; At Birth</span>
            <span class="vt-item"><i class="fas fa-circle-dot"></i> OPV-0 (Polio) &mdash; At Birth</span>
            <span class="vt-item"><i class="fas fa-circle-dot"></i> DPT + HepB + Hib &mdash; 6 Weeks</span>
            <span class="vt-item"><i class="fas fa-circle-dot"></i> OPV-1 (Polio) &mdash; 6 Weeks</span>
            <span class="vt-item"><i class="fas fa-circle-dot"></i> Rotavirus &mdash; 6 Weeks</span>
            <span class="vt-item"><i class="fas fa-circle-dot"></i> Pneumococcal (PCV) &mdash; 6 Weeks</span>
            <span class="vt-item"><i class="fas fa-circle-dot"></i> DPT + HepB + Hib &mdash; 10 Weeks</span>
            <span class="vt-item"><i class="fas fa-circle-dot"></i> OPV-2 (Polio) &mdash; 10 Weeks</span>
            <span class="vt-item"><i class="fas fa-circle-dot"></i> Rotavirus &mdash; 10 Weeks</span>
        </div>
    </div>
</div>

<!-- ===== FEATURES SECTION ===== -->
<section class="py-5 mt-5" id="features">
    <div class="container py-4">
        <div class="text-center mb-5">
            <div class="section-badge">Why Choose Vaxora</div>
            <h2 class="section-title">Everything You Need,<br>All in One Place</h2>
            <p class="section-sub mt-3 mx-auto" style="max-width:520px;">A complete end-to-end vaccination management platform built for modern Pakistan.</p>
        </div>
        <div class="row g-4">
            <div class="col-md-6 col-lg-4">
                <div class="feature-card h-100">
                    <div class="feature-icon" style="background:rgba(98,125,45,0.12);color:#4A6120;"><i class="fas fa-calendar-check"></i></div>
                    <h5>Easy Online Booking</h5>
                    <p>Book vaccination appointments at any partner hospital in just a few taps — no queues, no calls.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="feature-card h-100">
                    <div class="feature-icon" style="background:rgba(74,124,89,0.12);color:#4A7C59;"><i class="fas fa-file-pdf"></i></div>
                    <h5>PDF Certificates</h5>
                    <p>Download verified vaccination certificates instantly — accepted at schools, airports, and government offices.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="feature-card h-100">
                    <div class="feature-icon" style="background:rgba(139,94,60,0.12);color:#8B5E3C;"><i class="fas fa-hospital"></i></div>
                    <h5>10 Karachi Hospitals</h5>
                    <p>Choose from Civil Hospital, Aga Khan, Indus Hospital and 7 more top-rated Karachi facilities.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="feature-card h-100">
                    <div class="feature-icon" style="background:rgba(200,168,75,0.12);color:#A08030;"><i class="fas fa-users"></i></div>
                    <h5>Family Dashboard</h5>
                    <p>Manage all your children's vaccination schedules from one beautiful, easy-to-use dashboard.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="feature-card h-100">
                    <div class="feature-icon" style="background:rgba(192,57,43,0.10);color:#C0392B;"><i class="fas fa-shield-alt"></i></div>
                    <h5>Secure & Private</h5>
                    <p>Your medical data is encrypted and secure. Role-based access ensures only you can see your records.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="feature-card h-100">
                    <div class="feature-icon" style="background:rgba(74,97,32,0.12);color:#627D2D;"><i class="fas fa-chart-line"></i></div>
                    <h5>Real-time Analytics</h5>
                    <p>Hospitals and administrators get live dashboards with vaccination coverage data and reports.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===== ABOUT SECTION ===== -->
<section class="py-5 bg-white" id="about">
    <div class="container py-4">
        <div class="row g-5 align-items-center">
            <div class="col-lg-5">
                <div class="about-img-wrap">
                    <img src="https://images.unsplash.com/photo-1530026186672-2cd00ffc50fe?w=600&q=80"
                         class="img-fluid" alt="Children Vaccination" style="border-radius:20px;box-shadow:0 30px 80px rgba(74,97,32,0.15);">
                    <div class="about-badge-float">
                        <div class="big-num">98%</div>
                        <div class="label">Parent Satisfaction</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="section-badge">About Vaxora</div>
                <h2 class="section-title mb-4">Pakistan's Dedicated<br>Child Vaccination Platform</h2>
                <p class="text-muted mb-4" style="font-size:1.0rem;line-height:1.8;">
                    Vaxora was founded with a single mission: to ensure every child in Pakistan receives timely, complete immunization. We partner with leading hospitals across Karachi to make vaccination accessible, trackable, and effortless for parents.
                </p>
                <div class="row g-3 mb-4">
                    <div class="col-6">
                        <div class="d-flex align-items-start gap-3">
                            <div style="width:40px;height:40px;background:rgba(98,125,45,0.12);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;"><i class="fas fa-check text-violet"></i></div>
                            <div><strong style="font-size:0.9rem;">EPI Compliant</strong><p class="text-muted small mb-0">Follows Pakistan's Expanded Programme on Immunization</p></div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="d-flex align-items-start gap-3">
                            <div style="width:40px;height:40px;background:rgba(74,124,89,0.12);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;"><i class="fas fa-check text-success"></i></div>
                            <div><strong style="font-size:0.9rem;">WHO Guidelines</strong><p class="text-muted small mb-0">All vaccines follow WHO recommended schedules</p></div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="d-flex align-items-start gap-3">
                            <div style="width:40px;height:40px;background:rgba(139,94,60,0.12);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;"><i class="fas fa-check" style="color:#8B5E3C;"></i></div>
                            <div><strong style="font-size:0.9rem;">Real-time Records</strong><p class="text-muted small mb-0">Instant digital updates after every vaccination</p></div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="d-flex align-items-start gap-3">
                            <div style="width:40px;height:40px;background:rgba(200,168,75,0.12);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;"><i class="fas fa-check" style="color:#C8A84B;"></i></div>
                            <div><strong style="font-size:0.9rem;">Free for Parents</strong><p class="text-muted small mb-0">Registration and record keeping is 100% free</p></div>
                        </div>
                    </div>
                </div>
                <a href="/auth/register.php" class="btn btn-primary px-5 py-2 fw-bold">Join Vaxora Today <i class="fas fa-arrow-right ms-2"></i></a>
            </div>
        </div>
    </div>
</section>

<!-- ===== HOW IT WORKS ===== -->
<section class="py-5 bg-light-violet" id="how-it-works">
    <div class="container py-4">
        <div class="text-center mb-5">
            <div class="section-badge">Simple Process</div>
            <h2 class="section-title">Up and Running in 3 Steps</h2>
        </div>
        <div class="row g-4 position-relative">
            <div class="col-md-4 step-card text-center">
                <div class="step-number">1</div>
                <div class="step-connector d-none d-md-block"></div>
                <h5 class="fw-bold mb-2">Create Your Account</h5>
                <p class="text-muted small">Register for free in under 2 minutes. No paperwork, no waiting — just your email and you're in.</p>
            </div>
            <div class="col-md-4 step-card text-center">
                <div class="step-number">2</div>
                <div class="step-connector d-none d-md-block"></div>
                <h5 class="fw-bold mb-2">Add Your Children</h5>
                <p class="text-muted small">Add your children's profiles with their date of birth, blood group, and medical history.</p>
            </div>
            <div class="col-md-4 step-card text-center">
                <div class="step-number">3</div>
                <h5 class="fw-bold mb-2">Book & Get Certified</h5>
                <p class="text-muted small">Book at your nearest hospital, get vaccinated, and download your verified PDF certificate instantly.</p>
            </div>
        </div>
    </div>
</section>

<!-- ===== KARACHI HOSPITALS SHOWCASE ===== -->
<section class="py-5 bg-white">
    <div class="container py-4">
        <div class="text-center mb-5">
            <div class="section-badge">Partner Network</div>
            <h2 class="section-title">Trusted Karachi Hospitals</h2>
            <p class="section-sub mt-3">Book at any of our verified partner hospitals across Karachi</p>
        </div>
        <div class="row g-3">
            <?php
            $karachi_hospitals = [
                ["Civil Hospital Karachi",            "Liaquatabad Road",     "fa-hospital",    "#4A6120"],
                ["Aga Khan University Hospital",      "Stadium Road",         "fa-star",        "#8B5E3C"],
                ["Jinnah Postgraduate Medical Centre","Rafiqui Shaheed Road", "fa-heartbeat",   "#4A7C59"],
                ["Indus Hospital",                    "Korangi Industrial",   "fa-plus-circle", "#C8A84B"],
                ["Liaquat National Hospital",         "Stadium Road",         "fa-clinic-medical","#C0392B"],
                ["Ziauddin Hospital",                 "North Nazimabad",      "fa-stethoscope", "#627D2D"],
            ];
            foreach ($karachi_hospitals as $h): ?>
            <div class="col-md-6 col-lg-4">
                <div class="card p-3 d-flex flex-row align-items-center gap-3">
                    <div style="width:48px;height:48px;background:<?= $h[3] ?>1a;border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="fas <?= $h[2] ?>" style="color:<?= $h[3] ?>;font-size:1.2rem;"></i>
                    </div>
                    <div>
                        <div class="fw-semibold" style="font-size:0.9rem;"><?= $h[0] ?></div>
                        <div class="text-muted" style="font-size:0.78rem;"><i class="fas fa-map-marker-alt me-1"></i><?= $h[1] ?>, Karachi</div>
                    </div>
                    <span class="badge bg-success ms-auto" style="font-size:0.65rem;">Active</span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-4">
            <p class="text-muted small">+ 4 more partner hospitals available after login</p>
        </div>
    </div>
</section>

<!-- ===== TESTIMONIALS ===== -->
<section class="py-5 bg-light-violet">
    <div class="container py-4">
        <div class="text-center mb-5">
            <div class="section-badge">What Parents Say</div>
            <h2 class="section-title">Loved by Karachi Families</h2>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="testimonial-card h-100">
                    <div class="star-rating mb-3"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
                    <p class="text-muted mb-4" style="font-size:0.9rem;">"Vaxora made it so easy to track my son's vaccination schedule. The PDF certificate was accepted at his school without any issues!"</p>
                    <div class="d-flex align-items-center gap-3">
                        <img src="https://ui-avatars.com/api/?name=Fatima+Noor&background=627D2D&color=fff&size=48" class="testimonial-avatar" alt="">
                        <div>
                            <div class="fw-semibold" style="font-size:0.88rem;">Fatima Noor</div>
                            <div class="text-muted" style="font-size:0.78rem;">DHA Phase 5, Karachi</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="testimonial-card h-100">
                    <div class="star-rating mb-3"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
                    <p class="text-muted mb-4" style="font-size:0.9rem;">"Booked an appointment at Aga Khan Hospital in 2 minutes. The digital records are so convenient — I no longer fear losing the vaccination card!"</p>
                    <div class="d-flex align-items-center gap-3">
                        <img src="https://ui-avatars.com/api/?name=Muhammad+Usman&background=8B5E3C&color=fff&size=48" class="testimonial-avatar" alt="">
                        <div>
                            <div class="fw-semibold" style="font-size:0.88rem;">Muhammad Usman</div>
                            <div class="text-muted" style="font-size:0.78rem;">North Nazimabad, Karachi</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="testimonial-card h-100">
                    <div class="star-rating mb-3"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i></div>
                    <p class="text-muted mb-4" style="font-size:0.9rem;">"As a working mother I never had time to manage vaccination records. Vaxora changed everything — everything is in one app, one click away."</p>
                    <div class="d-flex align-items-center gap-3">
                        <img src="https://ui-avatars.com/api/?name=Ayesha+Farooq&background=4A7C59&color=fff&size=48" class="testimonial-avatar" alt="">
                        <div>
                            <div class="fw-semibold" style="font-size:0.88rem;">Ayesha Farooq</div>
                            <div class="text-muted" style="font-size:0.78rem;">Saddar, Karachi</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===== CTA ===== -->
<section class="cta-section py-5">
    <div class="container py-4 text-center text-white" style="position:relative;z-index:1;">
        <div class="section-badge" style="background:rgba(255,255,255,0.15);border-color:rgba(255,255,255,0.2);color:rgba(255,255,255,0.9);">Start Today — It's Free</div>
        <h2 class="section-title mb-3">Ready to Protect Your Child?</h2>
        <p class="mb-5 opacity-80" style="max-width:480px;margin:0 auto 32px;">Join thousands of Karachi families who trust Vaxora with their children's health records.</p>
        <?php if (!$isLoggedIn): ?>
        <div class="d-flex flex-wrap justify-content-center gap-3">
            <a href="/auth/register.php" class="btn btn-light btn-lg px-5 fw-bold" style="color:#4A6120;">
                <i class="fas fa-rocket me-2"></i>Register Free
            </a>
            <a href="/auth/login.php" class="btn btn-outline-light btn-lg px-4">Login</a>
        </div>
        <?php else: ?>
        <a href="/dashboard.php" class="btn btn-light btn-lg px-5 fw-bold" style="color:#4A6120;"><i class="fas fa-tachometer-alt me-2"></i>Go to Dashboard</a>
        <?php endif; ?>
    </div>
</section>

<?php include 'includes/home_footer.php'; ?>
