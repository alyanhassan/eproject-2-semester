<?php
require_once '../includes/auth_check.php';
checkRole(['admin']);
require_once '../config/config.php';
require_once '../config/database.php';

$db = (new Database())->getConnection();
$pageTitle = 'Dashboard Overview';

$stats = [
    'parents'   => $db->query("SELECT COUNT(*) FROM users WHERE role='parent'")->fetchColumn(),
    'children'  => $db->query("SELECT COUNT(*) FROM children")->fetchColumn(),
    'hospitals' => $db->query("SELECT COUNT(*) FROM hospitals WHERE status='active'")->fetchColumn(),
    'vaccines'  => $db->query("SELECT COUNT(*) FROM vaccines WHERE status='active'")->fetchColumn(),
    'pending'   => $db->query("SELECT COUNT(*) FROM appointments WHERE status='pending'")->fetchColumn(),
    'completed' => $db->query("SELECT COUNT(*) FROM vaccination_records")->fetchColumn(),
    'approved'  => $db->query("SELECT COUNT(*) FROM appointments WHERE status='approved'")->fetchColumn(),
    'inquiries' => $db->query("SELECT COUNT(*) FROM contact_messages WHERE status='pending'")->fetchColumn(),
];

$recent = $db->query("
    SELECT a.appointment_date, a.status, c.full_name as child_name, h.hospital_name, v.vaccine_name
    FROM appointments a
    JOIN children c ON a.child_id=c.child_id
    JOIN hospitals h ON a.hospital_id=h.hospital_id
    JOIN vaccines v ON a.vaccine_id=v.vaccine_id
    ORDER BY a.created_at DESC LIMIT 8
")->fetchAll(PDO::FETCH_ASSOC);

$topHospitals = $db->query("
    SELECT h.hospital_name, COUNT(*) as cnt
    FROM vaccination_records vr
    JOIN hospitals h ON vr.hospital_id=h.hospital_id
    GROUP BY vr.hospital_id ORDER BY cnt DESC LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

$topVaccines = $db->query("
    SELECT v.vaccine_name, COUNT(*) as cnt
    FROM vaccination_records vr
    JOIN vaccines v ON vr.vaccine_id=v.vaccine_id
    GROUP BY vr.vaccine_id ORDER BY cnt DESC LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

include '../includes/admin_header.php';
?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<!-- Stats Row -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-4 col-xl-2">
        <div class="card stat-card sc-violet h-100">
            <div class="stat-num"><?= $stats['parents'] ?></div>
            <div class="stat-label">Parents</div>
            <i class="fas fa-users stat-icon"></i>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="card stat-card sc-purple h-100">
            <div class="stat-num"><?= $stats['children'] ?></div>
            <div class="stat-label">Children</div>
            <i class="fas fa-child stat-icon"></i>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="card stat-card sc-cyan h-100">
            <div class="stat-num"><?= $stats['hospitals'] ?></div>
            <div class="stat-label">Hospitals</div>
            <i class="fas fa-hospital stat-icon"></i>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="card stat-card sc-green h-100">
            <div class="stat-num"><?= $stats['vaccines'] ?></div>
            <div class="stat-label">Vaccines</div>
            <i class="fas fa-vial stat-icon"></i>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="card stat-card sc-amber h-100">
            <div class="stat-num"><?= $stats['pending'] ?></div>
            <div class="stat-label">Pending</div>
            <i class="fas fa-clock stat-icon"></i>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="card stat-card sc-rose h-100">
            <div class="stat-num"><?= $stats['completed'] ?></div>
            <div class="stat-label">Vaccinated</div>
            <i class="fas fa-check-double stat-icon"></i>
        </div>
    </div>
</div>

<!-- Quick Action Tiles -->
<div class="row g-3 mb-4">
    <?php
    $qa = [
        ['/admin/manage_hospitals.php', 'fas fa-hospital',      'Manage Hospitals', $stats['hospitals'].' active',  '#4A6120','#F4F0E6'],
        ['/admin/manage_vaccines.php',  'fas fa-vial',          'Manage Vaccines',  $stats['vaccines'].' types',    '#4A7C59','#f0fdf4'],
        ['/admin/appointments.php',     'fas fa-calendar-check','Appointments',     $stats['pending'].' pending',   '#C8A84B','#fffbeb'],
        ['/admin/inquiries.php',        'fas fa-envelope',      'Inquiries',        $stats['inquiries'].' unread',  '#C0392B','#fef2f2'],
    ];
    foreach ($qa as $a): ?>
    <div class="col-6 col-md-3">
        <a href="<?= $a[0] ?>" class="card p-3 text-decoration-none d-flex align-items-center gap-3">
            <div style="width:48px;height:48px;background:<?= $a[5] ?>;border-radius:14px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="<?= $a[1] ?>" style="color:<?= $a[4] ?>;font-size:1.2rem;"></i>
            </div>
            <div>
                <div class="fw-semibold" style="font-size:0.88rem;color:#2C1F0A;"><?= $a[2] ?></div>
                <div class="text-muted" style="font-size:0.75rem;"><?= $a[3] ?></div>
            </div>
        </a>
    </div>
    <?php endforeach; ?>
</div>

<!-- Charts + Tables Row -->
<div class="row g-4 mb-4">
    <!-- Donut Chart: Appointment Status -->
    <div class="col-md-5">
        <div class="card chart-card h-100">
            <div class="card-header"><i class="fas fa-chart-pie me-2 text-violet"></i>Appointment Status</div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <canvas id="statusChart" height="220"></canvas>
            </div>
        </div>
    </div>

    <!-- Top Hospitals Bar -->
    <div class="col-md-7">
        <div class="card chart-card h-100">
            <div class="card-header"><i class="fas fa-hospital me-2" style="color:#8B5E3C;"></i>Top Hospitals by Vaccinations</div>
            <div class="card-body">
                <?php if ($topHospitals): ?>
                    <?php $max = max(array_column($topHospitals,'cnt')); ?>
                    <?php foreach ($topHospitals as $i => $h): ?>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span style="font-size:0.82rem;font-weight:600;"><?= htmlspecialchars($h['hospital_name']) ?></span>
                            <span class="badge" style="background:#F4F0E6;color:#4A6120;"><?= $h['cnt'] ?></span>
                        </div>
                        <div class="progress" style="height:6px;border-radius:4px;background:#EDE7D5;">
                            <?php $w = $max > 0 ? round(($h['cnt']/$max)*100) : 0; ?>
                            <div class="progress-bar" style="width:<?= $w ?>%;background:linear-gradient(90deg,#4A6120,#8B5E3C);border-radius:4px;"></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center text-muted py-4">No vaccination data yet</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Recent Appointments Table -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span class="fw-semibold"><i class="fas fa-history me-2 text-violet"></i>Recent Appointments</span>
        <a href="/admin/appointments.php" class="btn btn-sm btn-primary">View All</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead><tr><th>Child</th><th>Vaccine</th><th>Hospital</th><th>Date</th><th>Status</th></tr></thead>
                <tbody>
                <?php foreach ($recent as $r): ?>
                <tr>
                    <td class="fw-semibold"><?= htmlspecialchars($r['child_name']) ?></td>
                    <td><span class="badge" style="background:#F4F0E6;color:#4A6120;border:1px solid rgba(74,97,32,0.2);font-size:0.72rem;"><?= htmlspecialchars($r['vaccine_name']) ?></span></td>
                    <td class="text-muted" style="font-size:0.82rem;"><?= htmlspecialchars(explode(' ',$r['hospital_name'])[0]) ?> <?= htmlspecialchars(explode(' ',$r['hospital_name'])[1] ?? '') ?>...</td>
                    <td style="font-size:0.82rem;"><?= date('d M Y', strtotime($r['appointment_date'])) ?></td>
                    <td>
                        <?php $cls=['pending'=>'bg-warning text-dark','approved'=>'bg-info','completed'=>'bg-success','cancelled'=>'bg-danger']; ?>
                        <span class="badge <?= $cls[$r['status']] ?? 'bg-secondary' ?>"><?= ucfirst($r['status']) ?></span>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($recent)): ?><tr><td colspan="5" class="text-center py-4 text-muted">No appointments yet</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
const ctx = document.getElementById('statusChart').getContext('2d');
new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: ['Pending', 'Approved', 'Completed'],
        datasets: [{
            data: [<?= $stats['pending'] ?>, <?= $stats['approved'] ?>, <?= $stats['completed'] ?>],
            backgroundColor: ['#C8A84B','#8B5E3C','#4A7C59'],
            borderWidth: 0,
            hoverOffset: 6
        }]
    },
    options: {
        cutout: '70%',
        plugins: {
            legend: { position: 'bottom', labels: { font: { family: 'Poppins', size: 12 }, padding: 16, boxWidth: 12 } }
        },
        responsive: true,
        maintainAspectRatio: false
    }
});
</script>

<?php include '../includes/admin_footer.php'; ?>
