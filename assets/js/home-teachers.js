jQuery(function ($) {

    console.log('home-teachers.js loaded!');

    /*
        ----------------------------------------------------------------
        Lesson Planning image slideshow (matches home-schools curriculum)
        ----------------------------------------------------------------
    */

    if ($('.home-story-slider__track').length && typeof $.fn.slick === 'function') {
        $('.home-story-slider__track').slick({
            slidesToShow: 1,
            slidesToScroll: 1,
            infinite: true,
            autoplay: true,
            autoplaySpeed: 2000,
            arrows: true,
            dots: false,
            prevArrow: '<button type="button" class="slick-prev slick-arrow" aria-label="Previous slide"><i class="lni lni-arrow-left" aria-hidden="true"></i></button>',
            nextArrow: '<button type="button" class="slick-next slick-arrow" aria-label="Next slide"><i class="lni lni-arrow-right" aria-hidden="true"></i></button>',
        });
    }

    /*
        ----------------------------------------------------------------
        Promo video play (Why Teachers Choose)
        ----------------------------------------------------------------
    */

    const homeIframe = document.getElementById('promo-video');
    if (homeIframe && typeof Vimeo !== 'undefined') {
        const homePlayer = new Vimeo.Player(homeIframe);
        const overlay = document.querySelector('.video-overlay');

        if (overlay) {
            overlay.addEventListener('click', function () {
                homePlayer.play().then(() => {
                    overlay.style.display = 'none';
                }).catch(err => {
                    console.error('Error playing video:', err);
                });
            });

            homePlayer.on('play', () => overlay.style.display = 'none');
        }
    }

});
