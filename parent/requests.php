<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/auth.php';
requireParent();
$db  = getDB();
$uid = $_SESSION['user_id'];

// Cancel appointment
if (isset($_GET['cancel'])) {
    $db->prepare('UPDATE appointments SET status="cancelled" WHERE id=? AND parent_id=? AND status="pending"')->execute([(int)$_GET['cancel'],$uid]);
    setFlash('success','Appointment cancelled.'); redirect(SITE_URL.'/parent/requests.php');
}

$appointments=$db->prepare('SELECT a.*,c.name AS cname,v.name AS vname,h.name AS hname,h.city,h.phone AS hphone FROM appointments a JOIN children c ON a.child_id=c.id JOIN vaccines v ON a.vaccine_id=v.id JOIN hospitals h ON a.hospital_id=h.id WHERE a.parent_id=? ORDER BY a.created_at DESC');
$appointments->execute([$uid]); $appointments=$appointments->fetchAll();

$dashRole='parent'; $dashTitle='My Requests'; $activeKey='requests';
$dashNav=[['label'=>'Dashboard','key'=>'dashboard','url'=>'/parent/index.php','icon'=>''],['label'=>'My Children','key'=>'children','url'=>'/parent/children.php','icon'=>''],['label'=>'Vaccination Dates','key'=>'vax_dates','url'=>'/parent/vaccination-dates.php','icon'=>''],['label'=>'Book Hospital','key'=>'book','url'=>'/parent/book-hospital.php','icon'=>''],['label'=>'My Requests','key'=>'requests','url'=>'/parent/requests.php','icon'=>''],['label'=>'Vaccination Report','key'=>'reports','url'=>'/parent/reports.php','icon'=>''],['label'=>'My Profile','key'=>'profile','url'=>'/parent/profile.php','icon'=>'']];
require dirname(__DIR__) . '/includes/dash-header.php';
?>
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px">
  <h2 style="font-size:1.1rem">My Appointment Requests (<?= count($appointments) ?>)</h2>
  <a href="<?= SITE_URL ?>/parent/book-hospital.php" class="btn btn-primary">+ New Booking</a>
</div>

<div class="card" style="padding:0">
  <div class="table-wrap">
    <table>
      <thead><tr><th>Booked On</th><th>Child</th><th>Vaccine</th><th>Hospital</th><th>City</th><th>Appt Date</th><th>Status</th><th>Action</th></tr></thead>
      <tbody>
        <?php foreach ($appointments as $a): ?>
        <tr>
          <td><?= date('d M Y', strtotime($a['created_at'])) ?></td>
          <td><?= e($a['cname']) ?></td>
          <td><?= e($a['vname']) ?></td>
          <td><?= e($a['hname']) ?><br><small class="text-muted"><?= e($a['hphone']) ?></small></td>
          <td><?= e($a['city']) ?></td>
          <td><strong><?= date('d M Y', strtotime($a['appointment_date'])) ?></strong>
              <?php if ($a['appointment_time']): ?><br><small><?= date('h:i A', strtotime($a['appointment_time'])) ?></small><?php endif; ?></td>
          <td><?php $cl=['pending'=>'badge-yellow','approved'=>'badge-blue','completed'=>'badge-lime','rejected'=>'badge-red','cancelled'=>'badge-gray']; ?>
              <span class="badge <?= $cl[$a['status']]??'badge-gray' ?>"><?= ucfirst($a['status']) ?></span></td>
          <td><?php if ($a['status']==='pending'): ?>
              <a href="?cancel=<?= $a['id'] ?>" class="btn btn-sm btn-danger" data-confirm="Cancel this appointment?">Cancel</a>
              <?php else: ?><span class="text-muted" style="font-size:0.82rem">—</span><?php endif; ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if (!$appointments): ?><tr><td colspan="8" class="text-center text-muted" style="padding:28px">No bookings yet. <a href="<?= SITE_URL ?>/parent/book-hospital.php" style="color:#4a7c00">Book now</a></td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php require dirname(__DIR__) . '/includes/dash-footer.php'; ?>
