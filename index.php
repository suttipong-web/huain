<?php
require_once __DIR__ . '/includes/init.php';

$lang = currentLang();
$isTh = $lang === 'th';

$siteTitle = SITE_NAME . ' | Professional AV Solutions';
$pageKey = 'home';
$metaDescription = 'HUAIN Thailand delivers premium AV solutions including HDMI Matrix, AV over IP, and enterprise multimedia integration.';
$canonicalUrl = baseUrl('');
$metaImage = baseUrl('images/logo.jpg');

$bannerStmt = $pdo->query('SELECT * FROM banners WHERE status = 1 ORDER BY sort_order ASC, id DESC');
$banners = $bannerStmt->fetchAll();

$featuredStmt = $pdo->query('SELECT * FROM products WHERE status = 1 AND featured = 1 ORDER BY id DESC LIMIT 6');
$featuredProducts = $featuredStmt->fetchAll();

if (!$featuredProducts) {
    $fallbackStmt = $pdo->query('SELECT * FROM products WHERE status = 1 ORDER BY id DESC LIMIT 6');
    $featuredProducts = $fallbackStmt->fetchAll();
}

$newsStmt = $pdo->query('SELECT * FROM news WHERE status = 1 ORDER BY created_at DESC LIMIT 3');
$newsItems = $newsStmt->fetchAll();

$contactStmt = $pdo->query('SELECT * FROM contact_information ORDER BY id ASC LIMIT 1');
$contactInfo = $contactStmt->fetch();

include __DIR__ . '/includes/site-header.php';
?>

<section class="hero">
    <div class="swiper hero-swiper">
        <div class="swiper-wrapper">
            <?php if ($banners): ?>
                <?php foreach ($banners as $banner): ?>
                    <?php
                    $bannerTitle = $isTh ? ($banner['title_th'] ?: $banner['title_en']) : ($banner['title_en'] ?: $banner['title_th']);
                    $bannerImage = $banner['image'] ? uploadUrl($banner['image']) : 'https://images.unsplash.com/photo-1498050108023-c5249f4df085?q=80&w=1600';
                    $bannerLink = normalizeInternalLink($banner['link_url'] ?? '');
                    ?>
                    <div class="swiper-slide">
                        <?php if ($bannerLink): ?><a href="<?= e($bannerLink) ?>"><?php endif; ?>
                            <img src="<?= e($bannerImage) ?>" alt="<?= e($bannerTitle ?: 'HUAIN Banner') ?>">
                        <?php if ($bannerLink): ?></a><?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="swiper-slide"><img src="https://images.unsplash.com/photo-1516321318423-f06f85e504b3?q=80&w=1600" alt="HUAIN AV"></div>
                <div class="swiper-slide"><img src="https://images.unsplash.com/photo-1520607162513-77705c0f0d4a?q=80&w=1600" alt="HUAIN conference"></div>
            <?php endif; ?>
        </div>
        <div class="swiper-pagination"></div>
    </div>

    <div class="hero-overlay"></div>

    <div class="hero-content">
        <div class="container">
            <h1>Professional <span class="brand-gold">AV Solutions</span><br>for Thailand Projects</h1>
            <p>
                HUAIN Thailand is the local branch for high-performance AV systems including HDMI Matrix, AV over IP,
                control integration, and enterprise deployment support.
            </p>
            <div class="d-flex gap-2 flex-wrap mt-4">
                <a href="<?= e(productsUrl()) ?>" class="btn-gold">View Products</a>
                <a href="<?= e(contactUrl()) ?>" class="btn btn-outline-light">Contact Team</a>
            </div>
        </div>
    </div>
</section>

<section class="section-block">
    <div class="container">
        <div class="section-head">
            <div>
                <h2>Featured Products</h2>
                <p>HDMI Matrix, HUAIN Power Squeezer and enterprise AV line-up</p>
            </div>
            <a href="<?= e(productsUrl()) ?>" class="btn btn-outline-light btn-sm rounded-pill">See all products</a>
        </div>

        <div class="row g-4">
            <?php if ($featuredProducts): ?>
                <?php foreach ($featuredProducts as $product): ?>
                    <?php
                    $productName = $isTh ? ($product['name_th'] ?: $product['name_en']) : ($product['name_en'] ?: $product['name_th']);
                    $productDesc = $isTh ? ($product['short_desc_th'] ?: $product['short_desc_en']) : ($product['short_desc_en'] ?: $product['short_desc_th']);
                    $productImage = $product['image'] ? uploadUrl($product['image']) : 'https://images.unsplash.com/photo-1518770660439-4636190af475?q=80&w=1200';
                    ?>
                    <div class="col-lg-4 col-md-6">
                        <article class="card-premium">
                            <img src="<?= e($productImage) ?>" alt="<?= e($productName) ?>">
                            <div class="inner">
                                <div class="meta">AV PRODUCT</div>
                                <h4><?= e($productName) ?></h4>
                                <p><?= e(truncateText($productDesc, 120)) ?></p>
                                <a class="btn-gold mt-2" href="<?= e(productUrl($product['slug'])) ?>">View Detail</a>
                            </div>
                        </article>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12"><div class="empty-state">No product data found.</div></div>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="section-block pt-0">
    <div class="container">
        <div class="section-head">
            <div>
                <h2>News & Projects</h2>
                <p>Latest installations, showcases, and deployment highlights</p>
            </div>
            <a href="<?= e(newsListUrl()) ?>" class="btn btn-outline-light btn-sm rounded-pill">View all news</a>
        </div>

        <div class="row g-4">
            <?php if ($newsItems): ?>
                <?php foreach ($newsItems as $news): ?>
                    <?php
                    $newsTitle = $isTh ? ($news['title_th'] ?: $news['title_en']) : ($news['title_en'] ?: $news['title_th']);
                    $newsDesc = $isTh ? ($news['short_desc_th'] ?: $news['short_desc_en']) : ($news['short_desc_en'] ?: $news['short_desc_th']);
                    $newsImage = $news['image'] ? uploadUrl($news['image']) : 'https://images.unsplash.com/photo-1497366811353-6870744d04b2?q=80&w=1200';
                    ?>
                    <div class="col-lg-4">
                        <article class="card-premium">
                            <img src="<?= e($newsImage) ?>" alt="<?= e($newsTitle) ?>">
                            <div class="inner">
                                <div class="meta"><?= e(date('d M Y', strtotime($news['created_at']))) ?></div>
                                <h4><?= e($newsTitle) ?></h4>
                                <p><?= e(truncateText($newsDesc, 110)) ?></p>
                                <a href="<?= e(newsUrl($news['slug'])) ?>" class="btn-gold mt-2">Read More</a>
                            </div>
                        </article>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12"><div class="empty-state">No news data found.</div></div>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="section-block pt-0">
    <div class="container">
        <div class="form-shell">
            <div class="row align-items-center g-4">
                <div class="col-lg-8">
                    <h3 class="mb-2">Start Your AV Project with HUAIN Thailand</h3>
                    <p class="mb-0">
                        Email: <?= e($contactInfo['email'] ?? 'sales@huain-th.com') ?> |
                        Phone: <?= e($contactInfo['phone'] ?? '+66 0 0000 0000') ?>
                    </p>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <a href="<?= e(contactUrl()) ?>" class="btn-gold">Request Consultation</a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/site-footer.php';
