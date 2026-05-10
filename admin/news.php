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
    $descTh = sanitizeRichText($_POST['description_th'] ?? '');
    $descEn = sanitizeRichText($_POST['description_en'] ?? '');

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

<div id="form-panel" class="panel" style="<?= $editing ? '' : 'display:none;' ?>">
    <div style="display:flex;justify-content:space-between;align-items:center;">
        <h2><?= $editing ? 'Edit News' : 'Add News' ?></h2>
        <button type="button" id="close-form-btn" class="btn btn-muted">Close</button>
    </div>

    <?php if (!empty($_GET['success'])): ?>
        <div class="alert alert-success">Saved successfully.</div>
    <?php endif; ?>
    <?php if (!empty($_GET['error'])): ?>
        <div class="alert alert-error">Request validation failed.</div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data" id="news-form">
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
                <textarea id="description_th" name="description_th" class="quill-source" style="display:none;"><?= e($editing['description_th'] ?? '') ?></textarea>
                <div id="quill-editor-th" class="quill-editor"></div>
            </div>
            <div>
                <label>Description EN</label>
                <textarea id="description_en" name="description_en" class="quill-source" style="display:none;"><?= e($editing['description_en'] ?? '') ?></textarea>
                <div id="quill-editor-en" class="quill-editor"></div>
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
            <button type="button" class="btn btn-muted" id="close-form-btn-2">Cancel</button>
        </div>
    </form>
</div>

<div class="panel">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;gap:1rem;flex-wrap:wrap;">
        <h3>News List</h3>
        <div style="display:flex;gap:0.5rem;flex-wrap:wrap;">
            <input type="text" id="search-news" placeholder="Search news..." style="padding:0.6rem;border:1px solid #444;border-radius:5px;background:#1a1a1a;color:#fff;flex:1;min-width:200px;">
            <button type="button" class="btn btn-primary" id="add-news-btn">+ Add News</button>
        </div>
    </div>
    <div class="table-wrap">
    <table id="news-table">
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
                        <a class="btn btn-muted edit-news-btn" href="news.php?edit=<?= (int) $row['id'] ?>" data-edit-id="<?= (int) $row['id'] ?>">Edit</a>
                        <form method="post" style="display:inline;" class="delete-news-form" data-news-title="<?= e($row['title_en']) ?>">
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

<link rel="stylesheet" href="../assets/css/quill.snow.css">
<link rel="stylesheet" href="../assets/css/quill-better-table.min.css">
<style>
.quill-editor {
    min-height: 320px;
    margin-bottom: 0.5rem;
}
</style>
<script src="../assets/js/quill.min.js"></script>
<script src="../assets/js/quill-better-table.min.js"></script>
<script>
(() => {
    const formPanel = document.getElementById('form-panel');
    const newsForm = document.getElementById('news-form');
    const addNewsBtn = document.getElementById('add-news-btn');
    const closeFormBtn = document.getElementById('close-form-btn');
    const closeFormBtn2 = document.getElementById('close-form-btn-2');
    const searchInput = document.getElementById('search-news');
    const newsTable = document.getElementById('news-table');
    let quillTH, quillEN;

    function initQuillEditors() {
        if (typeof Quill === 'undefined') return;
        var BetterTable = window.quillBetterTable || window.QuillBetterTable;
        if (BetterTable) {
            Quill.register({'modules/better-table': BetterTable}, true);
        }

        var editorModules = {
            toolbar: [
                [{ 'header': [2, 3, 4, false] }],
                ['bold', 'italic', 'underline'],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                ['link'],
                ['clean']
            ]
        };

        try {
            quillTH = new Quill('#quill-editor-th', { theme: 'snow', modules: editorModules });
        } catch(e) { console.error('QuillTH init error:', e); }

        try {
            quillEN = new Quill('#quill-editor-en', { theme: 'snow', modules: editorModules });
        } catch(e) { console.error('QuillEN init error:', e); }

        // Set initial content
        const descTH = document.getElementById('description_th');
        if (descTH && descTH.value) {
            try { quillTH.root.innerHTML = descTH.value; } catch(e) {}
        }
        const descEN = document.getElementById('description_en');
        if (descEN && descEN.value) {
            try { quillEN.root.innerHTML = descEN.value; } catch(e) {}
        }
    }

    function syncQuillEditors() {
        if (quillTH) {
            document.getElementById('description_th').value = quillTH.root.innerHTML.trim();
        }
        if (quillEN) {
            document.getElementById('description_en').value = quillEN.root.innerHTML.trim();
        }
    }

    function clearQuillEditors() {
        if (quillTH) quillTH.setContents([]);
        if (quillEN) quillEN.setContents([]);
        if (document.getElementById('description_th')) document.getElementById('description_th').value = '';
        if (document.getElementById('description_en')) document.getElementById('description_en').value = '';
    }

    // Scripts are loaded at bottom of page, DOM is ready - call directly
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initQuillEditors);
    } else {
        initQuillEditors();
    }

    // Show form when Add News is clicked
    if (addNewsBtn) {
        addNewsBtn.addEventListener('click', () => {
            if (newsForm) {
                newsForm.reset();
            }
            const editIdInput = newsForm?.querySelector('input[name="id"]');
            if (editIdInput) {
                editIdInput.value = '0';
            }
            clearQuillEditors();
            if (formPanel) {
                formPanel.style.display = 'block';
                formPanel.scrollIntoView({ behavior: 'smooth' });
            }
        });
    }

    // Close form
    if (closeFormBtn) {
        closeFormBtn.addEventListener('click', () => {
            if (formPanel) {
                formPanel.style.display = 'none';
            }
        });
    }

    if (closeFormBtn2) {
        closeFormBtn2.addEventListener('click', () => {
            if (formPanel) {
                formPanel.style.display = 'none';
            }
        });
    }

    // Handle edit links
    document.addEventListener('click', (e) => {
        if (e.target.classList.contains('edit-news-btn')) {
            e.preventDefault();
            const editId = e.target.dataset.editId;
            window.location.href = 'news.php?edit=' + editId;
        }
    });

    // Confirm news deletion before submit
    document.addEventListener('submit', (e) => {
        const deleteForm = e.target.closest('.delete-news-form');
        if (deleteForm) {
            const newsTitle = (deleteForm.dataset.newsTitle || '').trim();
            const message = newsTitle
                ? 'Delete news "' + newsTitle + '"?'
                : 'Delete this news item?';

            if (!window.confirm(message)) {
                e.preventDefault();
            }
        }
    });

    // Sync Quill editors before news form submit
    if (newsForm) {
        newsForm.addEventListener('submit', (e) => {
            syncQuillEditors();
        });
    }

    // Search functionality
    if (searchInput && newsTable) {
        searchInput.addEventListener('keyup', (e) => {
            const searchTerm = e.target.value.toLowerCase();
            const rows = newsTable.querySelectorAll('tbody tr');

            rows.forEach((row) => {
                const titleCell = row.querySelector('td:nth-child(3)'); // Title EN
                const dateCell = row.querySelector('td:nth-child(4)'); // Date
                const idCell = row.querySelector('td:nth-child(1)'); // ID

                const title = titleCell ? titleCell.textContent.toLowerCase() : '';
                const date = dateCell ? dateCell.textContent.toLowerCase() : '';
                const id = idCell ? idCell.textContent.toLowerCase() : '';

                if (title.includes(searchTerm) || date.includes(searchTerm) || id.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }
})();
</script>

<?php include __DIR__ . '/partials/footer.php';
