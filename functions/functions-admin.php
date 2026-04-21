<?php
/**
 * ----------------------------------------------------------------
 * Add "Main Settings" & "Mobile Settings" menu in CMS
 * ----------------------------------------------------------------
 */

if (function_exists('acf_add_options_page')) {

    acf_add_options_page([
        'page_title'  => 'Main Settings',
        'menu_title'  => 'Main Settings',
        'menu_slug'   => 'main-settings',
        'capability'  => 'manage_options',
        'redirect'    => false,
        'position'    => 2,
        'icon_url'    => 'dashicons-admin-settings'

    ]);

    acf_add_options_sub_page([
        'page_title'  => 'Mobile Settings',
        'menu_title'  => 'Mobile Settings',
        'menu_slug'   => 'mobile-settings',
        'capability'  => 'manage_options',
        'parent_slug' => 'main-settings'
    ]);

}

/**
 * ----------------------------------------------------------------
 * Add "Languages" custom post type and menu in CMS
 * ----------------------------------------------------------------
 */

add_action('init', function () {

    $labels = [
        'name'               => 'Languages',
        'singular_name'      => 'Language',
        'add_new_item'       => 'Add New Language',
        'edit_item'          => 'Edit Language',
        'new_item'           => 'New Language',
        'view_item'          => 'View Language',
        'search_items'       => 'Search Languages',
        'not_found'          => 'No languages found',
        'not_found_in_trash' => 'No languages found in Trash',
    ];

    register_post_type('language', [
        'labels'             => $labels,
        'public'             => true,
        'has_archive'        => true,
        'show_in_menu'       => true,      
        'menu_position'      => 6,         
        'menu_icon'          => 'dashicons-translation',
        'supports'           => ['title', 'editor', 'thumbnail'],
        'rewrite'            => ['slug' => 'languages', 'with_front' => false],
        'capability_type'    => 'post',
    ]);

});

/**
 * ----------------------------------------------------------------
 * Add "Languages" count to the WP CMS sidebar
 * ----------------------------------------------------------------
 */

add_filter('admin_menu', function() {

    global $menu;

    $cpt_slug = 'language';
    $count = wp_count_posts($cpt_slug)->publish;

    foreach ($menu as $key => $value) {
        if ($menu[$key][2] === "edit.php?post_type=$cpt_slug") {
            $menu[$key][0] .= " ($count)";
            break;
        }
    }

});

/**
 * ----------------------------------------------------------------
 * Add "Libraries" custom post type and menu in CMS
 * ----------------------------------------------------------------
 */

add_action('init', function () {

    $labels = [
        'name'               => 'Libraries',
        'singular_name'      => 'Library',
        'add_new_item'       => 'Add New Library',
        'edit_item'          => 'Edit Library',
        'new_item'           => 'New Library',
        'view_item'          => 'View Library',
        'search_items'       => 'Search Libraries',
        'not_found'          => 'No libraries found',
        'not_found_in_trash' => 'No libraries found in Trash',
    ];

    register_post_type('library', [
        'labels'             => $labels,
        'public'             => true,
        'has_archive'        => false,
        'show_in_menu'       => true,
        'menu_position'      => 7,  
        'menu_icon'          => 'dashicons-index-card',
        'supports'           => ['title', 'editor', 'thumbnail'],
		'rewrite'            => false, // Disable default rewrite
        /*'rewrite'            => ['slug' => 'libraries', 'with_front' => false],*/
        'capability_type'    => 'post',
    ]);

});

/**
 * ----------------------------------------------------------------
 * Add "Libraries" count to the WP CMS sidebar
 * ----------------------------------------------------------------
 */

add_filter('admin_menu', function() {

    global $menu;

    $cpt_slug = 'library';
    $count = wp_count_posts($cpt_slug)->publish;

    foreach ($menu as $key => $value) {
        if ($menu[$key][2] === "edit.php?post_type=$cpt_slug") {
            $menu[$key][0] .= " ($count)";
            break;
        }
    }

});

/**
 * ----------------------------------------------------------------
 * Add "View Dashboard" and etc. in WP CMS for Libraries post
 * ----------------------------------------------------------------
 */

add_filter('post_row_actions', function($actions, $post) {

    if ($post->post_type === 'library') 
    {
    	// show link only if the library has a value for dashboard_iframe_source meta
		if (get_field('dashboard_iframe_source', $post->ID)) {
			$actions['view_dashboard'] = '<a href="' . get_permalink($post->ID).'dashboard' . '">View Dashboard</a>';
		}

		// show link only if the library has a value for trial_form meta
		if (get_field('trial_form', $post->ID)) {
        	$actions['view_trial'] = '<a href="' . get_permalink($post->ID).'trial' . '">View Trial Form</a>';
        }

		// show link only if the library has a value for competition_form meta
		if (get_field('competition_form', $post->ID)) {
        	$actions['competition_form'] = '<a href="' . get_permalink($post->ID).'competition' . '">View Competition Form</a>';
        }

		// show link only if the library has a value for training_form meta
		if (get_field('training_form', $post->ID)) {
        	$actions['training_form'] = '<a href="' . get_permalink($post->ID).'webinar' . '">View Webinar Form</a>';
        }

		// show link only if the library has a value for staff-training content meta
		if (get_field('review_content', $post->ID)) {
        	$actions['review_content'] = '<a href="' . get_permalink($post->ID).'staff-training' . '">View Staff Training Page</a>';
        }
    }

    return $actions;

}, 10, 2);

/**
 * ----------------------------------------------------------------
 * Add "Books" custom post type and menu in CMS
 * ----------------------------------------------------------------
 */

add_action('init', function () {

    $labels = [
        'name'               => 'Books',
        'singular_name'      => 'Book',
        'add_new_item'       => 'Add New Book',
        'edit_item'          => 'Edit Book',
        'new_item'           => 'New Book',
        'view_item'          => 'View Book',
        'search_items'       => 'Search Books',
        'not_found'          => 'No books found',
        'not_found_in_trash' => 'No books found in Trash',
    ];

    register_post_type('book', [
        'labels'             => $labels,
        'public'             => true,
        'has_archive'        => false,
        'show_in_menu'       => true,
        'menu_position'      => 8,
        'menu_icon'          => 'dashicons-book',
        'supports'           => ['title', 'editor', 'thumbnail', 'comments'],
        'rewrite'            => ['slug' => 'books', 'with_front' => false],
        'capability_type'    => 'post',
    ]);

});

/**
 * ----------------------------------------------------------------
 * Add "Books" count to the WP CMS sidebar
 * ----------------------------------------------------------------
 */

add_filter('admin_menu', function() {

    global $menu;

    $cpt_slug = 'book';
    $count = wp_count_posts($cpt_slug)->publish;

    foreach ($menu as $key => $value) {
        if ($menu[$key][2] === "edit.php?post_type=$cpt_slug") {
            $menu[$key][0] .= " ($count)";
            break;
        }
    }

});

/**
 * ----------------------------------------------------------------
 * Add "Playlists" custom post type and menu in CMS
 * ----------------------------------------------------------------
 */

add_action('init', function() {
    $labels = [
        'name'                  => 'Playlists',
        'singular_name'         => 'Playlist',
        'menu_name'             => 'Playlists',
        'name_admin_bar'        => 'Playlist',
        'add_new'               => 'Add Playlist',
        'add_new_item'          => 'Add New Playlist',
        'edit_item'             => 'Edit Playlist',
        'new_item'              => 'New Playlist',
        'view_item'             => 'View Playlist',
        'search_items'          => 'Search Playlists',
        'not_found'             => 'No playlists found',
        'not_found_in_trash'    => 'No playlists found in Trash',
        'all_items'             => 'All Playlists',
    ];

    register_post_type('playlist', [
        'labels'             => $labels,
        'public'             => true,
        'has_archive'        => false,
        'show_in_menu'       => true,
        'menu_position'      => 9,
        'menu_icon'          => 'dashicons-playlist-audio',
        'supports'           => ['title', 'editor', 'thumbnail', 'comments'],
        'rewrite'            => ['slug' => 'playlists', 'with_front' => false],
        'capability_type'    => 'post',
    ]);
});


/**
 * ----------------------------------------------------------------
 * Add "Playlists" count to the WP CMS sidebar
 * ----------------------------------------------------------------
 */

add_filter('admin_menu', function() {

    global $menu;

    $cpt_slug = 'playlist';
    $count = wp_count_posts($cpt_slug)->publish;

    foreach ($menu as $key => $value) {
        if ($menu[$key][2] === "edit.php?post_type=$cpt_slug") {
            $menu[$key][0] .= " ($count)";
            break;
        }
    }

});

/**
 * ----------------------------------------------------------------
 * Add "Story" custom post type and menu in CMS
 * ----------------------------------------------------------------
 */

add_action('init', function () {

    $labels = [
        'name'               => 'Stories',
        'singular_name'      => 'Story',
        'add_new_item'       => 'Add New Story',
        'edit_item'          => 'Edit Story',
        'new_item'           => 'New Story',
        'view_item'          => 'View Story',
        'search_items'       => 'Search Stories',
        'not_found'          => 'No stories found',
        'not_found_in_trash' => 'No stories found in Trash',
    ];

    register_post_type('story', [
        'labels'             => $labels,
        'public'             => true,
        'has_archive'        => false,
        'show_in_menu'       => true,
        'menu_position'      => 10,
        'menu_icon'          => 'dashicons-excerpt-view',
        'supports'           => ['title', 'editor', 'thumbnail'],
        'rewrite'            => ['slug' => 'stories', 'with_front' => false],
        'capability_type'    => 'post',
    ]);

});

/**
 * ----------------------------------------------------------------
 * Add "Story" count to the WP CMS sidebar
 * ----------------------------------------------------------------
 */

add_filter('admin_menu', function() {

    global $menu;

    $cpt_slug = 'story';
    $count = wp_count_posts($cpt_slug)->publish;

    foreach ($menu as $key => $value) {
        if ($menu[$key][2] === "edit.php?post_type=$cpt_slug") {
            $menu[$key][0] .= " ($count)";
            break;
        }
    }

});

/**
 * ----------------------------------------------------------------
 * Add "Activities" custom post type and menu in CMS
 * ----------------------------------------------------------------
 */

add_action('init', function () {

    $labels = [
        'name'               => 'Activities',
        'singular_name'      => 'Activity',
        'add_new_item'       => 'Add New Activity',
        'edit_item'          => 'Edit Activity',
        'new_item'           => 'New Activity',
        'view_item'          => 'View Activity',
        'search_items'       => 'Search Activities',
        'not_found'          => 'No activities found',
        'not_found_in_trash' => 'No activities found in Trash',
    ];

    register_post_type('activity', [
        'labels'             => $labels,
        'public'             => true,
        'has_archive'        => false,
        'show_in_menu'       => true,
        'menu_position'      => 11,
        'menu_icon'          => 'dashicons-welcome-write-blog',
        'supports'           => ['title', 'editor', 'thumbnail'],
        'rewrite'            => ['slug' => 'activities', 'with_front' => false],
        'capability_type'    => 'post',
    ]);

});

/**
 * ----------------------------------------------------------------
 * Add "Activities" count to the WP CMS sidebar
 * ----------------------------------------------------------------
 */

add_filter('admin_menu', function() {

    global $menu;

    $cpt_slug = 'activity';
    $count = wp_count_posts($cpt_slug)->publish;

    foreach ($menu as $key => $value) {
        if ($menu[$key][2] === "edit.php?post_type=$cpt_slug") {
            $menu[$key][0] .= " ($count)";
            break;
        }
    }

});

/**
 * ----------------------------------------------------------------
 * Add "Endpoints" custom post type and menu in CMS
 * ----------------------------------------------------------------
 */

add_action('init', function () {

    $labels = [
        'name'               => 'Endpoints',
        'singular_name'      => 'Endpoint',
        'add_new_item'       => 'Add New Endpoint',
        'edit_item'          => 'Edit Endpoint',
        'new_item'           => 'New Endpoint',
        'view_item'          => 'View Endpoint',
        'search_items'       => 'Search Endpoints',
        'not_found'          => 'No endpoints found',
        'not_found_in_trash' => 'No endpoints found in Trash',
    ];

    register_post_type('endpoint', [
        'labels'             => $labels,
        'public'             => true,
        'has_archive'        => false,
        'show_in_menu'       => true,
        'menu_position'      => 12,
        'menu_icon'          => 'dashicons-rest-api',
        'supports'           => ['title', 'editor', 'thumbnail'],
        'rewrite'            => ['slug' => 'endpoints', 'with_front' => false],
        'capability_type'    => 'post',
    ]);
});

/**
 * ----------------------------------------------------------------
 * Add "Endpoints" count to the WP CMS sidebar
 * ----------------------------------------------------------------
 */

add_filter('admin_menu', function() {

    global $menu;

    $cpt_slug = 'endpoint';
    $count = wp_count_posts($cpt_slug)->publish;

    foreach ($menu as $key => $value) {
        if ($menu[$key][2] === "edit.php?post_type=$cpt_slug") {
            $menu[$key][0] .= " ($count)";
            break;
        }
    }

});

/**
 * ----------------------------------------------------------------
 * Move "Media" from position 10 to 15
 * ----------------------------------------------------------------
 */

add_action('admin_menu', function () {

    global $menu;
    $menu[15] = $menu[10]; // Move Media from position 10 to 15
    unset($menu[10]);

}, 999);

/**
 * ----------------------------------------------------------------
 * Change "Posts" label to "Blog" in CMS
 * ----------------------------------------------------------------
 */

add_action('admin_menu', function() {

    global $menu;
    foreach ($menu as $key => $value) {
        if ($value[2] === 'edit.php') { // Posts menu slug
            $menu[$key][0] = 'Blog';   // rename it
            break;
        }
    }

});

/**
 * ----------------------------------------------------------------
 * Allow SVG upload and make sure to sanitize the SVG
 * ----------------------------------------------------------------
 */

add_filter('upload_mimes', function($mimes){

    $mimes['svg'] = 'image/svg+xml';
    return $mimes;

});

/**
 * ----------------------------------------------------------------
 * Color code ACF for flipbook vs. video type
 * ----------------------------------------------------------------
 */

add_action('acf/input/admin_head', function() {

    ?>
    <style>
        .bg-flipbook { background: #f2faff !important; }
        .bg-video { background: #f3fef6 !important; }
        .bg-trial { background: #FEF3F3 !important; }
    </style>
    <?php

});

/**
 * ----------------------------------------------------------------
 * Disable WP admin bar on top of the page for "subscriber"
 * ----------------------------------------------------------------
 */

function l4k_HideAdminBarForSubscribers() {

    if (is_user_logged_in()) {
        $user = wp_get_current_user();
        if (in_array( 'subscriber', (array) $user->roles)) {
            show_admin_bar(false);
        }
    }

}
add_action('after_setup_theme', 'l4k_HideAdminBarForSubscribers');

/**
 * ----------------------------------------------------------------
 * Change login logo and link in WP CMS
 * ----------------------------------------------------------------
 */

function l4k_customLoginLogo() {
    ?>
    <style type="text/css">
        #login h1 a {
            background-image: url('<?php echo get_stylesheet_directory_uri(); ?>/assets/img/logo-main.svg');
            background-size: contain;
            width: 100%;
            height: 80px;
        }
    </style>
    <?php
}
add_action('login_enqueue_scripts', 'l4k_customLoginLogo');

function l4k_customLoginLogoURL() { return home_url(); }
add_filter('login_headerurl', 'l4k_customLoginLogoURL');

function l4k_customLoginLogoTitle() { return get_bloginfo('name'); }
add_filter('login_headertext', 'l4k_customLoginLogoTitle');

/**
 * ----------------------------------------------------------------
 * Clear all transients when any CPT is published or updated
 * ----------------------------------------------------------------
 */

function l4k_clearAllTransients($post_id, $post, $update) {
    
    if (defined( 'DOING_AUTOSAVE') && DOING_AUTOSAVE ) { return; } // avoid running on autosave
    if ($post->post_type === 'post' || $post->post_type === 'page') { return; } // check if it's a CPT (not a standard post or page)
    if ($post->post_status !== 'publish') { return; } // only run on publish status
    
    global $wpdb;
  
    $wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_%'" ); // delete regular transients    
    $wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_%'" ); // delete transient timeouts
    
    wp_cache_flush(); // clear object cache if enabled
}

add_action('save_post', 'l4k_clearAllTransients', 10, 3);

/**
 * ----------------------------------------------------------------
 * Custom admin favicon
 * ----------------------------------------------------------------
 */

function l4k_customAdminFavicon() {
    echo '<link rel="shortcut icon" href="' . get_stylesheet_directory_uri() . '/assets/img/favicon.png" type="image/x-icon">';
}

add_action('admin_head', 'l4k_customAdminFavicon');

/**
 * ----------------------------------------------------------------
 * Register "Download Report" under Tools menu
 * ----------------------------------------------------------------
 */

add_action( 'admin_menu', function () {
    add_management_page(
        'Download Report', 'Download Report',
        'manage_options', 'wda-download-report',
        'wda_render_page'
    );
} );

add_action( 'admin_init', function () {
    if ( ! isset( $_POST['wda_export_nonce'] ) ) return;
    if ( ! current_user_can( 'manage_options' ) ) return;
    if ( ! wp_verify_nonce( $_POST['wda_export_nonce'], 'wda_export_action' ) ) return;

    global $wpdb;
    $type = isset( $_POST['wda_submit_web'] ) ? 'web' : ( isset( $_POST['wda_submit_mobile'] ) ? 'mobile' : null );
    if ( ! $type ) return;

    $from = sanitize_text_field( $_POST["wda_from_{$type}"] ?? '' );
    $to   = sanitize_text_field( $_POST["wda_to_{$type}"]   ?? '' );
    if ( ! $from || ! $to || $from > $to ) return;

    $table   = $type === 'web' ? "{$wpdb->prefix}web_activity" : "{$wpdb->prefix}mobile_activity";
    $results = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT * FROM {$table} WHERE DATE(time) BETWEEN %s AND %s ORDER BY time ASC",
            $from, $to
        ),
        ARRAY_A
    );

    // --- Append user_login_logs rows for mobile only ---
    if ( $type === 'mobile' ) {
        $login_logs = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT barcode, alert_code, library_group, region, language, time, os_type, device_type, status
                 FROM {$wpdb->prefix}user_login_logs
                 WHERE DATE(time) BETWEEN %s AND %s
                 AND alert_code = %s
                 ORDER BY time ASC",
                $from, $to, '800'
            ),
            ARRAY_A
        );

        foreach ( $login_logs as $log ) {
            $results[] = [
                'alert_code'   => $log['alert_code'],
                'barcode'      => $log['barcode'],
                'library_name' => $log['library_group'],
                'region_name'  => $log['region'],
                'os_type'      => $log['os_type'],
                'device_type'  => $log['device_type'],
                'time'         => $log['time'],
                'data'         => json_encode( [
                    'Language'      => $log['language'],
                ] ),
                '_source'      => 'login_log', // internal flag, not output
            ];
        }
    }

    if ( empty( $results ) ) return;

    header( 'Content-Type: text/csv; charset=utf-8' );
    header( "Content-Disposition: attachment; filename=\"{$type}-activity-{$from}-to-{$to}.csv\"" );
    header( 'Pragma: no-cache' );

    $out = fopen( 'php://output', 'w' );
    fputcsv( $out, [
        'Alert Code', 'Barcode', 'Library Group', 'Region', 'Post ID',
        'Post Title', 'Language', 'Type', 'Activity Name', 'Activity Title',
        'Activity Type', 'IP', 'OS Type', 'Device Type', 'Time', 'User Agent',
    ] );

    foreach ( $results as $r ) {
        $d = json_decode( stripslashes( $r['data'] ?? '' ) );
        if ( json_last_error() !== JSON_ERROR_NONE ) {
            $d = (object) [];
        }

        $activity_name  = $d->{'Activity Name'}  ?? '';
        $activity_title = $d->{'Activity Title'} ?? '';
        $activity_type  = $d->{'Activity Type'}  ?? '';

        // Mobile-only: handle {"Viewed":"<screen>"} shaped data
        if ( $type === 'mobile' && isset( $d->{'Viewed'} ) ) {
            $activity_type = 'Viewed';
            $activity_name = $d->{'Viewed'};
        }

        // Populate User Agent only for alert_code 900
        $user_agent = ( $r['alert_code'] === '900' ) ? ( $d->{'user-agent'} ?? '' ) : '';

        fputcsv( $out, [
            $r['alert_code'],
            '="' . $r['barcode'] . '"',
            $r['library_name'],
            $r['region_name'],
            $d->{'Story ID'}       ?? '',
            html_entity_decode( $d->{'Story Title'} ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8' ),
            $d->{'Language'}       ?? '',
            $d->{'Type'}           ?? '',
            $activity_name,
            $activity_title,
            $activity_type,
            $r['ip']               ?? '',
            $type === 'web' ? 'Desktop' : $r['os_type'],
            $type === 'web' ? 'Desktop' : $r['device_type'],
            date( 'F j, Y g:i:s A', strtotime( $r['time'] ) ),
            $user_agent,
        ] );
    }

    fclose( $out );
    exit;
} );

function wda_render_page() {
    if ( ! current_user_can( 'manage_options' ) ) return;

    $web_from    = sanitize_text_field( $_POST['wda_from_web']    ?? '' );
    $web_to      = sanitize_text_field( $_POST['wda_to_web']      ?? '' );
    $mobile_from = sanitize_text_field( $_POST['wda_from_mobile'] ?? '' );
    $mobile_to   = sanitize_text_field( $_POST['wda_to_mobile']   ?? '' );

    $error      = '';
    $no_results = false;

    if ( isset( $_POST['wda_export_nonce'] ) ) {
        if ( ! wp_verify_nonce( $_POST['wda_export_nonce'], 'wda_export_action' ) ) {
            $error = 'Security check failed.';
        } elseif ( isset( $_POST['wda_submit_web'] ) && ( ! $web_from || ! $web_to ) ) {
            $error = 'Please select both a From and a To date for Web Activity.';
        } elseif ( isset( $_POST['wda_submit_web'] ) && $web_from > $web_to ) {
            $error = '"From" date cannot be later than "To" date for Web Activity.';
        } elseif ( isset( $_POST['wda_submit_mobile'] ) && ( ! $mobile_from || ! $mobile_to ) ) {
            $error = 'Please select both a From and a To date for Mobile Activity.';
        } elseif ( isset( $_POST['wda_submit_mobile'] ) && $mobile_from > $mobile_to ) {
            $error = '"From" date cannot be later than "To" date for Mobile Activity.';
        } else {
            $no_results = true;
        }
    }
    ?>
    <div class="wrap">
        <h1>Download Report</h1>
        <p>Select a date range for each activity type and download as a CSV file.</p>

        <?php if ( $error ) : ?>
            <div class="notice notice-error"><p><?php echo esc_html( $error ); ?></p></div>
        <?php elseif ( $no_results ) : ?>
            <div class="notice notice-warning"><p>No records found for the selected date range.</p></div>
        <?php endif; ?>

        <form method="post">
            <?php wp_nonce_field( 'wda_export_action', 'wda_export_nonce' ); ?>

            <div style="display:flex; align-items:center; gap:12px; margin-top:16px;">
                <strong style="min-width:110px;">Web Activity</strong>
                <label>From <input type="date" name="wda_from_web" value="<?php echo esc_attr( $web_from ); ?>"></label>
                <label>To <input type="date" name="wda_to_web" value="<?php echo esc_attr( $web_to ); ?>"></label>
                <input type="submit" name="wda_submit_web" class="button button-primary" value="Download Web Activity">
            </div>

            <div style="display:flex; align-items:center; gap:12px; margin-top:12px;">
                <strong style="min-width:110px;">Mobile Activity</strong>
                <label>From <input type="date" name="wda_from_mobile" value="<?php echo esc_attr( $mobile_from ); ?>"></label>
                <label>To <input type="date" name="wda_to_mobile" value="<?php echo esc_attr( $mobile_to ); ?>"></label>
                <input type="submit" name="wda_submit_mobile" class="button button-primary" value="Download Mobile Activity">
            </div>

        </form>
    </div>
    <?php
}

/**
 * ----------------------------------------------------------------
 * Register "Airtable Sync" under Tools menu
 * ----------------------------------------------------------------
 */

add_action('admin_menu', function() {
    add_submenu_page(
        'tools.php',
        'Airtable Sync',
        'Airtable Sync',
        'manage_options',
        'l4k-airtable-settings',
        'l4k_airtable_settings_page'
    );
});


    add_action('admin_init', function() {
    register_setting('l4k_airtable_group', 'l4k_airtable_token');
    register_setting('l4k_airtable_group', 'l4k_airtable_fail_email');
    register_setting('l4k_airtable_group', 'l4k_airtable_fail_bcc');
    register_setting('l4k_airtable_group', 'l4k_airtable_mappings');

    // Action: Delete Mapping
    if (isset($_POST['l4k_delete_mapping']) && check_admin_referer('l4k_delete_action', 'l4k_delete_nonce')) {
        $form_to_delete = intval($_POST['l4k_delete_mapping']);
        $all_mappings   = get_option('l4k_airtable_mappings', []);
        if (isset($all_mappings[$form_to_delete])) {
            unset($all_mappings[$form_to_delete]);
            update_option('l4k_airtable_mappings', $all_mappings);
        }
        wp_redirect(admin_url('tools.php?page=l4k-airtable-settings&deleted=1'));
        exit;
    }

    // Action: Save Global Config (Token, Email, BCC)
    if (isset($_POST['l4k_save_global_settings']) && check_admin_referer('l4k_global_action', 'l4k_global_nonce')) {
        update_option('l4k_airtable_token', sanitize_text_field($_POST['l4k_airtable_token']));
        update_option('l4k_airtable_fail_email', sanitize_email($_POST['l4k_airtable_fail_email']));
        update_option('l4k_airtable_fail_bcc', sanitize_text_field($_POST['l4k_airtable_fail_bcc']));
        wp_redirect(admin_url('tools.php?page=l4k-airtable-settings&settings_saved=1'));
        exit;
    }

    // Action: Save Specific Form Mapping
    if (isset($_POST['l4k_save_mapping']) && check_admin_referer('l4k_save_mapping_action', 'l4k_save_mapping_nonce')) {
        $form_id    = intval($_POST['l4k_form_id']);
        $base_id    = sanitize_text_field($_POST['l4k_base_id']);
        $table_id   = sanitize_text_field($_POST['l4k_table_id']);
        $table_name = sanitize_text_field($_POST['l4k_table_name']); 
        $fields     = isset($_POST['l4k_fields']) && is_array($_POST['l4k_fields']) ? array_map('sanitize_text_field', $_POST['l4k_fields']) : [];

        if ($form_id && $base_id && $table_id) {
            $all_mappings            = get_option('l4k_airtable_mappings', []);
            $all_mappings[$form_id]  = [
                'base_id'    => $base_id,
                'table_id'   => $table_id,
                'table_name' => $table_name, 
                'fields'     => $fields,
            ];
            update_option('l4k_airtable_mappings', $all_mappings);
        }
        wp_redirect(admin_url('tools.php?page=l4k-airtable-settings&saved=1'));
        exit;
    }
});

function l4k_get_airtable_bases() {
    $token = get_option('l4k_airtable_token');
    if (!$token) return [];
    $response = wp_remote_get('https://api.airtable.com/v0/meta/bases', [
        'headers' => ['Authorization' => 'Bearer ' . $token]
    ]);
    $body = json_decode(wp_remote_retrieve_body($response), true);
    return $body['bases'] ?? [];
}

function l4k_get_airtable_tables($base_id) {
    $token = get_option('l4k_airtable_token');
    if (!$token || !$base_id) return [];
    $response = wp_remote_get("https://api.airtable.com/v0/meta/bases/{$base_id}/tables", [
        'headers' => ['Authorization' => 'Bearer ' . $token]
    ]);
    $body = json_decode(wp_remote_retrieve_body($response), true);
    return $body['tables'] ?? [];
}

function l4k_get_all_wpforms() {
    return get_posts(['post_type' => 'wpforms', 'posts_per_page' => -1]);
}

function l4k_airtable_settings_page() {
    $token        = get_option('l4k_airtable_token');
    $fail_email   = get_option('l4k_airtable_fail_email', get_option('admin_email'));
    $fail_bcc     = get_option('l4k_airtable_fail_bcc');
    $all_mappings = get_option('l4k_airtable_mappings', []);

    $form_id  = isset($_GET['edit_form']) ? intval($_GET['edit_form']) : 0;
    $base_id  = isset($_GET['base_id'])  ? sanitize_text_field($_GET['base_id'])  : '';
    $table_id = isset($_GET['table_id']) ? sanitize_text_field($_GET['table_id']) : '';

    if ($form_id && isset($all_mappings[$form_id])) {
        $saved = $all_mappings[$form_id];
        if (!$base_id)  $base_id  = $saved['base_id']  ?? '';
        if (!$table_id) $table_id = $saved['table_id'] ?? '';
    }

    $page_url = admin_url('tools.php?page=l4k-airtable-settings');
    ?>
    <div class="wrap">
        <h1>Airtable Multi-Form Sync Manager</h1>

        <?php if (isset($_GET['saved'])) echo '<div class="notice notice-success is-dismissible"><p>Mapping saved.</p></div>'; ?>
        <?php if (isset($_GET['settings_saved'])) echo '<div class="notice notice-success is-dismissible"><p>Global settings updated.</p></div>'; ?>
        <?php if (isset($_GET['deleted'])) echo '<div class="notice notice-warning is-dismissible"><p>Mapping deleted.</p></div>'; ?>

        <div class="card" style="max-width: 100%; margin-top: 20px;">
            <h2>1. Global Notification & Token</h2>
            <form method="post" action="">
                <?php wp_nonce_field('l4k_global_action', 'l4k_global_nonce'); ?>
                <table class="form-table">
                    <tr>
                        <th scope="row">Airtable Token</th>
                        <td><input type="password" name="l4k_airtable_token" value="<?php echo esc_attr($token); ?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th scope="row">Fail Notification Email</th>
                        <td><input type="email" name="l4k_airtable_fail_email" value="<?php echo esc_attr($fail_email); ?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th scope="row">Fail BCC Recipients</th>
                        <td><input type="text" name="l4k_airtable_fail_bcc" value="<?php echo esc_attr($fail_bcc); ?>" class="regular-text" placeholder="dev@example.com, logs@example.com"></td>
                    </tr>
                </table>
                <input type="hidden" name="l4k_save_global_settings" value="1">
                <?php submit_button('Save Configuration', 'secondary'); ?>
            </form>
        </div>

        <h2>Active Connections</h2>
        <table class="wp-list-table widefat fixed striped" style="margin-bottom: 30px;">
            <thead>
                <tr>
                    <th>WPForm Name</th>
                    <th>Airtable Table Name</th>
                    <th>Mapped Fields</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($all_mappings)) : ?>
                    <tr><td colspan="4">No mappings found. Create one below.</td></tr>
                <?php else : foreach ($all_mappings as $m_id => $m_data) : ?>
                    <tr>
                        <td><strong><?php echo esc_html(get_the_title($m_id)); ?></strong></td>
                        <td><?php echo esc_html($m_data['table_name'] ?? $m_data['table_id']); ?></td>
                        <td><?php echo count($m_data['fields'] ?? []); ?> fields</td>
                        <td>
                            <a href="<?php echo esc_url(add_query_arg(['edit_form' => $m_id], $page_url)); ?>" class="button button-small">Edit</a>
                            <form method="post" action="" style="display:inline;" onsubmit="return confirm('Delete mapping?');">
                                <?php wp_nonce_field('l4k_delete_action', 'l4k_delete_nonce'); ?>
                                <input type="hidden" name="l4k_delete_mapping" value="<?php echo $m_id; ?>">
                                <button type="submit" class="button button-small" style="color:red;">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>

        <div class="card" style="max-width: 100%; padding: 20px; background: #fff; border: 1px solid #ccd0d4;">
            <h2><?php echo $form_id ? 'Edit Mapping' : 'Create New Mapping'; ?></h2>
            
            <form method="get" action="admin.php">
                <input type="hidden" name="page" value="l4k-airtable-settings">
                <table class="form-table">
                    <tr>
                        <th scope="row">Select WPForm</th>
                        <td>
                            <select name="edit_form" onchange="this.form.submit()">
                                <option value="">-- Choose a Form --</option>
                                <?php foreach (l4k_get_all_wpforms() as $form) : ?>
                                    <option value="<?php echo $form->ID; ?>" <?php selected($form_id, $form->ID); ?>><?php echo esc_html($form->post_title); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                </table>
            </form>

            <?php if ($form_id && $token) : $bases = l4k_get_airtable_bases(); ?>
                <form method="get" action="admin.php">
                    <input type="hidden" name="page" value="l4k-airtable-settings">
                    <input type="hidden" name="edit_form" value="<?php echo $form_id; ?>">
                    <table class="form-table">
                        <tr>
                            <th scope="row">Select Base</th>
                            <td>
                                <select name="base_id" onchange="this.form.submit()">
                                    <option value="">-- Select Base --</option>
                                    <?php foreach ($bases as $base) : ?>
                                        <option value="<?php echo esc_attr($base['id']); ?>" <?php selected($base_id, $base['id']); ?>><?php echo esc_html($base['name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                        </tr>
                    </table>
                </form>
            <?php endif; ?>

            <?php if ($base_id) : $tables = l4k_get_airtable_tables($base_id); ?>
                <form method="get" action="admin.php">
                    <input type="hidden" name="page" value="l4k-airtable-settings">
                    <input type="hidden" name="edit_form" value="<?php echo $form_id; ?>">
                    <input type="hidden" name="base_id" value="<?php echo esc_attr($base_id); ?>">
                    <table class="form-table">
                        <tr>
                            <th scope="row">Select Table</th>
                            <td>
                                <select id="l4k_table_select" name="table_id" onchange="this.form.submit()">
                                    <option value="">-- Select Table --</option>
                                    <?php foreach ($tables as $table) : ?>
                                        <option value="<?php echo esc_attr($table['id']); ?>" 
                                                data-name="<?php echo esc_attr($table['name']); ?>"
                                                <?php selected($table_id, $table['id']); ?>>
                                            <?php echo esc_html($table['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                        </tr>
                    </table>
                </form>
            <?php endif; ?>

            <?php if ($table_id) : 
                $columns = [];
                foreach ($tables as $t) { if ($t['id'] === $table_id) { $columns = $t['fields']; break; } }
                $form_data = wpforms()->form->get($form_id);
                $wp_fields = !empty($form_data->post_content) ? json_decode($form_data->post_content, true)['fields'] : [];
                $field_map = $all_mappings[$form_id]['fields'] ?? [];
            ?>
                <form method="post" action="">
                    <?php wp_nonce_field('l4k_save_mapping_action', 'l4k_save_mapping_nonce'); ?>
                    <input type="hidden" name="l4k_save_mapping" value="1">
                    <input type="hidden" name="l4k_form_id"  value="<?php echo $form_id; ?>">
                    <input type="hidden" name="l4k_base_id"  value="<?php echo esc_attr($base_id); ?>">
                    <input type="hidden" name="l4k_table_id" value="<?php echo esc_attr($table_id); ?>">
                    <input type="hidden" id="l4k_table_name_hidden" name="l4k_table_name" value="">

                    <h3>Map Columns</h3>
                    <table class="widefat fixed striped">
                        <thead><tr><th>Airtable Column</th><th>WPForm Field</th></tr></thead>
                        <tbody>
                            <?php foreach ($columns as $col) : 
                                $col_name = $col['name'];
                                $selected_wp = $field_map[$col_name] ?? '';
                            ?>
                            <tr>
                                <td><strong><?php echo esc_html($col_name); ?></strong></td>
                                <td>
                                    <select name="l4k_fields[<?php echo esc_attr($col_name); ?>]">
                                        <option value="">-- Don't Map --</option>
                                        <?php foreach ($wp_fields as $fid => $fval) : ?>
                                            <option value="<?php echo $fid; ?>" <?php selected($selected_wp, $fid); ?>><?php echo esc_html($fval['label']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php submit_button('Save Mapping', 'primary'); ?>
                </form>

                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    var sel = document.getElementById('l4k_table_select');
                    var hid = document.getElementById('l4k_table_name_hidden');
                    if(sel && hid) { hid.value = sel.options[sel.selectedIndex].getAttribute('data-name'); }
                });
                </script>
            <?php endif; ?>
        </div>
    </div>
    <?php
}

/**
 * ----------------------------------------------------------------
 * Books breakdown report
 * ----------------------------------------------------------------
 */

// --- Register the submenu page under the Books CPT ---
add_action( 'admin_menu', function () {
    add_submenu_page(
        'edit.php?post_type=book',   	// parent: Books CPT menu
        'Books Summary',          		// page <title>
        'Books Summary',          		// menu label
        'edit_posts',                	// capability required
        'books-breakdown-report',    	// menu slug
        'books_breakdown_report_cb'  	// callback
    );
} );


// --- CSV download handler (runs before any output) ---
add_action( 'admin_init', function () {
    if (
        ! isset( $_GET['page'], $_GET['action'] ) ||
        $_GET['page']   !== 'books-breakdown-report' ||
        $_GET['action'] !== 'download-csv'
    ) {
        return;
    }

    if ( ! current_user_can( 'edit_posts' ) ) {
        wp_die( 'Insufficient permissions.' );
    }

    global $wpdb;

    $book_types = [ 'video_monolingual', 'video_bilingual', 'video_english', 'flipbook' ];

    $type_labels = [
        'video_monolingual' => 'Monolingual',
        'video_bilingual'   => 'Bilingual',
        'video_english'     => 'English',
        'flipbook'          => 'Flipbook',
    ];

    $raw = $wpdb->get_results("
        SELECT
            pm_lang.meta_value                                      AS language_id,
            COALESCE( NULLIF( pm_type.meta_value, '' ), '(none)' ) AS book_type,
            COUNT( p.ID )                                           AS total
        FROM {$wpdb->posts} p
        LEFT JOIN {$wpdb->postmeta} pm_lang
            ON p.ID = pm_lang.post_id
            AND pm_lang.meta_key = 'language'
        LEFT JOIN {$wpdb->postmeta} pm_type
            ON p.ID = pm_type.post_id
            AND pm_type.meta_key = 'book_type'
        WHERE p.post_type    = 'book'
          AND p.post_status  = 'publish'
          AND pm_type.meta_value IN ('video_monolingual','video_bilingual','video_english','flipbook')
        GROUP BY pm_lang.meta_value, pm_type.meta_value
        ORDER BY pm_lang.meta_value, total DESC
    ");

    $total_books = (int) $wpdb->get_var("
        SELECT COUNT( ID ) FROM {$wpdb->posts}
        WHERE post_type = 'book' AND post_status = 'publish'
    ");

    $dl_raw = $wpdb->get_results("
        SELECT
            pm_lang.meta_value  AS language_id,
            pm_type.meta_value  AS book_type,
            pm_pdf.meta_value   AS pdf_value
        FROM {$wpdb->posts} p
        LEFT JOIN {$wpdb->postmeta} pm_lang
            ON p.ID = pm_lang.post_id
            AND pm_lang.meta_key = 'language'
        LEFT JOIN {$wpdb->postmeta} pm_type
            ON p.ID = pm_type.post_id
            AND pm_type.meta_key = 'book_type'
        INNER JOIN {$wpdb->postmeta} pm_pdf
            ON p.ID = pm_pdf.post_id
            AND pm_pdf.meta_key REGEXP '^download_links_[0-9]+_pdf$'
        WHERE p.post_type   = 'book'
          AND p.post_status = 'publish'
    ");

    $dl_counts = [];
    foreach ( $dl_raw as $row ) {
        $lid = $row->language_id ?: '';
        $bt  = $row->book_type   ?: '';
        if ( ! isset( $dl_counts[ $lid ] ) ) {
            $dl_counts[ $lid ] = [
                'quiz'     => 0,
                'activity' => 0,
                'types'    => array_fill_keys( $book_types, [ 'quiz' => 0, 'activity' => 0 ] ),
            ];
        }
        $is_quiz = str_contains( $row->pdf_value, '<iframe' );
        $key     = $is_quiz ? 'quiz' : 'activity';
        $dl_counts[ $lid ][ $key ]++;
        if ( in_array( $bt, $book_types ) ) {
            $dl_counts[ $lid ]['types'][ $bt ][ $key ]++;
        }
    }

    $empty_type_dl = array_fill_keys( $book_types, [ 'quiz' => 0, 'activity' => 0 ] );

    $all_languages = get_posts( [
        'post_type'      => 'language',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'fields'         => 'ids',
    ] );

    $languages = [];
    foreach ( $all_languages as $lid ) {
        $languages[ (string) $lid ] = [
            'total'    => 0,
            'quiz'     => 0,
            'activity' => 0,
            'types'    => array_fill_keys( $book_types, 0 ),
            'type_dl'  => $empty_type_dl,
        ];
    }

    foreach ( $raw as $row ) {
        $lid = $row->language_id ?: '';
        if ( ! isset( $languages[ $lid ] ) ) {
            $languages[ $lid ] = [
                'total'    => 0,
                'quiz'     => 0,
                'activity' => 0,
                'types'    => array_fill_keys( $book_types, 0 ),
                'type_dl'  => $empty_type_dl,
            ];
        }
        $languages[ $lid ]['total'] += (int) $row->total;
        if ( in_array( $row->book_type, $book_types ) ) {
            $languages[ $lid ]['types'][ $row->book_type ] += (int) $row->total;
        }
    }

    foreach ( $dl_counts as $lid => $counts ) {
        if ( ! isset( $languages[ $lid ] ) ) {
            $languages[ $lid ] = [
                'total'    => 0,
                'quiz'     => 0,
                'activity' => 0,
                'types'    => array_fill_keys( $book_types, 0 ),
                'type_dl'  => $empty_type_dl,
            ];
        }
        $languages[ $lid ]['quiz']     += $counts['quiz'];
        $languages[ $lid ]['activity'] += $counts['activity'];
        foreach ( $book_types as $bt ) {
            $languages[ $lid ]['type_dl'][ $bt ]['quiz']     += $counts['types'][ $bt ]['quiz'];
            $languages[ $lid ]['type_dl'][ $bt ]['activity'] += $counts['types'][ $bt ]['activity'];
        }
    }

    uasort( $languages, fn( $a, $b ) => $b['total'] - $a['total'] );

    $resolved = [];
    foreach ( $languages as $lid => $data ) {
        $title      = is_numeric( $lid ) ? get_the_title( (int) $lid ) : '';
        $resolved[] = [
            'id'       => $lid,
            'title'    => $title ?: $lid,
            'total'    => $data['total'],
            'quiz'     => $data['quiz'],
            'activity' => $data['activity'],
            'types'    => $data['types'],
            'type_dl'  => $data['type_dl'],
        ];
    }

    $pct = fn( $part, $whole ) => $whole > 0 ? round( $part / $whole * 100, 1 ) : 0;

    // --- Build CSV ---
    $grand_quiz     = array_sum( array_column( $dl_counts, 'quiz' ) );
    $grand_activity = array_sum( array_column( $dl_counts, 'activity' ) );

    $grand_type_dl = array_fill_keys( $book_types, [ 'quiz' => 0, 'activity' => 0 ] );
    foreach ( $dl_counts as $data ) {
        foreach ( $book_types as $bt ) {
            $grand_type_dl[ $bt ]['quiz']     += $data['types'][ $bt ]['quiz'];
            $grand_type_dl[ $bt ]['activity'] += $data['types'][ $bt ]['activity'];
        }
    }

    $csv_escape = function ( $val ) {
        $val = (string) $val;
        if ( str_contains( $val, ',' ) || str_contains( $val, '"' ) || str_contains( $val, "\n" ) ) {
            $val = '"' . str_replace( '"', '""', $val ) . '"';
        }
        return $val;
    };

    $rows = [];

    // Header row 1
    $h1 = [ 'Language', 'Total', '%', 'Act', 'Quiz' ];
    foreach ( $type_labels as $label ) {
        $h1[] = $label . ' Count';
        $h1[] = $label . ' %';
        $h1[] = $label . ' Act';
        $h1[] = $label . ' Quiz';
    }
    $rows[] = implode( ',', array_map( $csv_escape, $h1 ) );

    // Totals row
    $type_totals = [];
    foreach ( $book_types as $bt ) {
        $type_totals[ $bt ] = array_sum( array_column( array_map( fn( $r ) => $r['types'], $resolved ), $bt ) );
    }

    $totals_row = [ 'Total', $total_books, '100%', $grand_activity, $grand_quiz ];
    foreach ( $book_types as $bt ) {
        $totals_row[] = $type_totals[ $bt ];
        $totals_row[] = $pct( $type_totals[ $bt ], $total_books ) . '%';
        $totals_row[] = $grand_type_dl[ $bt ]['activity'];
        $totals_row[] = $grand_type_dl[ $bt ]['quiz'];
    }
    $rows[] = implode( ',', array_map( $csv_escape, $totals_row ) );

    // Data rows
    foreach ( $resolved as $r ) {
        $lang_total = $r['total'];
        $data_row   = [
            $r['title'],
            $lang_total,
            $pct( $lang_total, $total_books ) . '%',
            $r['activity'],
            $r['quiz'],
        ];
        foreach ( $book_types as $bt ) {
            $count      = $r['types'][ $bt ]               ?? 0;
            $data_row[] = $count;
            $data_row[] = $pct( $count, $lang_total ) . '%';
            $data_row[] = $r['type_dl'][ $bt ]['activity'] ?? 0;
            $data_row[] = $r['type_dl'][ $bt ]['quiz']     ?? 0;
        }
        $rows[] = implode( ',', array_map( $csv_escape, $data_row ) );
    }

    $filename = 'books-breakdown-' . current_time( 'Y-m-d' ) . '.csv';

    header( 'Content-Type: text/csv; charset=utf-8' );
    header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
    header( 'Pragma: no-cache' );
    header( 'Expires: 0' );

    // BOM for Excel UTF-8 compatibility
    echo "\xEF\xBB\xBF";
    echo implode( "\n", $rows );
    exit;
} );


// --- Report callback ---
function books_breakdown_report_cb() {
    global $wpdb;

    // --- Get book counts per language + book_type combination ---
    $raw = $wpdb->get_results("
        SELECT
            pm_lang.meta_value                                      AS language_id,
            COALESCE( NULLIF( pm_type.meta_value, '' ), '(none)' ) AS book_type,
            COUNT( p.ID )                                           AS total
        FROM {$wpdb->posts} p
        LEFT JOIN {$wpdb->postmeta} pm_lang
            ON p.ID = pm_lang.post_id
            AND pm_lang.meta_key = 'language'
        LEFT JOIN {$wpdb->postmeta} pm_type
            ON p.ID = pm_type.post_id
            AND pm_type.meta_key = 'book_type'
        WHERE p.post_type    = 'book'
          AND p.post_status  = 'publish'
          AND pm_type.meta_value IN ('video_monolingual','video_bilingual','video_english','flipbook')
        GROUP BY pm_lang.meta_value, pm_type.meta_value
        ORDER BY pm_lang.meta_value, total DESC
    ");

    // --- Total published books ---
    $total_books = (int) $wpdb->get_var("
        SELECT COUNT( ID ) FROM {$wpdb->posts}
        WHERE post_type = 'book' AND post_status = 'publish'
    ");

    // --- Count QUIZ vs ACTIVITY per language AND per book_type ---
    $dl_raw = $wpdb->get_results("
        SELECT
            pm_lang.meta_value  AS language_id,
            pm_type.meta_value  AS book_type,
            pm_pdf.meta_value   AS pdf_value
        FROM {$wpdb->posts} p
        LEFT JOIN {$wpdb->postmeta} pm_lang
            ON p.ID = pm_lang.post_id
            AND pm_lang.meta_key = 'language'
        LEFT JOIN {$wpdb->postmeta} pm_type
            ON p.ID = pm_type.post_id
            AND pm_type.meta_key = 'book_type'
        INNER JOIN {$wpdb->postmeta} pm_pdf
            ON p.ID = pm_pdf.post_id
            AND pm_pdf.meta_key REGEXP '^download_links_[0-9]+_pdf$'
        WHERE p.post_type   = 'book'
          AND p.post_status = 'publish'
    ");

    $book_types = [ 'video_monolingual', 'video_bilingual', 'video_english', 'flipbook' ];

    $dl_counts = [];
    foreach ( $dl_raw as $row ) {
        $lid = $row->language_id ?: '';
        $bt  = $row->book_type   ?: '';
        if ( ! isset( $dl_counts[ $lid ] ) ) {
            $dl_counts[ $lid ] = [
                'quiz'     => 0,
                'activity' => 0,
                'types'    => array_fill_keys( $book_types, [ 'quiz' => 0, 'activity' => 0 ] ),
            ];
        }
        $is_quiz = str_contains( $row->pdf_value, '<iframe' );
        $key     = $is_quiz ? 'quiz' : 'activity';
        $dl_counts[ $lid ][ $key ]++;
        if ( in_array( $bt, $book_types ) ) {
            $dl_counts[ $lid ]['types'][ $bt ][ $key ]++;
        }
    }

    $grand_quiz     = array_sum( array_column( $dl_counts, 'quiz' ) );
    $grand_activity = array_sum( array_column( $dl_counts, 'activity' ) );

    $grand_type_dl = array_fill_keys( $book_types, [ 'quiz' => 0, 'activity' => 0 ] );
    foreach ( $dl_counts as $data ) {
        foreach ( $book_types as $bt ) {
            $grand_type_dl[ $bt ]['quiz']     += $data['types'][ $bt ]['quiz'];
            $grand_type_dl[ $bt ]['activity'] += $data['types'][ $bt ]['activity'];
        }
    }

    $empty_type_dl = array_fill_keys( $book_types, [ 'quiz' => 0, 'activity' => 0 ] );

    $all_languages = get_posts( [
        'post_type'      => 'language',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'fields'         => 'ids',
    ] );

    $languages = [];
    foreach ( $all_languages as $lid ) {
        $languages[ (string) $lid ] = [
            'total'    => 0,
            'quiz'     => 0,
            'activity' => 0,
            'types'    => array_fill_keys( $book_types, 0 ),
            'type_dl'  => $empty_type_dl,
        ];
    }

    foreach ( $raw as $row ) {
        $lid = $row->language_id ?: '';
        if ( ! isset( $languages[ $lid ] ) ) {
            $languages[ $lid ] = [
                'total'    => 0,
                'quiz'     => 0,
                'activity' => 0,
                'types'    => array_fill_keys( $book_types, 0 ),
                'type_dl'  => $empty_type_dl,
            ];
        }
        $languages[ $lid ]['total'] += (int) $row->total;
        if ( in_array( $row->book_type, $book_types ) ) {
            $languages[ $lid ]['types'][ $row->book_type ] += (int) $row->total;
        }
    }

    foreach ( $dl_counts as $lid => $counts ) {
        if ( ! isset( $languages[ $lid ] ) ) {
            $languages[ $lid ] = [
                'total'    => 0,
                'quiz'     => 0,
                'activity' => 0,
                'types'    => array_fill_keys( $book_types, 0 ),
                'type_dl'  => $empty_type_dl,
            ];
        }
        $languages[ $lid ]['quiz']     += $counts['quiz'];
        $languages[ $lid ]['activity'] += $counts['activity'];
        foreach ( $book_types as $bt ) {
            $languages[ $lid ]['type_dl'][ $bt ]['quiz']     += $counts['types'][ $bt ]['quiz'];
            $languages[ $lid ]['type_dl'][ $bt ]['activity'] += $counts['types'][ $bt ]['activity'];
        }
    }

    uasort( $languages, fn( $a, $b ) => $b['total'] - $a['total'] );

    $resolved = [];
    foreach ( $languages as $lid => $data ) {
        $title = is_numeric( $lid ) ? get_the_title( (int) $lid ) : '';
        $resolved[] = [
            'id'       => $lid,
            'title'    => $title ?: $lid,
            'total'    => $data['total'],
            'quiz'     => $data['quiz'],
            'activity' => $data['activity'],
            'types'    => $data['types'],
            'type_dl'  => $data['type_dl'],
        ];
    }

    $pct = fn( $part, $whole ) => $whole > 0 ? round( $part / $whole * 100, 1 ) : 0;

    $type_labels = [
        'video_monolingual' => 'Monolingual',
        'video_bilingual'   => 'Bilingual',
        'video_english'     => 'English',
        'flipbook'          => 'Flipbook',
    ];

    $csv_url = add_query_arg( [
        'post_type' => 'book',
        'page'      => 'books-breakdown-report',
        'action'    => 'download-csv',
    ], admin_url( 'edit.php' ) );
    ?>

    <div class="wrap">
        <h1>Books Breakdown Report</h1>
        <p class="description" style="margin-bottom:6px;">
            Generated: <?php echo esc_html( current_time( 'F j, Y \a\t g:i a' ) ); ?> &nbsp;|&nbsp;
            Total books: <strong><?php echo number_format( $total_books ); ?></strong> &nbsp;|&nbsp;
            Languages: <strong><?php echo count( $resolved ); ?></strong> &nbsp;|&nbsp;
            Total activities: <strong><?php echo number_format( $grand_activity ); ?></strong> &nbsp;|&nbsp;
            Total quizzes: <strong><?php echo number_format( $grand_quiz ); ?></strong> &nbsp;|&nbsp;
            <a href="<?php echo esc_url( $csv_url ); ?>">Download CSV</a>
        </p>

        <style>
            /*
             * The wrapper provides the scrollable viewport.
             * A fixed max-height lets vertical scrolling kick in so the
             * sticky <thead> actually works — without a scroll container,
             * position:sticky has nothing to stick inside.
             */
            #books-breakdown-wrap {
                overflow: auto;          /* both axes */
                max-height: 75vh;        /* adjust to taste */
                border: 1px solid #e5e7eb;
            }

            #books-breakdown { border-collapse: collapse; font-size: 0.85rem; min-width: 100%; }
            #books-breakdown thead th { background-clip: padding-box; }
            #books-breakdown-totals-row td { background-clip: padding-box; }
            #books-breakdown-divider-row td { background-clip: padding-box; }

            /* Freeze both header rows */
            #books-breakdown thead th {
                position: sticky;
                top: 0;
                z-index: 2;             /* above tbody cells */
            }

            /*
             * Row 2 of the header must sit below row 1.
             * Each <th> in the first row has rowspan="2", so its rendered
             * height determines the offset for the second row.
             * We use a JS snippet below to set the correct pixel value
             * automatically, but provide a sensible CSS fallback here.
             */
            #books-breakdown thead tr:nth-child(2) th {
                top: 37px; /* fallback – overridden by JS */
            }

            #books-breakdown th { background: #1e3a5f; color: #fff; padding: 8px 10px; text-align: center; white-space: nowrap; }
            #books-breakdown thead tr:nth-child(2) th {
                white-space: nowrap;
                padding: 8px 10px;
            }
            #books-breakdown th.left  { text-align: left; }
            #books-breakdown th.act   { background: #2d6a4f; }
            #books-breakdown th.quiz  { background: #7b3f00; }
            #books-breakdown th.count { background: #2c4a7c; }
            #books-breakdown td { background: #fff; padding: 6px 10px; border-bottom: 1px solid #e5e7eb; text-align: center; }
            #books-breakdown th.pct { border-bottom: none; }
            #books-breakdown td.left  { text-align: left; }
            #books-breakdown td.act   { background: #f0faf4; }
            #books-breakdown td.quiz  { background: #fff8f0; }
            #books-breakdown tr:hover td      { background: #f0f7ff; }
            #books-breakdown tr:hover td.act  { background: #d8f3e8; }
            #books-breakdown tr:hover td.quiz { background: #fde8cc; }
            #books-breakdown-totals-row td      { background: #f4f4f4; position: sticky; z-index: 2; border-top: 2px solid #1e3a5f; border-bottom: none; }
            #books-breakdown-totals-row td.act  { background: #e0f5ea; border-top: 2px solid #1e3a5f; }
            #books-breakdown-totals-row td.quiz { background: #fde8cc; border-top: 2px solid #1e3a5f; }
            #books-breakdown-totals-row td.section-divider { background: #f4f4f4; border-top: 2px solid #1e3a5f; }
            #books-breakdown-divider-row td { height: 2px; background: #1e3a5f; padding: 0; position: sticky; z-index: 2; border: none; }
            #books-breakdown .zero { color: #d1d5db; }
            #books-breakdown .section-divider { border-left: 3px solid #1e3a5f; }
        </style>

        <div id="books-breakdown-wrap">
        <table id="books-breakdown">
            <thead>
                <tr>
                    <th class="left" rowspan="2">Language</th>
                    <th rowspan="2">Total</th>
                    <th rowspan="2">%</th>
                    <th class="act"  rowspan="2">Act</th>
                    <th class="quiz" rowspan="2">Quiz</th>
                    <?php foreach ( $type_labels as $bt => $label ) : ?>
                        <th class="section-divider count" colspan="4"><?php echo esc_html( $label ); ?></th>
                    <?php endforeach; ?>
                </tr>
                <tr>
                    <?php foreach ( $type_labels as $bt => $label ) : ?>
                        <th class="section-divider count">Count</th>
                        <th class="pct count">%</th>
                        <th class="act">Act</th>
                        <th class="quiz">Quiz</th>
                    <?php endforeach; ?>
                </tr>
                <tr id="books-breakdown-totals-row">
                    <td class="left" style="font-weight:bold;">Total</td>
                    <td style="font-weight:bold;"><?php echo number_format( $total_books ); ?></td>
                    <td style="font-weight:bold;">100%</td>
                    <td class="act" style="font-weight:bold;"><?php echo number_format( $grand_activity ); ?></td>
                    <td class="quiz" style="font-weight:bold;"><?php echo number_format( $grand_quiz ); ?></td>
                    <?php foreach ( $book_types as $bt ) :
                        $type_total = array_sum( array_column( array_map( fn($r) => $r['types'], $resolved ), $bt ) );
                    ?>
                        <td class="section-divider" style="font-weight:bold;"><?php echo number_format( $type_total ); ?></td>
                        <td style="font-weight:bold;"><?php echo $pct( $type_total, $total_books ); ?>%</td>
                        <td class="act" style="font-weight:bold;"><?php echo number_format( $grand_type_dl[ $bt ]['activity'] ); ?></td>
                        <td class="quiz" style="font-weight:bold;"><?php echo number_format( $grand_type_dl[ $bt ]['quiz'] ); ?></td>
                    <?php endforeach; ?>
                </tr>
                <tr id="books-breakdown-divider-row">
                    <?php
                    $divider_cols = 5 + ( count( $book_types ) * 4 );
                    for ( $i = 0; $i < $divider_cols; $i++ ) echo '<td></td>';
                    ?>
                </tr>
            </thead>
            <tbody>
            <?php foreach ( $resolved as $r ) :
                $lang_total = $r['total'];
            ?>
                <tr>
                    <td class="left"><?php echo esc_html( $r['title'] ); ?></td>
                    <td><strong><?php echo number_format( $lang_total ); ?></strong></td>
                    <td><?php echo $pct( $lang_total, $total_books ); ?>%</td>
                    <td class="act  <?php echo $r['activity'] === 0 ? 'zero' : ''; ?>"><?php echo number_format( $r['activity'] ); ?></td>
                    <td class="quiz <?php echo $r['quiz']     === 0 ? 'zero' : ''; ?>"><?php echo number_format( $r['quiz'] ); ?></td>
                    <?php foreach ( $book_types as $bt ) :
                        $count = $r['types'][ $bt ]               ?? 0;
                        $act   = $r['type_dl'][ $bt ]['activity'] ?? 0;
                        $qz    = $r['type_dl'][ $bt ]['quiz']     ?? 0;
                    ?>
                        <td class="section-divider <?php echo $count === 0 ? 'zero' : ''; ?>"><?php echo number_format( $count ); ?></td>
                        <td class="<?php echo $count === 0 ? 'zero' : ''; ?>"><?php echo $pct( $count, $lang_total ); ?>%</td>
                        <td class="act  <?php echo $act === 0 ? 'zero' : ''; ?>"><?php echo number_format( $act ); ?></td>
                        <td class="quiz <?php echo $qz  === 0 ? 'zero' : ''; ?>"><?php echo number_format( $qz ); ?></td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div><!-- #books-breakdown-wrap -->
    </div><!-- .wrap -->

    <script>
    ( function () {
        /*
         * The second header row uses `position:sticky` with a `top` value
         * equal to the height of the first header row.  We measure that
         * height after the DOM is ready and apply it so it works regardless
         * of font size, zoom level, or WP admin bar height.
         */
        var table    = document.getElementById( 'books-breakdown' );
        if ( ! table ) return;

        var firstRow = table.querySelector( 'thead tr:first-child' );
        var secRow   = table.querySelector( 'thead tr:nth-child(2)' );
        var tfootRow    = table.querySelector( '#books-breakdown-totals-row' );
        var dividerRow  = table.querySelector( '#books-breakdown-divider-row' );
        if ( ! firstRow || ! secRow ) return;

        function syncStickyTop() {
            var h1 = firstRow.getBoundingClientRect().height;
            var h2 = secRow.getBoundingClientRect().height;

            secRow.querySelectorAll( 'th' ).forEach( function ( th ) {
                th.style.top = h1 + 'px';
            } );

            if ( tfootRow ) {
                var h3 = tfootRow.getBoundingClientRect().height;
                tfootRow.querySelectorAll( 'td' ).forEach( function ( td ) {
                    td.style.top = ( h1 + h2 ) + 'px';
                } );
                if ( dividerRow ) {
                    dividerRow.querySelectorAll( 'td' ).forEach( function ( td ) {
                        td.style.top = ( h1 + h2 + h3 ) + 'px';
                    } );
                }
            }
        }

        syncStickyTop();
        window.addEventListener( 'resize', syncStickyTop );
    } )();
    </script>
    <?php
}

/**
 * ----------------------------------------------------------------
 * Auto prepend "decommissioned-" to libraries that are deactivated
 * Auto prepend "Decommissioned - " to title if deactivated
 * ----------------------------------------------------------------
 */

add_action( 'wp_insert_post', 'prefix_decommission_library_slug', 10, 3 );

function prefix_decommission_library_slug( $post_id, $post, $update ) {
    // Only run on the 'library' CPT
    if ( $post->post_type !== 'library' ) {
        return;
    }
    // Bail on autosave, revisions, or non-publish status
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( wp_is_post_revision( $post_id ) ) return;
    if ( $post->post_status !== 'publish' ) return;

    // Get the custom field value
    $subscription_status = get_post_meta( $post_id, 'library_subscription_status', true );

    // Current slug and title
    $current_slug  = $post->post_name;
    $current_title = $post->post_title;
    $slug_prefix   = 'decommissioned-';
    $title_prefix  = 'Decommissioned - ';

    $new_slug  = null;
    $new_title = null;

    if ( ! $subscription_status ) {
        // Needs prefixes — add them if not already there
        if ( strpos( $current_slug, $slug_prefix ) !== 0 ) {
            $new_slug = $slug_prefix . $current_slug;
        }
        if ( strpos( $current_title, $title_prefix ) !== 0 ) {
            $new_title = $title_prefix . $current_title;
        }
    } else {
        // Status is active — strip prefixes if previously added
        if ( strpos( $current_slug, $slug_prefix ) === 0 ) {
            $new_slug = substr( $current_slug, strlen( $slug_prefix ) );
        }
        if ( strpos( $current_title, $title_prefix ) === 0 ) {
            $new_title = substr( $current_title, strlen( $title_prefix ) );
        }
    }

    // Only run wp_update_post if something actually needs to change
    if ( $new_slug !== null || $new_title !== null ) {
        $args = [ 'ID' => $post_id ];
        if ( $new_slug !== null )  $args['post_name']  = $new_slug;
        if ( $new_title !== null ) $args['post_title'] = $new_title;

        remove_action( 'wp_insert_post', 'prefix_decommission_library_slug', 10 );
        wp_update_post( $args );
        add_action( 'wp_insert_post', 'prefix_decommission_library_slug', 10, 3 );
    }
}

/**
 * ----------------------------------------------------------------
 * Media export URLs in the CMS (export media URLs only)
 * ----------------------------------------------------------------
 */

// 1. Register the submenu page under Media
add_action( 'admin_menu', 'l4k_emuRegisterMenuPage' );

function l4k_emuRegisterMenuPage() {
    add_media_page(
        'Export Media URLs',
        'Export Media URLs',
        'upload_files',
        'export-media-urls',
        'l4k_emuRenderPage'
    );
}

// 2. Handle the CSV export (runs before any output)
add_action( 'admin_init', 'l4k_emuHandleExport' );

function l4k_emuHandleExport() {
    if (
        ! isset( $_POST['emu_export_nonce'] ) ||
        ! wp_verify_nonce( $_POST['emu_export_nonce'], 'emu_export_action' )
    ) {
        return;
    }

    if ( empty( $_POST['emu_do_export'] ) ) {
        return;
    }

    if ( ! current_user_can( 'upload_files' ) ) {
        wp_die( 'You do not have permission to export media.' );
    }

    $date_from = sanitize_text_field( $_POST['emu_date_from'] ?? '' );
    $date_to   = sanitize_text_field( $_POST['emu_date_to']   ?? '' );
    $author_id = intval( $_POST['emu_author'] ?? 0 );

    $args = [
        'post_type'      => 'attachment',
        'post_status'    => 'inherit',
        'posts_per_page' => -1,
        'orderby'        => 'date',
        'order'          => 'ASC',
    ];

    if ( $author_id > 0 ) {
        $args['author'] = $author_id;
    }

    if ( $date_from || $date_to ) {
        $date_query = [ 'inclusive' => true ];
        if ( $date_from ) $date_query['after']  = $date_from . ' 00:00:00';
        if ( $date_to )   $date_query['before'] = $date_to   . ' 23:59:59';
        $args['date_query'] = [ $date_query ];
    }

    $attachments = ( new WP_Query( $args ) )->posts;

    header( 'Content-Type: text/csv; charset=utf-8' );
    header( 'Content-Disposition: attachment; filename="media-urls-' . date( 'Y-m-d' ) . '.csv"' );
    header( 'Pragma: no-cache' );

    $out = fopen( 'php://output', 'w' );
    fputcsv( $out, [ 'File URL', 'Upload Date' ] );

    foreach ( $attachments as $att ) {
        fputcsv( $out, [
            wp_get_attachment_url( $att->ID ),
            get_the_date( 'Y-m-d H:i:s', $att->ID ),
        ] );
    }

    fclose( $out );
    exit;
}

// 3. Render the admin page UI
function l4k_emuRenderPage() {
    $date_from = sanitize_text_field( $_POST['emu_date_from'] ?? '' );
    $date_to   = sanitize_text_field( $_POST['emu_date_to']   ?? '' );
    $author_id = intval( $_POST['emu_author'] ?? 0 );

    $admins = get_users( [ 'role' => 'administrator' ] );
    ?>
    <div class="wrap">
        <h1>Export Media URLs</h1>
        <p>Select a date range and/or an administrator, then click <strong>Export CSV</strong>. Leave fields empty to export all media.</p>
        <form method="post">
            <?php wp_nonce_field( 'emu_export_action', 'emu_export_nonce' ); ?>
            <input type="hidden" name="emu_do_export" value="1">
            <table class="form-table">
                <tr>
                    <th><label for="emu_date_from">Date From</label></th>
                    <td><input type="date" id="emu_date_from" name="emu_date_from" value="<?php echo esc_attr( $date_from ); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="emu_date_to">Date To</label></th>
                    <td><input type="date" id="emu_date_to" name="emu_date_to" value="<?php echo esc_attr( $date_to ); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="emu_author">Administrator</label></th>
                    <td>
                        <select id="emu_author" name="emu_author" class="regular-text">
                            <option value="0">— All Administrators —</option>
                            <?php foreach ( $admins as $admin ) : ?>
                                <option value="<?php echo esc_attr( $admin->ID ); ?>" <?php selected( $author_id, $admin->ID ); ?>>
                                    <?php echo esc_html( $admin->display_name . ' (' . $admin->user_login . ')' ); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
            </table>
            <?php submit_button( 'Export CSV', 'primary', 'submit', false ); ?>
        </form>
    </div>
    <?php
}

/**
 * ----------------------------------------------------------------
 * Books level breakdown report
 * ----------------------------------------------------------------
 */

// --- Custom level sort order ---
function books_level_sort_order(): array {
    return [ 'A', 'P', '1', '2', '3', '4+' ];
}

function books_sort_levels( array $levels ): array {
    $order = books_level_sort_order();
    usort( $levels, function ( $a, $b ) use ( $order ) {
        $ai = array_search( $a, $order, true );
        $bi = array_search( $b, $order, true );
        if ( $ai === false && $bi === false ) return strcmp( $a, $b );
        if ( $ai === false ) return 1;
        if ( $bi === false ) return -1;
        return $ai - $bi;
    } );
    return $levels;
}

// --- Book types ---
function books_get_types(): array {
    return [
        'video_monolingual' => 'Mono',
        'video_bilingual'   => 'Bi',
        'video_english'     => 'Eng',
        'flipbook'          => 'Flip',
    ];
}


// --- Register the submenu page under the Books CPT ---
add_action( 'admin_menu', function () {
    add_submenu_page(
        'edit.php?post_type=book',
        'Level Summary',
        'Level Summary',
        'edit_posts',
        'books-level-breakdown-report',
        'books_level_breakdown_report_cb'
    );
} );


// --- Shared data builder ---
function books_level_breakdown_get_data(): array {
    global $wpdb;

    $book_types = books_get_types();
    $type_keys  = array_keys( $book_types );

    $all_levels_raw = $wpdb->get_col("
        SELECT DISTINCT pm_level.meta_value
        FROM {$wpdb->posts} p
        INNER JOIN {$wpdb->postmeta} pm_level
            ON p.ID = pm_level.post_id
            AND pm_level.meta_key = 'levels_level'
        WHERE p.post_type   = 'book'
          AND p.post_status = 'publish'
          AND pm_level.meta_value != ''
    ");
    $all_levels = books_sort_levels( $all_levels_raw );

    // Build empty per-level structure
    $empty_level = [ 'total' => 0 ];
    foreach ( $type_keys as $t ) $empty_level[ $t ] = 0;

    // Query: language, level, type, count
    $raw = $wpdb->get_results("
        SELECT
            pm_lang.meta_value                                        AS language_id,
            COALESCE( NULLIF( pm_level.meta_value, '' ), '(none)' )  AS level,
            pm_type.meta_value                                        AS book_type,
            COUNT( p.ID )                                             AS total
        FROM {$wpdb->posts} p
        LEFT JOIN {$wpdb->postmeta} pm_lang
            ON p.ID = pm_lang.post_id AND pm_lang.meta_key = 'language'
        LEFT JOIN {$wpdb->postmeta} pm_level
            ON p.ID = pm_level.post_id AND pm_level.meta_key = 'levels_level'
        LEFT JOIN {$wpdb->postmeta} pm_type
            ON p.ID = pm_type.post_id AND pm_type.meta_key = 'book_type'
        WHERE p.post_type   = 'book'
          AND p.post_status = 'publish'
        GROUP BY pm_lang.meta_value, pm_level.meta_value, pm_type.meta_value
        ORDER BY pm_lang.meta_value, total DESC
    ");

    $total_books = (int) $wpdb->get_var("
        SELECT COUNT( ID ) FROM {$wpdb->posts}
        WHERE post_type = 'book' AND post_status = 'publish'
    ");

    $all_languages = get_posts( [
        'post_type'      => 'language',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'fields'         => 'ids',
    ] );

    $languages = [];
    foreach ( $all_languages as $lid ) {
        $languages[ (string) $lid ] = [
            'total'  => 0,
            'levels' => array_fill_keys( $all_levels, $empty_level ),
        ];
    }

    foreach ( $raw as $row ) {
        $lid  = $row->language_id ?: '';
        $cnt  = (int) $row->total;
        $type = $row->book_type;

        if ( ! isset( $languages[ $lid ] ) ) {
            $languages[ $lid ] = [
                'total'  => 0,
                'levels' => array_fill_keys( $all_levels, $empty_level ),
            ];
        }

        if ( ! in_array( $row->level, $all_levels, true ) ) continue;

        if ( in_array( $type, $type_keys, true ) ) {
            $languages[ $lid ]['levels'][ $row->level ][ $type ] += $cnt;
            $languages[ $lid ]['levels'][ $row->level ]['total'] += $cnt;
            $languages[ $lid ]['total'] += $cnt;
        }
    }

    uasort( $languages, fn( $a, $b ) => $b['total'] - $a['total'] );

    $resolved = [];
    foreach ( $languages as $lid => $data ) {
        $title = is_numeric( $lid ) ? get_the_title( (int) $lid ) : '';
        $resolved[] = [
            'id'     => $lid,
            'title'  => $title ?: $lid,
            'total'  => $data['total'],
            'levels' => $data['levels'],
        ];
    }

    // Level totals row
    $level_totals = array_fill_keys( $all_levels, $empty_level );
    foreach ( $resolved as $r ) {
        foreach ( $all_levels as $level ) {
            $level_totals[ $level ]['total'] += $r['levels'][ $level ]['total'];
            foreach ( $type_keys as $t ) {
                $level_totals[ $level ][ $t ] += $r['levels'][ $level ][ $t ];
            }
        }
    }

    return compact( 'all_levels', 'resolved', 'level_totals', 'total_books' );
}


// --- CSV download handler ---
add_action( 'admin_init', function () {
    if (
        ! isset( $_GET['page'], $_GET['action'] ) ||
        $_GET['page']   !== 'books-level-breakdown-report' ||
        $_GET['action'] !== 'download-csv'
    ) return;

    if ( ! current_user_can( 'edit_posts' ) ) wp_die( 'Insufficient permissions.' );

    [ 'all_levels' => $all_levels, 'resolved' => $resolved, 'level_totals' => $level_totals, 'total_books' => $total_books ]
        = books_level_breakdown_get_data();

    $book_types = books_get_types();
    $type_keys  = array_keys( $book_types );

    $csv_escape = function ( $val ) {
        $val = (string) $val;
        if ( str_contains( $val, ',' ) || str_contains( $val, '"' ) || str_contains( $val, "\n" ) ) {
            $val = '"' . str_replace( '"', '""', $val ) . '"';
        }
        return $val;
    };

    $rows = [];

    // Header
    $h1 = [ 'Language', 'Total' ];
    foreach ( $all_levels as $level ) {
        $h1[] = $level . ' Total';
        foreach ( $type_keys as $t ) {
            $h1[] = $level . ' ' . $book_types[ $t ];
        }
    }
    $rows[] = implode( ',', array_map( $csv_escape, $h1 ) );

    // Totals row
    $totals_row = [ 'Total', $total_books ];
    foreach ( $all_levels as $level ) {
        $totals_row[] = $level_totals[ $level ]['total'];
        foreach ( $type_keys as $t ) {
            $totals_row[] = $level_totals[ $level ][ $t ];
        }
    }
    $rows[] = implode( ',', array_map( $csv_escape, $totals_row ) );

    // Per-language rows
    foreach ( $resolved as $r ) {
        $data_row = [ $r['title'], $r['total'] ];
        foreach ( $all_levels as $level ) {
            $data_row[] = $r['levels'][ $level ]['total'];
            foreach ( $type_keys as $t ) {
                $data_row[] = $r['levels'][ $level ][ $t ];
            }
        }
        $rows[] = implode( ',', array_map( $csv_escape, $data_row ) );
    }

    $filename = 'books-level-breakdown-' . current_time( 'Y-m-d' ) . '.csv';
    header( 'Content-Type: text/csv; charset=utf-8' );
    header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
    header( 'Pragma: no-cache' );
    header( 'Expires: 0' );
    echo "\xEF\xBB\xBF";
    echo implode( "\n", $rows );
    exit;
} );


// --- Report callback ---
function books_level_breakdown_report_cb() {
    [
        'all_levels'   => $all_levels,
        'resolved'     => $resolved,
        'level_totals' => $level_totals,
        'total_books'  => $total_books,
    ] = books_level_breakdown_get_data();

    $book_types = books_get_types();
    $type_keys  = array_keys( $book_types );

    $csv_url = add_query_arg( [
        'post_type' => 'book',
        'page'      => 'books-level-breakdown-report',
        'action'    => 'download-csv',
    ], admin_url( 'edit.php' ) );
    ?>

    <div class="wrap">
        <h1>Reading Level Breakdown Report</h1>
        <p class="description" style="margin-bottom:6px;">
            Generated: <?php echo esc_html( current_time( 'F j, Y \a\t g:i a' ) ); ?> &nbsp;|&nbsp;
            Total books: <strong><?php echo number_format( $total_books ); ?></strong> &nbsp;|&nbsp;
            Languages: <strong><?php echo count( $resolved ); ?></strong> &nbsp;|&nbsp;
            Levels: <strong><?php echo count( $all_levels ); ?></strong> &nbsp;|&nbsp;
            <a href="<?php echo esc_url( $csv_url ); ?>">Download CSV</a>
        </p>

        <style>
            #books-level-breakdown-wrap {
                overflow: auto;
                max-height: 75vh;
                border: 1px solid #e5e7eb;
            }

            #books-level-breakdown { border-collapse: collapse; font-size: 0.85rem; min-width: 100%; }
            #books-level-breakdown thead th { background-clip: padding-box; }
            #books-level-breakdown-totals-row td { background-clip: padding-box; }
            #books-level-breakdown-divider-row td { background-clip: padding-box; }

            #books-level-breakdown thead th {
                position: sticky;
                top: 0;
                z-index: 2;
            }

            #books-level-breakdown thead tr:nth-child(2) th {
                top: 37px; /* updated by JS */
            }

            #books-level-breakdown th { background: #1e3a5f; color: #fff; padding: 8px 10px; text-align: center; white-space: nowrap; }
            #books-level-breakdown th.left  { text-align: left; }
            #books-level-breakdown th.sub   { background: #2c4a7c; font-weight: normal; font-size: 0.78rem; }
            #books-level-breakdown th.total-sub { background: #1a3355; }
            #books-level-breakdown td { background: #fff; padding: 6px 10px; border-bottom: 1px solid #e5e7eb; text-align: center; }
            #books-level-breakdown td.left  { text-align: left; }
            #books-level-breakdown tr:hover td { background: #f0f7ff; }
            #books-level-breakdown-totals-row td { background: #f4f4f4; position: sticky; z-index: 2; border-top: 2px solid #1e3a5f; border-bottom: none; }
            #books-level-breakdown-divider-row td { height: 2px; background: #1e3a5f; padding: 0; position: sticky; z-index: 2; border: none; }
            #books-level-breakdown .zero { color: #d1d5db; }
            #books-level-breakdown .section-divider { border-left: 3px solid #1e3a5f; }
            #books-level-breakdown .type-divider { border-left: 1px solid #3a5a8c; }
        </style>

        <div id="books-level-breakdown-wrap">
        <table id="books-level-breakdown">
            <thead>
                <!-- Row 1: Language | Total | Level A (colspan 5) | Level P (colspan 5) | ... -->
                <tr>
                    <th class="left" rowspan="2">Language</th>
                    <th rowspan="2">Total</th>
                    <?php foreach ( $all_levels as $level ) : ?>
                        <th class="section-divider" colspan="4">
                            <?php echo esc_html( $level ); ?>
                        </th>
                    <?php endforeach; ?>
                </tr>
                <!-- Row 2: sub-columns: Mono | Bi | Eng | Flip -->
                <tr>
                    <?php foreach ( $all_levels as $level ) : ?>
                        <?php foreach ( $book_types as $t_key => $t_label ) : ?>
                            <th class="sub <?php echo $t_key === array_key_first( $book_types ) ? 'section-divider' : 'type-divider'; ?>">
                                <?php echo esc_html( $t_label ); ?>
                            </th>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </tr>
                <!-- Totals row -->
                <tr id="books-level-breakdown-totals-row">
                    <td class="left" style="font-weight:bold;">Total</td>
                    <td style="font-weight:bold;"><?php echo number_format( $total_books ); ?></td>
                    <?php foreach ( $all_levels as $level ) : ?>
                        <?php foreach ( $type_keys as $i => $t ) : ?>
                            <td class="<?php echo $i === 0 ? 'section-divider' : 'type-divider'; ?>" style="font-weight:bold;"><?php echo number_format( $level_totals[ $level ][ $t ] ); ?></td>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </tr>
                <tr id="books-level-breakdown-divider-row">
                    <?php
                    $divider_cols = 2 + ( count( $all_levels ) * count( $type_keys ) );
                    for ( $i = 0; $i < $divider_cols; $i++ ) echo '<td></td>';
                    ?>
                </tr>
            </thead>
            <tbody>
            <?php foreach ( $resolved as $r ) :
                $lang_total = $r['total'];
            ?>
                <tr>
                    <td class="left"><?php echo esc_html( $r['title'] ); ?></td>
                    <td><strong><?php echo number_format( $lang_total ); ?></strong></td>
                    <?php foreach ( $all_levels as $level ) :
                        $ldata = $r['levels'][ $level ];
                    ?>
                        <?php foreach ( $type_keys as $i => $t ) :
                            $c = $ldata[ $t ];
                        ?>
                            <td class="<?php echo $i === 0 ? 'section-divider' : 'type-divider'; ?> <?php echo $c === 0 ? 'zero' : ''; ?>">
                                <?php echo number_format( $c ); ?>
                            </td>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div><!-- #books-level-breakdown-wrap -->
    </div><!-- .wrap -->

    <script>
    ( function () {
        var table = document.getElementById( 'books-level-breakdown' );
        if ( ! table ) return;

        var firstRow   = table.querySelector( 'thead tr:first-child' );
        var secRow     = table.querySelector( 'thead tr:nth-child(2)' );
        var tfootRow   = table.querySelector( '#books-level-breakdown-totals-row' );
        var dividerRow = table.querySelector( '#books-level-breakdown-divider-row' );
        if ( ! firstRow || ! secRow ) return;

        function syncStickyTop() {
            var h1 = firstRow.getBoundingClientRect().height;
            var h2 = secRow.getBoundingClientRect().height;

            secRow.querySelectorAll( 'th' ).forEach( function ( th ) {
                th.style.top = h1 + 'px';
            } );

            if ( tfootRow ) {
                var h3 = tfootRow.getBoundingClientRect().height;
                tfootRow.querySelectorAll( 'td' ).forEach( function ( td ) {
                    td.style.top = ( h1 + h2 ) + 'px';
                } );
                if ( dividerRow ) {
                    dividerRow.querySelectorAll( 'td' ).forEach( function ( td ) {
                        td.style.top = ( h1 + h2 + h3 ) + 'px';
                    } );
                }
            }
        }

        syncStickyTop();
        window.addEventListener( 'resize', syncStickyTop );
    } )();
    </script>
    <?php
}
?>
<?php
// -------------------------------------------------------
// CLAUDE KNOWLEDGE BASE
// -------------------------------------------------------

// 1. REGISTER ADMIN PAGE UNDER TOOLS
// -------------------------------------------------------
add_action("admin_menu", function () {
    add_management_page(
        "Claude Knowledge Base",
        "Claude Knowledge Base",
        "manage_options",
        "claude-knowledge-base",
        "ckb_render_page"
    );
});

function ckb_render_page() {
    if (!function_exists("get_field")) {
        echo '<div class="wrap"><h1>Claude Knowledge Base</h1>';
        echo '<div class="notice notice-error"><p><strong>ACF Pro is required.</strong></p></div></div>';
        return;
    }

    if (!get_option("claude_kb_secret_token")) {
        update_option("claude_kb_secret_token", wp_generate_password(32, false));
    }

    $saved_message = "";
    if (isset($_POST["ckb_save_settings"]) && check_admin_referer("ckb_settings_nonce")) {
        update_option("claude_kb_api_key",   sanitize_text_field($_POST["ckb_api_key"] ?? ""));
        update_option("claude_kb_chat_slug", sanitize_title($_POST["ckb_chat_slug"] ?? "claudechat"));
        flush_rewrite_rules();
        $saved_message = "✅ Settings saved!";
    }

    $apiKey  = get_option("claude_kb_api_key", "");
    $slug    = get_option("claude_kb_chat_slug", "claudechat");
    $chatUrl = home_url("/" . $slug);
    ?>
    <div class="wrap">
        <h1>Claude Knowledge Base</h1>

        <?php if ($saved_message): ?>
            <div class="notice notice-success is-dismissible"><p><?php echo $saved_message; ?></p></div>
        <?php endif; ?>

        <form method="POST">
            <?php wp_nonce_field("ckb_settings_nonce"); ?>
            <h2>⚙️ Settings</h2>
            <table class="form-table">
                <tr>
                    <th><label for="ckb_api_key">Anthropic API Key</label></th>
                    <td>
                        <input type="text" id="ckb_api_key" name="ckb_api_key"
                            value="<?php echo esc_attr($apiKey); ?>"
                            style="width:500px;font-family:monospace;" placeholder="sk-ant-..." />
                        <p class="description">Your key from <a href="https://console.anthropic.com" target="_blank">console.anthropic.com</a>.</p>
                    </td>
                </tr>
                <tr>
                    <th><label for="ckb_chat_slug">Chat Endpoint Slug</label></th>
                    <td>
                        <input type="text" id="ckb_chat_slug" name="ckb_chat_slug"
                            value="<?php echo esc_attr($slug); ?>"
                            style="width:200px;" placeholder="claudechat" />
                        <p class="description">
                            Chatbot endpoint:<br>
                            <strong><a href="<?php echo esc_url($chatUrl); ?>" target="_blank"><?php echo esc_html($chatUrl); ?></a></strong>
                        </p>
                    </td>
                </tr>
            </table>
            <?php submit_button("Save Settings", "primary", "ckb_save_settings"); ?>
        </form>

        <hr>

        <h2>📄 Knowledge Base Documents</h2>
        <p style="color:#555;margin-bottom:16px;">
            Add, remove or replace documents below. Changes take effect immediately.<br>
            <strong>Supported formats:</strong> PDF, TXT, DOC, DOCX, CSV, XLSX
        </p>
        <?php
        if (function_exists("acf_form")) {
            acf_form([
                "post_id"        => "options",
                "field_groups"   => ckb_get_acf_field_group_ids(),
                "submit_value"   => "Save Documents",
                "updated_message"=> "✅ Documents saved!",
            ]);
        }
        ?>
    </div>
    <?php
}

function ckb_get_acf_field_group_ids() {
    $groups = acf_get_field_groups(["options_page" => "claude-knowledge-base"]);
    return array_column($groups, "key");
}

// -------------------------------------------------------
// 2. ENQUEUE ACF FORM SCRIPTS ON OUR PAGE
// -------------------------------------------------------
add_action("admin_enqueue_scripts", function ($hook) {
    if ($hook !== "tools_page_claude-knowledge-base") return;
    if (function_exists("acf_form_head")) acf_form_head();
});

// -------------------------------------------------------
// 3. REGISTER CUSTOM CHAT ENDPOINT
// -------------------------------------------------------
add_action("init", function () {
    $slug = get_option("claude_kb_chat_slug", "claudechat");
    add_rewrite_rule("^" . preg_quote($slug, "/") . "/?$", "index.php?ckb_chat=1", "top");
});

add_filter("query_vars", function ($vars) {
    $vars[] = "ckb_chat";
    return $vars;
});

add_action("template_redirect", function () {
    if (!get_query_var("ckb_chat")) return;

    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: POST, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type");
    header("Content-Type: text/event-stream");
    header("Cache-Control: no-cache");
    header("X-Accel-Buffering: no");

    if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
        http_response_code(200);
        exit();
    }

    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        echo "data: " . json_encode(["error" => "Only POST requests are allowed."]) . "\n\n";
        exit();
    }

    $apiKey = get_option("claude_kb_api_key", "");
    if (empty($apiKey)) {
        echo "data: " . json_encode(["error" => "Anthropic API key is not configured."]) . "\n\n";
        exit();
    }

    $docs         = [];
    $pdf_files    = get_field("claude_pdf_files", "option");
    $systemPrompt = get_field("claude_system_prompt", "option")
        ?: "You are a helpful customer support agent for LOTE4Kids, an online database of children's picture books in World Languages. Follow these rules strictly: 1. Answer only based on the documents provided. 2. Always be friendly and professional. 3. If you don't know the answer, say: 'I am not sure about that. Please contact us at support@lote4kids.com for further assistance.' 4. Do not answer questions unrelated to LOTE4Kids. 5. Always respond in the same language the user writes in.";

    if ($pdf_files) {
        foreach ($pdf_files as $row) {
            if (empty($row["active"])) continue;
            if (empty($row["file"])) continue;

            $file     = $row["file"];
            $name     = $row["label"] ?? "Document";
            $filePath = is_array($file)
                ? get_attached_file($file["ID"])
                : get_attached_file(attachment_url_to_postid($file));

            if (!$filePath || !file_exists($filePath)) continue;

            $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

            if ($ext === "pdf") {
                $docs[] = [
                    "type"   => "document",
                    "source" => [
                        "type"       => "base64",
                        "media_type" => "application/pdf",
                        "data"       => base64_encode(file_get_contents($filePath))
                    ],
                    "title" => $name
                ];

            } elseif (in_array($ext, ["txt", "csv"])) {
                $docs[] = [
                    "type" => "text",
                    "text" => "--- Document: $name ---\n" . file_get_contents($filePath)
                ];

            } elseif (in_array($ext, ["doc", "docx"])) {
                $text = ($ext === "docx") ? ckb_extract_docx_text($filePath) : strip_tags(file_get_contents($filePath));
                if (!empty($text)) {
                    $docs[] = [
                        "type" => "text",
                        "text" => "--- Document: $name ---\n" . $text
                    ];
                }

            } elseif ($ext === "xlsx") {
                $text = ckb_extract_xlsx_text($filePath);
                if (!empty($text)) {
                    $docs[] = [
                        "type" => "text",
                        "text" => "--- Document: $name ---\n" . $text
                    ];
                }
            }
        }
    }

    $body = json_decode(file_get_contents("php://input"), true);
    if (!isset($body["messages"])) {
        echo "data: " . json_encode(["error" => "No messages provided."]) . "\n\n";
        exit();
    }

    $messages = $body["messages"];

    if (!empty($docs) && count($messages) > 0) {
        $originalText = is_array($messages[0]["content"])
            ? $messages[0]["content"][0]["text"]
            : $messages[0]["content"];

        $contentWithDocs   = $docs;
        $contentWithDocs[] = ["type" => "text", "text" => $originalText];
        $messages[0]["content"] = $contentWithDocs;
    }

    $payload = json_encode([
        "model"      => "claude-sonnet-4-20250514",
        "max_tokens" => 1000,
        "stream"     => true,
        "system"     => $systemPrompt,
        "messages"   => $messages
    ]);

    $ch = curl_init("https://api.anthropic.com/v1/messages");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "x-api-key: " . $apiKey,
        "anthropic-version: 2023-06-01"
    ]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    curl_setopt($ch, CURLOPT_WRITEFUNCTION, function ($ch, $chunk) {
        echo $chunk;
        if (ob_get_level() > 0) ob_flush();
        flush();
        return strlen($chunk);
    });

    curl_exec($ch);
    curl_close($ch);
    exit();
});

// -------------------------------------------------------
// 4. HELPER — Extract text from DOCX
// -------------------------------------------------------
function ckb_extract_docx_text($filePath) {
    $text = "";
    $zip  = new ZipArchive();
    if ($zip->open($filePath) === true) {
        if (($index = $zip->locateName("word/document.xml")) !== false) {
            $xml  = $zip->getFromIndex($index);
            $text = strip_tags(str_replace(["</w:p>", "</w:tr>"], "\n", $xml));
        }
        $zip->close();
    }
    return trim($text);
}

// -------------------------------------------------------
// 5. HELPER — Extract text from XLSX
// -------------------------------------------------------
function ckb_extract_xlsx_text($filePath) {
    $text    = "";
    $zip     = new ZipArchive();
    if ($zip->open($filePath) === true) {
        $strings = [];
        if (($si = $zip->locateName("xl/sharedStrings.xml")) !== false) {
            $xml = $zip->getFromIndex($si);
            preg_match_all('/<t[^>]*>(.*?)<\/t>/s', $xml, $matches);
            $strings = $matches[1];
        }

        $sheetIndex = $zip->locateName("xl/worksheets/sheet1.xml");
        if ($sheetIndex !== false) {
            $sheetXml = $zip->getFromIndex($sheetIndex);
            preg_match_all('/<c[^>]*t="s"[^>]*><v>(\d+)<\/v><\/c>/', $sheetXml, $cells);
            $rows = [];
            foreach ($cells[1] as $idx) {
                $rows[] = isset($strings[$idx]) ? html_entity_decode($strings[$idx]) : "";
            }
            $text = implode(", ", $rows);
        }
        $zip->close();
    }
    return trim($text);
}

// -------------------------------------------------------
// 6. ALLOW ADDITIONAL FILE TYPES IN WORDPRESS MEDIA
// -------------------------------------------------------
add_filter("upload_mimes", function ($mimes) {
    $mimes["txt"]  = "text/plain";
    $mimes["csv"]  = "text/csv";
    $mimes["docx"] = "application/vnd.openxmlformats-officedocument.wordprocessingml.document";
    $mimes["doc"]  = "application/msword";
    $mimes["xlsx"] = "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet";
    return $mimes;
});

add_filter("wp_check_filetype_and_ext", function ($data, $file, $filename, $mimes) {
    $ext     = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    $allowed = ["txt", "csv", "doc", "docx", "xlsx"];
    if (in_array($ext, $allowed) && empty($data["ext"])) {
        $data["ext"]  = $ext;
        $data["type"] = $mimes[$ext] ?? "application/octet-stream";
    }
    return $data;
}, 10, 4);