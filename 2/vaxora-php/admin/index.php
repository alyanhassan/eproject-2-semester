<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/auth.php';
requireAdmin();

$db = getDB();

$totalChildren  = $db->query('SELECT COUNT(*) FROM children')->fetchColumn();
$totalHospitals = $db->query('SELECT COUNT(*) FROM hospitals WHERE status="active"')->fetchColumn();
$totalVaccines  = $db->query('SELECT COUNT(*) FROM vaccines WHERE status="available"')->fetchColumn();
$pendingRequests= $db->query('SELECT COUNT(*) FROM appointments WHERE status="pending"')->fetchColumn();
$completedVax   = $db->query('SELECT COUNT(*) FROM vaccination_records WHERE status="completed"')->fetchColumn();
$totalParents   = $db->query('SELECT COUNT(*) FROM users WHERE role="parent"')->fetchColumn();

$recentAppointments = $db->query('
    SELECT a.*, u.name AS parent_name, c.name AS child_name, v.name AS vaccine_name, h.name AS hospital_name
    FROM appointments a
    JOIN users u ON a.parent_id = u.id
    JOIN children c ON a.child_id = c.id
    JOIN vaccines v ON a.vaccine_id = v.id
    JOIN hospitals h ON a.hospital_id = h.id
    ORDER BY a.created_at DESC LIMIT 8
')->fetchAll();

$dashRole  = 'admin';
$dashTitle = 'Admin Dashboard';
$activeKey = 'dashboard';
$dashNav   = [
    ['label'=>'Dashboard',          'key'=>'dashboard',   'url'=>'/admin/index.php',            'icon'=>'<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>'],
    ['label'=>'All Children',       'key'=>'children',    'url'=>'/admin/children.php',         'icon'=>'<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>'],
    ['label'=>'Vaccination Dates',  'key'=>'vax_dates',   'url'=>'/admin/vaccination-dates.php','icon'=>'<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>'],
    ['label'=>'Reports',            'key'=>'reports',     'url'=>'/admin/reports.php',          'icon'=>'<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>'],
    ['label'=>'Vaccines',           'key'=>'vaccines',    'url'=>'/admin/vaccines.php',         'icon'=>'<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.5 20.5L3 13l7.5-7.5m3 15L21 13l-7.5-7.5"/></svg>'],
    ['label'=>'Parent Requests',    'key'=>'requests',    'url'=>'/admin/requests.php',         'icon'=>'<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8h1a4 4 0 010 8h-1"/><path d="M2 8h16v9a4 4 0 01-4 4H6a4 4 0 01-4-4V8z"/><line x1="6" y1="1" x2="6" y2="4"/><line x1="10" y1="1" x2="10" y2="4"/><line x1="14" y1="1" x2="14" y2="4"/></svg>'],
    ['label'=>'Hospitals',          'key'=>'hospitals',   'url'=>'/admin/hospitals.php',        'icon'=>'<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>'],
    ['label'=>'Booking Details',    'key'=>'bookings',    'url'=>'/admin/bookings.php',         'icon'=>'<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/><line x1="9" y1="12" x2="15" y2="12"/><line x1="9" y1="16" x2="12" y2="16"/></svg>'],
];
require dirname(__DIR__) . '/includes/dash-header.php';
?>

<div class="widget-grid">
  <div class="widget">
    <div class="widget-icon lime">&#128100;</div>
    <div class="widget-value"><?= $totalChildren ?></div>
    <div class="widget-label">Registered Children</div>
  </div>
  <div class="widget">
    <div class="widget-icon blue">&#127968;</div>
    <div class="widget-value"><?= $totalHospitals ?></div>
    <div class="widget-label">Active Hospitals</div>
  </div>
  <div class="widget">
    <div class="widget-icon orange">&#9889;</div>
    <div class="widget-value"><?= $pendingRequests ?></div>
    <div class="widget-label">Pending Requests</div>
  </div>
  <div class="widget">
    <div class="widget-icon lime">&#10003;</div>
    <div class="widget-value"><?= $completedVax ?></div>
    <div class="widget-label">Vaccinations Done</div>
  </div>
</div>

<div class="grid-2" style="gap:24px;margin-bottom:24px">
  <div class="card" style="padding:0">
    <div class="card-header" style="display:flex;justify-content:space-between;align-items:center">
      Recent Appointments
      <a href="<?= SITE_URL ?>/admin/bookings.php" class="btn btn-sm btn-outline-dark">View All</a>
    </div>
    <div class="table-wrap">
      <table>
        <thead><tr><th>Child</th><th>Vaccine</th><th>Hospital</th><th>Date</th><th>Status</th></tr></thead>
        <tbody>
          <?php foreach ($recentAppointments as $appt): ?>
          <tr>
            <td><?= e($appt['child_name']) ?><br><small class="text-muted"><?= e($appt['parent_name']) ?></small></td>
            <td><?= e($appt['vaccine_name']) ?></td>
            <td><?= e($appt['hospital_name']) ?></td>
            <td><?= date('d M Y', strtotime($appt['appointment_date'])) ?></td>
            <td>
              <?php
              $colors = ['pending'=>'badge-yellow','approved'=>'badge-blue','completed'=>'badge-lime','rejected'=>'badge-red','cancelled'=>'badge-gray'];
              ?>
              <span class="badge <?= $colors[$appt['status']] ?? 'badge-gray' ?>"><?= ucfirst($appt['status']) ?></span>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php if (!$recentAppointments): ?>
          <tr><td colspan="5" class="text-center text-muted" style="padding:20px">No appointments yet</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <div>
    <div class="card mb-16">
      <h3 style="font-size:1rem;margin-bottom:16px">Quick Stats</h3>
      <div style="display:flex;flex-direction:column;gap:12px">
        <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--border)">
          <span class="text-muted">Total Parents</span><strong><?= $totalParents ?></strong>
        </div>
        <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--border)">
          <span class="text-muted">Available Vaccines</span><strong><?= $totalVaccines ?></strong>
        </div>
        <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--border)">
          <span class="text-muted">Active Hospitals</span><strong><?= $totalHospitals ?></strong>
        </div>
        <div style="display:flex;justify-content:space-between;padding:10px 0">
          <span class="text-muted">Completed Vaccinations</span><strong><?= $completedVax ?></strong>
        </div>
      </div>
    </div>
    <div class="card">
      <h3 style="font-size:1rem;margin-bottom:16px">Quick Actions</h3>
      <div style="display:flex;flex-direction:column;gap:10px">
        <a href="<?= SITE_URL ?>/admin/hospitals.php?action=add" class="btn btn-primary btn-sm">+ Add Hospital</a>
        <a href="<?= SITE_URL ?>/admin/vaccines.php" class="btn btn-outline-dark btn-sm">Manage Vaccines</a>
        <a href="<?= SITE_URL ?>/admin/requests.php" class="btn btn-outline-dark btn-sm">Review Requests <span class="badge badge-orange" style="margin-left:4px"><?= $pendingRequests ?></span></a>
      </div>
    </div>
  </div>
</div>

<?php require dirname(__DIR__) . '/includes/dash-footer.php'; ?>
