<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vaxora — E-Vaccination System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark sticky-top">
    <div class="container-fluid px-4">
        <a class="navbar-brand d-flex align-items-center" href="/index.php">
            <span class="brand-icon"><i class="fas fa-syringe text-white"></i></span>
            Vaxora<span class="brand-dot">.</span>
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav ms-auto align-items-center gap-1">
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'parent'): ?>
                    <li class="nav-item"><a class="nav-link" href="/dashboard.php"><i class="fas fa-tachometer-alt me-1"></i>Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="/children.php"><i class="fas fa-child me-1"></i>My Children</a></li>
                    <li class="nav-item"><a class="nav-link" href="/book_appointment.php"><i class="fas fa-calendar-plus me-1"></i>Book</a></li>
                    <li class="nav-item"><a class="nav-link" href="/vaccination_history.php"><i class="fas fa-notes-medical me-1"></i>History</a></li>
                    <li class="nav-item"><a class="nav-link" href="/contact.php"><i class="fas fa-envelope me-1"></i>Contact</a></li>
                <?php elseif (isset($_SESSION['role']) && $_SESSION['role'] === 'hospital'): ?>
                    <li class="nav-item"><a class="nav-link" href="/hospital/dashboard.php"><i class="fas fa-tachometer-alt me-1"></i>Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="/hospital/appointments.php"><i class="fas fa-calendar-check me-1"></i>Appointments</a></li>
                <?php elseif (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <li class="nav-item"><a class="nav-link" href="/admin/dashboard.php"><i class="fas fa-tachometer-alt me-1"></i>Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="/admin/manage_hospitals.php"><i class="fas fa-hospital me-1"></i>Hospitals</a></li>
                    <li class="nav-item"><a class="nav-link" href="/admin/manage_vaccines.php"><i class="fas fa-vial me-1"></i>Vaccines</a></li>
                    <li class="nav-item"><a class="nav-link" href="/admin/appointments.php"><i class="fas fa-calendar me-1"></i>Appointments</a></li>
                    <li class="nav-item"><a class="nav-link" href="/admin/reports.php"><i class="fas fa-chart-bar me-1"></i>Reports</a></li>
                <?php endif; ?>
                <li class="nav-item ms-2">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:32px;height:32px;background:rgba(98,125,45,0.30);font-size:0.8rem;color:#D8E8A8;">
                            <i class="fas fa-user"></i>
                        </div>
                        <span class="nav-link text-muted small p-0"><?= htmlspecialchars(explode('@',$_SESSION['email'] ?? '')[0]) ?></span>
                    </div>
                </li>
                <li class="nav-item ms-1">
                    <a class="btn btn-sm" href="/auth/logout.php" style="background:rgba(239,68,68,0.15);color:#fca5a5;border:1px solid rgba(239,68,68,0.3);">
                        <i class="fas fa-sign-out-alt me-1"></i>Logout
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
