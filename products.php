<?php
require_once __DIR__ . '/includes/init.php';

$lang = currentLang();
$isTh = $lang === 'th';
$pageKey = 'products';
$siteTitle = SITE_NAME . ' | Products';
$metaDescription = 'Explore HUAIN Thailand professional AV product catalog including HDMI Matrix, AV over IP, and signal management systems.';
$canonicalUrl = productsUrl();

$stmt = $pdo->query('SELECT p.*, c.name_en AS category_name_en, c.name_th AS category_name_th FROM products p LEFT JOIN product_categories c ON c.id = p.category_id WHERE p.status = 1 ORDER BY p.featured DESC, p.created_at DESC');
$products = $stmt->fetchAll();

include __DIR__ . '/includes/site-header.php';
?>

<section class="section-block">
    <div class="container">
        <div class="section-head">
            <div>
                <h2>Products</h2>
                <p>Professional AV catalog from HUAIN Thailand</p>
            </div>
        </div>

        <div class="row g-4">
            <?php if ($products): ?>
                <?php foreach ($products as $product): ?>
                    <?php
                    $name = $isTh ? ($product['name_th'] ?: $product['name_en']) : ($product['name_en'] ?: $product['name_th']);
                    $desc = $isTh ? ($product['short_desc_th'] ?: $product['short_desc_en']) : ($product['short_desc_en'] ?: $product['short_desc_th']);
                    $cat = $isTh ? ($product['category_name_th'] ?: $product['category_name_en']) : ($product['category_name_en'] ?: $product['category_name_th']);
                    $img = $product['image'] ? uploadUrl($product['image']) : 'https://images.unsplash.com/photo-1518770660439-4636190af475?q=80&w=1200';
                    ?>
                    <div class="col-lg-4 col-md-6">
                        <article class="card-premium">
                            <img src="<?= e($img) ?>" alt="<?= e($name) ?>">
                            <div class="inner">
                                <div class="meta"><?= e($cat ?: 'AV SOLUTION') ?></div>
                                <h4><?= e($name) ?></h4>
                                <p><?= e(truncateText($desc, 120)) ?></p>
                                <p class="mb-3"><strong>THB <?= e(formatPrice($product['price'])) ?></strong></p>
                                <a class="btn-gold" href="<?= e(productUrl($product['slug'])) ?>">View Detail</a>
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

<?php include __DIR__ . '/includes/site-footer.php';
