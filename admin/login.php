<?php
require_once __DIR__ . '/../includes/init.php';

if (isAdminLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login | HUAIN Thailand</title>
    <link rel="icon" type="image/svg+xml" href="<?= e(baseUrl('favicon.svg')) ?>">
    <link rel="icon" type="image/x-icon" href="<?= e(baseUrl('favicon.ico')) ?>">
    <link rel="stylesheet" href="<?= e(baseUrl('assets/css/admin.css')) ?>">
</head>
<body>
<div class="login-wrap">
    <div class="panel login-box">
        <h2>Admin Login</h2>
        <p><small>Default account is provided in huain_db.txt</small></p>
        <?php if (!empty($_GET['error'])): ?>
            <div class="alert alert-error">Login failed. Please check username/password.</div>
        <?php endif; ?>
        <form action="login-process.php" method="post">
            <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
            <div style="margin-bottom:0.7rem;">
                <label>Username</label>
                <input type="text" name="username" required>
            </div>
            <div style="margin-bottom:0.9rem;">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <button class="btn btn-primary" type="submit">Login</button>
            <a class="btn btn-muted" href="<?= e(baseUrl('index.php')) ?>">Back to site</a>
        </form>
    </div>
</div>
</body>
</html>
