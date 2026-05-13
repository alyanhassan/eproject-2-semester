<?php
// REMOVED checkRole(['parent']); 
// Because a Landing Page should be visible to everyone!
// If you want to show different buttons for logged-in users, we can add a check later.

require_once 'config/config.php';
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

/* ==============================================
   FETCH GLOBAL STATISTICS (OPTIMIZED)
============================================== */
$stats = [];

try {
    // We can combine these into one query for better performance, 
    // but separate queries are fine for a low-traffic landing page.
    
    // Children Protected
    $stats['children'] = $db->query("SELECT COUNT(*) FROM children")->fetchColumn() ?: 0;

    // Vaccinations Given
    $stats['vaccinations'] = $db->query("SELECT COUNT(*) FROM vaccination_records")->fetchColumn() ?: 0;

    // Active Partner Hospitals
    $stats['hospitals'] = $db->query("SELECT COUNT(*) FROM hospitals WHERE status = 'active'")->fetchColumn() ?: 0;

    // Total Vaccine Types Available
    $stats['vaccines'] = $db->query("SELECT COUNT(*) FROM vaccines WHERE status = 'active'")->fetchColumn() ?: 0;

} catch (PDOException $e) {
    error_log("Landing Page Stats Error: " . $e->getMessage());
    $stats = ['children' => 0, 'vaccinations' => 0, 'hospitals' => 0, 'vaccines' => 0];
}

// Check if user is logged in to change Hero Buttons
session_start();
$isLoggedIn = isset($_SESSION['user_id']);

include 'includes/home_header.php';
?>

<!-- HERO SECTION -->
<section class="hero-section bg-gradient-primary text-white position-relative overflow-hidden" style="background: linear-gradient(45deg, #4e73df 0%, #224abe 100%);">
    <div class="container">
        <div class="row min-vh-100 align-items-center">

            <div class="col-lg-6 py-5">
                <h1 class="display-4 fw-bold mb-4">
                    Protect Your Child's Future with Timely Vaccination
                </h1>

                <p class="lead mb-4 opacity-75">
                    Join thousands of parents who trust our automated scheduling and record-keeping system to keep their children healthy.
                </p>

                <div class="d-grid gap-3 d-sm-flex">
                    <?php if ($isLoggedIn): ?>
                        <a href="dashboard.php" class="btn btn-light btn-lg px-4 shadow">
                            Go to Dashboard
                        </a>
                    <?php else: ?>
                        <a href="auth/register.php" class="btn btn-light btn-lg px-4 shadow">
                            Register Now
                        </a>
                        <a href="auth/login.php" class="btn btn-outline-light btn-lg px-4">
                            Member Login
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="col-lg-6 d-none d-lg-block text-center">
                <!-- Ensure this path is correct or use a placeholder -->
                <img src="assets/img/vaccine-hero.svg" class="img-fluid animated pulse-slow" alt="Vaccination Illustration" style="max-height: 500px;">
            </div>

        </div>
    </div>
</section>

<!-- STATS COUNTER -->
<section class="py-5 bg-white shadow-sm">
    <div class="container">
        <div class="row text-center">

            <div class="col-md-3 col-6 mb-4 mb-md-0">
                <div class="p-3">
                    <h2 class="display-5 fw-bold text-primary"><?= number_format($stats['children']) ?>+</h2>
                    <p class="text-muted text-uppercase small fw-bold">Children Protected</p>
                </div>
            </div>

            <div class="col-md-3 col-6 mb-4 mb-md-0">
                <div class="p-3">
                    <h2 class="display-5 fw-bold text-primary"><?= number_format($stats['vaccinations']) ?>+</h2>
                    <p class="text-muted text-uppercase small fw-bold">Doses Administered</p>
                </div>
            </div>

            <div class="col-md-3 col-6">
                <div class="p-3">
                    <h2 class="display-5 fw-bold text-primary"><?= number_format($stats['hospitals']) ?>+</h2>
                    <p class="text-muted text-uppercase small fw-bold">Partner Hospitals</p>
                </div>
            </div>

            <div class="col-md-3 col-6">
                <div class="p-3">
                    <h2 class="display-5 fw-bold text-primary"><?= $stats['vaccines'] ?></h2>
                    <p class="text-muted text-uppercase small fw-bold">Vaccine Types</p>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- FEATURES SECTION -->
<section class="py-5 bg-light">
    <div class="container py-5">
        <div class="text-center mb-5">
            <h2 class="fw-bold">How We Help You</h2>
            <p class="text-muted">A complete digital solution for modern parenting.</p>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm p-4 text-center">
                    <div class="icon-box mb-3 text-primary">
                        <i class="fas fa-calendar-check fa-3x"></i>
                    </div>
                    <h5>Easy Booking</h5>
                    <p class="text-muted">Select your preferred hospital and time slot in just a few clicks.</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm p-4 text-center">
                    <div class="icon-box mb-3 text-primary">
                        <i class="fas fa-bell fa-3x"></i>
                    </div>
                    <h5>Smart Reminders</h5>
                    <p class="text-muted">Receive automated notifications before your next scheduled dose.</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm p-4 text-center">
                    <div class="icon-box mb-3 text-primary">
                        <i class="fas fa-file-medical fa-3x"></i>
                    </div>
                    <h5>Digital Records</h5>
                    <p class="text-muted">Access your child's vaccination history anytime, anywhere.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CALL TO ACTION -->
<section class="py-5 bg-primary text-white text-center">
    <div class="container py-4">
        <h2 class="fw-bold mb-3">Ready to Secure Your Child's Health?</h2>
        <p class="lead mb-4 opacity-75">It takes less than 2 minutes to get started.</p>
        
        <?php if (!$isLoggedIn): ?>
            <a href="auth/register.php" class="btn btn-light btn-lg px-5 fw-bold">Get Started Now</a>
        <?php else: ?>
            <a href="dashboard.php" class="btn btn-light btn-lg px-5 fw-bold">Manage Appointments</a>
        <?php endif; ?>
    </div>
</section>

<?php include 'includes/home_footer.php'; ?>