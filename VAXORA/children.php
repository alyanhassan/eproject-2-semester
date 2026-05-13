<?php
require_once 'includes/auth_check.php';
checkRole(['parent']);
require_once 'config/config.php';
require_once 'config/database.php';

$db = (new Database())->getConnection();

$pStmt = $db->prepare("SELECT parent_id FROM parents WHERE user_id = ? LIMIT 1");
$pStmt->execute([$_SESSION['user_id']]);
$parent = $pStmt->fetch(PDO::FETCH_ASSOC);
if (!$parent) redirect('/auth/logout.php');
$parent_id = (int)$parent['parent_id'];

$error = ''; $success = '';

if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $child_id = (int)$_GET['delete'];
    $chk = $db->prepare("SELECT COUNT(*) FROM appointments WHERE child_id = ?");
    $chk->execute([$child_id]);
    if ($chk->fetchColumn() > 0) {
        $error = "Cannot delete: this child has existing appointment records.";
    } else {
        $del = $db->prepare("DELETE FROM children WHERE child_id = ? AND parent_id = ?");
        $del->execute([$child_id, $parent_id]);
        $success = "Child record deleted successfully.";
    }
}

$stmt = $db->prepare("
    SELECT c.*,
        (SELECT COUNT(*) FROM appointments a WHERE a.child_id = c.child_id AND a.status = 'pending') as pending_appts,
        (SELECT COUNT(*) FROM vaccination_records v WHERE v.child_id = c.child_id) as completed_vacc
    FROM children c WHERE c.parent_id = ? ORDER BY c.created_at DESC
");
$stmt->execute([$parent_id]);
$children = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'includes/header.php';
?>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-0">My Children</h3>
            <p class="text-muted small mb-0"><?= count($children) ?> child(ren) registered</p>
        </div>
        <a href="/add_child.php" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Add Child</a>
    </div>

    <?php if ($success): ?><div class="alert alert-success"><i class="fas fa-check-circle me-1"></i><?= htmlspecialchars($success) ?></div><?php endif; ?>
    <?php if ($error):   ?><div class="alert alert-danger"><i class="fas fa-exclamation-circle me-1"></i><?= htmlspecialchars($error) ?></div><?php endif; ?>

    <?php if ($children): ?>
        <div class="row g-4">
            <?php foreach ($children as $c):
                $dob = new DateTime($c['date_of_birth']);
                $age = (new DateTime())->diff($dob)->y;
            ?>
            <div class="col-md-6 col-lg-4">
                <div class="card h-100">
                    <div class="card-body text-center pt-4">
                        <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-inline-flex align-items-center justify-content-center mb-3" style="width:72px;height:72px;font-size:1.8rem;">
                            <?= $c['gender'] === 'female' ? '👧' : '👦' ?>
                        </div>
                        <h5 class="fw-bold mb-1"><?= htmlspecialchars($c['full_name']) ?></h5>
                        <p class="text-muted small mb-3">ID: <?= htmlspecialchars($c['unique_child_id'] ?? '#'.$c['child_id']) ?></p>
                        <div class="row g-2 text-start mb-3">
                            <div class="col-6"><small class="text-muted d-block">Date of Birth</small><strong class="small"><?= date('d M Y', strtotime($c['date_of_birth'])) ?></strong></div>
                            <div class="col-6"><small class="text-muted d-block">Age</small><strong class="small"><?= $age ?> years</strong></div>
                            <div class="col-6"><small class="text-muted d-block">Gender</small><strong class="small"><?= ucfirst($c['gender'] ?? 'N/A') ?></strong></div>
                            <div class="col-6"><small class="text-muted d-block">Blood Group</small><strong class="small text-danger"><?= htmlspecialchars($c['blood_group'] ?? 'N/A') ?></strong></div>
                        </div>
                        <div class="d-flex gap-2 justify-content-center mb-2">
                            <span class="badge bg-warning text-dark">⏳ <?= $c['pending_appts'] ?> Pending</span>
                            <span class="badge bg-success">✅ <?= $c['completed_vacc'] ?> Done</span>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-top d-flex gap-2">
                        <a href="/edit_child.php?id=<?= $c['child_id'] ?>" class="btn btn-outline-primary btn-sm flex-fill"><i class="fas fa-edit me-1"></i>Edit</a>
                        <a href="/book_appointment.php?child_id=<?= $c['child_id'] ?>" class="btn btn-primary btn-sm flex-fill"><i class="fas fa-calendar-plus me-1"></i>Book</a>
                        <a href="?delete=<?= $c['child_id'] ?>" onclick="return confirmDelete('<?= addslashes($c['full_name']) ?>')" class="btn btn-outline-danger btn-sm"><i class="fas fa-trash"></i></a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="card">
            <div class="card-body text-center py-5">
                <div style="font-size:4rem;" class="mb-3">👶</div>
                <h5 class="text-muted">No children registered yet</h5>
                <p class="text-muted small mb-4">Add your first child to start booking vaccination appointments</p>
                <a href="/add_child.php" class="btn btn-primary px-4"><i class="fas fa-plus me-2"></i>Add First Child</a>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php include 'includes/footer.php'; ?>
