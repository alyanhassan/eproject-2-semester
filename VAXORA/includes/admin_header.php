<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vaxora Admin — <?php echo $pageTitle ?? 'Dashboard'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body>

<div class="admin-sidebar">
    <div class="brand">
        <div class="d-flex align-items-center gap-2">
            <div style="width:36px;height:36px;background:linear-gradient(135deg,#7c3aed,#0ea5e9);border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1rem;">
                <i class="fas fa-syringe text-white"></i>
            </div>
            <div>
                <div style="font-size:1.1rem;font-weight:800;color:#fff;letter-spacing:-0.3px;">Vaxora<span style="color:#f59e0b;">.</span></div>
                <div style="font-size:0.7rem;color:#6b6b9a;font-weight:500;">Admin Panel</div>
            </div>
        </div>
    </div>

    <div class="sidebar-section">Dashboard</div>
    <a href="/admin/dashboard.php"><i class="fas fa-tachometer-alt"></i> Overview</a>
    <a href="/admin/reports.php"><i class="fas fa-chart-bar"></i> Reports & Analytics</a>

    <div class="sidebar-section">Management</div>
    <a href="/admin/manage_hospitals.php"><i class="fas fa-hospital"></i> Hospitals</a>
    <a href="/admin/manage_vaccines.php"><i class="fas fa-vial"></i> Vaccines</a>
    <a href="/admin/appointments.php"><i class="fas fa-calendar-check"></i> Appointments</a>
    <a href="/admin/users.php"><i class="fas fa-users"></i> Parent Accounts</a>

    <div class="sidebar-section">Support</div>
    <a href="/admin/inquiries.php"><i class="fas fa-envelope"></i> Inquiries</a>

    <div style="border-top:1px solid rgba(255,255,255,0.06);margin:16px 0;"></div>
    <a href="/auth/logout.php" style="color:#fca5a5 !important;"><i class="fas fa-sign-out-alt"></i> Sign Out</a>
</div>

<div class="admin-content">
<div class="admin-topbar">
    <div>
        <h5 class="fw-bold mb-0"><?php echo $pageTitle ?? 'Dashboard'; ?></h5>
        <div class="text-muted" style="font-size:0.78rem;"><?= date('l, d F Y') ?></div>
    </div>
    <div class="d-flex align-items-center gap-3">
        <div class="position-relative">
            <a href="/admin/inquiries.php" class="btn btn-sm" style="background:#f5f3ff;color:#7c3aed;border:1px solid rgba(124,58,237,0.2);">
                <i class="fas fa-bell me-1"></i>
                <?php
                try {
                    $db_tmp = (new Database())->getConnection();
                    $pending_count = $db_tmp->query("SELECT COUNT(*) FROM contact_messages WHERE status='pending'")->fetchColumn();
                    if ($pending_count > 0) echo '<span class="badge bg-danger rounded-pill" style="font-size:0.65rem;padding:2px 6px;">' . $pending_count . '</span>';
                } catch(Exception $e) {}
                ?>
            </a>
        </div>
        <div class="d-flex align-items-center gap-2">
            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:36px;height:36px;background:linear-gradient(135deg,#7c3aed,#5b21b6);font-size:0.9rem;color:#fff;">
                <i class="fas fa-user-shield"></i>
            </div>
            <div>
                <div style="font-size:0.8rem;font-weight:600;color:#1e1b4b;"><?= htmlspecialchars(explode('@',$_SESSION['email'] ?? '')[0]) ?></div>
                <div style="font-size:0.7rem;color:#9ca3af;">Administrator</div>
            </div>
        </div>
    </div>
</div>
<div class="admin-body">
