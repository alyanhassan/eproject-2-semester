<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/auth.php';
requireParent();
$db  = getDB();
$uid = $_SESSION['user_id'];

$error = '';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $childId   = (int)$_POST['child_id'];
    $hospitalId= (int)$_POST['hospital_id'];
    $vaccineId = (int)$_POST['vaccine_id'];
    $date      = $_POST['appointment_date']??'';
    $time      = $_POST['appointment_time']??'';
    $notes     = trim($_POST['notes']??'');

    // Validate child belongs to parent
    $chk=$db->prepare('SELECT id FROM children WHERE id=? AND parent_id=?'); $chk->execute([$childId,$uid]);
    if (!$chk->fetch()) { $error='Invalid child selected.'; }
    elseif (!$date) { $error='Please select an appointment date.'; }
    elseif ($date < date('Y-m-d')) { $error='Appointment date cannot be in the past.'; }
    else {
        $db->prepare('INSERT INTO appointments (parent_id,child_id,hospital_id,vaccine_id,appointment_date,appointment_time,notes,status) VALUES (?,?,?,?,?,?,?,"pending")')
           ->execute([$uid,$childId,$hospitalId,$vaccineId,$date,$time?:null,$notes]);
        setFlash('success','Appointment request submitted! Waiting for admin approval.');
        redirect(SITE_URL.'/parent/requests.php');
    }
}

$children  = $db->prepare('SELECT * FROM children WHERE parent_id=? ORDER BY name'); $children->execute([$uid]); $children=$children->fetchAll();
$hospitals = $db->query('SELECT * FROM hospitals WHERE status="active" ORDER BY city,name')->fetchAll();
$vaccines  = $db->query('SELECT * FROM vaccines WHERE status="available" ORDER BY name')->fetchAll();

$cityFilter = $_GET['city']??'all';

$dashRole='parent'; $dashTitle='Book Hospital'; $activeKey='book';
$dashNav=[['label'=>'Dashboard','key'=>'dashboard','url'=>'/parent/index.php','icon'=>''],['label'=>'My Children','key'=>'children','url'=>'/parent/children.php','icon'=>''],['label'=>'Vaccination Dates','key'=>'vax_dates','url'=>'/parent/vaccination-dates.php','icon'=>''],['label'=>'Book Hospital','key'=>'book','url'=>'/parent/book-hospital.php','icon'=>''],['label'=>'My Requests','key'=>'requests','url'=>'/parent/requests.php','icon'=>''],['label'=>'Vaccination Report','key'=>'reports','url'=>'/parent/reports.php','icon'=>''],['label'=>'My Profile','key'=>'profile','url'=>'/parent/profile.php','icon'=>'']];
require dirname(__DIR__) . '/includes/dash-header.php';
?>
<?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>

<div class="grid-2" style="gap:24px">
  <!-- Booking Form -->
  <div class="card">
    <h3 style="margin-bottom:20px">Request Appointment</h3>
    <?php if (!$children): ?>
      <div class="alert alert-info">Please <a href="<?= SITE_URL ?>/parent/children.php" style="color:#2467E3">add a child</a> first before booking.</div>
    <?php else: ?>
    <form method="POST">
      <div class="form-group">
        <label class="form-label">Select Child *</label>
        <select name="child_id" class="form-control" required>
          <option value="">— Choose child —</option>
          <?php foreach ($children as $ch): ?>
          <option value="<?= $ch['id'] ?>"><?= e($ch['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Select Vaccine *</label>
        <select name="vaccine_id" class="form-control" required>
          <option value="">— Choose vaccine —</option>
          <?php foreach ($vaccines as $v): ?>
          <option value="<?= $v['id'] ?>"><?= e($v['name']) ?> (<?= e($v['type']) ?>, <?= $v['doses'] ?> dose<?= $v['doses']>1?'s':'' ?>)</option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Select Hospital *</label>
        <select name="hospital_id" class="form-control" required>
          <option value="">— Choose hospital —</option>
          <?php foreach ($hospitals as $h): ?>
          <option value="<?= $h['id'] ?>"><?= e($h['name']) ?> — <?= e($h['city']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Preferred Date *</label>
          <input type="date" name="appointment_date" class="form-control" min="<?= date('Y-m-d') ?>" required>
        </div>
        <div class="form-group">
          <label class="form-label">Preferred Time</label>
          <input type="time" name="appointment_time" class="form-control">
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Notes (optional)</label>
        <textarea name="notes" class="form-control" rows="3" placeholder="Any special requirements or notes..."></textarea>
      </div>
      <button type="submit" class="btn btn-primary">Submit Appointment Request</button>
    </form>
    <?php endif; ?>
  </div>

  <!-- Hospital Directory -->
  <div>
    <div class="filter-tabs">
      <?php $cities=['all'=>'All Cities','Karachi'=>'Karachi','Lahore'=>'Lahore','Islamabad'=>'Islamabad'];
      foreach ($cities as $k=>$label): ?>
      <a href="?city=<?= $k ?>" class="filter-tab <?= $cityFilter===$k?'active':'' ?>" style="text-decoration:none"><?= $label ?></a>
      <?php endforeach; ?>
    </div>
    <?php foreach ($hospitals as $h): ?>
    <?php if ($cityFilter!=='all' && $h['city']!==$cityFilter) continue; ?>
    <div class="card mb-16" data-city="<?= e($h['city']) ?>" data-hospital-name="<?= e($h['name']) ?>">
      <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:8px">
        <h4 style="font-size:0.98rem"><?= e($h['name']) ?></h4>
        <span class="badge badge-lime"><span class="stars">&#9733;</span> <?= $h['rating'] ?></span>
      </div>
      <div class="text-muted" style="font-size:0.82rem;margin-bottom:8px">
        <?= e($h['city']) ?><?= $h['address'] ? ' · ' . e($h['address']) : '' ?>
        <?= $h['hours'] ? ' · ' . e($h['hours']) : '' ?>
      </div>
      <?php if ($h['services']): ?>
        <div style="display:flex;gap:6px;flex-wrap:wrap">
          <?php foreach (explode(',', $h['services']) as $svc): ?>
          <span class="pill-tag"><?= e(trim($svc)) ?></span>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
    <?php endforeach; ?>
  </div>
</div>
<?php require dirname(__DIR__) . '/includes/dash-footer.php'; ?>
