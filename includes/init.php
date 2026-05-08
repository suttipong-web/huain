<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

if (isset($_GET['lang']) && in_array($_GET['lang'], ['en', 'th'], true)) {
    $_SESSION['site_lang'] = $_GET['lang'];
}

if (empty($_SESSION['site_lang'])) {
    $_SESSION['site_lang'] = DEFAULT_LANG;
}
