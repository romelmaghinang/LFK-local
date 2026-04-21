<?php 
/**
 * ----------------------------------------------------------------
 * Check if member is logged in
 * Otherwise, redirect with message
 * ----------------------------------------------------------------
 */

function l4k_checkMemberLoggedIn() {

	$uri = $_SERVER['REQUEST_URI'];
	$isDashboard = (strpos($uri, '/dashboard') !== false);

	// start session if not already started
	if (session_status() === PHP_SESSION_NONE) { 
		session_start(); 
	}

	// check if teacher type is logged in, if yes compare session tokens first in alternate_barcode table
	// if session_token do not match, logout from this session. this ensures they are only logged in to one device
	if (isset($_SESSION['is_teacher']) && ($_SESSION['is_teacher'] == 1)) {
	    global $wpdb;
	    $stored_token = $wpdb->get_var($wpdb->prepare(
	        "SELECT session_token FROM {$wpdb->prefix}alternate_barcode WHERE barcode = %s",
	        strtoupper($_SESSION['library_barcode'])
	    ));

	    if ($stored_token && $stored_token !== $_SESSION['session_token']) {
    		wp_safe_redirect(home_url('/?member-and-staff-logout&single-device')); // tokens do not match — force logout
    		exit;
	    }
	}

    // if going to /books/aryo-and-the-fairy-filipino/ for example and not logged in
    // if going to /languages/german/ for example and not logged in
    // if going to /playlists/german-playlist-level-1/ for example and not logged in
    // save the url in a session that's being accessed and redirect to it when logged in
    if ((is_singular('book') || is_singular('language') || is_singular('playlist')) && (empty($_SESSION['library_barcode']))) { 
    	$_SESSION['redirect_to'] = $_SERVER['REQUEST_URI'];
    	wp_safe_redirect(home_url()); 
		exit;
    }
    
    // if not logged in, redirect to home page
    // do not do this redirect if on home page
    // do not do this redirect if on library login page
    if (empty($_SESSION['library_barcode']) && !is_front_page() && !is_singular('library') && !is_page_template('page-templates/page-find-library.php')) { 
    	wp_safe_redirect(home_url()); 
    	exit; 
    } 

    // if going back to home page but has a library_barcode session
    // redirect to member-home
    if (!empty($_SESSION['library_barcode']) && is_front_page()) { 
    	wp_safe_redirect(home_url().'/member-home'); 
    	exit; 
    } 

	// if on /libraries/au-demo for example and IS logged in
	// the user should be redirected back to /member-home
	if (is_singular('library') && (!$isDashboard) && (!empty($_SESSION['library_barcode']))) { 
		wp_safe_redirect(home_url().'/member-home'); 
		exit; 
	}

    // if on /libraries/au-demo/dashboard for example and NOT logged in
    // the user should be redirected back to /libraries/au-demo
    if (is_singular('library') && ($isDashboard) && (empty($_SESSION['library_barcode']))) { 
    	wp_safe_redirect(get_permalink(get_the_ID())); 
    	exit; 
    }

	// if on /libraries/au-demo/dashboard for example and IS logged in
    // if there's a WP user logged in, allow this
    // otherwise, redirect to member-home
	if (is_singular('library') && ($isDashboard) && (!is_user_logged_in())) { 
    	wp_safe_redirect(home_url().'/member-home'); 
    	exit; 
    }

    // if going to home page but currently logged in, redirect back to member-home
    if (is_front_page() && (!empty($_SESSION['library_barcode']))) { 
    	wp_safe_redirect(home_url().'/member-home'); 
    	exit; 
    }

}

/**
 * ----------------------------------------------------------------
 * Check if previously logged in
 * ----------------------------------------------------------------
 */

function l4k_checkCookieLoggedIn() {

	if (isset($_COOKIE['remember_member'])) 
    {
        $data = json_decode(base64_decode($_COOKIE['remember_member']), true);
    	$_SESSION['ownerID'] 				= $data['ownerID'];
    	$_SESSION['library_id'] 			= $data['library_id'];
    	$_SESSION['library_welcome_logo'] 	= $data['library_welcome_logo'];
    	$_SESSION['library_permalink'] 		= $data['library_permalink'];
    	$_SESSION['library_barcode'] 		= strtoupper($data['library_barcode']);
    	$_SESSION['library_name'] 			= $data['library_name'];
    	$_SESSION['library_group'] 			= $data['library_group'];
    	$_SESSION['library_region'] 		= $data['library_region'];
    	$_SESSION['library_remember'] 		= $data['library_remember'];
    	$_SESSION['last_viewed_book'] 		= $data['last_viewed_book'];
    	$_SESSION['auth_token'] 			= $data['auth_token'];
    	$_SESSION['auto_login_status'] 		= $data['auto_login_status'];
    	return true;
    }

    return false;

}

/**
 * ----------------------------------------------------------------
 * Check if auto logged in via whitelisted DOMAIN
 * ----------------------------------------------------------------
 */

function l4k_checkDomainAutoLogin() {

	$referrer = l4k_getClientReferrerDomain(); // var_dump($referrer);

    if (have_rows('domain_whitelist', 'option')) {
        while (have_rows('domain_whitelist', 'option')) {

            the_row();
        	if (get_sub_field('referrer') == $referrer) {

				$libraryID = get_sub_field('library');

				// record event 900 for user login (user_login_logs table) - if row is not existing yet
				$ownerID = l4k_addUserLoginActivity(
					'900', 
					strtoupper(get_the_title($libraryID) . ' Domain Login'),
					get_field('library_group_name', $libraryID), 
					get_field('library_group_region', $libraryID), 
					'english', current_time('mysql'), '', '', 'active');

				// record this event (characters table) - if row is not existing yet
				l4k_addCharacterActivity($ownerID);

				$_SESSION['ownerID'] 				= $ownerID;
				$_SESSION['library_id'] 			= $libraryID;
		    	$_SESSION['library_welcome_logo'] 	= get_field('logo_welcome', $libraryID);
		    	$_SESSION['library_permalink'] 		= get_the_permalink($libraryID);
		    	$_SESSION['library_barcode'] 		= strtoupper(get_the_title($libraryID) . ' Domain Login');
		    	$_SESSION['library_name'] 			= get_the_title($libraryID);
		    	$_SESSION['library_group'] 			= get_field('library_group_name', $libraryID);
		    	$_SESSION['library_region'] 		= get_field('library_group_region', $libraryID);
		    	$_SESSION['library_remember'] 		= 1;
		    	$_SESSION['last_viewed_book'] 		= '';
		    	$_SESSION['auth_token'] 			= get_field('auto_login_auth_token', $libraryID);
		    	$_SESSION['auto_login_status'] 		= get_field('auto_login_status', $libraryID);

		    	l4k_addWebActivity('900'); // record event 900 for member login
        		return true;
        	}


        }
	}

	return false;

}

/**
 * ----------------------------------------------------------------
 * Check if auto logged in via whitelisted IP
 * ----------------------------------------------------------------
 */

/*
function l4k_checkIpAutoLogin() {

	$visitorIP = l4k_getClientIP();
    if (empty($visitorIP)) { return false; } // unable to get the IP of the visitor
    $visitorIPLong = sprintf('%u', ip2long($visitorIP)); // always compare IPs as integers

    // check cache first
    $cache_key = 'l4k_libraries_with_ip';
    $libraries = false;
    
    if (!isset($_GET['purge-cache'])) { $libraries = get_transient($cache_key); }

    // get all libraries with ip ranges ONLY
	if ($libraries === false) {
		$libraries = get_posts([
		    'post_type'      => 'library',
		    'post_status'    => 'publish',
		    'posts_per_page' => -1,
		    'fields'         => 'ids',
			'meta_query' => [
			    [
			        'key'     => 'library_whitelisted_ips_ranges',
			        'value'   => 0,
			        'compare' => '>',
			        'type'    => 'NUMERIC',
			    ],
			],
		]);
		set_transient($cache_key, $libraries, 24 * HOUR_IN_SECONDS); // save to cache for 24 hours
	}

    if (empty($libraries)) { return false; }

    foreach ($libraries as $libraryID) {
        if (have_rows('library_whitelisted_ips_ranges', $libraryID)) {
            while (have_rows('library_whitelisted_ips_ranges', $libraryID)) {
                the_row();

                $ipFrom = get_sub_field('ip_address_from');
                $ipTo   = get_sub_field('ip_address_to');

                if (empty($ipFrom) || empty($ipTo)) { continue; }

                $fromLong = sprintf('%u', ip2long($ipFrom)); // always compare IPs as integers
                $toLong   = sprintf('%u', ip2long($ipTo)); // always compare IPs as integers

                // once found, create the session
                if ($visitorIPLong >= $fromLong && $visitorIPLong <= $toLong) {

					// record event 900 for user login (user_login_logs table) - if row is not existing yet
					$ownerID = l4k_addUserLoginActivity(
						'900', 
						strtoupper(get_the_title($libraryID) . ' IP Login'),
						get_field('library_group_name', $libraryID), 
						get_field('library_group_region', $libraryID), 
						'english', current_time('mysql'), '', '', 'active');

					// record this event (characters table) - if row is not existing yet
					l4k_addCharacterActivity($ownerID);

    				$_SESSION['ownerID'] 				= $ownerID;
					$_SESSION['library_id'] 			= $libraryID;
			    	$_SESSION['library_welcome_logo'] 	= get_field('logo_welcome', $libraryID);
			    	$_SESSION['library_permalink'] 		= get_the_permalink($libraryID);
			    	$_SESSION['library_barcode'] 		= strtoupper(get_the_title($libraryID) . ' IP Login');
			    	$_SESSION['library_name'] 			= get_the_title($libraryID);
			    	$_SESSION['library_group'] 			= get_field('library_group_name', $libraryID);
			    	$_SESSION['library_region'] 		= get_field('library_group_region', $libraryID);
			    	$_SESSION['library_remember'] 		= 1;
			    	$_SESSION['last_viewed_book'] 		= '';
			    	$_SESSION['auth_token'] 			= get_field('auto_login_auth_token', $libraryID);
			    	$_SESSION['auto_login_status'] 		= get_field('auto_login_status', $libraryID);

			    	l4k_addWebActivity('900'); // record event 900 for member login
			    	return $libraryID;

                }
            }
        }
    }

    return false; // no match

}
*/

function l4k_checkIpAutoLogin() {

    $visitorIP = l4k_getClientIP();
    if (empty($visitorIP)) { return false; }
    $visitorIPLong = sprintf('%u', ip2long($visitorIP));
    
    // if we're on a library post, just check that library directly
    if (is_singular('library')) {
        $libraries = [get_queried_object_id()];
    } else {
        // cache library IDs
        $cache_key = 'l4k_libraries_with_ip';
        if (!isset($_GET['purge-cache'])) { $libraries = get_transient($cache_key); }
        else { $libraries = false; }

        if ($libraries === false) {
            $libraries = get_posts([
                'post_type'      => 'library',
                'post_status'    => 'publish',
                'posts_per_page' => -1,
                'fields'         => 'ids',
                'meta_query' => [[
                    'key'     => 'library_whitelisted_ips_ranges',
                    'value'   => 0,
                    'compare' => '>',
                    'type'    => 'NUMERIC',
                ]],
            ]);
            set_transient($cache_key, $libraries, 24 * HOUR_IN_SECONDS);
        }
    }
    
    if (empty($libraries)) { return false; }
    
    foreach ($libraries as $libraryID) {

        // cache IP ranges per library
        $ranges_cache_key = 'l4k_ip_ranges_' . $libraryID;
        $ip_ranges = get_transient($ranges_cache_key);
        
        if ($ip_ranges === false) {
            $ip_ranges = [];
            if (have_rows('library_whitelisted_ips_ranges', $libraryID)) {
                while (have_rows('library_whitelisted_ips_ranges', $libraryID)) {
                    the_row();
                    $ipFrom = get_sub_field('ip_address_from');
                    $ipTo   = get_sub_field('ip_address_to');
                    
                    if (!empty($ipFrom) && !empty($ipTo)) {
                        $ip_ranges[] = [
                            'from' => sprintf('%u', ip2long($ipFrom)),
                            'to'   => sprintf('%u', ip2long($ipTo))
                        ];
                    }
                }
            }
            set_transient($ranges_cache_key, $ip_ranges, 24 * HOUR_IN_SECONDS);
        }
        
        foreach ($ip_ranges as $range) {
            if ($visitorIPLong >= $range['from'] && $visitorIPLong <= $range['to']) {

                // match found - setup session 
				// record event 900 for user login (user_login_logs table) - if row is not existing yet
				$ownerID = l4k_addUserLoginActivity(
					'900', 
					strtoupper(get_the_title($libraryID) . ' IP Login'),
					get_field('library_group_name', $libraryID), 
					get_field('library_group_region', $libraryID), 
					'english', current_time('mysql'), '', '', 'active');

				// record this event (characters table) - if row is not existing yet
				l4k_addCharacterActivity($ownerID);

				$_SESSION['ownerID'] 				= $ownerID;
				$_SESSION['library_id'] 			= $libraryID;
		    	$_SESSION['library_welcome_logo'] 	= get_field('logo_welcome', $libraryID);
		    	$_SESSION['library_permalink'] 		= get_the_permalink($libraryID);
		    	$_SESSION['library_barcode'] 		= strtoupper(get_the_title($libraryID) . ' IP Login');
		    	$_SESSION['library_name'] 			= get_the_title($libraryID);
		    	$_SESSION['library_group'] 			= get_field('library_group_name', $libraryID);
		    	$_SESSION['library_region'] 		= get_field('library_group_region', $libraryID);
		    	$_SESSION['library_remember'] 		= 1;
		    	$_SESSION['last_viewed_book'] 		= '';
		    	$_SESSION['auth_token'] 			= get_field('auto_login_auth_token', $libraryID);
		    	$_SESSION['auto_login_status'] 		= get_field('auto_login_status', $libraryID);

		    	// l4k_addWebActivity('900'); // record event 900 for member login
				l4k_addWebActivity('900', array('user-agent' => $_SERVER['HTTP_USER_AGENT'])); // record event 900 for member login
		    	l4k_rememberLoginCookie(); // create persistent cookie storing session data

		    	return $libraryID;

            }
        }
    }
    
    return false;
}

/**
 * ----------------------------------------------------------------
 * Check if auth param is present and determine auto login
 * ----------------------------------------------------------------
 */

function l4k_checkAuthAutoLogin() {

	if (isset($_GET['auth'])) 
    {

		$metaQuery = [
		    'relation' => 'AND',
		    ['key' => 'library_subscription_status', 'value' => 1, 'compare' => '='],
		    ['key' => 'auto_login_auth_token', 'value' => $_GET['auth'], 'compare' => '=']
		];

	    $library = get_posts([
	        'post_type'      => 'library',
	        'post_status'    => 'publish',
	        'posts_per_page' => -1,
	        'meta_query'     => $metaQuery,
	    ]);

	    // check if there's a library that has the auth token
	    // check too if that library has the auto login status set to true
		if ((!empty($library)) && (get_field('auto_login_status', $library[0]->ID))) 
		{
		    $library = $library[0];
		    $barcode = !empty($_GET['barcode']) ? strtoupper($_GET['barcode']) : strtoupper(get_the_title($library->ID) . ' Auto Login');

			// record event 900 for user login (user_login_logs table) - if row is not existing yet
			$ownerID = l4k_addUserLoginActivity(
				'900', 
				$barcode,
				get_field('library_group_name', $library->ID), 
				get_field('library_group_region', $library->ID), 
				'english', current_time('mysql'), '', '', 'active');

			// record this event (characters table) - if row is not existing yet
			l4k_addCharacterActivity($ownerID);

			$_SESSION['ownerID'] 				= $ownerID;
			$_SESSION['library_id'] 			= $library->ID;
	    	$_SESSION['library_welcome_logo'] 	= get_field('logo_welcome', $library->ID);
	    	$_SESSION['library_permalink'] 		= get_the_permalink($library->ID);
	    	$_SESSION['library_barcode'] 		= $barcode;
	    	$_SESSION['library_name'] 			= get_the_title($library->ID);
	    	$_SESSION['library_group'] 			= get_field('library_group_name', $library->ID);
	    	$_SESSION['library_region'] 		= get_field('library_group_region', $library->ID);
	    	$_SESSION['library_remember'] 		= 1;
	    	$_SESSION['last_viewed_book'] 		= '';
	    	$_SESSION['auth_token'] 			= $_GET['auth'];
	    	$_SESSION['auto_login_status'] 		= get_field('auto_login_status', $library->ID);

	    	l4k_addWebActivity('900'); // record event 900 for member login
	    	l4k_rememberLoginCookie(); // create persistent cookie storing session data
	    	
	    	return true;
		}

    }

    return false;

}

/**
 * ----------------------------------------------------------------
 * Check if auto logged in via whitelisted DOMAIN identifier
 * ----------------------------------------------------------------
 */

function l4k_checkDomainIdentifierAutoLogin() {

	$get = array_change_key_case($_GET, CASE_LOWER);
	$custID = isset($get['custid']) ? explode('/', $get['custid'])[0] : null; // get only the first custID when sometimes it's doubled

	if ($custID) {
	    if (have_rows('domain_whitelist', 'option')) {
	        while (have_rows('domain_whitelist', 'option')) {

	            the_row();
	        	if (get_sub_field('identifier') == $custID) {

					$libraryID = get_sub_field('library');

					// record event 900 for user login (user_login_logs table) - if row is not existing yet
					$ownerID = l4k_addUserLoginActivity(
						'900', 
						strtoupper(get_the_title($libraryID) . ' Domain Login'),
						get_field('library_group_name', $libraryID), 
						get_field('library_group_region', $libraryID), 
						'english', current_time('mysql'), '', '', 'active');

					// record this event (characters table) - if row is not existing yet
					l4k_addCharacterActivity($ownerID);

					$_SESSION['ownerID'] 				= $ownerID;
					$_SESSION['library_id'] 			= $libraryID;
			    	$_SESSION['library_welcome_logo'] 	= get_field('logo_welcome', $libraryID);
			    	$_SESSION['library_permalink'] 		= get_the_permalink($libraryID);
			    	$_SESSION['library_barcode'] 		= strtoupper(get_the_title($libraryID) . ' Domain Login');
			    	$_SESSION['library_name'] 			= get_the_title($libraryID);
			    	$_SESSION['library_group'] 			= get_field('library_group_name', $libraryID);
			    	$_SESSION['library_region'] 		= get_field('library_group_region', $libraryID);
			    	$_SESSION['library_remember'] 		= 1;
			    	$_SESSION['last_viewed_book'] 		= '';
			    	$_SESSION['auth_token'] 			= get_field('auto_login_auth_token', $libraryID);
			    	$_SESSION['auto_login_status'] 		= get_field('auto_login_status', $libraryID);

			    	l4k_addWebActivity('900'); // record event 900 for member login
					l4k_rememberLoginCookie(); // create persistent cookie storing session data

	        		return true;

	        	}

	        }
		}
	}
	
	return false;

}

/**
 * ----------------------------------------------------------------
 * On WP user login, create a session for the member-home too
 * ----------------------------------------------------------------
 */

function l4k_loginMemberAfterWPUserLogin($user_login, $user) {

    if (!session_id()) { session_start(); } // make sure session exists
	$libraryID = get_field('library', 'user_'.$user->ID); // get library ID of the current logged in user

	if (!empty($libraryID)) 
	{
		// record event 900 for user login (user_login_logs table) - if row is not existing yet
		$ownerID = l4k_addUserLoginActivity(
			'900', 
			strtoupper(get_the_title($libraryID) . ' Staff Login'),
			get_field('library_group_name', $libraryID), 
			get_field('library_group_region', $libraryID), 
			'english', current_time('mysql'), '', '', 'active');

		// record this event (characters table) - if row is not existing yet
		l4k_addCharacterActivity($ownerID);

		$_SESSION['ownerID'] 				= $ownerID;
		$_SESSION['library_id'] 			= $libraryID;
    	$_SESSION['library_welcome_logo'] 	= get_field('logo_welcome', $libraryID);
    	$_SESSION['library_permalink'] 		= get_the_permalink($libraryID);
    	$_SESSION['library_barcode'] 		= strtoupper(get_the_title($libraryID) . ' Staff Login');
    	$_SESSION['library_name'] 			= get_the_title($libraryID);
    	$_SESSION['library_group'] 			= get_field('library_group_name', $libraryID);
    	$_SESSION['library_region'] 		= get_field('library_group_region', $libraryID);
    	$_SESSION['library_remember'] 		= 1;
    	$_SESSION['last_viewed_book'] 		= '';
    	$_SESSION['auth_token'] 			= get_field('auto_login_auth_token', $libraryID);
    	$_SESSION['auto_login_status'] 		= get_field('auto_login_status', $libraryID);

    	l4k_addWebActivity('900'); // record event 900 for member login
    	l4k_rememberLoginCookie(); // create persistent cookie storing session data
	}

} 
add_action('wp_login', 'l4k_loginMemberAfterWPUserLogin', 10, 2);

/**
 * ----------------------------------------------------------------
 * When visiting Staff Access page, check if staff is logged in
 * If yes, redirect to library's dashboard
 * ----------------------------------------------------------------
 */

function l4k_checkStaffLoggedIn() {

	if (get_query_var('dashboard', false) === false) {
		if (is_user_logged_in()) { 
			$customRedirect = get_the_permalink(get_field('library', 'user_'.get_current_user_id())).'dashboard'; 
			wp_safe_redirect($customRedirect); 
		} 
	}

	return; 

}

/**
 * ----------------------------------------------------------------
 * For staff, block access to wp-admin login and etc.
 * ----------------------------------------------------------------
 */

function l4k_blockSubscriberAdminAccess() {

    if (wp_doing_ajax()) { return; } // allow admin-ajax requests

    if (is_admin() && is_user_logged_in()) 
    {
        $user = wp_get_current_user();
        if (in_array('subscriber', (array) $user->roles)) 
        {
            $redirect = wp_get_referer(); // get previous page
            if (!$redirect) { $redirect = home_url(); } // fallback to home if referrer is empty
            wp_safe_redirect($redirect);
            exit;
        }
    }

}
add_action('init', 'l4k_blockSubscriberAdminAccess');

/**
 * ----------------------------------------------------------------
 * Anyone who tries to access wp-login.php, redirect to wp-admin
 * ----------------------------------------------------------------
 */

add_action('init', function () {

    if (is_user_logged_in() && $GLOBALS['pagenow'] === 'wp-login.php' && !isset($_GET['action'])) {
        wp_safe_redirect(admin_url());
        exit;
    }

});

/**
 * ----------------------------------------------------------------
 * Auto-redirect all books like the below 
 * OLD /aiovg_videos/the-fish-and-the-cat-japanese-romaji-flipbook/	
 * NEW /below/the-fish-and-the-cat-japanese-romaji-flipbook/	
 * ----------------------------------------------------------------
 */

add_action('template_redirect', function() {

    $request_uri = $_SERVER['REQUEST_URI'];
    
    if (strpos($request_uri, 'aiovg_videos') !== false) { // check if the URL contains 'aiovg_videos'
        
        $pattern = '/aiovg_videos\/(.+?)(\/|$)/'; // extract the slug (the part after aiovg_videos/)
        if (preg_match($pattern, $request_uri, $matches)) 
        {
            $slug = $matches[1];
            $new_url = home_url('/books/' . $slug . '/'); // build the new URL
            wp_redirect($new_url, 301); // perform 301 permanent redirect
            exit;
        }
    }

});

/**
 * ----------------------------------------------------------------
 * Generate barcode from trial/competition and auto login
 * ----------------------------------------------------------------
 */ 

function l4k_trialFormAutoLogin($isTeacher=false) {

	$allowedDomains = array('localhost', 'v2.lote4kids.com', 'lote4kids.com');

	if (in_array(l4k_getClientReferrerDomain(), $allowedDomains)) {
		
		$libraryID = get_the_ID();

		// if coming from competition, trigger the getting started and interim/summary emails
		if ($_SESSION['is_competition']) {

		    $lib_bcc        		= get_field('lib_bcc_recipients', $libraryID);
		    $lib_login_url  		= get_field('lib_login_url', $libraryID);
		    $lib_mobile_txt 		= get_field('lib_name_for_mobile_text', $libraryID);
		    $lib_from_name  		= get_field('lib_from_email_name', $libraryID);
		    $lib_email_sent_from  	= get_field('lib_email_sent_from', $libraryID);
		    $lib_sig        		= get_field('lib_email_signature', $libraryID);
		    $lib_calendly   		= get_field('lib_calendly_link', $libraryID);
		    $fallback_sig 			= l4k_fallbackEmailSignature(); // fallback ONLY if the ACF field is empty

		    $trial_data = [
		        'first_name'            => $_SESSION['is_competition__name'],
		        'barcode'               => $_SESSION['trial_barcode'],
		        'trial_details'         => [
		            'barcode' 		=> $_SESSION['trial_barcode'],
		            'website' 		=> $_SESSION['website'], 
		            'library' 		=> $_SESSION['is_competition__library'],
		            'library_id' 	=> $libraryID
		        ],
		        'kindRegards'           => !empty($lib_from_name) ? $lib_from_name : 'Sunny',
		        'sent_from_name'        => !empty($lib_from_name) ? $lib_from_name : 'LOTE4Kids',
		        'sent_from'             => !empty($lib_email_sent_from) ? $lib_email_sent_from : 'sales@storytimepods.com',
		        'email_signature'       => !empty($lib_sig) ? $lib_sig : $fallback_sig,
		        'clickHereLink'         => !empty($lib_calendly) ? $lib_calendly : 'https://calendly.com/lote4kids/demo-meeting',
		        'trialExtensionLink'    => $_SESSION['trialurl'] ?? '',
		        'login_url'             => !empty($lib_login_url) ? $lib_login_url : $_SESSION['website'],
		        'mobile_text'           => !empty($lib_mobile_txt) ? $lib_mobile_txt : 'Demo Library',
		        'bcc_recipients'        => !empty($lib_bcc) ? $lib_bcc : 'pete@storytimepods.com.au, sunny@storytimepods.com.au, storytimepods@pipedrivemail.com',
		        'second_email_schedule' => 14
		    ];

  			global $wpdb;

			$result = $wpdb->insert(
			    $wpdb->prefix . 'alternate_barcode',
			    array(
			        'barcode_prefix' 	=> $_SESSION['is_competition__barcode_prefix'],
			        'barcode_number' 	=> $_SESSION['is_competition__barcode_number'],
			        'barcode' 			=> $_SESSION['trial_barcode'],
			        'name' 				=> $_SESSION['is_competition__name'],
			        'library' 			=> $_SESSION['is_competition__library'],
			        'email' 			=> $_SESSION['is_competition__email'],
			        'phone' 			=> $_SESSION['is_competition__phone'],
			        'job_title' 		=> $_SESSION['is_competition__job_title'],
			        'time' 				=> current_time('mysql'),
			        'expiration_date' 	=> date('Y-m-d H:i:s', strtotime(current_time('mysql') . ' +14 days')),
			        'is_teacher' 		=> '0',
			        'session_token' 	=> ''
			    ),
			    array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')
			);

			if ($result) {
				// append '(proceeded to trial)' if they came from competition and proceeded to trial
				foreach ($_SESSION['is_competition__fields'] as $key => $field) {
				    if ($field['name'] === 'Signup Form') {
				        $_SESSION['is_competition__fields'][$key]['value'] .= ' (proceeded to trial)';
				        break;
				    }
				}
				l4k_push_mapped_form_data($_SESSION['is_competition__form'], $_SESSION['is_competition__fields']);
				l4k_send_getting_started_email($_SESSION['is_competition__email'], $_SESSION['is_competition__name'], $_SESSION['trial_barcode'], $libraryID); 
	 			schedule_trial_emails($_SESSION['is_competition__email'], $trial_data); 
	 		}

		}

		// record event 900 for user login (user_login_logs table) - if row is not existing yet
		$ownerID = l4k_addUserLoginActivity(
			'900', 
			strtoupper($_SESSION['trial_barcode']),
			get_field('library_group_name', $libraryID), 
			get_field('library_group_region', $libraryID), 
			'english', current_time('mysql'), '', '', 'active');

		// record this event (characters table) - if row is not existing yet
		l4k_addCharacterActivity($ownerID);

		$_SESSION['ownerID'] 				= $ownerID;
		$_SESSION['library_id'] 			= $libraryID;
    	$_SESSION['library_welcome_logo'] 	= get_field('logo_welcome', $libraryID);
    	$_SESSION['library_permalink'] 		= get_the_permalink($libraryID);
    	$_SESSION['library_barcode'] 		= strtoupper($_SESSION['trial_barcode']);
    	$_SESSION['library_name'] 			= get_the_title($libraryID);
    	$_SESSION['library_group'] 			= get_field('library_group_name', $libraryID);
    	$_SESSION['library_region'] 		= get_field('library_group_region', $libraryID);
    	$_SESSION['library_remember'] 		= 1;
    	$_SESSION['last_viewed_book'] 		= '';
    	$_SESSION['auth_token'] 			= get_field('auto_login_auth_token', $libraryID);
    	$_SESSION['auto_login_status'] 		= get_field('auto_login_status', $libraryID);
    	$_SESSION['is_teacher']				= ($isTeacher) ? true : false; 

    	l4k_addWebActivity('900'); // record event 900 for member login
    	l4k_rememberLoginCookie(); // create persistent cookie storing session data

	}

}

/**
 * ----------------------------------------------------------------
 * The code section below will make URLs like /libraries/au-demo 
 * work directly without the /libraries/ 
 * ex: lote4kids.com/au-demo == lote4kids.com/libraries/au-demo
 * ----------------------------------------------------------------
 */ 

add_action('init', function() {
    $patterns = [
        ['^libraries/([^/]+)/trial/?$', 'library=$matches[1]&trial=1'], // /libraries/slug/trial
        ['^libraries/([^/]+)/competition/?$', 'library=$matches[1]&competition=1'], // /libraries/slug/competition
        ['^libraries/([^/]+)/webinar/?$', 'library=$matches[1]&webinar=1'], // /libraries/slug/webinar
        ['^libraries/([^/]+)/staff-training/?$', 'library=$matches[1]&staff-training=1'], // /libraries/slug/staff-training
        ['^libraries/([^/]+)/dashboard/?$', 'library=$matches[1]&dashboard=1'], // /libraries/slug/dashboard
        ['^libraries/([^/]+)/?$', 'library=$matches[1]'], // /libraries/slug
        ['^([^/]+)/trial/?$', 'library=$matches[1]&trial=1'], // /slug/trial (root)
        ['^([^/]+)/competition/?$', 'library=$matches[1]&competition=1'], // /slug/competition (root)
        ['^([^/]+)/webinar/?$', 'library=$matches[1]&webinar=1'], // /slug/webinar (root)
        ['^([^/]+)/staff-training/?$', 'library=$matches[1]&staff-training=1'], // /slug/staff-training (root)
        ['^([^/]+)/dashboard/?$', 'library=$matches[1]&dashboard=1'], // /slug/dashboard (root)
        ['^([^/]+)/?$', 'library=$matches[1]'] // /slug (root)
    ];
    
    foreach ($patterns as [$regex, $query]) {
        add_rewrite_rule($regex, "index.php?$query", 'top');
    }
}, 20);

add_filter('query_vars', function($vars) {
    return array_merge($vars, ['library', 'trial', 'competition', 'webinar', 'staff-training', 'dashboard']);
});

add_filter('post_type_link', function($url, $post) {
    if ($post->post_type !== 'library') return $url;
    return home_url("{$post->post_name}/");
}, 10, 2);

add_filter('request', function($query_vars) {
    if (empty($query_vars['library'])) {
        return $query_vars;
    }
    
    $library_slug = $query_vars['library'];
    $cache_key = 'slug_lookup_' . $library_slug;
    $cached = get_transient($cache_key); // try to get from cache (24 hour cache)
    if ($cached !== false) { return $cached; } // found in cache, use it
    
    // not in cache, do database lookup
    $existing_post = get_posts([
        'name' => $library_slug,
        'post_type' => 'any',
        'post_status' => 'publish',
        'posts_per_page' => 1,
        'fields' => 'ids',
    ]);
    
    $result = ['error' => '404']; // default to 404
    
    if (!empty($existing_post)) {
        $post = get_post($existing_post[0]);
        
        if ($post->post_type === 'page') { $result = ['page' => '', 'pagename' => $library_slug]; } 
        elseif ($post->post_type === 'library') { $result = $query_vars; } // keep library query vars 
        else { $result = ['post_type' => $post->post_type, 'name' => $library_slug]; }
    }
    
    set_transient($cache_key, $result, 24 * HOUR_IN_SECONDS); // save to cache for 24 hours
    
    return $result;
}, 10);

add_action('template_redirect', function() {
    $library_slug = get_query_var('library');
    
    if (!$library_slug) return;
    
    $query = new WP_Query([
        'post_type' => 'library',
        'name' => $library_slug,
        'posts_per_page' => 1,
        'no_found_rows' => true,
        'update_post_meta_cache' => false,
        'update_post_term_cache' => false,
    ]);
    
    if ($query->have_posts()) {
        global $wp_query;
        $wp_query = $query;
        $wp_query->is_singular = true;
        $wp_query->is_single = true;
        $wp_query->is_404 = false;
    }
});

// clear cache when any post is saved/deleted
add_action('save_post', function($post_id, $post) {
    delete_transient('slug_lookup_' . $post->post_name);
}, 10, 2);

add_action('deleted_post', function($post_id, $post) {
    delete_transient('slug_lookup_' . $post->post_name);
}, 10, 2);
?>