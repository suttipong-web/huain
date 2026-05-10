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
    return baseUrl('products.php');
}

function productUrl($slug)
{
    return baseUrl('product.php?slug=' . rawurlencode((string) $slug));
}

function newsListUrl()
{
    return baseUrl('news.php');
}

function newsUrl($slug)
{
    return baseUrl('news-detail.php?slug=' . rawurlencode((string) $slug));
}

function contactUrl()
{
    return baseUrl('contact.php');
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

function sanitizeRichText($html)
{
    $html = trim((string) $html);
    if ($html === '') {
        return '';
    }

    if (!class_exists('DOMDocument')) {
        return strip_tags($html, '<p><br><strong><b><em><i><u><h1><h2><h3><h4><h5><h6><ul><ol><li><table><thead><tbody><tfoot><tr><th><td><a>');
    }

    $allowedTags = [
        'p', 'br', 'strong', 'b', 'em', 'i', 'u',
        'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
        'ul', 'ol', 'li',
        'table', 'thead', 'tbody', 'tfoot', 'tr', 'th', 'td',
        'a',
    ];
    $allowedAttributes = [
        'a' => ['href', 'target', 'rel'],
        'th' => ['colspan', 'rowspan'],
        'td' => ['colspan', 'rowspan'],
    ];

    $document = new DOMDocument();
    libxml_use_internal_errors(true);
    $document->loadHTML('<?xml encoding="utf-8" ?><div>' . $html . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
    libxml_clear_errors();

    $root = $document->documentElement;
    if (!$root) {
        return '';
    }

    $sanitizeNode = static function ($node) use (&$sanitizeNode, $document, $allowedTags, $allowedAttributes) {
        if ($node->nodeType === XML_ELEMENT_NODE) {
            $tagName = strtolower($node->nodeName);
            if (!in_array($tagName, $allowedTags, true)) {
                $fragment = $document->createDocumentFragment();
                while ($node->firstChild) {
                    $child = $node->removeChild($node->firstChild);
                    $fragment->appendChild($child);
                }

                if ($node->parentNode) {
                    $node->parentNode->replaceChild($fragment, $node);
                }

                return;
            }

            if ($node->hasAttributes()) {
                $attributesToRemove = [];
                foreach ($node->attributes as $attribute) {
                    $attributeName = strtolower($attribute->nodeName);
                    $isAllowed = in_array($attributeName, $allowedAttributes[$tagName] ?? [], true);
                    if (!$isAllowed) {
                        $attributesToRemove[] = $attributeName;
                        continue;
                    }

                    if ($tagName === 'a' && $attributeName === 'href') {
                        $href = trim($attribute->nodeValue);
                        if ($href !== '' && !preg_match('/^(https?:|mailto:|tel:|\/|#)/i', $href)) {
                            $attributesToRemove[] = $attributeName;
                        }
                    }
                }

                foreach ($attributesToRemove as $attributeName) {
                    $node->removeAttribute($attributeName);
                }
            }

            if ($tagName === 'a') {
                $href = trim($node->getAttribute('href'));
                if ($href !== '' && preg_match('/^https?:\/\//i', $href)) {
                    $node->setAttribute('target', '_blank');
                    $node->setAttribute('rel', 'noopener');
                } else {
                    $node->removeAttribute('target');
                    $node->removeAttribute('rel');
                }
            }
        }

        for ($child = $node->firstChild; $child !== null; $child = $child->nextSibling) {
            $sanitizeNode($child);
        }
    };

    $sanitizeNode($root);

    $output = '';
    foreach ($root->childNodes as $child) {
        $output .= $document->saveHTML($child);
    }

    return trim($output);
}

function renderRichText($html)
{
    $html = trim((string) $html);
    if ($html === '') {
        return '';
    }

    if (strip_tags($html) === $html) {
        return nl2br(e($html));
    }

    return sanitizeRichText($html);
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