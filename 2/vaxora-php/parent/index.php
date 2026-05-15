<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/auth.php';
requireParent();
$db  = getDB();
$uid = $_SESSION['user_id'];

$children  = $db->prepare('SELECT * FROM children WHERE parent_id=?'); $children->execute([$uid]); $children=$children->fetchAll();
$upcoming  = $db->prepare('SELECT a.*,c.name AS cname,v.name AS vname,h.name AS hname FROM appointments a JOIN children c ON a.child_id=c.id JOIN vaccines v ON a.vaccine_id=v.id JOIN hospitals h ON a.hospital_id=h.id WHERE a.parent_id=? AND a.appointment_date>=CURDATE() AND a.status IN("approved","pending") ORDER BY a.appointment_date ASC LIMIT 5'); $upcoming->execute([$uid]); $upcoming=$upcoming->fetchAll();
$pending   = $db->prepare('SELECT COUNT(*) FROM appointments WHERE parent_id=? AND status="pending"'); $pending->execute([$uid]); $pending=$pending->fetchColumn();
$completed = $db->prepare('SELECT COUNT(*) FROM vaccination_records vr JOIN children c ON vr.child_id=c.id WHERE c.parent_id=? AND vr.status="completed"'); $completed->execute([$uid]); $completed=$completed->fetchColumn();

$dashRole='parent'; $dashTitle='Parent Dashboard'; $activeKey='dashboard';
$dashNav=[['label'=>'Dashboard','key'=>'dashboard','url'=>'/parent/index.php','icon'=>'<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>'],['label'=>'My Children','key'=>'children','url'=>'/parent/children.php','icon'=>'<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>'],['label'=>'Vaccination Dates','key'=>'vax_dates','url'=>'/parent/vaccination-dates.php','icon'=>'<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="3" y1="10" x2="21" y2="10"/></svg>'],['label'=>'Book Hospital','key'=>'book','url'=>'/parent/book-hospital.php','icon'=>'<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>'],['label'=>'My Requests','key'=>'requests','url'=>'/parent/requests.php','icon'=>'<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/></svg>'],['label'=>'Vaccination Report','key'=>'reports','url'=>'/parent/reports.php','icon'=>'<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16h16V8z"/></svg>'],['label'=>'My Profile','key'=>'profile','url'=>'/parent/profile.php','icon'=>'<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>']];
require dirname(__DIR__) . '/includes/dash-header.php';
?>
<div class="widget-grid">
  <div class="widget"><div class="widget-icon lime">&#128100;</div><div class="widget-value"><?= count($children) ?></div><div class="widget-label">My Children</div></div>
  <div class="widget"><div class="widget-icon blue">&#128197;</div><div class="widget-value"><?= count($upcoming) ?></div><div class="widget-label">Upcoming Appointments</div></div>
  <div class="widget"><div class="widget-icon orange">&#9203;</div><div class="widget-value"><?= $pending ?></div><div class="widget-label">Pending Requests</div></div>
  <div class="widget"><div class="widget-icon lime">&#10003;</div><div class="widget-value"><?= $completed ?></div><div class="widget-label">Vaccinations Done</div></div>
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
          <td><?php $cl=['pending'=>'badge-yellow','approved'=>'badge-blue']; ?>
              <span class="badge <?= $cl[$a['status']]??'badge-gray' ?>"><?= ucfirst($a['status']) ?></span></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php endif; ?>

<div class="grid-2" style="gap:24px">
  <div class="card">
    <h3 style="font-size:1rem;margin-bottom:16px">My Children</h3>
    <?php if ($children): ?>
      <?php foreach ($children as $child):
        $dob = new DateTime($child['dob']); $now = new DateTime();
        $age = $dob->diff($now); $ageStr = $age->y>0 ? $age->y.' years' : $age->m.' months';
      ?>
      <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:1px solid var(--border)">
        <div>
          <strong><?= e($child['name']) ?></strong>
          <div class="text-muted" style="font-size:0.82rem"><?= $ageStr ?> · <?= ucfirst($child['gender']) ?> · <?= e($child['blood_group']?:'—') ?></div>
        </div>
        <a href="<?= SITE_URL ?>/parent/children.php" class="btn btn-sm btn-outline-dark">Edit</a>
      </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p class="text-muted">No children added yet. <a href="<?= SITE_URL ?>/parent/children.php" style="color:#4a7c00">Add your child</a></p>
    <?php endif; ?>
    <div class="mt-16"><a href="<?= SITE_URL ?>/parent/children.php" class="btn btn-primary btn-sm">+ Add Child</a></div>
  </div>
  <div class="card">
    <h3 style="font-size:1rem;margin-bottom:16px">Quick Actions</h3>
    <div style="display:flex;flex-direction:column;gap:10px">
      <a href="<?= SITE_URL ?>/parent/book-hospital.php" class="btn btn-primary">Book Vaccination</a>
      <a href="<?= SITE_URL ?>/parent/children.php" class="btn btn-outline-dark">Manage Children</a>
      <a href="<?= SITE_URL ?>/parent/reports.php" class="btn btn-outline-dark">View Reports</a>
      <a href="<?= SITE_URL ?>/parent/profile.php" class="btn btn-outline-dark">Update Profile</a>
    </div>
  </div>
</div>
<?php require dirname(__DIR__) . '/includes/dash-footer.php'; ?>
