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
                    <div class="swiper-slide hero-slide">
                        <?php if ($bannerLink): ?><a class="hero-slide-media" href="<?= e($bannerLink) ?>"><?php endif; ?>
                            <img src="<?= e($bannerImage) ?>" alt="<?= e($bannerTitle ?: 'HUAIN Banner') ?>">
                        <?php if ($bannerLink): ?></a><?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="swiper-slide hero-slide">
                    <img src="https://images.unsplash.com/photo-1516321318423-f06f85e504b3?q=80&w=1600" alt="HUAIN AV">
                </div>
                <div class="swiper-slide hero-slide">
                    <img src="https://images.unsplash.com/photo-1520607162513-77705c0f0d4a?q=80&w=1600" alt="HUAIN conference">
                </div>
            <?php endif; ?>
        </div>
        <div class="swiper-pagination"></div>
    </div>

    <div class="hero-luxury-edge"></div>

</section>

<section class="section-block why-section">
    <div class="container">
        <div class="why-shell reveal-up">
            <div class="row g-4 align-items-stretch why-layout">
                <div class="col-lg-5">
                    <div class="why-lead reveal-up">
                        <div class="kicker">Why Choose Us</div>
                        <h2>Trusted AV Partner for Professional Projects</h2>
                        <p class="why-intro">
                            We design premium-ready AV solutions that balance reliability, deployment speed, and
                            presentation quality for conference, education, and enterprise environments.
                        </p>
                        <div class="why-stat-grid">
                            <div class="why-stat">
                                <strong>4 Core Strengths</strong>
                                <span>Focused on service agility, delivery confidence, and professional execution.</span>
                            </div>
                            <div class="why-stat accent">
                                <strong>Built for Scale</strong>
                                <span>From boardrooms to smart campuses and integrated control systems.</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-7">
                    <div class="row g-3 g-lg-4 why-grid">
                        <div class="col-6 col-md-6">
                            <article class="why-card reveal-up">
                                <div class="why-icon-wrap">
                                    <span class="why-icon low-icon" aria-hidden="true">MOQ</span>
                                </div>
                                <h3>Low MOQ</h3>
                                <p>Flexible order support for pilot deployments and growing project phases.</p>
                            </article>
                        </div>

                        <div class="col-6 col-md-6">
                            <article class="why-card reveal-up">
                                <div class="why-icon-wrap">
                                    <span class="why-icon team-icon" aria-hidden="true">
                                        <svg viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg" role="img">
                                            <circle cx="21" cy="24" r="8"></circle>
                                            <circle cx="43" cy="24" r="8"></circle>
                                            <path d="M10 48c0-6.8 5.4-12 12-12s12 5.2 12 12"></path>
                                            <path d="M30 48c0-6.8 5.4-12 12-12s12 5.2 12 12"></path>
                                        </svg>
                                    </span>
                                </div>
                                <h3>Professional Team</h3>
                                <p>Experienced coordination across product recommendation, setup, and support.</p>
                            </article>
                        </div>

                        <div class="col-6 col-md-6">
                            <article class="why-card reveal-up">
                                <div class="why-icon-wrap">
                                    <span class="why-icon fast-icon" aria-hidden="true">
                                        <svg viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg" role="img">
                                            <path d="M10 33h26"></path>
                                            <path d="M18 23h22"></path>
                                            <path d="M14 43h20"></path>
                                            <path d="M36 18l18 14-18 14"></path>
                                        </svg>
                                    </span>
                                </div>
                                <h3>Fast Delivery</h3>
                                <p>Responsive supply and scheduling support to match demanding rollout timelines.</p>
                            </article>
                        </div>

                        <div class="col-6 col-md-6">
                            <article class="why-card reveal-up">
                                <div class="why-icon-wrap">
                                    <span class="why-icon quality-icon" aria-hidden="true">
                                        <svg viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg" role="img">
                                            <path d="M32 8l20 7v17c0 12.2-8.2 20.6-20 24-11.8-3.4-20-11.8-20-24V15z"></path>
                                            <path d="M23 32l6 6 12-12"></path>
                                        </svg>
                                    </span>
                                </div>
                                <h3>Quality Assurance</h3>
                                <p>Professional-grade systems selected for stable, integrated, long-term operation.</p>
                            </article>
                        </div>
                    </div>
                </div>
            </div>

            <div class="why-summary-wrap reveal-up">
                <p class="why-summary mb-0">
                    We specialize in providing cutting-edge solutions such as intelligent digital conference systems,
                    paperless meeting systems, and visual management systems tailored to meet diverse needs. From
                    improving meeting efficiency to enhancing communication clarity, our systems empower organizations to
                    achieve seamless, scalable, and integrated conferencing solutions.
                </p>
            </div>
        </div>
    </div>
</section>

<section class="section-block featured-section">
    <div class="container">
        <div class="section-head reveal-up">
            <div>
                <h2>Featured Products</h2>
                <p>HDMI Matrix, HUAIN Power Squeezer and enterprise AV line-up</p>
            </div>
            <a href="<?= e(productsUrl()) ?>" class="btn btn-outline-section btn-sm rounded-pill">See all products</a>
        </div>

        <div class="row g-4 featured-grid">
            <?php if ($featuredProducts): ?>
                <?php foreach ($featuredProducts as $product): ?>
                    <?php
                    $productName = $isTh ? ($product['name_th'] ?: $product['name_en']) : ($product['name_en'] ?: $product['name_th']);
                    $productDesc = $isTh ? ($product['short_desc_th'] ?: $product['short_desc_en']) : ($product['short_desc_en'] ?: $product['short_desc_th']);
                    $productImage = $product['image'] ? uploadUrl($product['image']) : 'https://images.unsplash.com/photo-1518770660439-4636190af475?q=80&w=1200';
                    ?>
                    <div class="col-lg-4 col-md-6">
                        <article class="card-premium card-showcase card-uniform reveal-up">
                            <div class="card-media">
                                <img src="<?= e($productImage) ?>" alt="<?= e($productName) ?>">
                            </div>
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

<section class="section-block pt-0 news-section">
    <div class="container">
        <div class="section-head reveal-up">
            <div>
                <h2>News & Projects</h2>
                <p>Latest installations, showcases, and deployment highlights</p>
            </div>
            <a href="<?= e(newsListUrl()) ?>" class="btn btn-outline-section btn-sm rounded-pill">View all news</a>
        </div>

        <div class="row g-4 newsroom-layout">
            <?php if ($newsItems): ?>
                <?php
                $leadNews = $newsItems[0];
                $secondaryNewsItems = array_slice($newsItems, 1);

                $leadNewsTitle = $isTh ? ($leadNews['title_th'] ?: $leadNews['title_en']) : ($leadNews['title_en'] ?: $leadNews['title_th']);
                $leadNewsDesc = $isTh ? ($leadNews['short_desc_th'] ?: $leadNews['short_desc_en']) : ($leadNews['short_desc_en'] ?: $leadNews['short_desc_th']);
                $leadNewsImage = $leadNews['image'] ? uploadUrl($leadNews['image']) : 'https://images.unsplash.com/photo-1497366811353-6870744d04b2?q=80&w=1200';
                ?>

                <div class="col-lg-7">
                    <article class="card-premium card-editorial is-lead reveal-up">
                        <div class="card-media">
                            <img src="<?= e($leadNewsImage) ?>" alt="<?= e($leadNewsTitle) ?>">
                        </div>
                        <div class="inner">
                            <div class="meta"><?= e(date('d M Y', strtotime($leadNews['created_at']))) ?></div>
                            <h4><?= e($leadNewsTitle) ?></h4>
                            <p><?= e(truncateText($leadNewsDesc, 180)) ?></p>
                            <a href="<?= e(newsUrl($leadNews['slug'])) ?>" class="btn-gold mt-2">Read More</a>
                        </div>
                    </article>
                </div>

                <div class="col-lg-5">
                    <div class="newsroom-side">
                        <?php foreach ($secondaryNewsItems as $news): ?>
                            <?php
                            $newsTitle = $isTh ? ($news['title_th'] ?: $news['title_en']) : ($news['title_en'] ?: $news['title_th']);
                            $newsDesc = $isTh ? ($news['short_desc_th'] ?: $news['short_desc_en']) : ($news['short_desc_en'] ?: $news['short_desc_th']);
                            $newsImage = $news['image'] ? uploadUrl($news['image']) : 'https://images.unsplash.com/photo-1497366811353-6870744d04b2?q=80&w=1200';
                            ?>
                            <article class="card-premium card-editorial is-side reveal-up">
                                <div class="card-media">
                                    <img src="<?= e($newsImage) ?>" alt="<?= e($newsTitle) ?>">
                                </div>
                                <div class="inner">
                                    <div class="meta"><?= e(date('d M Y', strtotime($news['created_at']))) ?></div>
                                    <h4><?= e($newsTitle) ?></h4>
                                    <p><?= e(truncateText($newsDesc, 95)) ?></p>
                                    <a href="<?= e(newsUrl($news['slug'])) ?>" class="text-link">Read More</a>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="col-12"><div class="empty-state">No news data found.</div></div>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="section-block pt-0 cta-finale-section">
    <div class="container">
        <div class="row g-4 align-items-stretch cta-finale-layout">
            <!-- Left: Motivational Copy Panel -->
            <div class="col-lg-6">
                <div class="cta-message reveal-up">
                    <div class="cta-kicker">Let's Begin</div>
                    <h2>Start Your AV Project with HUAIN Thailand</h2>
                    <p>
                        Ready to transform your space with professional-grade AV solutions? Our team is prepared to guide you from concept through deployment, ensuring seamless integration and long-term reliability.
                    </p>
                    <div class="cta-accent-box">
                        <strong>Fast-Track Your Project</strong>
                        <span>Get a customized proposal within 24 business hours. No commitments, just solutions tailored to your vision.</span>
                    </div>
                </div>
            </div>

            <!-- Right: Contact + CTA Card -->
            <div class="col-lg-6">
                <div class="cta-contact-card reveal-up">
                    <div class="contact-grid">
                        <!-- Email Contact -->
                        <div class="contact-method">
                            <div class="contact-icon">
                                <svg viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg">
                                    <rect x="8" y="16" width="48" height="32" rx="4"></rect>
                                    <path d="M8 16l24 20 24-20"></path>
                                </svg>
                            </div>
                            <div class="contact-text">
                                <div class="contact-label">Email</div>
                                <a href="mailto:<?= e($contactInfo['email'] ?? 'sales@huain-th.com') ?>" class="contact-value">
                                    <?= e($contactInfo['email'] ?? 'sales@huain-th.com') ?>
                                </a>
                            </div>
                        </div>

                        <!-- Phone Contact -->
                        <div class="contact-method">
                            <div class="contact-icon">
                                <svg viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M20 10h24c4.4 0 8 3.6 8 8v28c0 4.4-3.6 8-8 8H20c-4.4 0-8-3.6-8-8V18c0-4.4 3.6-8 8-8z"></path>
                                    <circle cx="32" cy="50" r="2"></circle>
                                    <path d="M28 16h8"></path>
                                </svg>
                            </div>
                            <div class="contact-text">
                                <div class="contact-label">Phone</div>
                                <a href="tel:<?= e($contactInfo['phone'] ?? '+66000000000') ?>" class="contact-value">
                                    <?= e($contactInfo['phone'] ?? '+66 0 0000 0000') ?>
                                </a>
                            </div>
                        </div>

                        <!-- Line Contact -->
                        <div class="contact-method">
                            <div class="contact-icon line-icon">
                                <svg viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg">
                                    <!-- LINE Icon: Horizontal lines -->
                                    <line x1="16" y1="20" x2="48" y2="20" stroke="currentColor" stroke-width="3" stroke-linecap="round"></line>
                                    <line x1="16" y1="32" x2="48" y2="32" stroke="currentColor" stroke-width="3" stroke-linecap="round"></line>
                                    <line x1="16" y1="44" x2="48" y2="44" stroke="currentColor" stroke-width="3" stroke-linecap="round"></line>
                                </svg>
                            </div>
                            <div class="contact-text">
                                <div class="contact-label">Line</div>
                                <a href="<?= e($contactInfo['line_id'] ? 'https://line.me/ti/p/' . urlencode($contactInfo['line_id']) : '#') ?>" class="contact-value" <?= !isset($contactInfo['line_id']) || !$contactInfo['line_id'] ? 'style="cursor: default; opacity: 0.6;"' : '' ?>>
                                    <?= e($contactInfo['line_id'] ?? 'huain.thailand') ?>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- CTA Button -->
                    <div class="cta-action">
                        <a href="<?= e(contactUrl()) ?>" class="btn-gold btn-lg w-100">Request Consultation</a>
                        <p class="cta-footnote">Typical response time: 2-4 hours during business days</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/site-footer.php';
