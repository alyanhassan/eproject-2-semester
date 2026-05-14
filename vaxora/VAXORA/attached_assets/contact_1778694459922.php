<?php
require_once 'includes/auth_check.php';
checkRole(['parent']);

require_once 'config/config.php';
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

$success = "";
$error = "";

/* =========================
   HANDLE FORM SUBMIT
========================= */
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);

    if (!empty($name) && !empty($email) && !empty($message)) {

        try {

            $query = "INSERT INTO contact_messages 
                      (name, email, subject, message, created_at) 
                      VALUES (:name, :email, :subject, :message, NOW())";

            $stmt = $db->prepare($query);

            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':subject', $subject);
            $stmt->bindParam(':message', $message);

            if ($stmt->execute()) {
                $success = "Your message has been sent successfully!";
            } else {
                $error = "Failed to send message. Try again.";
            }

        } catch (PDOException $e) {
            error_log($e->getMessage());
            $error = "Something went wrong!";
        }

    } else {
        $error = "Please fill all required fields.";
    }
}

include 'includes/header.php';
?>

<div class="container py-5">

    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="card shadow">

                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Contact Us</h4>
                </div>

                <div class="card-body">

                    <?php if ($success): ?>
                        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                    <?php endif; ?>

                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>

                    <form method="POST">

                        <div class="mb-3">
                            <label class="form-label">Name *</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email *</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Subject</label>
                            <input type="text" name="subject" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Message *</label>
                            <textarea name="message" rows="5" class="form-control" required></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            Send Message
                        </button>

                    </form>

                </div>

            </div>

        </div>
    </div>

</div>

<?php include 'includes/footer.php'; ?>