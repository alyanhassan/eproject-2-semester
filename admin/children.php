<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/auth.php';
requireAdmin();

$db = getDB();
$search = trim($_GET['search'] ?? '');
$sql = 'SELECT c.*, u.name AS parent_name, u.email AS parent_email, u.phone AS parent_phone FROM children c JOIN users u ON c.parent_id = u.id';
$params = [];
if ($search) { $sql .= ' WHERE c.name LIKE ? OR u.name LIKE ?'; $params = ["%$search%", "%$search%"]; }
$sql .= ' ORDER BY c.created_at DESC';
$stmt = $db->prepare($sql); $stmt->execute($params);
$children = $stmt->fetchAll();

$dashRole  = 'admin'; $dashTitle = 'All Children Details'; $activeKey = 'children';
$dashNav = require_nav_items_admin();
function require_nav_items_admin(){return [
    ['label'=>'Dashboard','key'=>'dashboard','url'=>'/admin/index.php','icon'=>'<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>'],
    ['label'=>'All Children','key'=>'children','url'=>'/admin/children.php','icon'=>'<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>'],
    ['label'=>'Vaccination Dates','key'=>'vax_dates','url'=>'/admin/vaccination-dates.php','icon'=>'<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="3" y1="10" x2="21" y2="10"/></svg>'],
    ['label'=>'Reports','key'=>'reports','url'=>'/admin/reports.php','icon'=>'<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/></svg>'],
    ['label'=>'Vaccines','key'=>'vaccines','url'=>'/admin/vaccines.php','icon'=>'<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.5 20.5L3 13l7.5-7.5"/></svg>'],
    ['label'=>'Parent Requests','key'=>'requests','url'=>'/admin/requests.php','icon'=>'<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8h1a4 4 0 010 8h-1"/></svg>'],
    ['label'=>'Hospitals','key'=>'hospitals','url'=>'/admin/hospitals.php','icon'=>'<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>'],
    ['label'=>'Booking Details','key'=>'bookings','url'=>'/admin/bookings.php','icon'=>'<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/></svg>'],
];}
require dirname(__DIR__) . '/includes/dash-header.php';
?>
<div class="card" style="padding:0">
  <div class="card-header" style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px">
    <span>All Children (<?= count($children) ?>)</span>
    <form method="GET" style="display:flex;gap:8px">
      <input type="text" name="search" class="form-control" placeholder="Search child or parent..." value="<?= e($search) ?>" style="width:220px">
      <button type="submit" class="btn btn-primary btn-sm">Search</button>
      <?php if ($search): ?><a href="?" class="btn btn-secondary btn-sm">Clear</a><?php endif; ?>
    </form>
  </div>
  <div class="table-wrap">
    <table>
      <thead><tr><th>#</th><th>Child Name</th><th>DOB</th><th>Gender</th><th>Blood Group</th><th>Parent</th><th>Parent Contact</th><th>Registered</th></tr></thead>
      <tbody>
        <?php foreach ($children as $i => $c): ?>
        <tr>
          <td><?= $i+1 ?></td>
          <td><strong><?= e($c['name']) ?></strong></td>
          <td><?= date('d M Y', strtotime($c['dob'])) ?></td>
          <td><span class="badge <?= $c['gender']==='male'?'badge-blue':'badge-purple' ?>"><?= ucfirst($c['gender']) ?></span></td>
          <td><?= e($c['blood_group'] ?: '—') ?></td>
          <td><?= e($c['parent_name']) ?></td>
          <td><?= e($c['parent_phone'] ?: $c['parent_email']) ?></td>
          <td><?= date('d M Y', strtotime($c['created_at'])) ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if (!$children): ?><tr><td colspan="8" class="text-center text-muted" style="padding:24px">No children found.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php require dirname(__DIR__) . '/includes/dash-footer.php'; ?>
