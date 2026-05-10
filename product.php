<?php
require_once __DIR__ . '/includes/init.php';

$lang = currentLang();
$isTh = $lang === 'th';
$pageKey = 'products';

$slug = trim($_GET['slug'] ?? '');
if ($slug === '') {
    header('Location: ' . productsUrl());
    exit;
}

$stmt = $pdo->prepare('SELECT * FROM products WHERE slug = ? AND status = 1 LIMIT 1');
$stmt->execute([$slug]);
$product = $stmt->fetch();

if (!$product) {
    http_response_code(404);
    exit('Product not found.');
}

$name = $isTh ? ($product['name_th'] ?: $product['name_en']) : ($product['name_en'] ?: $product['name_th']);
$shortDesc = $isTh ? ($product['short_desc_th'] ?: $product['short_desc_en']) : ($product['short_desc_en'] ?: $product['short_desc_th']);
$description = $isTh ? ($product['description_th'] ?: $product['description_en']) : ($product['description_en'] ?: $product['description_th']);
$specification = $isTh ? ($product['specification_th'] ?: $product['specification_en']) : ($product['specification_en'] ?: $product['specification_th']);
$pdfFile = $isTh ? ($product['pdf_th'] ?: $product['pdf_en']) : ($product['pdf_en'] ?: $product['pdf_th']);
$image = $product['image'] ? uploadUrl($product['image']) : 'https://images.unsplash.com/photo-1518770660439-4636190af475?q=80&w=1200';

$galleryStmt = $pdo->prepare('SELECT image FROM product_images WHERE product_id = ? ORDER BY sort_order ASC, id ASC');
$galleryStmt->execute([(int) $product['id']]);
$galleryRows = $galleryStmt->fetchAll();

$featuredStmt = $pdo->prepare('SELECT p.*, c.name_en AS category_name_en, c.name_th AS category_name_th FROM products p LEFT JOIN product_categories c ON c.id = p.category_id WHERE p.status = 1 AND p.featured = 1 AND p.id <> ? ORDER BY p.created_at DESC, p.id DESC LIMIT 3');
$featuredStmt->execute([(int) $product['id']]);
$featuredProducts = $featuredStmt->fetchAll();

$galleryImages = [$image];
foreach ($galleryRows as $galleryRow) {
    $galleryFile = trim((string) ($galleryRow['image'] ?? ''));
    if ($galleryFile === '') {
        continue;
    }

    $galleryUrl = uploadUrl($galleryFile);
    if (!in_array($galleryUrl, $galleryImages, true)) {
        $galleryImages[] = $galleryUrl;
    }
}

$siteTitle = SITE_NAME . ' | ' . $name;
$metaDescription = seoDescription($product['seo_description'] ?: $shortDesc);
$canonicalUrl = productUrl($product['slug']);
$metaImage = $image;
$ogType = 'product';
$priceLabel = ((float) $product['price'] > 0) ? ('THB ' . formatPrice($product['price'])) : '-';

include __DIR__ . '/includes/site-header.php';
?>

<section class="detail-hero">
    <div class="container">
        <div class="row g-4 align-items-center">
            <div class="col-lg-6">
                <div class="product-gallery js-product-gallery">
                    <div class="product-gallery-main-wrap">
                        <img id="product-main-image" class="product-gallery-main" src="<?= e($galleryImages[0]) ?>" alt="<?= e($name) ?>">
                    </div>
                    <?php if (count($galleryImages) > 1): ?>
                        <div class="product-gallery-thumbs">
                            <?php foreach ($galleryImages as $galleryImage): ?>
                                <button
                                    type="button"
                                    class="product-thumb<?= $galleryImage === $galleryImages[0] ? ' is-active' : '' ?>"
                                    data-image="<?= e($galleryImage) ?>"
                                    aria-label="View product image"
                                >
                                    <img src="<?= e($galleryImage) ?>" alt="<?= e($name) ?> thumbnail">
                                </button>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="meta">HUAIN PRODUCT</div>
                <h1 class="mb-3"><?= e($name) ?></h1>
                <p class="article-text detail-lead"><?= e($shortDesc) ?></p>
                <div class="detail-summary-bar">
                    <div class="detail-price-card">
                        <span>Price</span>
                        <strong><?= e($priceLabel) ?></strong>
                    </div>
                </div>
                <?php if ($pdfFile): ?>
                    <div class="detail-pdf-row">
                        <a href="<?= e(uploadUrl($pdfFile)) ?>" class="btn-gold" target="_blank" rel="noopener">Open PDF Catalog</a>
                    </div>
                <?php endif; ?>
                
            </div>
        </div>
    </div>
</section>

<section class="section-block">
    <div class="container">
        <div class="section-head detail-section-head">
            <div>
                <div class="kicker">Product Detail</div>
                <h2><?= e($name) ?></h2>
                <p>Clear overview, specification details, and quick navigation back to the full catalog.</p>
            </div>
            <div style="display: flex; gap: 0.8rem; align-items: center;">
                <a href="<?= e(productsUrl()) ?>" class="btn-outline-section" style="display: inline-flex; align-items: center; gap: 0.5rem;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0;">
                        <circle cx="11" cy="11" r="8"></circle>
                        <path d="m21 21-4.35-4.35"></path>
                    </svg>
                    Find Products
                </a>
                <a href="<?= e(productsUrl()) ?>" class="text-link">Back to Products</a>
            </div>
        </div>
        <div class="row g-4 align-items-start">
            <div class="col-lg-8">
                <div class="detail-content-card mb-4">
                    <div class="detail-card-head">
                        <div>
                            <div class="meta">Overview</div>
                            <h3>Product Overview</h3>
                        </div>
                    </div>
                    <div class="article-text rich-content"><?= renderRichText($description) ?></div>
                </div>
                <div class="detail-content-card detail-content-card-spec">
                    <div class="detail-card-head">
                        <div>
                            <div class="meta">Technical</div>
                            <h3>Specification</h3>
                        </div>
                    </div>
                    <div class="article-text rich-content"><?= renderRichText($specification) ?></div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="detail-side-stack">
                    <div class="detail-side-card">
                        <div class="meta">Navigation</div>
                        <h5 class="mb-3">Browse More Products</h5>
                        <p class="article-text">Go back to the full catalog to compare models and categories.</p>
                        <a href="<?= e(productsUrl()) ?>" class="btn-outline-section mt-2">View All Products</a>
                    </div>
                    <div class="detail-side-card detail-side-card-accent">
                        <div class="meta">Support</div>
                        <h5 class="mb-3">Need support?</h5>
                        <p class="article-text">Contact HUAIN Thailand for deployment consultation and system design.</p>
                        <a href="<?= e(contactUrl()) ?>" class="btn-gold mt-2">Contact Team</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php if ($pdfFile): ?>
    <section class="section-block pt-0">
        <div class="container">
            <div class="section-head">
                <div>
                    <h2>Catalog PDF</h2>
                    <p>Preview file directly below</p>
                </div>
            </div>
            <iframe class="pdf-frame" src="<?= e(uploadUrl($pdfFile)) ?>"></iframe>
        </div>
    </section>
<?php endif; ?>

<?php if ($featuredProducts): ?>
    <section class="section-block detail-featured-section">
        <div class="container">
            <div class="section-head">
                <div>
                    <div class="kicker">Featured</div>
                    <h2>Featured Products</h2>
                    <p>Explore other highlighted products from the HUAIN catalog.</p>
                </div>
                <a href="<?= e(productsUrl()) ?>" class="btn-outline-section">Back to Product Catalog</a>
            </div>

            <div class="row g-4">
                <?php foreach ($featuredProducts as $featuredProduct): ?>
                    <?php
                    $featuredName = $isTh ? ($featuredProduct['name_th'] ?: $featuredProduct['name_en']) : ($featuredProduct['name_en'] ?: $featuredProduct['name_th']);
                    $featuredDesc = $isTh ? ($featuredProduct['short_desc_th'] ?: $featuredProduct['short_desc_en']) : ($featuredProduct['short_desc_en'] ?: $featuredProduct['short_desc_th']);
                    $featuredCat = $isTh ? ($featuredProduct['category_name_th'] ?: $featuredProduct['category_name_en']) : ($featuredProduct['category_name_en'] ?: $featuredProduct['category_name_th']);
                    $featuredImg = $featuredProduct['image'] ? uploadUrl($featuredProduct['image']) : 'https://images.unsplash.com/photo-1518770660439-4636190af475?q=80&w=1200';
                    $featuredPriceLabel = ((float) $featuredProduct['price'] > 0) ? ('THB ' . formatPrice($featuredProduct['price'])) : '-';
                    ?>
                    <div class="col-lg-4 col-md-6">
                        <article class="card-premium detail-featured-card">
                            <img src="<?= e($featuredImg) ?>" alt="<?= e($featuredName) ?>">
                            <div class="inner">
                                <div class="meta"><?= e($featuredCat ?: 'HUAIN PRODUCT') ?></div>
                                <h4><?= e($featuredName) ?></h4>
                                <p><?= e(truncateText($featuredDesc, 120)) ?></p>
                                <p class="mb-3"><strong><?= e($featuredPriceLabel) ?></strong></p>
                                <a class="btn-gold" href="<?= e(productUrl($featuredProduct['slug'])) ?>">View Detail</a>
                            </div>
                        </article>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<?php include __DIR__ . '/includes/site-footer.php';
