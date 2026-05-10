<?php
$siteTitle = $siteTitle ?? SITE_NAME;
$pageKey = $pageKey ?? 'home';
$lang = currentLang();
$isTh = $lang === 'th';
$metaDescription = $metaDescription ?? 'HUAIN Thailand provides premium AV products and integration solutions for professional projects.';
$metaImage = $metaImage ?? baseUrl('images/logo.jpg');
$canonicalUrl = $canonicalUrl ?? currentUrl();
$ogType = $ogType ?? 'website';
?>
<!doctype html>
<html lang="<?= e($lang) ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($siteTitle) ?></title>
    <meta name="description" content="<?= e($metaDescription) ?>">
    <link rel="canonical" href="<?= e($canonicalUrl) ?>">
    <meta property="og:site_name" content="<?= e(SITE_NAME) ?>">
    <meta property="og:type" content="<?= e($ogType) ?>">
    <meta property="og:title" content="<?= e($siteTitle) ?>">
    <meta property="og:description" content="<?= e($metaDescription) ?>">
    <meta property="og:url" content="<?= e($canonicalUrl) ?>">
    <meta property="og:image" content="<?= e($metaImage) ?>">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= e($siteTitle) ?>">
    <meta name="twitter:description" content="<?= e($metaDescription) ?>">
    <meta name="twitter:image" content="<?= e($metaImage) ?>">
    <link rel="icon" type="image/svg+xml" href="<?= e(baseUrl('favicon.svg')) ?>">
    <link rel="icon" type="image/x-icon" href="<?= e(baseUrl('favicon.ico')) ?>">
    <link rel="apple-touch-icon" sizes="180x180" href="<?= e(baseUrl('favicon-180x180.png')) ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@500;600;700&family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
    <link rel="stylesheet" href="<?= e(baseUrl('assets/css/style.css')) ?>">
</head>
<body>
<header class="site-header fixed-top">
    <div class="container nav-shell">
        <a href="<?= e(baseUrl()) ?>" class="brand-mark">
            <img src="<?= e(baseUrl('images/logo.jpg')) ?>" alt="HUAIN Thailand logo">
            <div>
                <strong>HUAIN THAILAND</strong>
                <small>huain-th.com</small>
            </div>
        </a>

        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#topNav" aria-label="Toggle navigation">
            <span class="menu-icon"></span>
        </button>

        <nav id="topNav" class="collapse navbar-collapse">
            <ul class="main-nav ms-auto">
                <li><a class="<?= $pageKey === 'home' ? 'active' : '' ?>" href="<?= e(baseUrl('')) ?>">Home</a></li>
                <li><a class="<?= $pageKey === 'products' ? 'active' : '' ?>" href="<?= e(productsUrl()) ?>">Products</a></li>
                <li><a class="<?= $pageKey === 'news' ? 'active' : '' ?>" href="<?= e(newsListUrl()) ?>">News</a></li>
                <li><a class="<?= $pageKey === 'contact' ? 'active' : '' ?>" href="<?= e(contactUrl()) ?>">Contact</a></li>
            </ul>
            <div class="lang-switch">
                <a href="<?= e(languageUrl('en')) ?>" class="<?= $lang === 'en' ? 'active' : '' ?>">EN</a>
                <a href="<?= e(languageUrl('th')) ?>" class="<?= $lang === 'th' ? 'active' : '' ?>">TH</a>
            </div>
        </nav>
    </div>
</header>
<main class="page-wrap">
