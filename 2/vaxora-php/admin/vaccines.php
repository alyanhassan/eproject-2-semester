<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/auth.php';
requireAdmin();
$db = getDB();

if ($_SERVER['REQUEST_METHOD']==='POST') {
    $action = $_POST['action']??'';
    if ($action==='add') {
        $db->prepare('INSERT INTO vaccines (name,type,doses,age_group,description,side_effects,duration,status) VALUES (?,?,?,?,?,?,?,?)')
           ->execute([trim($_POST['name']),trim($_POST['type']),(int)$_POST['doses'],trim($_POST['age_group']),trim($_POST['description']),trim($_POST['side_effects']),trim($_POST['duration']),$_POST['status']]);
        setFlash('success','Vaccine added.'); redirect(SITE_URL.'/admin/vaccines.php');
    } elseif ($action==='update') {
        $db->prepare('UPDATE vaccines SET name=?,type=?,doses=?,age_group=?,description=?,side_effects=?,duration=?,status=? WHERE id=?')
           ->execute([trim($_POST['name']),trim($_POST['type']),(int)$_POST['doses'],trim($_POST['age_group']),trim($_POST['description']),trim($_POST['side_effects']),trim($_POST['duration']),$_POST['status'],(int)$_POST['id']]);
        setFlash('success','Vaccine updated.'); redirect(SITE_URL.'/admin/vaccines.php');
    }
}
if (isset($_GET['delete'])) { $db->prepare('DELETE FROM vaccines WHERE id=?')->execute([(int)$_GET['delete']]); setFlash('success','Vaccine deleted.'); redirect(SITE_URL.'/admin/vaccines.php'); }
if (isset($_GET['toggle'])) { $s=$db->prepare('SELECT status FROM vaccines WHERE id=?'); $s->execute([(int)$_GET['toggle']]); $cur=$s->fetchColumn(); $db->prepare('UPDATE vaccines SET status=? WHERE id=?')->execute([$cur==='available'?'unavailable':'available',(int)$_GET['toggle']]); redirect(SITE_URL.'/admin/vaccines.php'); }

$edit=null;
if (isset($_GET['edit'])) { $s=$db->prepare('SELECT * FROM vaccines WHERE id=?'); $s->execute([(int)$_GET['edit']]); $edit=$s->fetch(); }
$vaccines=$db->query('SELECT * FROM vaccines ORDER BY name')->fetchAll();
$dashRole='admin';$dashTitle='Manage Vaccines';$activeKey='vaccines';
$dashNav=[['label'=>'Dashboard','key'=>'dashboard','url'=>'/admin/index.php','icon'=>''],['label'=>'All Children','key'=>'children','url'=>'/admin/children.php','icon'=>''],['label'=>'Vaccination Dates','key'=>'vax_dates','url'=>'/admin/vaccination-dates.php','icon'=>''],['label'=>'Reports','key'=>'reports','url'=>'/admin/reports.php','icon'=>''],['label'=>'Vaccines','key'=>'vaccines','url'=>'/admin/vaccines.php','icon'=>''],['label'=>'Parent Requests','key'=>'requests','url'=>'/admin/requests.php','icon'=>''],['label'=>'Hospitals','key'=>'hospitals','url'=>'/admin/hospitals.php','icon'=>''],['label'=>'Booking Details','key'=>'bookings','url'=>'/admin/bookings.php','icon'=>'']];
require dirname(__DIR__).'/includes/dash-header.php';
?>
<div class="grid-2" style="gap:24px">
  <div class="card">
    <h3 style="margin-bottom:20px"><?= $edit?'Edit Vaccine':'Add Vaccine' ?></h3>
    <form method="POST">
      <input type="hidden" name="action" value="<?= $edit?'update':'add' ?>">
      <?php if ($edit): ?><input type="hidden" name="id" value="<?= $edit['id'] ?>"><?php endif; ?>
      <div class="form-group"><label class="form-label">Vaccine Name *</label><input type="text" name="name" class="form-control" value="<?= e($edit['name']??'') ?>" required></div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">Type</label><input type="text" name="type" class="form-control" value="<?= e($edit['type']??'') ?>" placeholder="mRNA, Live-attenuated..."></div>
        <div class="form-group"><label class="form-label">Doses</label><input type="number" name="doses" class="form-control" min="1" value="<?= e($edit['doses']??1) ?>"></div>
      </div>
      <div class="form-group"><label class="form-label">Age Group</label><input type="text" name="age_group" class="form-control" value="<?= e($edit['age_group']??'') ?>" placeholder="e.g. 12 months+"></div>
      <div class="form-group"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="3"><?= e($edit['description']??'') ?></textarea></div>
      <div class="form-group"><label class="form-label">Side Effects</label><input type="text" name="side_effects" class="form-control" value="<?= e($edit['side_effects']??'') ?>"></div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">Duration</label><input type="text" name="duration" class="form-control" value="<?= e($edit['duration']??'') ?>"></div>
        <div class="form-group"><label class="form-label">Status</label>
          <select name="status" class="form-control">
            <option value="available" <?= ($edit['status']??'')==='available'?'selected':'' ?>>Available</option>
            <option value="unavailable" <?= ($edit['status']??'')==='unavailable'?'selected':'' ?>>Unavailable</option>
          </select>
        </div>
      </div>
      <div style="display:flex;gap:8px">
        <button type="submit" class="btn btn-primary"><?= $edit?'Update':'Add Vaccine' ?></button>
        <?php if ($edit): ?><a href="?" class="btn btn-secondary">Cancel</a><?php endif; ?>
      </div>
    </form>
  </div>
  <div class="card" style="padding:0">
    <div class="card-header">Vaccine List (<?= count($vaccines) ?>)</div>
    <div class="table-wrap">
      <table>
        <thead><tr><th>Name</th><th>Type</th><th>Doses</th><th>Age</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
          <?php foreach ($vaccines as $v): ?>
          <tr>
            <td><strong><?= e($v['name']) ?></strong></td>
            <td><?= e($v['type']) ?></td>
            <td><?= $v['doses'] ?></td>
            <td><?= e($v['age_group']) ?></td>
            <td><span class="badge <?= $v['status']==='available'?'badge-lime':'badge-red' ?>"><?= ucfirst($v['status']) ?></span></td>
            <td style="white-space:nowrap">
              <a href="?edit=<?= $v['id'] ?>" class="btn btn-sm btn-outline-dark">Edit</a>
              <a href="?toggle=<?= $v['id'] ?>" class="btn btn-sm btn-secondary"><?= $v['status']==='available'?'Disable':'Enable' ?></a>
              <a href="?delete=<?= $v['id'] ?>" class="btn btn-sm btn-danger" data-confirm="Delete this vaccine?">Del</a>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php require dirname(__DIR__).'/includes/dash-footer.php'; ?>
