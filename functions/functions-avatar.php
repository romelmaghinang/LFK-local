<?php
session_start(); 

$user_login_logs_table = $wpdb->prefix . 'user_login_logs';
$alternate_barcode_table = $wpdb->prefix . 'alternate_barcode';
$web_activity_table_name = $wpdb->prefix . 'web_activity';
$web_activity_meta_table_name = $wpdb->prefix . 'web_activity_meta';
$characters_table_name = $wpdb->prefix . 'characters';
$video_claims_table_name = $wpdb->prefix . 'video_claims';
$language_visits_table_name = $wpdb->prefix . 'language_visits';

$message_for_console = "No Bar Code in session.";
$owner_id_for_js = 'null';
$character_data_for_js = 'null';
$activity_count_for_js = 0;
$book_count_for_js = 0;
$days_count_for_js = 0;
$language_count_for_js = 0;
$reward_count_for_js = 0;

$firebase_config = [
    'apiKey' => "AIzaSyBH45cy69vJQcWKFMjcr1_WKe3Mq4YjURE",
    'authDomain' => "lote4kids-gamification.firebaseapp.com",
    'projectId' => "lote4kids-gamification",
    'storageBucket' => "lote4kids-gamification.firebasestorage.app",
    'messagingSenderId' => "782995887802",
    'appId' => "1:782995887802:web:5675aace9b5902f67c1483",
    'measurementId' => "G-KY9BEJK7LV"
];

$category_prices = [
    'headwear' => 1,
    'facewear' => 2,
    'tops' => 5,
    'bottoms' => 5,
    'shoes' => 3
];

// Valid language URLs - lazy-loaded via get_valid_language_urls() to avoid running before WP/ACF is ready
$valid_language_urls = [];

function get_valid_language_urls() {
    global $valid_language_urls;
    if (empty($valid_language_urls)) {
        $languages_data = l4k_getLanguages_legacy_format();
        if (!empty($languages_data) && is_array($languages_data)) {
            foreach ($languages_data as $lang) {
                if (!empty($lang['slug'])) {
                    $valid_language_urls[] = '/' . $lang['slug'] . '/';
                }
            }
        }
    }
    return $valid_language_urls;
}

function calculate_reward_count($days_count, $book_count, $activity_count, $language_count) {
    $reward_count = $days_count + $activity_count + $book_count + $language_count;
    return $reward_count;
}


if ($wpdb->get_var("SHOW TABLES LIKE '{$characters_table_name}'") != $characters_table_name) {
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE {$characters_table_name} (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        ownerId bigint(20) NOT NULL UNIQUE,
        boughtItems longtext DEFAULT NULL,
        gainPointsFrom longtext DEFAULT NULL,
        libraryCode varchar(255) DEFAULT '',
        libraryName varchar(255) DEFAULT '',
        points int(11) DEFAULT 100,
        activity_count int(11) DEFAULT 0,
        book_count int(11) DEFAULT 0,
        days_count int(11) DEFAULT 0,
        language_count int(11) DEFAULT 0,
        reward_count int(11) DEFAULT 0,
        last_login timestamp NULL DEFAULT NULL,
        secret varchar(255) DEFAULT 'someDefaultSecretValue',
        selectedAccessoryUrl varchar(255) DEFAULT '',
        selectedBottomUrl varchar(255) DEFAULT '',
        selectedHeadUrl varchar(255) DEFAULT '',
        selectedShoesUrl varchar(255) DEFAULT '',
        selectedTopUrl varchar(255) DEFAULT '',
        selectedLanguage varchar(10) DEFAULT 'en',
        PRIMARY KEY (id)
    ) {$charset_collate};";
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
    $message_for_console .= "Table '{$characters_table_name}' created or updated.";
} else {
    $activity_column_exists = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_SCHEMA = %s AND TABLE_NAME = %s AND COLUMN_NAME = 'activity_count'",
            DB_NAME,
            $characters_table_name
        )
    );
    
    if (empty($activity_column_exists)) {
        $wpdb->query("ALTER TABLE {$characters_table_name} ADD COLUMN activity_count int(11) DEFAULT 0 AFTER points");
        $message_for_console .= " Added 'activity_count' column to '{$characters_table_name}'.";
    }
    
    $book_column_exists = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_SCHEMA = %s AND TABLE_NAME = %s AND COLUMN_NAME = 'book_count'",
            DB_NAME,
            $characters_table_name
        )
    );
    
    if (empty($book_column_exists)) {
        $wpdb->query("ALTER TABLE {$characters_table_name} ADD COLUMN book_count int(11) DEFAULT 0 AFTER activity_count");
        $message_for_console .= " Added 'book_count' column to '{$characters_table_name}'.";
    }
    
    $days_column_exists = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_SCHEMA = %s AND TABLE_NAME = %s AND COLUMN_NAME = 'days_count'",
            DB_NAME,
            $characters_table_name
        )
    );
    
    if (empty($days_column_exists)) {
        $wpdb->query("ALTER TABLE {$characters_table_name} ADD COLUMN days_count int(11) DEFAULT 0 AFTER book_count");
        $message_for_console .= " Added 'days_count' column to '{$characters_table_name}'.";
    }
    
    $language_column_exists = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_SCHEMA = %s AND TABLE_NAME = %s AND COLUMN_NAME = 'language_count'",
            DB_NAME,
            $characters_table_name
        )
    );
    
    if (empty($language_column_exists)) {
        $wpdb->query("ALTER TABLE {$characters_table_name} ADD COLUMN language_count int(11) DEFAULT 0 AFTER days_count");
        $message_for_console .= " Added 'language_count' column to '{$characters_table_name}'.";
    }
    
    $reward_column_exists = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_SCHEMA = %s AND TABLE_NAME = %s AND COLUMN_NAME = 'reward_count'",
            DB_NAME,
            $characters_table_name
        )
    );
    
    if (empty($reward_column_exists)) {
        $wpdb->query("ALTER TABLE {$characters_table_name} ADD COLUMN reward_count int(11) DEFAULT 0 AFTER language_count");
        $message_for_console .= " Added 'reward_count' column to '{$characters_table_name}'.";
    }
    
    $last_login_column_exists = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_SCHEMA = %s AND TABLE_NAME = %s AND COLUMN_NAME = 'last_login'",
            DB_NAME,
            $characters_table_name
        )
    );
    
    if (empty($last_login_column_exists)) {
        $wpdb->query("ALTER TABLE {$characters_table_name} ADD COLUMN last_login timestamp NULL DEFAULT NULL AFTER reward_count");
        $message_for_console .= " Added 'last_login' column to '{$characters_table_name}'.";
    }
}

if ($wpdb->get_var("SHOW TABLES LIKE '{$language_visits_table_name}'") != $language_visits_table_name) {
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE {$language_visits_table_name} (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        ownerId bigint(20) NOT NULL,
        languageUrl varchar(255) NOT NULL,
        visitedAt datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY (id),
        UNIQUE KEY owner_language_unique (ownerId, languageUrl),
        KEY ownerId (ownerId)
    ) {$charset_collate};";
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
    $message_for_console .= " Table '{$language_visits_table_name}' created or updated.";
}

if ($wpdb->get_var("SHOW TABLES LIKE '{$video_claims_table_name}'") != $video_claims_table_name) {
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE {$video_claims_table_name} (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        ownerId bigint(20) NOT NULL,
        videoUrl varchar(255) NOT NULL,
        claimedAt datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY (id),
        UNIQUE KEY owner_video_unique (ownerId, videoUrl)
    ) {$charset_collate};";
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
    $message_for_console .= "Table '{$video_claims_table_name}' created or updated.";
}

if ($wpdb->get_var("SHOW TABLES LIKE '{$web_activity_table_name}'") != $web_activity_table_name) {
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE {$web_activity_table_name} (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        alert_code mediumint(9) NOT NULL,
        barcode varchar(255) DEFAULT '1080',
        library_name varchar(255) DEFAULT 'Demo Library',
        region_name varchar(255) DEFAULT 'US',
        ip varchar(255) NOT NULL,
        time datetime NOT NULL,
        PRIMARY KEY (id)
    ) {$charset_collate};";
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
    $message_for_console .= " Table '{$web_activity_table_name}' created or updated.";
}

if ($wpdb->get_var("SHOW TABLES LIKE '{$web_activity_meta_table_name}'") != $web_activity_meta_table_name) {
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE {$web_activity_meta_table_name} (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        web_activity_id mediumint(9) NOT NULL,
        `key` varchar(255) NOT NULL,
        `value` varchar(255) DEFAULT NULL,
        time datetime NOT NULL,
        PRIMARY KEY (id),
        KEY web_activity_id (web_activity_id)
    ) {$charset_collate};";
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
    $message_for_console .= " Table '{$web_activity_meta_table_name}' created or updated.";
}

if ($wpdb->get_var("SHOW TABLES LIKE '{$user_login_logs_table}'") != $user_login_logs_table) {
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE {$user_login_logs_table} (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        barcode varchar(255) NOT NULL,
        alert_code mediumint(9) NOT NULL,
        library_group varchar(255) DEFAULT '',
        region varchar(255) DEFAULT '',
        language varchar(255) DEFAULT '',
        time datetime NOT NULL,
        status varchar(255) DEFAULT '',
        PRIMARY KEY (id)
    ) {$charset_collate};";
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
    $message_for_console .= "Table '{$user_login_logs_table}' created or updated.";
}

if ($wpdb->get_var("SHOW TABLES LIKE '{$alternate_barcode_table}'") != $alternate_barcode_table) {
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE {$alternate_barcode_table} (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        barcode varchar(255) NOT NULL,
        PRIMARY KEY (id),
        UNIQUE KEY barcode (barcode)
    ) {$charset_collate};";
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
    $message_for_console .= "Table '{$alternate_barcode_table}' created or updated.";
}

if (isset($_SESSION['library_barcode'])) {
    $barcode_value = sanitize_text_field($_SESSION['library_barcode']);
    $matched_id = null;
    
    $library_region = isset($_SESSION['library_region_name']) ? sanitize_text_field($_SESSION['library_region_name']) : 'Unknown Region';
    $library_name_to_log = isset($_SESSION['library_code_name']) ? sanitize_text_field($_SESSION['library_code_name']) : 'Unknown Library';
    
    /*
    $sql_get_owner_id = $wpdb->prepare(
        "SELECT id, barcode FROM {$user_login_logs_table} WHERE barcode = %s ORDER BY id ASC LIMIT 1",
        $barcode_value
    );
    $existing_user_login_log_entry = $wpdb->get_row($sql_get_owner_id);
    */

   	// check first if in the user_login_logs there's barcode match with alert_code 900
	$sql_get_owner_id = $wpdb->prepare(
	    "SELECT id, barcode FROM {$user_login_logs_table} 
	     WHERE barcode = %s AND alert_code = 900 
	     ORDER BY id ASC LIMIT 1",
	    $barcode_value
	);
	$existing_user_login_log_entry = $wpdb->get_row($sql_get_owner_id);

	// if no match to the above query, search for the barcode match with the alert_code 800
	// this is for older entries when we still had no distinction between 800/900 and all entries default to 800
	if ( ! $existing_user_login_log_entry ) {
	    $sql_get_owner_id = $wpdb->prepare(
	        "SELECT id, barcode FROM {$user_login_logs_table} 
	         WHERE barcode = %s AND alert_code = 800 
	         ORDER BY id ASC LIMIT 1",
	        $barcode_value
	    );
	    $existing_user_login_log_entry = $wpdb->get_row($sql_get_owner_id);
	}

    if ($existing_user_login_log_entry) {
        $matched_id = $existing_user_login_log_entry->id;
        $message_for_console = "Barcode '{$barcode_value}' found in user_login_logs. Using ID: {$matched_id}.";
    } else {
        $inserted_log = $wpdb->insert(
            $user_login_logs_table,
            [
                'barcode' => $barcode_value,
                'alert_code' => 800,
                'library_group' => $library_name_to_log,
                'region' => $library_region,
                'language' => 'english',
                'time' => current_time('mysql'),
                'status' => 'active'
            ],
            ['%s', '%d', '%s', '%s', '%s', '%s', '%s']
        );
        if ($inserted_log) {
            $matched_id = $wpdb->insert_id;
            $message_for_console = "Barcode '{$barcode_value}' not found. A new entry was created in user_login_logs with ID: {$matched_id}.";
        } else {
            error_log("WordPress DB Error: Failed to insert new user_login_logs entry: " . $wpdb->last_error);
            $message_for_console = "Error creating new user_login_logs entry: " . $wpdb->last_error;
            $matched_id = null;
        }
    }

    if ($matched_id) {
        $_SESSION['ownerId'] = $matched_id;
        $owner_id_for_js = json_encode($matched_id);

        $existing_character = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$characters_table_name} WHERE ownerId = %d", $matched_id),
            ARRAY_A
        );

        if (!$existing_character) {
            $character_data = [
                'ownerId' => $matched_id,
                'boughtItems' => json_encode([]),
                'gainPointsFrom' => json_encode([]),
                'libraryCode' => '',
                'libraryName' => '',
                'points' => 2,
                'activity_count' => 0,
                'book_count' => 0,
                'days_count' => 0,
                'language_count' => 0,
                'reward_count' => 0,
                'last_login' => null,
                'secret' => 'someDefaultSecretValue',
                'selectedAccessoryUrl' => '',
                'selectedBottomUrl' => '',
                'selectedHeadUrl' => '',
                'selectedShoesUrl' => '',
                'selectedTopUrl' => '',
                'selectedLanguage' => 'en'
            ];

            $inserted = $wpdb->insert(
                $characters_table_name,
                $character_data,
                ['%d', '%s', '%s', '%s', '%s', '%d', '%d', '%d', '%d', '%d', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s']
            );

            if ($inserted) {
                $character_data['boughtItems'] = [];
                $character_data_for_js = json_encode($character_data);
                $_SESSION['activity_count'] = 0;
                $_SESSION['book_count'] = 0;
                $_SESSION['days_count'] = 0;
                $_SESSION['language_count'] = 0;
                $_SESSION['reward_count'] = 0;
                $activity_count_for_js = 0;
                $book_count_for_js = 0;
                $days_count_for_js = 0;
                $language_count_for_js = 0;
                $reward_count_for_js = 0;
                $message_for_console .= " New character created for ownerId: {$matched_id}.";
            } else {
                error_log("WordPress DB Error: Failed to insert new character: " . $wpdb->last_error);
                $message_for_console = "Error creating new character: " . $wpdb->last_error;
            }
        } else {
            $existing_character['boughtItems'] = json_decode($existing_character['boughtItems'] ?: '[]', true);
            $existing_character['gainPointsFrom'] = json_decode($existing_character['gainPointsFrom'] ?: '[]', true);

            if (!isset($existing_character['selectedLanguage']) || empty($existing_character['selectedLanguage'])) {
                $existing_character['selectedLanguage'] = 'en';
            }

            $activity_count = isset($existing_character['activity_count']) ? intval($existing_character['activity_count']) : 0;
            $book_count = isset($existing_character['book_count']) ? intval($existing_character['book_count']) : 0;
            $days_count = isset($existing_character['days_count']) ? intval($existing_character['days_count']) : 0;
            $language_count = isset($existing_character['language_count']) ? intval($existing_character['language_count']) : 0;
            
            $reward_count = calculate_reward_count($days_count, $book_count, $activity_count, $language_count);
            
            if (!isset($existing_character['reward_count']) || intval($existing_character['reward_count']) !== $reward_count) {
                $wpdb->update(
                    $characters_table_name,
                    ['reward_count' => $reward_count],
                    ['ownerId' => $matched_id],
                    ['%d'],
                    ['%d']
                );
                $existing_character['reward_count'] = $reward_count;
            }
            
            $_SESSION['activity_count'] = $activity_count;
            $_SESSION['book_count'] = $book_count;
            $_SESSION['days_count'] = $days_count;
            $_SESSION['language_count'] = $language_count;
            $_SESSION['reward_count'] = $reward_count;
            $activity_count_for_js = $activity_count;
            $book_count_for_js = $book_count;
            $days_count_for_js = $days_count;
            $language_count_for_js = $language_count;
            $reward_count_for_js = $reward_count;

            $character_data_for_js = json_encode($existing_character);
            $message_for_console .= " Existing character loaded for ownerId: {$matched_id}. Reward count: {$reward_count}.";
        }
    } else {
        $message_for_console = "Failed to determine or create an owner ID for barcode '{$barcode_value}'.";
    }
} else {
    $message_for_console = "No Bar Code in session. Please set \$_SESSION['library_barcode'] to load a character.";
}

function get_user_timezone_from_ip($ip_address) {
    try {
        $api_url = "http://ipapi.co/{$ip_address}/timezone/";
        $context = stream_context_create([
            'http' => [
                'timeout' => 5,
                'user_agent' => 'Mozilla/5.0 (compatible; WordPress)'
            ]
        ]);
        
        $timezone = @file_get_contents($api_url, false, $context);

        if ($timezone && $timezone !== 'undefined' && in_array(trim($timezone), timezone_identifiers_list())) {
            return trim($timezone);
        }

        $fallback_url = "http://worldtimeapi.org/api/ip/{$ip_address}";
        $response = @file_get_contents($fallback_url, false, $context);

        if ($response) {
            $data = json_decode($response, true);
            if (isset($data['timezone']) && in_array($data['timezone'], timezone_identifiers_list())) {
                return $data['timezone'];
            }
        }

        return 'UTC';

    } catch (Exception $e) {
        error_log("Timezone detection error: " . $e->getMessage());
        return 'UTC';
    }
}

function is_new_day_for_user($last_login, $user_timezone) {
    if (!$last_login) {
        return true; 
    }
    
    try {
        $user_tz = new DateTimeZone($user_timezone);
        $now = new DateTime('now', $user_tz);
        $last_login_date = new DateTime($last_login, new DateTimeZone('UTC'));
        $last_login_date->setTimezone($user_tz);
        
        $today = $now->format('Y-m-d');
        $last_login_day = $last_login_date->format('Y-m-d');
        
        return $today !== $last_login_day;
        
    } catch (Exception $e) {
        error_log("Date comparison error: " . $e->getMessage());
        return false;
    }
}

function extract_language_path($url) {
    $valid_language_urls = get_valid_language_urls();

    $parsed_url = parse_url($url);
    $path = isset($parsed_url['path']) ? $parsed_url['path'] : '';

    foreach ($valid_language_urls as $lang_url) {
        if (strpos($path, $lang_url) !== false) {
            return $lang_url;
        }
    }

    return null;
}

function update_reward_count($owner_id) {
    global $wpdb, $characters_table_name;
    
    $character = $wpdb->get_row(
        $wpdb->prepare("SELECT activity_count, book_count, days_count, language_count FROM {$characters_table_name} WHERE ownerId = %d", $owner_id),
        ARRAY_A
    );
    
    if (!$character) {
        return false;
    }
    
    $days_count = intval($character['days_count']);
    $book_count = intval($character['book_count']);
    $activity_count = intval($character['activity_count']);
    $language_count = intval($character['language_count']);
    
    $new_reward_count = calculate_reward_count($days_count, $book_count, $activity_count, $language_count);
    
    $updated = $wpdb->update(
        $characters_table_name,
        ['reward_count' => $new_reward_count],
        ['ownerId' => $owner_id],
        ['%d'],
        ['%d']
    );
    
    if ($updated !== false) {
        $_SESSION['reward_count'] = $new_reward_count;
        return $new_reward_count;
    }
    
    return false;
}

// POST REQUEST HANDLERS
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');

    if ($_POST['action'] === 'log_language_visit') {
        // NEW: Handle language page visit logging
        $owner_id = intval($_POST['ownerId']);
        $current_url = sanitize_url($_POST['currentUrl']);
        
        if (!$owner_id || empty($current_url)) {
            echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
            exit;
        }

        // Extract language path from URL
        $language_path = extract_language_path($current_url);
        
        if (!$language_path) {
            echo json_encode(['success' => false, 'message' => 'Invalid language URL']);
            exit;
        }

        // Check if this language URL has already been visited by this user
        $existing_visit = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT id FROM {$language_visits_table_name} WHERE ownerId = %d AND languageUrl = %s",
                $owner_id,
                $language_path
            )
        );

        if ($existing_visit) {
            echo json_encode([
                'success' => false, 
                'message' => 'Language URL already visited',
                'languagePath' => $language_path,
                'alreadyVisited' => true
            ]);
            exit;
        }

        // Get current language count
        $character = $wpdb->get_row(
            $wpdb->prepare("SELECT language_count FROM {$characters_table_name} WHERE ownerId = %d", $owner_id),
            ARRAY_A
        );

        if (!$character) {
            echo json_encode(['success' => false, 'message' => 'Character not found']);
            exit;
        }

        $current_language_count = intval($character['language_count']);
        $new_language_count = $current_language_count + 1;

        // Start transaction
        $wpdb->query('START TRANSACTION');

        try {
            // Update language count
            $updated = $wpdb->update(
                $characters_table_name,
                ['language_count' => $new_language_count],
                ['ownerId' => $owner_id],
                ['%d'],
                ['%d']
            );

            if ($updated === false) {
                throw new Exception('Failed to update language count');
            }

            // Record the language visit
            $visit_inserted = $wpdb->insert(
                $language_visits_table_name,
                [
                    'ownerId' => $owner_id,
                    'languageUrl' => $language_path,
                    'visitedAt' => current_time('mysql')
                ],
                ['%d', '%s', '%s']
            );

            if ($visit_inserted === false) {
                throw new Exception('Failed to record language visit');
            }

            // Update reward count
            $new_reward_count = update_reward_count($owner_id);

            // Commit transaction
            $wpdb->query('COMMIT');

            $_SESSION['language_count'] = $new_language_count;
            echo json_encode([
                'success' => true, 
                'newLanguageCount' => $new_language_count,
                'newRewardCount' => $new_reward_count,
                'languagePath' => $language_path,
                'message' => 'Language visit logged successfully'
            ]);

        } catch (Exception $e) {
            // Rollback transaction
            $wpdb->query('ROLLBACK');
            echo json_encode(['success' => false, 'message' => 'Transaction failed: ' . $e->getMessage()]);
        }
        exit;
    }

    if ($_POST['action'] === 'log_members_home_visit') {
        // Handle members home page visit logging for daily count
        $owner_id = intval($_POST['ownerId']);
        $current_url = sanitize_url($_POST['currentUrl']);
        
        if (!$owner_id || empty($current_url)) {
            echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
            exit;
        }

        // Check if URL contains /member-home/
        if (strpos($current_url, '/member-home/') === false) {
            echo json_encode(['success' => false, 'message' => 'Invalid URL for member home logging']);
            exit;
        }

        // Get current character data
        $character = $wpdb->get_row(
            $wpdb->prepare("SELECT days_count, last_login FROM {$characters_table_name} WHERE ownerId = %d", $owner_id),
            ARRAY_A
        );

        if (!$character) {
            echo json_encode(['success' => false, 'message' => 'Character not found']);
            exit;
        }

        // Get user's IP and timezone
        $user_ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $user_timezone = get_user_timezone_from_ip($user_ip);
        
        $current_days_count = intval($character['days_count']);
        $last_login = $character['last_login'];
        
        // Check if it's a new day
        if (is_new_day_for_user($last_login, $user_timezone)) {
            $new_days_count = $current_days_count + 1;
            
            // Update days count and last login
            $updated = $wpdb->update(
                $characters_table_name,
                [
                    'days_count' => $new_days_count,
                    'last_login' => current_time('mysql', true) // UTC time
                ],
                ['ownerId' => $owner_id],
                ['%d', '%s'],
                ['%d']
            );

            if ($updated !== false) {
                // Update reward count
                $new_reward_count = update_reward_count($owner_id);
                
                $_SESSION['days_count'] = $new_days_count;
                echo json_encode([
                    'success' => true, 
                    'newDaysCount' => $new_days_count,
                    'newRewardCount' => $new_reward_count,
                    'isNewDay' => true,
                    'userTimezone' => $user_timezone,
                    'message' => 'New day logged successfully'
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update days count: ' . $wpdb->last_error]);
            }
        } else {
            // Same day, no increment
            echo json_encode([
                'success' => true, 
                'newDaysCount' => $current_days_count,
                'isNewDay' => false,
                'userTimezone' => $user_timezone,
                'message' => 'Same day visit, count not incremented'
            ]);
        }
        exit;
    }

    if ($_POST['action'] === 'log_activity_visit') {
        // Handle activity page visit logging
        $owner_id = intval($_POST['ownerId']);
        $current_url = sanitize_url($_POST['currentUrl']);
        
        if (!$owner_id || empty($current_url)) {
            echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
            exit;
        }

		if (
			(strpos($current_url, '/activities/') === false || preg_match('/\/activities\/$/', $current_url))
			&&
			!preg_match('#/aiovg_videos/[^/]+#', $current_url)
		) {
			echo json_encode(['success' => false, 'message' => 'Invalid URL for activity logging']);
			exit;
		}

        // Get current activity count
        $character = $wpdb->get_row(
            $wpdb->prepare("SELECT activity_count, points FROM {$characters_table_name} WHERE ownerId = %d", $owner_id),
            ARRAY_A
        );

        if (!$character) {
            echo json_encode(['success' => false, 'message' => 'Character not found']);
            exit;
        }

        $current_activity_count = intval($character['activity_count']);
        $new_activity_count = $current_activity_count + 1;
        $new_points = intval($character['points']) + 1;

        // Update activity count and points
        $updated = $wpdb->update(
            $characters_table_name,
            ['activity_count' => $new_activity_count, 'points' => $new_points],
            ['ownerId' => $owner_id],
            ['%d', '%d'],
            ['%d']
        );

        if ($updated !== false) {
            // Update reward count
            $new_reward_count = update_reward_count($owner_id);

            $_SESSION['activity_count'] = $new_activity_count;
            echo json_encode([
                'success' => true,
                'newActivityCount' => $new_activity_count,
                'newRewardCount' => $new_reward_count,
                'newPoints' => $new_points,
                'message' => 'Activity visit logged successfully'
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update activity count: ' . $wpdb->last_error]);
        }
        exit;
    }

    if ($_POST['action'] === 'log_book_visit') {
        // Handle book page visit logging
        $owner_id = intval($_POST['ownerId']);
        $current_url = sanitize_url($_POST['currentUrl']);
        
        if (!$owner_id || empty($current_url)) {
            echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
            exit;
        }

        if (strpos($current_url, '/aiovg_videos/') === false || preg_match('/\/aiovg_videos\/$/', $current_url)) {
            echo json_encode(['success' => false, 'message' => 'Invalid URL for book logging']);
            exit;
        }

        // Get current book count
        $character = $wpdb->get_row(
            $wpdb->prepare("SELECT book_count, points FROM {$characters_table_name} WHERE ownerId = %d", $owner_id),
            ARRAY_A
        );

        if (!$character) {
            echo json_encode(['success' => false, 'message' => 'Character not found']);
            exit;
        }

        $current_book_count = intval($character['book_count']);
        $new_book_count = $current_book_count + 1;
        $new_points = intval($character['points']) + 1;

        // Update book count and points
        $updated = $wpdb->update(
            $characters_table_name,
            ['book_count' => $new_book_count, 'points' => $new_points],
            ['ownerId' => $owner_id],
            ['%d', '%d'],
            ['%d']
        );

        if ($updated !== false) {
            // Update reward count
            $new_reward_count = update_reward_count($owner_id);

            $_SESSION['book_count'] = $new_book_count;
            echo json_encode([
                'success' => true,
                'newBookCount' => $new_book_count,
                'newRewardCount' => $new_reward_count,
                'newPoints' => $new_points,
                'message' => 'Book visit logged successfully'
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update book count: ' . $wpdb->last_error]);
        }
        exit;
    }

    // if ($_POST['action'] === 'log_1080_activity') {
    //     // Handle 1080 logging via POST request
    //     $owner_id = intval($_POST['ownerId']);
    //     $current_url = sanitize_url($_POST['currentUrl']);
    
    //     if (!$owner_id || empty($current_url)) {
    //         echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    //         exit;
    //     }

    //     // Check if URL contains /avatar-dress-up or /avatar-dress-up/
    //     if (strpos($current_url, '/avatar-dress-up') === false) {
    //         echo json_encode(['success' => false, 'message' => 'Invalid URL for 1080 logging']);
    //         exit;
    //     }

    //     // Get barcode from session or user_login_logs
    //     $barcode_value = '1080'; // Default
    //     if (isset($_SESSION['barCode'])) {
    //         $barcode_value = sanitize_text_field($_SESSION['barCode']);
    //     } else {
    //         // Try to get barcode from user_login_logs using owner_id
    //         $user_log = $wpdb->get_row(
    //             $wpdb->prepare("SELECT barcode FROM {$user_login_logs_table} WHERE id = %d", $owner_id)
    //         );
    //         if ($user_log) {
    //             $barcode_value = $user_log->barcode;
    //         }
    //     }

    //     // Get library info
    //     $library_region = isset($_SESSION['library_region_name']) ? sanitize_text_field($_SESSION['library_region_name']) : 'US';
    //     $library_name_to_log = isset($_SESSION['library_code_name']) ? sanitize_text_field($_SESSION['library_code_name']) : 'Demo Library';

    //     // Get character data for library info if available
    //     $character = $wpdb->get_row(
    //         $wpdb->prepare("SELECT libraryName FROM {$characters_table_name} WHERE ownerId = %d", $owner_id),
    //         ARRAY_A
    //     );
    //     if ($character && !empty($character['libraryName'])) {
    //         $library_name_to_log = $character['libraryName'];
    //     }

    //     $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
    //     $log_data = [
    //         'alert_code' => 1080,
    //         'barcode' => $barcode_value,
    //         'library_name' => $library_name_to_log,
    //         'region_name' => $library_region,
    //         'ip' => $ip_address,
    //         'time' => current_time('mysql')
    //     ];

    //     $inserted_web_activity = $wpdb->insert(
    //         $web_activity_table_name,
    //         $log_data,
    //         ['%d', '%s', '%s', '%s', '%s', '%s']
    //     );

    //     if ($inserted_web_activity) {
    //         $web_activity_id = $wpdb->insert_id;
            
    //         // Insert meta data for the web activity
    //         $meta_entries = [
    //             ['key' => 'Activity Name', 'value' => 'Avatar Dress Up'],
    //             ['key' => 'Activity Title', 'value' => 'Gamification'],
    //             ['key' => 'Activity Type', 'value' => 'Avatar Dress Up']
    //         ];

    //         foreach ($meta_entries as $meta) {
    //             $wpdb->insert(
    //                 $web_activity_meta_table_name,
    //                 [
    //                     'web_activity_id' => $web_activity_id,
    //                     'key' => $meta['key'],
    //                     'value' => $meta['value'],
    //                     'time' => current_time('mysql')
    //                 ],
    //                 ['%d', '%s', '%s', '%s']
    //             );
    //         }

    //         echo json_encode([
    //             'success' => true, 
    //             'message' => 'Successfully logged web activity 1080',
    //             'web_activity_id' => $web_activity_id,
    //             'barcode' => $barcode_value
    //         ]);
    //     } else {
    //         error_log("WordPress DB Error: Failed to insert web_activity 1080 entry: " . $wpdb->last_error);
    //         echo json_encode(['success' => false, 'message' => 'Failed to log web activity: ' . $wpdb->last_error]);
    //     }
    //     exit;
    // }

    if ($_POST['action'] === 'purchase_item') {
        $owner_id = intval($_POST['ownerId']);
        $item_url = sanitize_url($_POST['itemUrl']);
        $category = sanitize_text_field($_POST['category']);

        $item_cost = isset($category_prices[$category]) ? $category_prices[$category] : 5;

        if (!$owner_id || empty($item_url) || empty($category)) {
            echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
            exit;
        }

        $character = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$characters_table_name} WHERE ownerId = %d", $owner_id),
            ARRAY_A
        );

        if (!$character) {
            echo json_encode(['success' => false, 'message' => 'Character not found']);
            exit;
        }

        if ($character['points'] < $item_cost) {
            echo json_encode(['success' => false, 'message' => 'Not enough points']);
            exit;
        }

        $bought_items = json_decode($character['boughtItems'] ?: '[]', true);

        if (in_array($item_url, $bought_items)) {
            echo json_encode(['success' => false, 'message' => 'Item already purchased']);
            exit;
        }

        $bought_items[] = $item_url;
        $new_points = $character['points'] - $item_cost;
        $bought_items_json = json_encode($bought_items);

        $updated = $wpdb->update(
            $characters_table_name,
            ['boughtItems' => $bought_items_json, 'points' => $new_points],
            ['ownerId' => $owner_id],
            ['%s', '%d'],
            ['%d']
        );

        if ($updated !== false) {
            echo json_encode(['success' => true, 'newPoints' => $new_points, 'message' => 'Item purchased successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Purchase failed: ' . $wpdb->last_error]);
        }
        exit;
    }

    if ($_POST['action'] === 'save_avatar') {
        $owner_id = intval($_POST['ownerId']);
        $selections = isset($_POST['selections']) ? (array) $_POST['selections'] : [];

        if (!$owner_id) {
            echo json_encode(['success' => false, 'message' => 'Missing owner ID']);
            exit;
        }

        $update_data = [];
        $update_format = [];

        $update_data['selectedHeadUrl'] = sanitize_url($selections['headwear'] ?? '');
        $update_data['selectedTopUrl'] = sanitize_url($selections['tops'] ?? '');
        $update_data['selectedBottomUrl'] = sanitize_url($selections['bottoms'] ?? '');
        $update_data['selectedShoesUrl'] = sanitize_url($selections['shoes'] ?? '');
        $update_data['selectedAccessoryUrl'] = sanitize_url($selections['facewear'] ?? '');

        $update_format = ['%s', '%s', '%s', '%s', '%s'];

        $updated = $wpdb->update(
            $characters_table_name,
            $update_data,
            ['ownerId' => $owner_id],
            $update_format,
            ['%d']
        );

        if ($updated !== false) {
            echo json_encode(['success' => true, 'message' => 'Avatar saved successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Save failed: ' . $wpdb->last_error]);
        }
        exit;
    }

    if ($_POST['action'] === 'save_language') {
        $owner_id = intval($_POST['ownerId']);
        $language_code = sanitize_text_field($_POST['languageCode']);

        if (!$owner_id || empty($language_code)) {
            echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
            exit;
        }

        $updated = $wpdb->update(
            $characters_table_name,
            ['selectedLanguage' => $language_code],
            ['ownerId' => $owner_id],
            ['%s'],
            ['%d']
        );

        if ($updated !== false) {
            echo json_encode(['success' => true, 'message' => 'Language saved successfully', 'selectedLanguage' => $language_code]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to save language: ' . $wpdb->last_error]);
        }
        exit;
    }

    /*
    if ($_POST['action'] === 'aiovg_claim_video_points') {
        $owner_id = intval($_POST['ownerId']);
        $video_url = sanitize_url($_POST['videoUrl']);

        if (!$owner_id || empty($video_url)) {
            echo json_encode(['success' => false, 'message' => 'Missing owner ID or video URL.']);
            exit;
        }

        $character = $wpdb->get_row(
            $wpdb->prepare("SELECT points FROM {$characters_table_name} WHERE ownerId = %d", $owner_id),
            ARRAY_A
        );

        if (!$character) {
            echo json_encode(['success' => false, 'message' => 'Character not found.']);
            wp_die();
        }

        $current_points = intval($character['points']);
        $points_to_add = 1;
        $new_points = $current_points + $points_to_add;

        $updated_points = $wpdb->update(
            $characters_table_name,
            ['points' => $new_points],
            ['ownerId' => $owner_id],
            ['%d'],
            ['%d']
        );

        if ($updated_points === false) {
            echo json_encode(['success' => false, 'message' => 'Failed to update points: ' . $wpdb->last_error]);
            wp_die();
        }

        $wpdb->insert(
            $video_claims_table_name,
            [
                'ownerId' => $owner_id,
                'videoUrl' => $video_url,
                'claimedAt' => current_time('mysql')
            ],
            ['%d', '%s', '%s']
        );

        echo json_encode([
            'success' => true, 
            'newPoints' => $new_points,
            'message' => 'Feather Added +1'
        ]);
        wp_die();
    }
    */

    if ($_POST['action'] === 'get_firebase_config') {
    	/*
        if (!$owner_id_for_js || $owner_id_for_js === 'null') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }
        */
        echo json_encode(['success' => true, 'config' => $firebase_config]);
        exit;
    }
}

function enqueue_avatar_creator_data() {
    global $message_for_console, $owner_id_for_js, $character_data_for_js, $activity_count_for_js, $book_count_for_js, $days_count_for_js, $language_count_for_js, $reward_count_for_js;

    echo '<script>';
    echo 'window.phpData = {';
    echo 'messageForConsole: ' . json_encode($message_for_console) . ',';
    echo 'ownerIdFromPHP: ' . $owner_id_for_js . ',';
    echo 'characterDataFromPHP: ' . $character_data_for_js . ',';
    echo 'activityCount: ' . json_encode($activity_count_for_js) . ',';
    echo 'bookCount: ' . json_encode($book_count_for_js) . ',';
    echo 'daysCount: ' . json_encode($days_count_for_js) . ',';
    echo 'languageCount: ' . json_encode($language_count_for_js) . ',';
    echo 'rewardCount: ' . json_encode($reward_count_for_js) . ',';
    echo 'ajaxUrl: ' . json_encode(admin_url('admin-ajax.php')) . '';
    echo '};';
    echo '</script>';
}

add_action('wp_footer', 'enqueue_avatar_creator_data', 5);

function enqueue_avatar_creator_scripts() {
    wp_register_script('firebase-app', 'https://www.gstatic.com/firebasejs/11.9.0/firebase-app.js', [], null, true);
    wp_register_script('firebase-storage', 'https://www.gstatic.com/firebasejs/11.9.0/firebase-storage.js', ['firebase-app'], null, true);
    wp_register_script('firebase-firestore', 'https://www.gstatic.com/firebasejs/11.9.0/firebase-firestore.js', ['firebase-app'], null, true);
    wp_enqueue_script('sweetalert2', 'https://cdn.jsdelivr.net/npm/sweetalert2@11', [], '11.10.1', true);
    wp_enqueue_script('avatar-creator-script', get_stylesheet_directory_uri() . '/assets/js/avatar-creator.js?'.time(), ['firebase-storage', 'firebase-firestore', 'sweetalert2'], null, true);
    wp_enqueue_script('streaks-script', get_stylesheet_directory_uri() . '/assets/js/streaks.js?' . time(), [], null, true );
    add_action('wp_footer', function() {
        echo '<script>console.log("🚀 Avatar Creator Script (Lazy Loading) loaded on:", window.location.href);</script>';
    }, 1);
}

add_action('wp_enqueue_scripts', 'enqueue_avatar_creator_scripts');

function add_type_attribute_to_scripts($tag, $handle, $src) {
    if ('avatar-creator-script' === $handle || 'activities-script' === $handle) {
        $tag = '<script type="module" src="' . esc_url($src) . '"></script>';
    }
    if ('firebase-app' === $handle || 'firebase-storage' === $handle || 'firebase-firestore' === $handle) {
        $tag = '<script type="module" src="' . esc_url($src) . '"></script>';
    }
    return $tag;
}
add_filter('script_loader_tag', 'add_type_attribute_to_scripts', 10, 3);

add_action('wp_ajax_log_language_visit', 'log_language_visit_callback');
add_action('wp_ajax_nopriv_log_language_visit', 'log_language_visit_callback');
add_action('wp_ajax_log_members_home_visit', 'log_members_home_visit_callback');
add_action('wp_ajax_nopriv_log_members_home_visit', 'log_members_home_visit_callback');
add_action('wp_ajax_log_activity_visit', 'log_activity_visit_callback');
add_action('wp_ajax_nopriv_log_activity_visit', 'log_activity_visit_callback');
add_action('wp_ajax_log_sidebar_activity', 'log_sidebar_activity_callback');
add_action('wp_ajax_nopriv_log_sidebar_activity', 'log_sidebar_activity_callback');
add_action('wp_ajax_log_book_visit', 'log_book_visit_callback');
add_action('wp_ajax_nopriv_log_book_visit', 'log_book_visit_callback');
//add_action('wp_ajax_log_1080_activity', 'log_1080_activity_callback');
//add_action('wp_ajax_nopriv_log_1080_activity', 'log_1080_activity_callback');
//add_action('wp_ajax_aiovg_claim_video_points', 'aiovg_claim_video_points_callback');
//add_action('wp_ajax_nopriv_aiovg_claim_video_points', 'aiovg_claim_video_points_callback');
add_action('wp_ajax_get_firebase_config', 'get_firebase_config_callback');
add_action('wp_ajax_nopriv_get_firebase_config', 'get_firebase_config_callback');

function log_language_visit_callback() {
    global $wpdb;
    $valid_language_urls = get_valid_language_urls();
    $characters_table_name = $wpdb->prefix . 'characters';
    $language_visits_table_name = $wpdb->prefix . 'language_visits';
    
    if (ob_get_level()) {
        ob_clean();
    }
    
    header('Content-Type: application/json');
    
    $owner_id = intval($_POST['ownerId']);
    $current_url = sanitize_url($_POST['currentUrl']);
    
    if (!$owner_id || empty($current_url)) {
        echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
        wp_die();
    }

    // Extract language path from URL
    $parsed_url = parse_url($current_url);
    $path = isset($parsed_url['path']) ? $parsed_url['path'] : '';
    $language_path = null;
    
    foreach ($valid_language_urls as $lang_url) {
        if (strpos($path, $lang_url) !== false) {
            $language_path = $lang_url;
            break;
        }
    }
    
    if (!$language_path) {
        echo json_encode(['success' => false, 'message' => 'Invalid language URL']);
        wp_die();
    }

    // Check if this language URL has already been visited by this user
    $existing_visit = $wpdb->get_row(
        $wpdb->prepare(
            "SELECT id FROM {$language_visits_table_name} WHERE ownerId = %d AND languageUrl = %s",
            $owner_id,
            $language_path
        )
    );

    if ($existing_visit) {
        echo json_encode([
            'success' => false, 
            'message' => 'Language URL already visited',
            'languagePath' => $language_path,
            'alreadyVisited' => true
        ]);
        wp_die();
    }

    // Get current language count
    $character = $wpdb->get_row(
        $wpdb->prepare("SELECT language_count FROM {$characters_table_name} WHERE ownerId = %d", $owner_id),
        ARRAY_A
    );

    if (!$character) {
        echo json_encode(['success' => false, 'message' => 'Character not found']);
        wp_die();
    }

    $current_language_count = intval($character['language_count']);
    $new_language_count = $current_language_count + 1;

    // Start transaction
    $wpdb->query('START TRANSACTION');

    try {
        // Update language count
        $updated = $wpdb->update(
            $characters_table_name,
            ['language_count' => $new_language_count],
            ['ownerId' => $owner_id],
            ['%d'],
            ['%d']
        );

        if ($updated === false) {
            throw new Exception('Failed to update language count');
        }

        // Record the language visit
        $visit_inserted = $wpdb->insert(
            $language_visits_table_name,
            [
                'ownerId' => $owner_id,
                'languageUrl' => $language_path,
                'visitedAt' => current_time('mysql')
            ],
            ['%d', '%s', '%s']
        );

        if ($visit_inserted === false) {
            throw new Exception('Failed to record language visit');
        }

        // Update reward count
        $new_reward_count = update_reward_count($owner_id);

        // Commit transaction
        $wpdb->query('COMMIT');

        $_SESSION['language_count'] = $new_language_count;
        echo json_encode([
            'success' => true, 
            'newLanguageCount' => $new_language_count,
            'newRewardCount' => $new_reward_count,
            'languagePath' => $language_path,
            'message' => 'Language visit logged successfully'
        ]);

    } catch (Exception $e) {
        // Rollback transaction
        $wpdb->query('ROLLBACK');
        echo json_encode(['success' => false, 'message' => 'Transaction failed: ' . $e->getMessage()]);
    }
    wp_die();
}

function log_members_home_visit_callback() {
    global $wpdb;
    $characters_table_name = $wpdb->prefix . 'characters';
    
    if (ob_get_level()) {
        ob_clean();
    }
    
    header('Content-Type: application/json');
    
    $owner_id = intval($_POST['ownerId']);
    $current_url = sanitize_url($_POST['currentUrl']);
    
    if (!$owner_id || empty($current_url)) {
        echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
        wp_die();
    }

    // Check if URL contains /member-home/
    if (strpos($current_url, '/member-home/') === false) {
        echo json_encode(['success' => false, 'message' => 'Invalid URL for members home logging']);
        wp_die();
    }

    // Get current character data
    $character = $wpdb->get_row(
        $wpdb->prepare("SELECT days_count, last_login FROM {$characters_table_name} WHERE ownerId = %d", $owner_id),
        ARRAY_A
    );

    if (!$character) {
        echo json_encode(['success' => false, 'message' => 'Character not found']);
        wp_die();
    }

    // Get user's IP and timezone
    $user_ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    
    // Function to get user timezone from IP
    function get_user_timezone_from_ip_callback($ip_address) {
        try {
            // Use ipapi.co for timezone detection
            $api_url = "http://ipapi.co/{$ip_address}/timezone/";
            $context = stream_context_create([
                'http' => [
                    'timeout' => 5,
                    'user_agent' => 'Mozilla/5.0 (compatible; WordPress)'
                ]
            ]);
            
            $timezone = @file_get_contents($api_url, false, $context);

            if ($timezone && $timezone !== 'undefined' && in_array(trim($timezone), timezone_identifiers_list())) {
                return trim($timezone);
            }

            // Fallback to worldtimeapi.org
            $fallback_url = "http://worldtimeapi.org/api/ip/{$ip_address}";
            $response = @file_get_contents($fallback_url, false, $context);

            if ($response) {
                $data = json_decode($response, true);
                if (isset($data['timezone']) && in_array($data['timezone'], timezone_identifiers_list())) {
                    return $data['timezone'];
                }
            }
            
            // Default fallback
            return 'UTC';
            
        } catch (Exception $e) {
            error_log("Timezone detection error: " . $e->getMessage());
            return 'UTC';
        }
    }
    
    // Function to check if it's a new day for the user
    function is_new_day_for_user_callback($last_login, $user_timezone) {
        if (!$last_login) {
            return true; // First time login
        }
        
        try {
            $user_tz = new DateTimeZone($user_timezone);
            $now = new DateTime('now', $user_tz);
            $last_login_date = new DateTime($last_login, new DateTimeZone('UTC'));
            $last_login_date->setTimezone($user_tz);
            
            $today = $now->format('Y-m-d');
            $last_login_day = $last_login_date->format('Y-m-d');
            
            return $today !== $last_login_day;
            
        } catch (Exception $e) {
            error_log("Date comparison error: " . $e->getMessage());
            return false;
        }
    }
    
    $user_timezone = get_user_timezone_from_ip_callback($user_ip);
    
    $current_days_count = intval($character['days_count']);
    $last_login = $character['last_login'];
    
    // Check if it's a new day
    if (is_new_day_for_user_callback($last_login, $user_timezone)) {
        $new_days_count = $current_days_count + 1;
        
        // Update days count and last login
        $updated = $wpdb->update(
            $characters_table_name,
            [
                'days_count' => $new_days_count,
                'last_login' => current_time('mysql', true) // UTC time
            ],
            ['ownerId' => $owner_id],
            ['%d', '%s'],
            ['%d']
        );

        if ($updated !== false) {
            // Update reward count
            $new_reward_count = update_reward_count($owner_id);
            
            $_SESSION['days_count'] = $new_days_count;
            echo json_encode([
                'success' => true, 
                'newDaysCount' => $new_days_count,
                'newRewardCount' => $new_reward_count,
                'isNewDay' => true,
                'userTimezone' => $user_timezone,
                'message' => 'New day logged successfully'
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update days count: ' . $wpdb->last_error]);
        }
    } else {
        // Same day, no increment
        echo json_encode([
            'success' => true, 
            'newDaysCount' => $current_days_count,
            'isNewDay' => false,
            'userTimezone' => $user_timezone,
            'message' => 'Same day visit, count not incremented'
        ]);
    }
    wp_die();
}

function log_activity_visit_callback() {
    global $wpdb;
    $characters_table_name = $wpdb->prefix . 'characters';
    
    if (ob_get_level()) {
        ob_clean();
    }
    
    header('Content-Type: application/json');
    
    $owner_id = intval($_POST['ownerId']);
    $current_url = sanitize_url($_POST['currentUrl']);
    
    if (!$owner_id || empty($current_url)) {
        echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
        wp_die();
    }

    // Check if URL contains /activities/ and has content after it (not just /activities/)
    if (strpos($current_url, '/activities/') === false || preg_match('/\/activities\/$/', $current_url)) {
        echo json_encode(['success' => false, 'message' => 'Invalid URL for activity logging']);
        wp_die();
    }

    // Get current activity count
    $character = $wpdb->get_row(
        $wpdb->prepare("SELECT activity_count, points FROM {$characters_table_name} WHERE ownerId = %d", $owner_id),
        ARRAY_A
    );

    if (!$character) {
        echo json_encode(['success' => false, 'message' => 'Character not found']);
        wp_die();
    }

    $current_activity_count = intval($character['activity_count']);
    $new_activity_count = $current_activity_count + 1;
    $new_points = intval($character['points']) + 1;

    // Update activity count and points
    $updated = $wpdb->update(
        $characters_table_name,
        ['activity_count' => $new_activity_count, 'points' => $new_points],
        ['ownerId' => $owner_id],
        ['%d', '%d'],
        ['%d']
    );

    if ($updated !== false) {
        // Update reward count
        $new_reward_count = update_reward_count($owner_id);

        $_SESSION['activity_count'] = $new_activity_count;
        echo json_encode([
            'success' => true,
            'newActivityCount' => $new_activity_count,
            'newRewardCount' => $new_reward_count,
            'newPoints' => $new_points,
            'message' => 'Activity visit logged successfully'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update activity count: ' . $wpdb->last_error]);
    }
    wp_die();
}

function log_sidebar_activity_callback() {
    global $wpdb;
    $characters_table_name = $wpdb->prefix . 'characters';

    if (ob_get_level()) {
        ob_clean();
    }

    header('Content-Type: application/json');

    $owner_id = intval($_POST['ownerId']);

    if (!$owner_id) {
        echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
        wp_die();
    }

    // Get current activity count
    $character = $wpdb->get_row(
        $wpdb->prepare("SELECT activity_count FROM {$characters_table_name} WHERE ownerId = %d", $owner_id),
        ARRAY_A
    );

    if (!$character) {
        echo json_encode(['success' => false, 'message' => 'Character not found']);
        wp_die();
    }

    $current_activity_count = intval($character['activity_count']);
    $new_activity_count = $current_activity_count + 1;

    // Update activity count only (no points/feathers)
    $updated = $wpdb->update(
        $characters_table_name,
        ['activity_count' => $new_activity_count],
        ['ownerId' => $owner_id],
        ['%d'],
        ['%d']
    );

    if ($updated !== false) {
        $new_reward_count = update_reward_count($owner_id);

        $_SESSION['activity_count'] = $new_activity_count;
        echo json_encode([
            'success' => true,
            'newActivityCount' => $new_activity_count,
            'newRewardCount' => $new_reward_count,
            'message' => 'Sidebar activity logged successfully'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update activity count: ' . $wpdb->last_error]);
    }
    wp_die();
}

function log_book_visit_callback() {
    global $wpdb;
    $characters_table_name = $wpdb->prefix . 'characters';
    
    if (ob_get_level()) {
        ob_clean();
    }
    
    header('Content-Type: application/json');
    
    $owner_id = intval($_POST['ownerId']);
    $current_url = sanitize_url($_POST['currentUrl']);
    
    if (!$owner_id || empty($current_url)) {
        echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
        wp_die();
    }

    // Check if URL contains /aiovg_videos/ and has content after it (not just /aiovg_videos/)
    if (strpos($current_url, '/aiovg_videos/') === false || preg_match('/\/aiovg_videos\/$/', $current_url)) {
        echo json_encode(['success' => false, 'message' => 'Invalid URL for book logging']);
        wp_die();
    }

    // Get current book count
    $character = $wpdb->get_row(
        $wpdb->prepare("SELECT book_count, points FROM {$characters_table_name} WHERE ownerId = %d", $owner_id),
        ARRAY_A
    );

    if (!$character) {
        echo json_encode(['success' => false, 'message' => 'Character not found']);
        wp_die();
    }

    $current_book_count = intval($character['book_count']);
    $new_book_count = $current_book_count + 1;
    $new_points = intval($character['points']) + 1;

    // Update book count and points
    $updated = $wpdb->update(
        $characters_table_name,
        ['book_count' => $new_book_count, 'points' => $new_points],
        ['ownerId' => $owner_id],
        ['%d', '%d'],
        ['%d']
    );

    if ($updated !== false) {
        // Update reward count
        $new_reward_count = update_reward_count($owner_id);

        $_SESSION['book_count'] = $new_book_count;
        echo json_encode([
            'success' => true,
            'newBookCount' => $new_book_count,
            'newRewardCount' => $new_reward_count,
            'newPoints' => $new_points,
            'message' => 'Book visit logged successfully'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update book count: ' . $wpdb->last_error]);
    }
    wp_die();
}

function log_1080_activity_callback() {
    global $wpdb;
    $characters_table_name = $wpdb->prefix . 'characters';
    $web_activity_table_name = $wpdb->prefix . 'web_activity';
    $web_activity_meta_table_name = $wpdb->prefix . 'web_activity_meta';
    $user_login_logs_table = $wpdb->prefix . 'user_login_logs';
    
    if (ob_get_level()) {
        ob_clean();
    }
    
    header('Content-Type: application/json');
    
    $owner_id = intval($_POST['ownerId']);
    $current_url = sanitize_url($_POST['currentUrl']);
    
    if (!$owner_id || empty($current_url)) {
        echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
        wp_die();
    }

    // Check if URL contains /avatar-dress-up or /avatar-dress-up/
    if (strpos($current_url, '/avatar-dress-up') === false) {
        echo json_encode(['success' => false, 'message' => 'Invalid URL for 1080 logging']);
        wp_die();
    }

    // Get barcode from session or user_login_logs
    $barcode_value = '1080'; // Default
    if (isset($_SESSION['library_barcode'])) {
        $barcode_value = sanitize_text_field($_SESSION['library_barcode']);
    } else {
        // Try to get barcode from user_login_logs using owner_id
        $user_log = $wpdb->get_row(
            $wpdb->prepare("SELECT barcode FROM {$user_login_logs_table} WHERE id = %d", $owner_id)
        );
        if ($user_log) {
            $barcode_value = $user_log->barcode;
        }
    }

    // Get library info
    $library_region = isset($_SESSION['library_region_name']) ? sanitize_text_field($_SESSION['library_region_name']) : 'US';
    $library_name_to_log = isset($_SESSION['library_code_name']) ? sanitize_text_field($_SESSION['library_code_name']) : 'Demo Library';

    // Get character data for library info if available
    $character = $wpdb->get_row(
        $wpdb->prepare("SELECT libraryName FROM {$characters_table_name} WHERE ownerId = %d", $owner_id),
        ARRAY_A
    );
    if ($character && !empty($character['libraryName'])) {
        $library_name_to_log = $character['libraryName'];
    }

    $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
    $log_data = [
        'alert_code' => 1080,
        'barcode' => $barcode_value,
        'library_name' => $library_name_to_log,
        'region_name' => $library_region,
        'ip' => $ip_address,
        'time' => current_time('mysql')
    ];

    $inserted_web_activity = $wpdb->insert(
        $web_activity_table_name,
        $log_data,
        ['%d', '%s', '%s', '%s', '%s', '%s']
    );

    if ($inserted_web_activity) {
        $web_activity_id = $wpdb->insert_id;
        
        // Insert meta data for the web activity
        $meta_entries = [
            ['key' => 'Activity Name', 'value' => 'Avatar Dress Up'],
            ['key' => 'Activity Title', 'value' => 'Gamification'],
            ['key' => 'Activity Type', 'value' => 'Avatar Dress Up']
        ];

        foreach ($meta_entries as $meta) {
            $wpdb->insert(
                $web_activity_meta_table_name,
                [
                    'web_activity_id' => $web_activity_id,
                    'key' => $meta['key'],
                    'value' => $meta['value'],
                    'time' => current_time('mysql')
                ],
                ['%d', '%s', '%s', '%s']
            );
        } 

        echo json_encode([
            'success' => true, 
            'message' => 'Successfully logged web activity 1080',
            'web_activity_id' => $web_activity_id,
            'barcode' => $barcode_value
        ]);
    } else {
        error_log("WordPress DB Error: Failed to insert web_activity 1080 entry: " . $wpdb->last_error);
        echo json_encode(['success' => false, 'message' => 'Failed to log web activity: ' . $wpdb->last_error]);
    }
    wp_die();
}

/*
function aiovg_claim_video_points_callback() {
    global $wpdb;
    $characters_table_name = $wpdb->prefix . 'characters';
    $video_claims_table_name = $wpdb->prefix . 'video_claims';
    
    if (ob_get_level()) {
        ob_clean();
    }
    
    header('Content-Type: application/json');
    
    $owner_id = intval($_POST['ownerId']);
    $video_url = sanitize_url($_POST['videoUrl']);
    
    if (!$owner_id || empty($video_url)) {
        echo json_encode(['success' => false, 'message' => 'Missing owner ID or video URL.']);
        wp_die();
    }

    $character = $wpdb->get_row(
        $wpdb->prepare("SELECT points FROM {$characters_table_name} WHERE ownerId = %d", $owner_id),
        ARRAY_A
    );

    if (!$character) {
        echo json_encode(['success' => false, 'message' => 'Character not found.']);
        wp_die();
    }

    $current_points = intval($character['points']);
    $points_to_add = 1;
    $new_points = $current_points + $points_to_add;

    $updated_points = $wpdb->update(
        $characters_table_name,
        ['points' => $new_points],
        ['ownerId' => $owner_id],
        ['%d'],
        ['%d']
    );

    if ($updated_points === false) {
        echo json_encode(['success' => false, 'message' => 'Failed to update points: ' . $wpdb->last_error]);
        wp_die();
    }

    $wpdb->insert(
        $video_claims_table_name,
        [
            'ownerId' => $owner_id,
            'videoUrl' => $video_url,
            'claimedAt' => current_time('mysql')
        ],
        ['%d', '%s', '%s']
    );

    echo json_encode([
        'success' => true, 
        'newPoints' => $new_points,
        'message' => 'Feather Added +1'
    ]);
    wp_die();
}
*/

function get_firebase_config_callback() {
    global $firebase_config;

    header('Content-Type: application/json');

    $owner_id = intval($_POST['ownerId']);

    if (!$owner_id) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        wp_die();
    }

    echo json_encode(['success' => true, 'config' => $firebase_config]);
    wp_die();
}

?>