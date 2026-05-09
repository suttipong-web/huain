(() => {
    const hero = document.querySelector('.hero-swiper');
    if (hero) {
        new Swiper('.hero-swiper', {
            loop: true,
            effect: 'fade',
            speed: 1200,
            fadeEffect: {
                crossFade: true,
            },
            autoplay: {
                delay: 5600,
                disableOnInteraction: false,
            },
            allowTouchMove: true,
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            on: {
                init(swiper) {
                    const activeSlide = swiper.slides[swiper.activeIndex];
                    if (activeSlide) {
                        activeSlide.classList.add('is-animated');
                    }
                },
                slideChangeTransitionStart(swiper) {
                    swiper.slides.forEach((slide) => slide.classList.remove('is-animated'));
                },
                slideChangeTransitionEnd(swiper) {
                    const activeSlide = swiper.slides[swiper.activeIndex];
                    if (activeSlide) {
                        activeSlide.classList.add('is-animated');
                    }
                },
            },
        });
    }
})();

(() => {
    const revealItems = document.querySelectorAll('.reveal-up');
    if (revealItems.length === 0) {
        return;
    }

    const revealObserver = new IntersectionObserver(
        (entries, observer) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) {
                    return;
                }

                entry.target.classList.add('is-visible');
                observer.unobserve(entry.target);
            });
        },
        {
            threshold: 0.16,
            rootMargin: '0px 0px -8% 0px',
        }
    );

    revealItems.forEach((item) => revealObserver.observe(item));
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
