<?php
require_once '../includes/auth_check.php';
checkRole(['admin']);
require_once '../config/config.php';
require_once '../config/database.php';

$db = (new Database())->getConnection();
$pageTitle = 'Contact Inquiries';
$success = '';

if (isset($_GET['mark_replied']) && is_numeric($_GET['mark_replied'])) {
    $db->prepare("UPDATE contact_messages SET status='replied' WHERE id=?")->execute([(int)$_GET['mark_replied']]);
    $success = "Message marked as replied.";
}
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $db->prepare("DELETE FROM contact_messages WHERE id=?")->execute([(int)$_GET['delete']]);
    $success = "Message deleted.";
}

$filter = $_GET['filter'] ?? 'all';
$where = $filter !== 'all' ? "WHERE status='$filter'" : '';
$messages = $db->query("SELECT * FROM contact_messages $where ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);

include '../includes/admin_header.php';
?>
<?php if ($success): ?><div class="alert alert-success"><i class="fas fa-check-circle me-1"></i><?= htmlspecialchars($success) ?></div><?php endif; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex gap-2">
        <a href="?filter=all" class="btn btn-sm <?= $filter==='all' ? 'btn-primary' : 'btn-outline-secondary' ?>">All</a>
        <a href="?filter=pending" class="btn btn-sm <?= $filter==='pending' ? 'btn-warning' : 'btn-outline-secondary' ?>">Pending</a>
        <a href="?filter=replied" class="btn btn-sm <?= $filter==='replied' ? 'btn-success' : 'btn-outline-secondary' ?>">Replied</a>
    </div>
    <span class="text-muted small"><?= count($messages) ?> message(s)</span>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead><tr><th>From</th><th>Email</th><th>Subject</th><th>Message</th><th>Received</th><th>Status</th><th class="text-center">Actions</th></tr></thead>
                <tbody>
                <?php foreach ($messages as $m): ?>
                <tr>
                    <td class="fw-semibold"><?= htmlspecialchars($m['name']) ?></td>
                    <td class="small"><?= htmlspecialchars($m['email']) ?></td>
                    <td class="small"><?= htmlspecialchars($m['subject'] ?? '—') ?></td>
                    <td class="small text-muted" style="max-width:220px;"><?= htmlspecialchars(substr($m['message'], 0, 80)) ?><?= strlen($m['message']) > 80 ? '...' : '' ?></td>
                    <td class="small text-muted"><?= date('d M Y', strtotime($m['created_at'])) ?></td>
                    <td><span class="badge <?= $m['status']==='replied' ? 'bg-success' : 'bg-warning text-dark' ?>"><?= ucfirst($m['status']) ?></span></td>
                    <td class="text-center">
                        <?php if ($m['status'] === 'pending'): ?>
                            <a href="?mark_replied=<?= $m['id'] ?>&filter=<?= $filter ?>" class="btn btn-sm btn-success me-1" title="Mark Replied"><i class="fas fa-check"></i></a>
                        <?php endif; ?>
                        <a href="?delete=<?= $m['id'] ?>&filter=<?= $filter ?>" onclick="return confirmDelete('this message')" class="btn btn-sm btn-outline-danger" title="Delete"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($messages)): ?><tr><td colspan="7" class="text-center py-4 text-muted">No messages found.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include '../includes/admin_footer.php'; ?>
