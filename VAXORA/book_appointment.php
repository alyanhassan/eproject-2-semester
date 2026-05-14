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

$children  = $db->prepare("SELECT child_id, full_name FROM children WHERE parent_id = ? ORDER BY full_name");
$children->execute([$parent_id]);
$children  = $children->fetchAll(PDO::FETCH_ASSOC);

$hospitals = $db->query("SELECT hospital_id, hospital_name, city FROM hospitals WHERE status='active' ORDER BY hospital_name")->fetchAll(PDO::FETCH_ASSOC);
$vaccines  = $db->query("SELECT vaccine_id, vaccine_name, age_group FROM vaccines WHERE status='active' ORDER BY vaccine_name")->fetchAll(PDO::FETCH_ASSOC);

$error = ''; $success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $child_id    = (int)($_POST['child_id'] ?? 0);
    $hospital_id = (int)($_POST['hospital_id'] ?? 0);
    $vaccine_id  = (int)($_POST['vaccine_id'] ?? 0);
    $appt_date   = sanitizeInput($_POST['appointment_date'] ?? '');

    if (!$child_id || !$hospital_id || !$vaccine_id || !$appt_date) {
        $error = 'All fields are required.';
    } elseif ($appt_date < date('Y-m-d')) {
        $error = 'Appointment date cannot be in the past.';
    } else {
        // Verify child belongs to this parent
        $chk = $db->prepare("SELECT child_id FROM children WHERE child_id = ? AND parent_id = ?");
        $chk->execute([$child_id, $parent_id]);
        if (!$chk->fetch()) { $error = 'Invalid child selected.'; }
        else {
            try {
                $stmt = $db->prepare("INSERT INTO appointments (child_id,hospital_id,vaccine_id,appointment_date,status) VALUES (?,?,?,?,'pending')");
                $stmt->execute([$child_id, $hospital_id, $vaccine_id, $appt_date]);
                $success = 'Appointment booked successfully! The hospital will review and confirm your request.';
            } catch (PDOException $e) {
                $error = 'Failed to book appointment. Please try again.';
                error_log($e->getMessage());
            }
        }
    }
}
$preselect = (int)($_GET['child_id'] ?? 0);
include 'includes/header.php';
?>
<div class="container py-4" style="max-width:700px;">
    <div class="mb-4">
        <a href="/dashboard.php" class="text-muted text-decoration-none small"><i class="fas fa-arrow-left me-1"></i>Back to Dashboard</a>
        <h3 class="fw-bold mt-2 mb-0">Book Vaccination Appointment</h3>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success"><i class="fas fa-check-circle me-2"></i><?= htmlspecialchars($success) ?>
            <div class="mt-2 d-flex gap-2">
                <a href="/dashboard.php" class="btn btn-success btn-sm">View Dashboard</a>
                <a href="/vaccination_history.php" class="btn btn-outline-success btn-sm">View History</a>
            </div>
        </div>
    <?php endif; ?>
    <?php if ($error): ?><div class="alert alert-danger"><i class="fas fa-exclamation-circle me-1"></i><?= htmlspecialchars($error) ?></div><?php endif; ?>

    <?php if (empty($children)): ?>
        <div class="alert alert-info"><i class="fas fa-info-circle me-1"></i>You need to <a href="/add_child.php">add a child</a> before booking an appointment.</div>
    <?php else: ?>
    <div class="card">
        <div class="card-body p-4">
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Select Child *</label>
                    <select name="child_id" class="form-select" required>
                        <option value="">-- Choose Child --</option>
                        <?php foreach ($children as $c): ?>
                            <option value="<?= $c['child_id'] ?>" <?= $preselect === $c['child_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($c['full_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Select Hospital *</label>
                    <select name="hospital_id" class="form-select" required>
                        <option value="">-- Choose Hospital --</option>
                        <?php foreach ($hospitals as $h): ?>
                            <option value="<?= $h['hospital_id'] ?>"><?= htmlspecialchars($h['hospital_name']) ?> — <?= htmlspecialchars($h['city']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Select Vaccine *</label>
                    <select name="vaccine_id" class="form-select" required>
                        <option value="">-- Choose Vaccine --</option>
                        <?php foreach ($vaccines as $v): ?>
                            <option value="<?= $v['vaccine_id'] ?>"><?= htmlspecialchars($v['vaccine_name']) ?><?= $v['age_group'] ? ' (' . $v['age_group'] . ')' : '' ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="form-label">Preferred Appointment Date *</label>
                    <input type="date" name="appointment_date" class="form-control" data-min-today
                           min="<?= date('Y-m-d', strtotime('+1 day')) ?>"
                           max="<?= date('Y-m-d', strtotime('+30 days')) ?>" required>
                    <div class="form-text">Dates available within the next 30 days. Hospital staff will confirm.</div>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-lg fw-bold"><i class="fas fa-calendar-check me-2"></i>Confirm Booking</button>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>
</div>
<?php include 'includes/footer.php'; ?>
