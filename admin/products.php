<?php
require_once __DIR__ . '/auth.php';

$adminPage = 'products';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        header('Location: products.php?error=token');
        exit;
    }

    $action = $_POST['action'] ?? 'save';

    if ($action === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);

        $stmt = $pdo->prepare('SELECT image, pdf_th, pdf_en FROM products WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if ($row) {
            $galleryStmt = $pdo->prepare('SELECT image FROM product_images WHERE product_id = ?');
            $galleryStmt->execute([$id]);
            $galleryRows = $galleryStmt->fetchAll();
            foreach ($galleryRows as $galleryRow) {
                removeUpload($galleryRow['image'] ?? '');
            }

            removeUpload($row['image']);
            removeUpload($row['pdf_th']);
            removeUpload($row['pdf_en']);
            $pdo->prepare('DELETE FROM product_images WHERE product_id = ?')->execute([$id]);
            $pdo->prepare('DELETE FROM products WHERE id = ?')->execute([$id]);
        }

        header('Location: products.php?success=deleted');
        exit;
    }

    $id = (int) ($_POST['id'] ?? 0);
    $categoryId = (int) ($_POST['category_id'] ?? 0);

    $nameTh = trim($_POST['name_th'] ?? '');
    $nameEn = trim($_POST['name_en'] ?? '');
    $slug = trim($_POST['slug'] ?? '');
    if ($slug === '') {
        $slug = generateSlug($nameEn ?: $nameTh);
    }

    $shortTh = trim($_POST['short_desc_th'] ?? '');
    $shortEn = trim($_POST['short_desc_en'] ?? '');
    $descTh = sanitizeRichText($_POST['description_th'] ?? '');
    $descEn = sanitizeRichText($_POST['description_en'] ?? '');
    $specTh = trim($_POST['specification_th'] ?? '');
    $specEn = trim($_POST['specification_en'] ?? '');

    $price = parseFloatInput($_POST['price'] ?? 0);
    $featured = parseStatus($_POST['featured'] ?? 0);
    $status = parseStatus($_POST['status'] ?? 1);

    $seoTitle = trim($_POST['seo_title'] ?? '');
    $seoDescription = trim($_POST['seo_description'] ?? '');

    $newImage = saveUpload('image', ['jpg', 'jpeg', 'png', 'webp']);
    $newPdfTh = saveUpload('pdf_th', ['pdf'], 52428800);
    $newPdfEn = saveUpload('pdf_en', ['pdf'], 52428800);

    $imageName = $newImage ?: null;
    $pdfTh = $newPdfTh ?: null;
    $pdfEn = $newPdfEn ?: null;

    $savedProductId = 0;

    if ($id > 0) {
        $oldStmt = $pdo->prepare('SELECT image, pdf_th, pdf_en FROM products WHERE id = ? LIMIT 1');
        $oldStmt->execute([$id]);
        $old = $oldStmt->fetch();

        if (!$imageName) {
            $imageName = $old['image'] ?? '';
        } elseif (!empty($old['image'])) {
            removeUpload($old['image']);
        }

        if (!$pdfTh) {
            $pdfTh = $old['pdf_th'] ?? '';
        } elseif (!empty($old['pdf_th'])) {
            removeUpload($old['pdf_th']);
        }

        if (!$pdfEn) {
            $pdfEn = $old['pdf_en'] ?? '';
        } elseif (!empty($old['pdf_en'])) {
            removeUpload($old['pdf_en']);
        }

        $update = $pdo->prepare('UPDATE products SET category_id = ?, name_th = ?, name_en = ?, slug = ?, short_desc_th = ?, short_desc_en = ?, description_th = ?, description_en = ?, specification_th = ?, specification_en = ?, price = ?, image = ?, pdf_th = ?, pdf_en = ?, featured = ?, status = ?, seo_title = ?, seo_description = ? WHERE id = ?');
        $update->execute([$categoryId ?: null, $nameTh, $nameEn, $slug, $shortTh, $shortEn, $descTh, $descEn, $specTh, $specEn, $price, $imageName, $pdfTh, $pdfEn, $featured, $status, $seoTitle, $seoDescription, $id]);
        $savedProductId = $id;
    } else {
        $insert = $pdo->prepare('INSERT INTO products (category_id, name_th, name_en, slug, short_desc_th, short_desc_en, description_th, description_en, specification_th, specification_en, price, image, pdf_th, pdf_en, featured, status, seo_title, seo_description) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $insert->execute([$categoryId ?: null, $nameTh, $nameEn, $slug, $shortTh, $shortEn, $descTh, $descEn, $specTh, $specEn, $price, $imageName, $pdfTh, $pdfEn, $featured, $status, $seoTitle, $seoDescription]);
        $savedProductId = (int) $pdo->lastInsertId();
    }

    if ($savedProductId > 0) {
        $removeGalleryIds = $_POST['remove_gallery_ids'] ?? [];
        if (is_array($removeGalleryIds) && $removeGalleryIds) {
            $galleryDeleteStmt = $pdo->prepare('SELECT id, image FROM product_images WHERE id = ? AND product_id = ? LIMIT 1');
            $galleryRemoveStmt = $pdo->prepare('DELETE FROM product_images WHERE id = ? AND product_id = ?');
            foreach ($removeGalleryIds as $galleryIdRaw) {
                $galleryId = (int) $galleryIdRaw;
                if ($galleryId <= 0) {
                    continue;
                }

                $galleryDeleteStmt->execute([$galleryId, $savedProductId]);
                $galleryRow = $galleryDeleteStmt->fetch();
                if ($galleryRow) {
                    removeUpload($galleryRow['image'] ?? '');
                    $galleryRemoveStmt->execute([$galleryId, $savedProductId]);
                }
            }
        }

        if (!empty($_FILES['gallery_images']) && is_array($_FILES['gallery_images']['name'] ?? null)) {
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
            $maxGallerySize = 5242880;
            $sortStmt = $pdo->prepare('SELECT COALESCE(MAX(sort_order), 0) FROM product_images WHERE product_id = ?');
            $insertGalleryStmt = $pdo->prepare('INSERT INTO product_images (product_id, image, sort_order) VALUES (?, ?, ?)');

            $sortStmt->execute([$savedProductId]);
            $nextSort = (int) $sortStmt->fetchColumn();

            $galleryNames = $_FILES['gallery_images']['name'];
            $galleryTmpNames = $_FILES['gallery_images']['tmp_name'];
            $galleryErrors = $_FILES['gallery_images']['error'];
            $gallerySizes = $_FILES['gallery_images']['size'];

            foreach ($galleryNames as $index => $originalName) {
                $errorCode = $galleryErrors[$index] ?? UPLOAD_ERR_NO_FILE;
                if ($errorCode === UPLOAD_ERR_NO_FILE || $errorCode !== UPLOAD_ERR_OK) {
                    continue;
                }

                $tmpName = $galleryTmpNames[$index] ?? '';
                $fileSize = (int) ($gallerySizes[$index] ?? 0);
                if ($tmpName === '' || $fileSize <= 0 || $fileSize > $maxGallerySize) {
                    continue;
                }

                $extension = strtolower(pathinfo((string) $originalName, PATHINFO_EXTENSION));
                if (!in_array($extension, $allowedExtensions, true)) {
                    continue;
                }

                if (!is_dir(UPLOAD_PATH)) {
                    mkdir(UPLOAD_PATH, 0755, true);
                }

                $newName = date('YmdHis') . '_' . bin2hex(random_bytes(6)) . '.' . $extension;
                $destination = UPLOAD_PATH . $newName;
                if (!move_uploaded_file($tmpName, $destination)) {
                    continue;
                }

                $nextSort++;
                $insertGalleryStmt->execute([$savedProductId, $newName, $nextSort]);
            }
        }
    }

    header('Location: products.php?success=saved');
    exit;
}

$categories = $pdo->query('SELECT * FROM product_categories ORDER BY id ASC')->fetchAll();
$rows = $pdo->query('SELECT p.*, c.name_en AS category_name, (SELECT image FROM product_images pi WHERE pi.product_id = p.id ORDER BY pi.sort_order ASC, pi.id ASC LIMIT 1) AS gallery_first_image, (SELECT COUNT(*) FROM product_images pi WHERE pi.product_id = p.id) AS gallery_count FROM products p LEFT JOIN product_categories c ON c.id = p.category_id ORDER BY p.id DESC')->fetchAll();

$editId = (int) ($_GET['edit'] ?? 0);
$editing = null;
$editingGallery = [];
if ($editId > 0) {
    $stmt = $pdo->prepare('SELECT * FROM products WHERE id = ? LIMIT 1');
    $stmt->execute([$editId]);
    $editing = $stmt->fetch();

    if ($editing) {
        $galleryStmt = $pdo->prepare('SELECT id, image, sort_order FROM product_images WHERE product_id = ? ORDER BY sort_order ASC, id ASC');
        $galleryStmt->execute([$editId]);
        $editingGallery = $galleryStmt->fetchAll();
    }
}

include __DIR__ . '/partials/header.php';
?>

<div id="form-panel" class="panel" style="<?= $editing ? '' : 'display:none;' ?>">
    <div style="display:flex;justify-content:space-between;align-items:center;">
        <h2><?= $editing ? 'Edit Product' : 'Add Product' ?></h2>
        <button type="button" id="close-form-btn" class="btn btn-muted">Close</button>
    </div>

    <?php if (!empty($_GET['success'])): ?>
        <div class="alert alert-success">Saved successfully.</div>
    <?php endif; ?>
    <?php if (!empty($_GET['error'])): ?>
        <div class="alert alert-error">Request validation failed.</div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data" id="product-form">
        <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
        <input type="hidden" name="id" value="<?= (int) ($editing['id'] ?? 0) ?>">

        <div class="grid-2">
            <div>
                <label>Category</label>
                <select name="category_id">
                    <option value="">-- Select category --</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= (int) $cat['id'] ?>" <?= (int) ($editing['category_id'] ?? 0) === (int) $cat['id'] ? 'selected' : '' ?>><?= e($cat['name_en']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label>Slug (leave blank for auto)</label>
                <input type="text" name="slug" value="<?= e($editing['slug'] ?? '') ?>">
            </div>
            <div>
                <label>Name TH</label>
                <input type="text" name="name_th" value="<?= e($editing['name_th'] ?? '') ?>" required>
            </div>
            <div>
                <label>Name EN</label>
                <input type="text" name="name_en" value="<?= e($editing['name_en'] ?? '') ?>" required>
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
                <label>Specification TH</label>
                <textarea name="specification_th"><?= e($editing['specification_th'] ?? '') ?></textarea>
            </div>
            <div>
                <label>Specification EN</label>
                <textarea name="specification_en"><?= e($editing['specification_en'] ?? '') ?></textarea>
            </div>
            <div>
                <label>Price (THB)</label>
                <input type="number" step="0.01" name="price" value="<?= e($editing['price'] ?? '0') ?>">
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
                <label>Product Image</label>
                <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp">
                <?php if (!empty($editing['image'])): ?>
                    <div class="current-upload">
                        <img src="<?= e(uploadUrl($editing['image'])) ?>" alt="Current product image" class="thumb-sm">
                        <span class="muted">Current main image</span>
                    </div>
                <?php endif; ?>
                <div id="preview-main-upload" class="upload-preview-row"></div>
            </div>
            <div>
                <label>Gallery Images (multiple)</label>
                <button type="button" class="btn btn-secondary" id="add-gallery-btn">+ Add Images</button>
                <small>Click button to select and add images. You can add multiple times.</small>
                <input type="file" name="gallery_images_select" id="gallery-images-input" accept=".jpg,.jpeg,.png,.webp" multiple style="display:none;">
                <div id="preview-gallery-upload" class="upload-preview-row"></div>
            </div>
            <div>
                <label>PDF TH</label>
                <input type="file" name="pdf_th" accept=".pdf">
                <?php if (!empty($editing['pdf_th'])): ?>
                    <div class="current-upload"><a href="<?= e(uploadUrl($editing['pdf_th'])) ?>" target="_blank" rel="noopener">Current PDF TH</a></div>
                <?php endif; ?>
            </div>
            <div>
                <label>PDF EN</label>
                <input type="file" name="pdf_en" accept=".pdf">
                <?php if (!empty($editing['pdf_en'])): ?>
                    <div class="current-upload"><a href="<?= e(uploadUrl($editing['pdf_en'])) ?>" target="_blank" rel="noopener">Current PDF EN</a></div>
                <?php endif; ?>
            </div>
            <div>
                <label>Featured</label>
                <select name="featured">
                    <option value="1" <?= (int) ($editing['featured'] ?? 0) === 1 ? 'selected' : '' ?>>Yes</option>
                    <option value="0" <?= (int) ($editing['featured'] ?? 0) === 0 ? 'selected' : '' ?>>No</option>
                </select>
            </div>
            <div>
                <label>Status</label>
                <select name="status">
                    <option value="1" <?= (int) ($editing['status'] ?? 1) === 1 ? 'selected' : '' ?>>Active</option>
                    <option value="0" <?= (int) ($editing['status'] ?? 1) === 0 ? 'selected' : '' ?>>Inactive</option>
                </select>
            </div>
        </div>

        <?php if ($editing && $editingGallery): ?>
            <div style="margin-top:1rem;">
                <label>Current Gallery Images (tick to remove)</label>
                <div style="display:flex;flex-wrap:wrap;gap:0.7rem;">
                    <?php foreach ($editingGallery as $galleryItem): ?>
                        <label style="display:flex;flex-direction:column;align-items:center;gap:0.4rem;background:#151515;border:1px solid #333;border-radius:10px;padding:0.55rem;">
                            <img src="<?= e(uploadUrl($galleryItem['image'])) ?>" alt="Gallery image" style="width:90px;height:90px;object-fit:cover;border-radius:8px;">
                            <small>#<?= (int) $galleryItem['sort_order'] ?></small>
                            <span><input type="checkbox" name="remove_gallery_ids[]" value="<?= (int) $galleryItem['id'] ?>"> Remove</span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <div style="margin-top:0.8rem;">
            <button class="btn btn-primary" type="submit">Save Product</button>
            <button type="button" class="btn btn-muted" id="close-form-btn-2">Cancel</button>
        </div>
    </form>
</div>

<div class="panel">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;gap:1rem;flex-wrap:wrap;">
        <h3>Product List</h3>
        <div style="display:flex;gap:0.5rem;flex-wrap:wrap;">
            <input type="text" id="search-products" placeholder="Search products..." style="padding:0.6rem;border:1px solid #444;border-radius:5px;background:#1a1a1a;color:#fff;flex:1;min-width:200px;">
            <button type="button" class="btn btn-primary" id="add-product-btn">+ Add Product</button>
        </div>
    </div>
    <div class="table-wrap">
    <table id="products-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Image</th>
                <th>Name EN</th>
                <th>Category</th>
                <th>Price</th>
                <th>Gallery</th>
                <th>Featured</th>
                <th>Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rows as $row): ?>
                <?php
                $mainImageUrl = !empty($row['image']) ? uploadUrl($row['image']) : '';
                $galleryImageUrl = !empty($row['gallery_first_image']) ? uploadUrl($row['gallery_first_image']) : '';
                ?>
                <tr>
                    <td><?= (int) $row['id'] ?></td>
                    <td>
                        <?php if ($mainImageUrl): ?>
                            <img src="<?= e($mainImageUrl) ?>" alt="<?= e($row['name_en']) ?>" class="thumb-sm">
                        <?php else: ?>
                            <span class="muted">No image</span>
                        <?php endif; ?>
                    </td>
                    <td><?= e($row['name_en']) ?></td>
                    <td><?= e($row['category_name'] ?? '-') ?></td>
                    <td><?= e(formatPrice($row['price'])) ?></td>
                    <td>
                        <div class="thumb-stack">
                            <?php if ($galleryImageUrl): ?>
                                <img src="<?= e($galleryImageUrl) ?>" alt="Gallery preview" class="thumb-sm">
                            <?php endif; ?>
                            <span><?= (int) ($row['gallery_count'] ?? 0) ?> images</span>
                        </div>
                    </td>
                    <td><?= (int) $row['featured'] === 1 ? 'Yes' : 'No' ?></td>
                    <td><?= (int) $row['status'] === 1 ? 'Active' : 'Inactive' ?></td>
                    <td>
                        <div class="action-group">
                        <a class="btn btn-muted edit-product-btn" href="products.php?edit=<?= (int) $row['id'] ?>" data-edit-id="<?= (int) $row['id'] ?>">Edit</a>
                        <form method="post" style="display:inline;" class="delete-product-form" data-product-name="<?= e($row['name_en']) ?>">
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

<link rel="stylesheet" href="<?= e(baseUrl('assets/css/quill.snow.css')) ?>">
<link rel="stylesheet" href="<?= e(baseUrl('assets/css/quill-better-table.min.css')) ?>">
<style>
.quill-editor {
    min-height: 320px;
    margin-bottom: 0.5rem;
}
</style>
<script src="<?= e(baseUrl('assets/js/quill.min.js')) ?>"></script>
<script src="<?= e(baseUrl('assets/js/quill-better-table.min.js')) ?>"></script>
<script>
(() => {
    const formPanel = document.getElementById('form-panel');
    const productForm = document.getElementById('product-form');
    const addProductBtn = document.getElementById('add-product-btn');
    const closeFormBtn = document.getElementById('close-form-btn');
    const searchInput = document.getElementById('search-products');
    const productsTable = document.getElementById('products-table');
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
    
    // Show form when Add Product is clicked
    if (addProductBtn) {
        addProductBtn.addEventListener('click', () => {
            // Reset form
            if (productForm) {
                productForm.reset();
            }
            // Clear edit mode
            const editIdInput = productForm?.querySelector('input[name="id"]');
            if (editIdInput) {
                editIdInput.value = '0';
            }
            clearQuillEditors();
            // Reset gallery
            selectedGalleryFiles = [];
            updateGalleryPreview();
            // Show form panel
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
    
    // Close form button 2 (in buttons)
    const closeFormBtn2 = document.getElementById('close-form-btn-2');
    if (closeFormBtn2) {
        closeFormBtn2.addEventListener('click', () => {
            if (formPanel) {
                formPanel.style.display = 'none';
            }
        });
    }
    
    // Handle edit links
    document.addEventListener('click', (e) => {
        if (e.target.classList.contains('edit-product-btn')) {
            e.preventDefault();
            const editId = e.target.dataset.editId;
            // Navigate to edit mode
            window.location.href = 'products.php?edit=' + editId;
        }
    });

    // Confirm product deletion before submit
    document.addEventListener('submit', (e) => {
        const deleteForm = e.target.closest('.delete-product-form');
        if (!deleteForm) {
            return;
        }

        const productName = (deleteForm.dataset.productName || '').trim();
        const message = productName
            ? 'Delete product "' + productName + '"?'
            : 'Delete this product?';

        if (!window.confirm(message)) {
            e.preventDefault();
        }
    });
    
    // Search functionality
    if (searchInput && productsTable) {
        searchInput.addEventListener('keyup', (e) => {
            const searchTerm = e.target.value.toLowerCase();
            const rows = productsTable.querySelectorAll('tbody tr');
            
            rows.forEach((row) => {
                const nameCell = row.querySelector('td:nth-child(3)'); // Name EN column
                const categoryCell = row.querySelector('td:nth-child(4)'); // Category column
                const idCell = row.querySelector('td:nth-child(1)'); // ID column
                
                const name = nameCell ? nameCell.textContent.toLowerCase() : '';
                const category = categoryCell ? categoryCell.textContent.toLowerCase() : '';
                const id = idCell ? idCell.textContent.toLowerCase() : '';
                
                if (name.includes(searchTerm) || category.includes(searchTerm) || id.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }
    
    // Handle main image preview
    const mainInput = document.querySelector('input[name="image"]');
    const mainPreview = document.getElementById('preview-main-upload');
    if (mainInput && mainPreview) {
        mainInput.addEventListener('change', () => {
            mainPreview.innerHTML = '';
            const file = mainInput.files && mainInput.files[0];
            if (!file) {
                return;
            }

            const url = URL.createObjectURL(file);
            const card = document.createElement('div');
            card.className = 'upload-preview-card';
            const img = document.createElement('img');
            img.src = url;
            img.alt = 'Main image preview';
            card.appendChild(img);
            mainPreview.appendChild(card);
        });
    }

    // Handle gallery images with incremental selection
    const galleryInput = document.getElementById('gallery-images-input');
    const addGalleryBtn = document.getElementById('add-gallery-btn');
    const galleryPreview = document.getElementById('preview-gallery-upload');
    
    let selectedGalleryFiles = [];
    
    if (addGalleryBtn && galleryInput) {
        addGalleryBtn.addEventListener('click', (e) => {
            e.preventDefault();
            galleryInput.click();
        });
    }
    
    if (galleryInput && galleryPreview) {
        galleryInput.addEventListener('change', () => {
            const files = galleryInput.files ? Array.from(galleryInput.files) : [];
            
            // Add new files to the selected files array
            files.forEach((file) => {
                selectedGalleryFiles.push({
                    file: file,
                    url: URL.createObjectURL(file)
                });
            });
            
            // Update preview
            updateGalleryPreview();
            
            // Reset the input so the same file can be selected again
            galleryInput.value = '';
        });
    }
    
    function updateGalleryPreview() {
        if (!galleryPreview) return;
        galleryPreview.innerHTML = '';
        selectedGalleryFiles.forEach((item, index) => {
            const card = document.createElement('div');
            card.className = 'upload-preview-card';
            card.style.position = 'relative';
            
            const img = document.createElement('img');
            img.src = item.url;
            img.alt = 'Gallery image preview';
            card.appendChild(img);
            
            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'btn btn-danger';
            removeBtn.style.position = 'absolute';
            removeBtn.style.top = '5px';
            removeBtn.style.right = '5px';
            removeBtn.style.padding = '2px 6px';
            removeBtn.style.fontSize = '12px';
            removeBtn.textContent = '✕';
            removeBtn.addEventListener('click', (e) => {
                e.preventDefault();
                selectedGalleryFiles.splice(index, 1);
                updateGalleryPreview();
            });
            card.appendChild(removeBtn);
            
            galleryPreview.appendChild(card);
        });
    }
    
    // Handle form submission with collected gallery files
    if (productForm) {
        productForm.addEventListener('submit', (e) => {
            syncQuillEditors();

            if (selectedGalleryFiles.length === 0) {
                // No gallery files selected, allow normal submission
                return;
            }
            
            // Prevent normal form submission
            e.preventDefault();
            
            // Create FormData from form
            const formData = new FormData(productForm);
            
            // Remove any existing gallery_images entries
            formData.delete('gallery_images[]');
            
            // Add all selected gallery files
            selectedGalleryFiles.forEach((item) => {
                formData.append('gallery_images[]', item.file);
            });
            
            // Submit via fetch
            fetch(productForm.action || 'products.php', {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                // Follow redirect if present
                if (response.redirected) {
                    window.location.href = response.url;
                } else {
                    // If no redirect, reload or navigate
                    window.location.href = 'products.php?success=saved';
                }
            })
            .catch((error) => {
                console.error('Form submission error:', error);
                alert('An error occurred while saving. Please try again.');
            });
        });
    }
})();
</script>

<?php include __DIR__ . '/partials/footer.php';
