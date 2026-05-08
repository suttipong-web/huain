<?php
require_once __DIR__ . '/auth.php';

$adminPage = 'news';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        header('Location: news.php?error=token');
        exit;
    }

    $action = $_POST['action'] ?? 'save';

    if ($action === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);

        $stmt = $pdo->prepare('SELECT image FROM news WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if ($row) {
            removeUpload($row['image']);
            $pdo->prepare('DELETE FROM news WHERE id = ?')->execute([$id]);
        }

        header('Location: news.php?success=deleted');
        exit;
    }

    $id = (int) ($_POST['id'] ?? 0);
    $titleTh = trim($_POST['title_th'] ?? '');
    $titleEn = trim($_POST['title_en'] ?? '');
    $slug = trim($_POST['slug'] ?? '');
    if ($slug === '') {
        $slug = generateSlug($titleEn ?: $titleTh);
    }

    $shortTh = trim($_POST['short_desc_th'] ?? '');
    $shortEn = trim($_POST['short_desc_en'] ?? '');
    $descTh = trim($_POST['description_th'] ?? '');
    $descEn = trim($_POST['description_en'] ?? '');

    $seoTitle = trim($_POST['seo_title'] ?? '');
    $seoDescription = trim($_POST['seo_description'] ?? '');
    $status = parseStatus($_POST['status'] ?? 1);

    $newImage = saveUpload('image', ['jpg', 'jpeg', 'png', 'webp']);
    $imageName = $newImage;

    if ($id > 0) {
        $oldStmt = $pdo->prepare('SELECT image FROM news WHERE id = ? LIMIT 1');
        $oldStmt->execute([$id]);
        $old = $oldStmt->fetch();

        if (!$imageName) {
            $imageName = $old['image'] ?? '';
        } elseif (!empty($old['image'])) {
            removeUpload($old['image']);
        }

        $update = $pdo->prepare('UPDATE news SET title_th = ?, title_en = ?, slug = ?, short_desc_th = ?, short_desc_en = ?, description_th = ?, description_en = ?, image = ?, seo_title = ?, seo_description = ?, status = ? WHERE id = ?');
        $update->execute([$titleTh, $titleEn, $slug, $shortTh, $shortEn, $descTh, $descEn, $imageName, $seoTitle, $seoDescription, $status, $id]);
    } else {
        $insert = $pdo->prepare('INSERT INTO news (title_th, title_en, slug, short_desc_th, short_desc_en, description_th, description_en, image, seo_title, seo_description, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $insert->execute([$titleTh, $titleEn, $slug, $shortTh, $shortEn, $descTh, $descEn, $imageName, $seoTitle, $seoDescription, $status]);
    }

    header('Location: news.php?success=saved');
    exit;
}

$rows = $pdo->query('SELECT * FROM news ORDER BY created_at DESC')->fetchAll();

$editId = (int) ($_GET['edit'] ?? 0);
$editing = null;
if ($editId > 0) {
    $stmt = $pdo->prepare('SELECT * FROM news WHERE id = ? LIMIT 1');
    $stmt->execute([$editId]);
    $editing = $stmt->fetch();
}

include __DIR__ . '/partials/header.php';
?>

<div class="panel">
    <h2><?= $editing ? 'Edit News' : 'Add News' ?></h2>

    <?php if (!empty($_GET['success'])): ?>
        <div class="alert alert-success">Saved successfully.</div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
        <input type="hidden" name="id" value="<?= (int) ($editing['id'] ?? 0) ?>">

        <div class="grid-2">
            <div>
                <label>Title TH</label>
                <input type="text" name="title_th" value="<?= e($editing['title_th'] ?? '') ?>" required>
            </div>
            <div>
                <label>Title EN</label>
                <input type="text" name="title_en" value="<?= e($editing['title_en'] ?? '') ?>" required>
            </div>
            <div>
                <label>Slug (leave blank for auto)</label>
                <input type="text" name="slug" value="<?= e($editing['slug'] ?? '') ?>">
            </div>
            <div>
                <label>Status</label>
                <select name="status">
                    <option value="1" <?= (int) ($editing['status'] ?? 1) === 1 ? 'selected' : '' ?>>Active</option>
                    <option value="0" <?= (int) ($editing['status'] ?? 1) === 0 ? 'selected' : '' ?>>Inactive</option>
                </select>
            </div>
            <div>
                <label>Short Description TH</label>
                <textarea name="short_desc_th"><?= e($editing['short_desc_th'] ?? '') ?></textarea>
            </div>
            <div>
                <label>Short Description EN</label>
                <textarea name="short_desc_en"><?= e($editing['short_desc_en'] ?? '') ?></textarea>
            </div>
            <div>
                <label>Description TH</label>
                <textarea name="description_th"><?= e($editing['description_th'] ?? '') ?></textarea>
            </div>
            <div>
                <label>Description EN</label>
                <textarea name="description_en"><?= e($editing['description_en'] ?? '') ?></textarea>
            </div>
            <div>
                <label>SEO Title</label>
                <input type="text" name="seo_title" value="<?= e($editing['seo_title'] ?? '') ?>">
            </div>
            <div>
                <label>SEO Description</label>
                <textarea name="seo_description"><?= e($editing['seo_description'] ?? '') ?></textarea>
            </div>
            <div>
                <label>News Image</label>
                <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp">
                <?php if (!empty($editing['image'])): ?>
                    <div class="current-upload">
                        <img src="<?= e(uploadUrl($editing['image'])) ?>" alt="Current news image" class="thumb-sm">
                        <span class="muted">Current image</span>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div style="margin-top:0.8rem;">
            <button class="btn btn-primary" type="submit">Save News</button>
            <?php if ($editing): ?>
                <a class="btn btn-muted" href="news.php">Cancel</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<div class="panel">
    <h3>News List</h3>
    <div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Image</th>
                <th>Title EN</th>
                <th>Date</th>
                <th>Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rows as $row): ?>
                <tr>
                    <td><?= (int) $row['id'] ?></td>
                    <td>
                        <?php if (!empty($row['image'])): ?>
                            <img src="<?= e(uploadUrl($row['image'])) ?>" alt="<?= e($row['title_en']) ?>" class="thumb-sm">
                        <?php else: ?>
                            <span class="muted">No image</span>
                        <?php endif; ?>
                    </td>
                    <td><?= e($row['title_en']) ?></td>
                    <td><?= e($row['created_at']) ?></td>
                    <td><?= (int) $row['status'] === 1 ? 'Active' : 'Inactive' ?></td>
                    <td>
                        <div class="action-group">
                        <a class="btn btn-muted" href="news.php?edit=<?= (int) $row['id'] ?>">Edit</a>
                        <form method="post" style="display:inline;" onsubmit="return confirm('Delete this news item?');">
                            <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
                            <button class="btn btn-danger" type="submit">Delete</button>
                        </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>
</div>

<?php include __DIR__ . '/partials/footer.php';
