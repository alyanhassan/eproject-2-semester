<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/auth.php';
requireParent();
$db  = getDB();
$uid = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD']==='POST') {
    $action = $_POST['action']??'';
    if ($action==='add') {
        $db->prepare('INSERT INTO children (parent_id,name,dob,gender,blood_group,weight,height,notes) VALUES (?,?,?,?,?,?,?,?)')
           ->execute([$uid,trim($_POST['name']),$_POST['dob'],$_POST['gender'],trim($_POST['blood_group']),(float)$_POST['weight'],(float)$_POST['height'],trim($_POST['notes'])]);
        setFlash('success','Child added successfully.'); redirect(SITE_URL.'/parent/children.php');
    } elseif ($action==='update') {
        $db->prepare('UPDATE children SET name=?,dob=?,gender=?,blood_group=?,weight=?,height=?,notes=? WHERE id=? AND parent_id=?')
           ->execute([trim($_POST['name']),$_POST['dob'],$_POST['gender'],trim($_POST['blood_group']),(float)$_POST['weight'],(float)$_POST['height'],trim($_POST['notes']),(int)$_POST['id'],$uid]);
        setFlash('success','Child updated.'); redirect(SITE_URL.'/parent/children.php');
    }
}
if (isset($_GET['delete'])) {
    $db->prepare('DELETE FROM children WHERE id=? AND parent_id=?')->execute([(int)$_GET['delete'],$uid]);
    setFlash('success','Child removed.'); redirect(SITE_URL.'/parent/children.php');
}

$edit=null;
if (isset($_GET['edit'])) { $s=$db->prepare('SELECT * FROM children WHERE id=? AND parent_id=?'); $s->execute([(int)$_GET['edit'],$uid]); $edit=$s->fetch(); }
$children=$db->prepare('SELECT * FROM children WHERE parent_id=? ORDER BY name'); $children->execute([$uid]); $children=$children->fetchAll();

$dashRole='parent'; $dashTitle='My Children'; $activeKey='children';
$dashNav=[['label'=>'Dashboard','key'=>'dashboard','url'=>'/parent/index.php','icon'=>''],['label'=>'My Children','key'=>'children','url'=>'/parent/children.php','icon'=>''],['label'=>'Vaccination Dates','key'=>'vax_dates','url'=>'/parent/vaccination-dates.php','icon'=>''],['label'=>'Book Hospital','key'=>'book','url'=>'/parent/book-hospital.php','icon'=>''],['label'=>'My Requests','key'=>'requests','url'=>'/parent/requests.php','icon'=>''],['label'=>'Vaccination Report','key'=>'reports','url'=>'/parent/reports.php','icon'=>''],['label'=>'My Profile','key'=>'profile','url'=>'/parent/profile.php','icon'=>'']];
require dirname(__DIR__) . '/includes/dash-header.php';
?>
<div class="grid-2" style="gap:24px">
  <div class="card">
    <h3 style="margin-bottom:20px"><?= $edit?'Edit Child':'Add Child' ?></h3>
    <form method="POST">
      <input type="hidden" name="action" value="<?= $edit?'update':'add' ?>">
      <?php if ($edit): ?><input type="hidden" name="id" value="<?= $edit['id'] ?>"><?php endif; ?>
      <div class="form-group"><label class="form-label">Child's Full Name *</label><input type="text" name="name" class="form-control" value="<?= e($edit['name']??'') ?>" required></div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">Date of Birth *</label><input type="date" name="dob" class="form-control" value="<?= e($edit['dob']??'') ?>" required></div>
        <div class="form-group"><label class="form-label">Gender *</label>
          <select name="gender" class="form-control" required>
            <option value="">Select</option>
            <option value="male" <?= ($edit['gender']??'')==='male'?'selected':'' ?>>Male</option>
            <option value="female" <?= ($edit['gender']??'')==='female'?'selected':'' ?>>Female</option>
            <option value="other" <?= ($edit['gender']??'')==='other'?'selected':'' ?>>Other</option>
          </select>
        </div>
      </div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">Blood Group</label>
          <select name="blood_group" class="form-control">
            <option value="">—</option>
            <?php foreach (['A+','A-','B+','B-','O+','O-','AB+','AB-'] as $bg): ?>
            <option <?= ($edit['blood_group']??'')===$bg?'selected':'' ?>><?= $bg ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group"><label class="form-label">Weight (kg)</label><input type="number" step="0.1" name="weight" class="form-control" value="<?= e($edit['weight']??'') ?>"></div>
      </div>
      <div class="form-group"><label class="form-label">Height (cm)</label><input type="number" step="0.1" name="height" class="form-control" value="<?= e($edit['height']??'') ?>"></div>
      <div class="form-group"><label class="form-label">Notes</label><textarea name="notes" class="form-control" rows="2"><?= e($edit['notes']??'') ?></textarea></div>
      <div style="display:flex;gap:8px">
        <button type="submit" class="btn btn-primary"><?= $edit?'Update Child':'Add Child' ?></button>
        <?php if ($edit): ?><a href="?" class="btn btn-secondary">Cancel</a><?php endif; ?>
      </div>
    </form>
  </div>
  <div>
    <?php foreach ($children as $ch):
      $dob=new DateTime($ch['dob']); $now=new DateTime(); $age=$dob->diff($now);
      $ageStr=$age->y>0?$age->y.' yrs':$age->m.' months';
    ?>
    <div class="card mb-16">
      <div style="display:flex;justify-content:space-between;align-items:flex-start">
        <div>
          <h3 style="margin-bottom:4px"><?= e($ch['name']) ?></h3>
          <div class="text-muted" style="font-size:0.85rem">DOB: <?= date('d M Y',strtotime($ch['dob'])) ?> · Age: <?= $ageStr ?></div>
          <div style="margin-top:8px;display:flex;gap:8px;flex-wrap:wrap">
            <span class="pill-tag"><?= ucfirst($ch['gender']) ?></span>
            <?php if ($ch['blood_group']): ?><span class="pill-tag"><?= e($ch['blood_group']) ?></span><?php endif; ?>
            <?php if ($ch['weight']): ?><span class="pill-tag"><?= $ch['weight'] ?> kg</span><?php endif; ?>
          </div>
        </div>
        <div style="display:flex;gap:6px">
          <a href="?edit=<?= $ch['id'] ?>" class="btn btn-sm btn-outline-dark">Edit</a>
          <a href="?delete=<?= $ch['id'] ?>" class="btn btn-sm btn-danger" data-confirm="Remove this child?">Del</a>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
    <?php if (!$children): ?><div class="card"><p class="text-muted text-center">No children added yet.</p></div><?php endif; ?>
  </div>
</div>
<?php require dirname(__DIR__) . '/includes/dash-footer.php'; ?>
