<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/auth.php';
requireAdmin();
$db = getDB();

$filter = $_GET['status'] ?? 'all';
$sql = 'SELECT a.*, u.name AS parent_name, u.phone AS parent_phone, c.name AS child_name,
               v.name AS vaccine_name, h.name AS hospital_name, h.city
        FROM appointments a
        JOIN users u ON a.parent_id = u.id
        JOIN children c ON a.child_id = c.id
        JOIN vaccines v ON a.vaccine_id = v.id
        JOIN hospitals h ON a.hospital_id = h.id';
if ($filter !== 'all') $sql .= ' WHERE a.status = ' . $db->quote($filter);
$sql .= ' ORDER BY a.appointment_date DESC';
$bookings = $db->query($sql)->fetchAll();

$counts = $db->query('SELECT status, COUNT(*) as cnt FROM appointments GROUP BY status')->fetchAll(PDO::FETCH_KEY_PAIR);

$dashRole='admin'; $dashTitle='Booking Details'; $activeKey='bookings';
$dashNav=[['label'=>'Dashboard','key'=>'dashboard','url'=>'/admin/index.php','icon'=>''],['label'=>'All Children','key'=>'children','url'=>'/admin/children.php','icon'=>''],['label'=>'Vaccination Dates','key'=>'vax_dates','url'=>'/admin/vaccination-dates.php','icon'=>''],['label'=>'Reports','key'=>'reports','url'=>'/admin/reports.php','icon'=>''],['label'=>'Vaccines','key'=>'vaccines','url'=>'/admin/vaccines.php','icon'=>''],['label'=>'Parent Requests','key'=>'requests','url'=>'/admin/requests.php','icon'=>''],['label'=>'Hospitals','key'=>'hospitals','url'=>'/admin/hospitals.php','icon'=>''],['label'=>'Booking Details','key'=>'bookings','url'=>'/admin/bookings.php','icon'=>'']];
require dirname(__DIR__) . '/includes/dash-header.php';
?>
<div class="widget-grid" style="margin-bottom:24px">
  <?php foreach (['pending'=>'orange','approved'=>'blue','completed'=>'lime','rejected'=>'red'] as $s=>$c): ?>
  <div class="widget">
    <div class="widget-value" style="<?= $c==='lime'?'color:#4a7c00':($c==='red'?'color:#e53e3e':'') ?>"><?= $counts[$s] ?? 0 ?></div>
    <div class="widget-label"><?= ucfirst($s) ?> Bookings</div>
  </div>
  <?php endforeach; ?>
</div>

<div class="filter-tabs" style="margin-bottom:20px">
  <?php foreach (['all','pending','approved','completed','rejected','cancelled'] as $s): ?>
    <a href="?status=<?= $s ?>" class="filter-tab <?= $filter===$s?'active':'' ?>" style="text-decoration:none"><?= ucfirst($s) ?></a>
  <?php endforeach; ?>
</div>

<div class="card" style="padding:0">
  <div class="card-header">All Bookings (<?= count($bookings) ?>)</div>
  <div class="table-wrap">
    <table>
      <thead><tr><th>#</th><th>Booked On</th><th>Parent</th><th>Child</th><th>Vaccine</th><th>Hospital</th><th>City</th><th>Appt Date</th><th>Time</th><th>Status</th><th>Notes</th></tr></thead>
      <tbody>
        <?php foreach ($bookings as $i => $b): ?>
        <tr>
          <td><?= $i+1 ?></td>
          <td><?= date('d M Y', strtotime($b['created_at'])) ?></td>
          <td><?= e($b['parent_name']) ?><br><small class="text-muted"><?= e($b['parent_phone']) ?></small></td>
          <td><?= e($b['child_name']) ?></td>
          <td><?= e($b['vaccine_name']) ?></td>
          <td><?= e($b['hospital_name']) ?></td>
          <td><?= e($b['city']) ?></td>
          <td><?= date('d M Y', strtotime($b['appointment_date'])) ?></td>
          <td><?= $b['appointment_time'] ? date('h:i A', strtotime($b['appointment_time'])) : '—' ?></td>
          <td><?php $cl=['pending'=>'badge-yellow','approved'=>'badge-blue','completed'=>'badge-lime','rejected'=>'badge-red','cancelled'=>'badge-gray']; ?>
              <span class="badge <?= $cl[$b['status']]??'badge-gray' ?>"><?= ucfirst($b['status']) ?></span></td>
          <td><?= e($b['notes'] ?: '—') ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if (!$bookings): ?><tr><td colspan="11" class="text-center text-muted" style="padding:28px">No bookings found.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php require dirname(__DIR__) . '/includes/dash-footer.php'; ?>
