<?php 
global $post;
$current_slug = $post->post_name;

/**
 * ----------------------------------------------------------------
 * Output for /endpoints/activity-log/
 * This provides all the activity log that was recorded
 * ----------------------------------------------------------------
 */

if ($current_slug == 'activity-log') :

	header('Content-Type: application/json; charset=utf-8');

	$webActivityArr = l4k_getActivityLog();
	echo json_encode($webActivityArr, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
	exit;

endif;

/**
 * ----------------------------------------------------------------
 * Output for /endpoints/activity-log-mobile/
 * This provides all the activity log mobile that was recorded
 * ----------------------------------------------------------------
 */

if ($current_slug == 'activity-log-mobile') :

	header('Content-Type: application/json; charset=utf-8');

	$mobileActivityArr = l4k_getActivityLogMobile();
	echo json_encode($mobileActivityArr, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
	exit;

endif;

/**
 * ----------------------------------------------------------------
 * Output for /endpoints/all-libraries/
 * This provides all the libraries
 * ----------------------------------------------------------------
 */

if ($current_slug == 'all-libraries') :

	header('Content-Type: application/json; charset=utf-8');

	$libraryArr = l4k_getLibraries(true);
	echo json_encode($libraryArr, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
	exit;

endif;

/**
 * ----------------------------------------------------------------
 * Output for /endpoints/mobile-app-version/
 * This provides the mobile app rating that was set in the WP CMS
 * ----------------------------------------------------------------
 */

if ($current_slug == 'mobile-app-version') :

	header('Content-Type: application/json; charset=utf-8');

	$androidArr = array('current_play_store_version_code' => get_field('android_version_code', 'option'), 
						'current_play_store_version_name' => get_field('android_version_name', 'option'));
	$iosArr 	= array('current_app_store_version' => get_field('ios_store_version', 'option'), 
						'current_app_store_build' 	=> get_field('ios_store_build', 'option'));
	$mobileArr 	= array('android' => $androidArr, 'ios' => $iosArr);						

	echo json_encode($mobileArr, JSON_PRETTY_PRINT);
	exit;

endif;

/**
 * ----------------------------------------------------------------
 * Output for /endpoints/all-books-by-language/
 * This provides all the list of books based on $_GET $languageId
 * ----------------------------------------------------------------
 */
if ($current_slug === 'all-books-by-language') :

    header('Content-Type: application/json; charset=utf-8');

    if (!isset($_GET['languageId'])) {
        http_response_code(400);
        echo json_encode([
            'error'   => 'missing_parameters',
            'message' => 'languageId parameter is required'
        ], JSON_PRETTY_PRINT);
        exit; 
    }

    $languageId = absint($_GET['languageId']);

    if ($languageId === 0) {
        http_response_code(400);
        echo json_encode([
            'error'   => 'invalid_parameter',
            'message' => 'languageId must be a valid ID'
        ], JSON_PRETTY_PRINT);
        exit;
    }

    $allBooksByLangArr = l4k_getBooks($languageId);

    $formattedBooks = array_map(function ($book) {
	    return [
	        'id'          	=> $book['book_id'],
	        'title' 		=> html_entity_decode($book['english_title'], ENT_QUOTES | ENT_HTML5, 'UTF-8'),
	        'image'       	=> $book['image_url'],
	        'filter_tags' 	=> $book['level_nicename'],
	    ];
	}, $allBooksByLangArr);


    echo json_encode($formattedBooks, JSON_PRETTY_PRINT);
    exit;

endif;

/**
 * ----------------------------------------------------------------
 * Output for /endpoints/book-details/
 * This provides the book details based on $_GET $bookId
 * ----------------------------------------------------------------
 */

if ($current_slug === 'book-details') :

    header('Content-Type: application/json; charset=utf-8');

    if (!isset($_GET['bookId'])) {
        http_response_code(400);
        echo json_encode([
            'error'   => 'missing_parameters',
            'message' => 'bookId parameter is required'
        ], JSON_PRETTY_PRINT);
        exit; 
    }

    $bookId = absint($_GET['bookId']);

    if ($bookId === 0) {
        http_response_code(400);
        echo json_encode([
            'error'   => 'invalid_parameter',
            'message' => 'bookId must be a valid ID'
        ], JSON_PRETTY_PRINT);
        exit;
    }

    $allBooksDetailsArr = l4k_getBookDetails($bookId);

    echo json_encode($allBooksDetailsArr, JSON_PRETTY_PRINT);
    exit;

endif;

/**
 * ----------------------------------------------------------------
 * Output for /endpoints/book-comments/
 * This provides the book comments based on $_GET $postId
 * ----------------------------------------------------------------
 */

if ($current_slug == 'book-comments') :

	header('Content-Type: application/json; charset=utf-8');

    if (!isset($_GET['post_id'])) {
        http_response_code(400);
        echo json_encode([
            'error'   => 'missing_parameters',
            'message' => 'post_id parameter is required'
        ], JSON_PRETTY_PRINT);
        exit;
    }

    $post_id = absint($_GET['post_id']);

    if (!$post_id) {
        http_response_code(400);
        echo json_encode([
            'error'   => 'invalid_parameter',
            'message' => 'post_id must be a valid ID'
        ], JSON_PRETTY_PRINT);
        exit;
    }

    $comments = l4k_getComments($post_id);
    echo json_encode($comments, JSON_PRETTY_PRINT);
    exit;

endif;

/**
 * ----------------------------------------------------------------
 * Output for /endpoints/mobile-notification/
 * This provides the mobile notification that was set in the WP CMS
 * ----------------------------------------------------------------
 */

if (isset($current_slug) && $current_slug === 'mobile-notification') :

    header('Content-Type: application/json; charset=utf-8');

    $required = ['library_name', 'device_id', 'os_type', 'device_type'];

    foreach ($required as $param) {
        if (empty($_GET[$param])) {
            http_response_code(400);
            echo json_encode([
                'error'   => 'missing_parameters',
                'message' => "{$param} parameter is required"
            ], JSON_PRETTY_PRINT);
            exit;
        }
    }

    $library_name = sanitize_text_field($_GET['library_name']);
    $device_id    = sanitize_text_field($_GET['device_id']);
    $os_type      = sanitize_text_field($_GET['os_type']);
    $device_type  = sanitize_text_field($_GET['device_type']);

    $mobileNotificationArr = l4k_getMobileAppNotification(
        $library_name,
        $device_id,
        $os_type,
        $device_type
    );

    echo json_encode($mobileNotificationArr, JSON_PRETTY_PRINT);
    exit;

endif;

/**
 * ----------------------------------------------------------------
 * Output for /endpoints/mobile-app-rating/
 * This provides the mobile app rating that was set in the WP CMS
 * ----------------------------------------------------------------
 */

if ($current_slug == 'mobile-app-rating') :

	header('Content-Type: application/json; charset=utf-8');

      $required = ['library_name', 'device_id', 'os_type', 'device_type'];

    foreach ($required as $param) {
        if (empty($_GET[$param])) {
            http_response_code(400);
            echo json_encode([
                'error'   => 'missing_parameters',
                'message' => "{$param} parameter is required"
            ], JSON_PRETTY_PRINT);
            exit;
        }
    }

    $library_name = sanitize_text_field($_GET['library_name']);
    $device_id    = sanitize_text_field($_GET['device_id']);
    $os_type      = sanitize_text_field($_GET['os_type']);
    $device_type  = sanitize_text_field($_GET['device_type']);

    $mobileAppRatingArr = l4k_getMobileAppRating($library_name, $device_id, $os_type, $device_type);
    echo json_encode($mobileAppRatingArr, JSON_PRETTY_PRINT);
	exit;

endif;

/**
 * ----------------------------------------------------------------
 * Output for /endpoints/all-languages/
 * This provides all the languages
 * ----------------------------------------------------------------
 */

if ($current_slug == 'all-languages') :

    header('Content-Type: application/json; charset=utf-8');

    $order = $_GET['order'] ?? 'ASC';  // default to ASC if not specified
    $search = $_GET['search'] ?? '';   // default to all if not specified
   
    //return l4k_getLanguages_legacy_format($search);
    $languageArr = l4k_getLanguages_legacy_format($search, $order);
    echo json_encode($languageArr, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;

endif;

/**
 * ----------------------------------------------------------------
 * Update for /endpoints/mobile-post-comment/
 * This provides the mobile post comment endpoint
 * ----------------------------------------------------------------
 */
if ($current_slug == 'mobile-post-comment') :

    header('Content-Type: application/json; charset=utf-8');

    // Allow only POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode([
            'error'   => 'method_not_allowed',
            'message' => 'POST method required'
        ], JSON_PRETTY_PRINT);
        exit;
    }

    // Get JSON or form data
    $raw = file_get_contents('php://input');
    $body = json_decode($raw, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        $body = $_POST; // fallback for form-encoded
    }

    // Call shared logic
    $response = l4k_updatePostComment_legacy($body);

    // Handle WP_Error
    if (is_wp_error($response)) {
        http_response_code($response->get_error_data()['status'] ?? 500);
        echo json_encode([
            'error'   => $response->get_error_code(),
            'message' => $response->get_error_message()
        ], JSON_PRETTY_PRINT);
        exit;
    }

    echo json_encode($response, JSON_PRETTY_PRINT);
    exit;

endif;


/**
 * ----------------------------------------------------------------
 * Output for /endpoints/mobile-submit-form/
 * This provides the mobile submit form endpoint
 * ----------------------------------------------------------------
 */
if ($current_slug === 'mobile-submit-form') :

    header('Content-Type: application/json; charset=utf-8');

    // Only allow POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode([
            'error'   => 'method_not_allowed',
            'message' => 'POST method required'
        ], JSON_PRETTY_PRINT);
        exit;
    }

    // Read JSON or form data
    $raw  = file_get_contents('php://input');
    $data = json_decode($raw, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        $data = $_POST; // fallback
    }

    $response = l4k_submitForm_legacy($data);

    if (is_wp_error($response)) {
        http_response_code($response->get_error_data()['status'] ?? 500);
        echo json_encode([
            'success' => false,
            'error'   => $response->get_error_code(),
            'message' => $response->get_error_message()
        ], JSON_PRETTY_PRINT);
        exit;
    }

    echo json_encode($response, JSON_PRETTY_PRINT);
    exit;

endif;

/**
 * ----------------------------------------------------------------
 * Mobile App User Login Logs
 * ----------------------------------------------------------------
 */

if ($current_slug === 'mobile-user-login-logs') :

    header('Content-Type: application/json; charset=utf-8');

    // Only allow POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode([
            'error'   => 'method_not_allowed',
            'message' => 'POST method required'
        ], JSON_PRETTY_PRINT);
        exit;
    }

    // Read JSON or form data
    $raw  = file_get_contents('php://input');
    $data = json_decode($raw, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        $data = $_POST; // fallback
    }

    $response = l4k_setUserLoginLogs_legacy($data);

    if (is_wp_error($response)) {
        http_response_code($response->get_error_data()['status'] ?? 500);
        echo json_encode([
            'success' => false,
            'error'   => $response->get_error_code(),
            'message' => $response->get_error_message()
        ], JSON_PRETTY_PRINT);
        exit;
    }

    echo json_encode($response, JSON_PRETTY_PRINT);
    exit;

endif;

/**
 * ----------------------------------------------------------------
 * Mobile App Activity Logs
 * ----------------------------------------------------------------
 */

if ($current_slug === 'mobile-activity-logs') :

    header('Content-Type: application/json; charset=utf-8');

    // Only allow POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode([
            'error'   => 'method_not_allowed',
            'message' => 'POST method required'
        ], JSON_PRETTY_PRINT);
        exit;
    }

    // Read JSON or form data
    $raw  = file_get_contents('php://input');
    $data = json_decode($raw, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        $data = $_POST; // fallback
    }

    $response = l4k_setMobileActivityLogs_legacy($data);

    if (is_wp_error($response)) {
        http_response_code($response->get_error_data()['status'] ?? 500);
        echo json_encode([
            'success' => false,
            'error'   => $response->get_error_code(),
            'message' => $response->get_error_message()
        ], JSON_PRETTY_PRINT);
        exit;
    }

    echo json_encode($response, JSON_PRETTY_PRINT);
    exit;

endif;

/**
 * ----------------------------------------------------------------
 * Endpoint /endpoints/record-mobile-activity/
 * Records mobile app activity
 * ----------------------------------------------------------------
 */

if ($current_slug == 'record-mobile-activity') :

    header('Content-Type: application/json; charset=utf-8');

    // ensure this is a POST request and required field is present
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['alert_code'])) {
        echo json_encode(['success' => false, 'error' => 'Invalid request or missing alert_code.']);
        exit;
    }

    // sanitize inputs
    $data = [
        'alert_code'   => sanitize_text_field($_POST['alert_code']),
        'barcode'      => strtoupper(sanitize_text_field($_POST['barcode'] ?? '')),
        'library_name' => sanitize_text_field($_POST['library_name'] ?? ''),
        'region_name'  => sanitize_text_field($_POST['library_region'] ?? ''),
        'data'         => sanitize_textarea_field($_POST['data'] ?? ''),
        'ip'           => sanitize_text_field($_POST['ip'] ?? ''),
        'time'         => current_time('mysql'),
        'os_type'      => sanitize_text_field($_POST['os_type'] ?? ''),
        'device_type'  => sanitize_text_field($_POST['device_type'] ?? ''),
        'status'       => sanitize_text_field($_POST['status'] ?? ''),
    ];

    // Insert record and return result
    $result = $wpdb->insert($wpdb->prefix . 'mobile_activity', $data);

    if ($result !== false) {
        echo json_encode(['success' => true, 'message' => 'Successfully added record ' . $wpdb->insert_id]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Unable to store record.']);
    }

    exit;

endif;


/**
 * ----------------------------------------------------------------
 * Endpoint /endpoints/process-scheduled-notifications/
 * Trigger scheduled push notifications (used by mobile or scheduler)
 * ----------------------------------------------------------------
 */
if ($current_slug === 'process-scheduled-notifications') :

    header('Content-Type: application/json; charset=utf-8');

    $secret = $_GET['secret'] ?? $_POST['secret'] ?? '';

    if (!defined('LFK_SCHEDULER_SECRET') || $secret !== LFK_SCHEDULER_SECRET) {
        http_response_code(403);
        echo json_encode([
            'success' => false,
            'error'   => 'unauthorized',
            'message' => 'Invalid or missing secret'
        ], JSON_PRETTY_PRINT);
        exit;
    }

    $response = lfk_process_scheduled_notifications_endpoint(null); 

    if ($response instanceof WP_REST_Response) {
        echo json_encode($response->get_data(), JSON_PRETTY_PRINT);
    } else {
        echo json_encode($response, JSON_PRETTY_PRINT);
    }

    exit;

endif;

/**
 * ----------------------------------------------------------------
 * Endpoint /endpoints/sync-notification-status/
 * Sync push notification status from Firebase
 * ----------------------------------------------------------------
 */
if ($current_slug === 'sync-notification-status') :

    header('Content-Type: application/json; charset=utf-8');

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode([
            'success' => false,
            'error'   => 'method_not_allowed'
        ]);
        exit;
    }

    $raw  = file_get_contents('php://input');
    $data = json_decode($raw, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode([
            'success' => false,
            'error'   => 'invalid_json'
        ]);
        exit;
    }

    // simulate WP_REST_Request for our endpoint function
    class Simulate_REST_Request {
        private $data;

        public function __construct($data) {
            $this->data = $data;
        }

        public function get_json_params() {
            return $this->data;
        }
    }

    $request = new Simulate_REST_Request($data);

    $response = lfk_sync_notification_status_endpoint($request);

    echo json_encode(
        $response instanceof WP_REST_Response
            ? $response->get_data()
            : $response
    );

    exit;

endif;

/**
 * ----------------------------------------------------------------
 * Output for /endpoints/playlist-mobile/
 * Returns level-grouped playlists for a language
 * ----------------------------------------------------------------
 */
if ($current_slug === 'playlist-mobile') :

    header('Content-Type: application/json; charset=utf-8');

    if (!isset($_GET['languageId'])) {
        http_response_code(400);
        echo json_encode([
            'error'   => 'missing_parameters',
            'message' => 'languageId parameter is required'
        ], JSON_PRETTY_PRINT);
        exit;
    }

    $languageId = absint($_GET['languageId']);

    if ($languageId === 0) {
        http_response_code(400);
        echo json_encode([
            'error'   => 'invalid_parameter',
            'message' => 'languageId must be valid'
        ], JSON_PRETTY_PRINT);
        exit;
    }

    $playlists = l4k_getPlaylistsForMobile($languageId);

    echo json_encode($playlists, JSON_PRETTY_PRINT);
    exit;

endif;

/**
 * ----------------------------------------------------------------
 * Output for /endpoints/playlist-books-mobile/
 * Returns books within a specific playlist based on playlistId
 * ----------------------------------------------------------------
 */
if ($current_slug === 'playlist-books-mobile') :

    header('Content-Type: application/json; charset=utf-8');

    if (!isset($_GET['playlistId'])) {
        http_response_code(400);
        echo json_encode([
            'error'   => 'missing_parameters',
            'message' => 'playlistId parameter is required'
        ], JSON_PRETTY_PRINT);
        exit;
    }

    $playlistId = absint($_GET['playlistId']);

    if ($playlistId === 0) {
        http_response_code(400);
        echo json_encode([
            'error'   => 'invalid_parameter',
            'message' => 'playlistId must be valid'
        ], JSON_PRETTY_PRINT);
        exit;
    }

    $books = l4k_getPlaylistBooksForMobile($playlistId);

    echo json_encode($books, JSON_PRETTY_PRINT);
    exit;

endif;

/**
 * ----------------------------------------------------------------
 * Output for /endpoints/reading-packs/
 * Returns reading pack books
 * ----------------------------------------------------------------
 */
if ($current_slug === 'reading-packs-mobile') :

    header('Content-Type: application/json; charset=utf-8');

    if (!isset($_GET['languageId']) || !isset($_GET['libraryId'])) {
        http_response_code(400);
        echo json_encode([
            'error'   => 'missing_parameters',
            'message' => 'languageId and libraryId are required'
        ], JSON_PRETTY_PRINT);
        exit;
    }

    $languageId = absint($_GET['languageId']);
    $libraryId  = absint($_GET['libraryId']);

    if (!$languageId || !$libraryId) {
        http_response_code(400);
        echo json_encode([
            'error'   => 'invalid_parameter',
            'message' => 'Invalid IDs provided'
        ], JSON_PRETTY_PRINT);
        exit;
    }

    $readingPacks = l4k_getReadingPacksforMobile($libraryId, $languageId);

    echo json_encode($readingPacks, JSON_PRETTY_PRINT);
    exit;

endif;
?>