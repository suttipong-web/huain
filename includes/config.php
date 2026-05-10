<?php

date_default_timezone_set('Asia/Bangkok');

$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (isset($_SERVER['SERVER_PORT']) && (int) $_SERVER['SERVER_PORT'] === 443);
$scheme = $isHttps ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';

$documentRoot = rtrim(str_replace('\\', '/', (string) ($_SERVER['DOCUMENT_ROOT'] ?? '')), '/');
$projectRoot = rtrim(str_replace('\\', '/', dirname(__DIR__)), '/');
$basePath = '';

if ($documentRoot !== '' && strpos($projectRoot, $documentRoot) === 0) {
    $relativePath = trim(substr($projectRoot, strlen($documentRoot)), '/');
    $basePath = $relativePath === '' ? '' : '/' . $relativePath;
} else {
    $scriptName = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? ''));
    $segments = array_values(array_filter(explode('/', trim($scriptName, '/'))));
    if (count($segments) >= 2) {
        $basePath = '/' . $segments[0];
    }
}

define('BASE_PATH', $basePath);

if ($host === 'huain-th.local') {
    define('BASE_URL', $scheme . '://huain-th.local' . BASE_PATH . '/');
} elseif ($host === 'huain-th.com' || $host === 'www.huain-th.com') {
    define('BASE_URL', 'https://huain-th.com' . BASE_PATH . '/');
} else {
    define('BASE_URL', $scheme . '://' . $host . BASE_PATH . '/');
}

define('ROOT_PATH', dirname(__DIR__) . DIRECTORY_SEPARATOR);
define('UPLOAD_PATH', ROOT_PATH . 'uploads' . DIRECTORY_SEPARATOR);
define('UPLOAD_URL', BASE_URL . 'uploads/');

if($_SERVER['HTTP_HOST'] === 'huain-th.local' || $_SERVER['HTTP_HOST'] === 'localhost') {
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'huain');
    define('DB_USER', 'root');
    define('DB_PASS', '12345678');
  
} else {
    define('DB_HOST', getenv('HUAIN_DB_HOST') ?: 'localhost');
    define('DB_NAME', getenv('HUAIN_DB_NAME') ?: 'mumpraco_huain');
    define('DB_USER', getenv('HUAIN_DB_USER') ?: 'mumpraco_huain');
    define('DB_PASS', getenv('HUAIN_DB_PASS') ?: ''); 
}


define('SITE_NAME', 'HUAIN Thailand');
define('DEFAULT_LANG', 'en');
