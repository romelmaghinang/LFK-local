jQuery(function ($) {
    
    console.log('member-home.js loaded!');

    /* 
        ----------------------------------------------------------------
        Sort 
        ----------------------------------------------------------------
    */

    const $langWrap = $('.lang-wrap');
    const $sortLinks = $('.sort a');
    const $searchInput = $('.search input'); // adjust selector if needed

    // keep a copy of the original order
    const originalOrder = $langWrap.find('.lang-item').get();

    // sort
    function sortLanguages(type = 'az') {
        const items = $langWrap.find('.lang-item').get();

        items.sort(function(a, b) {
            const nameA = ($(a).data('english-name') || '').toString().toLowerCase().trim();
            const nameB = ($(b).data('english-name') || '').toString().toLowerCase().trim();

            const dateA = new Date($(a).data('latest-book-date'));
            const dateB = new Date($(b).data('latest-book-date'));

            const viewsA = $(a).data('total-views');
            const viewsB = $(b).data('total-views');

            switch (type) {
                case 'az':
                    if (nameA < nameB) return -1;
                    if (nameA > nameB) return 1;
                    return 0;

                case 'za':
                    if (nameA < nameB) return 1;
                    if (nameA > nameB) return -1;
                    return 0;

				case 'popular':
                    if (parseInt(viewsA) < parseInt(viewsB)) return 1;
                    if (parseInt(viewsA) > parseInt(viewsB)) return -1;
                    return 0;

                case 'latest':
                    return dateB - dateA; // most recent first
            }
        });

        $langWrap.append(items);
    }

    $(document).on('click', '.sort a', function(e) {
        e.preventDefault();

        // handle active state
        $sortLinks.removeClass('active');
        $(this).addClass('active');

        if ($(this).hasClass('sort-az')) {
            sortLanguages('az');
        } else if ($(this).hasClass('sort-za')) {
            sortLanguages('za');
        } else if ($(this).hasClass('sort-latest')) {
            sortLanguages('latest');
		} else if ($(this).hasClass('sort-popular')) {
            sortLanguages('popular');
        } else if ($(this).hasClass('sort-default active')) {
            $langWrap.append(originalOrder); // restore original order
        }
    });

	/* 
        ----------------------------------------------------------------
        Search 
        ----------------------------------------------------------------
    */

    $searchInput.on('keyup', function() {

        const query = $(this).val().toLowerCase().trim();
        let hasMatch = false;

        $langWrap.find('.lang-item').each(function() {
            const nativeName = ($(this).data('native-name') || '').toString().toLowerCase();
            const englishName = ($(this).data('english-name') || '').toString().toLowerCase();
            
            if (englishName.includes(query) || nativeName.includes(query)) {
                $(this).show(); hasMatch = true;
            } else {
                $(this).hide();
            }
        });

    	if (hasMatch || query === '') { $('#no-results').hide(); } 
    	else { $('#no-results').show(); }

    });
    
}); 