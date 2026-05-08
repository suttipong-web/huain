<?php
require_once __DIR__ . '/includes/init.php';

$lang = currentLang();
$isTh = $lang === 'th';
$pageKey = 'news';

$slug = trim($_GET['slug'] ?? '');
if ($slug === '') {
    header('Location: ' . newsListUrl());
    exit;
}

$stmt = $pdo->prepare('SELECT * FROM news WHERE slug = ? AND status = 1 LIMIT 1');
$stmt->execute([$slug]);
$news = $stmt->fetch();

if (!$news) {
    http_response_code(404);
    exit('News not found.');
}

$title = $isTh ? ($news['title_th'] ?: $news['title_en']) : ($news['title_en'] ?: $news['title_th']);
$shortDesc = $isTh ? ($news['short_desc_th'] ?: $news['short_desc_en']) : ($news['short_desc_en'] ?: $news['short_desc_th']);
$description = $isTh ? ($news['description_th'] ?: $news['description_en']) : ($news['description_en'] ?: $news['description_th']);
$image = $news['image'] ? uploadUrl($news['image']) : 'https://images.unsplash.com/photo-1497366811353-6870744d04b2?q=80&w=1200';

$siteTitle = SITE_NAME . ' | ' . $title;
$metaDescription = seoDescription($news['seo_description'] ?: $shortDesc);
$canonicalUrl = newsUrl($news['slug']);
$metaImage = $image;
$ogType = 'article';

include __DIR__ . '/includes/site-header.php';
?>

<section class="detail-hero">
    <div class="container">
        <div class="row g-4 align-items-center">
            <div class="col-lg-6">
                <img src="<?= e($image) ?>" alt="<?= e($title) ?>">
            </div>
            <div class="col-lg-6">
                <div class="meta"><?= e(date('d M Y', strtotime($news['created_at']))) ?></div>
                <h1 class="mb-3"><?= e($title) ?></h1>
                <p class="article-text"><?= e($shortDesc) ?></p>
            </div>
        </div>
    </div>
</section>

<section class="section-block">
    <div class="container">
        <div class="form-shell">
            <h3>Project Detail</h3>
            <div class="article-text"><?= nl2br(e($description)) ?></div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/site-footer.php';
