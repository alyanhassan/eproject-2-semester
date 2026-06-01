<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/auth.php';
require_once dirname(__DIR__) . '/includes/mailer.php';
requireAdmin();

$db = getDB();

// Handle approve / reject / complete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['id'])) {
    $id     = (int)$_POST['id'];
    $action = $_POST['action'];

    if (in_array($action, ['approved', 'rejected', 'completed'])) {
        $db->prepare('UPDATE appointments SET status=? WHERE id=?')->execute([$action, $id]);

        // If approving, create vaccination record placeholder
        if ($action === 'approved') {
            $appt = $db->prepare('SELECT * FROM appointments WHERE id=?');
            $appt->execute([$id]);
            $a = $appt->fetch();
            if ($a) {
                $db->prepare('INSERT IGNORE INTO vaccination_records (appointment_id,child_id,vaccine_id,hospital_id,date_given,status) VALUES (?,?,?,?,?,?)')
                   ->execute([$id, $a['child_id'], $a['vaccine_id'], $a['hospital_id'], $a['appointment_date'], 'pending']);
            }
        }

        // Send email notification to parent
        if (in_array($action, ['approved', 'rejected'])) {
            $row = $db->prepare('
                SELECT a.*, u.name AS parent_name, u.email AS parent_email,
                       c.name AS child_name, v.name AS vaccine_name,
                       h.name AS hospital_name
                FROM appointments a
                JOIN users u    ON a.parent_id   = u.id
                JOIN children c ON a.child_id    = c.id
                JOIN vaccines v ON a.vaccine_id  = v.id
                JOIN hospitals h ON a.hospital_id = h.id
                WHERE a.id = ?
            ');
            $row->execute([$id]);
            $apptData = $row->fetch();

            if ($apptData) {
                sendAppointmentStatusEmail(
                    $apptData['parent_email'],
                    $apptData['parent_name'],
                    $action,
                    $apptData
                );
            }
        }

        setFlash('success', 'Appointment ' . $action . '.' . (MAIL_ENABLED ? ' Email sent to parent.' : ''));
    }
    redirect(SITE_URL . '/admin/requests.php');
}

$filter = $_GET['status'] ?? 'all';
$sql    = 'SELECT a.*, u.name AS parent_name, u.email AS parent_email,
                  c.name AS child_name, v.name AS vaccine_name,
                  h.name AS hospital_name
           FROM appointments a
           JOIN users u     ON a.parent_id   = u.id
           JOIN children c  ON a.child_id    = c.id
           JOIN vaccines v  ON a.vaccine_id  = v.id
           JOIN hospitals h ON a.hospital_id = h.id';
if ($filter !== 'all') { $sql .= ' WHERE a.status = ?'; }
$sql .= ' ORDER BY a.created_at DESC';
$stmt = $db->prepare($sql);
$stmt->execute($filter !== 'all' ? [$filter] : []);
$appointments = $stmt->fetchAll();

$dashRole  = 'admin';
$dashTitle = 'Parent Requests';
$activeKey = 'requests';
$dashNav = [
    ['label'=>'Dashboard',         'key'=>'dashboard', 'url'=>'/admin/index.php',            'icon'=>'<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>'],
    ['label'=>'All Children',      'key'=>'children',  'url'=>'/admin/children.php',         'icon'=>'<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>'],
    ['label'=>'Vaccination Dates', 'key'=>'vax_dates', 'url'=>'/admin/vaccination-dates.php','icon'=>'<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="3" y1="10" x2="21" y2="10"/></svg>'],
    ['label'=>'Reports',           'key'=>'reports',   'url'=>'/admin/reports.php',          'icon'=>'<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16h16V8z"/></svg>'],
    ['label'=>'Vaccines',          'key'=>'vaccines',  'url'=>'/admin/vaccines.php',         'icon'=>'<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/></svg>'],
    ['label'=>'Parent Requests',   'key'=>'requests',  'url'=>'/admin/requests.php',         'icon'=>'<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8h1a4 4 0 010 8h-1"/></svg>'],
    ['label'=>'Hospitals',         'key'=>'hospitals', 'url'=>'/admin/hospitals.php',        'icon'=>'<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>'],
    ['label'=>'Booking Details',   'key'=>'bookings',  'url'=>'/admin/bookings.php',         'icon'=>'<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10"/></svg>'],
    ['label'=>'Reviews',           'key'=>'reviews',   'url'=>'/admin/reviews.php',          'icon'=>'<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>'],
];
require dirname(__DIR__) . '/includes/dash-header.php';
?>

<?php if (!MAIL_ENABLED): ?>
<div class="alert alert-info" style="margin-bottom:20px">
    <strong>Email notifications are disabled.</strong>
    Open <code>includes/mailer.php</code>, set your SMTP credentials, and set <code>MAIL_ENABLED = true</code> to enable automatic emails on approve/reject.
</div>
<?php endif; ?>

<div class="filter-tabs" style="margin-bottom:20px">
    <?php foreach (['all','pending','approved','rejected','completed'] as $s): ?>
    <a href="?status=<?= $s ?>" class="filter-tab <?= $filter===$s?'active':'' ?>" style="text-decoration:none"><?= ucfirst($s) ?></a>
    <?php endforeach; ?>
</div>

<div class="card" style="padding:0">
    <div class="card-header">Appointment Requests (<?= count($appointments) ?>)</div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>#</th><th>Parent</th><th>Child</th><th>Vaccine</th><th>Hospital</th><th>Date</th><th>Status</th><th>Actions</th></tr>
            </thead>
            <tbody>
                <?php foreach ($appointments as $i => $a): ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td>
                        <?= e($a['parent_name']) ?>
                        <br><small class="text-muted"><?= e($a['parent_email']) ?></small>
                    </td>
                    <td><?= e($a['child_name']) ?></td>
                    <td><?= e($a['vaccine_name']) ?></td>
                    <td><?= e($a['hospital_name']) ?></td>
                    <td>
                        <strong><?= date('d M Y', strtotime($a['appointment_date'])) ?></strong>
                        <?php if ($a['appointment_time']): ?>
                        <br><small class="text-muted"><?= date('h:i A', strtotime($a['appointment_time'])) ?></small>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php $colors = ['pending'=>'badge-yellow','approved'=>'badge-blue','completed'=>'badge-lime','rejected'=>'badge-red','cancelled'=>'badge-gray']; ?>
                        <span class="badge <?= $colors[$a['status']] ?? 'badge-gray' ?>"><?= ucfirst($a['status']) ?></span>
                    </td>
                    <td>
                        <?php if ($a['status'] === 'pending'): ?>
                        <form method="POST" style="display:inline;white-space:nowrap">
                            <input type="hidden" name="id" value="<?= $a['id'] ?>">
                            <button name="action" value="approved" class="btn btn-success btn-sm">Approve</button>
                            <button name="action" value="rejected" class="btn btn-danger btn-sm">Reject</button>
                        </form>
                        <?php elseif ($a['status'] === 'approved'): ?>
                        <form method="POST" style="display:inline">
                            <input type="hidden" name="id" value="<?= $a['id'] ?>">
                            <button name="action" value="completed" class="btn btn-primary btn-sm">Mark Done</button>
                        </form>
                        <?php else: ?>
                        <span class="text-muted">—</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (!$appointments): ?>
                <tr><td colspan="8" class="text-center text-muted" style="padding:28px">No requests found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require dirname(__DIR__) . '/includes/dash-footer.php'; ?>
