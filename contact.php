<?php
require_once __DIR__ . '/includes/init.php';

$pageKey = 'contact';
$siteTitle = SITE_NAME . ' | Contact';
$metaDescription = 'Contact HUAIN Thailand for professional AV consultation, product information, and project support.';
$canonicalUrl = contactUrl();
$lang = currentLang();
$isTh = $lang === 'th';

$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid request token.';
    } else {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $subject = trim($_POST['subject'] ?? '');
        $message = trim($_POST['message'] ?? '');

        if ($name === '' || $email === '' || $message === '') {
            $error = 'Please complete required fields.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Please provide a valid email.';
        } else {
            $stmt = $pdo->prepare('INSERT INTO contact_messages (name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)');
            $stmt->execute([$name, $email, $phone, $subject, $message]);
            $success = true;
        }
    }
}

$contactStmt = $pdo->query('SELECT * FROM contact_information ORDER BY id ASC LIMIT 1');
$contactInfo = $contactStmt->fetch();

include __DIR__ . '/includes/site-header.php';
?>

<section class="section-block">
    <div class="container">
        <div class="section-head">
            <div>
                <h2>Contact HUAIN Thailand</h2>
                <p>Consultation for AV solution and enterprise projects</p>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-5">
                <div class="side-panel h-100">
                    <h4 class="mb-3">Office Information</h4>
                    <p><strong>Company:</strong> <?= e($contactInfo['company_name'] ?? 'HUAIN Thailand') ?></p>
                    <p><strong>Address:</strong> <?= e($contactInfo['address'] ?? '-') ?></p>
                    <p><strong>Phone:</strong> <?= e($contactInfo['phone'] ?? '-') ?></p>
                    <p><strong>Email:</strong> <?= e($contactInfo['email'] ?? '-') ?></p>
                    <p><strong>Line:</strong> <?= e($contactInfo['line_id'] ?? '-') ?></p>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="form-shell">
                    <?php if ($success): ?>
                        <div class="alert alert-success">Thank you. Your message has been sent successfully.</div>
                    <?php endif; ?>
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?= e($error) ?></div>
                    <?php endif; ?>

                    <form method="post">
                        <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Name *</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email *</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone</label>
                                <input type="text" name="phone" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Subject</label>
                                <input type="text" name="subject" class="form-control">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Message *</label>
                                <textarea name="message" class="form-control" rows="6" required></textarea>
                            </div>
                            <div class="col-12">
                                <button class="btn-gold" type="submit">Send Message</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/site-footer.php';
