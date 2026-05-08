<?php
require_once __DIR__ . '/auth.php';

$adminPage = 'dashboard';

$productCount = (int) $pdo->query('SELECT COUNT(*) FROM products')->fetchColumn();
$categoryCount = (int) $pdo->query('SELECT COUNT(*) FROM product_categories')->fetchColumn();
$newsCount = (int) $pdo->query('SELECT COUNT(*) FROM news')->fetchColumn();
$bannerCount = (int) $pdo->query('SELECT COUNT(*) FROM banners')->fetchColumn();
$messageCount = (int) $pdo->query('SELECT COUNT(*) FROM contact_messages')->fetchColumn();

include __DIR__ . '/partials/header.php';
?>

<div class="panel">
    <h2>Dashboard</h2>
    <p>Welcome, <?= e($_SESSION['admin_name'] ?? 'Admin') ?>.</p>
</div>

<div class="grid-2">
    <div class="panel">
        <h3><?= $productCount ?></h3>
        <small>Products</small>
    </div>
    <div class="panel">
        <h3><?= $categoryCount ?></h3>
        <small>Categories</small>
    </div>
    <div class="panel">
        <h3><?= $bannerCount ?></h3>
        <small>Banners</small>
    </div>
    <div class="panel">
        <h3><?= $newsCount ?></h3>
        <small>News items</small>
    </div>
    <div class="panel">
        <h3><?= $messageCount ?></h3>
        <small>Contact messages</small>
    </div>
</div>

<?php include __DIR__ . '/partials/footer.php';
