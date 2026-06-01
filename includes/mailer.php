<?php
// ============================================================
// VAXORA — Email Helper (PHPMailer)
// ============================================================
// SETUP INSTRUCTIONS:
// 1. Run `composer install` in the vaxora-php/ folder to install PHPMailer
//    OR manually download from https://github.com/PHPMailer/PHPMailer
//    and place files in vaxora-php/vendor/phpmailer/phpmailer/src/
//
// 2. Set MAIL_ENABLED to true and fill in your SMTP credentials below
//
// Gmail SMTP:
//   - MAIL_HOST     = smtp.gmail.com
//   - MAIL_PORT     = 587
//   - MAIL_USERNAME = your Gmail address
//   - MAIL_PASSWORD = App Password (not your Gmail password)
//     → Enable 2FA on Gmail → Google Account → Security → App Passwords
// ============================================================

define('MAIL_HOST',      'smtp.gmail.com');
define('MAIL_PORT',      587);
define('MAIL_USERNAME',  'alyanhassan2009@gmail.com');   // <-- change this
define('MAIL_PASSWORD',  'pczylfgkxvlzzvuu');       // <-- change this
define('MAIL_FROM',      'noreply@vaxora.pk');
define('MAIL_FROM_NAME', 'VAXORA Platform');
define('MAIL_ENABLED',   true); // set to true after configuring above


/**
 * Send an HTML email using PHPMailer (or PHP's mail() as fallback)
 */
function sendVaxoraEmail(string $to, string $toName, string $subject, string $htmlBody): bool {
    if (!MAIL_ENABLED) return true; // silent no-op when disabled

    $vendorAutoload = dirname(__DIR__) . '/vendor/autoload.php';

    if (file_exists($vendorAutoload)) {
        require_once $vendorAutoload;
        try {
            $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
            $mail->isSMTP();
            $mail->Host       = MAIL_HOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = MAIL_USERNAME;
            $mail->Password   = MAIL_PASSWORD;
            $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = MAIL_PORT;
            $mail->CharSet    = 'UTF-8';
            $mail->setFrom(MAIL_FROM, MAIL_FROM_NAME);
            $mail->addAddress($to, $toName);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $htmlBody;
            $mail->AltBody = wordwrap(strip_tags($htmlBody), 70);
            $mail->send();
            return true;
        } catch (\PHPMailer\PHPMailer\Exception $e) {
            error_log('VAXORA PHPMailer Error: ' . $e->getMessage());
            return false;
        }
    }

    // Fallback: PHP built-in mail()
    $headers  = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";
    $headers .= "From: " . MAIL_FROM_NAME . " <" . MAIL_FROM . ">\r\n";
    return mail($to, $subject, $htmlBody, $headers);
}


/**
 * Send appointment status notification to parent
 */
function sendAppointmentStatusEmail(string $parentEmail, string $parentName, string $newStatus, array $appt): bool {
    $isApproved  = $newStatus === 'approved';
    $statusLabel = ucfirst($newStatus);
    $statusColor = $isApproved ? '#90E300' : '#e53e3e';
    $statusBg    = $isApproved ? '#f0ffe6'  : '#fff5f5';
    $msgLine     = $isApproved
        ? 'Your appointment has been <strong>approved</strong>. Please arrive at the hospital on time on the scheduled date.'
        : 'Unfortunately your appointment request has been <strong>rejected</strong>. You can book a new appointment from your dashboard.';

    $html = '<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"></head>
<body style="margin:0;padding:0;background:#F8FAF2;font-family:Arial,Helvetica,sans-serif">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#F8FAF2;padding:40px 20px">
<tr><td align="center">
<table width="560" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 8px 32px rgba(28,39,6,0.1);max-width:560px;width:100%">

  <!-- Header -->
  <tr><td style="background:#1C2706;padding:32px;text-align:center">
    <div style="font-size:26px;font-weight:800;color:#90E300;letter-spacing:-0.5px">VAXORA</div>
    <div style="color:rgba(255,255,255,0.55);font-size:13px;margin-top:4px">Pakistan\'s Vaccination Platform</div>
  </td></tr>

  <!-- Status Banner -->
  <tr><td style="padding:28px 36px 0">
    <div style="background:' . $statusBg . ';border-left:4px solid ' . $statusColor . ';padding:14px 18px;border-radius:8px">
      <div style="font-weight:700;font-size:15px;color:#1C2706">Appointment ' . $statusLabel . '</div>
      <div style="font-size:13px;color:#555;margin-top:5px">' . $msgLine . '</div>
    </div>
  </td></tr>

  <!-- Body -->
  <tr><td style="padding:24px 36px 32px">
    <p style="font-size:14px;color:#333;margin:0 0 20px">Dear <strong>' . htmlspecialchars($parentName) . '</strong>,</p>

    <table width="100%" cellpadding="0" cellspacing="0" style="font-size:13px;border-collapse:collapse">
      <tr style="border-bottom:1px solid #D5DEC5">
        <td style="padding:10px 0;color:#888;width:40%">Child</td>
        <td style="padding:10px 0;font-weight:600;color:#1C2706">' . htmlspecialchars($appt['child_name']) . '</td>
      </tr>
      <tr style="border-bottom:1px solid #D5DEC5">
        <td style="padding:10px 0;color:#888">Vaccine</td>
        <td style="padding:10px 0;font-weight:600;color:#1C2706">' . htmlspecialchars($appt['vaccine_name']) . '</td>
      </tr>
      <tr style="border-bottom:1px solid #D5DEC5">
        <td style="padding:10px 0;color:#888">Hospital</td>
        <td style="padding:10px 0;font-weight:600;color:#1C2706">' . htmlspecialchars($appt['hospital_name']) . '</td>
      </tr>
      <tr style="border-bottom:1px solid #D5DEC5">
        <td style="padding:10px 0;color:#888">Date</td>
        <td style="padding:10px 0;font-weight:600;color:#1C2706">' . date('d F Y', strtotime($appt['appointment_date'])) . '</td>
      </tr>
      ' . ($appt['appointment_time'] ? '
      <tr>
        <td style="padding:10px 0;color:#888">Time</td>
        <td style="padding:10px 0;font-weight:600;color:#1C2706">' . date('h:i A', strtotime($appt['appointment_time'])) . '</td>
      </tr>' : '') . '
    </table>

    ' . ($isApproved ? '
    <div style="background:#1C2706;border-radius:10px;padding:16px 20px;margin-top:24px">
      <div style="color:#90E300;font-weight:700;font-size:13px;margin-bottom:6px">Reminder</div>
      <div style="color:rgba(255,255,255,0.75);font-size:12px;line-height:1.7">
        Please bring this confirmation along with your child\'s vaccination card.<br>
        Arrive 10 minutes before your scheduled time.
      </div>
    </div>' : '') . '
  </td></tr>

  <!-- Footer -->
  <tr><td style="background:#f8faf2;padding:20px 36px;border-top:1px solid #D5DEC5;text-align:center">
    <p style="color:#aaa;font-size:11px;margin:0">VAXORA &mdash; Protecting Lives, One Vaccine at a Time</p>
    <p style="color:#bbb;font-size:11px;margin:6px 0 0">hello@vaxora.pk &nbsp;&bull;&nbsp; 0800-VAXORA</p>
  </td></tr>

</table>
</td></tr>
</table>
</body>
</html>';

    $subject = 'VAXORA — Your appointment has been ' . $statusLabel;
    return sendVaxoraEmail($parentEmail, $parentName, $subject, $html);
}
