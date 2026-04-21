jQuery(function($){

	console.log('library.js loaded!');

	// put focus on the login input on load
	if (window.innerWidth > 1000) {
	    $('#barcode').focus();
	}

    // trigger on enter inside input
    $('#barcode').on('keypress', function (e) {
        if (e.which === 13) { 
            e.preventDefault();
            $('#library_submit_btn').click();
        }
    });

    // attempt to login via ajax
    $('#library_submit_btn').on('click', function(e){

    	// clear error message first
		$('.lib__error').html('').hide();

    	// check if barcode is blank
		if ($('#barcode').val().trim() === '') { $('.lib__error').html('Please enter your barcode.').fadeIn(); return; }

        $('#library_submit_btn').prop('disabled', true);
    	$('#library_submit_btn').html('<i class="loading lni lni-spinner-2-sacle"></i> Validating...');

        e.preventDefault();

        let barcode  	= $('#barcode').val();
        let library_id  = $('#library_id').val();
        let remember 	= $('#remember_me').is(':checked') ? 1 : 0;

        $.ajax({
            url: library_ajax.url, // WP auto-provides this in admin; for frontend use wp_localize_script
            type: 'POST',
            data: {
                action: 	'l4k_loginToLibrary',
                barcode: 	barcode,
                library_id: library_id,
                remember: 	remember
            },
            success: function(response){
			    setTimeout(function() {
			        $('.ajax-response__wrapper').html(response);

			        if (response.status == 0) {
						$('#library_submit_btn').prop('disabled', false);
			        	$('#library_submit_btn').html('<i class="lni lni-locked-2"></i> Secure Login');
			        	$('.lib__error').html(response.message).fadeIn();
			        }
					
					if (response.status == 1) {
			        	$('#library_submit_btn').html('<i class="lni lni-check-circle-1"></i> Redirecting...');

						let redirectPath = '/member-home'; // default path

			        	if (response.redirect_to) {
			        		redirectPath = response.redirect_to;
			        	} else {
				        	const host = window.location.hostname; // get the current hostname
				        	redirectPath = '/member-home'; // default path for live domain

				        	// if on localhost, preserve the folder name
				        	if (host === 'localhost') {
				        	    const folder = window.location.pathname.split('/')[1];
				        	    redirectPath = '/' + folder + '/member-home';
				        	}
			        	}

			        	window.location.href = redirectPath;
			        }

			        // for debugging purposes
			        // $('.ajax-response__wrapper').html(response.raw_data);

			    }, 1000);
            }
        });
    });

    /* 
        ----------------------------------------------------------------
        MARC overlay
        ----------------------------------------------------------------
    */

	// when clicked -> show marc overlay
	$('.btn-marc').on('click', function() {
		$('.embed__overlay.marc').fadeIn();
		$('body').addClass('_noscroll'); // prevent scrolling
	});

    /* 
        ----------------------------------------------------------------
        Language breakdown overlay
        ----------------------------------------------------------------
    */

	// when clicked -> show language breakdown overlay
	$('.btn-breakdown').on('click', function() {
		$('.embed__overlay.breakdown').fadeIn();
		$('body').addClass('_noscroll'); // prevent scrolling
	});

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
        Trial/competition/training
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

	// show check mark on competition page or training page once submitted
	jQuery(document).on('wpformsAjaxSubmitSuccess', function(event, formData, formID) {
	    console.log('Form submitted!');
	    console.log('Form ID:', formID);
	    console.log('Form Data:', formData);
	    
	    // check if .competition-text exists
	    var competitionText = document.querySelector('.competition-text');
	    if (competitionText) {
	        competitionText.innerHTML = '<img class="check" src="https://lote4kids.com/wp-content/uploads/2021/06/ClipartKey_0610-1-1.png" alt="Submitted">';
	        console.log('Competition text updated with checkmark image');
	    }

	    // check if .training-text exists
	    var trainingText = document.querySelector('.training-text');
	    if (trainingText) {
	    	$('.form-label').hide();
	    	$('.video-wrap').remove();
	    	$('.training-wrap .form-video-section').addClass('confirmation');
	        trainingText.innerHTML = '<img class="check" src="https://lote4kids.com/wp-content/uploads/2021/06/ClipartKey_0610-1-1.png" alt="Submitted">';
	        console.log('Training text updated with checkmark image');
	    }
	});

    /* 
        ----------------------------------------------------------------
        Teacher profile management -- update avatar
        ----------------------------------------------------------------
    */

	// click the avatar div → open file picker
    $('#teacher-avatar-trigger').on('click', function(e) {
        e.preventDefault();
        $('#teacher-avatar-input').trigger('click');
    });

    // file selected → upload via AJAX
    $('#teacher-avatar-input').on('change', function() {
        var file = this.files[0];
        if (!file) return;

        // Basic client-side type check
        if (!file.type.match(/^image\//)) {
            alert('Please select an image file.');
            return;
        }

        var formData = new FormData();
        formData.append('action',  'l4k_upload_teacher_avatar');
        formData.append('nonce',   $('#l4k-avatar-nonce').data('nonce'));
        formData.append('avatar',  file);

        // Visual feedback: dim the avatar while uploading
        $('#teacher-avatar-img').css('opacity', '0.4');

        $.ajax({
            url: library_ajax.url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    $('#teacher-avatar-img')
                        .attr('src', response.data.url)
                        .css('opacity', '1');

                    // if successfully saved, show toast
				    const duration = 5000;
				    const toastId = 'teacher-avatar-saved';
			        const $toastWrap = $('._toast');

			        let $toastItem = $toastWrap.find(`._toast-item[data-toast-id="${toastId}"]`); // find existing toast for THIS button only

			        if ($toastItem.length) {
			            // RESET existing toast
			            $toastItem.stop(true, true).removeClass('counting-down').show();
			        } else {
			            // CREATE new toast
			            $toastItem = $(`
			                <div class="_toast-item" data-toast-id="${toastId}">
			                    <i class="lni lni-check-circle-1"></i>
			                    <div>Changes saved!</div>
			                </div>
			            `).hide();

			            $toastWrap.prepend($toastItem);
			            $toastItem.fadeIn(250);
			        }

			        $toastItem.css('--duration', duration + 'ms'); // set this toast's timer
			        setTimeout(() => { $toastItem.addClass('counting-down'); }, 20); // restart countdown bar
			        clearTimeout($toastItem.data('removeTimeout')); // clear old removal timer FOR THIS toast
			        const removeTimeout = setTimeout(function () { $toastItem.fadeOut(250, function () { $(this).remove(); }); }, duration);
			        $toastItem.data('removeTimeout', removeTimeout); // store timer on element
                } else {
                    alert('Upload failed: ' + response.data.message);
                    $('#teacher-avatar-img').css('opacity', '1');
                }
            },
            error: function() {
                alert('Something went wrong. Please try again.');
                $('#teacher-avatar-img').css('opacity', '1');
            }
        });

        // Reset input so the same file can be re-selected if needed
        $(this).val('');
    });

    /* 
        ----------------------------------------------------------------
        Teacher profile management -- edit name
        ----------------------------------------------------------------
    */

    // Enter edit mode
	$('#teacher-name-edit').on('click', function(e) {
	    e.preventDefault();
	    var currentName = $('#teacher-name-display').text();
	    $('#teacher-name-input').val(currentName).show().focus();
	    var input = $('#teacher-name-input')[0];
	    var len = input.value.length;
	    input.setSelectionRange(len, len);
	    $('#teacher-name-display').hide();
	    $(this).hide();
	    $('#teacher-name-save, #teacher-name-divider, #teacher-name-cancel').show();
	});

    // Cancel edit
    $('#teacher-name-cancel').on('click', function(e) {
        e.preventDefault();
        $('#teacher-name-input').hide();
        $('#teacher-name-display').show();
        $('#teacher-name-edit').show();
        $(this).hide();
        $('#teacher-name-save, #teacher-name-divider').hide();
    });

    // Save name
    $('#teacher-name-save').on('click', function(e) {
        e.preventDefault();
        var newName = $('#teacher-name-input').val().trim();

        if (!newName) {
            alert('Name cannot be empty.');
            return;
        }

        $.ajax({
            url:  library_ajax.url,
            type: 'POST',
            data: {
                action: 'l4k_update_teacher_name',
                nonce:  $('#l4k-name-nonce').data('nonce'),
                name:   newName
            },
            success: function(response) {
                if (response.success) {
                    $('#teacher-name-display').text(response.data.name).show();
                    $('#teacher-name-input').hide();
                    $('#teacher-name-edit').show();
                    $('#teacher-name-save, #teacher-name-divider, #teacher-name-cancel').hide();

                    // if successfully saved, show toast
				    const duration = 5000;
				    const toastId = 'teacher-name-saved';
			        const $toastWrap = $('._toast');

			        let $toastItem = $toastWrap.find(`._toast-item[data-toast-id="${toastId}"]`); // find existing toast for THIS button only

			        if ($toastItem.length) {
			            // RESET existing toast
			            $toastItem.stop(true, true).removeClass('counting-down').show();
			        } else {
			            // CREATE new toast
			            $toastItem = $(`
			                <div class="_toast-item" data-toast-id="${toastId}">
			                    <i class="lni lni-check-circle-1"></i>
			                    <div>Changes saved!</div>
			                </div>
			            `).hide();

			            $toastWrap.prepend($toastItem);
			            $toastItem.fadeIn(250);
			        }

			        $toastItem.css('--duration', duration + 'ms'); // set this toast's timer
			        setTimeout(() => { $toastItem.addClass('counting-down'); }, 20); // restart countdown bar
			        clearTimeout($toastItem.data('removeTimeout')); // clear old removal timer FOR THIS toast
			        const removeTimeout = setTimeout(function () { $toastItem.fadeOut(250, function () { $(this).remove(); }); }, duration);
			        $toastItem.data('removeTimeout', removeTimeout); // store timer on element
                } else {
                    alert('Error: ' + response.data.message);
                }
            },
            error: function() {
                alert('Something went wrong. Please try again.');
            }
        });
    });

    // Also allow Enter key to save
    $('#teacher-name-input').on('keydown', function(e) {
        if (e.key === 'Enter') $('#teacher-name-save').trigger('click');
        if (e.key === 'Escape') $('#teacher-name-cancel').trigger('click');
    });

	/*
	    ----------------------------------------------------------------
	    Teacher profile management -- edit info
	    ----------------------------------------------------------------
	*/
	
	var infoFields = ['email', 'school', 'country', 'state', 'phone', 'currency'];

	// Enter edit mode
	$('#teacher-info-edit').on('click', function(e) {
	    e.preventDefault();
	    infoFields.forEach(function(field) {
	        $('#teacher-' + field + '-display').hide();
	        $('#teacher-' + field + '-input').show();
	    });
	    $(this).hide();
	    $('#teacher-info-save, #teacher-info-divider, #teacher-info-cancel').show();
	});

	// Cancel edit
	$('#teacher-info-cancel').on('click', function(e) {
	    e.preventDefault();
	    infoFields.forEach(function(field) {
	        $('#teacher-' + field + '-display').show();
	        $('#teacher-' + field + '-input').hide();
	    });
	    $('#teacher-info-edit').show();
	    $('#teacher-info-save, #teacher-info-divider, #teacher-info-cancel').hide();
	});

	// Save info
	$('#teacher-info-save').on('click', function(e) {
	    e.preventDefault();

	    var data = {
	        action: 'l4k_update_teacher_info',
	        nonce:  $('#l4k-info-nonce').data('nonce')
	    };

	    infoFields.forEach(function(field) {
	        data[field] = $('#teacher-' + field + '-input').val().trim();
	    });

	    $.ajax({
	        url:  library_ajax.url,
	        type: 'POST',
	        data: data,
	        success: function(response) {
	            if (response.success) {
	                var labels = {
	                    email:    'Email',
	                    school:   'School',
	                    country:  'Country',
	                    state:    'State',
	                    phone:    'Phone',
	                    currency: 'Currency'
	                };
					infoFields.forEach(function(field) {
					    var val = response.data[field];
					    $('#teacher-' + field + '-display').text(val).show();
					    $('#teacher-' + field + '-input').hide();
					});
	                $('#teacher-info-edit').show();
	                $('#teacher-info-save, #teacher-info-divider, #teacher-info-cancel').hide();
	            } else {
	                alert('Error: ' + response.data.message);
	            }
	        },
	        error: function() {
	            alert('Something went wrong. Please try again.');
	        }
	    });
	});

});

function printDashboardReport() 
{
    const iframe = document.querySelector('iframe');
    
    if (!iframe) {
        alert("No iframe found on the page!");
        return;
    }

    const iframeSrc = iframe.src;
    const newWin = window.open('', '_blank');

    newWin.document.write(`
        <html>
            <head>
                <title>Print</title>
                <style>
                    @page { size: landscape; }
                </style>
            </head>
            <body data-rsssl=1 style="margin:0;overflow:hidden">
                <iframe src="${iframeSrc}" 
                        style="border:none;width:100%;height:100vh"></iframe>
                <script>
                    setTimeout(() => {
                        window.print();
                        setTimeout(() => window.close(), 2000);
                    }, 3000);
                <\/script>
            </body>
        </html>
    `);
    newWin.document.close();
}