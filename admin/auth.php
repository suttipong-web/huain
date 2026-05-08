<?php
require_once __DIR__ . '/../includes/init.php';

if (!isAdminLoggedIn()) {
    header('Location: login.php');
    exit;
}
