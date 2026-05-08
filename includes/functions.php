<?php

function clean($data)
{
    return htmlspecialchars(trim((string) $data), ENT_QUOTES, 'UTF-8');
}

function generateSlug($text)
{
    $text = strtolower((string) $text);
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    return trim($text, '-');
}

function csrfToken()
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function verifyCsrfToken($token)
{
    if (empty($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }

    return hash_equals($_SESSION['csrf_token'], $token);
}

function baseUrl($path = '')
{
    return rtrim(BASE_URL, '/') . '/' . ltrim($path, '/');
}

function appPath($path = '')
{
    return '/' . ltrim($path, '/');
}

function productsUrl()
{
    return baseUrl('products');
}

function productUrl($slug)
{
    return baseUrl('products/' . rawurlencode((string) $slug));
}

function newsListUrl()
{
    return baseUrl('news');
}

function newsUrl($slug)
{
    return baseUrl('news/' . rawurlencode((string) $slug));
}

function contactUrl()
{
    return baseUrl('contact');
}

function uploadUrl($fileName = '')
{
    return rtrim(UPLOAD_URL, '/') . '/' . ltrim($fileName, '/');
}

function redirectTo($path)
{
    header('Location: ' . baseUrl($path));
    exit;
}

function redirectToPath($path)
{
    header('Location: ' . appPath($path));
    exit;
}

function currentLang()
{
    return $_SESSION['site_lang'] ?? DEFAULT_LANG;
}

function langField($prefix)
{
    return $prefix . '_' . currentLang();
}

function parseStatus($value)
{
    return ((int) $value === 1) ? 1 : 0;
}

function parseFloatInput($value)
{
    $value = str_replace(',', '', (string) $value);
    return is_numeric($value) ? (float) $value : 0;
}

function formatPrice($price)
{
    return number_format((float) $price, 2);
}

function truncateText($text, $length = 130)
{
    $text = trim(strip_tags((string) $text));
    if (mb_strlen($text) <= $length) {
        return $text;
    }

    return mb_substr($text, 0, $length - 3) . '...';
}

function saveUpload($fieldName, array $allowedExtensions, $maxSize = 5242880)
{
    if (empty($_FILES[$fieldName]) || $_FILES[$fieldName]['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    $file = $_FILES[$fieldName];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    if ($file['size'] > $maxSize) {
        return null;
    }

    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, $allowedExtensions, true)) {
        return null;
    }

    if (!is_dir(UPLOAD_PATH)) {
        mkdir(UPLOAD_PATH, 0755, true);
    }

    $newName = date('YmdHis') . '_' . bin2hex(random_bytes(6)) . '.' . $extension;
    $destination = UPLOAD_PATH . $newName;

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        return null;
    }

    return $newName;
}

function removeUpload($fileName)
{
    if (!$fileName) {
        return;
    }

    $path = UPLOAD_PATH . $fileName;
    if (is_file($path)) {
        unlink($path);
    }
}

function isAdminLoggedIn()
{
    return !empty($_SESSION['admin_id']);
}

function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function absoluteUrl($path)
{
    if (preg_match('/^https?:\/\//i', (string) $path)) {
        return (string) $path;
    }

    return baseUrl(ltrim((string) $path, '/'));
}

function currentUrl()
{
    $requestUri = $_SERVER['REQUEST_URI'] ?? '/';
    return absoluteUrl(ltrim($requestUri, '/'));
}

function seoDescription($text, $maxLength = 160)
{
    return truncateText((string) $text, (int) $maxLength);
}

function normalizeInternalLink($value)
{
    $value = trim((string) $value);
    if ($value === '') {
        return '';
    }

    if (preg_match('/^https?:\/\//i', $value)) {
        return $value;
    }

    if (preg_match('/^(product\.php\?slug=)([^&]+)$/i', $value, $matches)) {
        return productUrl(urldecode($matches[2]));
    }

    if (preg_match('/^(news-detail\.php\?slug=)([^&]+)$/i', $value, $matches)) {
        return newsUrl(urldecode($matches[2]));
    }

    if ($value === 'products.php' || $value === 'products') {
        return productsUrl();
    }

    if ($value === 'news.php' || $value === 'news') {
        return newsListUrl();
    }

    if ($value === 'contact.php' || $value === 'contact') {
        return contactUrl();
    }

    if ($value === 'index.php' || $value === '/') {
        return baseUrl('');
    }

    return absoluteUrl($value);
}

function languageUrl($lang)
{
    $allowed = ['en', 'th'];
    if (!in_array($lang, $allowed, true)) {
        $lang = DEFAULT_LANG;
    }

    $path = strtok($_SERVER['REQUEST_URI'] ?? '/', '?');
    $params = $_GET;
    $params['lang'] = $lang;
    $query = http_build_query($params);

    return $path . ($query ? ('?' . $query) : '');
}