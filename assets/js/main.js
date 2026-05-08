(() => {
    const hero = document.querySelector('.hero-swiper');
    if (hero) {
        new Swiper('.hero-swiper', {
            loop: true,
            effect: 'fade',
            speed: 850,
            autoplay: {
                delay: 5000,
                disableOnInteraction: false,
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
        });
    }
})();

(() => {
    const gallery = document.querySelector('.js-product-gallery');
    if (!gallery) {
        return;
    }

    const mainImage = gallery.querySelector('#product-main-image');
    const thumbs = gallery.querySelectorAll('.product-thumb[data-image]');
    if (!mainImage || thumbs.length === 0) {
        return;
    }

    thumbs.forEach((thumb) => {
        thumb.addEventListener('click', () => {
            const imageUrl = thumb.getAttribute('data-image');
            if (!imageUrl) {
                return;
            }

            mainImage.src = imageUrl;
            thumbs.forEach((item) => item.classList.remove('is-active'));
            thumb.classList.add('is-active');
        });
    });
})();
