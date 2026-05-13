<?php
require_once 'includes/auth_check.php';
checkRole(['parent']);
require_once 'config/config.php';
require_once 'config/database.php';

$db = (new Database())->getConnection();

$pStmt = $db->prepare("SELECT parent_id, full_name, phone FROM parents WHERE user_id=? LIMIT 1");
$pStmt->execute([$_SESSION['user_id']]);
$parent = $pStmt->fetch(PDO::FETCH_ASSOC);
if (!$parent) redirect('/auth/logout.php');
$pid = (int)$parent['parent_id'];

$child_id = (int)($_GET['child_id'] ?? 0);
if (!$child_id) redirect('/children.php');

$cStmt = $db->prepare("SELECT * FROM children WHERE child_id=? AND parent_id=? LIMIT 1");
$cStmt->execute([$child_id, $pid]);
$child = $cStmt->fetch(PDO::FETCH_ASSOC);
if (!$child) redirect('/children.php');

$records = $db->prepare("
    SELECT vr.*, v.vaccine_name, v.doses_required, h.hospital_name, h.city
    FROM vaccination_records vr
    JOIN vaccines v ON vr.vaccine_id=v.vaccine_id
    JOIN hospitals h ON vr.hospital_id=h.hospital_id
    WHERE vr.child_id=?
    ORDER BY vr.administration_date ASC
");
$records->execute([$child_id]);
$records = $records->fetchAll(PDO::FETCH_ASSOC);

$age = (new DateTime())->diff(new DateTime($child['date_of_birth']));
$cert_no = 'VAX-CERT-' . strtoupper(substr(md5($child_id . $child['unique_child_id']), 0, 8));
$issued_date = date('d F Y');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vaccination Certificate — <?= htmlspecialchars($child['full_name']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
        body { background: #f5f3ff; }
        @page { margin: 1cm; }
        @media print {
            body { background: white; }
            .no-print { display: none !important; }
            .cert-page { box-shadow: none !important; border: 2px solid #7c3aed !important; }
        }
        .cert-border {
            border: 3px solid transparent;
            background: linear-gradient(white, white) padding-box,
                        linear-gradient(135deg, #7c3aed, #0ea5e9, #10b981) border-box;
        }
    </style>
</head>
<body>

<!-- Action Bar -->
<div class="no-print" style="background:white;border-bottom:1px solid #e5e7eb;padding:12px 24px;position:sticky;top:0;z-index:100;display:flex;align-items:center;gap:12px;">
    <a href="/children.php" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left me-1"></i>Back</a>
    <span class="fw-semibold text-muted" style="font-size:0.9rem;">Vaccination Certificate — <?= htmlspecialchars($child['full_name']) ?></span>
    <button onclick="window.print()" class="btn btn-primary btn-sm ms-auto"><i class="fas fa-print me-2"></i>Print / Save as PDF</button>
    <a href="/vaccination_history.php" class="btn btn-outline-secondary btn-sm"><i class="fas fa-history me-1"></i>Full History</a>
</div>

<div class="container py-5" style="max-width:820px;">
    <div class="cert-page cert-border" style="background:white;border-radius:24px;overflow:hidden;box-shadow:0 20px 60px rgba(91,33,182,0.15);">

        <!-- Certificate Header -->
        <div style="background:linear-gradient(135deg,#0f0c29 0%,#302b63 50%,#5b21b6 100%);padding:44px;text-align:center;color:white;position:relative;overflow:hidden;">
            <!-- Decorative circles -->
            <div style="position:absolute;width:200px;height:200px;background:rgba(255,255,255,0.04);border-radius:50%;top:-80px;left:-60px;"></div>
            <div style="position:absolute;width:150px;height:150px;background:rgba(255,255,255,0.04);border-radius:50%;bottom:-60px;right:-40px;"></div>

            <div style="position:relative;z-index:1;">
                <!-- Logo -->
                <div style="margin-bottom:20px;">
                    <div style="display:inline-flex;align-items:center;gap:12px;background:rgba(255,255,255,0.1);padding:10px 24px;border-radius:50px;border:1px solid rgba(255,255,255,0.15);backdrop-filter:blur(8px);">
                        <i class="fas fa-syringe" style="font-size:1.2rem;color:#c4b5fd;"></i>
                        <span style="font-size:1.3rem;font-weight:800;letter-spacing:-0.3px;">Vaxora<span style="color:#f59e0b;">.</span></span>
                    </div>
                </div>

                <!-- Seal -->
                <div style="width:80px;height:80px;background:linear-gradient(135deg,#f59e0b,#d97706);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 18px;box-shadow:0 8px 24px rgba(245,158,11,0.4);">
                    <i class="fas fa-award" style="font-size:2rem;color:white;"></i>
                </div>

                <div style="font-size:0.75rem;letter-spacing:3px;color:#c4b5fd;text-transform:uppercase;margin-bottom:8px;">Official Document</div>
                <h1 style="font-size:2rem;font-weight:800;margin-bottom:6px;">Vaccination Certificate</h1>
                <p style="color:#a78bfa;font-size:0.9rem;margin:0;">Issued by Vaxora E-Vaccination Management System, Pakistan</p>

                <div style="margin-top:20px;padding-top:20px;border-top:1px solid rgba(255,255,255,0.1);">
                    <span style="background:rgba(245,158,11,0.2);border:1px solid rgba(245,158,11,0.4);color:#fde68a;padding:6px 18px;border-radius:50px;font-size:0.8rem;font-weight:600;">
                        Certificate No: <?= $cert_no ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- Certificate Body -->
        <div style="padding:44px;">
            <!-- Child Info -->
            <div style="background:#f5f3ff;border-radius:16px;padding:28px;margin-bottom:28px;border:1px solid rgba(124,58,237,0.1);">
                <div style="display:flex;align-items:center;gap:16px;margin-bottom:20px;">
                    <div style="width:64px;height:64px;background:linear-gradient(135deg,#7c3aed,#5b21b6);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1.6rem;flex-shrink:0;">
                        <?= $child['gender']==='female' ? '👧' : '👦' ?>
                    </div>
                    <div>
                        <h4 style="font-weight:800;color:#1e1b4b;margin-bottom:4px;"><?= htmlspecialchars($child['full_name']) ?></h4>
                        <div style="color:#7c3aed;font-size:0.82rem;font-weight:600;">Child ID: <?= htmlspecialchars($child['unique_child_id'] ?? 'VAX-'.$child['child_id']) ?></div>
                    </div>
                </div>
                <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;">
                    <div>
                        <div style="font-size:0.72rem;color:#9ca3af;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:4px;">Date of Birth</div>
                        <div style="font-weight:600;font-size:0.9rem;"><?= date('d F Y', strtotime($child['date_of_birth'])) ?></div>
                    </div>
                    <div>
                        <div style="font-size:0.72rem;color:#9ca3af;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:4px;">Age</div>
                        <div style="font-weight:600;font-size:0.9rem;"><?= $age->y ?> years <?= $age->m ?> months</div>
                    </div>
                    <div>
                        <div style="font-size:0.72rem;color:#9ca3af;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:4px;">Gender</div>
                        <div style="font-weight:600;font-size:0.9rem;"><?= ucfirst($child['gender'] ?? 'N/A') ?></div>
                    </div>
                    <div>
                        <div style="font-size:0.72rem;color:#9ca3af;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:4px;">Blood Group</div>
                        <div style="font-weight:600;font-size:0.9rem;color:#ef4444;"><?= htmlspecialchars($child['blood_group'] ?? 'N/A') ?></div>
                    </div>
                    <div>
                        <div style="font-size:0.72rem;color:#9ca3af;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:4px;">Parent / Guardian</div>
                        <div style="font-weight:600;font-size:0.9rem;"><?= htmlspecialchars($parent['full_name']) ?></div>
                    </div>
                    <div>
                        <div style="font-size:0.72rem;color:#9ca3af;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:4px;">Issue Date</div>
                        <div style="font-weight:600;font-size:0.9rem;"><?= $issued_date ?></div>
                    </div>
                </div>
            </div>

            <!-- Certification Text -->
            <div style="text-align:center;margin-bottom:28px;">
                <p style="font-size:0.95rem;color:#374151;line-height:1.8;max-width:580px;margin:0 auto;">
                    This is to certify that <strong><?= htmlspecialchars($child['full_name']) ?></strong> has received the following vaccines as part of the Expanded Programme on Immunization (EPI), administered at Vaxora partner hospitals in Karachi, Pakistan.
                </p>
            </div>

            <!-- Vaccination Records -->
            <div style="margin-bottom:28px;">
                <h5 style="font-weight:700;color:#1e1b4b;margin-bottom:16px;display:flex;align-items:center;gap:8px;">
                    <span style="width:28px;height:28px;background:linear-gradient(135deg,#7c3aed,#5b21b6);border-radius:8px;display:inline-flex;align-items:center;justify-content:center;">
                        <i class="fas fa-syringe" style="color:white;font-size:0.75rem;"></i>
                    </span>
                    Immunization Records
                </h5>

                <?php if ($records): ?>
                <div style="border:1px solid #e5e7eb;border-radius:12px;overflow:hidden;">
                    <table style="width:100%;border-collapse:collapse;font-size:0.85rem;">
                        <thead>
                            <tr style="background:linear-gradient(135deg,#0f0c29,#302b63);color:white;">
                                <th style="padding:12px 16px;font-weight:600;letter-spacing:0.3px;">#</th>
                                <th style="padding:12px 16px;font-weight:600;letter-spacing:0.3px;">Vaccine</th>
                                <th style="padding:12px 16px;font-weight:600;letter-spacing:0.3px;">Hospital</th>
                                <th style="padding:12px 16px;font-weight:600;letter-spacing:0.3px;">Date Administered</th>
                                <th style="padding:12px 16px;font-weight:600;letter-spacing:0.3px;text-align:center;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($records as $i => $r): ?>
                        <tr style="background:<?= $i%2===0 ? 'white' : '#fafafa' ?>;border-top:1px solid #f3f4f6;">
                            <td style="padding:12px 16px;color:#9ca3af;"><?= $i+1 ?></td>
                            <td style="padding:12px 16px;font-weight:600;color:#1e1b4b;"><?= htmlspecialchars($r['vaccine_name']) ?></td>
                            <td style="padding:12px 16px;color:#6b7280;font-size:0.8rem;"><?= htmlspecialchars($r['hospital_name']) ?>, <?= htmlspecialchars($r['city'] ?? 'Karachi') ?></td>
                            <td style="padding:12px 16px;color:#374151;"><?= date('d M Y', strtotime($r['administration_date'])) ?></td>
                            <td style="padding:12px 16px;text-align:center;">
                                <span style="background:#dcfce7;color:#166534;padding:4px 12px;border-radius:20px;font-size:0.72rem;font-weight:700;display:inline-flex;align-items:center;gap:4px;">
                                    <i class="fas fa-check-circle"></i> Completed
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div style="text-align:center;padding:40px;background:#f9fafb;border-radius:12px;border:2px dashed #d1d5db;">
                    <div style="font-size:2.5rem;margin-bottom:12px;">💉</div>
                    <p style="color:#9ca3af;font-size:0.9rem;margin:0;">No vaccination records found for this child yet.<br>Complete appointments to populate this certificate.</p>
                </div>
                <?php endif; ?>
            </div>

            <!-- Summary Badge -->
            <?php if ($records): ?>
            <div style="background:linear-gradient(135deg,rgba(124,58,237,0.06),rgba(14,165,233,0.06));border:1px solid rgba(124,58,237,0.15);border-radius:12px;padding:20px;display:flex;align-items:center;gap:16px;margin-bottom:28px;">
                <div style="width:48px;height:48px;background:linear-gradient(135deg,#10b981,#059669);border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="fas fa-check-double" style="color:white;font-size:1.1rem;"></i>
                </div>
                <div>
                    <div style="font-weight:700;color:#1e1b4b;">Total: <?= count($records) ?> Vaccination<?= count($records)!=1?'s':'' ?> on Record</div>
                    <div style="font-size:0.82rem;color:#6b7280;">All records verified by Vaxora partner hospitals, Karachi</div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Signatures -->
            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:20px;margin-bottom:28px;">
                <div style="text-align:center;">
                    <div style="height:50px;margin-bottom:8px;display:flex;align-items:flex-end;justify-content:center;">
                        <div style="font-family:cursive;font-size:1.4rem;color:#7c3aed;opacity:0.7;">Dr. Ahmed Raza</div>
                    </div>
                    <div style="border-top:1px solid #e5e7eb;padding-top:8px;">
                        <div style="font-size:0.78rem;font-weight:700;color:#1e1b4b;">Dr. Ahmed Raza</div>
                        <div style="font-size:0.72rem;color:#9ca3af;">Chief Medical Officer</div>
                    </div>
                </div>
                <div style="text-align:center;">
                    <div style="height:50px;margin-bottom:8px;display:flex;align-items:flex-end;justify-content:center;">
                        <div style="width:70px;height:70px;background:linear-gradient(135deg,#7c3aed,#0ea5e9);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto;">
                            <i class="fas fa-shield-alt" style="color:white;font-size:1.5rem;"></i>
                        </div>
                    </div>
                    <div style="border-top:1px solid #e5e7eb;padding-top:8px;">
                        <div style="font-size:0.78rem;font-weight:700;color:#1e1b4b;">Vaxora Official Seal</div>
                        <div style="font-size:0.72rem;color:#9ca3af;">E-Vaccination System, Pakistan</div>
                    </div>
                </div>
                <div style="text-align:center;">
                    <div style="height:50px;margin-bottom:8px;display:flex;align-items:flex-end;justify-content:center;">
                        <div style="font-family:cursive;font-size:1.4rem;color:#0ea5e9;opacity:0.7;">Dr. Sara Khan</div>
                    </div>
                    <div style="border-top:1px solid #e5e7eb;padding-top:8px;">
                        <div style="font-size:0.78rem;font-weight:700;color:#1e1b4b;">Dr. Sara Khan</div>
                        <div style="font-size:0.72rem;color:#9ca3af;">EPI Programme Head</div>
                    </div>
                </div>
            </div>

            <!-- Verification Footer -->
            <div style="background:#0f0c29;border-radius:12px;padding:16px 20px;text-align:center;">
                <div style="font-size:0.75rem;color:#6b7280;margin-bottom:4px;">Verify this certificate at</div>
                <div style="font-size:0.85rem;color:#c4b5fd;font-weight:600;">www.vaxora.pk/verify · Certificate: <?= $cert_no ?></div>
                <div style="font-size:0.72rem;color:#4b4b7a;margin-top:4px;">This is a digitally generated certificate. Valid without physical signature. Issued: <?= $issued_date ?></div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
