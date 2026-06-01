<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/auth.php';
requireAdmin();
$db = getDB();

// Approve / Reject / Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id     = (int)($_POST['id'] ?? 0);
    $action = $_POST['action'] ?? '';
    if ($id && in_array($action, ['approved', 'rejected'])) {
        $db->prepare('UPDATE reviews SET status=? WHERE id=?')->execute([$action, $id]);
        setFlash('success', 'Review ' . $action . '.');
    }
    redirect(SITE_URL . '/admin/reviews.php');
}
if (isset($_GET['delete'])) {
    $db->prepare('DELETE FROM reviews WHERE id=?')->execute([(int)$_GET['delete']]);
    setFlash('success', 'Review deleted.');
    redirect(SITE_URL . '/admin/reviews.php');
}

$filter = $_GET['status'] ?? 'all';
$sql = 'SELECT * FROM reviews';
if ($filter !== 'all') $sql .= ' WHERE status = ' . $db->quote($filter);
$sql .= ' ORDER BY created_at DESC';
$reviews = $db->query($sql)->fetchAll();

$counts = $db->query('SELECT status, COUNT(*) FROM reviews GROUP BY status')->fetchAll(PDO::FETCH_KEY_PAIR);

$dashRole  = 'admin';
$dashTitle = 'Manage Reviews';
$activeKey = 'reviews';
$dashNav   = [
    ['label'=>'Dashboard',         'key'=>'dashboard', 'url'=>'/admin/index.php',           'icon'=>'<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>'],
    ['label'=>'All Children',      'key'=>'children',  'url'=>'/admin/children.php',        'icon'=>'<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>'],
    ['label'=>'Vaccination Dates', 'key'=>'vax_dates', 'url'=>'/admin/vaccination-dates.php','icon'=>'<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="3" y1="10" x2="21" y2="10"/></svg>'],
    ['label'=>'Reports',           'key'=>'reports',   'url'=>'/admin/reports.php',         'icon'=>'<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16h16V8z"/></svg>'],
    ['label'=>'Vaccines',          'key'=>'vaccines',  'url'=>'/admin/vaccines.php',        'icon'=>'<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/></svg>'],
    ['label'=>'Parent Requests',   'key'=>'requests',  'url'=>'/admin/requests.php',        'icon'=>'<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8h1a4 4 0 010 8h-1"/></svg>'],
    ['label'=>'Hospitals',         'key'=>'hospitals', 'url'=>'/admin/hospitals.php',       'icon'=>'<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>'],
    ['label'=>'Booking Details',   'key'=>'bookings',  'url'=>'/admin/bookings.php',        'icon'=>'<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10"/></svg>'],
    ['label'=>'Reviews',           'key'=>'reviews',   'url'=>'/admin/reviews.php',         'icon'=>'<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>'],
];
require dirname(__DIR__) . '/includes/dash-header.php';
?>

<div class="widget-grid" style="grid-template-columns:repeat(3,1fr);margin-bottom:24px">
    <div class="widget"><div class="widget-value"><?= array_sum($counts) ?></div><div class="widget-label">Total Reviews</div></div>
    <div class="widget"><div class="widget-value" style="color:#90E300"><?= $counts['approved'] ?? 0 ?></div><div class="widget-label">Approved</div></div>
    <div class="widget"><div class="widget-value" style="color:#f59e0b"><?= $counts['pending'] ?? 0 ?></div><div class="widget-label">Pending</div></div>
</div>

<div class="filter-tabs" style="margin-bottom:20px">
    <?php foreach (['all','pending','approved','rejected'] as $s): ?>
    <a href="?status=<?= $s ?>" class="filter-tab <?= $filter===$s?'active':'' ?>" style="text-decoration:none"><?= ucfirst($s) ?></a>
    <?php endforeach; ?>
</div>

<div style="display:flex;flex-direction:column;gap:16px">
    <?php foreach ($reviews as $r): ?>
    <div class="card" style="padding:20px 24px">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:12px">
            <div style="flex:1;min-width:200px">
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px;flex-wrap:wrap">
                    <strong><?= e($r['name']) ?></strong>
                    <?php if ($r['location']): ?><span class="text-muted" style="font-size:0.82rem"><?= e($r['location']) ?></span><?php endif; ?>
                    <span class="badge <?= $r['status']==='approved'?'badge-lime':($r['status']==='rejected'?'badge-red':'badge-yellow') ?>"><?= ucfirst($r['status']) ?></span>
                </div>
                <!-- Star rating -->
                <div style="margin-bottom:10px">
                    <?php for ($i=1;$i<=5;$i++): ?>
                    <span style="color:<?= $i<=$r['rating']?'#f59e0b':'#ccc' ?>;font-size:1rem">&#9733;</span>
                    <?php endfor; ?>
                    <span class="text-muted" style="font-size:0.82rem;margin-left:4px"><?= $r['rating'] ?>/5</span>
                </div>
                <p style="font-size:0.9rem;color:#444;line-height:1.65;margin:0"><?= e($r['review_text']) ?></p>
                <div class="text-muted" style="font-size:0.78rem;margin-top:8px"><?= date('d M Y', strtotime($r['created_at'])) ?> &nbsp;|&nbsp; <?= e($r['email']) ?></div>
            </div>
            <div style="display:flex;gap:8px;flex-shrink:0">
                <?php if ($r['status'] === 'pending'): ?>
                <form method="POST" style="display:inline">
                    <input type="hidden" name="id" value="<?= $r['id'] ?>">
                    <button name="action" value="approved" class="btn btn-success btn-sm">Approve</button>
                    <button name="action" value="rejected" class="btn btn-secondary btn-sm">Reject</button>
                </form>
                <?php elseif ($r['status'] === 'rejected'): ?>
                <form method="POST" style="display:inline">
                    <input type="hidden" name="id" value="<?= $r['id'] ?>">
                    <button name="action" value="approved" class="btn btn-success btn-sm">Approve</button>
                </form>
                <?php endif; ?>
                <a href="?delete=<?= $r['id'] ?>" class="btn btn-danger btn-sm" data-confirm="Delete this review permanently?">Delete</a>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
    <?php if (!$reviews): ?>
    <div class="card text-center" style="padding:48px">
        <div style="width:56px;height:56px;background:#f0ffe6;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#4a7c00" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
        </div>
        <h3>No reviews found</h3>
        <p class="text-muted mt-8">Reviews submitted by users will appear here.</p>
    </div>
    <?php endif; ?>
</div>

<?php require dirname(__DIR__) . '/includes/dash-footer.php'; ?>
