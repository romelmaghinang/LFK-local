jQuery(function ($) {
    
    console.log('find-library.js loaded!');

	/* 
        ----------------------------------------------------------------
        Library search filter
        ----------------------------------------------------------------
    */

	$('#lib-search').on('keyup', function(){
	    $('.search-txt').removeClass('not-found'); 
	    $('.search-btn').attr('href', '');

	    let q = $(this).val().toLowerCase().trim();
	    let $sug = $('#suggestions');
	    $sug.empty();

	    if (q.length < 3) { return; }

	    $('#lib-list li').each(function(){
	        let text = $(this).text();
	        let url = $(this).attr('data-url');

	        if (text.toLowerCase().indexOf(q) > -1) {
	            $sug.append('<li data-url="'+url+'">'+text+'</li>');
	        }
	    });
	});

    // click to fill input
    $(document).on('click','#suggestions li', function(){
        $('#lib-search').val($(this).text());
        $('.search-btn').attr('href', $(this).attr('data-url'));
        $('#suggestions').empty(); // hide after choose
    });

	$('.search-btn').on('click', function(e) {
		e.preventDefault();
		if ($('.search-btn').attr('href') == '') { 
			$('.search-txt').addClass('not-found'); 
			$('#lib-search').focus(); // put focus on the library search
		} else { 
			window.location.href = $('.search-btn').attr('href'); 
		}	
	});

}); 