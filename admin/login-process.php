<?php

session_start();
require '../includes/config.php';
require '../includes/db.php';
require '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
    header('Location: login.php?error=token');
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = (string) ($_POST['password'] ?? '');

$stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ? LIMIT 1");
$stmt->execute([$username]);
$user = $stmt->fetch();

if ($user && password_verify($password, $user['password'])) {

    session_regenerate_id(true);

    $_SESSION['admin_id'] = $user['id'];
    $_SESSION['admin_name'] = $user['name'];

    header('Location: dashboard.php');
    exit;
}

header('Location: login.php?error=1');
exit;