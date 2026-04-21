jQuery(function($){

	console.log('teacher-subscription.js loaded!');

    /* 
        ----------------------------------------------------------------
        Functions for ALL overlays in the sidebar
        ----------------------------------------------------------------
    */

	// close overlay when background is clicked
	$('.embed__overlay').on('click', function() {
		$(this).fadeOut();
		$('body').removeClass('_noscroll'); // restore scroll
	});

	// close overlay when X is clicked
	$('.embed__close').on('click', function(e) {
        e.preventDefault(); 
        $(this).closest('.embed__overlay').fadeOut();
        $('body').removeClass('_noscroll'); // restore scroll
	});

	// close overlay when ESC is pressed
	$(document).on('keyup', function(e) {
	    if (e.key === "Escape") {
	        $('.embed__overlay:visible').fadeOut();
	        $('body').removeClass('_noscroll'); // restore scroll
	    }
	});

	// do NOT close when the box inside the overlay is clicked
	$('.embed__overlay .embed__wrap').on('click', function(e) {
		e.stopPropagation();
	});

    /* 
        ----------------------------------------------------------------
        Terms popup
        ----------------------------------------------------------------
    */

    $('.terms-text-wrapper label').each(function() {
        var html = $(this).html();
        var newHtml = html.replace('Terms', '<span class="terms-btn">Terms</span>');
        $(this).html(newHtml);
    });

	// add click event to the newly created span
	$(document).on('click', '.terms-btn', function(e) {
	    e.preventDefault(); // prevents the checkbox from toggling
	    e.stopPropagation(); // stops the click from bubbling up to the label
	    
    	// when clicked -> show marc overlay
		$('.embed__overlay.terms').fadeIn();
		$('body').addClass('_noscroll'); // prevent scrolling
	});

});