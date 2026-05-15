<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/auth.php';
requireParent();
$db=$getDB=getDB(); $uid=$_SESSION['user_id'];

$childFilter=(int)($_GET['child_id']??0);
$children=$db->prepare('SELECT * FROM children WHERE parent_id=? ORDER BY name'); $children->execute([$uid]); $children=$children->fetchAll();

$sql='SELECT vr.*,c.name AS cname,v.name AS vname,v.type AS vtype,h.name AS hname FROM vaccination_records vr JOIN children c ON vr.child_id=c.id JOIN vaccines v ON vr.vaccine_id=v.id LEFT JOIN hospitals h ON vr.hospital_id=h.id WHERE c.parent_id=?';
$params=[$uid];
if ($childFilter) { $sql.=' AND vr.child_id=?'; $params[]=$childFilter; }
$sql.=' ORDER BY vr.date_given DESC';
$stmt=$db->prepare($sql); $stmt->execute($params); $records=$stmt->fetchAll();

$dashRole='parent'; $dashTitle='Vaccination Report'; $activeKey='reports';
$dashNav=[['label'=>'Dashboard','key'=>'dashboard','url'=>'/parent/index.php','icon'=>''],['label'=>'My Children','key'=>'children','url'=>'/parent/children.php','icon'=>''],['label'=>'Vaccination Dates','key'=>'vax_dates','url'=>'/parent/vaccination-dates.php','icon'=>''],['label'=>'Book Hospital','key'=>'book','url'=>'/parent/book-hospital.php','icon'=>''],['label'=>'My Requests','key'=>'requests','url'=>'/parent/requests.php','icon'=>''],['label'=>'Vaccination Report','key'=>'reports','url'=>'/parent/reports.php','icon'=>''],['label'=>'My Profile','key'=>'profile','url'=>'/parent/profile.php','icon'=>'']];
require dirname(__DIR__) . '/includes/dash-header.php';
?>
<div class="card" style="margin-bottom:24px;padding:20px 24px">
  <form method="GET" style="display:flex;gap:12px;align-items:flex-end">
    <div class="form-group" style="margin:0;flex:1">
      <label class="form-label">Filter by Child</label>
      <select name="child_id" class="form-control">
        <option value="0">All Children</option>
        <?php foreach ($children as $ch): ?>
        <option value="<?= $ch['id'] ?>" <?= $childFilter===$ch['id']?'selected':'' ?>><?= e($ch['name']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <button type="submit" class="btn btn-primary" style="align-self:flex-end">Filter</button>
  </form>
</div>

<?php if (!$records): ?>
<div class="card text-center" style="padding:48px">
  <div style="font-size:3rem;margin-bottom:12px">&#128196;</div>
  <h3>No Vaccination Records</h3>
  <p class="text-muted mt-8">No vaccination history found for the selected child.</p>
</div>
<?php else: ?>
<div class="card" style="padding:0">
  <div class="card-header">Vaccination History (<?= count($records) ?> records)</div>
  <div class="table-wrap">
    <table>
      <thead><tr><th>Child</th><th>Vaccine</th><th>Type</th><th>Dose</th><th>Hospital</th><th>Date Given</th><th>Next Due</th><th>Status</th></tr></thead>
      <tbody>
        <?php foreach ($records as $r): ?>
        <tr>
          <td><?= e($r['cname']) ?></td>
          <td><strong><?= e($r['vname']) ?></strong></td>
          <td><span class="badge badge-blue"><?= e($r['vtype']) ?></span></td>
          <td><?= $r['dose_number'] ?></td>
          <td><?= e($r['hname']?:'—') ?></td>
          <td><?= $r['date_given']?date('d M Y',strtotime($r['date_given'])):'—' ?></td>
          <td><?= $r['next_due_date']?date('d M Y',strtotime($r['next_due_date'])):'—' ?></td>
          <td><?php $cl=['completed'=>'badge-lime','missed'=>'badge-red','pending'=>'badge-yellow']; ?>
              <span class="badge <?= $cl[$r['status']]??'badge-gray' ?>"><?= ucfirst($r['status']) ?></span></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php endif; ?>
<?php require dirname(__DIR__) . '/includes/dash-footer.php'; ?>
