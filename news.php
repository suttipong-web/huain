<?php
require_once __DIR__ . '/includes/init.php';

$lang = currentLang();
$isTh = $lang === 'th';
$pageKey = 'news';
$siteTitle = SITE_NAME . ' | News';
$metaDescription = 'Read HUAIN Thailand latest project news, installations, and AV deployment showcases.';
$canonicalUrl = newsListUrl();

$stmt = $pdo->query('SELECT * FROM news WHERE status = 1 ORDER BY created_at DESC');
$newsItems = $stmt->fetchAll();

include __DIR__ . '/includes/site-header.php';
?>

<section class="section-block">
    <div class="container">
        <div class="section-head">
            <div>
                <h2>News & Projects</h2>
                <p>Showcase of recent installations and deployments</p>
            </div>
        </div>

        <div class="row g-4">
            <?php if ($newsItems): ?>
                <?php foreach ($newsItems as $news): ?>
                    <?php
                    $title = $isTh ? ($news['title_th'] ?: $news['title_en']) : ($news['title_en'] ?: $news['title_th']);
                    $desc = $isTh ? ($news['short_desc_th'] ?: $news['short_desc_en']) : ($news['short_desc_en'] ?: $news['short_desc_th']);
                    $img = $news['image'] ? uploadUrl($news['image']) : 'https://images.unsplash.com/photo-1497366811353-6870744d04b2?q=80&w=1200';
                    ?>
                    <div class="col-lg-4 col-md-6">
                        <article class="card-premium">
                            <img src="<?= e($img) ?>" alt="<?= e($title) ?>">
                            <div class="inner">
                                <div class="meta"><?= e(date('d M Y', strtotime($news['created_at']))) ?></div>
                                <h4><?= e($title) ?></h4>
                                <p><?= e(truncateText($desc, 130)) ?></p>
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

<?php include __DIR__ . '/includes/site-footer.php';
