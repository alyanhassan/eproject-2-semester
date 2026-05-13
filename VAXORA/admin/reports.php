<?php
require_once '../includes/auth_check.php';
checkRole(['admin']);
require_once '../config/config.php';
require_once '../config/database.php';

$db = (new Database())->getConnection();
$pageTitle = 'Reports & Analytics';

$stats = [
    'parents'    => $db->query("SELECT COUNT(*) FROM users WHERE role='parent'")->fetchColumn(),
    'children'   => $db->query("SELECT COUNT(*) FROM children")->fetchColumn(),
    'hospitals'  => $db->query("SELECT COUNT(*) FROM hospitals WHERE status='active'")->fetchColumn(),
    'vaccines'   => $db->query("SELECT COUNT(*) FROM vaccines WHERE status='active'")->fetchColumn(),
    'total_appt' => $db->query("SELECT COUNT(*) FROM appointments")->fetchColumn(),
    'pending'    => $db->query("SELECT COUNT(*) FROM appointments WHERE status='pending'")->fetchColumn(),
    'completed'  => $db->query("SELECT COUNT(*) FROM appointments WHERE status='completed'")->fetchColumn(),
    'cancelled'  => $db->query("SELECT COUNT(*) FROM appointments WHERE status='cancelled'")->fetchColumn(),
    'records'    => $db->query("SELECT COUNT(*) FROM vaccination_records")->fetchColumn(),
    'inquiries'  => $db->query("SELECT COUNT(*) FROM contact_messages WHERE status='pending'")->fetchColumn(),
];
$completion_rate = $stats['total_appt'] > 0 ? round(($stats['completed']/$stats['total_appt'])*100) : 0;

$topVaccines  = $db->query("SELECT v.vaccine_name, COUNT(*) as cnt FROM vaccination_records vr JOIN vaccines v ON vr.vaccine_id=v.vaccine_id GROUP BY vr.vaccine_id ORDER BY cnt DESC LIMIT 8")->fetchAll(PDO::FETCH_ASSOC);
$topHospitals = $db->query("SELECT h.hospital_name, COUNT(*) as cnt FROM vaccination_records vr JOIN hospitals h ON vr.hospital_id=h.hospital_id GROUP BY vr.hospital_id ORDER BY cnt DESC LIMIT 6")->fetchAll(PDO::FETCH_ASSOC);
$childGender  = $db->query("SELECT gender, COUNT(*) as cnt FROM children GROUP BY gender")->fetchAll(PDO::FETCH_ASSOC);
$genderData   = ['male'=>0,'female'=>0,'other'=>0];
foreach ($childGender as $g) $genderData[$g['gender']??'other'] = $g['cnt'];

// Fake monthly data for chart
$months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
$monthlyVacc = [3, 5, 4, 7, 6, 9, 8, 12, 10, 11, 8, 15];
$monthlyAppts = [4, 6, 5, 8, 7, 10, 9, 14, 12, 13, 10, 17];

include '../includes/admin_header.php';
?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="text-muted small">Data as of <?= date('d F Y') ?></div>
    <button onclick="window.print()" class="btn btn-outline-secondary btn-sm no-print"><i class="fas fa-print me-1"></i>Print Report</button>
</div>

<!-- KPI Row -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3"><div class="card stat-card sc-violet p-3"><div class="stat-num"><?= $stats['parents'] ?></div><div class="stat-label">Registered Parents</div><i class="fas fa-users stat-icon"></i></div></div>
    <div class="col-6 col-md-3"><div class="card stat-card sc-purple p-3"><div class="stat-num"><?= $stats['children'] ?></div><div class="stat-label">Children in System</div><i class="fas fa-child stat-icon"></i></div></div>
    <div class="col-6 col-md-3"><div class="card stat-card sc-green p-3"><div class="stat-num"><?= $stats['records'] ?></div><div class="stat-label">Vaccinations Done</div><i class="fas fa-check-double stat-icon"></i></div></div>
    <div class="col-6 col-md-3"><div class="card stat-card sc-cyan p-3"><div class="stat-num"><?= $completion_rate ?>%</div><div class="stat-label">Completion Rate</div><i class="fas fa-percentage stat-icon"></i></div></div>
</div>

<!-- Monthly Trend Chart -->
<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="card chart-card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-chart-line me-2 text-violet"></i>Monthly Vaccination Trend (2024)</span>
            </div>
            <div class="card-body">
                <canvas id="trendChart" height="200"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card chart-card h-100">
            <div class="card-header"><i class="fas fa-venus-mars me-2 text-violet"></i>Children by Gender</div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <canvas id="genderChart" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Appointment Breakdown + Top Vaccines -->
<div class="row g-4 mb-4">
    <div class="col-md-5">
        <div class="card chart-card h-100">
            <div class="card-header"><i class="fas fa-chart-bar me-2 text-violet"></i>Appointment Breakdown</div>
            <div class="card-body">
                <div class="row g-2 mb-4">
                    <div class="col-6"><div class="p-3 rounded-3 text-center" style="background:#fffbeb;border:1px solid #fde68a;"><div class="fw-bold h4 mb-0" style="color:#d97706;"><?= $stats['pending'] ?></div><div class="small text-muted">Pending</div></div></div>
                    <div class="col-6"><div class="p-3 rounded-3 text-center" style="background:#f0fdf4;border:1px solid #a7f3d0;"><div class="fw-bold h4 mb-0 text-success"><?= $stats['completed'] ?></div><div class="small text-muted">Completed</div></div></div>
                    <div class="col-6"><div class="p-3 rounded-3 text-center" style="background:#fef2f2;border:1px solid #fecaca;"><div class="fw-bold h4 mb-0 text-danger"><?= $stats['cancelled'] ?></div><div class="small text-muted">Cancelled</div></div></div>
                    <div class="col-6"><div class="p-3 rounded-3 text-center" style="background:#f5f3ff;border:1px solid #ddd6fe;"><div class="fw-bold h4 mb-0 text-violet"><?= $stats['total_appt'] ?></div><div class="small text-muted">Total</div></div></div>
                </div>
                <div>
                    <div class="d-flex justify-content-between small mb-1"><span>Completion Rate</span><strong><?= $completion_rate ?>%</strong></div>
                    <div class="progress mb-2" style="height:8px;border-radius:4px;">
                        <div class="progress-bar" style="width:<?= $completion_rate ?>%;background:linear-gradient(90deg,#10b981,#059669);border-radius:4px;"></div>
                    </div>
                    <div class="d-flex justify-content-between small mb-1"><span>Hospitals Active</span><strong><?= $stats['hospitals'] ?>/10</strong></div>
                    <div class="progress" style="height:8px;border-radius:4px;">
                        <div class="progress-bar" style="width:<?= round($stats['hospitals']/10*100) ?>%;background:linear-gradient(90deg,#7c3aed,#0ea5e9);border-radius:4px;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-7">
        <div class="card chart-card h-100">
            <div class="card-header"><i class="fas fa-vial me-2 text-success"></i>Vaccines Administered</div>
            <div class="card-body">
                <canvas id="vaccineChart" height="220"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Tables -->
<div class="row g-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><i class="fas fa-trophy me-2" style="color:#f59e0b;"></i>Top Hospitals by Vaccinations</div>
            <div class="card-body p-0">
                <?php if ($topHospitals): ?>
                <table class="table mb-0">
                    <thead><tr><th>#</th><th>Hospital</th><th class="text-end">Doses</th></tr></thead>
                    <tbody>
                    <?php foreach ($topHospitals as $i => $h): ?>
                    <tr>
                        <td><span class="badge" style="background:<?= ['#f59e0b','#9ca3af','#cd7f32','#7c3aed','#0ea5e9','#10b981'][$i] ?? '#9ca3af' ?>;"><?= $i+1 ?></span></td>
                        <td style="font-size:0.85rem;"><?= htmlspecialchars($h['hospital_name']) ?></td>
                        <td class="text-end"><span class="badge bg-success"><?= $h['cnt'] ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?><div class="text-center py-4 text-muted small">No records yet</div><?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><i class="fas fa-vial me-2" style="color:#7c3aed;"></i>Most Used Vaccines</div>
            <div class="card-body p-0">
                <?php if ($topVaccines): ?>
                <table class="table mb-0">
                    <thead><tr><th>Vaccine</th><th class="text-end">Times Given</th></tr></thead>
                    <tbody>
                    <?php foreach ($topVaccines as $v): ?>
                    <tr>
                        <td style="font-size:0.85rem;"><?= htmlspecialchars($v['vaccine_name']) ?></td>
                        <td class="text-end"><span class="badge" style="background:#f5f3ff;color:#7c3aed;border:1px solid rgba(124,58,237,0.2);"><?= $v['cnt'] ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?><div class="text-center py-4 text-muted small">No records yet</div><?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
// Trend Chart
new Chart(document.getElementById('trendChart').getContext('2d'), {
    type: 'line',
    data: {
        labels: <?= json_encode($months) ?>,
        datasets: [
            {
                label: 'Vaccinations', data: <?= json_encode($monthlyVacc) ?>,
                borderColor: '#7c3aed', backgroundColor: 'rgba(124,58,237,0.08)',
                fill: true, tension: 0.4, pointRadius: 4, pointBackgroundColor: '#7c3aed'
            },
            {
                label: 'Appointments', data: <?= json_encode($monthlyAppts) ?>,
                borderColor: '#0ea5e9', backgroundColor: 'rgba(14,165,233,0.08)',
                fill: true, tension: 0.4, pointRadius: 4, pointBackgroundColor: '#0ea5e9'
            }
        ]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { position: 'top', labels: { font: { family: 'Poppins' }, boxWidth: 10, padding: 16 } } },
        scales: {
            y: { beginAtZero: true, grid: { color: '#f3f4f6' }, ticks: { font: { family: 'Poppins', size: 11 } } },
            x: { grid: { display: false }, ticks: { font: { family: 'Poppins', size: 11 } } }
        }
    }
});

// Gender Chart
new Chart(document.getElementById('genderChart').getContext('2d'), {
    type: 'doughnut',
    data: {
        labels: ['Male', 'Female', 'Other'],
        datasets: [{ data: [<?= $genderData['male'] ?>, <?= $genderData['female'] ?>, <?= $genderData['other'] ?>],
            backgroundColor: ['#0ea5e9','#f43f5e','#a78bfa'], borderWidth: 0 }]
    },
    options: {
        cutout: '65%',
        plugins: { legend: { position: 'bottom', labels: { font: { family: 'Poppins', size: 11 }, boxWidth: 10, padding: 12 } } },
        responsive: true, maintainAspectRatio: false
    }
});

// Vaccine Bar Chart
const vaccineNames = <?= json_encode(array_column($topVaccines,'vaccine_name')) ?>;
const vaccineCounts = <?= json_encode(array_column($topVaccines,'cnt')) ?>;
new Chart(document.getElementById('vaccineChart').getContext('2d'), {
    type: 'bar',
    data: {
        labels: vaccineNames,
        datasets: [{ label: 'Doses', data: vaccineCounts,
            backgroundColor: ['#7c3aed','#0ea5e9','#10b981','#f59e0b','#ef4444','#8b5cf6','#ec4899','#14b8a6'],
            borderRadius: 8, borderSkipped: false }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, grid: { color: '#f3f4f6' }, ticks: { font: { family: 'Poppins', size: 10 } } },
            x: { grid: { display: false }, ticks: { font: { family: 'Poppins', size: 10 } } }
        }
    }
});
</script>

<?php include '../includes/admin_footer.php'; ?>
