<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/auth.php';
requireAdmin();
$db = getDB();

// Add hospital
if ($_SERVER['REQUEST_METHOD']==='POST' && ($_POST['action']??'')==='add') {
    $db->prepare('INSERT INTO hospitals (name,address,city,phone,email,rating,services,hours,status) VALUES (?,?,?,?,?,?,?,?,?)')
       ->execute([trim($_POST['name']),trim($_POST['address']),trim($_POST['city']),trim($_POST['phone']),trim($_POST['email']),(float)$_POST['rating'],trim($_POST['services']),trim($_POST['hours']),'active']);
    setFlash('success','Hospital added successfully.'); redirect(SITE_URL.'/admin/hospitals.php');
}
// Update
if ($_SERVER['REQUEST_METHOD']==='POST' && ($_POST['action']??'')==='update') {
    $db->prepare('UPDATE hospitals SET name=?,address=?,city=?,phone=?,email=?,rating=?,services=?,hours=?,status=? WHERE id=?')
       ->execute([trim($_POST['name']),trim($_POST['address']),trim($_POST['city']),trim($_POST['phone']),trim($_POST['email']),(float)$_POST['rating'],trim($_POST['services']),trim($_POST['hours']),$_POST['status'],(int)$_POST['id']]);
    setFlash('success','Hospital updated.'); redirect(SITE_URL.'/admin/hospitals.php');
}
// Delete
if (isset($_GET['delete'])) {
    $db->prepare('DELETE FROM hospitals WHERE id=?')->execute([(int)$_GET['delete']]);
    setFlash('success','Hospital deleted.'); redirect(SITE_URL.'/admin/hospitals.php');
}
// Toggle status
if (isset($_GET['toggle'])) {
    $h=$db->prepare('SELECT status FROM hospitals WHERE id=?'); $h->execute([(int)$_GET['toggle']]); $cur=$h->fetchColumn();
    $db->prepare('UPDATE hospitals SET status=? WHERE id=?')->execute([$cur==='active'?'inactive':'active',(int)$_GET['toggle']]);
    redirect(SITE_URL.'/admin/hospitals.php');
}

$editHospital = null;
if (isset($_GET['edit'])) { $s=$db->prepare('SELECT * FROM hospitals WHERE id=?'); $s->execute([(int)$_GET['edit']]); $editHospital=$s->fetch(); }

$hospitals = $db->query('SELECT * FROM hospitals ORDER BY city,name')->fetchAll();
$dashRole='admin';$dashTitle='Manage Hospitals';$activeKey='hospitals';
$dashNav=[
    ['label'=>'Dashboard','key'=>'dashboard','url'=>'/admin/index.php','icon'=>'<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>'],
    ['label'=>'All Children','key'=>'children','url'=>'/admin/children.php','icon'=>'<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>'],
    ['label'=>'Vaccination Dates','key'=>'vax_dates','url'=>'/admin/vaccination-dates.php','icon'=>'<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="3" y1="10" x2="21" y2="10"/></svg>'],
    ['label'=>'Reports','key'=>'reports','url'=>'/admin/reports.php','icon'=>'<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16h16V8z"/></svg>'],
    ['label'=>'Vaccines','key'=>'vaccines','url'=>'/admin/vaccines.php','icon'=>'<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/></svg>'],
    ['label'=>'Parent Requests','key'=>'requests','url'=>'/admin/requests.php','icon'=>'<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8h1a4 4 0 010 8h-1"/></svg>'],
    ['label'=>'Hospitals','key'=>'hospitals','url'=>'/admin/hospitals.php','icon'=>'<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>'],
    ['label'=>'Booking Details','key'=>'bookings','url'=>'/admin/bookings.php','icon'=>'<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10"/></svg>'],
];
require dirname(__DIR__).'/includes/dash-header.php';
?>
<div class="grid-2" style="gap:24px">
  <!-- Form -->
  <div class="card">
    <h3 style="margin-bottom:20px"><?= $editHospital ? 'Edit Hospital' : 'Add New Hospital' ?></h3>
    <form method="POST">
      <input type="hidden" name="action" value="<?= $editHospital?'update':'add' ?>">
      <?php if ($editHospital): ?><input type="hidden" name="id" value="<?= $editHospital['id'] ?>"><?php endif; ?>
      <div class="form-group"><label class="form-label">Hospital Name *</label><input type="text" name="name" class="form-control" value="<?= e($editHospital['name']??'') ?>" required></div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">City *</label><input type="text" name="city" class="form-control" value="<?= e($editHospital['city']??'') ?>" required></div>
        <div class="form-group"><label class="form-label">Phone</label><input type="text" name="phone" class="form-control" value="<?= e($editHospital['phone']??'') ?>"></div>
      </div>
      <div class="form-group"><label class="form-label">Address</label><input type="text" name="address" class="form-control" value="<?= e($editHospital['address']??'') ?>"></div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="<?= e($editHospital['email']??'') ?>"></div>
        <div class="form-group"><label class="form-label">Rating (0-5)</label><input type="number" name="rating" class="form-control" step="0.1" min="0" max="5" value="<?= e($editHospital['rating']??'4.5') ?>"></div>
      </div>
      <div class="form-group"><label class="form-label">Services</label><input type="text" name="services" class="form-control" value="<?= e($editHospital['services']??'') ?>" placeholder="e.g. All vaccines, Pediatric, Adult"></div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">Hours</label><input type="text" name="hours" class="form-control" value="<?= e($editHospital['hours']??'') ?>" placeholder="Mon-Sat 9am-5pm"></div>
        <?php if ($editHospital): ?>
        <div class="form-group"><label class="form-label">Status</label>
          <select name="status" class="form-control">
            <option value="active" <?= ($editHospital['status']??'')==='active'?'selected':'' ?>>Active</option>
            <option value="inactive" <?= ($editHospital['status']??'')==='inactive'?'selected':'' ?>>Inactive</option>
          </select>
        </div>
        <?php endif; ?>
      </div>
      <div style="display:flex;gap:8px">
        <button type="submit" class="btn btn-primary"><?= $editHospital?'Update Hospital':'Add Hospital' ?></button>
        <?php if ($editHospital): ?><a href="?" class="btn btn-secondary">Cancel</a><?php endif; ?>
      </div>
    </form>
  </div>

  <!-- List -->
  <div class="card" style="padding:0">
    <div class="card-header">Hospital List (<?= count($hospitals) ?>)</div>
    <div class="table-wrap">
      <table>
        <thead><tr><th>Name</th><th>City</th><th>Rating</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
          <?php foreach ($hospitals as $h): ?>
          <tr>
            <td><strong><?= e($h['name']) ?></strong><br><small class="text-muted"><?= e($h['phone']) ?></small></td>
            <td><?= e($h['city']) ?></td>
            <td><span class="stars">&#9733;</span> <?= e($h['rating']) ?></td>
            <td><span class="badge <?= $h['status']==='active'?'badge-lime':'badge-gray' ?>"><?= ucfirst($h['status']) ?></span></td>
            <td style="white-space:nowrap">
              <a href="?edit=<?= $h['id'] ?>" class="btn btn-sm btn-outline-dark">Edit</a>
              <a href="?toggle=<?= $h['id'] ?>" class="btn btn-sm btn-secondary"><?= $h['status']==='active'?'Disable':'Enable' ?></a>
              <a href="?delete=<?= $h['id'] ?>" class="btn btn-sm btn-danger" data-confirm="Delete this hospital?">Del</a>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php require dirname(__DIR__).'/includes/dash-footer.php'; ?>
