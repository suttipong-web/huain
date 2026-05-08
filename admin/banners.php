<?php
require_once __DIR__ . '/auth.php';

$adminPage = 'banners';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        header('Location: banners.php?error=token');
        exit;
    }

    $action = $_POST['action'] ?? 'save';

    if ($action === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);
        $stmt = $pdo->prepare('SELECT image FROM banners WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if ($row) {
            removeUpload($row['image']);
            $del = $pdo->prepare('DELETE FROM banners WHERE id = ?');
            $del->execute([$id]);
        }

        header('Location: banners.php?success=deleted');
        exit;
    }

    $id = (int) ($_POST['id'] ?? 0);
    $titleEn = trim($_POST['title_en'] ?? '');
    $titleTh = trim($_POST['title_th'] ?? '');
    $subtitleEn = trim($_POST['subtitle_en'] ?? '');
    $subtitleTh = trim($_POST['subtitle_th'] ?? '');
    $linkUrl = trim($_POST['link_url'] ?? '');
    $sortOrder = (int) ($_POST['sort_order'] ?? 0);
    $status = parseStatus($_POST['status'] ?? 1);

    $newImage = saveUpload('image', ['jpg', 'jpeg', 'png', 'webp']);
    $imageName = $newImage;

    if ($id > 0) {
        $oldStmt = $pdo->prepare('SELECT image FROM banners WHERE id = ? LIMIT 1');
        $oldStmt->execute([$id]);
        $old = $oldStmt->fetch();

        if (!$imageName) {
            $imageName = $old['image'] ?? '';
        } elseif (!empty($old['image'])) {
            removeUpload($old['image']);
        }

        $stmt = $pdo->prepare('UPDATE banners SET title_en = ?, title_th = ?, subtitle_en = ?, subtitle_th = ?, image = ?, link_url = ?, sort_order = ?, status = ? WHERE id = ?');
        $stmt->execute([$titleEn, $titleTh, $subtitleEn, $subtitleTh, $imageName, $linkUrl, $sortOrder, $status, $id]);
    } else {
        $stmt = $pdo->prepare('INSERT INTO banners (title_en, title_th, subtitle_en, subtitle_th, image, link_url, sort_order, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([$titleEn, $titleTh, $subtitleEn, $subtitleTh, $imageName, $linkUrl, $sortOrder, $status]);
    }

    header('Location: banners.php?success=saved');
    exit;
}

$editId = (int) ($_GET['edit'] ?? 0);
$editing = null;
if ($editId > 0) {
    $stmt = $pdo->prepare('SELECT * FROM banners WHERE id = ? LIMIT 1');
    $stmt->execute([$editId]);
    $editing = $stmt->fetch();
}

$rows = $pdo->query('SELECT * FROM banners ORDER BY sort_order ASC, id DESC')->fetchAll();

include __DIR__ . '/partials/header.php';
?>

<div class="panel">
    <h2><?= $editing ? 'Edit Banner' : 'Add Banner' ?></h2>

    <?php if (!empty($_GET['success'])): ?>
        <div class="alert alert-success">Saved successfully.</div>
    <?php endif; ?>
    <?php if (!empty($_GET['error'])): ?>
        <div class="alert alert-error">Request validation failed.</div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
        <input type="hidden" name="id" value="<?= (int) ($editing['id'] ?? 0) ?>">

        <div class="grid-2">
            <div>
                <label>Title (EN)</label>
                <input type="text" name="title_en" value="<?= e($editing['title_en'] ?? '') ?>">
            </div>
            <div>
                <label>Title (TH)</label>
                <input type="text" name="title_th" value="<?= e($editing['title_th'] ?? '') ?>">
            </div>
            <div>
                <label>Subtitle (EN)</label>
                <textarea name="subtitle_en"><?= e($editing['subtitle_en'] ?? '') ?></textarea>
            </div>
            <div>
                <label>Subtitle (TH)</label>
                <textarea name="subtitle_th"><?= e($editing['subtitle_th'] ?? '') ?></textarea>
            </div>
            <div>
                <label>Link URL</label>
                <input type="text" name="link_url" value="<?= e($editing['link_url'] ?? '') ?>" placeholder="/products/hdmi-matrix or https://...">
            </div>
            <div>
                <label>Sort Order</label>
                <input type="number" name="sort_order" value="<?= (int) ($editing['sort_order'] ?? 0) ?>">
            </div>
            <div>
                <label>Status</label>
                <select name="status">
                    <option value="1" <?= (int) ($editing['status'] ?? 1) === 1 ? 'selected' : '' ?>>Active</option>
                    <option value="0" <?= (int) ($editing['status'] ?? 1) === 0 ? 'selected' : '' ?>>Inactive</option>
                </select>
            </div>
            <div>
                <label>Banner Image</label>
                <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp">
                <small>Recommended size: 1600x900</small>
            </div>
        </div>

        <div style="margin-top:0.8rem;">
            <button class="btn btn-primary" type="submit">Save Banner</button>
            <?php if ($editing): ?>
                <a class="btn btn-muted" href="banners.php">Cancel</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<div class="panel">
    <h3>Banner List</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Title EN</th>
                <th>Link</th>
                <th>Image</th>
                <th>Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rows as $row): ?>
                <tr>
                    <td><?= (int) $row['id'] ?></td>
                    <td><?= e($row['title_en']) ?></td>
                    <td><?= e($row['link_url']) ?></td>
                    <td><?= $row['image'] ? '<small>' . e($row['image']) . '</small>' : '-' ?></td>
                    <td><?= (int) $row['status'] === 1 ? 'Active' : 'Inactive' ?></td>
                    <td>
                        <a class="btn btn-muted" href="banners.php?edit=<?= (int) $row['id'] ?>">Edit</a>
                        <form method="post" style="display:inline;" onsubmit="return confirm('Delete this banner?');">
                            <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
                            <button class="btn btn-danger" type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/partials/footer.php';
