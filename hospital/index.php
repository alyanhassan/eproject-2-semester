<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/auth.php';
requireHospital();

$db  = getDB();
$uid = $_SESSION['user_id'];

$hospitalStmt = $db->prepare('SELECT * FROM hospitals WHERE user_id=?');
$hospitalStmt->execute([$uid]);
$hospital = $hospitalStmt->fetch();

if (!$hospital) {
    echo '<div style="font-family:Inter,sans-serif;padding:60px;text-align:center;max-width:500px;margin:60px auto">
        <h2 style="margin-bottom:12px">Account Pending Activation</h2>
        <p style="color:#666;line-height:1.7">Your hospital registration is under review. An admin will activate your account shortly.</p>
        <a href="' . SITE_URL . '/logout.php" style="display:inline-block;margin-top:24px;color:#e53e3e">Logout</a>
    </div>';
    exit;
}

$hid = $hospital['id'];

$todayStmt = $db->prepare('SELECT COUNT(*) FROM appointments WHERE hospital_id=? AND appointment_date=CURDATE() AND status="approved"');
$todayStmt->execute([$hid]);
$today = $todayStmt->fetchColumn();

$pendingStmt = $db->prepare('SELECT COUNT(*) FROM appointments WHERE hospital_id=? AND status="approved"');
$pendingStmt->execute([$hid]);
$pending = $pendingStmt->fetchColumn();

$completedStmt = $db->prepare('SELECT COUNT(*) FROM vaccination_records WHERE hospital_id=? AND status="completed"');
$completedStmt->execute([$hid]);
$completed = $completedStmt->fetchColumn();

$upcomingStmt = $db->prepare('
    SELECT a.*, c.name AS cname, c.dob, u.name AS pname, u.phone AS pphone, v.name AS vname
    FROM appointments a
    JOIN children c  ON a.child_id    = c.id
    JOIN users u     ON a.parent_id   = u.id
    JOIN vaccines v  ON a.vaccine_id  = v.id
    WHERE a.hospital_id=? AND a.appointment_date>=CURDATE() AND a.status="approved"
    ORDER BY a.appointment_date ASC LIMIT 10
');
$upcomingStmt->execute([$hid]);
$upcoming = $upcomingStmt->fetchAll();

$dashRole  = 'hospital';
$dashTitle = 'Hospital Dashboard';
$activeKey = 'dashboard';
$dashNav   = [
    ['label'=>'Dashboard',    'key'=>'dashboard', 'url'=>'/hospital/index.php',         'icon'=>'<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>'],
    ['label'=>'Appointments', 'key'=>'vaccine',   'url'=>'/hospital/vaccine-status.php','icon'=>'<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/><line x1="9" y1="12" x2="15" y2="12"/><line x1="9" y1="16" x2="12" y2="16"/></svg>'],
];
require dirname(__DIR__) . '/includes/dash-header.php';
?>

<!-- Hospital Identity Card -->
<div class="card mb-24" style="background:var(--dark);border:none">
    <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:16px">
        <div>
            <h2 style="color:var(--accent);margin-bottom:4px;font-size:1.2rem"><?= e($hospital['name']) ?></h2>
            <div style="color:rgba(255,255,255,0.6);font-size:0.88rem">
                <?= e($hospital['city']) ?><?= $hospital['address'] ? ' &middot; ' . e($hospital['address']) : '' ?>
            </div>
            <?php if ($hospital['phone']): ?>
            <div style="display:flex;align-items:center;gap:6px;color:rgba(255,255,255,0.5);font-size:0.82rem;margin-top:6px">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6A2 2 0 014.11 2h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6"/></svg>
                <?= e($hospital['phone']) ?>
            </div>
            <?php endif; ?>
        </div>
        <div style="text-align:right">
            <div style="font-size:2rem;font-weight:800;color:var(--accent)"><?= $hospital['rating'] ?></div>
            <div style="color:rgba(255,255,255,0.4);font-size:0.75rem;letter-spacing:0.04em">RATING</div>
        </div>
    </div>
    <?php if ($hospital['services']): ?>
    <div style="margin-top:16px;display:flex;gap:8px;flex-wrap:wrap">
        <?php foreach (explode(',', $hospital['services']) as $svc): ?>
        <span style="background:rgba(144,227,0,0.12);color:#90E300;padding:3px 10px;border-radius:100px;font-size:0.76rem"><?= e(trim($svc)) ?></span>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<!-- Widgets -->
<div class="widget-grid" style="margin-bottom:24px">
    <div class="widget">
        <div class="widget-icon lime" style="color:#4a7c00">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        </div>
        <div class="widget-value" style="color:#4a7c00"><?= $today ?></div>
        <div class="widget-label">Appointments Today</div>
    </div>
    <div class="widget">
        <div class="widget-icon blue" style="color:#2467E3">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        </div>
        <div class="widget-value"><?= $pending ?></div>
        <div class="widget-label">Awaiting Vaccination</div>
    </div>
    <div class="widget">
        <div class="widget-icon lime" style="color:#4a7c00">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><polyline points="20 6 9 17 4 12"/></svg>
        </div>
        <div class="widget-value"><?= $completed ?></div>
        <div class="widget-label">Completed</div>
    </div>
    <div class="widget" style="justify-content:center;align-items:flex-start">
        <div class="widget-label" style="margin-bottom:12px;font-weight:600">Update Status</div>
        <a href="<?= SITE_URL ?>/hospital/vaccine-status.php" class="btn btn-primary btn-sm">Go to Appointments</a>
    </div>
</div>

<!-- Upcoming Appointments Table -->
<div class="card" style="padding:0">
    <div class="card-header">Upcoming Approved Appointments (<?= count($upcoming) ?>)</div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>Date</th><th>Time</th><th>Child</th><th>Age</th><th>Parent</th><th>Contact</th><th>Vaccine</th><th>Action</th></tr>
            </thead>
            <tbody>
                <?php foreach ($upcoming as $a):
                    $dob    = new DateTime($a['dob']);
                    $now    = new DateTime();
                    $age    = $dob->diff($now);
                    $ageStr = $age->y > 0 ? $age->y . ' yrs' : $age->m . ' mo';
                    $isToday = $a['appointment_date'] === date('Y-m-d');
                ?>
                <tr>
                    <td>
                        <strong><?= date('d M Y', strtotime($a['appointment_date'])) ?></strong>
                        <?php if ($isToday): ?><br><span class="badge badge-lime">Today</span><?php endif; ?>
                    </td>
                    <td><?= $a['appointment_time'] ? date('h:i A', strtotime($a['appointment_time'])) : '—' ?></td>
                    <td><?= e($a['cname']) ?></td>
                    <td><?= $ageStr ?></td>
                    <td><?= e($a['pname']) ?></td>
                    <td><?= e($a['pphone'] ?: '—') ?></td>
                    <td><?= e($a['vname']) ?></td>
                    <td>
                        <a href="<?= SITE_URL ?>/hospital/vaccine-status.php?appt=<?= $a['id'] ?>" class="btn btn-primary btn-sm">Update</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (!$upcoming): ?>
                <tr><td colspan="8" class="text-center text-muted" style="padding:32px">No upcoming appointments.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require dirname(__DIR__) . '/includes/dash-footer.php'; ?>
