<?php
require_once __DIR__ . '/auth.php';

$adminPage = 'categories';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        header('Location: categories.php?error=token');
        exit;
    }

    $action = $_POST['action'] ?? 'save';

    if ($action === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);

        $check = $pdo->prepare('SELECT COUNT(*) FROM products WHERE category_id = ?');
        $check->execute([$id]);
        $inUse = (int) $check->fetchColumn();

        if ($inUse > 0) {
            header('Location: categories.php?error=in-use');
            exit;
        }

        $pdo->prepare('DELETE FROM product_categories WHERE id = ?')->execute([$id]);
        header('Location: categories.php?success=deleted');
        exit;
    }

    $id = (int) ($_POST['id'] ?? 0);
    $nameTh = trim($_POST['name_th'] ?? '');
    $nameEn = trim($_POST['name_en'] ?? '');
    $slug = trim($_POST['slug'] ?? '');
    if ($slug === '') {
        $slug = generateSlug($nameEn ?: $nameTh);
    }
    $status = parseStatus($_POST['status'] ?? 1);

    if ($nameTh === '' || $nameEn === '') {
        header('Location: categories.php?error=required');
        exit;
    }

    if ($id > 0) {
        $stmt = $pdo->prepare('UPDATE product_categories SET name_th = ?, name_en = ?, slug = ?, status = ? WHERE id = ?');
        $stmt->execute([$nameTh, $nameEn, $slug, $status, $id]);
    } else {
        $stmt = $pdo->prepare('INSERT INTO product_categories (name_th, name_en, slug, status) VALUES (?, ?, ?, ?)');
        $stmt->execute([$nameTh, $nameEn, $slug, $status]);
    }

    header('Location: categories.php?success=saved');
    exit;
}

$editId = (int) ($_GET['edit'] ?? 0);
$editing = null;
if ($editId > 0) {
    $stmt = $pdo->prepare('SELECT * FROM product_categories WHERE id = ? LIMIT 1');
    $stmt->execute([$editId]);
    $editing = $stmt->fetch();
}

$rows = $pdo->query('SELECT c.*, (SELECT COUNT(*) FROM products p WHERE p.category_id = c.id) AS product_count FROM product_categories c ORDER BY c.id DESC')->fetchAll();

include __DIR__ . '/partials/header.php';
?>

<div class="panel">
    <h2><?= $editing ? 'Edit Category' : 'Add Category' ?></h2>

    <?php if (!empty($_GET['success'])): ?>
        <div class="alert alert-success">Saved successfully.</div>
    <?php endif; ?>
    <?php if (!empty($_GET['error'])): ?>
        <div class="alert alert-error">
            <?php if ($_GET['error'] === 'in-use'): ?>
                Category cannot be deleted because it is used by products.
            <?php else: ?>
                Please complete required fields or check request token.
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <form method="post">
        <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
        <input type="hidden" name="id" value="<?= (int) ($editing['id'] ?? 0) ?>">

        <div class="grid-2">
            <div>
                <label>Name TH</label>
                <input type="text" name="name_th" value="<?= e($editing['name_th'] ?? '') ?>" required>
            </div>
            <div>
                <label>Name EN</label>
                <input type="text" name="name_en" value="<?= e($editing['name_en'] ?? '') ?>" required>
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
        </div>

        <div style="margin-top:0.8rem;">
            <button class="btn btn-primary" type="submit">Save Category</button>
            <?php if ($editing): ?>
                <a class="btn btn-muted" href="categories.php">Cancel</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<div class="panel">
    <h3>Category List</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name EN</th>
                <th>Name TH</th>
                <th>Slug</th>
                <th>Products</th>
                <th>Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rows as $row): ?>
                <tr>
                    <td><?= (int) $row['id'] ?></td>
                    <td><?= e($row['name_en']) ?></td>
                    <td><?= e($row['name_th']) ?></td>
                    <td><?= e($row['slug']) ?></td>
                    <td><?= (int) $row['product_count'] ?></td>
                    <td><?= (int) $row['status'] === 1 ? 'Active' : 'Inactive' ?></td>
                    <td>
                        <a class="btn btn-muted" href="categories.php?edit=<?= (int) $row['id'] ?>">Edit</a>
                        <form method="post" style="display:inline;" onsubmit="return confirm('Delete this category?');">
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
