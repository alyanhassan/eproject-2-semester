<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/auth.php';
requireParent();

$db  = getDB();
$uid = $_SESSION['user_id'];

$childrenStmt = $db->prepare('SELECT * FROM children WHERE parent_id=?');
$childrenStmt->execute([$uid]);
$children = $childrenStmt->fetchAll();

$upcomingStmt = $db->prepare('
    SELECT a.*, c.name AS cname, v.name AS vname, h.name AS hname
    FROM appointments a
    JOIN children c  ON a.child_id    = c.id
    JOIN vaccines v  ON a.vaccine_id  = v.id
    JOIN hospitals h ON a.hospital_id = h.id
    WHERE a.parent_id=? AND a.appointment_date>=CURDATE() AND a.status IN("approved","pending")
    ORDER BY a.appointment_date ASC LIMIT 5
');
$upcomingStmt->execute([$uid]);
$upcoming = $upcomingStmt->fetchAll();

$pendingStmt = $db->prepare('SELECT COUNT(*) FROM appointments WHERE parent_id=? AND status="pending"');
$pendingStmt->execute([$uid]);
$pending = $pendingStmt->fetchColumn();

$completedStmt = $db->prepare('
    SELECT COUNT(*) FROM vaccination_records vr
    JOIN children c ON vr.child_id = c.id
    WHERE c.parent_id=? AND vr.status="completed"
');
$completedStmt->execute([$uid]);
$completed = $completedStmt->fetchColumn();

$dashRole  = 'parent';
$dashTitle = 'Parent Dashboard';
$activeKey = 'dashboard';
$dashNav   = [
    ['label'=>'Dashboard',         'key'=>'dashboard', 'url'=>'/parent/index.php',           'icon'=>'<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>'],
    ['label'=>'My Children',       'key'=>'children',  'url'=>'/parent/children.php',        'icon'=>'<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>'],
    ['label'=>'Vaccination Dates', 'key'=>'vax_dates', 'url'=>'/parent/vaccination-dates.php','icon'=>'<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>'],
    ['label'=>'Book Hospital',     'key'=>'book',      'url'=>'/parent/book-hospital.php',   'icon'=>'<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>'],
    ['label'=>'My Requests',       'key'=>'requests',  'url'=>'/parent/requests.php',        'icon'=>'<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/></svg>'],
    ['label'=>'Vaccination Report','key'=>'reports',   'url'=>'/parent/reports.php',         'icon'=>'<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>'],
    ['label'=>'My Profile',        'key'=>'profile',   'url'=>'/parent/profile.php',         'icon'=>'<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>'],
];
require dirname(__DIR__) . '/includes/dash-header.php';
?>

<div class="widget-grid">
    <div class="widget">
        <div class="widget-icon lime" style="color:#4a7c00">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
        </div>
        <div class="widget-value"><?= count($children) ?></div>
        <div class="widget-label">My Children</div>
    </div>
    <div class="widget">
        <div class="widget-icon blue" style="color:#2467E3">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        </div>
        <div class="widget-value"><?= count($upcoming) ?></div>
        <div class="widget-label">Upcoming Appointments</div>
    </div>
    <div class="widget">
        <div class="widget-icon orange" style="color:#b56b00">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        </div>
        <div class="widget-value"><?= $pending ?></div>
        <div class="widget-label">Pending Requests</div>
    </div>
    <div class="widget">
        <div class="widget-icon lime" style="color:#4a7c00">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><polyline points="20 6 9 17 4 12"/></svg>
        </div>
        <div class="widget-value"><?= $completed ?></div>
        <div class="widget-label">Vaccinations Done</div>
    </div>
</div>

<?php if ($upcoming): ?>
<div class="card" style="padding:0;margin-bottom:24px">
    <div class="card-header" style="display:flex;justify-content:space-between;align-items:center">
        Upcoming Appointments
        <a href="<?= SITE_URL ?>/parent/vaccination-dates.php" class="btn btn-sm btn-outline-dark">View All</a>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Date</th><th>Child</th><th>Vaccine</th><th>Hospital</th><th>Status</th></tr></thead>
            <tbody>
                <?php foreach ($upcoming as $a): ?>
                <tr>
                    <td><strong><?= date('d M Y', strtotime($a['appointment_date'])) ?></strong></td>
                    <td><?= e($a['cname']) ?></td>
                    <td><?= e($a['vname']) ?></td>
                    <td><?= e($a['hname']) ?></td>
                    <td>
                        <?php $cl = ['pending'=>'badge-yellow','approved'=>'badge-blue']; ?>
                        <span class="badge <?= $cl[$a['status']] ?? 'badge-gray' ?>"><?= ucfirst($a['status']) ?></span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<div class="grid-2" style="gap:24px">
    <!-- Children list -->
    <div class="card">
        <h3 style="font-size:1rem;margin-bottom:16px">My Children</h3>
        <?php if ($children): ?>
            <?php foreach ($children as $child):
                $dob    = new DateTime($child['dob']);
                $now    = new DateTime();
                $age    = $dob->diff($now);
                $ageStr = $age->y > 0 ? $age->y . ' years' : $age->m . ' months';
            ?>
            <div style="display:flex;justify-content:space-between;align-items:center;padding:11px 0;border-bottom:1px solid var(--border)">
                <div>
                    <div style="font-weight:600;font-size:0.92rem"><?= e($child['name']) ?></div>
                    <div class="text-muted" style="font-size:0.8rem"><?= $ageStr ?> &middot; <?= ucfirst($child['gender']) ?> &middot; <?= e($child['blood_group'] ?: '—') ?></div>
                </div>
                <a href="<?= SITE_URL ?>/parent/children.php?edit=<?= $child['id'] ?>" class="btn btn-sm btn-outline-dark">Edit</a>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-muted" style="font-size:0.88rem">No children added yet. <a href="<?= SITE_URL ?>/parent/children.php" style="color:#4a7c00">Add your child</a> to get started.</p>
        <?php endif; ?>
        <div class="mt-16">
            <a href="<?= SITE_URL ?>/parent/children.php" class="btn btn-primary btn-sm">+ Add Child</a>
        </div>
    </div>

    <!-- Quick actions -->
    <div class="card">
        <h3 style="font-size:1rem;margin-bottom:16px">Quick Actions</h3>
        <div style="display:flex;flex-direction:column;gap:10px">
            <a href="<?= SITE_URL ?>/parent/book-hospital.php" class="btn btn-primary">Book Vaccination</a>
            <a href="<?= SITE_URL ?>/parent/children.php"      class="btn btn-outline-dark">Manage Children</a>
            <a href="<?= SITE_URL ?>/parent/reports.php"       class="btn btn-outline-dark">View Reports</a>
            <a href="<?= SITE_URL ?>/parent/profile.php"       class="btn btn-outline-dark">Update Profile</a>
        </div>
    </div>
</div>

<?php require dirname(__DIR__) . '/includes/dash-footer.php'; ?>
