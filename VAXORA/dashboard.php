<?php
require_once 'includes/auth_check.php';
checkRole(['parent']);
require_once 'config/config.php';
require_once 'config/database.php';

$db = (new Database())->getConnection();
$parentStmt = $db->prepare("SELECT parent_id, full_name FROM parents WHERE user_id = :uid LIMIT 1");
$parentStmt->execute([':uid' => $_SESSION['user_id']]);
$parent = $parentStmt->fetch(PDO::FETCH_ASSOC);
if (!$parent) { session_destroy(); redirect('/auth/login.php'); }
$pid = (int)$parent['parent_id'];

$childrenCount = (int)$db->prepare("SELECT COUNT(*) FROM children WHERE parent_id=?")->execute([$pid]) ? $db->query("SELECT COUNT(*) FROM children WHERE parent_id=$pid")->fetchColumn() : 0;

$s1 = $db->prepare("SELECT COUNT(*) FROM children WHERE parent_id=?"); $s1->execute([$pid]); $childrenCount = (int)$s1->fetchColumn();
$s2 = $db->prepare("SELECT COUNT(*) FROM appointments a JOIN children c ON a.child_id=c.child_id WHERE c.parent_id=? AND a.status='pending'"); $s2->execute([$pid]); $pendingCount = (int)$s2->fetchColumn();
$s3 = $db->prepare("SELECT COUNT(*) FROM vaccination_records vr JOIN children c ON vr.child_id=c.child_id WHERE c.parent_id=?"); $s3->execute([$pid]); $doneCount = (int)$s3->fetchColumn();
$s4 = $db->prepare("SELECT COUNT(*) FROM appointments a JOIN children c ON a.child_id=c.child_id WHERE c.parent_id=? AND a.status='approved'"); $s4->execute([$pid]); $approvedCount = (int)$s4->fetchColumn();

$upcomingStmt = $db->prepare("
    SELECT a.*, c.full_name as child_name, h.hospital_name, v.vaccine_name
    FROM appointments a
    JOIN children c ON a.child_id=c.child_id
    JOIN hospitals h ON a.hospital_id=h.hospital_id
    JOIN vaccines v ON a.vaccine_id=v.vaccine_id
    WHERE c.parent_id=? AND a.status IN ('pending','approved')
    ORDER BY a.appointment_date ASC LIMIT 5
");
$upcomingStmt->execute([$pid]);
$upcoming = $upcomingStmt->fetchAll(PDO::FETCH_ASSOC);

$recentStmt = $db->prepare("
    SELECT vr.*, c.full_name as child_name, v.vaccine_name, h.hospital_name, c.child_id
    FROM vaccination_records vr
    JOIN children c ON vr.child_id=c.child_id
    JOIN vaccines v ON vr.vaccine_id=v.vaccine_id
    JOIN hospitals h ON vr.hospital_id=h.hospital_id
    WHERE c.parent_id=?
    ORDER BY vr.administration_date DESC LIMIT 5
");
$recentStmt->execute([$pid]);
$recent = $recentStmt->fetchAll(PDO::FETCH_ASSOC);

$childrenList = $db->prepare("SELECT c.*, (SELECT COUNT(*) FROM vaccination_records v WHERE v.child_id=c.child_id) as vacc_count FROM children c WHERE c.parent_id=? ORDER BY c.full_name LIMIT 4");
$childrenList->execute([$pid]);
$childrenList = $childrenList->fetchAll(PDO::FETCH_ASSOC);

$name_parts = explode(' ', $parent['full_name']);
$first_name = $name_parts[0];

include 'includes/header.php';
?>

<div class="container-fluid py-4 px-4">
    <!-- Welcome Banner -->
    <div class="welcome-banner">
        <div class="row align-items-center" style="position:relative;z-index:1;">
            <div class="col-md-8">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div style="width:54px;height:54px;background:rgba(98,125,45,0.40);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1.4rem;border:2px solid rgba(98,125,45,0.50);">
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <div>
                        <div style="font-size:0.8rem;color:#D8E8A8;font-weight:500;">Welcome back,</div>
                        <h3 class="fw-bold mb-0"><?= htmlspecialchars($first_name) ?> 👋</h3>
                    </div>
                </div>
                <p style="color:#C8BE9A;font-size:0.9rem;max-width:460px;margin:0;">
                    You have <strong class="text-white"><?= $childrenCount ?> child<?= $childrenCount!=1?'ren':'' ?></strong> registered
                    and <strong class="text-white"><?= $pendingCount + $approvedCount ?> upcoming appointment<?= ($pendingCount+$approvedCount)!=1?'s':'' ?></strong>.
                    <?= $doneCount > 0 ? "Great job — <strong class='text-yellow-300'>$doneCount vaccination" . ($doneCount!=1?'s':'') . " completed!</strong>" : '' ?>
                </p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <a href="/book_appointment.php" class="btn btn-primary px-4 py-2 fw-bold me-2">
                    <i class="fas fa-calendar-plus me-2"></i>Book Appointment
                </a>
                <a href="/children.php" class="btn btn-sm py-2 px-3 fw-semibold" style="background:rgba(255,255,255,0.1);color:#e2e8f0;border:1px solid rgba(255,255,255,0.2);">
                    <i class="fas fa-plus me-1"></i>Add Child
                </a>
            </div>
        </div>
    </div>

    <!-- Stat Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-xl-3">
            <div class="card stat-card sc-violet">
                <div class="stat-num"><?= $childrenCount ?></div>
                <div class="stat-label">Registered Children</div>
                <i class="fas fa-child stat-icon"></i>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card stat-card sc-amber">
                <div class="stat-num"><?= $pendingCount ?></div>
                <div class="stat-label">Pending Appointments</div>
                <i class="fas fa-clock stat-icon"></i>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card stat-card sc-cyan">
                <div class="stat-num"><?= $approvedCount ?></div>
                <div class="stat-label">Confirmed Bookings</div>
                <i class="fas fa-calendar-check stat-icon"></i>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card stat-card sc-green">
                <div class="stat-num"><?= $doneCount ?></div>
                <div class="stat-label">Vaccinations Done</div>
                <i class="fas fa-check-double stat-icon"></i>
            </div>
        </div>
    </div>

    <!-- Children Cards Row -->
    <?php if ($childrenList): ?>
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0">My Children</h5>
            <a href="/children.php" class="btn btn-sm btn-outline-secondary">View All</a>
        </div>
        <div class="row g-3">
            <?php foreach ($childrenList as $c):
                $age = (new DateTime())->diff(new DateTime($c['date_of_birth']))->y;
                $colors = ['#4A6120','#8B5E3C','#4A7C59','#C8A84B'];
                $ci = array_search($c, $childrenList);
                $col = $colors[$ci % 4];
            ?>
            <div class="col-6 col-lg-3">
                <div class="card p-3 text-center h-100">
                    <div class="rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center" style="width:56px;height:56px;background:<?= $col ?>1a;font-size:1.6rem;border:2px solid <?= $col ?>33;">
                        <?= $c['gender']==='female' ? '👧' : '👦' ?>
                    </div>
                    <div class="fw-semibold" style="font-size:0.88rem;"><?= htmlspecialchars($c['full_name']) ?></div>
                    <div class="text-muted" style="font-size:0.75rem;"><?= $age ?> yrs · <?= ucfirst($c['gender']??'') ?></div>
                    <div class="mt-2"><span class="badge bg-success" style="font-size:0.68rem;"><?= $c['vacc_count'] ?> vaccinated</span></div>
                    <div class="mt-2 d-flex gap-1">
                        <a href="/book_appointment.php?child_id=<?= $c['child_id'] ?>" class="btn btn-xs btn-primary flex-fill" style="font-size:0.72rem;padding:4px;">Book</a>
                        <a href="/certificate.php?child_id=<?= $c['child_id'] ?>" class="btn btn-xs flex-fill" style="font-size:0.72rem;padding:4px;background:#F4F0E6;color:#4A6120;border:1px solid rgba(74,97,32,0.2);">Cert</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            <div class="col-6 col-lg-3">
                <a href="/add_child.php" class="card p-3 text-center h-100 text-decoration-none d-flex align-items-center justify-content-center flex-column" style="border:2px dashed #d1d5db;min-height:160px;">
                    <div style="width:48px;height:48px;background:#F4F0E6;border-radius:50%;display:flex;align-items:center;justify-content:center;margin-bottom:8px;">
                        <i class="fas fa-plus" style="color:#4A6120;"></i>
                    </div>
                    <div class="fw-semibold text-muted" style="font-size:0.85rem;">Add Child</div>
                </a>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="row g-4">
        <!-- Upcoming Appointments -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span class="fw-semibold"><i class="fas fa-calendar-check me-2" style="color:#4A6120;"></i>Upcoming Appointments</span>
                    <a href="/book_appointment.php" class="btn btn-sm btn-primary">+ Book</a>
                </div>
                <div class="card-body p-0">
                    <?php if ($upcoming): ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead><tr><th>Child</th><th>Vaccine</th><th>Date</th><th>Status</th></tr></thead>
                            <tbody>
                            <?php foreach ($upcoming as $a): ?>
                            <tr>
                                <td class="fw-semibold"><?= htmlspecialchars($a['child_name']) ?></td>
                                <td><span class="badge" style="background:#F4F0E6;color:#4A6120;border:1px solid rgba(74,97,32,0.2);"><?= htmlspecialchars($a['vaccine_name']) ?></span></td>
                                <td style="font-size:0.82rem;"><?= date('d M Y', strtotime($a['appointment_date'])) ?></td>
                                <td><span class="badge <?= $a['status']==='approved' ? 'bg-success' : 'bg-warning text-dark' ?>"><?= ucfirst($a['status']) ?></span></td>
                            </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-5">
                        <div style="font-size:3rem;margin-bottom:12px;">📅</div>
                        <p class="text-muted mb-3" style="font-size:0.9rem;">No upcoming appointments</p>
                        <a href="/book_appointment.php" class="btn btn-primary btn-sm">Book Now</a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Recent Vaccinations -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span class="fw-semibold"><i class="fas fa-notes-medical me-2 text-success"></i>Recent Vaccinations</span>
                    <a href="/vaccination_history.php" class="btn btn-sm btn-outline-success">View All</a>
                </div>
                <div class="card-body p-0">
                    <?php if ($recent): ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead><tr><th>Child</th><th>Vaccine</th><th>Hospital</th><th>Date</th></tr></thead>
                            <tbody>
                            <?php foreach ($recent as $r): ?>
                            <tr>
                                <td class="fw-semibold"><?= htmlspecialchars($r['child_name']) ?></td>
                                <td style="font-size:0.82rem;"><?= htmlspecialchars($r['vaccine_name']) ?></td>
                                <td class="text-muted" style="font-size:0.78rem;"><?= htmlspecialchars(explode(' ', $r['hospital_name'])[0]) ?>...</td>
                                <td style="font-size:0.82rem;"><?= date('d M Y', strtotime($r['administration_date'])) ?></td>
                            </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-5">
                        <div style="font-size:3rem;margin-bottom:12px;">💉</div>
                        <p class="text-muted" style="font-size:0.9rem;">No vaccination records yet</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="row g-3 mt-2">
        <div class="col-12"><h5 class="fw-bold">Quick Access</h5></div>
        <?php
        $quicklinks = [
            ['/book_appointment.php',     'fas fa-calendar-plus', 'Book Appointment',   '#4A6120', '#F4F0E6'],
            ['/vaccination_history.php',  'fas fa-notes-medical', 'Vaccination History','#4A7C59', '#f0fdf4'],
            ['/children.php',             'fas fa-child',         'My Children',        '#8B5E3C', '#F8F4EC'],
            ['/contact.php',              'fas fa-headset',       'Get Support',        '#C8A84B', '#fffbeb'],
        ];
        foreach ($quicklinks as $ql): ?>
        <div class="col-6 col-md-3">
            <a href="<?= $ql[0] ?>" class="card p-3 d-flex flex-row align-items-center gap-3 text-decoration-none">
                <div style="width:44px;height:44px;background:<?= $ql[4] ?>;border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="<?= $ql[1] ?>" style="color:<?= $ql[2] ?>;font-size:1.1rem;"></i>
                </div>
                <span class="fw-semibold" style="font-size:0.85rem;color:#2C1F0A;"><?= $ql[3] ?></span>
            </a>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php include 'includes/footer.php'; ?>
