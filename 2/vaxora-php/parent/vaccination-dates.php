<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/auth.php';
requireParent();
$db=$getDB=getDB(); $uid=$_SESSION['user_id'];

$upcoming=$db->prepare('SELECT a.*,c.name AS cname,c.dob,v.name AS vname,h.name AS hname FROM appointments a JOIN children c ON a.child_id=c.id JOIN vaccines v ON a.vaccine_id=v.id JOIN hospitals h ON a.hospital_id=h.id WHERE a.parent_id=? AND a.appointment_date>=CURDATE() AND a.status IN("approved","pending") ORDER BY a.appointment_date ASC');
$upcoming->execute([$uid]); $upcoming=$upcoming->fetchAll();

$dashRole='parent'; $dashTitle='Vaccination Dates'; $activeKey='vax_dates';
$dashNav=[['label'=>'Dashboard','key'=>'dashboard','url'=>'/parent/index.php','icon'=>''],['label'=>'My Children','key'=>'children','url'=>'/parent/children.php','icon'=>''],['label'=>'Vaccination Dates','key'=>'vax_dates','url'=>'/parent/vaccination-dates.php','icon'=>''],['label'=>'Book Hospital','key'=>'book','url'=>'/parent/book-hospital.php','icon'=>''],['label'=>'My Requests','key'=>'requests','url'=>'/parent/requests.php','icon'=>''],['label'=>'Vaccination Report','key'=>'reports','url'=>'/parent/reports.php','icon'=>''],['label'=>'My Profile','key'=>'profile','url'=>'/parent/profile.php','icon'=>'']];
require dirname(__DIR__) . '/includes/dash-header.php';
?>
<?php if (!$upcoming): ?>
<div class="card text-center" style="padding:48px">
  <div style="font-size:3rem;margin-bottom:16px">&#128197;</div>
  <h3>No Upcoming Vaccinations</h3>
  <p class="text-muted mt-8">You have no approved or pending appointments coming up.</p>
  <a href="<?= SITE_URL ?>/parent/book-hospital.php" class="btn btn-primary mt-24">Book Vaccination</a>
</div>
<?php else: ?>
<?php foreach ($upcoming as $a):
  $dob=new DateTime($a['dob']); $now=new DateTime(); $age=$dob->diff($now);
  $ageStr=$age->y>0?$age->y.' yrs':$age->m.' months';
  $apptDate=new DateTime($a['appointment_date']);
  $daysLeft=$now->diff($apptDate)->days;
  $isToday=$apptDate->format('Y-m-d')===date('Y-m-d');
?>
<div class="card mb-16" style="border-left:4px solid <?= $a['status']==='approved'?'#90E300':'#f59e0b' ?>">
  <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:12px">
    <div>
      <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px">
        <span style="font-size:1.4rem"><?= $isToday?'&#128197;':'&#9201;' ?></span>
        <h3 style="font-size:1.05rem"><?= e($a['vname']) ?></h3>
        <span class="badge <?= $a['status']==='approved'?'badge-lime':'badge-yellow' ?>"><?= ucfirst($a['status']) ?></span>
      </div>
      <div class="text-muted" style="font-size:0.88rem">Child: <strong><?= e($a['cname']) ?></strong> (<?= $ageStr ?>)</div>
      <div class="text-muted" style="font-size:0.88rem;margin-top:4px">Hospital: <?= e($a['hname']) ?></div>
    </div>
    <div style="text-align:right">
      <div style="font-size:1.6rem;font-weight:800;color:<?= $isToday?'#90E300':'#1C2706' ?>"><?= date('d', strtotime($a['appointment_date'])) ?></div>
      <div style="font-size:0.85rem;color:#5a6a40"><?= date('M Y', strtotime($a['appointment_date'])) ?></div>
      <?php if ($a['appointment_time']): ?><div style="font-size:0.82rem;color:#888"><?= date('h:i A', strtotime($a['appointment_time'])) ?></div><?php endif; ?>
      <?php if (!$isToday): ?><div class="badge badge-blue mt-8">in <?= $daysLeft ?> day<?= $daysLeft!=1?'s':'' ?></div><?php else: ?><div class="badge badge-lime mt-8">Today!</div><?php endif; ?>
    </div>
  </div>
</div>
<?php endforeach; ?>
<?php endif; ?>
<?php require dirname(__DIR__) . '/includes/dash-footer.php'; ?>
