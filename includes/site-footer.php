</main>
<footer class="site-footer">
    <div class="container footer-grid">
        <div>
            <h5>HUAIN Thailand</h5>
            <p>Professional AV solutions for enterprise, education, hospitality, and command center projects.</p>
        </div>
        <div>
            <h6>Quick links</h6>
            <a href="<?= e(productsUrl()) ?>">Products</a>
            <a href="<?= e(newsListUrl()) ?>">News</a>
            <a href="<?= e(contactUrl()) ?>">Contact</a>
            <a href="<?= e(baseUrl('admin/login.php')) ?>">Admin</a>
        </div>
        <div>
            <h6>Domain</h6>
            <p>https://huain-th.com</p>
            <small>© <?= date('Y') ?> HUAIN Thailand</small>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script src="<?= e(baseUrl('assets/js/main.js')) ?>"></script>
</body>
</html>
