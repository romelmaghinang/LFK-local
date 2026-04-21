jQuery(function ($) {
    
    console.log('activity.js loaded!');

    /* 
        ----------------------------------------------------------------
        When loading an online activity
        - record 1060 event by default
        - feather claimed 
        - determine if there's custom tracking then record accordingly
        ----------------------------------------------------------------
    */

    if ($('.main-mid').data('activity-type') == 'online') {

		// record web activity
		$.ajax({
            url: activity_ajax.url, // WP auto-provides this in admin; for frontend use wp_localize_script
            type: 'POST',
            data: {
                action: 'l4k_addWebActivityViaAjax', 
                alert_code: '1060',
                activity_name: $('.main-mid').data('activity-title') + ' Online',
                activity_title: $('.main-mid').data('activity-title'),
                activity_type: 'Pdf',
            },
            success: function(response){
			    console.log(response);
            }
        });

    	// increment feather count
		$.ajax({
            url: activity_ajax.url, // WP auto-provides this in admin; for frontend use wp_localize_script
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

		// if custom tracking is set, perform custom tracking here
		if ($('.main-mid').data('tracking-status')) {
		    var duration = $('.main-mid').data('tracking-duration');
		    var maxDuration = $('.main-mid').data('tracking-max-duration');
		    var refreshPeriod = $('.main-mid').data('tracking-refresh-period');
		   
		    var executionCount = 1;
		    var maxExecutions = maxDuration / duration;
		    var intervalTime = duration * 60 * 1000; // convert minutes to milliseconds
		    var refreshTime = refreshPeriod * 60 * 1000; // convert minutes to milliseconds
		    
		    var trackingInterval;
		    
		    function startTracking() {
		        trackingInterval = setInterval(function() {
		            executionCount++;
		            
		            // record web activity
		            $.ajax({
		                url: activity_ajax.url,
		                type: 'POST',
		                data: {
		                    action: 'l4k_addWebActivityViaAjax', 
		                    alert_code: '1060',
		                    activity_name: $('.main-mid').data('activity-title') + ' Online',
		                    activity_title: $('.main-mid').data('activity-title'),
		                    activity_type: 'Pdf',
		                },
		                success: function(response){
		                    console.log(response);
		                }
		            });
		            
		            console.log('Tracking fired: ' + executionCount + ' of ' + maxExecutions);
		            
		            // stop when max duration is reached
		            if (executionCount >= maxExecutions) {
		                clearInterval(trackingInterval);
		                console.log('Tracking limit reached for this period');
		            }
		        }, intervalTime);
		    }
		    
		    startTracking(); // start tracking initially
		    
		    // reset execution count and restart tracking after refresh period
		    setInterval(function() {
		        clearInterval(trackingInterval); // clear the old interval
		        executionCount = 1; // reset to 1 to account for initial page load
		        console.log('Tracking count reset - new period started');
		        startTracking(); // restart tracking
		    }, refreshTime);
		}

	}

    /* 
        ----------------------------------------------------------------
        Feather claimed when clicking an activity in a collection
        ----------------------------------------------------------------
    */

	if ($('.main-mid').data('activity-type') == 'collection') {

		// when download button is clicked -> open pdf in a new tab
		// when download button is clicked -> record web activity via ajax
		// when download button is clicked -> attempt to increment feather count via ajax
		$('.perform-activity').on('click', function() {

			const actIdentifier = $(this).data('activity-title');

			// record web activity
			$.ajax({
	            url: activity_ajax.url, // WP auto-provides this in admin; for frontend use wp_localize_script
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
	            url: activity_ajax.url, // WP auto-provides this in admin; for frontend use wp_localize_script
	            type: 'POST',
	            data: {
	                action: 'l4k_addFeatherCountViaAjax',
	                owner_id: $('body').data('owner-id'),
	                url: 'Activity Collection - '+$(this).attr('href'),
	            },
	            success: function(response){
				    console.log(response);

				    // if successfully added to database, show toast
				    if (response.status == 1) {  
						const duration = 5000;
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

		});

	}
    
}); 
