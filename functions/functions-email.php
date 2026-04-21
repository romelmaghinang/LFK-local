<?php 
/**
 * Send Getting Started Email
 */

function l4k_send_getting_started_email($email, $name, $barcode, $libraryID, $isTeacher=false) {

    $first_name = explode(' ', trim($name))[0];

    if (empty($email) || empty($barcode)) {
        error_log('Getting Started email skipped: missing email or barcode');
        return;
    }
  
    $lib_login_url  		= get_field('lib_login_url', $libraryID);
    $lib_bcc        		= get_field('lib_bcc_recipients', $libraryID);
    $lib_from_name  		= get_field('lib_from_email_name', $libraryID);
    $lib_email_sent_from  	= get_field('lib_email_sent_from', $libraryID);
    $library_name  			= get_field('lib_name_for_mobile_text', $libraryID);
    $email_signature 		= get_field('lib_email_signature', $libraryID);
    $apple_link  			= get_field('footer_mobile_app_links_apple_download_link', 'option');
    $google_link 			= get_field('footer_mobile_app_links_google_download_link', 'option');
    $auth_token				= get_field('auto_login_auth_token', $libraryID);
    $auth_token_append		= '?auth='.$auth_token.'&barcode='.$barcode;
    
    $checklist_flipbook__text		= get_field('onboarding_flipbook_text', $libraryID);
    $checklist_quiz__text			= get_field('onboarding_quiz_text', $libraryID);
    $checklist_video__text			= get_field('onboarding_video_text', $libraryID);
    $checklist_picture_card__text	= get_field('onboarding_picture_card_text', $libraryID);
    $checklist_non_fiction__text	= get_field('onboarding_non_fiction_text', $libraryID);
    $checklist_sign_language__text	= get_field('onboarding_sign_language_text', $libraryID);
    $checklist_fun_facts__text		= get_field('onboarding_language_fun_facts_text', $libraryID);
    $checklist_lekti__text			= get_field('onboarding_lekti_text', $libraryID);
    $checklist_activities__text		= get_field('onboarding_activities_text', $libraryID);
    $checklist_mobile__text 		= get_field('onboarding_mobile_text', $libraryID);
    $checklist_overview_video__text	= get_field('onboarding_overview_video_text', $libraryID);
    $checklist_staff_portal__text	= get_field('onboarding_staff_portal_guide_text', $libraryID);

    $checklist_flipbook__link		= l4k_normalizeChecklistLink(get_field('onboarding_flipbook_link', $libraryID).$auth_token_append);
    $checklist_quiz__link			= l4k_normalizeChecklistLink(get_field('onboarding_quiz_link', $libraryID).$auth_token_append);
    $checklist_video__link			= l4k_normalizeChecklistLink(get_field('onboarding_video_link', $libraryID).$auth_token_append);
    $checklist_picture_card__link	= l4k_normalizeChecklistLink(get_field('onboarding_picture_card_link', $libraryID).$auth_token_append);
    $checklist_non_fiction__link	= l4k_normalizeChecklistLink(get_field('onboarding_non_fiction_link', $libraryID).$auth_token_append);
    $checklist_sign_language__link	= l4k_normalizeChecklistLink(get_field('onboarding_sign_language_link', $libraryID).$auth_token_append);
    $checklist_fun_facts__link		= l4k_normalizeChecklistLink(get_field('onboarding_language_fun_facts_link', $libraryID).$auth_token_append);
    $checklist_lekti__link			= l4k_normalizeChecklistLink(get_field('onboarding_lekti_link', $libraryID).$auth_token_append);
    $checklist_activities__link		= l4k_normalizeChecklistLink(get_field('onboarding_activities_link', $libraryID).$auth_token_append);
    $checklist_mobile__link 		= l4k_normalizeChecklistLink(get_field('onboarding_mobile_link', $libraryID).$auth_token_append);
    $checklist_overview_video__link	= l4k_normalizeChecklistLink(get_field('onboarding_overview_video_link', $libraryID).$auth_token_append);
    $checklist_staff_portal__link	= l4k_normalizeChecklistLink(get_field('onboarding_staff_portal_guide_link', $libraryID).$auth_token_append);

    $subject = 'Your LOTE4Kids Trial - Getting Started';

    ob_start();
    $template_path = ($isTeacher) ? get_stylesheet_directory() . '/email-templates/getting-started-teacher.php' : get_stylesheet_directory() . '/email-templates/getting-started.php';
    if(file_exists($template_path)) { include($template_path); }
    $message = ob_get_clean();

    $headers = [
        "From: {$lib_from_name} <{$lib_email_sent_from}>",
        "Content-Type: text/html; charset=UTF-8",
        'Bcc: ' . $lib_bcc, 
    ];

    add_filter('wp_mail_from', function() use ($lib_email_sent_from) { return $lib_email_sent_from; });

    if (wp_mail($email, $subject, $message, $headers)) {
        l4k_mark_getting_started_sent($barcode);
    } else {
        error_log('Getting Started email failed for barcode: ' . $barcode);
    }

}

/**
 * Mark email as sent (optional safety flag)
 */
function l4k_mark_getting_started_sent($barcode) {
    global $wpdb;

    $wpdb->update(
        $wpdb->prefix . 'alternate_barcode',
        ['is_getting_started' => 1],
        ['barcode' => $barcode],
        ['%d'],
        ['%s']
    );
}

//////////////////////////USER ENGAGEMENT FUNCTIONS///////////////////////
function get_activities_user_engagement($barcode, $time)
{
    global $wpdb;
    $table_web_activity = $wpdb->prefix . 'web_activity';
    
  
    $barcode_param = '%' . $barcode . '%';
    $today = date('Y-m-d');

   
    $query = $wpdb->prepare(
        "SELECT alert_code, data 
         FROM $table_web_activity 
         WHERE barcode LIKE %s 
         AND DATE(time) BETWEEN %s AND %s",
        $barcode_param,
        $time,
        $today
    );
    
    $web_activities = $wpdb->get_results($query);

    $language = [];
    $quizzes = 0;
    $activities = 0;

    foreach ($web_activities as $row) {
       
        $meta = json_decode($row->data, true);
        if (empty($meta)) continue;

    switch ($row->alert_code) {
    case 1062:
    if (!empty($meta['Language'])) {
        $lang_id = $meta['Language'];

        
        if (is_numeric($lang_id)) {
            $lang_name = get_the_title($lang_id);
        } else {
            $lang_name = $lang_id; 
        }

        if ($lang_name) {
            $lang_name = str_replace("-", " ", $lang_name);

            $sign_languages = [
                'American Sign Language'   => ' (ASL)',
                'British Sign Language'    => ' (BSL)',
                'Australian Sign Language' => ' (Auslan)',
                'New Zealand Sign Language'=> ' (NZSL)'
            ];

            if (isset($sign_languages[$lang_name])) {
                $language[] = $lang_name . $sign_languages[$lang_name];
            } else {
                $language[] = $lang_name;
            }
        }
    }
    break;

    case 1060: 
        if (!empty($meta['Activity Name'])) {
            $activity_name = strtolower($meta['Activity Name']);
            
      
            if (strpos($activity_name, 'quiz') !== false) {
                $quizzes += 1;
            } else {
              
                $activities += 1;
            }
        }
        break;
}
    }
    
    $views_count = count($language);
    $unique_languages = array_unique($language);
    

    return array(
        'langauage_viewed'          => !empty($unique_languages) ? implode(", ", $unique_languages) : "",
        'number_of_views_read'      => $views_count,
        'number_of_quizzes_started' => $quizzes,
        'number_of_activities'      => $activities,
        'total_engagement'          => $quizzes + $activities + $views_count
    );
}

//////////////////////////USER ENGAGEMENT FUNCTIONS///////////////////////


/////////////////INSERT TO CRON PART///////////////////////////////


function schedule_trial_emails($email, $trial) {

	global $wpdb;
    $table_name = $wpdb->prefix . 'alternate_barcode';

	$db_entry = $wpdb->get_row($wpdb->prepare(
        "SELECT barcode, name, library FROM {$table_name} WHERE email = %s ORDER BY time DESC LIMIT 1",
        $email
    ), ARRAY_A);

    $fallback_barcode = 'TRIAL' . time(); 
    $fallback_name    = 'Trial User';
    $fallback_library = 'LOTE4Kids Library';

    $barcode    = !empty($db_entry['barcode']) ? $db_entry['barcode'] : ($trial['trial_details']['barcode'] ?? $fallback_barcode);
    $first_name = !empty($db_entry['name'])    ? $db_entry['name']    : ($trial['first_name'] ?? $fallback_name);
    $library    = !empty($db_entry['library']) ? $db_entry['library'] : ($trial['trial_details']['website'] ?? $fallback_library);

	$trial['first_name'] = $first_name;
    $trial['barcode']    = $barcode;

    if(!isset($trial['trial_details'])) $trial['trial_details'] = [];
    $trial['trial_details']['barcode'] = $barcode;
    //$trial['trial_details']['website'] = $_SESSION['website'];

	// ---- CRON HANDLING ----
	$test_mode = defined('LOTE4KIDS_TEST_CRON') && LOTE4KIDS_TEST_CRON;
	//$interim_delay = 1 * HOUR_IN_SECONDS;  // 24 hour
	//$summary_delay = 1 * HOUR_IN_SECONDS;  // 48 hours
	$interim_delay = 7 * DAY_IN_SECONDS;  // Exactly 7 days
	$summary_delay = 14 * DAY_IN_SECONDS; // Exactly 14 days

	// Remove any previously scheduled events for this email (ignore old $trial data)
	foreach (['interim_report_event', 'summary_report_event'] as $hook) {
	    
	    $crons = _get_cron_array();
	    if (is_array($crons)) {
	        foreach ($crons as $timestamp => $cronhooks) {
	            if (isset($cronhooks[$hook])) {
	                foreach ($cronhooks[$hook] as $key => $args) {
	                    if (!empty($args['args'][0]) && $args['args'][0] === $email) {
	                        wp_unschedule_event($timestamp, $hook, $args['args']);
	                    }
	                }
	            }
	        }
	    }
	}

	wp_schedule_single_event(time() + $interim_delay, 'interim_report_event', [$email, $trial]);
	wp_schedule_single_event(time() + $summary_delay, 'summary_report_event', [$email, $trial]);

}

/**
 * Named handler for interim (7-day)
 */
function send_email_after_7_days($email, $trial = []) {

    global $wpdb;
    
    if (is_array($email)) $email = reset($email);
  
    if (empty($trial)) {
        error_log("Cron Error: Trial data missing for interim email to $email");
    }

    $table_name = $wpdb->prefix . 'alternate_barcode';
    $seven_days_ago_timestamp = date('Y-m-d H:i:s', strtotime('-8 days'));

	$sql_query = $wpdb->prepare(
	    "SELECT DISTINCT barcode, name, library, data 
	     FROM {$table_name} 
	     WHERE email = %s AND time >= %s AND is_interim = 0
	     ORDER BY time DESC", 
	    $email,
	    $seven_days_ago_timestamp
	);
    $db_results = $wpdb->get_results($sql_query);

    if (empty($db_results)) return; 

    $barcodes = wp_list_pluck($db_results, 'barcode');
    $barcode = $barcodes[0];
    $raw_name = !empty($db_results[0]->name) ? $db_results[0]->name : 'Trial User';
    $first_name = explode(' ', trim($raw_name))[0];

	// get all rows for this email (no DISTINCT) within the same time window
	// the $checklistArr below should contain the merged data for all barcodes under this email address
	$sql_query_all = $wpdb->prepare(
	    "SELECT barcode, name, library, data 
	     FROM {$table_name} 
	     WHERE email = %s AND time >= %s AND is_interim = 0
	     ORDER BY time ASC",
	    $email,
	    $seven_days_ago_timestamp
	);
	$db_results_all = $wpdb->get_results($sql_query_all);

	// decode each row's JSON and merge — a key is "1" if any row has it as "1"
	$checklistArr = array();
	foreach ($db_results_all as $row) {
	    if (!empty($row->data)) {
	        $decoded = json_decode($row->data, true);
	        if (is_array($decoded)) {
	            foreach ($decoded as $key => $value) {
	                // keep "1" if already set, otherwise take current row's value
	                if (!isset($checklistArr[$key]) || $checklistArr[$key] !== "1") {
	                    $checklistArr[$key] = $value;
	                }
	            }
	        }
	    }
	}

    $website = $trial['login_url'] ?? "https://www.lote4kids.com";

    $seven_days_later = date('Y-m-d', strtotime('-8 days'));
    $seven_days_results = [
        'langauage_viewed' => [],
        'number_of_views_read' => 0,
        'number_of_quizzes_started' => 0,
        'number_of_activities' => 0,
        'total_engagement' => 0,
    ];

    foreach ($barcodes as $bcode) {
        $result = get_activities_user_engagement($bcode, $seven_days_later);
        if ($result && is_array($result)) {
            if (!empty($result['langauage_viewed'])) {
                $langs = array_map('trim', explode(', ', $result['langauage_viewed']));
                $seven_days_results['langauage_viewed'] = array_unique(array_merge($seven_days_results['langauage_viewed'], $langs));
            }
            $seven_days_results['number_of_views_read']      += (int)($result['number_of_views_read'] ?? 0);
            $seven_days_results['number_of_quizzes_started'] += (int)($result['number_of_quizzes_started'] ?? 0);
            $seven_days_results['number_of_activities']      += (int)($result['number_of_activities'] ?? 0);
            $seven_days_results['total_engagement']          += (int)($result['total_engagement'] ?? 0);
        }
    }

    $language_viewed_7days = implode(', ', $seven_days_results['langauage_viewed']);
    $barcodes_str = implode(', ', $barcodes);

    $kindRegards        = $trial['kindRegards'] ?? 'Sunny';
    $from_name          = $trial['sent_from_name'] ?? 'LOTE4Kids';
    $from_email         = $trial['sent_from'] ?? 'sales@storytimepods.com';
    $email_signature    = $trial['email_signature'] ?? 'test signature'; 
    $clickHereLink      = $trial['clickHereLink'] ?? 'https://calendly.com/lote4kids/demo-meeting';
    $websiteLink        = $trial['websiteLink'] ?? 'www.lote4kids.com';
    $mobileApp          = $trial['mobile_text'] ?? 'LOTE4Kids App';
    $bcc_recipients     = $trial['bcc_recipients'] ?? 'pete@storytimepods.com.au, sunny@storytimepods.com.au';
    $libraryID     		= $trial['trial_details']['library_id'];
    $auth_token			= get_field('auto_login_auth_token', $libraryID);
    $auth_token_append	= '?auth='.$auth_token.'&barcode='.$barcode;

    $subject = "Your LOTE4Kids Trial - How's It Going?";

	$checklist_flipbook__text		= get_field('onboarding_flipbook_text', $libraryID);
    $checklist_quiz__text			= get_field('onboarding_quiz_text', $libraryID);
    $checklist_video__text			= get_field('onboarding_video_text', $libraryID);
    $checklist_picture_card__text	= get_field('onboarding_picture_card_text', $libraryID);
    $checklist_non_fiction__text	= get_field('onboarding_non_fiction_text', $libraryID);
    $checklist_sign_language__text	= get_field('onboarding_sign_language_text', $libraryID);
    $checklist_fun_facts__text		= get_field('onboarding_language_fun_facts_text', $libraryID);
    $checklist_lekti__text			= get_field('onboarding_lekti_text', $libraryID);
    $checklist_activities__text		= get_field('onboarding_activities_text', $libraryID);
    $checklist_mobile__text 		= get_field('onboarding_mobile_text', $libraryID);
    $checklist_overview_video__text	= get_field('onboarding_overview_video_text', $libraryID);
    $checklist_staff_portal__text	= get_field('onboarding_staff_portal_guide_text', $libraryID);

    $checklist_flipbook__link		= l4k_normalizeChecklistLink(get_field('onboarding_flipbook_link', $libraryID).$auth_token_append);
    $checklist_quiz__link			= l4k_normalizeChecklistLink(get_field('onboarding_quiz_link', $libraryID).$auth_token_append);
    $checklist_video__link			= l4k_normalizeChecklistLink(get_field('onboarding_video_link', $libraryID).$auth_token_append);
    $checklist_picture_card__link	= l4k_normalizeChecklistLink(get_field('onboarding_picture_card_link', $libraryID).$auth_token_append);
    $checklist_non_fiction__link	= l4k_normalizeChecklistLink(get_field('onboarding_non_fiction_link', $libraryID).$auth_token_append);
    $checklist_sign_language__link	= l4k_normalizeChecklistLink(get_field('onboarding_sign_language_link', $libraryID).$auth_token_append);
    $checklist_fun_facts__link		= l4k_normalizeChecklistLink(get_field('onboarding_language_fun_facts_link', $libraryID).$auth_token_append);
    $checklist_lekti__link			= l4k_normalizeChecklistLink(get_field('onboarding_lekti_link', $libraryID).$auth_token_append);
    $checklist_activities__link		= l4k_normalizeChecklistLink(get_field('onboarding_activities_link', $libraryID).$auth_token_append);
    $checklist_mobile__link 		= l4k_normalizeChecklistLink(get_field('onboarding_mobile_link', $libraryID).$auth_token_append);
    $checklist_overview_video__link	= l4k_normalizeChecklistLink(get_field('onboarding_overview_video_link', $libraryID).$auth_token_append);
    $checklist_staff_portal__link	= l4k_normalizeChecklistLink(get_field('onboarding_staff_portal_guide_link', $libraryID).$auth_token_append);
   
    ob_start();
    $template_path = get_stylesheet_directory() . '/email-templates/interim-email-7days.php';
    if(file_exists($template_path)) { include($template_path); }
    $message = ob_get_clean();
   
    $headers = [
        "From: {$from_name} <{$from_email}>",
        "Content-Type: text/html; charset=UTF-8",
        'Bcc: ' . $bcc_recipients, 
    ];

   	add_filter('wp_mail_from', function() use ($from_email) { return $from_email; });
    if (wp_mail($email, $subject, $message, $headers)) {
        foreach ($barcodes as $bcode) {
            $wpdb->update($table_name, ['is_interim' => 1], ['barcode' => $bcode], ['%d'], ['%s']);
        }
    }

}

add_action('interim_report_event', 'send_email_after_7_days', 10, 2);


/**
 * Named handler for final (15-day)
 */
function send_email_after_15_days($email, $trial = []) {

    global $wpdb;
    if (is_array($email)) $email = reset($email);

    $table_name = $wpdb->prefix . 'alternate_barcode';
    $fifteen_days_ago_timestamp = date('Y-m-d H:i:s', strtotime('-15 days'));

    $sql_query = $wpdb->prepare(
        "SELECT DISTINCT barcode, name, library, data
         FROM {$table_name} 
         WHERE email = %s AND time >= %s AND is_summary = 0 
         ORDER BY time DESC", 
        $email,
        $fifteen_days_ago_timestamp
    );
    $db_results = $wpdb->get_results($sql_query);

    if (empty($db_results)) return; 

    $barcodes = wp_list_pluck($db_results, 'barcode');
    $barcode = $barcodes[0];
    $raw_name = !empty($db_results[0]->name) ? $db_results[0]->name : 'Trial User';
    $first_name = explode(' ', trim($raw_name))[0];

	// get all rows for this email (no DISTINCT) within the same time window
	// the $checklistArr below should contain the merged data for all barcodes under this email address
	$sql_query_all = $wpdb->prepare(
	    "SELECT barcode, name, library, data 
	     FROM {$table_name} 
	     WHERE email = %s AND time >= %s AND is_summary = 0
	     ORDER BY time ASC",
	    $email,
	    $fifteen_days_ago_timestamp
	);
	$db_results_all = $wpdb->get_results($sql_query_all);

	// decode each row's JSON and merge — a key is "1" if any row has it as "1"
	$checklistArr = array();
	foreach ($db_results_all as $row) {
	    if (!empty($row->data)) {
	        $decoded = json_decode($row->data, true);
	        if (is_array($decoded)) {
	            foreach ($decoded as $key => $value) {
	                // keep "1" if already set, otherwise take current row's value
	                if (!isset($checklistArr[$key]) || $checklistArr[$key] !== "1") {
	                    $checklistArr[$key] = $value;
	                }
	            }
	        }
	    }
	}

    $fifteen_days_later = date('Y-m-d', strtotime('-15 days'));
    $fifteen_days_results = [
        'langauage_viewed' => [],
        'number_of_views_read' => 0,
        'number_of_quizzes_started' => 0,
        'number_of_activities' => 0,
        'total_engagement' => 0,
    ];

    foreach ($barcodes as $bcode) {
        $result = get_activities_user_engagement($bcode, $fifteen_days_later);
        if ($result && is_array($result)) {
            if (!empty($result['langauage_viewed'])) {
                $langs = array_map('trim', explode(', ', $result['langauage_viewed']));
                $fifteen_days_results['langauage_viewed'] = array_unique(array_merge($fifteen_days_results['langauage_viewed'], $langs));
            }
            $fifteen_days_results['number_of_views_read']      += (int)($result['number_of_views_read'] ?? 0);
            $fifteen_days_results['number_of_quizzes_started'] += (int)($result['number_of_quizzes_started'] ?? 0);
            $fifteen_days_results['number_of_activities']      += (int)($result['number_of_activities'] ?? 0);
            $fifteen_days_results['total_engagement']          += (int)($result['total_engagement'] ?? 0);
        }
    }

    $language_viewed_15days = implode(', ', $fifteen_days_results['langauage_viewed']);
    $barcodes_str           = implode(', ', $barcodes); 
    $total_engagement       = (int)($fifteen_days_results['total_engagement'] ?? 0);
    $from_name          = $trial['sent_from_name'] ?? 'LOTE4Kids';
    $from_email = $trial['sent_from'] ?? 'sales@storytimepods.com';
    $email_signature    = $trial['email_signature'] ?? 'test signature'; 
    $trialExtensionLink = !empty($trial['trialExtensionLink']) ? $trial['trialExtensionLink'] : 'https://lote4kids.com/trial-extension/';
    $bcc_recipients     = $trial['bcc_recipients'] ?? 'pete@storytimepods.com.au, sunny@storytimepods.com.au';
    $mobileApp          = $trial['mobile_text'] ?? 'LOTE4Kids App';
    $clickHereLink      = $trial['clickHereLink'] ?? 'https://calendly.com/lote4kids/demo-meeting';
    $libraryID     		= $trial['trial_details']['library_id'];
    $auth_token			= get_field('auto_login_auth_token', $libraryID);
    $auth_token_append	= '?auth='.$auth_token.'&barcode='.$barcode;

    $subject = ($total_engagement < 5) ? "Your LOTE4Kids Trial - Summary" : "Your LOTE4Kids Trial - Summary and Next Steps";

	$checklist_flipbook__text		= get_field('onboarding_flipbook_text', $libraryID);
    $checklist_quiz__text			= get_field('onboarding_quiz_text', $libraryID);
    $checklist_video__text			= get_field('onboarding_video_text', $libraryID);
    $checklist_picture_card__text	= get_field('onboarding_picture_card_text', $libraryID);
    $checklist_non_fiction__text	= get_field('onboarding_non_fiction_text', $libraryID);
    $checklist_sign_language__text	= get_field('onboarding_sign_language_text', $libraryID);
    $checklist_fun_facts__text		= get_field('onboarding_language_fun_facts_text', $libraryID);
    $checklist_lekti__text			= get_field('onboarding_lekti_text', $libraryID);
    $checklist_activities__text		= get_field('onboarding_activities_text', $libraryID);
    $checklist_mobile__text 		= get_field('onboarding_mobile_text', $libraryID);
    $checklist_overview_video__text	= get_field('onboarding_overview_video_text', $libraryID);
    $checklist_staff_portal__text	= get_field('onboarding_staff_portal_guide_text', $libraryID);

    $checklist_flipbook__link		= l4k_normalizeChecklistLink(get_field('onboarding_flipbook_link', $libraryID).$auth_token_append);
    $checklist_quiz__link			= l4k_normalizeChecklistLink(get_field('onboarding_quiz_link', $libraryID).$auth_token_append);
    $checklist_video__link			= l4k_normalizeChecklistLink(get_field('onboarding_video_link', $libraryID).$auth_token_append);
    $checklist_picture_card__link	= l4k_normalizeChecklistLink(get_field('onboarding_picture_card_link', $libraryID).$auth_token_append);
    $checklist_non_fiction__link	= l4k_normalizeChecklistLink(get_field('onboarding_non_fiction_link', $libraryID).$auth_token_append);
    $checklist_sign_language__link	= l4k_normalizeChecklistLink(get_field('onboarding_sign_language_link', $libraryID).$auth_token_append);
    $checklist_fun_facts__link		= l4k_normalizeChecklistLink(get_field('onboarding_language_fun_facts_link', $libraryID).$auth_token_append);
    $checklist_lekti__link			= l4k_normalizeChecklistLink(get_field('onboarding_lekti_link', $libraryID).$auth_token_append);
    $checklist_activities__link		= l4k_normalizeChecklistLink(get_field('onboarding_activities_link', $libraryID).$auth_token_append);
    $checklist_mobile__link 		= l4k_normalizeChecklistLink(get_field('onboarding_mobile_link', $libraryID).$auth_token_append);
    $checklist_overview_video__link	= l4k_normalizeChecklistLink(get_field('onboarding_overview_video_link', $libraryID).$auth_token_append);
    $checklist_staff_portal__link	= l4k_normalizeChecklistLink(get_field('onboarding_staff_portal_guide_link', $libraryID).$auth_token_append);

    ob_start();
    $template_dir = get_stylesheet_directory() . '/email-templates/';
    if ($total_engagement < 5) {
        include($template_dir . 'summary-email-15days.php');
    } else {
        include($template_dir . 'summary-next-step-15days.php'); 
    }
    $message = ob_get_clean();

    $headers = [
        "From: {$from_name} <{$from_email}>",
        "Content-Type: text/html; charset=UTF-8",
        'Bcc: ' . $bcc_recipients, 
    ];
    add_filter('wp_mail_from', function() use ($from_email) { return $from_email; });
    if (wp_mail($email, $subject, $message, $headers)) {
        foreach ($barcodes as $bcode) {
            $wpdb->update($table_name, ['is_summary' => 1], ['barcode' => $bcode], ['%d'], ['%s']);

            // 1. Get the current expiration date for this specific barcode
        $current_expiry = $wpdb->get_var($wpdb->prepare(
            "SELECT expiration_date FROM {$table_name} WHERE barcode = %s",
            $bcode
        ));

        // 2. Calculate the new date (Current Expiry + 7 Days)
        $new_expiry = date('Y-m-d H:i:s', strtotime($current_expiry . ' +7 days'));

        // 3. Update both the flag and the new date
        $wpdb->update(
            $table_name, 
            array(
                'is_summary'      => 1,
                'expiration_date' => $new_expiry
            ), 
            array('barcode' => $bcode), 
            array('%d', '%s'), 
            array('%s')       
        );
			
        }
    }
}

add_action('summary_report_event', 'send_email_after_15_days', 10, 2);
/////////////////INSERT TO CRON PART///////////////////////////////



add_action( 'wp_mail_failed', function( $error ) {
    error_log( 'Newsletter Email Failed: ' . print_r( $error, true ) );
});

function send_custom_newsletter_email( $fields, $entry, $form_data, $entry_id ) {
    $newsletter_form_id = 164240;
    if ( (int) $form_data['id'] !== $newsletter_form_id ) return;

    $user_email = '';
    foreach ( $fields as $field ) {
        if ( strpos( strtolower($field['type']), 'email' ) !== false ) {
            $user_email = $field['value'];
            break;
        }
    }

    if ( empty( $user_email ) ) return;

    $acf_group       = get_field( 'footer_contact_custom_email', 'option' );
    $from_email      = $acf_group['from_email']      ?? 'info@storytimepods.com.au';
    $bcc_email       = $acf_group['bcc_email']       ?? '';
    $email_content   = $acf_group['email_content']   ?? '';
    $header_logo     = $acf_group['header_logo']     ?? '';  // Image field (URL)
    $header_bg_color = $acf_group['header_bg_color'] ?? '#ff9900'; // Color picker
    $wrapper_bg_color = $acf_group['wrapper_bg_color'] ?? '#eeeeee'; // Color picker

    // Logo HTML — fallback to text if no image set
    $logo_html = ! empty( $header_logo )
        ? '<img src="' . esc_url( $header_logo ) . '" alt="LOTE4Kids Logo" style="max-height:60px;">'
        : '<span style="color:#fff;font-size:26px;">LOTE4Kids</span>';

    if ( empty( $email_content ) ) {
        $email_content = '<p>Please confirm your subscription by <a href="https://lote4kids.com/wp-admin/admin-ajax.php?action=tnp&na=c&nk=11-09388aa4fa">clicking here</a>.</p>';
    }

    $headers = array(
        'Content-Type: text/html; charset=UTF-8',
        'From: LOTE4Kids <' . sanitize_email( $from_email ) . '>',
    );

    if ( ! empty( $bcc_email ) ) {
        $headers[] = 'Bcc: ' . sanitize_email( $bcc_email );
    }

    $subject = "Welcome to our Newsletter!";
    $logoheader = get_stylesheet_directory_uri() . '/assets/img/logo-main.svg';

    $message = '
    <div style="background-color:' . esc_attr( $wrapper_bg_color ) . ';padding:20px;font-family:sans-serif;">
        <table style="background-color:' . esc_attr( $header_bg_color ) . ';width:100%;margin-bottom:20px;">
            <tr><td style="padding:20px;text-align:center;">' . $logo_html . '</td></tr>
        </table>
        <div style="background-color:#fff;padding:30px;max-width:600px;margin:0 auto;border:1px solid #ddd;">
            ' . $email_content . '
            <hr style="border:none;border-top:1px solid #eee;margin:20px 0;">
            <p style="font-size:13px;color:#777;">LOTE4Kids - https://lote4kids.com/</p>
        </div>
    </div>';

    wp_mail( $user_email, $subject, $message, $headers );
}

// add_action( 'wpforms_process_complete', 'send_custom_newsletter_email', 10, 4 );

/**
 * ----------------------------------------------------------------
 * Provide a fallback signature for emails
 * ----------------------------------------------------------------
 */

function l4k_fallbackEmailSignature() {

	return "<p>Kind regards,<br>Sunny<br><br>--<br>Director of Sales</p>
       		<img style='width:450px' src='https://lote4kids.com/wp-content/uploads/stp-l4k-ebo-1.png' alt='Logo'><br>
	        P: <a href='tel:1-800-974-8917'>1-800-974-8917</a><br>
	        E: <a href='mailto:sales@storytimepods.com'>sales@storytimepods.com</a><br>
	        W: <a href='https://lote4kids.com'>www.lote4kids.com</a><br>
	        W: <a href='https://erabooksonline.com'>www.erabooksonline.com</a>";

}