jQuery(function ($) {

	console.log('404.js loaded!');

	$('#searchform #s').focus();

	// on click enter, go to search results
    $('#searchform #s').on('keypress', function(e) {

		$('.search-error').hide();

        if (e.which === 13) { // Enter key
            e.preventDefault(); // prevent default submission

            var query = $(this).val().trim();
            query = query.replace(/[<>\/\\{}[\]]/g, ''); // basic sanitization: remove potentially harmful characters

            // check if input is not empty
            if (query.length === 0) {
                $('.search-error').fadeIn();
                return false; // stop submission
            }

            $(this).val(query); // set sanitized value back to input
            $(this).closest('form').submit(); // submit form
        }

    });

});
