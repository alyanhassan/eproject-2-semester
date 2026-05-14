<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vaxora — Pakistan's Smart Vaccination Platform</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark sticky-top">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="/index.php">
            <span class="brand-icon"><i class="fas fa-syringe text-white"></i></span>
            Vaxora<span class="brand-dot">.</span>
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#homeNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="homeNav">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item"><a class="nav-link" href="/index.php#features">Features</a></li>
                <li class="nav-item"><a class="nav-link" href="/index.php#about">About</a></li>
                <li class="nav-item"><a class="nav-link" href="/index.php#how-it-works">How It Works</a></li>
                <li class="nav-item"><a class="nav-link" href="/contact.php">Contact</a></li>
            </ul>
            <div class="d-flex align-items-center gap-2">
                <?php if ($isLoggedIn): ?>
                    <a class="btn btn-primary btn-sm px-4" href="/dashboard.php"><i class="fas fa-tachometer-alt me-1"></i>Dashboard</a>
                <?php else: ?>
                    <a class="nav-link btn btn-outline-light btn-sm px-3" href="/auth/login.php">Login</a>
                    <a class="btn btn-primary btn-sm px-4" href="/auth/register.php">Get Started Free</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>
