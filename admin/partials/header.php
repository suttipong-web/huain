<?php
$adminPage = $adminPage ?? 'dashboard';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin | HUAIN Thailand</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
<div class="admin-shell">
    <aside class="admin-side">
        <h4>HUAIN Admin</h4>
        <a class="<?= $adminPage === 'dashboard' ? 'active' : '' ?>" href="dashboard.php">Dashboard</a>
        <a class="<?= $adminPage === 'banners' ? 'active' : '' ?>" href="banners.php">Banners</a>
        <a class="<?= $adminPage === 'categories' ? 'active' : '' ?>" href="categories.php">Categories</a>
        <a class="<?= $adminPage === 'products' ? 'active' : '' ?>" href="products.php">Products</a>
        <a class="<?= $adminPage === 'news' ? 'active' : '' ?>" href="news.php">News</a>
        <a class="<?= $adminPage === 'contacts' ? 'active' : '' ?>" href="contacts.php">Contacts</a>
        <hr style="margin: 0.5rem 0; border: none; border-top: 1px solid #ddd;">
        <a class="<?= $adminPage === 'change-password' ? 'active' : '' ?>" href="change-password.php">Change Password</a>
        <a href="logout.php">Logout</a>
    </aside>
    <main class="admin-main">
