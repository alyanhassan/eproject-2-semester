<?php
// Paths check karein: Agar config folder index ke sath hai toh path sahi hai
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
// Header include (Ensure path is correct)
include 'includes/home_header.php';
?>

<div id="heroCarousel" class="carousel slide carousel-fade hero-carousel" data-bs-ride="carousel">
    <div class="carousel-indicators">
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active"></button>
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1"></button>
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2"></button>
    </div>
    <div class="carousel-inner">
        <div class="carousel-item active">
            <img src="https://images.unsplash.com/photo-1584515933487-779824d29309?w=1400&q=80" alt="Vaccination">
            <div class="carousel-caption">
                <div class="hero-badge"><i class="fas fa-shield-alt me-1"></i> Pakistan's #1 Vaccination Platform</div>
                <h1 class="fw-bold">Protect Your Child's<br>Health Today</h1>
                <p class="mb-4">Book vaccinations at top Karachi hospitals, track records digitally, and never miss a dose again.</p>
                <div class="d-flex flex-wrap gap-3">
                    <?php if ($isLoggedIn): ?>
                        <a href="dashboard.php" class="btn btn-primary btn-lg px-5 fw-bold">Go to Dashboard <i class="fas fa-arrow-right ms-2"></i></a>
                    <?php else: ?>
                        <a href="auth/register.php" class="btn btn-primary btn-lg px-5 fw-bold">Get Started Free <i class="fas fa-arrow-right ms-2"></i></a>
                        <a href="auth/login.php" class="btn btn-outline-light btn-lg px-4">Login</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="carousel-item">
            <img src="https://images.unsplash.com/photo-1576091160550-2173dba999ef?w=1400&q=80" alt="Doctor">
            <div class="carousel-caption">
                <div class="hero-badge"><i class="fas fa-hospital me-1"></i> 10 Partner Hospitals in Karachi</div>
                <h1 class="fw-bold">World-Class Hospitals.<br>One Platform.</h1>
                <p class="mb-4">From Civil Hospital to Aga Khan — choose your nearest hospital and book in minutes.</p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="auth/register.php" class="btn btn-primary btn-lg px-5 fw-bold">Find a Hospital <i class="fas fa-search ms-2"></i></a>
                </div>
            </div>
        </div>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev"><i class="fas fa-chevron-left"></i></button>
    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next"><i class="fas fa-chevron-right"></i></button>
</div>

<section class="cta-section py-5">
    <div class="container py-4 text-center text-white" style="position:relative;z-index:1;">
        <h2 class="section-title mb-3">Ready to Protect Your Child?</h2>
        <?php if (!$isLoggedIn): ?>
        <div class="d-flex flex-wrap justify-content-center gap-3">
            <a href="auth/register.php" class="btn btn-light btn-lg px-5 fw-bold" style="color:#5b21b6;">
                <i class="fas fa-rocket me-2"></i>Register Free
            </a>
            <a href="auth/login.php" class="btn btn-outline-light btn-lg px-4">Login</a>
        </div>
        <?php else: ?>
        <a href="dashboard.php" class="btn btn-light btn-lg px-5 fw-bold" style="color:#5b21b6;"><i class="fas fa-tachometer-alt me-2"></i>Go to Dashboard</a>
        <?php endif; ?>
    </div>
</section>

<?php include 'includes/home_footer.php'; ?>