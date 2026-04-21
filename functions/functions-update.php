<?php
/**
 * ----------------------------------------------------------------
 * Increment number of views for book
 * ----------------------------------------------------------------
 */

function l4k_updateBookNumViews($bookID, $preventDoubleCount=false) {

    if ($preventDoubleCount) { $cookie = 'book_viewed_' . $book_id; }

    if ($preventDoubleCount) {
	    if (!isset($_COOKIE[$cookie])) {
			l4k_incrementViews($bookID);
	        setcookie($cookie, 1, time() + DAY_IN_SECONDS, '/'); // prevent recount for 24h
	    }
    } else {
    	l4k_incrementViews($bookID); // increment as usual without checking cookie
    }

	return;

}

function l4k_incrementViews($bookID) {

    $total  = (int) get_post_meta($bookID, 'additional_details_views', true);
    $recent = (int) get_post_meta($bookID, 'additional_details_views_last_3_months', true);

    update_post_meta($bookID, 'additional_details_views', $total + 1);  // lifetime views
    update_post_meta($bookID, 'additional_details_views_last_3_months', $recent + 1); // rolling 3-month views

    return $total + 1;

}

/**
 * ----------------------------------------------------------------
 * Record activity - web
 * ----------------------------------------------------------------
 */

function l4k_addWebActivity($alertCode, $dataArr=array()) {

    global $wpdb;

    if ($alertCode == '100') {

	    $wpdb->insert(
	        $wpdb->prefix . 'web_activity',
	        array(
	            'alert_code' 	=> $alertCode,
	            'barcode' 		=> '',
	            'library_name' 	=> '',
	            'region_name' 	=> '',
	            'data' 			=> json_encode($dataArr),
	            'ip' 			=> l4k_getClientIP(),
	        )
	    );

    } 

    elseif ($alertCode == '901') {

	    $wpdb->insert(
	        $wpdb->prefix . 'web_activity',
	        array(
	            'alert_code' 	=> $alertCode,
	            'barcode' 		=> strtoupper($dataArr['barcode']),
	            'library_name' 	=> $dataArr['url'],
	            'region_name' 	=> '',
	            'data' 			=> json_encode($dataArr),
	            'ip' 			=> l4k_getClientIP(),
	        )
	    );

    } 

    else {

	    $wpdb->insert(
	        $wpdb->prefix . 'web_activity',
	        array(
	            'alert_code' 	=> $alertCode,
	            'barcode' 		=> strtoupper($_SESSION['library_barcode']),
	            'library_name' 	=> $_SESSION['library_group'], // save the group name instead of the library name
	            'region_name' 	=> $_SESSION['library_region'],
	            'data' 			=> json_encode($dataArr),
	            'ip' 			=> l4k_getClientIP(),
	        )
	    );

    }

    return $wpdb->insert_id;

}

/**
 * ----------------------------------------------------------------
 * Record user login activity (user_login_logs table)
 * ----------------------------------------------------------------
 */

function l4k_addUserLoginActivity($alertCode, $barCode, $libraryGroup, $region, $language, $time, $osType, $deviceType, $status) {

    global $wpdb;
    $table = $wpdb->prefix . 'user_login_logs';

    // check if combination of barCode, osType, and deviceType exists and get its ID
    $existingID = $wpdb->get_var(
        $wpdb->prepare(
            "SELECT id FROM $table WHERE barcode = %s AND os_type = %s AND device_type = %s LIMIT 1",
            $barCode,
            $osType,
            $deviceType
        )
    );

    if ($existingID) { return $existingID; } // record exists, return its ID

    // record does not exist, insert new
    $wpdb->insert(
        $table,
        array(
            'alert_code'    => $alertCode,
            'barcode'       => $barCode,
            'library_group' => $libraryGroup,
            'region'        => $region,
            'language'      => $language,
            'time'          => $time,
            'os_type'       => $osType,
            'device_type'   => $deviceType,
            'status'        => $status
        ),
        array(
            '%s','%s','%s','%s','%s','%s','%s','%s','%s'
        )
    );

    return $wpdb->insert_id; // return the ID of the newly inserted record
}

/**
 * ----------------------------------------------------------------
 * Record user login activity (characters table)
 * ----------------------------------------------------------------
 */

function l4k_addCharacterActivity($ownerID) {

    global $wpdb;
    $table = $wpdb->prefix . 'characters';

    // check if ownerID exists in the characters table
    $existingID = $wpdb->get_var(
        $wpdb->prepare(
            "SELECT id FROM $table WHERE ownerId = %s LIMIT 1",
            $ownerID,
        )
    );

    if ($existingID) { return $existingID; } // record exists, return its ID

    // record does not exist, insert new
    // 2 feathers by default for new barcodes
    $wpdb->insert(
        $table,
        array(
            'ownerId' => $ownerID,
            'boughtItems' => '[]',
            'gainPointsFrom' => '[]',
            'points' => 2
        ),
        array(
            '%s', '%s', '%s', '%s'
        )
    );

    return $wpdb->insert_id; // return the ID of the newly inserted record
}

/**
 * ----------------------------------------------------------------
 * aitrable - push mapped form data to Airtable when a form is submitted
 * ----------------------------------------------------------------
 */

function l4k_push_mapped_form_data($form_id, $entry_fields) {

    $api_token    = get_option('l4k_airtable_token');
    $all_mappings = get_option('l4k_airtable_mappings', []);

    $conf = $all_mappings[$form_id] ?? false;
    if (!$conf || empty($conf['base_id']) || empty($conf['table_id']) || !$api_token) return;

    $airtable_data = [];
    foreach ($conf['fields'] ?? [] as $airtable_column => $wpforms_field_id) {
        if (!empty($wpforms_field_id) && isset($entry_fields[$wpforms_field_id]['value'])) {
            $airtable_data[$airtable_column] = $entry_fields[$wpforms_field_id]['value'];
        }
    }

    if (empty($airtable_data)) return;

    $url = "https://api.airtable.com/v0/{$conf['base_id']}/{$conf['table_id']}";
    
    $response = wp_remote_post($url, [
        'headers' => [
            'Authorization' => 'Bearer ' . $api_token,
            'Content-Type'  => 'application/json',
        ],
        'body'    => wp_json_encode(['fields' => $airtable_data]),
        'timeout' => 30, 
    ]);

    $is_error = false;
    $error_cause = '';

    if (is_wp_error($response)) {
        $is_error = true;
        $error_cause = $response->get_error_message();
    } else {
        $code = wp_remote_retrieve_response_code($response);
        if ($code < 200 || $code >= 300) {
            $is_error = true;
            $body = json_decode(wp_remote_retrieve_body($response), true);
            $error_cause = $body['error']['message'] ?? 'API Error Code: ' . $code;
        }
    }

 
    if ($is_error) {
        $to = get_option('l4k_airtable_fail_email', get_option('admin_email'));
        $subject = 'FAILED: Airtable Sync - ' . get_the_title($form_id);
        $from_name      = 'Airtable Sync'; 
        $from_email     = get_option('l4k_airtable_fail_email', get_option('admin_email'));
        $bcc_recipients = get_option('l4k_airtable_fail_bcc');

        $headers = [
            "From: {$from_name} <{$from_email}>",
            "Content-Type: text/html; charset=UTF-8",
        ];

        if (!empty($bcc_recipients)) {
            $headers[] = "Bcc: {$bcc_recipients}";
        }
        
        //$table_name = l4k_get_table_name_by_id($conf['base_id'], $conf['table_id']);
        $table_display_name = !empty($conf['table_name']) ? $conf['table_name'] : $conf['table_id'];
        $page_url   = wp_get_referer() ?: home_url($_SERVER['REQUEST_URI']);

        
        $message  = "<h2>Airtable Submission Failed</h2>";
        $message .= "<p><strong>Reason for Failure:</strong> <span style='color:red;'>{$error_cause}</span></p>";
        $message .= "<hr>";
        $message .= "<strong>Form Name:</strong> " . get_the_title($form_id) . "<br>";
        $message .= "<strong>Airtable Table:</strong> " . esc_html($table_display_name) . "<br>";
        $message .= "<strong>Submitted from URL:</strong> " . esc_url($page_url) . "<br>";
        $message .= "<br><strong>SUBMITTED DATA:</strong><br>";

        foreach ($airtable_data as $field_name => $value) {
           
            $message .= "<strong>" . esc_html($field_name) . ":</strong> " . esc_html($value) . "<br>";
        }

        wp_mail($to, $subject, $message, $headers);
    }

}

/**
 * Helper to get the Table Name from Metadata
 */
function l4k_get_table_name_by_id($base_id, $table_id) {
  
    $tables = l4k_get_airtable_tables($base_id);
    if (!empty($tables)) {
        foreach ($tables as $table) {
            if ($table['id'] === $table_id) {
                return $table['name'];
            }
        }
    }
    return $table_id; 
}

/**
 * ----------------------------------------------------------------
 * Make sure that in the teacher subscription form - the email 
 * address is unique and can only be registered once
 * ----------------------------------------------------------------
 */

add_action('wpforms_process', function($fields, $entry, $form_data) {
    if ($form_data['id'] != '218230') return;

    $email = isset($fields[3]['value']) ? sanitize_email($fields[3]['value']) : '';

    if ($email && email_exists($email)) {
        wpforms()->process->errors[$form_data['id']][3] = 
            'This email address is already registered. Please log in instead.';
    }
}, 10, 3);

/**
 * ----------------------------------------------------------------
 * Capture data from trial form
 * ----------------------------------------------------------------
 */

function l4k_saveFormEntriesToDB($fields, $entry, $formData, $entry_id) {

    global $wpdb;
    
    $formID = $formData['id'];
	$referer = wp_get_referer();
	$isCompetitionForm = (strpos($referer, '/competition') !== false);
    $isTeacherSubForm = ($formID == '218230');
    $libraryID = ($isTeacherSubForm) ? get_field('teacher_default_library', 'option') : get_the_ID();

    // do NOT process or save to the database the following forms
    // 164381 (FAQ Contact Form), 164353 (Main Contact Form)
    // 164243 (Sidebar Feedback Form), 164240 (Footer Contact Form), 164614 (Staff Feedback Form)
    if (in_array($formID, array('164381', '164353', '164243', '164240', '164614'))) { return; }

	l4k_push_mapped_form_data($formID, $fields); // save to airtable regardless if coming from trial, competition, or teacher

    $websiteurl = '';
    if (isset($_POST['page_url'])) {
        $websiteurl = esc_url_raw($_POST['page_url']);
        $_SESSION['trialurl'] = $websiteurl;
    }

    $website = str_replace('/trial/', '/', trailingslashit($websiteurl));
    $_SESSION['website'] = $website;
    $trail_website = $_SESSION['website'];

    if (!$isTeacherSubForm) {
		// get barcode prefix (first only) for the library
		$barcode = get_field('library_barcodes', $libraryID);
		if ($barcode && is_array($barcode)) {
		    $firstRow = $barcode[0];
		    $firstFieldValue = $firstRow['barcode_prefix'];
		}
	}

    $barcodePrefix  = ($isTeacherSubForm) ? l4k_generateTeacherBarcodePrefix($fields[3]['value']) : $firstFieldValue;
    $barcodeNumber  = ($isTeacherSubForm) ? l4k_getNextTeacherBarcodeNumber() : l4k_getNextBarcodeNumber($barcodePrefix);
    $barcode 		= $barcodePrefix.$barcodeNumber;

    if ($isTeacherSubForm) {
		$name 		= !empty($fields[1]['value']) 	? $fields[1]['value'] 	: ''; 
	    $email 		= !empty($fields[3]['value']) 	? $fields[3]['value'] 	: '';
	    $school	 	= !empty($fields[2]['value']) 	? $fields[2]['value'] 	: '';
	    $country	= !empty($fields[5]['value']) 	? $fields[5]['value'] 	: '';
	    $state		= !empty($fields[10]['value']) 	? $fields[10]['value'] 	: '';
	    $phone	 	= !empty($fields[4]['value']) 	? $fields[4]['value'] 	: '';
	    $currency 	= !empty($fields[12]['value']) 	? $fields[12]['value'] 	: '';
	    $library 	= 'Teacher Login';
	    $jobTitle	= 'Teacher';
    } else {
		$name 		= !empty($fields[1]['value']) ? $fields[1]['value'] : ''; 
	    $library 	= !empty($fields[2]['value']) ? $fields[2]['value'] : ''; 
	    $email 		= !empty($fields[3]['value']) ? $fields[3]['value'] : '';
	    $phone	 	= !empty($fields[4]['value']) ? $fields[4]['value'] : '';
	    $jobTitle 	= !empty($fields[5]['value']) ? $fields[5]['value'] : '';
    }

	$currentTime 	= current_time('mysql');
    $expirationDate = ($isTeacherSubForm) ? date('Y-m-d H:i:s', strtotime($currentTime . ' +365 days')) : date('Y-m-d H:i:s', strtotime($currentTime . ' +14 days'));

    // save to session so on auto redirect, we can login based on the details here
    $_SESSION['trial_library'] = $libraryID;
    $_SESSION['trial_barcode'] = $barcode;

	// save to session so we can utilize this again on redirect
    if ($isCompetitionForm) {
    	$_SESSION['is_competition'] 				= $isCompetitionForm;
		$_SESSION['is_competition__fields'] 		= $fields;  
		$_SESSION['is_competition__form'] 			= $formID;  
		$_SESSION['is_competition__email'] 			= $email;
		$_SESSION['is_competition__name'] 			= $name;
		$_SESSION['is_competition__library'] 		= $library;
		$_SESSION['is_competition__barcode_prefix'] = $barcodePrefix;
		$_SESSION['is_competition__barcode_number'] = $barcodeNumber;
		$_SESSION['is_competition__phone'] 			= $phone;
		$_SESSION['is_competition__job_title'] 		= $jobTitle;
		$_SESSION['session_token']					= ($isTeacherSubForm) ? bin2hex(random_bytes(32)) : '';
    }

    // insert data to alternate_barcode table only if NOT coming from competition
    // trial forms and teacher forms should still be saved in the alternate_barcode table
    if (!$isCompetitionForm) {
		$result = $wpdb->insert(
		    $wpdb->prefix . 'alternate_barcode',
		    array(
		        'barcode_prefix' 	=> $barcodePrefix,
		        'barcode_number' 	=> $barcodeNumber,
		        'barcode' 			=> $barcode,
		        'name' 				=> $name,
		        'library' 			=> $library,
		        'email' 			=> $email,
		        'phone' 			=> $phone,
		        'job_title' 		=> $jobTitle,
		        'time' 				=> $currentTime,
		        'expiration_date' 	=> $expirationDate,
		        'is_teacher' 		=> ($isTeacherSubForm) ? '1' : '0',
		        'session_token' 	=> ($isTeacherSubForm) ? $_SESSION['session_token'] : ''
		    ),
		    array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')
		);
    }

	// create user account as subscriber if the entry came from teacher subscription
	if ($isTeacherSubForm) { 
		l4k_createTeacherWPAccount($name, $email, $barcode, $school, $country, $state, $phone, $currency); 
	} 

	if ($result) {

	    l4k_send_getting_started_email($email, $name, $barcode, $libraryID, $isTeacherSubForm); 
	    
		// do NOT schedule interim/summary emails for teacher subscriptions
	    if (!$isTeacherSubForm) {
	    	
		    $lib_bcc        		= get_field('lib_bcc_recipients', $libraryID);
		    $lib_login_url  		= get_field('lib_login_url', $libraryID);
		    $lib_mobile_txt 		= get_field('lib_name_for_mobile_text', $libraryID);
		    $lib_from_name  		= get_field('lib_from_email_name', $libraryID);
		    $lib_email_sent_from  	= get_field('lib_email_sent_from', $libraryID);
		    $lib_sig        		= get_field('lib_email_signature', $libraryID);
		    $lib_calendly   		= get_field('lib_calendly_link', $libraryID);
		    $fallback_sig 			= l4k_fallbackEmailSignature(); // fallback ONLY if the ACF field is empty

		    $trial_data = [
		        'first_name'            => $name,
		        'barcode'               => $barcode,
		        'trial_details'         => [
		            'barcode' 		=> $barcode,
		            'website' 		=> $trail_website, 
		            'library' 		=> $library,
		            'library_id' 	=> $libraryID
		        ],
		        'kindRegards'           => !empty($lib_from_name) ? $lib_from_name : 'Sunny',
		        'sent_from_name'        => !empty($lib_from_name) ? $lib_from_name : 'LOTE4Kids',
		        'sent_from'             => !empty($lib_email_sent_from) ? $lib_email_sent_from : 'sales@storytimepods.com',
		        'email_signature'       => !empty($lib_sig) ? $lib_sig : $fallback_sig,
		        'clickHereLink'         => !empty($lib_calendly) ? $lib_calendly : 'https://calendly.com/lote4kids/demo-meeting',
		        'trialExtensionLink'    => $_SESSION['trialurl'] ?? '',
		        'login_url'             => !empty($lib_login_url) ? $lib_login_url : $trail_website,
		        'mobile_text'           => !empty($lib_mobile_txt) ? $lib_mobile_txt : 'Demo Library',
		        'bcc_recipients'        => !empty($lib_bcc) ? $lib_bcc : 'pete@storytimepods.com.au, sunny@storytimepods.com.au, storytimepods@pipedrivemail.com',
		        'second_email_schedule' => 14
		    ];

	 		schedule_trial_emails($email, $trial_data); 

 		}

	} else {

		error_log('Insert failed: ' . $wpdb->last_error);

	}

}

add_action('wpforms_process_complete', 'l4k_saveFormEntriesToDB', 10, 4);

/*
add_action('wpforms_process_complete', function($fields, $entry, $form_data, $entry_id) {
    $form_id = $form_data['id'];
    l4k_push_mapped_form_data($form_id, $fields);
}, 10, 4);
*/

/**
 * ----------------------------------------------------------------
 * Increment feather count (video_claims table)
 * Feather count + 1 if visited a book
 * Feather count + 1 if clicked an activity in the sidebar
 * Feather count + 1 if visited /activities page
 * ----------------------------------------------------------------
 */

function l4k_addFeatherCount($ownerID, $url) {

    global $wpdb;
    $video_claims_table = $wpdb->prefix . 'video_claims';
    $characters_table = $wpdb->prefix . 'characters';

    // attempt to insert into video_claims first
    $result = $wpdb->insert(
        $video_claims_table,
        array(
            'ownerId'   => $ownerID,
            'videoUrl'  => $url,
            'claimedAt' => current_time('mysql')
        ),
        array('%s', '%s', '%s')
    );

    // if insert fails, stop immediately - log it
    // if ($result === false) { error_log('Video claim insert failed: ' . $wpdb->last_error); return false; }

    // only increment characters points if insert succeeded
	$updated = $wpdb->query(
	    $wpdb->prepare(
	        "UPDATE $characters_table
	         SET points = LAST_INSERT_ID(points + 1)
	         WHERE ownerId = %s",
	        $ownerID
	    )
	);

	$newPointsCount = $wpdb->get_var("SELECT LAST_INSERT_ID()");

    // insert succeeded but points update failed — log it
    if ($updated === false) { error_log('Points update failed for ' . $ownerID . ': ' . $wpdb->last_error); return false; }

    return $newPointsCount;

}

/**
 * ----------------------------------------------------------------
 * Insert or Update POST comments record
 * ----------------------------------------------------------------
 */

function l4k_updatePostComment_legacy(array $data) {

 if (!is_array($data)) {
        return new WP_Error(
            'invalid_body',
            'Invalid request body',
            ['status' => 400]
        );
    }
    $required = ['post_id', 'author_name', 'comment_author_email', 'content'];

    foreach ($required as $field) {
        if (!isset($data[$field]) || trim((string)$data[$field]) === '') {
            return new WP_Error(
                'missing_param',
                "Missing required parameter: {$field}",
                ['status' => 400]
            );
        }
    }

    if (!is_email($data['comment_author_email'])) {
        return new WP_Error(
            'invalid_email',
            'Invalid email address',
            ['status' => 400]
        );
    }

    if (!get_post((int)$data['post_id'])) {
        return new WP_Error(
            'invalid_post',
            'Post does not exist',
            ['status' => 404]
        );
    }

    $comment_data = [
        'comment_post_ID'      => (int)$data['post_id'],
        'comment_author'       => sanitize_text_field($data['author_name']),
        'comment_author_email' => sanitize_email($data['comment_author_email']),
        'comment_content'      => sanitize_textarea_field($data['content']),
        'comment_approved'     => 0,
        'comment_type'         => 'comment',
        'comment_parent'       => 0,
        'user_id'              => 0,
    ];

    $comment_data = apply_filters('preprocess_comment', $comment_data);

    $comment_id = wp_new_comment($comment_data);

    if (is_wp_error($comment_id)) {
        return new WP_Error(
            'comment_failed',
            'Failed to insert comment',
            ['status' => 500]
        );
    }

    if (isset($data['library']) && $data['library'] !== '') {
        $library_value = sanitize_text_field($data['library']);

        // ACF / existing system
        add_comment_meta(
            $comment_id,
            'custom_field_6939180959ceb',
            $library_value,
            true
        );

        // New standardized key (for mobile / API)
        add_comment_meta(
            $comment_id,
            'comment_library',
            $library_value,
            true
        );
    }

    return [
        'success'    => true,
        'comment_id' => $comment_id,
        'status'     => 'pending',
    ];

}

/**
 * ----------------------------------------------------------------
 * Mobile App Submit Form
 * ----------------------------------------------------------------
 */

function l4k_submitForm_legacy(array $data) {

    $required = ['name', 'email', 'message', 'library_name'];

    foreach ($required as $field) {
        if (empty($data[$field])) {
            return new WP_Error(
                'missing_param',
                "Missing required field: {$field}",
                ['status' => 400]
            );
        }
    }

    $user_name    = sanitize_text_field($data['name']);
    $user_email   = sanitize_email($data['email']);
    $library_name = sanitize_text_field($data['library_name']);
    $user_message = sanitize_textarea_field($data['message']);

    if (!is_email($user_email)) {
        return new WP_Error(
            'invalid_email',
            'Invalid email address',
            ['status' => 400]
        );
    }

    $from_name  = 'LOTE4Kids App';
    $from_email = 'admin@storytimepods.com.au';

    $to_emails = [
        'pete@storytimepods.com.au',
    ];

    $subject = 'LOTE4Kids App – Contact Us';

    $body  = "User's Name: {$user_name}\n";
    $body .= "Library Name: {$library_name}\n";
    $body .= "User's Email: {$user_email}\n\n";
    $body .= "User's Message:\n{$user_message}";

    $headers = [
        'Content-Type: text/plain; charset=UTF-8',
        'From: ' . $from_name . ' <' . $from_email . '>',
        'Reply-To: ' . $user_email,
    ];

    $sent = wp_mail($to_emails, $subject, $body, $headers);

    if (!$sent) {
        return new WP_Error(
            'mail_failed',
            'Failed to send email',
            ['status' => 500]
        );
    }

    return [
        'success' => true,
        'message' => 'Form submitted successfully'
    ];
}

/**
 * ----------------------------------------------------------------
 * Mobile App User Login Logs
 * ----------------------------------------------------------------
 */

function l4k_setUserLoginLogs_legacy(array $data) {
    global $wpdb;

    $required = [
        'alert_code',
        'barcode',
        'library_group',
        'region',
        'language',
        'os_type',
        'device_type',
        'status',
    ];

    foreach ($required as $field) {
        if (!isset($data[$field]) || $data[$field] === '') {
            return new WP_Error(
                'missing_param',
                "Missing required field: {$field}",
                ['status' => 400]
            );
        }
    }

    $insert_data = [
        'alert_code'    => sanitize_text_field($data['alert_code']),
        'barcode'       => sanitize_text_field(strtoupper($data['barcode'])),
        'library_group' => sanitize_text_field($data['library_group']),
        'region'        => sanitize_text_field($data['region']),
        'language'      => sanitize_text_field($data['language']),
        'os_type'       => sanitize_text_field($data['os_type']),
        'device_type'   => sanitize_text_field($data['device_type']),
        'status'        => sanitize_text_field($data['status']),
        'time'        	=> current_time('mysql')
    ];

    $result = $wpdb->insert(
        $wpdb->prefix . 'user_login_logs',
        $insert_data,
        [
            '%s','%s','%s','%s','%s','%s','%s','%s','%s'
        ]
    );

    if ($result === false) {
        return new WP_Error(
            'db_insert_failed',
            'Failed to insert user login log',
            ['status' => 500]
        );
    }

    return [
        'success' => true,
        'data'    => $insert_data,
        'log_id'  => $wpdb->insert_id,
    ];
}


/**
 * ----------------------------------------------------------------
 * Mobile App Activity Logs
 * ----------------------------------------------------------------
 */

function l4k_setMobileActivityLogs_legacy(array $data) {
    global $wpdb;

    // Safe defaults (same behavior as your REST version)
    $insert_data = [
        'alert_code'  => isset($data['alert_code']) ? sanitize_text_field($data['alert_code']) : '',
        'barcode'     => isset($data['barcode']) ? sanitize_text_field(strtoupper($data['barcode'])) : '',
        'language'    => isset($data['language']) ? sanitize_text_field($data['language']) : '',
        'library_id'  => isset($data['library_id']) ? sanitize_text_field($data['library_id']) : '',
        'post_id'     => isset($data['story_id']) ? sanitize_text_field($data['story_id']) : '',
        'os_type'     => isset($data['os_type']) ? sanitize_text_field($data['os_type']) : '',
        'device_type' => isset($data['device_type']) ? sanitize_text_field($data['device_type']) : '',
        'status'      => isset($data['status']) ? sanitize_text_field($data['status']) : '',
    ];

    $result = $wpdb->insert(
        $wpdb->prefix . 'mobile_activity_logs',
        $insert_data,
        [
            '%s','%s','%s','%s','%s','%s','%s','%s'
        ]
    );

    if ($result === false) {
        return new WP_Error(
            'db_insert_failed',
            'Failed to insert mobile activity log',
            ['status' => 500]
        );
    }

    return [
        'success' => true,
        'data'    => $insert_data,
        'log_id'  => $wpdb->insert_id,
    ];
}

/**
 * ----------------------------------------------------------------
 * Update alternate_barcode w/ the barcode's current trial activity
 * ----------------------------------------------------------------
 */

function l4k_updateTrialActivityStatus() {

    global $wpdb;
    $table = $wpdb->prefix . 'alternate_barcode';

    $activities = [
        'flipbook'       	=> isset($_POST['flipbook'])       	? $_POST['flipbook']       	: '0',
		'quiz'           	=> isset($_POST['quiz'])           	? $_POST['quiz']           	: '0',
        'video'          	=> isset($_POST['video'])          	? $_POST['video']          	: '0',
        'picture_card' 		=> isset($_POST['picture_card'])   	? $_POST['picture_card']  	: '0',
        'non_fiction' 		=> isset($_POST['non_fiction'])    	? $_POST['non_fiction']     : '0',
        'sign_language' 	=> isset($_POST['sign_language']) 	? $_POST['sign_language']	: '0',
        'fun_facts' 		=> isset($_POST['fun_facts'])		? $_POST['fun_facts']		: '0',
        'lekti'          	=> isset($_POST['lekti'])          	? $_POST['lekti']          	: '0',
        'activities'     	=> isset($_POST['activities'])     	? $_POST['activities']     	: '0',
        'mobile'         	=> isset($_POST['mobile'])         	? $_POST['mobile']         	: '0',
        'overview_video' 	=> isset($_POST['overview_video']) 	? $_POST['overview_video'] 	: '0',
        'staff_portal' 		=> isset($_POST['staff_portal']) 	? $_POST['staff_portal'] 	: '0',
    ];

    $updated = $wpdb->update(
        $table,
        ['data' => json_encode($activities)],
        ['barcode' => $_POST['barcode']]
    );

    if ($updated !== false) {
        wp_send_json(['status' => 1, 'message' => 'Trial activity updated successfully.']);
    } else {
        wp_send_json(['status' => 0, 'message' => 'Failed to update trial activity.']);
    }

    wp_die();

}

/**
 * ----------------------------------------------------------------
 * Generate new user - teacher role from the teacher sub form
 * ----------------------------------------------------------------
 */

function l4k_createTeacherWPAccount($name, $email, $barcode, $school='', $country='', $state='', $phone='', $currency='') {

    $user_id = wp_create_user($barcode, wp_generate_password(), $email);
    if (is_wp_error($user_id)) {
        return $user_id;
    }

    $parts      = explode(' ', trim($name));
    $first_name = $parts[0];
    $last_name  = count($parts) > 1 ? implode(' ', array_slice($parts, 1)) : '';

    wp_update_user(array(
        'ID'           => $user_id,
        'role'         => 'subscriber',
        'display_name' => $name,
        'first_name'   => $first_name,
        'last_name'    => $last_name,
    ));

    // save custom fields
    update_user_meta($user_id, 'library', get_field('teacher_default_library', 'option'));
    update_user_meta($user_id, 'barcode', $barcode);
	update_user_meta($user_id, 'school', $school);
	update_user_meta($user_id, 'country', $country);
	update_user_meta($user_id, 'state', $state);
	update_user_meta($user_id, 'phone', $phone);
	update_user_meta($user_id, 'currency', $currency);

    wp_set_current_user($user_id);
    wp_set_auth_cookie($user_id, true);
    return $user_id;

}
?>