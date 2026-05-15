<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/auth.php';
requireAdmin();
$db = getDB();

$dateFrom = $_GET['date_from'] ?? date('Y-m-01');
$dateTo   = $_GET['date_to']   ?? date('Y-m-d');
$type     = $_GET['type']      ?? 'all';

$sql = 'SELECT vr.*, c.name AS child_name, c.dob, c.gender, v.name AS vaccine_name, v.type AS vaccine_type,
               h.name AS hospital_name, h.city, u.name AS parent_name
        FROM vaccination_records vr
        JOIN children c ON vr.child_id = c.id
        JOIN vaccines v ON vr.vaccine_id = v.id
        LEFT JOIN hospitals h ON vr.hospital_id = h.id
        JOIN users u ON c.parent_id = u.id
        WHERE vr.date_given BETWEEN ? AND ?';
$params = [$dateFrom, $dateTo];
if ($type !== 'all') { $sql .= ' AND vr.status = ?'; $params[] = $type; }
$sql .= ' ORDER BY vr.date_given DESC';
$stmt = $db->prepare($sql); $stmt->execute($params);
$records = $stmt->fetchAll();

$totalCompleted = array_filter($records, fn($r) => $r['status']==='completed');
$dashRole='admin'; $dashTitle='Vaccination Reports'; $activeKey='reports';
$dashNav=[['label'=>'Dashboard','key'=>'dashboard','url'=>'/admin/index.php','icon'=>''],['label'=>'All Children','key'=>'children','url'=>'/admin/children.php','icon'=>''],['label'=>'Vaccination Dates','key'=>'vax_dates','url'=>'/admin/vaccination-dates.php','icon'=>''],['label'=>'Reports','key'=>'reports','url'=>'/admin/reports.php','icon'=>''],['label'=>'Vaccines','key'=>'vaccines','url'=>'/admin/vaccines.php','icon'=>''],['label'=>'Parent Requests','key'=>'requests','url'=>'/admin/requests.php','icon'=>''],['label'=>'Hospitals','key'=>'hospitals','url'=>'/admin/hospitals.php','icon'=>''],['label'=>'Booking Details','key'=>'bookings','url'=>'/admin/bookings.php','icon'=>'']];
require dirname(__DIR__) . '/includes/dash-header.php';
?>
<div class="card" style="margin-bottom:24px">
  <h3 style="margin-bottom:16px">Filter Report</h3>
  <form method="GET" style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end">
    <div class="form-group" style="margin:0">
      <label class="form-label">From Date</label>
      <input type="date" name="date_from" class="form-control" value="<?= e($dateFrom) ?>">
    </div>
    <div class="form-group" style="margin:0">
      <label class="form-label">To Date</label>
      <input type="date" name="date_to" class="form-control" value="<?= e($dateTo) ?>">
    </div>
    <div class="form-group" style="margin:0">
      <label class="form-label">Status</label>
      <select name="type" class="form-control">
        <option value="all">All</option>
        <option value="completed" <?= $type==='completed'?'selected':'' ?>>Completed</option>
        <option value="missed" <?= $type==='missed'?'selected':'' ?>>Missed</option>
        <option value="pending" <?= $type==='pending'?'selected':'' ?>>Pending</option>
      </select>
    </div>
    <button type="submit" class="btn btn-primary" style="align-self:flex-end">Generate Report</button>
  </form>
</div>

<div class="widget-grid" style="grid-template-columns:repeat(3,1fr);margin-bottom:24px">
  <div class="widget"><div class="widget-value"><?= count($records) ?></div><div class="widget-label">Total Records</div></div>
  <div class="widget"><div class="widget-value" style="color:#90E300"><?= count($totalCompleted) ?></div><div class="widget-label">Completed</div></div>
  <div class="widget"><div class="widget-value" style="color:#e53e3e"><?= count($records)-count($totalCompleted) ?></div><div class="widget-label">Pending / Missed</div></div>
</div>

<div class="card" style="padding:0">
  <div class="card-header">Vaccination Records — <?= date('d M Y', strtotime($dateFrom)) ?> to <?= date('d M Y', strtotime($dateTo)) ?> (<?= count($records) ?> records)</div>
  <div class="table-wrap">
    <table>
      <thead><tr><th>#</th><th>Child</th><th>Gender</th><th>Parent</th><th>Vaccine</th><th>Type</th><th>Dose</th><th>Hospital</th><th>Date Given</th><th>Next Due</th><th>Status</th></tr></thead>
      <tbody>
        <?php foreach ($records as $i => $r): ?>
        <tr>
          <td><?= $i+1 ?></td>
          <td><?= e($r['child_name']) ?></td>
          <td><?= ucfirst($r['gender']) ?></td>
          <td><?= e($r['parent_name']) ?></td>
          <td><?= e($r['vaccine_name']) ?></td>
          <td><span class="badge badge-blue"><?= e($r['vaccine_type']) ?></span></td>
          <td><?= $r['dose_number'] ?></td>
          <td><?= e($r['hospital_name'] ?: '—') ?></td>
          <td><?= $r['date_given'] ? date('d M Y', strtotime($r['date_given'])) : '—' ?></td>
          <td><?= $r['next_due_date'] ? date('d M Y', strtotime($r['next_due_date'])) : '—' ?></td>
          <td><?php $cl=['completed'=>'badge-lime','missed'=>'badge-red','pending'=>'badge-yellow']; ?>
              <span class="badge <?= $cl[$r['status']]??'badge-gray' ?>"><?= ucfirst($r['status']) ?></span></td>
        </tr>
        <?php endforeach; ?>
        <?php if (!$records): ?><tr><td colspan="11" class="text-center text-muted" style="padding:28px">No records for this period.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php require dirname(__DIR__) . '/includes/dash-footer.php'; ?>
