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

if (empty($_SESSION['contact_captcha']) || !is_array($_SESSION['contact_captcha'])) {
    $_SESSION['contact_captcha'] = [];
}

if (empty($_SESSION['contact_captcha']['question']) || empty($_SESSION['contact_captcha']['answer'])) {
    $firstNumber = random_int(2, 9);
    $secondNumber = random_int(1, 9);

    $_SESSION['contact_captcha'] = [
        'question' => $firstNumber . ' + ' . $secondNumber,
        'answer' => (string) ($firstNumber + $secondNumber),
    ];
}

$captchaQuestion = $_SESSION['contact_captcha']['question'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid request token.';
    } else {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $subject = trim($_POST['subject'] ?? '');
        $message = trim($_POST['message'] ?? '');
        $captchaAnswer = trim($_POST['captcha_answer'] ?? '');

        if ($name === '' || $email === '' || $message === '') {
            $error = 'Please complete required fields.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Please provide a valid email.';
        } elseif ($captchaAnswer === '' || !hash_equals((string) ($_SESSION['contact_captcha']['answer'] ?? ''), $captchaAnswer)) {
            $error = 'Captcha code is incorrect. Please try again.';
        } else {
            $stmt = $pdo->prepare('INSERT INTO contact_messages (name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)');
            $stmt->execute([$name, $email, $phone, $subject, $message]);
            $success = true;

            $firstNumber = random_int(2, 9);
            $secondNumber = random_int(1, 9);

            $_SESSION['contact_captcha'] = [
                'question' => $firstNumber . ' + ' . $secondNumber,
                'answer' => (string) ($firstNumber + $secondNumber),
            ];
            $captchaQuestion = $_SESSION['contact_captcha']['question'];
        }
    }
}

$contactStmt = $pdo->query('SELECT * FROM contact_information ORDER BY id ASC LIMIT 1');
$contactInfo = $contactStmt->fetch() ?: [];

$companyName = trim((string) ($contactInfo['company_name'] ?? 'HUAIN Thailand'));
$address = trim((string) ($contactInfo['address'] ?? ''));
$phone = trim((string) ($contactInfo['phone'] ?? ''));
$email = trim((string) ($contactInfo['email'] ?? ''));
$lineId = trim((string) ($contactInfo['line_id'] ?? ''));

$contactLinks = [];
if ($phone !== '') {
    $contactLinks[] = ['label' => 'Phone', 'value' => $phone, 'href' => 'tel:' . preg_replace('/[^0-9+]/', '', $phone)];
}
if ($email !== '') {
    $contactLinks[] = ['label' => 'Email', 'value' => $email, 'href' => 'mailto:' . $email];
}
if ($lineId !== '') {
    $contactLinks[] = ['label' => 'Line', 'value' => $lineId, 'href' => 'https://line.me/R/ti/p/~' . rawurlencode($lineId)];
}

include __DIR__ . '/includes/site-header.php';
?>

<section class="contact-page section-block">
    <div class="container">
        <div class="contact-hero">
            <div class="contact-hero-copy">
                <span class="contact-kicker">Contact Center</span>
                <h1>Talk to HUAIN Thailand</h1>
                <p>Request product advice, AV consultation, or project support through a clear and easy-to-read contact form.</p>
                <div class="contact-hero-pills">
                    <span>Fast response</span>
                    <span>Secure submission</span>
                    <span>Captcha protected</span>
                </div>
            </div>

            <div class="contact-hero-card">
                <div class="contact-hero-card-label">Office</div>
                <h2><?= e($companyName) ?></h2>
                <?php if ($address !== ''): ?>
                    <p><?= e($address) ?></p>
                <?php endif; ?>
                <div class="contact-mini-list">
                    <?php if ($phone !== ''): ?>
                        <a href="tel:<?= e(preg_replace('/[^0-9+]/', '', $phone)) ?>"><?= e($phone) ?></a>
                    <?php endif; ?>
                    <?php if ($email !== ''): ?>
                        <a href="mailto:<?= e($email) ?>"><?= e($email) ?></a>
                    <?php endif; ?>
                    <?php if ($lineId !== ''): ?>
                        <a href="https://line.me/R/ti/p/~<?= e(rawurlencode($lineId)) ?>" target="_blank" rel="noopener"><?= e($lineId) ?></a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="contact-grid-layout">
            <aside class="contact-info-panel">
                <div class="contact-info-head">
                    <span class="contact-section-tag">Reach us</span>
                    <h3>Office Information</h3>
                    <p>Use the details below if you prefer direct contact instead of the form.</p>
                </div>

                <div class="contact-info-card-list">
                    <?php foreach ($contactLinks as $link): ?>
                        <a class="contact-info-card" href="<?= e($link['href']) ?>"<?php if (strpos($link['href'], 'http') === 0) echo ' target="_blank" rel="noopener"'; ?>>
                            <span><?= e($link['label']) ?></span>
                            <strong><?= e($link['value']) ?></strong>
                        </a>
                    <?php endforeach; ?>
                    <?php if ($address !== ''): ?>
                        <div class="contact-info-card contact-info-card-static">
                            <span>Address</span>
                            <strong><?= e($address) ?></strong>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="contact-note">
                    <strong>Response window</strong>
                    <p>We review messages as quickly as possible during business hours.</p>
                </div>
            </aside>

            <div class="contact-form-panel">
                <div class="contact-form-head">
                    <span class="contact-section-tag">Send a message</span>
                    <h3>Tell us what you need</h3>
                    <p>Fill in the details below. Required fields are marked with an asterisk.</p>
                </div>

                <?php if ($success): ?>
                    <div class="alert alert-success">Thank you. Your message has been sent successfully.</div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= e($error) ?></div>
                <?php endif; ?>

                <form method="post" class="contact-form">
                    <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Name *</label>
                            <input type="text" name="name" class="form-control" value="<?= e($_POST['name'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email *</label>
                            <input type="email" name="email" class="form-control" value="<?= e($_POST['email'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control" value="<?= e($_POST['phone'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Subject</label>
                            <input type="text" name="subject" class="form-control" value="<?= e($_POST['subject'] ?? '') ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Message *</label>
                            <textarea name="message" class="form-control" rows="6" required><?= e($_POST['message'] ?? '') ?></textarea>
                        </div>
                        <div class="col-12">
                            <div class="captcha-box">
                                <div>
                                    <label class="form-label mb-1">Captcha *</label>
                                    <p class="captcha-question">What is <?= e($captchaQuestion) ?>?</p>
                                </div>
                                <input type="text" name="captcha_answer" class="form-control captcha-input" inputmode="numeric" autocomplete="off" required>
                            </div>
                        </div>
                        <div class="col-12 d-flex flex-wrap gap-3 align-items-center">
                            <button class="btn-gold" type="submit">Send Message</button>
                            <span class="contact-submit-note">Protected by captcha and CSRF validation.</span>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/site-footer.php';
