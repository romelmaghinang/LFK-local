jQuery(function ($) {

    console.log('home-2.js loaded!');

    // Focus library search on desktop
    if (window.innerWidth > 1000) {
        $('#lib-search').focus();
    }

    /*
        ----------------------------------------------------------------
        Home language flag slider (Row 1)
        ----------------------------------------------------------------
    */

    if ($('.home-lang-slider__track').length && typeof $.fn.slick === 'function') {
        $('.home-lang-slider__track').slick({
            slidesToShow: 5,
            slidesToScroll: 5,
            infinite: true,
            autoplay: true,
            autoplaySpeed: 2000,
            arrows: true,
            dots: false,
            prevArrow: '<button type="button" class="slick-prev slick-arrow" aria-label="Previous slide"><i class="lni lni-arrow-left" aria-hidden="true"></i></button>',
            nextArrow: '<button type="button" class="slick-next slick-arrow" aria-label="Next slide"><i class="lni lni-arrow-right" aria-hidden="true"></i></button>',
            responsive: [
                { breakpoint: 800, settings: { slidesToShow: 4, slidesToScroll: 4 } },
                { breakpoint: 480, settings: { slidesToShow: 3, slidesToScroll: 3 } },
            ]
        });
    }

    /*
        ----------------------------------------------------------------
        Stats counter animation (Row 1)
        ----------------------------------------------------------------
    */

    function animateCounters() {
        $('.count').each(function () {
            var $this = $(this);
            if ($this.data('animated')) return;
            $this.data('animated', true);

            var final = $this.data('final');
            var suffix = $this.data('suffix') || '';
            var num = parseFloat(final);
            var decimals = (num.toString().split('.')[1] || '').length;

            $({ n: 0 }).animate({ n: num }, {
                duration: 1500,
                easing: 'swing',
                step: function (val) { $this.text(val.toFixed(decimals) + suffix + '+'); },
                complete: function () { $this.text(num.toFixed(decimals) + suffix + '+'); }
            });
        });
    }

    if ('IntersectionObserver' in window) {
        const counterObserver = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) animateCounters();
            });
        }, { threshold: 0.3 });

        document.querySelectorAll('.count').forEach(function (el) {
            counterObserver.observe(el);
        });
    }

    /*
        ----------------------------------------------------------------
        Library / School search
        ----------------------------------------------------------------
    */

    let activeSugIndex = -1;

    function updateActiveSug(items) {
        items.removeClass('is-active');
        if (activeSugIndex >= 0 && activeSugIndex < items.length) {
            items.eq(activeSugIndex).addClass('is-active');
        }
    }

    function selectSuggestion($item) {
        $('#lib-search').val($item.text());
        $('.search-btn').attr('href', $item.attr('data-url'));
        $('#suggestions').empty();
        activeSugIndex = -1;
    }

    $('#lib-search').on('keyup', function (e) {
        if ([9, 13, 38, 40].includes(e.keyCode)) return;

        $('.search-txt').removeClass('not-found');
        $('.search-btn').attr('href', '');
        activeSugIndex = -1;

        let q = $(this).val().toLowerCase().trim();
        let $sug = $('#suggestions');
        $sug.empty();

        if (q.length < 3) return;

        $('#lib-list li').each(function () {
            let text = $(this).text();
            let url = $(this).attr('data-url');
            if (text.toLowerCase().indexOf(q) > -1) {
                $sug.append('<li data-url="' + url + '">' + text + '</li>');
            }
        });
    });

    $('#lib-search').on('keydown', function (e) {
        let $items = $('#suggestions li');

        if (e.keyCode === 13) {
            e.preventDefault();
            if (activeSugIndex >= 0 && $items.length) {
                selectSuggestion($items.eq(activeSugIndex));
            } else {
                let href = $('.search-btn').attr('href');
                if (href) {
                    window.location.href = href;
                } else {
                    $('.search-txt').addClass('not-found');
                }
            }
            return;
        }

        if (!$items.length) return;

        if (e.keyCode === 9 || e.keyCode === 40) {
            e.preventDefault();
            activeSugIndex = (activeSugIndex + 1) % $items.length;
            updateActiveSug($items);
        } else if (e.keyCode === 38) {
            e.preventDefault();
            activeSugIndex = (activeSugIndex - 1 + $items.length) % $items.length;
            updateActiveSug($items);
        }
    });

    $(document).on('click', '#suggestions li', function () {
        selectSuggestion($(this));
    });

    $('.search-btn').on('click', function (e) {
        e.preventDefault();
        if ($('.search-btn').attr('href') == '') {
            $('.search-txt').addClass('not-found');
            $('#lib-search').focus();
        } else {
            window.location.href = $('.search-btn').attr('href');
        }
    });

    /*
        ----------------------------------------------------------------
        Stories to Life carousel (Row 3)
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

});
