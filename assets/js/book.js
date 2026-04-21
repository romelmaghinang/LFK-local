jQuery(function ($) {

	console.log('book.js loaded!');

	// add active indicator to main menu when in books
	$('.menu-item-books').addClass('current-menu-item');

	// populate the "library" field that's hidden in the comment form
	$('#custom_field_6939180959ceb-0_0').val($('#comment-library-name').val());

    /* 
        ----------------------------------------------------------------
        Functions for ALL overlays in the sidebar
        ----------------------------------------------------------------
    */

	// close overlay when background is clicked
	$('.embed__overlay:not(.trial-popup)').on('click', function() {
		$(this).fadeOut();
		$('body').removeClass('_noscroll'); // restore scroll
		$('.iframe__wrap iframe').attr('src', '');
	});

	// close overlay when X is clicked
	$('.embed__overlay:not(.trial-popup) .embed__close').on('click', function(e) {
        e.preventDefault(); 
        $(this).closest('.embed__overlay').fadeOut();
        $('body').removeClass('_noscroll'); // restore scroll
        $('.iframe__wrap iframe').attr('src', '');
	});

	// close overlay when ESC is pressed
	$(document).on('keyup', function(e) {
	    if (e.key === "Escape") {
	        $('.embed__overlay:visible').fadeOut();
	        $('body').removeClass('_noscroll'); // restore scroll
	        $('.iframe__wrap iframe').attr('src', '');
	    }
	});

	// do NOT close when the box inside the overlay is clicked
	$('.embed__overlay:not(.trial-popup) .embed__wrap').on('click', function(e) {
		e.stopPropagation();
	});

    /* 
        ----------------------------------------------------------------
        Log activities via ajax
        ----------------------------------------------------------------
    */

	// when clicked -> open pdf in a new tab
	// when clicked -> record web activity via ajax
	// when clicked -> attempt to increment feather count via ajax
	// when clicked -> update trial checklist for activity to "1"
	$('.sidebar__item.related-activities .activities__btn').on('click', function() {

		// const actIdentifier = $(this).data('activity-name');
		const actIdentifier = `act-${Date.now()}-${Math.random().toString(36).substr(2, 9)}`;

		// record web activity
		$.ajax({
            url: book_ajax.url, // WP auto-provides this in admin; for frontend use wp_localize_script
            type: 'POST',
            data: {
                action: 'l4k_addWebActivityViaAjax', 
                alert_code: '1060',
                activity_name: $(this).data('activity-name'),
                activity_title: $(this).data('activity-title'),
                activity_type: 'Pdf',
            },
            success: function(response){
			    console.log(response);
            }
        });

		// increment feather count
        $.ajax({
            url: book_ajax.url, // WP auto-provides this in admin; for frontend use wp_localize_script
            type: 'POST',
            data: {
                action: 'l4k_addFeatherCountViaAjax',
                owner_id: $('body').data('owner-id'),
                url: 'Sidebar Activity - '+$(this).attr('href'),
            },
            success: function(response){
			    console.log(response);

			    // if successfully added to database, show toast
			    if (response.status == 1) {  
					const duration = 8000;
				    const toastId = 'feather-activity-toast-'+actIdentifier;
			        const $toastWrap = $('._toast');
			        const homeURL = $('#global-home-url').html();

			        let $toastItem = $toastWrap.find(`._toast-item[data-toast-id="${toastId}"]`); // find existing toast for THIS button only

			        if ($toastItem.length) {
			            // RESET existing toast
			            $toastItem.stop(true, true).removeClass('counting-down').show();
			        } else {
			            // CREATE new toast
			            $toastItem = $(`
			                <a class="_toast-item feather" data-toast-id="${toastId}" href="${homeURL}/avatar">
			                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" transform="rotate(0 0 0)"> <path d="M21.8303 3.82936C20.7331 3.1767 19.2508 2.83203 17.5403 2.83203C14.9242 2.83203 12.0403 3.63595 9.44983 5.03478L9.4315 5.02836L9.42783 5.04761C8.64408 5.47295 7.88417 5.94686 7.171 6.47578C2.96808 9.59336 1.79108 13.293 2.78475 15.2712C1.69667 17.0789 1.0275 18.9498 1 21.1654C3.09 17.2851 4.344 14.1409 11.153 8.8472C9.20783 9.25328 5.8455 11.1801 3.58317 14.1043C3.36958 12.3938 4.7565 9.63186 7.84567 7.3402C8.238 7.05053 8.64408 6.7792 9.05933 6.52161C8.73483 7.50336 8.83933 7.25953 8.16558 8.63636C9.16108 7.71878 9.81558 7.15136 10.7992 5.57928C12.0476 4.97723 13.3635 4.5264 14.7188 4.23636C14.5007 4.93945 14.0863 6.13111 13.5253 7.07345C13.5253 7.07345 14.9489 6.77645 16.1259 6.84428C15.4833 7.53453 14.904 8.28253 14.3173 9.04703C13.5143 10.0939 12.6838 11.1755 11.6196 12.1829C11.4912 12.3049 11.3693 12.4149 11.2456 12.5285C9.61025 12.3754 8.53133 12.9713 7.51933 14.0227C8.31683 13.6606 9.38933 13.3627 10.0603 13.5424C8.82283 14.5259 6.87308 15.8221 5.27167 15.7149C4.96733 16.1659 4.94808 16.1796 4.61442 16.7232C7.21317 17.3539 10.4765 14.7817 12.3969 12.9621C13.5235 11.8951 14.3815 10.7777 15.2111 9.69878C16.9179 7.47403 18.3928 5.5527 21.6644 4.82211L23 4.5242L21.8303 3.82936Z" fill="#343C54"/> </svg>
			                    <div>Gained 1 feather!<span>Earned by doing an activity.</span><span>You now have <p>${response.result}</p> feathers.</span></div>
			                </a>
			            `).hide();

			            $toastWrap.prepend($toastItem);
			            $toastItem.fadeIn(250);
			        }

			        $toastItem.css('--duration', duration + 'ms'); // set this toast's timer
			        setTimeout(() => { $toastItem.addClass('counting-down'); }, 20); // restart countdown bar
			        clearTimeout($toastItem.data('removeTimeout')); // clear old removal timer FOR THIS toast
			        const removeTimeout = setTimeout(function () { $toastItem.fadeOut(250, function () { $(this).remove(); }); }, duration);
			        $toastItem.data('removeTimeout', removeTimeout); // store timer on element
			    }
            }
        });

        // update trial checklist activity status to "1"
		$.ajax({
		    url: main_ajax.url,
		    type: 'POST',
		    data: {
		        action: 'l4k_checkIfBarcodeIsTrial',
		        activity: true, 
		        page_id: l4k_vars.page_id, 
		        barcode: l4k_vars.library_barcode,
		    },
		    success: function(response) {
		        if (response.status === 1) {
		            // if data exists, check the boxes in the popup that are marked as '1'
		            if (response.data) {
		                $.each(response.data, function(action, value) {
		                    if (value === '1') { $('input[data-action="' + action + '"]').prop('checked', true); }
		                });
		            }

					// fill in the trial counter like 2/8 for example
					const total = $('.trial-popup-content li input[type="checkbox"][data-action]').length;
					const checked = $('.trial-popup-content li input[type="checkbox"][data-action]:checked').length;
					if (checked == total) { $('.trial-counter').html('All steps completed'); }
					else { $('.trial-counter').html(checked + '/' + total + ' completed'); }

               	 	$('#trial-menu-status, #trial-toggle').addClass('active');
		        } else {
		        	$('.toolbox-item.trial-checklist').remove();
		        }
		        console.log(response);
		    }
		});

	});

    /* 
        ----------------------------------------------------------------
        Quiz overlay
        ----------------------------------------------------------------
    */

	// auto-trigger quiz overlay if URL contains #quiz
	if (window.location.hash === '#quiz') {
	    setTimeout(function() {
	        const $quizBtn = $('.sidebar__item.reward-driven .quiz__btn').first();
	        console.log('delayed btn found:', $quizBtn.length);
	        $quizBtn.trigger('click');
	    }, 500);
	}

	// when clicked -> show quiz overlay 
	// when clicked -> record web activity via ajax
	// when clicked -> record book view activity via ajax
	// when clicked -> attempt to increment feather count via ajax
	// when clicked -> update trial checklist for quiz to "1"
	$('.sidebar__item.reward-driven .quiz__btn').on('click', function() {

		// const quizIdentifier = $(this).attr('book-quiz');
		const quizIdentifier = `quiz-${Date.now()}-${Math.random().toString(36).substr(2, 9)}`;

		var iframeCode = $('.iframe__wrap[data-index="' + $(this).data('index') + '"]').data('iframe');
		$('.iframe__wrap[data-index="' + $(this).data('index') + '"]').html(iframeCode);

		$('.embed__overlay[data-index="' + $(this).data('index') + '"]').fadeIn();
		$('body').addClass('_noscroll'); // prevent scrolling

		// record web activity
		$.ajax({
            url: book_ajax.url, // WP auto-provides this in admin; for frontend use wp_localize_script
            type: 'POST',
            data: {
                action: 'l4k_addWebActivityViaAjax',
                alert_code: '1060',
                activity_name: 'Quiz',
                activity_title: $(this).data('activity-title'),
                activity_type: 'Embedded',
            },
            success: function(response){
			    console.log(response);
            }
        });

		// record book view activity
		$.ajax({
            url: book_ajax.url, // WP auto-provides this in admin; for frontend use wp_localize_script
            type: 'POST',
            data: {
                action: 'l4k_addWebActivityViaAjax',
                alert_code: '1062',
                story_id: $(this).data('book-id'),
                story_title: $(this).data('book-title'),
                language: $(this).data('book-language'),
                type: $(this).data('book-type'),
            },
            success: function(response){
			    console.log(response);
            }
        });

		// increment feather count
		$.ajax({
            url: book_ajax.url, // WP auto-provides this in admin; for frontend use wp_localize_script
            type: 'POST',
            data: {
                action: 'l4k_addFeatherCountViaAjax',
                owner_id: $('body').data('owner-id'),
                url: quizIdentifier,
            },
            success: function(response){
			    console.log(response);

			    // if successfully added to database, show toast
			    if (response.status == 1) {  
					const duration = 8000;
				    const toastId = 'feather-quiz-toast-'+quizIdentifier;
			        const $toastWrap = $('._toast');
			        const homeURL = $('#global-home-url').html();

			        let $toastItem = $toastWrap.find(`._toast-item[data-toast-id="${toastId}"]`); // find existing toast for THIS button only

			        if ($toastItem.length) {
			            // RESET existing toast
			            $toastItem.stop(true, true).removeClass('counting-down').show();
			        } else {
			            // CREATE new toast
			            $toastItem = $(`
			                <a class="_toast-item feather" data-toast-id="${toastId}" href="${homeURL}/avatar">
			                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" transform="rotate(0 0 0)"> <path d="M21.8303 3.82936C20.7331 3.1767 19.2508 2.83203 17.5403 2.83203C14.9242 2.83203 12.0403 3.63595 9.44983 5.03478L9.4315 5.02836L9.42783 5.04761C8.64408 5.47295 7.88417 5.94686 7.171 6.47578C2.96808 9.59336 1.79108 13.293 2.78475 15.2712C1.69667 17.0789 1.0275 18.9498 1 21.1654C3.09 17.2851 4.344 14.1409 11.153 8.8472C9.20783 9.25328 5.8455 11.1801 3.58317 14.1043C3.36958 12.3938 4.7565 9.63186 7.84567 7.3402C8.238 7.05053 8.64408 6.7792 9.05933 6.52161C8.73483 7.50336 8.83933 7.25953 8.16558 8.63636C9.16108 7.71878 9.81558 7.15136 10.7992 5.57928C12.0476 4.97723 13.3635 4.5264 14.7188 4.23636C14.5007 4.93945 14.0863 6.13111 13.5253 7.07345C13.5253 7.07345 14.9489 6.77645 16.1259 6.84428C15.4833 7.53453 14.904 8.28253 14.3173 9.04703C13.5143 10.0939 12.6838 11.1755 11.6196 12.1829C11.4912 12.3049 11.3693 12.4149 11.2456 12.5285C9.61025 12.3754 8.53133 12.9713 7.51933 14.0227C8.31683 13.6606 9.38933 13.3627 10.0603 13.5424C8.82283 14.5259 6.87308 15.8221 5.27167 15.7149C4.96733 16.1659 4.94808 16.1796 4.61442 16.7232C7.21317 17.3539 10.4765 14.7817 12.3969 12.9621C13.5235 11.8951 14.3815 10.7777 15.2111 9.69878C16.9179 7.47403 18.3928 5.5527 21.6644 4.82211L23 4.5242L21.8303 3.82936Z" fill="#343C54"/> </svg>
			                    <div>Gained 1 feather!<span>Earned by doing a quiz.</span><span>You now have <p>${response.result}</p> feathers.</span></div>
			                </a>
			            `).hide();

			            $toastWrap.prepend($toastItem);
			            $toastItem.fadeIn(250);
			        }

			        $toastItem.css('--duration', duration + 'ms'); // set this toast's timer
			        setTimeout(() => { $toastItem.addClass('counting-down'); }, 20); // restart countdown bar
			        clearTimeout($toastItem.data('removeTimeout')); // clear old removal timer FOR THIS toast
			        const removeTimeout = setTimeout(function () { $toastItem.fadeOut(250, function () { $(this).remove(); }); }, duration);
			        $toastItem.data('removeTimeout', removeTimeout); // store timer on element
			    }
            }
        });

		// update trial checklist activity status to "1"
		$.ajax({
		    url: main_ajax.url,
		    type: 'POST',
		    data: {
		        action: 'l4k_checkIfBarcodeIsTrial',
		        quiz: true, 
		        page_id: l4k_vars.page_id, 
		        barcode: l4k_vars.library_barcode,
		    },
		    success: function(response) {
		        if (response.status === 1) {
		            // if data exists, check the boxes in the popup that are marked as '1'
		            if (response.data) {
		                $.each(response.data, function(action, value) {
		                    if (value === '1') { $('input[data-action="' + action + '"]').prop('checked', true); }
		                });
		            }

					// fill in the trial counter like 2/8 for example
					const total = $('.trial-popup-content li input[type="checkbox"][data-action]').length;
					const checked = $('.trial-popup-content li input[type="checkbox"][data-action]:checked').length;
					if (checked == total) { $('.trial-counter').html('All steps completed'); }
					else { $('.trial-counter').html(checked + '/' + total + ' completed'); }

               	 	$('#trial-menu-status, #trial-toggle').addClass('active');
		        } else {
		        	$('.toolbox-item.trial-checklist').remove();
		        }
		        console.log(response);
		    }
		});

	});

    /* 
        ----------------------------------------------------------------
        Learning dashboard overlay
        ----------------------------------------------------------------
    */

	// show learning dashboard overlay when clicked in sidebar
	$('.sidebar__item.reward-driven .ld__btn').on('click', function() {

		// put the loading content back on the counts and charts
 		$('.count-data').html('<div class="_loader-small"></div>'); 
		$('.chart .chart-label').after('<div class="_loader-small"></div>');

		// destroy past charts before opening popup
		const chartBook = Chart.getChart($('#chart-book')); if (chartBook) { chartBook.destroy(); }
		const chartActivity = Chart.getChart($('#chart-activity')); if (chartActivity) { chartActivity.destroy(); }

		let barcode = $(this).data('barcode');

		$.ajax({
            url: book_ajax.url, // WP auto-provides this in admin; for frontend use wp_localize_script
            type: 'POST',
            data: {
                action: 'l4k_getLearningDashboardContent',
                barcode: barcode
            },
            success: function(response){
			    setTimeout(function() {

			    	$('.chart ._loader-small').remove();

			        $('#count-books').html(response.dashboardContentArr.countBooks);
			        $('#count-quizzes').html(response.dashboardContentArr.countQuizzes);
			        $('#count-activities').html(response.dashboardContentArr.countActivities);
			        $('#count-feathers').html(response.dashboardContentArr.countFeathers);
			        $('#count-streaks').html(response.dashboardContentArr.countStreaks);
			        
			        const bgColors = ['#f8e48f', '#87b299', '#88c7ed', '#cccccc', '#dddddd']; // can be used by both quiz and activity charts

				    const booklabels = [];
				    const bookCounts = [];
				    const bookTypes = response.dashboardContentArr.countBooks_types;
					const bookBgColors = bgColors.slice(0, bookTypes.length);

				    $.each(bookTypes, function(label, data) {
						booklabels.push(label); // flipbook, video_monolingual, etc
						bookCounts.push(data); // 14, 114, etc
				    });

					const chartBook = new Chart($('#chart-book'), {
						type: 'doughnut',
						data: {
							labels: booklabels,
							datasets: [{
								label: 'Books',
								data: bookCounts,
								backgroundColor: bookBgColors,
								hoverOffset: 4 }]
							},  
						options: {
							responsive: true,
							maintainAspectRatio: true,
							plugins: { 
								legend: { 
									position: 'bottom',
									labels: { color: '#000000', font: { size: 14 } }
								} 
							}
						}
					});

				    const activitylabels = [];
				    const activityCounts = [];
				    const activityTypes = response.dashboardContentArr.countActivity_types;
					const activityBgColors = bgColors.slice(0, activityTypes.length);

				    $.each(activityTypes, function(label, data) {
						activitylabels.push(label); // flipbook, video_monolingual, etc
						activityCounts.push(data); // 14, 114, etc
				    });

					const chartActivity = new Chart($('#chart-activity'), {
						type: 'doughnut',
						data: {
							labels: activitylabels,
							datasets: [{
								label: 'Activities',
								data: activityCounts,
								backgroundColor: activityBgColors,
								hoverOffset: 4 }]
							},  
						options: {
							responsive: true,
							maintainAspectRatio: true,
							plugins: { 
								legend: { 
									position: 'bottom',
									labels: { color: '#000000', font: { size: 14 } }
								}
							}
						}
					});

			    }, 1000);
            }
        });

		$('.embed__overlay[data-index="learning-dashboard"]').fadeIn();
		$('body').addClass('_noscroll'); // prevent scrolling
	});

    /* 
        ----------------------------------------------------------------
        Quick copy video
        ----------------------------------------------------------------
    */

	$('.actions-wrapper .copy-link').on('click', function(e) {
	    e.preventDefault();

	    const url      = $(this).data('url');
	    const duration = 6000;
	    const toastId  = $(this).data('toast-id');

	    navigator.clipboard.writeText(url).then(function () {

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
	                    <div>Link copied!<span>Paste this link into your LMS, email, or class portal.</span></div>
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

	    }).catch(function (err) {
	        console.error('Copy failed', err);
	    });
	});

    /* 
        ----------------------------------------------------------------
        Feather claimed on single book view after 3 seconds
        ----------------------------------------------------------------
    */

    setTimeout(function() {
		if (!window.location.search.includes('comments-only')) {

			$.ajax({
	            url: book_ajax.url, // WP auto-provides this in admin; for frontend use wp_localize_script
	            type: 'POST',
	            data: {
	                action: 'l4k_addFeatherCountViaAjax',
	                owner_id: $('body').data('owner-id'),
	                url: window.location.href,
	            },
	            success: function(response){
				    console.log(response);

				    // if successfully added to database, show toast
				    if (response.status == 1) {  
						const duration = 5000;
					    const toastId = 'feather-book-toast';
				        const $toastWrap = $('._toast');
				        const homeURL = $('#global-home-url').html();

				        let $toastItem = $toastWrap.find(`._toast-item[data-toast-id="${toastId}"]`); // find existing toast for THIS button only

				        if ($toastItem.length) {
				            // RESET existing toast
							$toastItem.stop(true, true).removeClass('counting-down').show();
				        } else {
				            // CREATE new toast
				            $toastItem = $(`
				                <a class="_toast-item feather" data-toast-id="${toastId}" href="${homeURL}/avatar">
				                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" transform="rotate(0 0 0)"> <path d="M21.8303 3.82936C20.7331 3.1767 19.2508 2.83203 17.5403 2.83203C14.9242 2.83203 12.0403 3.63595 9.44983 5.03478L9.4315 5.02836L9.42783 5.04761C8.64408 5.47295 7.88417 5.94686 7.171 6.47578C2.96808 9.59336 1.79108 13.293 2.78475 15.2712C1.69667 17.0789 1.0275 18.9498 1 21.1654C3.09 17.2851 4.344 14.1409 11.153 8.8472C9.20783 9.25328 5.8455 11.1801 3.58317 14.1043C3.36958 12.3938 4.7565 9.63186 7.84567 7.3402C8.238 7.05053 8.64408 6.7792 9.05933 6.52161C8.73483 7.50336 8.83933 7.25953 8.16558 8.63636C9.16108 7.71878 9.81558 7.15136 10.7992 5.57928C12.0476 4.97723 13.3635 4.5264 14.7188 4.23636C14.5007 4.93945 14.0863 6.13111 13.5253 7.07345C13.5253 7.07345 14.9489 6.77645 16.1259 6.84428C15.4833 7.53453 14.904 8.28253 14.3173 9.04703C13.5143 10.0939 12.6838 11.1755 11.6196 12.1829C11.4912 12.3049 11.3693 12.4149 11.2456 12.5285C9.61025 12.3754 8.53133 12.9713 7.51933 14.0227C8.31683 13.6606 9.38933 13.3627 10.0603 13.5424C8.82283 14.5259 6.87308 15.8221 5.27167 15.7149C4.96733 16.1659 4.94808 16.1796 4.61442 16.7232C7.21317 17.3539 10.4765 14.7817 12.3969 12.9621C13.5235 11.8951 14.3815 10.7777 15.2111 9.69878C16.9179 7.47403 18.3928 5.5527 21.6644 4.82211L23 4.5242L21.8303 3.82936Z" fill="#343C54"/> </svg>
				                    <div>Gained 1 feather!<span>Earned by viewing a book.</span><span>You now have <p>${response.result}</p> feathers.</span></div>
				                </a>
				            `).hide();

				            $toastWrap.prepend($toastItem);
				            $toastItem.fadeIn(250);
				        }

				        $toastItem.css('--duration', duration + 'ms'); // set this toast's timer
				        setTimeout(() => { $toastItem.addClass('counting-down'); }, 20); // restart countdown bar
				        clearTimeout($toastItem.data('removeTimeout')); // clear old removal timer FOR THIS toast
				        const removeTimeout = setTimeout(function () { $toastItem.fadeOut(250, function () { $(this).remove(); }); }, duration);
				        $toastItem.data('removeTimeout', removeTimeout); // store timer on element
				    }
	            }
	        });

		}
	}, 3000);

});

/* 
    ----------------------------------------------------------------
    Script for flipbook
    ----------------------------------------------------------------
*/

pdfjsLib.GlobalWorkerOptions.workerSrc ="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.worker.min.js";

/*
// updated size variants
const SIZE_VARIANTS = {
    square:   { width: 1040, height: 600 },
    portrait: { width: 900, height: 650 }
};
*/

// updated size variants
const BASE_VARIANTS = {
    square:   { width: 1040, height: 600 },
    portrait: { width: 900,  height: 650 }
};

function getSizeVariant(layout) 
{
    const base  = BASE_VARIANTS[layout] || BASE_VARIANTS.square;
    const ratio = base.height / base.width;

    const container = document.querySelector('.flipbook-parent');
    const style = window.getComputedStyle(container);
	const paddingLeft = parseFloat(style.paddingLeft) || 0;
	const paddingRight = parseFloat(style.paddingRight) || 0;
	const availableWidth = container.clientWidth - paddingLeft - paddingRight;

    const width  = Math.min(availableWidth, base.width);
    const height = Math.round(width * ratio);

    return { width, height };
}

async function convertPDF(ltr) 
{

	showLoader();

    const flipbookDetails = document.getElementById('flipbook-details');
    const url = ltr ? flipbookDetails.dataset.pdf : flipbookDetails.dataset.rtl;

    // use layout instead of size
    const fb_layout = flipbookDetails.dataset.layout;
    // const size = SIZE_VARIANTS[fb_layout] || SIZE_VARIANTS.square;
    const size = getSizeVariant(fb_layout);

    console.log('pdf width' + size.width);
    console.log('pdf height' + size.height);

    const pdf = await pdfjsLib.getDocument(url).promise;
    const container = document.getElementById("flipbook");

    for (let i = 1; i <= pdf.numPages; i++) 
    {
        const page = await pdf.getPage(i);

        // get unscaled viewport
        const unscaledViewport = page.getViewport({ scale: 1 });

        // scale PDF width to match selected layout width
        const scale = size.width / unscaledViewport.width;
        const viewport = page.getViewport({ scale });

        const canvas = document.createElement("canvas");
        const ctx = canvas.getContext("2d");

        canvas.width = viewport.width;
        canvas.height = viewport.height;

        await page.render({ canvasContext: ctx, viewport }).promise;

        container.appendChild(canvas);
    }

    convertToFlipbook(ltr);
}

if (document.getElementById('flipbook')) { convertPDF(true); } 

function convertToFlipbook(ltr) {

  	jQuery(function ($) {

		// hide the both audio wrappers by default
		$('.flipbook-audio').hide();
		$('.flipbook-audio-bilingual').hide();
		disableRefreshBtn(); // hide refresh button on load

	  	// flipbook details
	  	var fb_rtl   		= $('#flipbook-details').data('rtl');
		var fb_layout 		= $('#flipbook-details').data('layout'); 
		var fb_doublepage  	= $('#flipbook-details').data('doublepage');
		// var size 		= SIZE_VARIANTS[fb_layout] || SIZE_VARIANTS.square; // match layout to size variant
		var size 			= getSizeVariant(fb_layout);
		var resizeTimer;

		// resize wrapper based on book layout
		$('.flipbook-wrapper').css('width', size.width+'px');
		$('.flipbook-wrapper').css('height', size.height+'px');

		if (fb_doublepage) { $("#flipbook").prepend('<div class="sheet"></div>'); }

		if (ltr) { setupLtrControls(); } else { setupRtlControls(); }

		// initialize book
		$("#flipbook").turn({
			width: size.width,
			height: size.height,
			autoCenter: true,
			overlays: true,
			display: 'double',
			direction: (ltr == true ? 'ltr' : 'rtl'),
			gradients: true,
			acceleration: true,
			duration: 1000,
			page: (fb_doublepage == true ? 3 : 1),
		});

		// controls --> refresh
		$("#flipbook-btn-refresh").off('click').on('click', function() {
			if (isRefreshBtnActive())
			{
				stopAllAudio();
		    	destroyFlipbook();
    			convertPDF(true); 

				// record web activity
				$.ajax({
		            url: book_ajax.url, // WP auto-provides this in admin; for frontend use wp_localize_script
		            type: 'POST',
		            data: {
		                action: 'l4k_addWebActivityViaAjax',
		                alert_code: '1062',
				        story_id: $('body').data('book-id'),
				        story_title: $('body').data('book-title'),
				        language: $('body').data('book-lang'),
				        type: 'flipbook',
		            },
		            success: function(response){
					    console.log(response);
		            }
		        });

    			/*
				if (fb_doublepage) { $('#flipbook').turn('page', 3); }
				else { $('#flipbook').turn('page', 1); }

				$('#flipbook-btn-audio').removeClass('active');
				$('#flipbook-btn-audio').children('i').removeClass('pulse-shadow');
				$('#flipbook-btn-audio-bilingual').removeClass('active');
				$('#flipbook-btn-audio-bilingual').children('i').removeClass('pulse-shadow');
				stopAllAudio();
				*/
			}
		});

		// controls --> next
		$("#flipbook-btn-next").off('click').on('click', function() {
			if (isNextBtnActive()) { $("#flipbook").turn("next"); }
		});

		// controls --> previous
		$("#flipbook-btn-prev").off('click').on('click', function() {
			if (isPrevBtnActive()) { $("#flipbook").turn("previous"); }
		});	

		// controls --> fullscreen
		$("#flipbook-btn-fullscreen").off('click').on('click', function() {
		    toggleFakeFullscreen();
		});	

		$(window).off('resize.flipbook').on('resize.flipbook', function() {
		    if ($('.flipbook-parent').hasClass('is-fullscreen')) {
		        // fullscreen: just scale, no need to regenerate
		        updateFullscreenScale();
		    } else {
		        // normal: debounce regeneration to avoid firing mid-drag
		        clearTimeout(resizeTimer);
		        resizeTimer = setTimeout(function() {
		            const newSize = getSizeVariant(fb_layout);
		            // only regenerate if width actually changed
		            if (newSize.width !== size.width) {
		                stopAllAudio();
		                destroyFlipbook();
		                convertPDF(ltr);
		            }
		        }, 300); // wait 300ms after resize stops
		    }
		});

		// controls --> play audio file
		$('#flipbook-btn-audio').off('click').on('click', function() 
		{
			toggleAudioPulse(this);
			deactivateAudioBilingualOption();

		    if (isAudioPlayerActive) 
		    { 
		    	stopAllAudio();
		    	playAudioFile(getCurrentAudioPage($("#flipbook").turn("page"))); 
		    }
		});

		// controls --> play audio bilingual file
		$('#flipbook-btn-audio-bilingual').off('click').on('click', function() 
		{
		    toggleAudioPulse(this);
		    deactivateAudioOption();

		    if (isAudioBilingualPlayerActive) 
		    { 
		    	stopAllAudio();
		    	playAudioBilingualFile(getCurrentAudioPage($("#flipbook").turn("page"))); 
		    }
		});

		// controls --> rtl
		$('#flipbook-btn-rtl').off('click').on('click', function() 
		{
			if (isCurrentlyInLTR()) 
			{
				stopAllAudio();
			    destroyFlipbook();
			 	convertPDF(false);

				// record web activity
				$.ajax({
		            url: book_ajax.url, // WP auto-provides this in admin; for frontend use wp_localize_script
		            type: 'POST',
		            data: {
		                action: 'l4k_addWebActivityViaAjax',
		                alert_code: '1062',
				        story_id: $('body').data('book-id'),
				        story_title: $('body').data('book-title') + ' R2L',
				        language: $('body').data('book-lang'),
				        type: 'flipbook',
		            },
		            success: function(response){
					    console.log(response);
		            }
		        });
			 }
		});

		// controls --> ltr
		$('#flipbook-btn-ltr').off('click').on('click', function() 
		{	
			if (isCurrentlyInRTL()) 
			{
				stopAllAudio();
			    destroyFlipbook();
	    		convertPDF(true); 

				// record web activity
				$.ajax({
		            url: book_ajax.url, // WP auto-provides this in admin; for frontend use wp_localize_script
		            type: 'POST',
		            data: {
		                action: 'l4k_addWebActivityViaAjax',
		                alert_code: '1062',
				        story_id: $('body').data('book-id'),
				        story_title: $('body').data('book-title') + ' L2R',
				        language: $('body').data('book-lang'),
				        type: 'flipbook',
		            },
		            success: function(response){
					    console.log(response);
		            }
		        });
	    	}
		});

  		// controls -> left and right arrow buttons on the keyboard
		$(document).off('keyup.flipbookArrows').on('keyup.flipbookArrows', function(e) 
		{
		    if (e.key === "ArrowRight") { $("#flipbook-btn-next").trigger('click'); }
		    if (e.key === "ArrowLeft")  { $("#flipbook-btn-prev").trigger('click'); }
		});

		// controls -> F key for fullscreen
		$(document).off('keyup.flipbookFullscreen').on('keyup.flipbookFullscreen', function(e) {
		    if (e.key === 'f' || e.key === 'F') {
		        $("#flipbook-btn-fullscreen").trigger('click');
		    }
		});

		// controls -> close fullscreen using ESC button
		$(document).off('keyup.flipbookEsc').on('keyup.flipbookEsc', function(e) 
		{
		    if (e.key === "Escape") {
		        if ($('.flipbook-parent').hasClass('is-fullscreen')) {
		            toggleFakeFullscreen();
		        }
		    }
		});

		getCurrentAudioPage($("#flipbook").turn("page"));

		// page turning
		$("#flipbook").bind('turning', function(event, page) 
		{
			// prevent users from going to page 1 if book is double paged
			if (fb_doublepage) {
			    if (page === 1) {
			        event.preventDefault();
			        $(this).turn('page', 3);
			    }
			}

		    var totalPages = (fb_doublepage) ? (parseInt($(this).turn('pages'))-1) : $(this).turn('pages'); 
		    var firstPage = (fb_doublepage) ? 3 : 1; 

		    // disable next button if reached the last page
		    if (page === totalPages) { disableNextBtn(); } 
		    else { enableNextBtn(); }

			// disable prev button if reached the first page
		    if ((page === firstPage) || (page === 1)) { disablePrevBtn(); disableRefreshBtn(); } 
		    else { enablePrevBtn(); enableRefreshBtn(); }

		    stopAllAudio();
		    if (isAudioPlayerActive()) { playAudioFile(getCurrentAudioPage(page)); }
		    else if (isAudioBilingualPlayerActive()) { playAudioBilingualFile(getCurrentAudioPage(page)); }
		    else { getCurrentAudioPage(page); }
		});

		/*
	    // full page navigation
		$("#flipbook").off('click').on('click', function(e) 
		{
			let offset = $(this).offset();
			let x = e.pageX || (e.originalEvent.touches ? e.originalEvent.touches[0].pageX : 0);
			let pageWidth = $(this).width() / 2;

			if (x - offset.left < pageWidth) { $(this).turn('previous'); } 
			else { $(this).turn('next'); }
		});
		*/

		// full page navigation with RTL support
		$("#flipbook").off('click').on('click', function(e) 
		{
		    let $this = $(this);
		    let offset = $this.offset();
		    let x = e.pageX || (e.originalEvent.touches ? e.originalEvent.touches[0].pageX : 0);
		    let pageWidth = $this.width() / 2;

		    if (ltr) 
		    {
	         	if (x - offset.left < pageWidth) { $this.turn('previous'); } 
	         	else { $this.turn('next'); }
		    } 
		    else 
		    {
		       	if (x - offset.left < pageWidth) { $this.turn('next'); } 
		       	else { $this.turn('previous'); }
		    }
		});

		hideLoader(); // hide loading once done + show controls

		function getCurrentAudioPage(page) 
		{
			// get audio page
			var result = Math.floor((parseInt(page) / 2) + 1);
		    var audioPage = (page == 4) ? 3 : result;
		    console.log('page: ' + page + ' | audio page: ' + audioPage);
		    return audioPage;
		}

		function enableNextBtn() { $("#flipbook-btn-next").removeClass('disabled'); return; }
		function enablePrevBtn() { $("#flipbook-btn-prev").removeClass('disabled'); return; }
		function enableRefreshBtn() { $("#flipbook-btn-refresh").removeClass('disabled'); return; }
		function enableLtrBtn() { $("#flipbook-btn-ltr").removeClass('disabled'); return; }
		function enableRtlBtn() { $("#flipbook-btn-rtl").removeClass('disabled'); return; }

		function disableNextBtn() { $("#flipbook-btn-next").addClass('disabled'); return; }
		function disablePrevBtn() { $("#flipbook-btn-prev").addClass('disabled'); return; }
		function disableRefreshBtn() { $("#flipbook-btn-refresh").addClass('disabled'); return; }
		function disableLtrBtn() { $("#flipbook-btn-ltr").addClass('disabled'); return; }
		function disableRtlBtn() { $("#flipbook-btn-rtl").addClass('disabled'); return; }

		function isPrevBtnActive() 
		{
			if ($('#flipbook-btn-prev').hasClass('disabled')) { return false; }
			else { return true; }
		}

		function isNextBtnActive() 
		{
			if ($('#flipbook-btn-next').hasClass('disabled')) { return false; }
			else { return true; }
		}

		function isRefreshBtnActive() 
		{
			if ($('#flipbook-btn-refresh').hasClass('disabled')) { return false; }
			else { return true; }
		}

		function isAudioPlayerActive() 
		{
			if ($('#flipbook-btn-audio').hasClass('active')) { return true; }
			else { return false; }
		}

		function isAudioBilingualPlayerActive() 
		{
			if ($('#flipbook-btn-audio-bilingual').hasClass('active')) { return true; }
			else { return false; }
		}

		function isCurrentlyInRTL() 
		{
			if ($('#flipbook-btn-rtl').hasClass('disabled')) { return true; }
			else { return false; }
		}

		function isCurrentlyInLTR() 
		{
			if ($('#flipbook-btn-ltr').hasClass('disabled')) { return true; }
			else { return false; }
		}

		function playAudioFile(audioPage) 
		{
			var audioElement = $('.flipbook-audio audio[data-page-number="'+audioPage+'"]');

		    if (isAudioPlayerActive() && (audioElement.length > 0)) // check too if audio element is present
		    {
			    $('.flipbook-audio').show(); // show the audio wrapper
			    $('.flipbook-audio audio[data-page-number="'+audioPage+'"]').fadeIn(); // show audio of current audioPage

			    const audio = audioElement.get(0);
				if (audio) { audio.play(); }
		    }
		}

		function playAudioBilingualFile(audioPage) 
		{
			var audioElement = $('.flipbook-audio-bilingual audio[data-page-number="'+audioPage+'"]');

		    if (isAudioBilingualPlayerActive() && (audioElement.length > 0)) // check too if audio element is present
		    {
			    $('.flipbook-audio-bilingual').show(); // show the audio wrapper
			    $('.flipbook-audio-bilingual audio[data-page-number="'+audioPage+'"]').fadeIn(); // show audio of current audioPage

			    const audio = audioElement.get(0);
				if (audio) { audio.play(); }
		    }
		}

		function stopAllAudio() 
		{
			$('.flipbook-audio').hide();
			$('.flipbook-audio audio').each(function () { this.pause(); this.currentTime = 0; }); // stop all audio
		    $('.flipbook-audio audio').hide(); // hide all other audio

			$('.flipbook-audio-bilingual').hide();
			$('.flipbook-audio-bilingual audio').each(function () { this.pause(); this.currentTime = 0; }); // stop all audio
		    $('.flipbook-audio-bilingual audio').hide(); // hide all other audio
		}

		function toggleAudioPulse(element) 
		{
			$(element).toggleClass('active');
		    $(element).children('i').toggleClass('pulse-shadow');
		}

		function deactivateAudioOption() 
		{
			$('#flipbook-btn-audio').removeClass('active');
		    $('#flipbook-btn-audio').children('i').removeClass('pulse-shadow');
		}

		function deactivateAudioBilingualOption() 
		{
			$('#flipbook-btn-audio-bilingual').removeClass('active');
		    $('#flipbook-btn-audio-bilingual').children('i').removeClass('pulse-shadow');
		}

		function destroyFlipbook() 
		{
			try { $("#flipbook").turn("destroy"); } 
			catch (e) { console.log("Flipbook not initialized yet."); }
			$("#flipbook").empty();
		}

		function setupLtrControls() 
		{
			// flipbook is currently in LTR mode
			$('.flipbook-controls').removeClass('rtl');
			enableRtlBtn(); 
			disableLtrBtn();
		}

		function setupRtlControls() 
		{
			// flipbook is currently in RTL mode
			$('.flipbook-controls').addClass('rtl');
			disablePrevBtn();
			enableLtrBtn(); 
			disableRtlBtn();
		}

		function toggleFakeFullscreen() 
		{
		    const $wrapper = $('.flipbook-parent');
		    const $btn = $('#flipbook-btn-fullscreen');

		    if ($wrapper.hasClass('is-fullscreen')) {
		        // exit: move back to original position
		        $('#flipbook-parent-placeholder').replaceWith($wrapper);
		        $wrapper.removeClass('is-fullscreen');
		        $btn.removeClass('active');
		        $('body').removeClass('_noscroll');

		        // restore flipbook to original dimensions
		        $('#flipbook').css({ width: size.width + 'px', height: size.height + 'px' });
		        try { $('#flipbook').turn('size', size.width, size.height); } catch(e) {}

		        // restore wrapper to original dimensions (was missing before)
		        $('.flipbook-wrapper').css({ width: size.width + 'px', height: size.height + 'px' });

		        // clear any leftover transform scale
		        $wrapper[0].style.removeProperty('--fb-scale');
		        $('#flipbook').css('transform', '');

		        $btn.html('<i class="lni lni-expand-arrow-1"></i>');
		        $btn.attr('data-tooltip', 'Maximize');
		    } else {
		        // enter: leave a placeholder behind, move wrapper to body
		        $('<div id="flipbook-parent-placeholder"></div>').insertAfter($wrapper);
		        $wrapper.appendTo('body');
		        $wrapper.addClass('is-fullscreen');
		        $btn.addClass('active');
		        $('body').addClass('_noscroll');

		        $btn.html('<i class="lni lni-expand-square-4"></i>');
		        $btn.attr('data-tooltip', 'Minimize');
		        updateFullscreenScale();
		    }
		}

		function updateFullscreenScale() 
		{
		    if (!$('.flipbook-parent').hasClass('is-fullscreen')) return;

		    const padding = 80; // px breathing room on each axis
		    const controlsHeight = 100; // approximate height of .flipbook-controls

		    const availW = window.innerWidth  - (padding * 2);
		    const availH = window.innerHeight - (padding * 2) - controlsHeight;

		    const scaleX = availW / size.width;
		    const scaleY = availH / size.height;
		    const scale  = Math.min(scaleX, scaleY);

		    const newW = Math.floor(size.width  * scale);
		    const newH = Math.floor(size.height * scale);

		    // resize the flipbook element itself
		    $('#flipbook').css({ width: newW + 'px', height: newH + 'px' });

		    // also resize turn.js to match
		    try {
		        $('#flipbook').turn('size', newW, newH);
		    } catch(e) {
		        // turn.js not ready yet, css resize is enough
		    }

		    // update wrapper to match
		    $('.flipbook-wrapper').css({ width: newW + 'px', height: newH + 'px' });

		    // no CSS transform scale needed anymore — reset it
		    $('.flipbook-parent')[0].style.removeProperty('--fb-scale');
		    $('#flipbook').css('transform', '');
		}

  	});

}

function hideLoader() 
{
    const loader = document.querySelector('.flipbook-wrapper__loading');
    const controls = document.querySelector('.flipbook-controls');

    if (loader) loader.style.display = 'none';
    if (controls) controls.classList.add('show');

   	// if we're in fullscreen, re-apply the scaled dimensions after flipbook reinitializes
    if (document.querySelector('.flipbook-parent.is-fullscreen')) {
        const fb_layout = document.getElementById('flipbook-details').dataset.layout;
        // const size = SIZE_VARIANTS[fb_layout] || SIZE_VARIANTS.square;
        const size = getSizeVariant(fb_layout);

        const padding = 80;
        const controlsHeight = 100;

        const availW = window.innerWidth  - (padding * 2);
        const availH = window.innerHeight - (padding * 2) - controlsHeight;

        const scaleX = availW / size.width;
        const scaleY = availH / size.height;
        const scale  = Math.min(scaleX, scaleY);

        const newW = Math.floor(size.width  * scale);
        const newH = Math.floor(size.height * scale);

        jQuery('#flipbook').css({ width: newW + 'px', height: newH + 'px' });
        jQuery('.flipbook-wrapper').css({ width: newW + 'px', height: newH + 'px' });

        try {
            jQuery('#flipbook').turn('size', newW, newH);
        } catch(e) {
            console.log('turn resize after refresh:', e);
        }
    }
}

function showLoader() 
{
    const loader = document.querySelector('.flipbook-wrapper__loading');
    const controls = document.querySelector('.flipbook-controls');

    if (loader) loader.style.display = 'block';
    if (controls) controls.classList.remove('show');
}