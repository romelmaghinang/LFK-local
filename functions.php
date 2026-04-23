<?php
/**
 * ----------------------------------------------------------------
 * Check if previously logged in
 * ----------------------------------------------------------------
 */

add_action('init', function () {

	if (session_status() === PHP_SESSION_NONE) { session_start(); } // start session if not already started
    if (!empty($_SESSION['library_barcode'])) { return; } // if session already exists → do nothing
	if (l4k_checkCookieLoggedIn()) { return; } // if cookie exists → rebuild session
	if (l4k_checkDomainAutoLogin()) { return; } // if domain is whitelisted -> create session and login automatically
	if (l4k_checkDomainIdentifierAutoLogin()) { return; } // if custID GET para is present -> create session and login automatically
	// if (l4k_checkIpAutoLogin()) { return; } // if IP is whitelisted -> create session and login automatically 
	if (l4k_checkAuthAutoLogin()) { return; } // if auth querystring exists -> create session and login automatically

});

/**
 * ----------------------------------------------------------------
 * Require other functions
 * ----------------------------------------------------------------
 */

require_once('functions/functions-dev.php'); 		// temporary dev functions ex. for debugging
require_once('functions/functions-redirect.php'); 	// anything that has to do with logins and redirects 
require_once('functions/functions-admin.php'); 		// all admin functions
require_once('functions/functions-retrieve.php'); 	// all database retrieval functions
require_once('functions/functions-update.php'); 	// all database update functions
require_once('functions/functions-ajax.php'); 		// all ajax functions
require_once('functions/functions-mobile.php'); 	// mobile endpoints & settings
require_once('functions/functions-avatar.php'); 	// avatar creation - to be migrated soon
require_once('functions/functions-email.php'); 		// all code with email functionality

/**
 * ----------------------------------------------------------------
 * Enqueue styles and scripts
 * ----------------------------------------------------------------
 */

add_action('wp_enqueue_scripts', function () {

	l4k_loadStyles();
	l4k_loadScripts();

});

function l4k_loadStyles() {

	$assetVersion = (WP_ENVIRONMENT_TYPE == 'PRD') ? WP_ASSET_VERSION : time(); 

    // load only styles specific per page
   
    if (is_page_template('page-templates/page-home.php'))                   { wp_enqueue_style('home', get_stylesheet_directory_uri() . '/assets/css/home.css', [], $assetVersion); }
    if (is_page_template('page-templates/page-home-schools.php'))           { wp_enqueue_style('home', get_stylesheet_directory_uri() . '/assets/css/home-schools.css', [], $assetVersion); }
    if (is_page_template('page-templates/page-home-libraries.php'))         { wp_enqueue_style('home', get_stylesheet_directory_uri() . '/assets/css/home-libraries.css', [], $assetVersion); }
    if (is_page_template('page-templates/page-home-teachers.php'))          { wp_enqueue_style('home', get_stylesheet_directory_uri() . '/assets/css/home-teachers.css', [], $assetVersion); }
    if (is_page_template('page-templates/page-avatar.php'))                 { wp_enqueue_style('avatar', get_stylesheet_directory_uri() . '/assets/css/avatar.css', [], $assetVersion); }
    if (is_page_template('page-templates/page-streaks.php'))                { wp_enqueue_style('streaks', get_stylesheet_directory_uri() . '/assets/css/streaks.css', [], $assetVersion); }
    if (is_page_template('page-templates/page-faqs.php'))                   { wp_enqueue_style('faq', get_stylesheet_directory_uri() . '/assets/css/faq.css', [], $assetVersion); }
    if (is_page_template('page-templates/page-find-library.php'))           { wp_enqueue_style('find-library', get_stylesheet_directory_uri() . '/assets/css/find-library.css', [], $assetVersion); }
    if (is_page_template('page-templates/page-mobile-app.php'))             { wp_enqueue_style('mobile-app', get_stylesheet_directory_uri() . '/assets/css/mobile-app.css', [], $assetVersion); }
    if (is_page_template('page-templates/page-about-us.php'))               { wp_enqueue_style('about-us', get_stylesheet_directory_uri() . '/assets/css/about-us.css', [], $assetVersion); }
    if (is_page_template('page-templates/page-publishers.php'))             { wp_enqueue_style('publishers', get_stylesheet_directory_uri() . '/assets/css/publishers.css', [], $assetVersion); }
    if (is_page_template('page-templates/page-privacy-policy.php'))         { wp_enqueue_style('privacy-terms', get_stylesheet_directory_uri() . '/assets/css/privacy-terms.css', [], $assetVersion); }
    if (is_page_template('page-templates/page-terms-of-use.php'))           { wp_enqueue_style('privacy-terms', get_stylesheet_directory_uri() . '/assets/css/privacy-terms.css', [], $assetVersion); }    
    if (is_page_template('page-templates/page-available-languages.php'))    { wp_enqueue_style('member-home', get_stylesheet_directory_uri() . '/assets/css/member-home.css', [], $assetVersion); }    
    if (is_page_template('page-templates/page-contact-us.php'))             { wp_enqueue_style('contact', get_stylesheet_directory_uri() . '/assets/css/contact.css', [], $assetVersion); }
    if (is_page_template('page-templates/page-blog.php') || is_tag())       { wp_enqueue_style('blog', get_stylesheet_directory_uri() . '/assets/css/blog.css', [], $assetVersion); }
	if (is_page_template('page-templates/page-staff-access.php'))           { wp_enqueue_style('staff', get_stylesheet_directory_uri() . '/assets/css/staff.css', [], $assetVersion); }
	if (is_page_template('page-templates/page-staff-video.php'))           	{ wp_enqueue_style('staff-video', get_stylesheet_directory_uri() . '/assets/css/staff-video.css', [], $assetVersion); }
	if (is_page_template('page-templates/page-teacher-subscription.php'))   { wp_enqueue_style('teacher-subscription', get_stylesheet_directory_uri() . '/assets/css/teacher-subscription.css', [], $assetVersion); }
	if (is_page_template('page-templates/page-marketing-collateral.php'))   { wp_enqueue_style('marketing-collateral', get_stylesheet_directory_uri() . '/assets/css/marketing-collateral.css', [], $assetVersion); }
    if (is_singular('post'))                                                { wp_enqueue_style('blog', get_stylesheet_directory_uri() . '/assets/css/blog.css', [], $assetVersion); }
    if (is_404())                                                			{ wp_enqueue_style('404', get_stylesheet_directory_uri() . '/assets/css/404.css', [], $assetVersion); }

	if (is_search()) { 
		wp_enqueue_style('search', get_stylesheet_directory_uri() . '/assets/css/search.css', [], $assetVersion); 
		wp_enqueue_style('sidebar-main', get_stylesheet_directory_uri() . '/assets/css/sidebar-main.css', [], $assetVersion); 
	}
	if (is_singular('library')) { 
		wp_enqueue_style('library', get_stylesheet_directory_uri() . '/assets/css/library.css', [], $assetVersion); 
		wp_enqueue_style('sidebar-dashboard', get_stylesheet_directory_uri() . '/assets/css/sidebar-dashboard.css', [], $assetVersion);
	}    
	if (is_page_template('page-templates/page-member-home.php')) { 
		wp_enqueue_style('member-home', get_stylesheet_directory_uri() . '/assets/css/member-home.css', [], $assetVersion); 
		wp_enqueue_style('sidebar-main', get_stylesheet_directory_uri() . '/assets/css/sidebar-main.css', [], $assetVersion);
	} 
    if (is_singular('book')) { 
    	wp_enqueue_style('book', get_stylesheet_directory_uri() . '/assets/css/book.css', [], $assetVersion); 
    	wp_enqueue_style('sidebar-book', get_stylesheet_directory_uri() . '/assets/css/sidebar-book.css', [], $assetVersion); 
    	wp_enqueue_style('wp-discuz', get_stylesheet_directory_uri() . '/assets/css/wp-discuz.css', [], $assetVersion); 
	}
    if (is_singular('playlist')) { 
    	wp_enqueue_style('playlist', get_stylesheet_directory_uri() . '/assets/css/playlist.css', [], $assetVersion); 
    	wp_enqueue_style('sidebar-playlist', get_stylesheet_directory_uri() . '/assets/css/sidebar-playlist.css', [], $assetVersion); 
    	wp_enqueue_style('wp-discuz', get_stylesheet_directory_uri() . '/assets/css/wp-discuz.css', [], $assetVersion); 
	}
	if (is_singular('language')) { 
		wp_enqueue_style('language', get_stylesheet_directory_uri() . '/assets/css/language.css', [], $assetVersion); 
		wp_enqueue_style('sidebar-main', get_stylesheet_directory_uri() . '/assets/css/sidebar-main.css', [], $assetVersion); 
   	}
	if (is_singular('activity') || is_page_template('page-templates/page-activities.php')) { 
		wp_enqueue_style('activity', get_stylesheet_directory_uri() . '/assets/css/activity.css', [], $assetVersion); 
		wp_enqueue_style('sidebar-main', get_stylesheet_directory_uri() . '/assets/css/sidebar-main.css', [], $assetVersion); 
   	}

    // styles for all

    wp_enqueue_style('google-fonts', 'https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap', false);
    wp_enqueue_style('lote4kids-style', get_stylesheet_directory_uri() . '/style.css', [], $assetVersion);
    wp_enqueue_style('slick', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css', [], null);
    wp_enqueue_style('line-icons', 'https://cdn.lineicons.com/5.0/lineicons.css', [], '5.0');

    return;
}

function l4k_loadScripts() {

	$assetVersion = (WP_ENVIRONMENT_TYPE == 'STG') ? time() : WP_ASSET_VERSION; 

	// load only scripts specific per page

    if (is_404())							                				{ wp_enqueue_script('language', get_stylesheet_directory_uri() . '/assets/js/404.js', ['jquery'], $assetVersion, true); }
    if (is_singular('playlist'))							                { wp_enqueue_script('playlist', get_stylesheet_directory_uri() . '/assets/js/playlist.js', ['jquery'], $assetVersion, true); }
    if (is_page_template('page-templates/page-home.php'))                   { wp_enqueue_script('home', get_stylesheet_directory_uri() . '/assets/js/home.js', ['jquery'], $assetVersion, true); }
    if (is_page_template('page-templates/page-home-schools.php'))           { wp_enqueue_script('home', get_stylesheet_directory_uri() . '/assets/js/home-schools.js', ['jquery'], $assetVersion, true); }
    if (is_page_template('page-templates/page-home-libraries.php'))         { wp_enqueue_script('home', get_stylesheet_directory_uri() . '/assets/js/home-libraries.js', ['jquery'], $assetVersion, true); }
    if (is_page_template('page-templates/page-home-teachers.php'))          { wp_enqueue_script('home', get_stylesheet_directory_uri() . '/assets/js/home-teachers.js', ['jquery'], $assetVersion, true); }
    if (is_page_template('page-templates/page-find-library.php'))           { wp_enqueue_script('home', get_stylesheet_directory_uri() . '/assets/js/find-library.js', ['jquery'], $assetVersion, true); }
    if (is_page_template('page-templates/page-faqs.php'))                   { wp_enqueue_script('faq', get_stylesheet_directory_uri() . '/assets/js/faq.js', ['jquery'], $assetVersion, true); }
    if (is_page_template('page-templates/page-member-home.php'))            { wp_enqueue_script('member-home', get_stylesheet_directory_uri() . '/assets/js/member-home.js', ['jquery'], $assetVersion, true); }
    if (is_page_template('page-templates/page-teacher-subscription.php'))   { wp_enqueue_script('teacher-subscription', get_stylesheet_directory_uri() . '/assets/js/teacher-subscription.js', [], $assetVersion, true); }
    if (is_page_template('page-templates/page-available-languages.php'))    { wp_enqueue_script('member-home', get_stylesheet_directory_uri() . '/assets/js/member-home.js', ['jquery'], $assetVersion, true); }

	if (is_front_page() || is_page_template('page-templates/page-find-library.php')) { 
		wp_enqueue_script('botpress', 'https://cdn.botpress.cloud/webchat/v2.4/inject.js', ['jquery'], null, true); 
	}
    if (is_singular('book')) { 
    	wp_enqueue_script('pdf', 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js', ['jquery'], '', true); 
    	wp_enqueue_script('chart', 'https://cdn.jsdelivr.net/npm/chart.js', ['jquery'], '', false); 
    	wp_enqueue_script('turn', get_stylesheet_directory_uri() . '/assets/js/turn.js', ['jquery'], $assetVersion, true); 
    	wp_enqueue_script('book', get_stylesheet_directory_uri() . '/assets/js/book.js', ['jquery'], $assetVersion, true); 
    	wp_localize_script('book', 'book_ajax', [ 'url' => admin_url('admin-ajax.php') ]); // so you can use 'book_ajax.url' variable
    }
    if (is_singular('language')) { 
    	wp_enqueue_script('language', get_stylesheet_directory_uri() . '/assets/js/language.js', ['jquery'], $assetVersion, true); 
    	wp_localize_script('language', 'language_ajax', [ 'url' => admin_url('admin-ajax.php') ]); // so you can use 'language_ajax.url' variable
   	}
    if (is_singular('library')) { 
		wp_enqueue_script('library', get_stylesheet_directory_uri() . '/assets/js/library.js', ['jquery'], $assetVersion, true); 
		wp_localize_script('library', 'library_ajax', [ 'url' => admin_url('admin-ajax.php') ]); // so you can use 'library_ajax.url' variable
	}
	if (is_singular('activity')) { 
		wp_enqueue_script('activity', get_stylesheet_directory_uri() . '/assets/js/activity.js', ['jquery'], $assetVersion, true); 
		wp_localize_script('activity', 'activity_ajax', [ 'url' => admin_url('admin-ajax.php') ]); // so you can use 'library_ajax.url' variable
	}

    // scripts for all

    wp_enqueue_script('jquery');
    wp_enqueue_script('slick', '//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js', ['jquery'], null, true);
    wp_enqueue_script('google-translate', '//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit', ['jquery'], null, true);
    wp_enqueue_script('vimeo', '//player.vimeo.com/api/player.js', ['jquery'], null, true);
    wp_enqueue_script('backstretch', get_stylesheet_directory_uri() . '/assets/js/jquery.backstretch.js', ['jquery'], null, true);
    wp_enqueue_script('lote4kids', get_stylesheet_directory_uri() . '/assets/js/main.js', ['jquery'], $assetVersion, true);

	wp_localize_script('lote4kids', 'main_ajax', [ 'url' => admin_url('admin-ajax.php') ]); // so you can use 'main_ajax.url' variable
	wp_localize_script('lote4kids', 'l4k_vars', array(
		'page_id' 			=> get_the_ID(), // so you can use 'l4k_vars.page_id' variable
	    'library_barcode' 	=> isset($_SESSION['library_barcode']) ? $_SESSION['library_barcode'] : '' // so you can use 'l4k_vars.library_barcode' variable
	));

    return;

}

/**
 * ----------------------------------------------------------------
 * Prepare current page's breadcrumb
 * ----------------------------------------------------------------
 */

function l4k_breadcrumbs() {

    global $post;
    global $wp;

    $uri = $_SERVER['REQUEST_URI'];
	$isDashboard = (strpos($uri, '/dashboard') !== false);
	$isTrial = (strpos($uri, '/trial') !== false);
	$isCompetition = (strpos($uri, '/competition') !== false);

    $isReadingPack = l4k_isReadingPackContext();

    if (!empty($_SESSION['library_barcode'])) {
	    $linksArr = array(array('label' => '<i class="lni lni-home-2"></i> <strong>Home</strong>',
                           		'permalink' => home_url('/member-home')));
    } else {
	    $linksArr = array(array('label' => '<i class="lni lni-home-2"></i> <strong>Home</strong>',
                           		'permalink' => get_site_url()));
    }

    // do not show breadcrumb on home and 404 pages
    if (is_front_page() || is_404()) 
    {
        return array();
    }

    // member home
    elseif (is_page_template('page-templates/page-member-home.php')) 
    {
    	$linksArr[] = array('label' => 'Member Home', 'permalink' => get_permalink());
    }

    // home > [page title]
    elseif (is_search()) 
    {
        $linksArr[] = array('label' => "Search", 'permalink' => l4k_getCurrentURL());
        $linksArr[] = array('label' => "'".get_search_query()."'", 'permalink' => l4k_getCurrentURL());
    }

    // home > avatar
    elseif (is_page_template('page-templates/page-avatar.php')) 
    {
        $linksArr[] = array('label' => 'Dress Your Avatar', 'permalink' => get_permalink());
    }

    // home > [page title]
    elseif (is_singular('page')) 
    {
        $linksArr[] = array('label' => get_the_title(), 'permalink' =>get_permalink());

        // cases like Blog > Page 2 for example
        if (is_paged())
        { 
            $paged = get_query_var('paged') ? get_query_var('paged') : 1;
            $current_url = home_url(add_query_arg([], $wp->request));
            $linksArr[] = array('label' => "Page ". $paged, 'permalink' => $current_url);
        }
    }

    // home > blog > [post title]
    elseif (is_singular('post')) 
    {
        $blog_page = get_page_by_path('blog');
        $linksArr[] = array('label' => "Blog", 'permalink' => get_permalink($blog_page->ID));
        $linksArr[] = array('label' => get_the_title(), 'permalink' => get_permalink());
    }

    // home > blog > tag > [tag title]
    elseif (is_tag()) 
    {
     
        $blog_page = get_page_by_path('blog');
        $linksArr[] = array('label' => "Blog", 'permalink' => get_permalink($blog_page->ID));

        $tag = get_queried_object();
        $tag_link = get_tag_link($tag->term_id);
        $linksArr[] = array('label' => 'Tag', 'permalink' => $tag_link);
        $linksArr[] = array('label' => $tag->name, 'permalink' => $tag_link);

        // cases like Tag > Page 2 for example
        if (is_paged())
        { 
            $paged = get_query_var('paged') ? get_query_var('paged') : 1;
            $current_url = home_url(add_query_arg([], $wp->request));
            $linksArr[] = array('label' => "Page ". $paged, 'permalink' => $current_url);
        }
    }

    // home > [language title]
    elseif (is_singular('language') && !$isReadingPack) 
    {
        $linksArr[] = array('label' => get_the_title(),'permalink' => get_permalink());
    }

    // home > [language title] > reading pack
    elseif (is_singular('language') && $isReadingPack)  
    {
        $linksArr[] = array('label' => get_the_title(), 'permalink' => get_permalink());
        $linksArr[] = array('label' => 'Reading Pack', 'permalink' => get_permalink().'reading-pack');
    }

    // home > [language title] > [book title]
    elseif (is_singular('book')) 
    {
        $langID = get_field('language');
        $linksArr[] = array('label' => get_the_title($langID), 'permalink' => get_permalink($langID));

        if ($isReadingPack) { 
			$linksArr[] = array('label' => 'Reading Pack', 'permalink' => get_permalink($langID).'reading-pack');
        }

        $linksArr[] = array('label' => get_the_title(get_field('native_story')), 'permalink' => get_permalink());
    }

    // home > [language title] > [playlist title]
    elseif (is_singular('playlist')) 
    {
    	$langID = get_field('language', get_the_ID());
        $linksArr[] = array('label' => get_the_title($langID), 'permalink' => get_permalink($langID));
        $linksArr[] = array('label' => get_field('display_title', get_the_ID()), 'permalink' => get_permalink());
    }

    // home > activities > [activity title]
    elseif (is_singular('activity')) 
    {
        $activities_page = get_page_by_path('activities');
        $linksArr[] = array('label' => "Activities", 'permalink' => get_permalink($activities_page->ID));
        $linksArr[] = array('label' => get_the_title(), 'permalink' => get_permalink());
    }

    // home > [library title] > Dashboard
    elseif (is_singular('library') && $isDashboard)  
    {
        $linksArr[] = array('label' => get_the_title(), 'permalink' => get_permalink());
        $linksArr[] = array('label' => 'Dashboard', 'permalink' => '');
    }

    // home > [library title] > Trial
    elseif (is_singular('library') && $isTrial) 
    {
        $linksArr[] = array('label' => get_the_title(), 'permalink' => get_permalink());
        $linksArr[] = array('label' => 'Trial', 'permalink' => '');
    }

    // home > [library title] > Competition
    elseif (is_singular('library') && $isCompetition) 
    {
        $linksArr[] = array('label' => get_the_title(), 'permalink' => get_permalink());
        $linksArr[] = array('label' => 'Competition', 'permalink' => '');
    }

    // home > [library title]
    elseif (is_singular('library')) 
    {
        $linksArr[] = array('label' => get_the_title(), 'permalink' => get_permalink());
    }

    return $linksArr;
}

/**
 * ----------------------------------------------------------------
 * Add active class to the main nav when needed
 * ----------------------------------------------------------------
 */

add_filter('nav_menu_css_class', function ($classes, $item) {

    if (is_singular('activity')) { if ($item->ID == 39) { $classes[] = 'current-menu-item'; } } // activities menu item
    if (is_singular('playlist')) { if ($item->ID == 42) { $classes[] = 'current-menu-item'; } } // books menu item
    if (is_singular('library') && (get_query_var('dashboard', false) !== false)) { if ($item->ID == 49) { $classes[] = 'current-menu-item'; } } // staff access menu item
    if (is_singular('post') || is_tag()) { if ($item->ID == 41) { $classes[] = 'current-menu-item'; } } // blog menu item

    return $classes;

}, 10, 2);

/**
 * ----------------------------------------------------------------
 * Remove domain and return path only for any URL
 * ----------------------------------------------------------------
 */

function l4k_getLinkPathOnly($url) {

	return home_url().parse_url($url, PHP_URL_PATH);

}

/**
 * ----------------------------------------------------------------
 * Remove domain and return path only for any URL
 * ----------------------------------------------------------------
 */

function l4k_getBGColor() {

	if (is_singular('book') || is_singular('playlist')) { 
		$currentLang = get_field('language', get_the_ID());
		$bgColor = get_field('lang_background_color', $currentLang);
		return $bgColor;
	}

	if (is_singular('language')) { 
		$bgColor = get_field('lang_background_color', get_the_ID());
		return $bgColor;
	}

}

/**
 * ----------------------------------------------------------------
 * Add Logout link to the main menu if session exists
 * ----------------------------------------------------------------
 */

add_filter('wp_nav_menu_items', function($items, $args) {

    if ($args->menu === 'Main Menu') {

        // change "Staff Access" to "Teacher Access" if teacher is logged in
        if (isset($_SESSION['is_teacher']) && $_SESSION['is_teacher'] == '1') {
            $items = str_replace('>Staff Access<', '>Teacher Access<', $items);
        }

        if (isset($_SESSION['library_barcode']) && ($_SESSION['library_barcode'] != '')) {
        	// if a teacher is logged in, the URL for the logout link should be member-and-staff-logout
        	if (isset($_SESSION['is_teacher']) && ($_SESSION['is_teacher'] == '1')) {
				$logoutLink = home_url('/?member-and-staff-logout');
        	} else {
				$logoutLink = home_url('/?member-logout');
        	}

            $items .= '
            	<li class="menu-item logout">
            		<a title="Logout '.$_SESSION['library_barcode'].'" href="' . $logoutLink . '">Logout
            			<i class="lni lni-exit"></i>
            		</a>
            	</li>';
        }

    }

    return $items;

}, 10, 2);

/**
 * ----------------------------------------------------------------
 * Handle member logout via ?member-logout
 * ----------------------------------------------------------------
 */

add_action('init', function() {

	if (isset($_GET['member-and-staff-logout'])) {

	    session_start(); // ensure session is active before touching it
	    $_SESSION = [];
	    session_destroy();
	    
	    setcookie('remember_member', '', time() - 3600, '/');
	    wp_clear_auth_cookie();
	    wp_logout();
	    
	    if (isset($_GET['single-device'])) {
	    	wp_redirect(home_url().'?single-device');
	    	exit;
	    }

		wp_redirect(home_url());
	    exit;
	    
	}

    if (isset($_GET['member-logout'])) {

    	$_SESSION['ownerID'] 				= '';
    	$_SESSION['library_id'] 			= '';
    	$_SESSION['library_welcome_logo'] 	= '';
    	$_SESSION['library_permalink'] 		= '';
    	$_SESSION['library_barcode'] 		= '';
    	$_SESSION['library_name']	 		= '';
    	$_SESSION['library_group']	 		= '';
    	$_SESSION['library_region'] 		= '';
    	$_SESSION['library_remember'] 		= '';
    	$_SESSION['last_viewed_book'] 		= '';  
    	$_SESSION['auth_token'] 			= '';
    	$_SESSION['auto_login_status'] 		= '';
    	$_SESSION['is_teacher'] 			= '';
    	$_SESSION['is_competition'] 		= '';

        setcookie('remember_member', '', time() - 3600, '/'); // delete persistent cookie

        wp_safe_redirect(home_url());

        exit; 

    }

});

/**
 * ----------------------------------------------------------------
 * Save the currently viewing book to session
 * To be displayed as last viewed
 * ----------------------------------------------------------------
 */

function l4k_updateLastViewedBook($bookID) {

	$_SESSION['last_viewed_book'] = $bookID;

}

/**
 * ----------------------------------------------------------------
 * Parse vimeo URL and get the IDs
 * ----------------------------------------------------------------
 */

function l4k_parseVimeoUrl($vimeoURL) {

	$idArr 		= array();
	$vimeoURL 	= rtrim($vimeoURL, '/'); // remove trailing slash
	$parts 		= parse_url($vimeoURL, PHP_URL_PATH);  // returns "/1005728639/2da9378777"
	$idArr 		= explode('/', trim($parts, '/')); // split by slash

	return $idArr;

}

/**
 * ----------------------------------------------------------------
 * Retrieve visitor's IP address even when behind a proxy
 * ----------------------------------------------------------------
 */

function l4k_getClientIP() {

    $keys = [
        'HTTP_CLIENT_IP',
        'HTTP_X_FORWARDED_FOR',
        'HTTP_X_FORWARDED',
        'HTTP_X_CLUSTER_CLIENT_IP',
        'HTTP_FORWARDED_FOR',
        'HTTP_FORWARDED',
        'REMOTE_ADDR'
    ];

    foreach ($keys as $key) {
        if (!empty($_SERVER[$key])) {
            $ipList = explode(',', $_SERVER[$key]); // Could contain multiple IPs
            foreach ($ipList as $ip) {
                $ip = trim($ip);
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }
    }

    return $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN'; // fallback to REMOTE_ADDR even if private

}

/**
 * ----------------------------------------------------------------
 * Retrieve visitor's domain referrer
 * ----------------------------------------------------------------
 */

function l4k_getClientReferrerDomain() {

	$referrer = $_SERVER['HTTP_REFERER'] ?? ''; // get the referrer URL
    if (empty($referrer)) { return ''; } // if no referrer, return empty string
    $parsedUrl = parse_url($referrer); // parse the URL and get the host (domain)
    return isset($parsedUrl['host']) ? $parsedUrl['host'] : ''; // return the host/domain if it exists

}

/**
 * ----------------------------------------------------------------
 * Check if book as quiz
 * ----------------------------------------------------------------
 */

function l4k_hasQuiz($bookID) {

	if (have_rows('download_links', $bookID)) :
		while (have_rows('download_links', $bookID)) : the_row();

			if (get_sub_field('activity') == 'Quiz') { return true; }

		endwhile;
	endif;

	return false;

}

/**
 * ----------------------------------------------------------------
 * Generate the quick copy video link with auth code
 * ----------------------------------------------------------------
 */

function l4k_generateQuickCopyLink($bookID) {

	if ($_SESSION['auto_login_status'] && $_SESSION['auth_token']) {
		return get_permalink($bookID) . '?auth=' . $_SESSION['auth_token'];
	}

	return get_permalink($bookID);
	
}

/**
 * ----------------------------------------------------------------
 * Staff access login form
 * ----------------------------------------------------------------
 */

function l4k_staffLoginShortCode() {

    if (is_user_logged_in()) { return '<p>You are already logged in.</p>'; }

    $error_msg = '';

    // handle login POST
    if (isset($_POST['l4k_staff_login'])) {

        $creds = [
            'user_login'    => sanitize_text_field($_POST['log']),
            'user_password' => $_POST['pwd'],
            'remember'      => true,
        ];

        $user = wp_signon($creds);

        if (is_wp_error($user)) { $error_msg = '<p class="error-msg">'. $user->get_error_message() .'</p>'; } 
        else { wp_safe_redirect( home_url('/staff-access') ); exit; }
    }

    ob_start();
    ?>

    <?php echo $error_msg; ?>

    <form method="post" id="staff-loginform">
        <p class='login-username'>
            <label>Username<br>
                <input type="text" name="log" value="" required>
            </label>
        </p>

        <p class='login-password'>
            <label>Password<br>
                <input type="password" name="pwd" required>
            </label>
        </p>

        <p>
            <input type="submit" class='button-primary _btn' name="l4k_staff_login" value="Staff Login">
        </p>
    </form>

    <?php
    return ob_get_clean();
}
add_shortcode('l4k_staffLogin', 'l4k_staffLoginShortCode');

/**
 * ----------------------------------------------------------------
 * Staff forgot password form
 * ----------------------------------------------------------------
 */

function l4k_staffForgotPassword() {

    if (is_user_logged_in()) { 
        return '<p>You are already logged in.</p>'; 
    }
    
    $error_msg = '';
    $success_msg = '';
    
    // handle forgot password POST
    if (isset($_POST['l4k_staff_forgot_password'])) {
        $user_login = sanitize_text_field($_POST['user_login']);
        
        if (empty($user_login)) {
            $error_msg = '<p class="error-msg">Please enter a username or email address.</p>';
        } else {
            // Attempt to retrieve password
            $result = retrieve_password($user_login);
            
            if (is_wp_error($result)) {
                $error_msg = '<p class="error-msg">'. $result->get_error_message() .'</p>';
            } else {
                $success_msg = '<p class="success-msg">Password reset email has been sent. Please check your inbox.</p>';
            }
        }
    }
    
    ob_start();
    ?>
    <?php echo $error_msg; ?>
    <?php echo $success_msg; ?>
    <form method="post" id="staff-forgot-password-form">
        <p class='forgot-username'>
            <label>Email Address<br>
                <input type="text" name="user_login" value="" required>
            </label>
        </p>
        <p>
            <input type="submit" class='button-primary _btn' name="l4k_staff_forgot_password" value="Reset Password">
        </p>
    </form>
    <?php
    return ob_get_clean();

}
add_shortcode('l4k_staffForgotPassword', 'l4k_staffForgotPassword');

/**
 * ----------------------------------------------------------------
 * Do the redirect below if logged in user is a staff
 * Other adminstrators when logging should not be affected
 * ----------------------------------------------------------------
 */

function l4k_staffLoginRedirect($redirect_to, $requested_redirect_to, $user) {

    if (!isset($user->ID)) { return $redirect_to; } // make sure we have a valid user object

    // redirect to the library dashboard only if role is 'subscriber'
    if (in_array('subscriber', (array) $user->roles, true)) {
      	$customRedirect = get_the_permalink(get_field('library', 'user_'.$user->ID)).'dashboard'; 
        if (!empty($customRedirect)) { return esc_url_raw($customRedirect); }
    }

    return $redirect_to; // default: use WordPress redirect
}
add_filter('login_redirect', 'l4k_staffLoginRedirect', 10, 3);

/**
 * ----------------------------------------------------------------
 * Add /dashboard endpoint to "Libraries" custom post type
 * Old - lote4kids.com/libraries/au-demo?dashboard=true
 * New - lote4kids.com/libraries/au-demo/dashboard
 * ----------------------------------------------------------------
 */

function l4k_addDashboardEndpoint() {
    add_rewrite_endpoint('dashboard', EP_PERMALINK);
}
add_action('init', 'l4k_addDashboardEndpoint');

function l4k_dashboardQueryVars($vars) {
    $vars[] = 'dashboard';
    return $vars;
}
add_filter('query_vars', 'l4k_dashboardQueryVars');

/**
 * ----------------------------------------------------------------
 * Add /trial endpoint to "Libraries" custom post type
 * Old - lote4kids.com/libraries/au-demo?trial=true
 * New - lote4kids.com/libraries/au-demo/trial
 * ----------------------------------------------------------------
 */

function l4k_addTrialEndpoint() {
    add_rewrite_endpoint('trial', EP_PERMALINK);
}
add_action('init', 'l4k_addTrialEndpoint');

function l4k_TrialQueryVars($vars) {
    $vars[] = 'trial';
    return $vars;
}
add_filter('query_vars', 'l4k_TrialQueryVars');

/**
 * ----------------------------------------------------------------
 * Add /competition endpoint to "Libraries" custom post type
 * Old - lote4kids.com/libraries/au-demo?competition=true
 * New - lote4kids.com/libraries/au-demo/competition
 * ----------------------------------------------------------------
 */

function l4k_addCompetitionEndpoint() {
    add_rewrite_endpoint('competition', EP_PERMALINK);
}
add_action('init', 'l4k_addCompetitionEndpoint');

function l4k_CompetitionQueryVars($vars) {
    $vars[] = 'competition';
    return $vars;
}
add_filter('query_vars', 'l4k_CompetitionQueryVars');

/**
 * ----------------------------------------------------------------
 * Add /webinar endpoint to "Libraries" custom post type
 * Old - lote4kids.com/libraries/au-demo?webinar=true
 * New - lote4kids.com/libraries/au-demo/webinar
 * ----------------------------------------------------------------
 */

function l4k_addTrainingEndpoint() {
    add_rewrite_endpoint('webinar', EP_PERMALINK);
}
add_action('init', 'l4k_addTrainingEndpoint');

function l4k_TrainingQueryVars($vars) {
    $vars[] = 'webinar';
    return $vars;
}
add_filter('query_vars', 'l4k_TrainingQueryVars');

/**
 * ----------------------------------------------------------------
 * Add /review endpoint to "Libraries" custom post type
 * Old - lote4kids.com/libraries/au-demo?staff-training=true
 * New - lote4kids.com/libraries/au-demo/staff-training
 * ----------------------------------------------------------------
 */

function l4k_addReviewEndpoint() {
    add_rewrite_endpoint('staff-training', EP_PERMALINK);
}
add_action('init', 'l4k_addReviewEndpoint');

function l4k_ReviewQueryVars($vars) {
    $vars[] = 'staff-training';
    return $vars;
}
add_filter('query_vars', 'l4k_ReviewQueryVars');

/**
 * ----------------------------------------------------------------
 * Add /reading-pack endpoint to "Languages" custom post type
 * Old - lote4kids.com/languages/filipino-tagalog?reading-pack=true
 * New - lote4kids.com/languages/filipino-tagalog/reading-pack
 * ----------------------------------------------------------------
 */

function l4k_addReadingPackEndpoint() {
    add_rewrite_endpoint('reading-pack', EP_PERMALINK);
}
add_action('init', 'l4k_addReadingPackEndpoint');

function l4k_readingPackQueryVars($vars) {
    $vars[] = 'reading-pack';
    return $vars;
}
add_filter('query_vars', 'l4k_readingPackQueryVars');

/**
 * ----------------------------------------------------------------
 * Divide array into 3 parts (for table display)
 * This shows up in staff dashboard's Language Breakdown menu
 * ----------------------------------------------------------------
 */

function l4k_countTotalBookCountAndLatestRelease($languages) {

	$counter = $total = $mostRecent = 0; 

	while($counter < count($languages[0])) 
	{
		$total = 	$total + 
					$languages[0][$counter]['book_count'] + 
					$languages[1][$counter]['book_count'] + 
					$languages[2][$counter]['book_count']; 

		$mostRecent = ($languages[0][$counter]['date_published'] > $mostRecent) ? $languages[0][$counter]['date_published'] : $mostRecent;
		$mostRecent = ($languages[1][$counter]['date_published'] > $mostRecent) ? $languages[0][$counter]['date_published'] : $mostRecent;
		$mostRecent = ($languages[2][$counter]['date_published'] > $mostRecent) ? $languages[0][$counter]['date_published'] : $mostRecent;

		$counter++;
	}

	$summary['total_count'] = $total;
	$summary['most_recent'] = date('F j, Y', strtotime($mostRecent));

	return $summary;

}

/**
 * ----------------------------------------------------------------
 * Divide array into 3 parts (for table display)
 * ----------------------------------------------------------------
 */

function l4k_splitIntoThree($array) {

    $size = ceil(count($array) / 3);

    return [
        array_slice($array, 0, $size),
        array_slice($array, $size, $size),
        array_slice($array, $size * 2)
    ];

}

/**
 * ----------------------------------------------------------------
 * Add lote logo as custom avatar image for users
 * ----------------------------------------------------------------
 */

add_filter('avatar_defaults', function($avatars) {

    $custom_avatar_url = get_stylesheet_directory_uri() . '/assets/img/logo-lote-avatar.png';
    $avatars[$custom_avatar_url] = 'L4K Custom Avatar';
    return $avatars;

});

add_filter('get_avatar_url', function( $url, $id_or_email, $args) {

    // only override on local to avoid broken gravatar urls
    if ((strpos(home_url(), 'localhost') !== false) || (strpos(home_url(), '.local') !== false)) {
        return get_stylesheet_directory_uri() . '/assets/img/logo-lote-avatar.png';
    }

    return $url;
    
}, 10, 3 );

/**
 * ----------------------------------------------------------------
 * Compare 2 strings without any special characters if same
 * ----------------------------------------------------------------
 */

function l4k_compareStringsNoSpecial($stringA, $stringB) {

    // keep only A-Z (remove numbers and special characters)
    $stringA = preg_replace('/[^A-Za-z]/', '', $stringA);
    $stringB = preg_replace('/[^A-Za-z]/', '', $stringB);

    // make comparison case-insensitive
    $stringA = strtolower($stringA);
    $stringB = strtolower($stringB);

    return $stringA === $stringB;
    
}

/**
 * ----------------------------------------------------------------
 * Get the page's current URL
 * ----------------------------------------------------------------
 */

function l4k_getCurrentURL() {

    $protocol = is_ssl() ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'];
    $request_uri = $_SERVER['REQUEST_URI'];
    return $protocol . $host . $request_uri;

}

/**
 * ----------------------------------------------------------------
 * Exempt certain elements in the site from being searched
 * ----------------------------------------------------------------
 */

function l4k_exemptFromSearch($query) {

    // only modify frontend main search queries
    if ($query->is_search() && $query->is_main_query() && !is_admin()) {

        // remove 'story' CPT
        $post_types = get_post_types(['public' => true]);
        if (isset($post_types['story'])) { unset($post_types['story']); }
        $query->set('post_type', $post_types); // only search remaining post types

		// order posts: language CPT > normal posts > book CPT
		add_filter('posts_orderby', function($orderby, $query) {
		    global $wpdb;
		    if ($query->is_search() && $query->is_main_query()) {
		        $orderby = "
		            FIELD({$wpdb->posts}.post_type, 'language', 'post', 'book') ASC,
		            {$wpdb->posts}.post_title ASC,
		            {$wpdb->posts}.post_date DESC
		        ";
		    }
		    return $orderby;
		}, 10, 2);

    }

}
add_action('pre_get_posts', 'l4k_exemptFromSearch');

/**
 * ----------------------------------------------------------------
 * Change search results to a max of 200 instead of just 10
 * ----------------------------------------------------------------
 */

function l4k_increaseResultsCount($query) {

    // only modify frontend main search queries
    if ($query->is_search() && !is_admin() && $query->is_main_query()) {
        $query->set('posts_per_page', 200);
    }

}
add_action('pre_get_posts', 'l4k_increaseResultsCount');

/**
 * ----------------------------------------------------------------
 * Determine if section should be shown as RTL
 * ----------------------------------------------------------------
 */

function l4k_determineRTL($bookID) {

	$langID = get_field('language', $bookID);
	$bookType = get_field('book_type', $bookID);
	$textDirection = get_field('text_direction', $langID);

	if (($bookType == 'video_monolingual') && ($textDirection == 'rtl')) { return true; }

	return false;
	
}

/**
 * ----------------------------------------------------------------
 * Determine if Reading Pack is selected
 * ----------------------------------------------------------------
 */

function l4k_isReadingPackContext() {

    return strpos($_SERVER['REQUEST_URI'], '/reading-pack/') !== false;

}

/**
 * ----------------------------------------------------------------
 * Check if language is part of the library's reading pack
 * ----------------------------------------------------------------
 */

function l4k_isLanguagePartOfReadingPacks($langID) {

	if (have_rows('language_packs', $_SESSION['library_id'])) :
		while (have_rows('language_packs', $_SESSION['library_id'])) : the_row();
	        if (get_sub_field('language') == $langID) { return true; }
	    endwhile;
	endif;

	return false;

}

/**
 * ----------------------------------------------------------------
 * Add additional email addresses to be notified every new comment
 * These can be set on Main Settings > Comment Notification Emails
 * ----------------------------------------------------------------
 */

function l4k_addExtraCommentNotificationEmails($emails, $comment_id) {

    $emails = [];

	if (have_rows('comment_notification_emails', 'option')) :
		while (have_rows('comment_notification_emails', 'option')) : the_row();
			$emails[] = get_sub_field('email_address');
		endwhile;
	endif;	
    
    return $emails;
}
add_filter('comment_moderation_recipients', 'l4k_addExtraCommentNotificationEmails', 10, 2);

/**
 * ----------------------------------------------------------------
 * Schedule the cron event for 3 months views cleanup
 * ----------------------------------------------------------------
 */

// 1. Add monthly interval
add_filter('cron_schedules', function ($schedules) {
    $schedules['three_months'] = [
        'interval' => 90 * DAY_IN_SECONDS,
        'display'  => __('Once Every 3 Months'),
    ];
    return $schedules;
});

// 2. Schedule event (once)
add_action('init', function(){
    if(!wp_next_scheduled('l4k_reset_recent_views')){
        wp_schedule_event(time(), 'three_months', 'l4k_reset_recent_views');
    }
});

// 3. Cron callback
// add_action('l4k_reset_recent_views', function () {

//     global $wpdb;

//     $limit   = 100;
//     $last_id = 0;
//     $total   = 0;

//     error_log('[ReadingPack] Reset started at ' . current_time('mysql'));

//     while (true) {

//         add_filter('posts_where', function ($where) use ($last_id, $wpdb) {
//             return $where . $wpdb->prepare(" AND {$wpdb->posts}.ID > %d ", $last_id);
//         });

//         $books = get_posts([
//             'post_type'      => 'book',
//             'post_status'    => 'publish',
//             'posts_per_page' => $limit,
//             'fields'         => 'ids',
//             'orderby'        => 'ID',
//             'order'          => 'ASC',
//             'no_found_rows'              => true,
//             'update_post_meta_cache'     => false,
//             'update_post_term_cache'     => false,
//             'suppress_filters'           => false, // IMPORTANT
//         ]);

//         remove_all_filters('posts_where');

//         if (empty($books)) {
//             break;
//         }

//         foreach ($books as $book_id) {
//             update_post_meta(
//                 $book_id,
//                 'additional_details_views_last_3_months',
//                 0
//             );
//             $total++;
//             $last_id = $book_id;
//         }

//         // Free memory between batches
//         wp_cache_flush();

//         error_log('[ReadingPack] Reset progress — ' . $total . ' books');
//     }

//     error_log('[ReadingPack] Reset finished — ' . $total . ' books at ' . current_time('mysql'));
// });
add_action('l4k_reset_recent_views', function () {

    $batch   = 300;
    $offset  = (int) get_option('l4k_reading_pack_offset', 0);
    $total   = (int) get_option('l4k_reading_pack_total', 0);

    error_log('[ReadingPack] Batch start — offset=' . $offset);

    $books = get_posts([
        'post_type'      => 'book',
        'post_status'    => 'publish',
        'posts_per_page' => $batch,
        'offset'         => $offset,
        'fields'         => 'ids',
        'orderby'        => 'ID',
        'order'          => 'ASC',
        'no_found_rows'  => true,
        'update_post_meta_cache' => false,
        'update_post_term_cache' => false,
    ]);

    if (empty($books)) {
        delete_option('l4k_reading_pack_offset');
        delete_option('l4k_reading_pack_total');

        error_log('[ReadingPack] Reset FINISHED — ' . $total . ' books');
        return;
    }

    foreach ($books as $book_id) {
        update_post_meta(
            $book_id,
            'additional_details_views_last_3_months',
            0
        );
        $total++;
    }

    update_option('l4k_reading_pack_offset', $offset + count($books), false);
    update_option('l4k_reading_pack_total', $total, false);

    error_log('[ReadingPack] Batch progress — ' . $total . ' books');

    // schedule next batch
    wp_schedule_single_event(time() + 5, 'l4k_reset_recent_views');
});



//Manual trigger via admin URL

add_action('admin_init', function () {
    if (
        current_user_can('manage_options') &&
        isset($_GET['run_reading_pack_reset'])
    ) {
        do_action('l4k_reset_recent_views');
        add_action('admin_notices', function () {
            echo '<div class="notice notice-success"><p>Reading Pack reset executed.</p></div>';
        });
    }
});

/**
 * ----------------------------------------------------------------
 * Remember session and do not logout when browser is closed
 * ----------------------------------------------------------------
 */

function l4k_rememberLoginCookie() {

    $cookie_data = json_encode([
        'ownerID' 				=> $_SESSION['ownerID'],
        'user_id' 				=> $_SESSION['user_id'],
        'library_id' 			=> $_SESSION['library_id'],
        'library_welcome_logo' 	=> $_SESSION['library_welcome_logo'],
        'library_permalink' 	=> $_SESSION['library_permalink'],
        'library_barcode' 		=> $_SESSION['library_barcode'],
        'library_name' 			=> $_SESSION['library_name'],
        'library_group' 		=> $_SESSION['library_group'],
        'library_region' 		=> $_SESSION['library_region'],
        'library_remember' 		=> $_SESSION['library_remember'],
        'last_viewed_book' 		=> $_SESSION['last_viewed_book'],
        'auth_token' 			=> $_SESSION['auth_token'],
        'auto_login_status' 	=> $_SESSION['auto_login_status']
    ]);	

    setcookie('remember_member', base64_encode($cookie_data), time() + 60*60*24*7, '/', '', false, true ); // cookie expires in 7 days

}

/**
 * ----------------------------------------------------------------
 * To make sure local and staging sources the book's image URL 
 * from lote4kids.com since only PRD has the physical files
 * ----------------------------------------------------------------
 */

function l4k_normalizeImageUrl($url) {

    $local_bases = [
        'http://localhost/lote4kids-new',
        'https://v2.lote4kids.com',
        'http://lote4kids.local',
    ];

    return str_replace($local_bases, 'https://lote4kids.com', $url);
    
}

/**
 * ----------------------------------------------------------------
 * In the blog page, the search will only look for the term in the 
 * post's title and the post's tag, NOT the content
 * ----------------------------------------------------------------
 */

add_filter('posts_search', function($search, $query) {
    if (!is_admin() && $query->is_search() && isset($query->query['s'])) {
        global $wpdb;
        $term = $wpdb->esc_like($query->query['s']);

        $search = " AND (
            {$wpdb->posts}.post_title LIKE '%{$term}%'
            OR {$wpdb->posts}.ID IN (
                SELECT object_id FROM {$wpdb->term_relationships}
                INNER JOIN {$wpdb->terms} ON {$wpdb->term_relationships}.term_taxonomy_id = {$wpdb->terms}.term_id
                WHERE {$wpdb->terms}.name LIKE '%{$term}%'
            )
        )";
    }
    return $search;
}, 10, 2);

/**
 * ----------------------------------------------------------------
 * Generate barcode prefix for teachers
 * if teacher used jeri.ilao@email.com, barcode will be JERI0001
 * if teacher used ben@email.com, barcode will be BENE0002
 * if teacher used pet-email@gmail.com, barcode will be PETE0003
 * if teacher used ab.cdefghk@email.com, barcode will be ABCD0004
 * if teacher used believeinme@gmail.com, barcode will be BELI0005 
 * ----------------------------------------------------------------
 */

function l4k_generateTeacherBarcodePrefix($email) {

    [$localPart, $domain] = explode('@', strtolower($email), 2);
    $cleaned = preg_replace('/[.\-]/', '', $localPart);

    if (strlen($cleaned) < 4) {
        $cleaned .= preg_replace('/[.\-]/', '', explode('.', $domain)[0]);
    }

    return strtoupper(substr($cleaned, 0, 4));
    
}

/**
 * ----------------------------------------------------------------
 * Checklist links like /path/to/foo will become 
 * https://v2.lote4kids.com/path/to/foo if on STG
 * https://lote4kids.com/path/to/foo if on PRD
 * ----------------------------------------------------------------
 */

function l4k_normalizeChecklistLink($url) {

    $base = (defined('WP_ENVIRONMENT_TYPE') && WP_ENVIRONMENT_TYPE === 'PRD') ? 'https://lote4kids.com' : 'https://v2.lote4kids.com';
    $path = preg_replace('#^https?://(v2\.)?lote4kids\.com#', '', $url);

    // split fragment out, then re-append it at the end
    $fragment = '';
    if (preg_match('/^([^#]*)(#[^?]*)(\?.*)?$/', $path, $m)) {
        $path     = $m[1] . ($m[3] ?? '');
        $fragment = $m[2];
    }

    return $base . (str_starts_with($path, '/') ? $path : '/' . $path) . $fragment;
    
}

/**
 * ----------------------------------------------------------------
 * For mobile app's temporary fix
 * ----------------------------------------------------------------
 */

// In functions.php or a custom plugin                                        
  add_action('rest_api_init', function() {                  
    register_rest_route('v2', '/activity/create/user_login_logs', [
      'methods' => 'POST',
      'callback' => function($request) {
        $response = wp_remote_post(
          'https://temp-lote4kids-server.vercel.app/wp-json/v2/activity/create/user_login_logs',
          ['body' => $request->get_body_params()]
        );
        return json_decode(wp_remote_retrieve_body($response), true);
      },
      'permission_callback' => '__return_true',
    ]);
  });

  add_action('init', function() {                                               
    $path = trim($_SERVER['REQUEST_URI'], '/');                                 
    $proxy_routes = [                                                           
      'wp-json/v2/mobile/activity',                                             
      'wp-json/v2/activity/create/story_logs',              
    ];                                                                          
                                                            
    foreach ($proxy_routes as $route) {
      if (strpos($path, $route) === 0 && $_SERVER['REQUEST_METHOD'] === 'POST')
  {
        $response = wp_remote_post(
          'https://temp-lote4kids-server.vercel.app/' . $route . '/',
          [
            'body' => file_get_contents('php://input'),
            'headers' => ['Content-Type' =>
  'application/x-www-form-urlencoded'],
            'timeout' => 30,
          ]
        );
        header('Content-Type: application/json');
        echo wp_remote_retrieve_body($response);
        exit;
      }
    }
  });
?>