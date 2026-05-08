<?php

date_default_timezone_set('Asia/Bangkok');

$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (isset($_SERVER['SERVER_PORT']) && (int) $_SERVER['SERVER_PORT'] === 443);
$scheme = $isHttps ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';

if ($host === 'huain-th.local') {
    define('BASE_URL', $scheme . '://huain-th.local/');
} else {
    define('BASE_URL', 'https://huain-th.com/');
}

define('ROOT_PATH', dirname(__DIR__) . DIRECTORY_SEPARATOR);
define('UPLOAD_PATH', ROOT_PATH . 'uploads' . DIRECTORY_SEPARATOR);
define('UPLOAD_URL', BASE_URL . 'uploads/');

define('DB_HOST', getenv('HUAIN_DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('HUAIN_DB_NAME') ?: 'huain');
define('DB_USER', getenv('HUAIN_DB_USER') ?: 'root');
define('DB_PASS', getenv('HUAIN_DB_PASS') ?: '');

define('SITE_NAME', 'HUAIN Thailand');
define('DEFAULT_LANG', 'en');
