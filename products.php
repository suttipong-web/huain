<?php
require_once __DIR__ . '/includes/init.php';

$lang = currentLang();
$isTh = $lang === 'th';
$pageKey = 'products';
$siteTitle = SITE_NAME . ' | Products';
$metaDescription = 'Explore HUAIN Thailand professional AV product catalog including HDMI Matrix, AV over IP, and signal management systems.';
$canonicalUrl = productsUrl();

$selectedCategoryId = (int) ($_GET['category'] ?? 0);

$categoryStmt = $pdo->query('SELECT id, name_en, name_th FROM product_categories ORDER BY id ASC');
$categories = $categoryStmt->fetchAll();

$productSql = 'SELECT p.*, c.name_en AS category_name_en, c.name_th AS category_name_th FROM products p LEFT JOIN product_categories c ON c.id = p.category_id WHERE p.status = 1';
$productParams = [];
if ($selectedCategoryId > 0) {
    $productSql .= ' AND p.category_id = ?';
    $productParams[] = $selectedCategoryId;
}
$productSql .= ' ORDER BY p.featured DESC, p.created_at DESC';

$stmt = $pdo->prepare($productSql);
$stmt->execute($productParams);
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
            <div style="display:flex;align-items:center;gap:0.55rem;min-width:260px;max-width:420px;width:100%;">
                <span aria-hidden="true" style="display:inline-flex;align-items:center;justify-content:center;width:40px;height:40px;border-radius:10px;border:1px solid rgba(244, 124, 32, 0.22);background:#fff6ef;color:#c85a00;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <path d="m21 21-4.35-4.35"></path>
                    </svg>
                </span>
                <input
                    type="text"
                    id="product-search-input"
                    placeholder="<?= $isTh ? 'ค้นหาสินค้า...' : 'Search products...' ?>"
                    aria-label="<?= $isTh ? 'ค้นหาสินค้า' : 'Search products' ?>"
                    style="width:100%;height:40px;border-radius:10px;border:1px solid rgba(244, 124, 32, 0.22);background:#fff;padding:0.45rem 0.8rem;color:#2d241f;"
                >
            </div>
        </div>

        <div class="product-filter-menu" aria-label="Product category filters">
            <a class="product-filter-chip <?= $selectedCategoryId === 0 ? 'active' : '' ?>" href="<?= e(productsUrl()) ?>">
                <?= $isTh ? 'ทั้งหมด' : 'All' ?>
            </a>
            <?php foreach ($categories as $category): ?>
                <?php
                $catId = (int) $category['id'];
                $catName = $isTh ? ($category['name_th'] ?: $category['name_en']) : ($category['name_en'] ?: $category['name_th']);
                ?>
                <a class="product-filter-chip <?= $selectedCategoryId === $catId ? 'active' : '' ?>" href="<?= e(productsUrl() . '?category=' . $catId) ?>">
                    <?= e($catName ?: ($isTh ? 'ไม่ระบุหมวดหมู่' : 'Uncategorized')) ?>
                </a>
            <?php endforeach; ?>
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
                    <div class="col-lg-4 col-md-6 product-card-col" data-search="<?= e(strtolower(trim(($name ?: '') . ' ' . ($cat ?: '') . ' ' . ($desc ?: '')))) ?>">
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

<script>
document.addEventListener('DOMContentLoaded', function () {
    var searchInput = document.getElementById('product-search-input');
    if (!searchInput) {
        return;
    }

    var cards = document.querySelectorAll('.product-card-col');
    searchInput.addEventListener('input', function () {
        var keyword = (searchInput.value || '').toLowerCase().trim();
        cards.forEach(function (card) {
            var searchText = (card.getAttribute('data-search') || '').toLowerCase();
            card.style.display = searchText.indexOf(keyword) !== -1 ? '' : 'none';
        });
    });
});
</script>

<?php include __DIR__ . '/includes/site-footer.php';
