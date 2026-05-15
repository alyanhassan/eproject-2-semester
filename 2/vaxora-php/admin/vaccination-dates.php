<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/auth.php';
requireAdmin();
$db = getDB();

$upcoming = $db->query('
    SELECT a.*, c.name AS child_name, c.dob, u.name AS parent_name, u.phone AS parent_phone,
           v.name AS vaccine_name, h.name AS hospital_name
    FROM appointments a
    JOIN children c ON a.child_id = c.id
    JOIN users u ON a.parent_id = u.id
    JOIN vaccines v ON a.vaccine_id = v.id
    JOIN hospitals h ON a.hospital_id = h.id
    WHERE a.appointment_date >= CURDATE() AND a.status IN ("pending","approved")
    ORDER BY a.appointment_date ASC
')->fetchAll();

$dashRole='admin'; $dashTitle='Upcoming Vaccination Dates'; $activeKey='vax_dates';
$dashNav=[['label'=>'Dashboard','key'=>'dashboard','url'=>'/admin/index.php','icon'=>''],['label'=>'All Children','key'=>'children','url'=>'/admin/children.php','icon'=>''],['label'=>'Vaccination Dates','key'=>'vax_dates','url'=>'/admin/vaccination-dates.php','icon'=>''],['label'=>'Reports','key'=>'reports','url'=>'/admin/reports.php','icon'=>''],['label'=>'Vaccines','key'=>'vaccines','url'=>'/admin/vaccines.php','icon'=>''],['label'=>'Parent Requests','key'=>'requests','url'=>'/admin/requests.php','icon'=>''],['label'=>'Hospitals','key'=>'hospitals','url'=>'/admin/hospitals.php','icon'=>''],['label'=>'Booking Details','key'=>'bookings','url'=>'/admin/bookings.php','icon'=>'']];
require dirname(__DIR__) . '/includes/dash-header.php';
?>
<div class="card" style="padding:0">
  <div class="card-header" style="display:flex;justify-content:space-between;align-items:center">
    <span>Upcoming Vaccination Dates (<?= count($upcoming) ?>)</span>
    <span class="badge badge-blue">From today onwards</span>
  </div>
  <div class="table-wrap">
    <table>
      <thead><tr><th>Date & Time</th><th>Child</th><th>Age</th><th>Parent</th><th>Contact</th><th>Vaccine</th><th>Hospital</th><th>Status</th></tr></thead>
      <tbody>
        <?php foreach ($upcoming as $a):
          $dob = new DateTime($a['dob']);
          $now = new DateTime();
          $age = $dob->diff($now);
          $ageStr = $age->y > 0 ? $age->y.' yr' : $age->m.' mo';
        ?>
        <tr>
          <td><strong><?= date('d M Y', strtotime($a['appointment_date'])) ?></strong><br>
              <?php if ($a['appointment_time']): ?><small><?= date('h:i A', strtotime($a['appointment_time'])) ?></small><?php endif; ?></td>
          <td><?= e($a['child_name']) ?></td>
          <td><?= $ageStr ?></td>
          <td><?= e($a['parent_name']) ?></td>
          <td><?= e($a['parent_phone'] ?: '—') ?></td>
          <td><?= e($a['vaccine_name']) ?></td>
          <td><?= e($a['hospital_name']) ?></td>
          <td>
            <?php $cl=['pending'=>'badge-yellow','approved'=>'badge-blue']; ?>
            <span class="badge <?= $cl[$a['status']]??'badge-gray' ?>"><?= ucfirst($a['status']) ?></span>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (!$upcoming): ?><tr><td colspan="8" class="text-center text-muted" style="padding:28px">No upcoming appointments found.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php require dirname(__DIR__) . '/includes/dash-footer.php'; ?>
