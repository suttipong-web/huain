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
                <p class="article-text"><?= e($shortDesc) ?></p>
                <h4 class="mt-3">THB <?= e(formatPrice($product['price'])) ?></h4>
                <?php if ($pdfFile): ?>
                    <a href="<?= e(uploadUrl($pdfFile)) ?>" class="btn-gold mt-2" target="_blank" rel="noopener">Open PDF Catalog</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<section class="section-block">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="form-shell mb-4">
                    <h3>Product Overview</h3>
                    <div class="article-text"><?= nl2br(e($description)) ?></div>
                </div>
                <div class="form-shell">
                    <h3>Specification</h3>
                    <div class="article-text"><?= nl2br(e($specification)) ?></div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="side-panel">
                    <h5 class="mb-3">Need support?</h5>
                    <p class="article-text">Contact HUAIN Thailand for deployment consultation and system design.</p>
                    <a href="<?= e(contactUrl()) ?>" class="btn-gold mt-2">Contact Team</a>
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

<?php include __DIR__ . '/includes/site-footer.php';
