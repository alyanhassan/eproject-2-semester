<?php
require_once 'includes/auth_check.php';
checkRole(['parent']);
require_once 'config/config.php';
require_once 'config/database.php';

$db = (new Database())->getConnection();
$error = ''; $success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = sanitizeInput($_POST['name'] ?? '');
    $email   = sanitizeInput($_POST['email'] ?? '');
    $subject = sanitizeInput($_POST['subject'] ?? '');
    $message = sanitizeInput($_POST['message'] ?? '');

    if (empty($name) || empty($email) || empty($message)) {
        $error = 'Name, email, and message are required.';
    } else {
        try {
            $stmt = $db->prepare("INSERT INTO contact_messages (name,email,subject,message) VALUES (?,?,?,?)");
            $stmt->execute([$name, $email, $subject, $message]);
            $success = 'Your message has been sent! We will get back to you soon.';
        } catch (PDOException $e) {
            $error = 'Failed to send message. Please try again.';
        }
    }
}
include 'includes/header.php';
?>
<div class="container py-4" style="max-width:680px;">
    <div class="mb-4">
        <h3 class="fw-bold mb-0">Contact Us</h3>
        <p class="text-muted">Have a question or need support? We're here to help.</p>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card text-center p-3">
                <i class="fas fa-envelope fa-2x text-primary mb-2"></i>
                <div class="fw-semibold small">Email</div>
                <div class="text-muted small">admin@evaccination.com</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center p-3">
                <i class="fas fa-clock fa-2x text-success mb-2"></i>
                <div class="fw-semibold small">Hours</div>
                <div class="text-muted small">Mon–Fri, 9AM–5PM</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center p-3">
                <i class="fas fa-map-marker-alt fa-2x text-danger mb-2"></i>
                <div class="fw-semibold small">Location</div>
                <div class="text-muted small">Karachi, Pakistan</div>
            </div>
        </div>
    </div>

    <?php if ($success): ?><div class="alert alert-success"><i class="fas fa-check-circle me-1"></i><?= htmlspecialchars($success) ?></div><?php endif; ?>
    <?php if ($error):   ?><div class="alert alert-danger"><i class="fas fa-exclamation-circle me-1"></i><?= htmlspecialchars($error) ?></div><?php endif; ?>

    <div class="card">
        <div class="card-body p-4">
            <form method="POST">
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Your Name *</label>
                        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email Address *</label>
                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($_POST['email'] ?? $_SESSION['email'] ?? '') ?>" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Subject</label>
                    <input type="text" name="subject" class="form-control" value="<?= htmlspecialchars($_POST['subject'] ?? '') ?>">
                </div>
                <div class="mb-4">
                    <label class="form-label">Message *</label>
                    <textarea name="message" class="form-control" rows="5" required placeholder="Describe your question or issue..."><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
                </div>
                <button type="submit" class="btn btn-primary px-4"><i class="fas fa-paper-plane me-2"></i>Send Message</button>
            </form>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>
