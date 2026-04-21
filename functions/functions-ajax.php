<?php 
/**
 * ----------------------------------------------------------------
 * Login from the library barcode
 * ----------------------------------------------------------------
 */

add_action('wp_ajax_l4k_loginToLibrary', 'l4k_loginToLibrary');
add_action('wp_ajax_nopriv_l4k_loginToLibrary', 'l4k_loginToLibrary');

function l4k_loginToLibrary() {

    if (!session_id()) { session_start(); }

    if ($_POST['library_id']) { $libraryDetails = l4k_getLibraryDetails($_POST['library_id']); }

    $overallMatched = false;
	$matchedErrorMsg = 'Invalid library barcode or card number. Please try again.';

	// a teacher is trying to login to the default teacher library
	if ($_POST['library_id'] == get_field('teacher_default_library', 'option')) {

		global $wpdb;

		// check if teacher is existing in alternate_barcodes table
        $teacherFound = $wpdb->get_row(
          	$wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}alternate_barcode WHERE barcode = %s LIMIT 1",
                $_POST['barcode']
            )
        );

        // if teacher is found, log them in to WP CMS as well (as subscriber)
	    if ($teacherFound) { 
    	   	$user = get_user_by('email', $teacherFound->email);
		    if ($user) {
				wp_set_current_user($user->ID);
		    	wp_set_auth_cookie($user->ID, true);
				update_user_meta($user->ID, 'last_login', current_time('mysql'));
		    }
		    
	        // generate a secure session token and update the table
	        $_SESSION['session_token'] = bin2hex(random_bytes(32));
	        $wpdb->update(
	            "{$wpdb->prefix}alternate_barcode",
	            [ 'session_token' => $_SESSION['session_token'] ],
	            [ 'barcode' => $_POST['barcode'] ],
	            [ '%s' ],
	            [ '%s' ]
	        );


	    	$overallMatched = true; 
	    }

	}

	// a normal user is trying to login using his/her barcode
    if ($libraryDetails['barcodes'] && is_array($libraryDetails['barcodes'])) {

		foreach ($libraryDetails['barcodes'] as $barcode => $b) {

			$skipDateCheck = false;
			$matchedBarcode = true;
			$matchedErrorMsg = '';

			// check barcode prefix
			if (stripos($_POST['barcode'], $b['barcode_prefix']) !== 0) { 
				$matchedBarcode = false; 
				$matchedErrorMsg = 'Invalid library barcode or card number. Please try again.';
			} 

			// check barcode length
			if (strlen($_POST['barcode']) != $b['barcode_length']) { 
				$matchedBarcode = false; 
				$matchedErrorMsg = 'Invalid library barcode or card number. Please try again.';
			} 

	        // check if barcode exists in alternate_barcode and validate expiration_date
	        if ($matchedBarcode) {
	            global $wpdb;
	            $alt = $wpdb->get_row(
	              	$wpdb->prepare(
	                    "SELECT * FROM {$wpdb->prefix}alternate_barcode WHERE barcode = %s LIMIT 1",
	                    $_POST['barcode']
	                )
	            );
	            if ($alt) {
	                if (!empty($alt->expiration_date)) {
	                    $altExpiry = strtotime($alt->expiration_date);
	                    if ($altExpiry === false || time() > $altExpiry) {
	                        $matchedBarcode = false;
	                        $matchedExpiredMsg = 'Barcode expired. Please contact us <a href="/contact-us" target="_blank">here</a>.';
	                    }
	                }
	                // if found in alternate table (and not expired), skip the date range check below
	                $skipDateCheck = true;
	            }
	        }

	        // check barcode validity only if start/end date has values
	        if (empty($skipDateCheck) && $b['barcode_start_date'] && $b['barcode_end_date'] && $matchedBarcode) {
	            $start = strtotime($b['barcode_start_date']);
	            $end   = strtotime($b['barcode_end_date']);
	            $now   = time();
	            if ($now < $start || $now > $end) {
	                $matchedBarcode = false; 
	                $matchedExpiredMsg = 'Barcode expired. Please contact us <a href="/contact-us" target="_blank">here</a>.';
	            }
	        }

			if ($matchedBarcode) { $overallMatched = true; }

			$rawData .= 'barcode_prefix : ' . $b['barcode_prefix'] . '<br/>';
			$rawData .= 'barcode_length : ' . $b['barcode_length'] . '<br/>';
			$rawData .= 'barcode_start_date : ' . $b['barcode_start_date'] . '<br/>';
			$rawData .= 'barcode_end_date : ' . $b['barcode_end_date'] . '<br/>';
			$rawData .= 'matchedBarcode : ' . $matchedBarcode . '<br/>';
			$rawData .= 'matchedErrorMsg : ' . $matchedErrorMsg . '<br/>';
			$rawData .= 'matchedExpiredMsg : ' . $matchedExpiredMsg . '<br/>';
			$rawData .= "<hr/>";

		}

    }

    if ($overallMatched) 
    {
		// record event 900 for user login (user_login_logs table) - if row is not existing yet
		$ownerID = l4k_addUserLoginActivity(
			'900', 
			strtoupper($_POST['barcode']),
			get_field('library_group_name', $_POST['library_id']), 
			get_field('library_group_region', $_POST['library_id']), 
			'english', current_time('mysql'), '', '', 'active');

		// record this event (characters table) - if row is not existing yet
		l4k_addCharacterActivity($ownerID);

    	$_SESSION['ownerID'] 				= $ownerID;
    	$_SESSION['library_id'] 			= $_POST['library_id'];
    	$_SESSION['library_welcome_logo'] 	= get_field('logo_welcome', $_POST['library_id']);
    	$_SESSION['library_permalink'] 		= get_the_permalink($_POST['library_id']);
    	$_SESSION['library_barcode'] 		= strtoupper($_POST['barcode']);
    	$_SESSION['library_name']	 		= get_the_title($_POST['library_id']);
    	$_SESSION['library_group']	 		= get_field('library_group_name', $_POST['library_id']);
    	$_SESSION['library_region'] 		= get_field('library_group_region', $_POST['library_id']);
    	$_SESSION['library_remember'] 		= intval($_POST['remember']);  
    	$_SESSION['last_viewed_book'] 		= '';  
    	$_SESSION['auth_token'] 			= get_field('auto_login_auth_token', $_POST['library_id']);
    	$_SESSION['auto_login_status'] 		= get_field('auto_login_status', $_POST['library_id']);
    	$_SESSION['is_teacher'] 			= ($_POST['library_id'] == get_field('teacher_default_library', 'option')) ? true : false;

		l4k_addWebActivity('900'); // record event 900 for member login (web_activity table) - fully incremental
		l4k_rememberLoginCookie(); // create persistent cookie storing session data

	    $resultsArr = array(
	    	'status' 		=> 1,
		    'message' 		=> 'Session started and saved!',
		    'redirect_to' 	=> $_SESSION['redirect_to'],
		    'raw_data' 		=> $rawData);  	
    }
   	else
   	{

   		// record invalid logins
   		l4k_addWebActivity(901, array(	
    		'url' 		=> get_permalink($_POST['library_id']),
			'library' 	=> get_the_title($_POST['library_id']),
			'barcode' 	=> $_POST['barcode'] ));

	    $resultsArr = array(
	    	'status' 	=> 0,
		    'message' 	=> ($matchedExpiredMsg) ? $matchedExpiredMsg : $matchedErrorMsg,
		    'raw_data' 	=> $rawData);
   	}

   	wp_send_json($resultsArr);

}

/**
 * ----------------------------------------------------------------
 * Get dashboard content
 * ----------------------------------------------------------------
 */

add_action('wp_ajax_l4k_getLearningDashboardContent', 'l4k_getLearningDashboardContent');
add_action('wp_ajax_nopriv_l4k_getLearningDashboardContent', 'l4k_getLearningDashboardContent');

/**
 * ----------------------------------------------------------------
 * Log web activity via ajax
 * Reuse the function l4k_addWebActivity
 * Just pass the parameters to it
 * ----------------------------------------------------------------
 */

add_action('wp_ajax_l4k_addWebActivityViaAjax', 'l4k_addWebActivityViaAjax');
add_action('wp_ajax_nopriv_l4k_addWebActivityViaAjax', 'l4k_addWebActivityViaAjax');

function l4k_addWebActivityViaAjax() {

    if ($_POST) 
    { 
    	$alertCode = $_POST['alert_code'];

    	if ($alertCode == '1060') {
	    	$dataArr = array(	
	    		'Activity Name' => $_POST['activity_name'],
	    		'Activity Title' => $_POST['activity_title'],
	    		'Activity Type'	=> $_POST['activity_type']);    		
    	}
    	
    	if ($alertCode == '1062') {
	    	$dataArr = array(	
	    		'Story ID' => $_POST['story_id'],
				'Story Title' => $_POST['story_title'],
				'Language' => $_POST['language'],
				'Type' => $_POST['type']);    		
    	}

    	if ($alertCode == '1090') {
	    	$dataArr = array(	
	    		'Language' => $_POST['language']);    		
    	}

    }

    $insertID = l4k_addWebActivity($alertCode, $dataArr);

    if ($insertID) {
	    $resultsArr = array(
	    	'status' 	=> 1,
			'insert_id' => $insertID,
			'message' 	=> 'Sucessfully logged '.$alertCode.'!');
	} else { 
		$resultsArr = array(
			'status' 	=> 0,
			'message' 	=> 'Unable to save to database!');
	}
   
   	wp_send_json($resultsArr);

}

/**
 * ----------------------------------------------------------------
 * Increment book view
 * ----------------------------------------------------------------
 */

add_action('wp_ajax_l4k_incrementViewsViaAjax', 'l4k_incrementViewsViaAjax');
add_action('wp_ajax_nopriv_l4k_incrementViewsViaAjax', 'l4k_incrementViewsViaAjax');

function l4k_incrementViewsViaAjax() {

    if ($_POST['book_id']) 
    { 
    	l4k_updateLastViewedBook($_POST['book_id']); // for last viewed book in recent videos
    	
		$viewCount 	= l4k_incrementViews($_POST['book_id']);
	    $resultsArr = array('status' 		=> 1,
	    					'view_count' 	=> $viewCount,
		    				'message' 		=> 'Sucessfully incremented book view!');
    }
   
   	wp_send_json($resultsArr);

}

/**
 * ----------------------------------------------------------------
 * Add feather count via ajax
 * ----------------------------------------------------------------
 */

add_action('wp_ajax_l4k_addFeatherCountViaAjax', 'l4k_addFeatherCountViaAjax');
add_action('wp_ajax_nopriv_l4k_addFeatherCountViaAjax', 'l4k_addFeatherCountViaAjax');

function l4k_addFeatherCountViaAjax() {

    if ($_POST['owner_id']) 
    { 
		$newPointsCount = l4k_addFeatherCount($_POST['owner_id'], $_POST['url']);
		if ($newPointsCount) {
		    $resultsArr = array(
		    	'status' 	=> 1,
		    	'result'	=> $newPointsCount,
			    'message' 	=> 'Sucessfully added +1 feather to database!');
		} else {
		    $resultsArr = array(
		    	'status' 	=> 0,
		    	'result'	=> 'Unable to add feather count, may be duplicate',
			    'message' 	=> 'You may check error log');
		}

    }
   
   	wp_send_json($resultsArr);

}

/**
 * Handle AJAX request to load video player for Book post type
 */

add_action('wp_ajax_aiovg_load_video', 'handle_book_playlist_load_video');
add_action('wp_ajax_nopriv_aiovg_load_video', 'handle_book_playlist_load_video');

function handle_book_playlist_load_video() {
    $video_id = isset($_POST['video_id']) ? intval($_POST['video_id']) : 0;
    if (!$video_id) wp_send_json_error(['message' => 'Invalid ID']);

    
    $video_url = get_post_meta($video_id, 'video_source', true); 
    $vimeo_id = '';
    $vimeo_hash = '';

    if (preg_match('/(?:vimeo\.com\/(?:video\/)?|player\.vimeo\.com\/video\/)(\d+)(?:\/([a-f0-9]+))?/i', $video_url, $matches)) {
        $vimeo_id = $matches[1];
        $vimeo_hash = isset($matches[2]) ? $matches[2] : '';
    }

    $iframe_src = "https://player.vimeo.com/video/{$vimeo_id}";
    $iframe_src .= (!empty($vimeo_hash)) ? "?h={$vimeo_hash}&autoplay=1" : "?autoplay=1";

    $player_html = sprintf(
        '<div class="aiovg-player">
            <div class="aiovg-responsive-container" style="padding-bottom: 56.25%%;">
                <iframe src="%s" class="aiovg-responsive-element" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>
            </div>
        </div>',
        esc_url($iframe_src)
    );

    wp_send_json_success(['player_html' => $player_html]);
}

/**
 * ----------------------------------------------------------------
 * Check if barcode exists in the alternate_barcode table
 * ----------------------------------------------------------------
 */

add_action('wp_ajax_l4k_checkIfBarcodeIsTrial', 'l4k_checkIfBarcodeIsTrial');
add_action('wp_ajax_nopriv_l4k_checkIfBarcodeIsTrial', 'l4k_checkIfBarcodeIsTrial');

/**
 * ----------------------------------------------------------------
 * Update alternate_barcode table with the trial activity status
 * ----------------------------------------------------------------
 */

add_action('wp_ajax_l4k_updateTrialActivityStatus', 'l4k_updateTrialActivityStatus');
add_action('wp_ajax_nopriv_l4k_updateTrialActivityStatus', 'l4k_updateTrialActivityStatus');










// Handle teacher avatar upload
add_action('wp_ajax_l4k_upload_teacher_avatar', 'l4k_upload_teacher_avatar');
function l4k_upload_teacher_avatar() {
    check_ajax_referer('l4k_avatar_nonce', 'nonce');

    if (!is_user_logged_in()) {
        wp_send_json_error(['message' => 'Not logged in']);
    }

    if (empty($_FILES['avatar'])) {
        wp_send_json_error(['message' => 'No file provided']);
    }

    require_once ABSPATH . 'wp-admin/includes/image.php';
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';

    $user_id     = get_current_user_id();
    $attachment_id = media_handle_upload('avatar', 0);

    if (is_wp_error($attachment_id)) {
        wp_send_json_error(['message' => $attachment_id->get_error_message()]);
    }

    // Delete old avatar attachment if one exists
    $old_id = get_user_meta($user_id, 'l4k_avatar_attachment_id', true);
    if ($old_id) {
        wp_delete_attachment($old_id, true);
    }

    update_user_meta($user_id, 'l4k_avatar_attachment_id', $attachment_id);

    $url = wp_get_attachment_image_url($attachment_id, 'thumbnail');
    wp_send_json_success(['url' => $url]);
}

// Handle teacher name edit
add_action('wp_ajax_l4k_update_teacher_name', 'l4k_update_teacher_name');
function l4k_update_teacher_name() {
    check_ajax_referer('l4k_name_nonce', 'nonce');

    if (!is_user_logged_in()) {
        wp_send_json_error(['message' => 'Not logged in']);
    }

    $name = sanitize_text_field($_POST['name']);

    if (empty($name)) {
        wp_send_json_error(['message' => 'Name cannot be empty']);
    }

    $user_id = get_current_user_id();
    wp_update_user(['ID' => $user_id, 'display_name' => $name]);

    wp_send_json_success(['name' => $name]);
}

// Handle teacher information edit
add_action('wp_ajax_l4k_update_teacher_info', 'l4k_update_teacher_info');
function l4k_update_teacher_info() {
    check_ajax_referer('l4k_info_nonce', 'nonce');

    if (!is_user_logged_in()) {
        wp_send_json_error(['message' => 'Not logged in']);
    }

    $user_id = get_current_user_id();

    $fields = ['email', 'school', 'country', 'state', 'phone', 'currency'];
    $updated = [];

    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            $value = sanitize_text_field($_POST[$field]);
            if ($field === 'email') {
                if (!is_email($value)) {
                    wp_send_json_error(['message' => 'Invalid email address']);
                }
                wp_update_user(['ID' => $user_id, 'user_email' => $value]);
            } else {
                update_field($field, $value, 'user_' . $user_id);
            }
            $updated[$field] = $value;
        }
    }

    wp_send_json_success($updated);
}
?>