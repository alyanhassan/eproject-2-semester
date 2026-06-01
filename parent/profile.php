<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/auth.php';
requireParent();
$db=$getDB=getDB(); $uid=$_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD']==='POST') {
    $action=$_POST['action']??'';
    if ($action==='profile') {
        $db->prepare('UPDATE users SET name=?,phone=?,city=?,address=? WHERE id=?')
           ->execute([trim($_POST['name']),trim($_POST['phone']),trim($_POST['city']),trim($_POST['address']),$uid]);
        // Refresh session
        $u=$db->prepare('SELECT * FROM users WHERE id=?'); $u->execute([$uid]); $_SESSION['user']=$u->fetch();
        setFlash('success','Profile updated.'); redirect(SITE_URL.'/parent/profile.php');
    } elseif ($action==='password') {
        $u=$db->prepare('SELECT password FROM users WHERE id=?'); $u->execute([$uid]); $user=$u->fetch();
        if (!password_verify($_POST['current_password'],$user['password'])) {
            setFlash('error','Current password is incorrect.');
        } elseif ($_POST['new_password']!==$_POST['confirm_password']) {
            setFlash('error','New passwords do not match.');
        } elseif (strlen($_POST['new_password'])<6) {
            setFlash('error','Password must be at least 6 characters.');
        } else {
            $db->prepare('UPDATE users SET password=? WHERE id=?')->execute([password_hash($_POST['new_password'],PASSWORD_DEFAULT),$uid]);
            setFlash('success','Password changed successfully.');
        }
        redirect(SITE_URL.'/parent/profile.php');
    }
}
$user=$_SESSION['user'];
$dashRole='parent'; $dashTitle='My Profile'; $activeKey='profile';
$dashNav=[['label'=>'Dashboard','key'=>'dashboard','url'=>'/parent/index.php','icon'=>''],['label'=>'My Children','key'=>'children','url'=>'/parent/children.php','icon'=>''],['label'=>'Vaccination Dates','key'=>'vax_dates','url'=>'/parent/vaccination-dates.php','icon'=>''],['label'=>'Book Hospital','key'=>'book','url'=>'/parent/book-hospital.php','icon'=>''],['label'=>'My Requests','key'=>'requests','url'=>'/parent/requests.php','icon'=>''],['label'=>'Vaccination Report','key'=>'reports','url'=>'/parent/reports.php','icon'=>''],['label'=>'My Profile','key'=>'profile','url'=>'/parent/profile.php','icon'=>'']];
require dirname(__DIR__) . '/includes/dash-header.php';
?>
<div class="grid-2" style="gap:24px">
  <div class="card">
    <h3 style="margin-bottom:20px">Personal Information</h3>
    <form method="POST">
      <input type="hidden" name="action" value="profile">
      <div class="form-group"><label class="form-label">Full Name *</label><input type="text" name="name" class="form-control" value="<?= e($user['name']) ?>" required></div>
      <div class="form-group"><label class="form-label">Email Address</label><input type="email" class="form-control" value="<?= e($user['email']) ?>" disabled style="background:#f5f5f5"></div>
      <div class="form-group"><label class="form-label">Phone</label><input type="tel" name="phone" class="form-control" value="<?= e($user['phone']??'') ?>"></div>
      <div class="form-group"><label class="form-label">City</label><input type="text" name="city" class="form-control" value="<?= e($user['city']??'') ?>"></div>
      <div class="form-group"><label class="form-label">Address</label><textarea name="address" class="form-control" rows="2"><?= e($user['address']??'') ?></textarea></div>
      <button type="submit" class="btn btn-primary">Save Changes</button>
    </form>
  </div>
  <div class="card">
    <h3 style="margin-bottom:20px">Change Password</h3>
    <form method="POST">
      <input type="hidden" name="action" value="password">
      <div class="form-group"><label class="form-label">Current Password *</label><input type="password" name="current_password" class="form-control" required></div>
      <div class="form-group"><label class="form-label">New Password *</label><input type="password" name="new_password" class="form-control" required minlength="6"></div>
      <div class="form-group"><label class="form-label">Confirm New Password *</label><input type="password" name="confirm_password" class="form-control" required></div>
      <button type="submit" class="btn btn-primary">Change Password</button>
    </form>
    <div class="divider"></div>
    <div style="background:#f8faf2;border-radius:10px;padding:16px">
      <div class="text-muted" style="font-size:0.82rem;margin-bottom:8px">Account Info</div>
      <div style="font-size:0.88rem">Member since: <?= date('d M Y',strtotime($user['created_at'])) ?></div>
      <div style="font-size:0.88rem;margin-top:4px">Role: <span class="badge badge-lime"><?= ucfirst($user['role']) ?></span></div>
    </div>
  </div>
</div>
<?php require dirname(__DIR__) . '/includes/dash-footer.php'; ?>
