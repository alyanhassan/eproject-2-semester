<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/auth.php';
requireHospital();
$db  = getDB();
$uid = $_SESSION['user_id'];

// Get hospital record for this user
$hospital = $db->prepare('SELECT * FROM hospitals WHERE user_id=?');
$hospital->execute([$uid]);
$hospital = $hospital->fetch();

if (!$hospital) {
    // Hospital registered but not yet linked/approved
    echo '<div style="font-family:sans-serif;padding:60px;text-align:center"><h2>Account Pending Approval</h2><p>Your hospital registration is under review. Admin will activate your account shortly.</p><a href="' . SITE_URL . '/logout.php">Logout</a></div>';
    exit;
}

$hid = $hospital['id'];

$pending   = $db->prepare('SELECT COUNT(*) FROM appointments WHERE hospital_id=? AND status="approved"');   $pending->execute([$hid]);   $pending=$pending->fetchColumn();
$completed = $db->prepare('SELECT COUNT(*) FROM vaccination_records WHERE hospital_id=? AND status="completed"'); $completed->execute([$hid]); $completed=$completed->fetchColumn();
$today     = $db->prepare('SELECT COUNT(*) FROM appointments WHERE hospital_id=? AND appointment_date=CURDATE() AND status="approved"'); $today->execute([$hid]); $today=$today->fetchColumn();

$upcoming  = $db->prepare('SELECT a.*,c.name AS cname,c.dob,u.name AS pname,u.phone AS pphone,v.name AS vname FROM appointments a JOIN children c ON a.child_id=c.id JOIN users u ON a.parent_id=u.id JOIN vaccines v ON a.vaccine_id=v.id WHERE a.hospital_id=? AND a.appointment_date>=CURDATE() AND a.status="approved" ORDER BY a.appointment_date ASC LIMIT 10');
$upcoming->execute([$hid]); $upcoming=$upcoming->fetchAll();

$dashRole='hospital'; $dashTitle='Hospital Dashboard'; $activeKey='dashboard';
$dashNav=[
    ['label'=>'Dashboard',       'key'=>'dashboard',  'url'=>'/hospital/index.php',         'icon'=>'<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>'],
    ['label'=>'Appointments',    'key'=>'vaccine',    'url'=>'/hospital/vaccine-status.php','icon'=>'<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/><line x1="9" y1="12" x2="15" y2="12"/><line x1="9" y1="16" x2="12" y2="16"/></svg>'],
];
require dirname(__DIR__) . '/includes/dash-header.php';
?>

<!-- Hospital Info Card -->
<div class="card mb-24" style="background:var(--dark);color:white;border:none">
  <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:16px">
    <div>
      <h2 style="color:var(--accent);margin-bottom:4px"><?= e($hospital['name']) ?></h2>
      <div style="color:rgba(255,255,255,0.7);font-size:0.9rem">
        <?= e($hospital['city']) ?><?= $hospital['address'] ? ' · ' . e($hospital['address']) : '' ?>
      </div>
      <?php if ($hospital['phone']): ?><div style="color:rgba(255,255,255,0.6);font-size:0.85rem;margin-top:4px">&#128222; <?= e($hospital['phone']) ?></div><?php endif; ?>
    </div>
    <div style="text-align:right">
      <div style="font-size:1.8rem;font-weight:800;color:var(--accent)"><?= $hospital['rating'] ?></div>
      <div style="color:rgba(255,255,255,0.5);font-size:0.8rem">Rating</div>
    </div>
  </div>
  <?php if ($hospital['services']): ?>
  <div style="margin-top:16px;display:flex;gap:8px;flex-wrap:wrap">
    <?php foreach (explode(',', $hospital['services']) as $svc): ?>
    <span style="background:rgba(144,227,0,0.15);color:#90E300;padding:3px 10px;border-radius:100px;font-size:0.78rem"><?= e(trim($svc)) ?></span>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>

<div class="widget-grid" style="margin-bottom:24px">
  <div class="widget"><div class="widget-icon lime">&#128197;</div><div class="widget-value" style="color:#4a7c00"><?= $today ?></div><div class="widget-label">Appointments Today</div></div>
  <div class="widget"><div class="widget-icon blue">&#9200;</div><div class="widget-value"><?= $pending ?></div><div class="widget-label">Pending Vaccinations</div></div>
  <div class="widget"><div class="widget-icon lime">&#10003;</div><div class="widget-value"><?= $completed ?></div><div class="widget-label">Completed</div></div>
  <div class="widget">
    <div class="widget-icon orange">&#8594;</div>
    <a href="<?= SITE_URL ?>/hospital/vaccine-status.php" class="btn btn-primary" style="margin-top:8px">Update Status</a>
  </div>
</div>

<div class="card" style="padding:0">
  <div class="card-header">Upcoming Approved Appointments (<?= count($upcoming) ?>)</div>
  <div class="table-wrap">
    <table>
      <thead><tr><th>Date</th><th>Time</th><th>Child</th><th>Age</th><th>Parent</th><th>Contact</th><th>Vaccine</th><th>Action</th></tr></thead>
      <tbody>
        <?php foreach ($upcoming as $a):
          $dob=new DateTime($a['dob']); $now=new DateTime(); $age=$dob->diff($now);
          $ageStr=$age->y>0?$age->y.' yrs':$age->m.' mo';
        ?>
        <tr>
          <td><strong><?= date('d M Y',strtotime($a['appointment_date'])) ?></strong>
              <?php if ($a['appointment_date']===date('Y-m-d')): ?><br><span class="badge badge-lime">Today</span><?php endif; ?></td>
          <td><?= $a['appointment_time']?date('h:i A',strtotime($a['appointment_time'])):'—' ?></td>
          <td><?= e($a['cname']) ?></td>
          <td><?= $ageStr ?></td>
          <td><?= e($a['pname']) ?></td>
          <td><?= e($a['pphone']?:'—') ?></td>
          <td><?= e($a['vname']) ?></td>
          <td><a href="<?= SITE_URL ?>/hospital/vaccine-status.php?appt=<?= $a['id'] ?>" class="btn btn-primary btn-sm">Update</a></td>
        </tr>
        <?php endforeach; ?>
        <?php if (!$upcoming): ?><tr><td colspan="8" class="text-center text-muted" style="padding:28px">No upcoming appointments.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php require dirname(__DIR__) . '/includes/dash-footer.php'; ?>
