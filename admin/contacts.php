<?php
require_once __DIR__ . '/auth.php';

$adminPage = 'contacts';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        header('Location: contacts.php?error=token');
        exit;
    }

    $id = (int) ($_POST['id'] ?? 0);
    $companyName = trim($_POST['company_name'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $lineId = trim($_POST['line_id'] ?? '');
    $googleMap = trim($_POST['google_map'] ?? '');
    $facebook = trim($_POST['facebook'] ?? '');
    $youtube = trim($_POST['youtube'] ?? '');

    if ($id > 0) {
        $update = $pdo->prepare('UPDATE contact_information SET company_name = ?, address = ?, phone = ?, email = ?, line_id = ?, google_map = ?, facebook = ?, youtube = ? WHERE id = ?');
        $update->execute([$companyName, $address, $phone, $email, $lineId, $googleMap, $facebook, $youtube, $id]);
    } else {
        $insert = $pdo->prepare('INSERT INTO contact_information (company_name, address, phone, email, line_id, google_map, facebook, youtube) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
        $insert->execute([$companyName, $address, $phone, $email, $lineId, $googleMap, $facebook, $youtube]);
    }

    header('Location: contacts.php?success=saved');
    exit;
}

$contactInfo = $pdo->query('SELECT * FROM contact_information ORDER BY id ASC LIMIT 1')->fetch();
$messages = $pdo->query('SELECT * FROM contact_messages ORDER BY created_at DESC')->fetchAll();

include __DIR__ . '/partials/header.php';
?>

<div class="panel">
    <h2>Contact Information</h2>

    <?php if (!empty($_GET['success'])): ?>
        <div class="alert alert-success">Saved successfully.</div>
    <?php endif; ?>

    <form method="post">
        <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
        <input type="hidden" name="id" value="<?= (int) ($contactInfo['id'] ?? 0) ?>">

        <div class="grid-2">
            <div>
                <label>Company Name</label>
                <input type="text" name="company_name" value="<?= e($contactInfo['company_name'] ?? '') ?>">
            </div>
            <div>
                <label>Phone</label>
                <input type="text" name="phone" value="<?= e($contactInfo['phone'] ?? '') ?>">
            </div>
            <div>
                <label>Email</label>
                <input type="email" name="email" value="<?= e($contactInfo['email'] ?? '') ?>">
            </div>
            <div>
                <label>Line ID</label>
                <input type="text" name="line_id" value="<?= e($contactInfo['line_id'] ?? '') ?>">
            </div>
            <div>
                <label>Facebook</label>
                <input type="text" name="facebook" value="<?= e($contactInfo['facebook'] ?? '') ?>">
            </div>
            <div>
                <label>YouTube</label>
                <input type="text" name="youtube" value="<?= e($contactInfo['youtube'] ?? '') ?>">
            </div>
            <div style="grid-column: span 2;">
                <label>Address</label>
                <textarea name="address"><?= e($contactInfo['address'] ?? '') ?></textarea>
            </div>
            <div style="grid-column: span 2;">
                <label>Google Map Embed</label>
                <textarea name="google_map"><?= e($contactInfo['google_map'] ?? '') ?></textarea>
            </div>
        </div>

        <div style="margin-top:0.8rem;">
            <button class="btn btn-primary" type="submit">Save Contact Information</button>
        </div>
    </form>
</div>

<div class="panel">
    <h3>Contact Messages</h3>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Subject</th>
                <th>Message</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($messages as $msg): ?>
                <tr>
                    <td><?= e($msg['created_at']) ?></td>
                    <td><?= e($msg['name']) ?></td>
                    <td><?= e($msg['email']) ?></td>
                    <td><?= e($msg['phone']) ?></td>
                    <td><?= e($msg['subject']) ?></td>
                    <td><?= e($msg['message']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/partials/footer.php';
