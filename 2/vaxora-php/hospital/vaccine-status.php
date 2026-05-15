<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/auth.php';
requireHospital();
$db  = getDB();
$uid = $_SESSION['user_id'];

$hospital = $db->prepare('SELECT * FROM hospitals WHERE user_id=?');
$hospital->execute([$uid]); $hospital = $hospital->fetch();
if (!$hospital) { redirect(SITE_URL . '/hospital/index.php'); }
$hid = $hospital['id'];

// Update vaccination status
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $apptId    = (int)$_POST['appointment_id'];
    $status    = $_POST['vax_status'] ?? 'completed'; // completed or missed
    $notes     = trim($_POST['notes'] ?? '');
    $nextDue   = $_POST['next_due_date'] ?? null;
    $doseNum   = (int)($_POST['dose_number'] ?? 1);

    // Get appointment
    $appt = $db->prepare('SELECT * FROM appointments WHERE id=? AND hospital_id=?');
    $appt->execute([$apptId, $hid]); $appt = $appt->fetch();

    if ($appt) {
        // Update appointment status
        $db->prepare('UPDATE appointments SET status=? WHERE id=?')
           ->execute([$status==='completed'?'completed':'completed', $apptId]);

        // Upsert vaccination record
        $existing = $db->prepare('SELECT id FROM vaccination_records WHERE appointment_id=?');
        $existing->execute([$apptId]); $existing = $existing->fetchColumn();

        if ($existing) {
            $db->prepare('UPDATE vaccination_records SET status=?,date_given=?,next_due_date=?,dose_number=?,notes=? WHERE id=?')
               ->execute([$status, date('Y-m-d'), $nextDue?:null, $doseNum, $notes, $existing]);
        } else {
            $db->prepare('INSERT INTO vaccination_records (appointment_id,child_id,vaccine_id,hospital_id,date_given,next_due_date,dose_number,status,notes) VALUES (?,?,?,?,?,?,?,?,?)')
               ->execute([$apptId, $appt['child_id'], $appt['vaccine_id'], $hid, date('Y-m-d'), $nextDue?:null, $doseNum, $status, $notes]);
        }
        setFlash('success', 'Vaccination status updated successfully.');
    } else {
        setFlash('error', 'Appointment not found or access denied.');
    }
    redirect(SITE_URL . '/hospital/vaccine-status.php');
}

$highlightId = (int)($_GET['appt'] ?? 0);
$filter = $_GET['filter'] ?? 'upcoming';

$sql = 'SELECT a.*,c.name AS cname,c.dob,u.name AS pname,u.phone AS pphone,v.name AS vname,
               vr.status AS vax_status, vr.id AS record_id
        FROM appointments a
        JOIN children c ON a.child_id=c.id
        JOIN users u ON a.parent_id=u.id
        JOIN vaccines v ON a.vaccine_id=v.id
        LEFT JOIN vaccination_records vr ON vr.appointment_id=a.id
        WHERE a.hospital_id=?';

if ($filter === 'upcoming') {
    $sql .= ' AND a.appointment_date >= CURDATE() AND a.status="approved"';
} elseif ($filter === 'today') {
    $sql .= ' AND a.appointment_date = CURDATE() AND a.status="approved"';
} elseif ($filter === 'completed') {
    $sql .= ' AND a.status="completed"';
} else {
    $sql .= ' AND a.status IN("approved","completed")';
}
$sql .= ' ORDER BY a.appointment_date ASC';
$stmt = $db->prepare($sql); $stmt->execute([$hid]);
$appointments = $stmt->fetchAll();

$dashRole='hospital'; $dashTitle='Update Vaccine Status'; $activeKey='vaccine';
$dashNav=[
    ['label'=>'Dashboard','key'=>'dashboard','url'=>'/hospital/index.php','icon'=>'<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>'],
    ['label'=>'Appointments','key'=>'vaccine','url'=>'/hospital/vaccine-status.php','icon'=>'<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/><line x1="9" y1="12" x2="15" y2="12"/></svg>'],
];
require dirname(__DIR__) . '/includes/dash-header.php';
?>

<div class="filter-tabs" style="margin-bottom:24px">
  <?php foreach (['today'=>'Today','upcoming'=>'Upcoming','completed'=>'Completed','all'=>'All'] as $k=>$label): ?>
    <a href="?filter=<?= $k ?>" class="filter-tab <?= $filter===$k?'active':'' ?>" style="text-decoration:none"><?= $label ?></a>
  <?php endforeach; ?>
</div>

<?php foreach ($appointments as $a):
  $highlight = ($highlightId && $a['id'] === $highlightId);
  $dob=new DateTime($a['dob']); $now=new DateTime(); $age=$dob->diff($now);
  $ageStr=$age->y>0?$age->y.' yrs':$age->m.' mo';
  $isToday = $a['appointment_date'] === date('Y-m-d');
?>
<div class="card mb-16" style="<?= $highlight ? 'border:2px solid #90E300;' : '' ?>" id="appt-<?= $a['id'] ?>">
  <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:16px">
    <div>
      <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px;flex-wrap:wrap">
        <h3 style="font-size:1rem"><?= e($a['cname']) ?> <span class="text-muted" style="font-weight:400">(<?= $ageStr ?>)</span></h3>
        <?php if ($isToday): ?><span class="badge badge-lime">Today</span><?php endif; ?>
        <?php if ($a['vax_status']): ?>
          <span class="badge <?= $a['vax_status']==='completed'?'badge-lime':'badge-red' ?>"><?= ucfirst($a['vax_status']) ?></span>
        <?php else: ?>
          <span class="badge badge-yellow">Pending</span>
        <?php endif; ?>
      </div>
      <div class="text-muted" style="font-size:0.85rem">
        Vaccine: <strong><?= e($a['vname']) ?></strong> &nbsp;|&nbsp;
        Parent: <?= e($a['pname']) ?> <?= $a['pphone'] ? '(' . e($a['pphone']) . ')' : '' ?> &nbsp;|&nbsp;
        Date: <?= date('d M Y',strtotime($a['appointment_date'])) ?>
        <?php if ($a['appointment_time']): ?> at <?= date('h:i A',strtotime($a['appointment_time'])) ?><?php endif; ?>
      </div>
    </div>
    <?php if ($a['status'] !== 'completed'): ?>
    <button type="button" class="btn btn-primary btn-sm"
      onclick="document.getElementById('form-<?= $a['id'] ?>').style.display=document.getElementById('form-<?= $a['id'] ?>').style.display==='none'?'block':'none'">
      Update Status
    </button>
    <?php endif; ?>
  </div>

  <!-- Update Form -->
  <div id="form-<?= $a['id'] ?>" style="display:<?= $highlight?'block':'none' ?>;margin-top:20px;padding-top:20px;border-top:1px solid var(--border)">
    <form method="POST">
      <input type="hidden" name="appointment_id" value="<?= $a['id'] ?>">
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Vaccination Status *</label>
          <select name="vax_status" class="form-control" required>
            <option value="completed">Vaccinated (Completed)</option>
            <option value="missed">Not Vaccinated (Missed)</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Dose Number</label>
          <input type="number" name="dose_number" class="form-control" min="1" max="10" value="1">
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Next Due Date (if applicable)</label>
          <input type="date" name="next_due_date" class="form-control" min="<?= date('Y-m-d') ?>">
        </div>
        <div class="form-group">
          <label class="form-label">Notes</label>
          <input type="text" name="notes" class="form-control" placeholder="Any observations...">
        </div>
      </div>
      <button type="submit" class="btn btn-primary">Save Status</button>
      <button type="button" class="btn btn-secondary" onclick="document.getElementById('form-<?= $a['id'] ?>').style.display='none'">Cancel</button>
    </form>
  </div>
</div>
<?php endforeach; ?>

<?php if (!$appointments): ?>
<div class="card text-center" style="padding:48px">
  <div style="font-size:3rem;margin-bottom:12px">&#128197;</div>
  <h3>No Appointments</h3>
  <p class="text-muted mt-8">No appointments found for the selected filter.</p>
</div>
<?php endif; ?>
<?php require dirname(__DIR__) . '/includes/dash-footer.php'; ?>
