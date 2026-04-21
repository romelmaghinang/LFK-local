<?php
/**
 * Mobile Push Notifications
 *
 * Handles FCM push notifications for the Lote4Kids mobile app.
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// MOBILE PUSH NOTIFICATIONS
add_action('init', 'create_push_notification_table');
function create_push_notification_table() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    $table_name = $wpdb->prefix . 'push_notifications';

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        title varchar(255) NOT NULL,
        body text NOT NULL,
        image varchar(500) DEFAULT NULL,
        region varchar(500) NOT NULL,
        library text DEFAULT NULL,
        sent_count int(11) DEFAULT 0,
        status varchar(20) DEFAULT 'sent',
        scheduled_at datetime DEFAULT NULL,
        scheduled_timezone varchar(100) DEFAULT 'Australia/Melbourne',
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) $charset_collate;";

    // Add missing columns for existing installations
    $columns_to_add = [
        'status' => "ALTER TABLE $table_name ADD COLUMN status varchar(20) DEFAULT 'sent' AFTER sent_count",
        'scheduled_at' => "ALTER TABLE $table_name ADD COLUMN scheduled_at datetime DEFAULT NULL AFTER status",
        'scheduled_timezone' => "ALTER TABLE $table_name ADD COLUMN scheduled_timezone varchar(100) DEFAULT 'Australia/Melbourne' AFTER scheduled_at",
    ];

    foreach ($columns_to_add as $column_name => $alter_sql) {
        $column_exists = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = '$table_name' AND COLUMN_NAME = '$column_name'");
        if (empty($column_exists)) {
            $wpdb->query($alter_sql);
        }
    }

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

// Schedule the cron job for processing scheduled notifications
add_action('init', 'schedule_push_notification_cron');
function schedule_push_notification_cron() {
    if (!wp_next_scheduled('process_scheduled_push_notifications')) {
        wp_schedule_event(time(), 'every_minute', 'process_scheduled_push_notifications');
    }
}

// Add custom cron interval (every minute)
add_filter('cron_schedules', 'add_push_notification_cron_interval');
function add_push_notification_cron_interval($schedules) {
    $schedules['every_minute'] = array(
        'interval' => 60,
        'display'  => 'Every Minute'
    );
    return $schedules;
}

// REST API endpoint for Firebase Cloud Scheduler to trigger scheduled notifications
add_action('rest_api_init', function () {
    register_rest_route('lfk/v1', '/process-scheduled-notifications', array(
        'methods' => ['GET', 'POST'],
        'callback' => 'lfk_process_scheduled_notifications_endpoint',
        'permission_callback' => 'lfk_verify_scheduler_secret',
    ));
});

function lfk_verify_scheduler_secret($request) {
    // Define secret in wp-config.php: define('LFK_SCHEDULER_SECRET', 'your-secret-key');
    if (!defined('LFK_SCHEDULER_SECRET')) {
        error_log('Push Notification API - LFK_SCHEDULER_SECRET not defined');
        return false;
    }

    $provided_secret = $request->get_header('X-Scheduler-Secret');
    if (empty($provided_secret)) {
        $provided_secret = $request->get_param('secret');
    }

    if ($provided_secret !== LFK_SCHEDULER_SECRET) {
        error_log('Push Notification API - Invalid secret provided');
        return false;
    }

    return true;
}

function lfk_process_scheduled_notifications_endpoint($request) {
    error_log('Push Notification API - Triggered by Firebase Cloud Scheduler');

    // Call the same handler as WP Cron
    process_scheduled_push_notifications_handler();

    // Get count of remaining scheduled notifications
    global $wpdb;
    $table_name = $wpdb->prefix . 'push_notifications';
    $remaining = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE status = 'scheduled'");

    // Get pending scheduled notifications
    $pending = $wpdb->get_results("SELECT id, title, region, scheduled_at FROM $table_name WHERE status = 'scheduled' ORDER BY scheduled_at ASC");

    $pending_list = [];
    $melb_tz = new DateTimeZone('Australia/Melbourne');
    $utc_tz = new DateTimeZone('UTC');

    foreach ($pending as $notif) {
        try {
            $utc_time = new DateTime($notif->scheduled_at, $utc_tz);
            $utc_time->setTimezone($melb_tz);
            $scheduled_melb = $utc_time->format('Y-m-d H:i:s');
        } catch (Exception $e) {
            $scheduled_melb = $notif->scheduled_at;
        }

        $pending_list[] = [
            'id' => (int) $notif->id,
            'title' => $notif->title,
            'region' => $notif->region,
            'scheduled_at_melb' => $scheduled_melb,
        ];
    }

    return new WP_REST_Response(array(
        'success' => true,
        'message' => 'Scheduled notifications processed',
        'remaining_scheduled' => (int) $remaining,
        'pending_notifications' => $pending_list,
        'processed_at_utc' => gmdate('Y-m-d H:i:s'),
    ), 200);
}

// Process scheduled notifications
add_action('process_scheduled_push_notifications', 'process_scheduled_push_notifications_handler');
function process_scheduled_push_notifications_handler() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'push_notifications';

    // Get scheduled notifications that are due (times are stored in UTC)
    $now_utc = gmdate('Y-m-d H:i:s');
    $scheduled = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $table_name WHERE status = 'scheduled' AND scheduled_at <= %s",
        $now_utc
    ));

    error_log("Push Notification Cron - Current UTC time: {$now_utc}, Found " . count($scheduled) . " scheduled notifications");

    if (empty($scheduled)) {
        return;
    }

    foreach ($scheduled as $notification) {
        // Parse regions and libraries
        $regions = !empty($notification->region) ? array_map('trim', explode(',', $notification->region)) : [];
        $libraries = [];
        if (!empty($notification->library) && $notification->library !== 'All') {
            $libraries = array_map('trim', explode(',', $notification->library));
        }

        // Get tokens
        $tokens = get_fcm_tokens_from_firestore($regions, $libraries);

        if (empty($tokens)) {
            $wpdb->update(
                $table_name,
                array('status' => 'sent', 'sent_count' => 0),
                array('id' => $notification->id)
            );
            continue;
        }

        // Send notification
        $sent_count = send_fcm_notification($tokens, $notification->title, $notification->body, $notification->image);

        // Update status
        $wpdb->update(
            $table_name,
            array('status' => 'sent', 'sent_count' => $sent_count),
            array('id' => $notification->id)
        );

        error_log("Push Notification Cron - Sent notification ID {$notification->id} to {$sent_count} devices");
    }
}

// Handle manual table creation
add_action('admin_init', 'handle_create_pn_table');
function handle_create_pn_table() {
    if (!isset($_GET['action']) || $_GET['action'] !== 'create_table' || !isset($_GET['page']) || $_GET['page'] !== 'mobile-push-notification') {
        return;
    }

    if (!wp_verify_nonce($_GET['_wpnonce'], 'create_pn_table')) {
        wp_die('Security check failed');
    }

    if (!current_user_can('manage_options')) {
        wp_die('Unauthorized');
    }

    create_push_notification_table();

    wp_safe_redirect(admin_url('admin.php?page=mobile-push-notification&table_created=1'));
    exit;
}

// Handle manual process scheduled notifications (for testing)
add_action('admin_init', 'handle_manual_process_scheduled_notifications');
function handle_manual_process_scheduled_notifications() {
    if (!isset($_GET['action']) || $_GET['action'] !== 'process_scheduled' || !isset($_GET['page']) || $_GET['page'] !== 'mobile-push-notification') {
        return;
    }

    if (!wp_verify_nonce($_GET['_wpnonce'], 'process_scheduled_notifications')) {
        wp_die('Security check failed');
    }

    if (!current_user_can('manage_options')) {
        wp_die('Unauthorized');
    }

    // Call the scheduled notifications processor
    process_scheduled_push_notifications_handler();

    wp_safe_redirect(admin_url('admin.php?page=mobile-push-notification&manually_processed=1'));
    exit;
}

// Handle cancel scheduled notification
add_action('admin_init', 'handle_cancel_scheduled_notification');
function handle_cancel_scheduled_notification() {
    if (!isset($_GET['cancel_id']) || !isset($_GET['page']) || $_GET['page'] !== 'mobile-push-notification') {
        return;
    }

    $cancel_id = intval($_GET['cancel_id']);

    if (!wp_verify_nonce($_GET['_wpnonce'], 'cancel_notification_' . $cancel_id)) {
        wp_die('Security check failed');
    }

    if (!current_user_can('manage_options')) {
        wp_die('Unauthorized');
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'push_notifications';

    $wpdb->delete($table_name, array('id' => $cancel_id, 'status' => 'scheduled'));

    // Also delete from Firestore so Cloud Function doesn't process it
    $firestore_deleted = delete_notification_from_firestore($cancel_id);
    if (!$firestore_deleted) {
        error_log("Push Notification - WARNING: Failed to delete notification {$cancel_id} from Firestore during cancel");
    }

    wp_safe_redirect(admin_url('admin.php?page=mobile-push-notification&cancelled=1'));
    exit;
}

add_action('admin_menu', 'mobile_push_notification');
function mobile_push_notification() {
    add_menu_page(
        'Push Notifications',
        'Push Notifications',
        'manage_options',
        'mobile-push-notification',
        'mobile_push_notification_template',
        'dashicons-megaphone',
        58
    );
}

add_action('admin_enqueue_scripts', function ($hook) {
    // Adjust `$hook` check if needed for your page.
    wp_enqueue_style(
        'select2',
        'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css',
        [],
        '4.1.0'
    );
    wp_enqueue_script(
        'select2',
        'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
        ['jquery'],
        '4.1.0',
        true
    );
});

// Admin page UI
function mobile_push_notification_template() {

    global $wpdb;
    $regions = [
        'AU',
        'US',
        'US_TLN',
        'CA',
        'NZ',
        'NZ_EPIC',
        'UK',
        'EU',
        'CA_OLS',
        'CA_BC',
        'US_IHLS',
        'US_CLF',
        'US_MBS',
        'US_NSA',
        'US_NEO',
        'US_PPL',
        'US_RAILS',
        'US_SFK',
        'US_BCLC',
        'AU_DIGI',
        'CA_TAL',
        'UK_DL',
        'US_TAL'
    ];

    // Get sent notifications
    $table_name = $wpdb->prefix . 'push_notifications';
    $sent_notifications = $wpdb->get_results("SELECT * FROM $table_name ORDER BY created_at DESC");

    // Check if editing existing notification
    $editing_notification = null;
    $editing_libraries = [];
    $editing_regions = [];
    if (isset($_GET['edit_id'])) {
        $edit_id = intval($_GET['edit_id']);
        $editing_notification = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $edit_id));

        // Parse saved regions for pre-selection
        if ($editing_notification && !empty($editing_notification->region)) {
            $editing_regions = array_map('trim', explode(',', $editing_notification->region));
        }

        // Parse saved libraries for pre-selection
        if ($editing_notification && !empty($editing_notification->library)) {
            if ($editing_notification->library === 'All') {
                $editing_libraries = ['all'];
            } else {
                $editing_libraries = array_map('trim', explode(',', $editing_notification->library));
            }
        }
    }

    $libraries = l4k_getLibraries(true);

    ?>
    <div class="wrap">
        <h1>Send Push Notification</h1>

        <?php
        // DEBUG: Check table status
        $debug_table_name = $wpdb->prefix . 'push_notifications';
        $debug_table_exists = $wpdb->get_var("SHOW TABLES LIKE '$debug_table_name'");
        $debug_row_count = $debug_table_exists ? $wpdb->get_var("SELECT COUNT(*) FROM $debug_table_name") : 0;
        ?>
        <div class="notice notice-info" style="background: #f0f0f0; border-left-color: #999;">
            <p><strong>Debug Info:</strong>
                Table: <code><?php echo $debug_table_name; ?></code> |
                Exists: <strong><?php echo $debug_table_exists ? 'YES' : 'NO'; ?></strong> |
                Rows: <strong><?php echo $debug_row_count; ?></strong>
                <?php if (!$debug_table_exists): ?>
                    | <a href="<?php echo wp_nonce_url(admin_url('admin.php?page=mobile-push-notification&action=create_table'), 'create_pn_table'); ?>">Create Table Now</a>
                <?php endif; ?>
            </p>
        </div>

        <?php if (isset($_GET['error']) && $_GET['error'] === 'no_tokens'): ?>
            <div class="notice notice-error"><p>No FCM tokens found for the selected region/libraries. Make sure users have registered their devices.</p></div>
        <?php endif; ?>

        <?php if (isset($_GET['db_error'])): ?>
            <div class="notice notice-error"><p><strong>Database Error:</strong> <?php echo esc_html(urldecode($_GET['db_error'])); ?></p></div>
        <?php endif; ?>

        <?php if (isset($_GET['scheduled'])): ?>
            <div class="notice notice-success"><p>Notification scheduled successfully!</p></div>
        <?php endif; ?>

        <?php if (isset($_GET['manually_processed'])): ?>
            <div class="notice notice-info"><p>Scheduled notifications processed manually. Check the table below for updates.</p></div>
        <?php endif; ?>

        <?php if (isset($_GET['table_created'])): ?>
            <div class="notice notice-success"><p>Database table created successfully!</p></div>
        <?php endif; ?>

        <?php if (isset($_GET['cancelled'])): ?>
            <div class="notice notice-warning"><p>Scheduled notification has been cancelled.</p></div>
        <?php endif; ?>

        <?php
        // Success popup data
        $show_success_popup = false;
        $popup_message = '';
        $sent_count_display = isset($_GET['sent']) ? intval($_GET['sent']) : 0;

        if (isset($_GET['success'])) {
            $show_success_popup = true;
            $popup_message = 'Notification sent successfully!';
        } elseif (isset($_GET['updated'])) {
            $show_success_popup = true;
            $popup_message = 'Notification updated and sent successfully!';
        }
        ?>

        <form method="post" action="<?php echo admin_url('admin.php?page=mobile-push-notification'); ?>">
            <?php wp_nonce_field( 'mobile_push_notification_post', 'mobile_push_notification_nonce' ); ?>
            <input type="hidden" name="action" value="mobile_push_notification_post">
            <?php if ($editing_notification): ?>
                <input type="hidden" name="notification_id" value="<?php echo $editing_notification->id; ?>">
            <?php endif; ?>

            <table class="form-table">
                <tr>
                    <th><label for="region">Region *</label></th>
                    <td>
                        <select name="region[]" id="region" multiple style="width: 100%;">
                            <?php foreach ($regions as $region): ?>
                                <option value="<?php echo esc_attr($region); ?>"
                                    <?php selected(in_array($region, $editing_regions, true)); ?>>
                                    <?php echo esc_html($region); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <p class="description">Select one or more regions.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="libraries">Libraries</label></th>
                    <td>
                        <select name="libraries[]" id="libraries" multiple style="width: 100%;">
                            <option value="all" id="libraries_all_option" <?php selected(in_array('all', $editing_libraries, true)); ?>>All libraries</option>
                            <?php foreach ($libraries as $library): ?>
                                <option value="<?php echo esc_attr($library['title']); ?>"
                                        data-region="<?php echo esc_attr($library['group_region']); ?>"
                                    <?php selected(in_array($library['title'], $editing_libraries, true)); ?>>
                                    <?php echo esc_html($library['title']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <p class="description">Search and select multiple libraries, or choose "All libraries".</p>
                    </td>
                </tr>
                <tr>
                    <th><label for="notification_title">Title *</label></th>
                    <td>
                        <input type="text" required name="notification_title" id="notification_title"
                               value="<?php echo $editing_notification ? esc_attr($editing_notification->title) : ''; ?>"
                               required style="width: 500px;">
                    </td>
                </tr>
                <tr>
                    <th><label for="notification_body">Body *</label></th>
                    <td>
                        <textarea name="notification_body" required id="notification_body" rows="5" required style="width: 500px;"><?php
                            echo $editing_notification ? esc_textarea($editing_notification->body) : '';
                        ?></textarea>
                    </td>
                </tr>
                <tr>
                    <th><label for="pn_image">Image URL</label></th>
                    <td>
                        <input type="url" name="pn_image" id="pn_image"
                               value="<?php echo $editing_notification ? esc_attr($editing_notification->image) : ''; ?>"
                               style="width: 500px;">
                    </td>
                </tr>
                <tr>
                    <th><label>Send Time</label></th>
                    <td>
                        <label style="margin-right: 20px;">
                            <input type="radio" name="send_type" value="now" <?php checked(!$editing_notification || empty($editing_notification->scheduled_at) || $editing_notification->status === 'sent'); ?>>
                            Send Now
                        </label>
                        <label>
                            <input type="radio" name="send_type" value="scheduled" <?php checked($editing_notification && !empty($editing_notification->scheduled_at) && $editing_notification->status === 'scheduled'); ?>>
                            Schedule for later
                        </label>
                        <div id="schedule_datetime_wrapper" style="margin-top: 10px; display: none;">
                            <?php
                            // Always use Melbourne timezone
                            $scheduled_timezone = 'Australia/Melbourne';

                            // Convert stored UTC time back to Melbourne timezone for display
                            $display_scheduled_time = '';
                            if ($editing_notification && $editing_notification->scheduled_at) {
                                try {
                                    $utc_time = new DateTime($editing_notification->scheduled_at, new DateTimeZone('UTC'));
                                    $melb_tz = new DateTimeZone($scheduled_timezone);
                                    $utc_time->setTimezone($melb_tz);
                                    $display_scheduled_time = $utc_time->format('Y-m-d\TH:i');
                                } catch (Exception $e) {
                                    $display_scheduled_time = date('Y-m-d\TH:i', strtotime($editing_notification->scheduled_at));
                                }
                            }
                            ?>
                            <input type="hidden" name="scheduled_timezone" value="Australia/Melbourne">
                            <input type="datetime-local" name="scheduled_at" id="scheduled_at"
                                   value="<?php echo esc_attr($display_scheduled_time); ?>"
                                   style="width: 300px;">
                            <p class="description" style="margin-top: 5px;">Time is based on Melbourne timezone.</p>
                        </div>
                    </td>
                </tr>
            </table>

            <p class="submit">
                <input type="submit" class="button button-primary" id="submit_btn"
                       value="<?php echo $editing_notification ? 'Update &amp; Send Notification' : 'Send Notification'; ?>">
                <?php if ( $editing_notification ): ?>
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=mobile-push-notification' ) ); ?>"
                       class="button">Cancel Edit</a>
                <?php endif; ?>
            </p>
        </form>

        <hr>

        <h2>Notifications</h2>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th style="width:40px;">ID</th>
                    <th>Title</th>
                    <th>Region</th>
                    <th>Library</th>
                    <th style="width:80px;">Status</th>
                    <th style="width:80px;">Sent</th>
                    <th>Scheduled/Sent At</th>
                    <th style="width:150px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($sent_notifications)): ?>
                    <?php foreach ($sent_notifications as $notification):
                        $status = isset($notification->status) ? $notification->status : 'sent';
                        $status_label = $status === 'scheduled' ? '<span style="color:#b26200;font-weight:bold;">Scheduled</span>' : '<span style="color:#00a32a;">Sent</span>';

                        // Convert UTC time to Melbourne timezone for display
                        $display_time = '';
                        $tz_abbrev = 'MELB';

                        if ($status === 'scheduled' && !empty($notification->scheduled_at)) {
                            try {
                                $utc_time = new DateTime($notification->scheduled_at, new DateTimeZone('UTC'));
                                $melb_tz = new DateTimeZone('Australia/Melbourne');
                                $utc_time->setTimezone($melb_tz);
                                $display_time = $utc_time->format('Y-m-d g:i A');
                            } catch (Exception $e) {
                                $display_time = date('Y-m-d g:i A', strtotime($notification->scheduled_at));
                            }
                        } else {
                            // For sent notifications, convert created_at to Melbourne time
                            try {
                                $created_time = new DateTime($notification->created_at, new DateTimeZone('UTC'));
                                $melb_tz = new DateTimeZone('Australia/Melbourne');
                                $created_time->setTimezone($melb_tz);
                                $display_time = $created_time->format('Y-m-d g:i A');
                            } catch (Exception $e) {
                                $display_time = date('Y-m-d g:i A', strtotime($notification->created_at));
                            }
                        }
                    ?>
                        <tr>
                            <td><?php echo $notification->id; ?></td>
                            <td><?php echo esc_html($notification->title); ?></td>
                            <td><?php echo esc_html($notification->region); ?></td>
                            <td><?php echo esc_html($notification->library ?: 'All'); ?></td>
                            <td><?php echo $status_label; ?></td>
                            <td><?php echo $notification->sent_count; ?></td>
                            <td><?php echo esc_html($display_time); ?> <small style="color:#666;">(<?php echo esc_html($tz_abbrev); ?>)</small></td>
                            <td>
                                <a href="?page=mobile-push-notification&edit_id=<?php echo $notification->id; ?>"
                                   class="button button-small">Edit</a>
                                <?php if ($status === 'scheduled'): ?>
                                    <a href="?page=mobile-push-notification&cancel_id=<?php echo $notification->id; ?>&_wpnonce=<?php echo wp_create_nonce('cancel_notification_' . $notification->id); ?>"
                                       class="button button-small" style="color:#b32d2e;"
                                       onclick="return confirm('Cancel this scheduled notification?');">Cancel</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="8">No notifications yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <style>
        .select2-container--default .select2-selection--multiple {
            min-height: 3.5em;   /* ~2 lines */
            max-height: 8em;     /* ~5 lines */
            overflow-y: auto;    /* vertical scroll when more than 5 lines */
            overflow-x: hidden;
            padding-bottom: 4px;
        }

        /* Ensure the selected tags wrap properly */
        .select2-container--default .select2-selection--multiple .select2-selection__rendered {
            display: block;
            white-space: normal;
        }

        /* Optional: fix inner tag layout */
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            margin-top: 2px;
        }
    </style>
    <script type="text/javascript">
        jQuery(document).ready(function ($) {
            var $region = $('#region');
            var $libraries = $('#libraries');
            var allValue = 'all';
            var selectedRegions = []; // Array to hold multiple selected regions

            // Initialize Select2 for regions (multiple)
            $region.select2({
                placeholder: 'Select regions',
                allowClear: true,
                closeOnSelect: false,
                width: '100%'
            });

            // Custom matcher for Select2 to filter libraries by selected regions
            function regionMatcher(params, data) {
                // Always show "All libraries" option
                if (data.id === allValue) {
                    return data;
                }

                var libraryRegion = $(data.element).data('region');

                // If no search term, check region filter
                if ($.trim(params.term) === '') {
                    // If no regions selected, show all libraries
                    if (selectedRegions.length === 0) {
                        return data;
                    }
                    // Show library if its region is in selected regions
                    if (selectedRegions.indexOf(libraryRegion) !== -1) {
                        return data;
                    }
                    return null;
                }

                // If there's a search term, also filter by text
                if (data.text.toLowerCase().indexOf(params.term.toLowerCase()) > -1) {
                    if (selectedRegions.length === 0 || selectedRegions.indexOf(libraryRegion) !== -1) {
                        return data;
                    }
                }
                return null;
            }

            // Initialize Select2 for libraries with custom matcher
            $libraries.select2({
                placeholder: 'Select libraries',
                allowClear: true,
                closeOnSelect: false,
                width: '100%',
                matcher: regionMatcher
            });

            // Store initial selections (for edit mode)
            var initialLibraries = $libraries.val() || [];
            var initialRegions = $region.val() || [];

            // Filter libraries when region changes
            $region.on('change', function (e, preserveSelection) {
                selectedRegions = $(this).val() || [];

                if (!preserveSelection) {
                    // Clear library selection when regions change
                    $libraries.val(null).trigger('change');
                }

                // Close and reopen to refresh the filtered list
                $libraries.select2('close');
            });

            // "All" behavior for libraries - when "all" is selected with others, keep only "all"
            $libraries.on('change', function () {
                var selected = $(this).val() || [];
                if (selected.indexOf(allValue) !== -1 && selected.length > 1) {
                    $libraries.val([allValue]).trigger('change');
                }
            });

            // On page load, set region filter but preserve pre-selected values (edit mode)
            if (initialRegions.length > 0) {
                selectedRegions = initialRegions;
                $region.trigger('change', [true]);
                if (initialLibraries.length > 0) {
                    $libraries.val(initialLibraries).trigger('change');
                }
            }

            // Schedule toggle functionality
            var $sendTypeRadios = $('input[name="send_type"]');
            var $scheduleDatetimeWrapper = $('#schedule_datetime_wrapper');
            var $scheduledAtInput = $('#scheduled_at');
            var $submitBtn = $('#submit_btn');

            function toggleScheduleField() {
                var selectedValue = $('input[name="send_type"]:checked').val();
                if (selectedValue === 'scheduled') {
                    $scheduleDatetimeWrapper.slideDown(200);
                    $scheduledAtInput.prop('required', true);
                    $submitBtn.val('Schedule Notification');
                } else {
                    $scheduleDatetimeWrapper.slideUp(200);
                    $scheduledAtInput.prop('required', false);
                    $submitBtn.val('Send Notification');
                }
            }

            $sendTypeRadios.on('change', toggleScheduleField);
            toggleScheduleField(); // Initialize on page load

            // Success popup
            <?php if ($show_success_popup): ?>
            showSuccessPopup('<?php echo esc_js($popup_message); ?>', <?php echo $sent_count_display; ?>);
            <?php endif; ?>
        });

        function showSuccessPopup(message, sentCount) {
            var $ = jQuery;
            var overlay = $('<div class="pn-popup-overlay"></div>');
            var popup = $('<div class="pn-popup">' +
                '<div class="pn-popup-icon">&#10003;</div>' +
                '<h2 class="pn-popup-title">' + message + '</h2>' +
                '<div class="pn-popup-count">' +
                    '<span class="pn-count-number">' + sentCount + '</span>' +
                    '<span class="pn-count-label">notification' + (sentCount !== 1 ? 's' : '') + ' sent</span>' +
                '</div>' +
                '<button class="pn-popup-close button button-primary">OK</button>' +
            '</div>');

            $('body').append(overlay).append(popup);

            // Close popup
            popup.find('.pn-popup-close').on('click', function() {
                overlay.fadeOut(200, function() { $(this).remove(); });
                popup.fadeOut(200, function() { $(this).remove(); });
                // Remove URL parameters
                if (window.history.replaceState) {
                    window.history.replaceState({}, document.title, window.location.pathname + '?page=mobile-push-notification');
                }
            });

            overlay.on('click', function() {
                popup.find('.pn-popup-close').click();
            });
        }
    </script>
    <style>
        .pn-popup-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 99999;
        }
        .pn-popup {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: #fff;
            padding: 40px 50px;
            border-radius: 10px;
            box-shadow: 0 5px 30px rgba(0, 0, 0, 0.3);
            z-index: 100000;
            text-align: center;
            min-width: 300px;
        }
        .pn-popup-icon {
            width: 60px;
            height: 60px;
            background: #00a32a;
            color: #fff;
            font-size: 30px;
            line-height: 60px;
            border-radius: 50%;
            margin: 0 auto 20px;
        }
        .pn-popup-title {
            margin: 0 0 20px;
            font-size: 20px;
            color: #1d2327;
        }
        .pn-popup-count {
            margin-bottom: 25px;
        }
        .pn-count-number {
            display: block;
            font-size: 48px;
            font-weight: 700;
            color: #2271b1;
            line-height: 1;
        }
        .pn-count-label {
            display: block;
            font-size: 14px;
            color: #646970;
            margin-top: 5px;
        }
        .pn-popup-close {
            min-width: 100px;
            height: 36px;
        }
    </style>
    <?php
}

// Form handler - hook early with admin_init to ensure no output before redirect
add_action('admin_init', 'mobile_push_notification_handler', 1);
function mobile_push_notification_handler() {
    // Only process if this is our form submission
    if (!isset($_POST['action']) || $_POST['action'] !== 'mobile_push_notification_post') {
        return;
    }

    // Start output buffering immediately to catch any stray output
    if (!ob_get_level()) {
        ob_start();
    }

    mobile_push_notification_post();
}

function mobile_push_notification_post() {
    // Start output buffering to prevent any stray output
    ob_start();

    if (!current_user_can('manage_options')) {
        ob_end_clean();
        wp_die('Unauthorized');
    }

    // Verify nonce
    if (!isset($_POST['mobile_push_notification_nonce']) ||
        !wp_verify_nonce($_POST['mobile_push_notification_nonce'], 'mobile_push_notification_post')) {
        ob_end_clean();
        wp_die('Security check failed');
    }

    $title     = isset($_POST['notification_title'])
        ? sanitize_text_field(wp_unslash($_POST['notification_title']))
        : '';

    $body      = isset($_POST['notification_body'])
        ? sanitize_textarea_field(wp_unslash($_POST['notification_body']))
        : '';

    $image     = isset($_POST['pn_image'])
        ? esc_url_raw(wp_unslash($_POST['pn_image']))
        : '';

    // Handle multiple regions
    $regions = [];
    if ( isset( $_POST['region'] ) && is_array( $_POST['region'] ) ) {
        $raw_regions = wp_unslash( $_POST['region'] );
        $regions = array_map(
            static function ( $value ) {
                return sanitize_text_field( (string) $value );
            },
            $raw_regions
        );
        $regions = array_values( array_filter( $regions, 'strlen' ) );
    }

    // libraries is expected to be an array from the form: name="libraries[]"
    $libraries = [];
    $is_all_libraries = false;

    if ( isset( $_POST['libraries'] ) && is_array( $_POST['libraries'] ) ) {
        // Unsanitize slashes first
        $raw_libraries = wp_unslash( $_POST['libraries'] );

        // Sanitize each value and ensure they are strings
        $libraries = array_map(
            static function ( $value ) {
                return sanitize_text_field( (string) $value );
            },
            $raw_libraries
        );

        // Optionally remove empty values and reindex
        $libraries = array_values( array_filter( $libraries, 'strlen' ) );

        // Check if "all" was selected - if so, don't filter by library
        if ( in_array( 'all', $libraries, true ) ) {
            $is_all_libraries = true;
            $libraries = []; // Empty array means no library filter
        }
    }

    $notification_id = isset($_POST['notification_id']) ? intval($_POST['notification_id']) : 0;

    // Handle scheduling
    $send_type = isset($_POST['send_type']) ? sanitize_text_field($_POST['send_type']) : 'now';
    $scheduled_at = null;
    $scheduled_timezone = isset($_POST['scheduled_timezone']) ? sanitize_text_field($_POST['scheduled_timezone']) : 'Australia/Melbourne';

    if ($send_type === 'scheduled' && !empty($_POST['scheduled_at'])) {
        $local_time_str = sanitize_text_field($_POST['scheduled_at']);

        // Convert from selected timezone to UTC for storage
        try {
            $local_tz = new DateTimeZone($scheduled_timezone);
            $utc_tz = new DateTimeZone('UTC');

            $local_time = new DateTime($local_time_str, $local_tz);
            $local_time->setTimezone($utc_tz);

            $scheduled_at = $local_time->format('Y-m-d H:i:s');

            error_log("Push Notification - Scheduling: Local time {$local_time_str} ({$scheduled_timezone}) -> UTC {$scheduled_at}");
        } catch (Exception $e) {
            error_log("Push Notification - Timezone conversion error: " . $e->getMessage());
            $scheduled_at = date('Y-m-d H:i:s', strtotime($local_time_str));
        }
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'push_notifications';

    // Ensure table exists
    $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'");
    if (!$table_exists) {
        create_push_notification_table();
        // Re-check if table was created
        $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'");
    }

    // DEBUG: Log table status
    error_log("Push Notification - Table name: {$table_name}, Exists: " . ($table_exists ? 'YES' : 'NO'));

    // Serialize regions and libraries for storage
    $regions_str = !empty($regions) ? implode(',', $regions) : '';
    $libraries_str = $is_all_libraries ? 'All' : (!empty($libraries) ? implode(',', $libraries) : '');

    // DEBUG: Log all values
    error_log("Push Notification - send_type: {$send_type}");
    error_log("Push Notification - scheduled_at: " . var_export($scheduled_at, true));
    error_log("Push Notification - title: {$title}");
    error_log("Push Notification - regions_str: {$regions_str}");

    // Debug file for logging
    $debug_file = WP_CONTENT_DIR . '/pn-debug.log';

    if ($send_type === 'scheduled' && $scheduled_at) {
        $data = array(
            'title' => $title,
            'body' => $body,
            'image' => $image,
            'region' => $regions_str,
            'library' => $libraries_str,
            'sent_count' => 0,
            'status' => 'scheduled',
            'scheduled_at' => $scheduled_at,
            'scheduled_timezone' => $scheduled_timezone,
            'created_at' => gmdate('Y-m-d H:i:s')
        );

        // Write to custom debug file
        file_put_contents($debug_file, date('Y-m-d H:i:s') . " - About to insert scheduled notification\n", FILE_APPEND);
        file_put_contents($debug_file, date('Y-m-d H:i:s') . " - Data: " . print_r($data, true) . "\n", FILE_APPEND);

        if ($notification_id > 0) {
            $result = $wpdb->update($table_name, $data, array('id' => $notification_id));
            error_log("Push Notification - Updated notification ID {$notification_id}, result: " . var_export($result, true));
            $wp_id_for_firestore = $notification_id;
        } else {
            $result = $wpdb->insert($table_name, $data);

            // Write to custom debug file
            file_put_contents($debug_file, date('Y-m-d H:i:s') . " - Insert result: " . var_export($result, true) . "\n", FILE_APPEND);
            file_put_contents($debug_file, date('Y-m-d H:i:s') . " - Last error: " . $wpdb->last_error . "\n", FILE_APPEND);
            file_put_contents($debug_file, date('Y-m-d H:i:s') . " - Last query: " . $wpdb->last_query . "\n", FILE_APPEND);
            file_put_contents($debug_file, date('Y-m-d H:i:s') . " - Insert ID: " . $wpdb->insert_id . "\n", FILE_APPEND);

            if ($result === false) {
                while (ob_get_level()) {
                    ob_end_clean();
                }
                $scheduled_db_error_redirect = admin_url('admin.php?page=mobile-push-notification&db_error=' . urlencode($wpdb->last_error));

                file_put_contents($debug_file, date('Y-m-d H:i:s') . " - Scheduled DB error, redirecting to: {$scheduled_db_error_redirect}\n", FILE_APPEND);

                if (!headers_sent()) {
                    wp_safe_redirect($scheduled_db_error_redirect);
                    exit;
                } else {
                    echo '<script>window.location.href = "' . esc_js($scheduled_db_error_redirect) . '";</script>';
                    echo '<noscript><meta http-equiv="refresh" content="0;url=' . esc_url($scheduled_db_error_redirect) . '"></noscript>';
                    exit;
                }
            }
            $wp_id_for_firestore = $wpdb->insert_id;
        }

        // Save to Firestore for Cloud Function to process
        // Convert scheduled_at to ISO 8601 format (it's already in UTC)
        $scheduled_at_iso = str_replace(' ', 'T', $scheduled_at);
        $firestore_result = save_notification_to_firestore(
            $wp_id_for_firestore,
            $title,
            $body,
            $image,
            $regions,
            $libraries,
            $scheduled_at_iso
        );

        if (!$firestore_result) {
            file_put_contents($debug_file, date('Y-m-d H:i:s') . " - WARNING: Failed to save to Firestore, but WP DB save succeeded\n", FILE_APPEND);
            error_log("Push Notification - WARNING: Failed to save notification {$wp_id_for_firestore} to Firestore");
        } else {
            file_put_contents($debug_file, date('Y-m-d H:i:s') . " - Successfully saved to Firestore with ID: {$wp_id_for_firestore}\n", FILE_APPEND);
        }

        while (ob_get_level()) {
            ob_end_clean();
        }

        $scheduled_redirect_url = admin_url('admin.php?page=mobile-push-notification&scheduled=1');
        file_put_contents($debug_file, date('Y-m-d H:i:s') . " - Attempting redirect to: {$scheduled_redirect_url}\n", FILE_APPEND);

        if (!headers_sent()) {
            wp_safe_redirect($scheduled_redirect_url);
            exit;
        } else {
            file_put_contents($debug_file, date('Y-m-d H:i:s') . " - Headers already sent, using JS redirect\n", FILE_APPEND);
            echo '<script>window.location.href = "' . esc_js($scheduled_redirect_url) . '";</script>';
            echo '<noscript><meta http-equiv="refresh" content="0;url=' . esc_url($scheduled_redirect_url) . '"></noscript>';
            exit;
        }
    }

    // Send now - get tokens and send immediately
    $tokens = get_fcm_tokens_from_firestore($regions, $libraries);

    error_log('Push Notification - Tokens found: ' . count($tokens));

    if (empty($tokens)) {
        while (ob_get_level()) {
            ob_end_clean();
        }
        $no_tokens_redirect = admin_url('admin.php?page=mobile-push-notification&error=no_tokens');

        file_put_contents($debug_file, date('Y-m-d H:i:s') . " - No tokens found, redirecting to: {$no_tokens_redirect}\n", FILE_APPEND);

        if (!headers_sent()) {
            wp_safe_redirect($no_tokens_redirect);
            exit;
        } else {
            echo '<script>window.location.href = "' . esc_js($no_tokens_redirect) . '";</script>';
            echo '<noscript><meta http-equiv="refresh" content="0;url=' . esc_url($no_tokens_redirect) . '"></noscript>';
            exit;
        }
    }

    $sent_count = send_fcm_notification($tokens, $title, $body, $image);

    error_log("Push Notification - Send Now: sent_count = {$sent_count}");

    $data = array(
        'title' => $title,
        'body' => $body,
        'image' => $image,
        'region' => $regions_str,
        'library' => $libraries_str,
        'sent_count' => $sent_count,
        'status' => 'sent',
        'scheduled_at' => null,
        'scheduled_timezone' => $scheduled_timezone,
        'created_at' => gmdate('Y-m-d H:i:s')
    );

    // Write to custom debug file
    file_put_contents($debug_file, date('Y-m-d H:i:s') . " - SEND NOW - Data: " . print_r($data, true) . "\n", FILE_APPEND);

    // Update existing or insert new
    if ($notification_id > 0) {
        $result = $wpdb->update($table_name, $data, array('id' => $notification_id));
        file_put_contents($debug_file, date('Y-m-d H:i:s') . " - SEND NOW - Update result: " . var_export($result, true) . "\n", FILE_APPEND);
        $redirect_url = admin_url('admin.php?page=mobile-push-notification&updated=1&sent=' . $sent_count);
    } else {
        $result = $wpdb->insert($table_name, $data);

        file_put_contents($debug_file, date('Y-m-d H:i:s') . " - SEND NOW - Insert result: " . var_export($result, true) . "\n", FILE_APPEND);
        file_put_contents($debug_file, date('Y-m-d H:i:s') . " - SEND NOW - Last error: " . $wpdb->last_error . "\n", FILE_APPEND);
        file_put_contents($debug_file, date('Y-m-d H:i:s') . " - SEND NOW - Last query: " . $wpdb->last_query . "\n", FILE_APPEND);
        file_put_contents($debug_file, date('Y-m-d H:i:s') . " - SEND NOW - Insert ID: " . $wpdb->insert_id . "\n", FILE_APPEND);

        if ($result === false) {
            while (ob_get_level()) {
                ob_end_clean();
            }
            $db_error_redirect = admin_url('admin.php?page=mobile-push-notification&db_error=' . urlencode($wpdb->last_error));

            file_put_contents($debug_file, date('Y-m-d H:i:s') . " - DB error, redirecting to: {$db_error_redirect}\n", FILE_APPEND);

            if (!headers_sent()) {
                wp_safe_redirect($db_error_redirect);
                exit;
            } else {
                echo '<script>window.location.href = "' . esc_js($db_error_redirect) . '";</script>';
                echo '<noscript><meta http-equiv="refresh" content="0;url=' . esc_url($db_error_redirect) . '"></noscript>';
                exit;
            }
        }

        $redirect_url = admin_url('admin.php?page=mobile-push-notification&success=1&sent=' . $sent_count);
    }

    // Redirect back to admin page
    while (ob_get_level()) {
        ob_end_clean();
    }

    // Log the redirect attempt
    file_put_contents($debug_file, date('Y-m-d H:i:s') . " - Attempting redirect to: {$redirect_url}\n", FILE_APPEND);

    if (!headers_sent()) {
        wp_safe_redirect($redirect_url);
        exit;
    } else {
        // Fallback: JavaScript redirect if headers already sent
        file_put_contents($debug_file, date('Y-m-d H:i:s') . " - Headers already sent, using JS redirect\n", FILE_APPEND);
        echo '<script>window.location.href = "' . esc_js($redirect_url) . '";</script>';
        echo '<noscript><meta http-equiv="refresh" content="0;url=' . esc_url($redirect_url) . '"></noscript>';
        exit;
    }
}

function get_fcm_tokens_from_firestore($regions = [], $libraries = []) {
    if (!defined('FCM_SERVICE_ACCOUNT_PATH') || !file_exists(FCM_SERVICE_ACCOUNT_PATH)) {
        error_log('Push Notification - FCM_SERVICE_ACCOUNT_PATH not defined or file does not exist');
        return [];
    }

    $service_account = json_decode(
        file_get_contents(FCM_SERVICE_ACCOUNT_PATH),
        true
    );

    if (!$service_account || !isset($service_account['project_id'])) {
        error_log('Push Notification - Invalid service account JSON');
        return [];
    }

    $project_id = $service_account['project_id'];
    $access_token = get_firebase_access_token($service_account);

    if (empty($access_token)) {
        error_log('Push Notification - Failed to get Firebase access token');
        return [];
    }

    // Build Firestore REST API URL - directly query fcm collection
    $collection_path = "fcm";
    $url = "https://firestore.googleapis.com/v1/projects/{$project_id}/databases/gamification/documents/{$collection_path}";

    $response = wp_remote_get($url, [
        'headers' => [
            'Authorization' => 'Bearer ' . $access_token,
            'Content-Type' => 'application/json'
        ]
    ]);

    if (is_wp_error($response)) {
        error_log('Firestore API Error: ' . $response->get_error_message());
        return [];
    }

    $body = json_decode(wp_remote_retrieve_body($response), true);

    $tokens = [];
    if (!empty($body['documents'])) {
        error_log('Push Notification - Total Firestore documents: ' . count($body['documents']));

        foreach ($body['documents'] as $document) {
            if (isset($document['fields']['fcm']['stringValue'])) {
                $regionDoc = $document['fields']['region']['stringValue'] ?? '';
                $libraryCode = $document['fields']['libraryCode']['stringValue'] ?? '';

                error_log("Push Notification - Document: region=$regionDoc, libraryCode=$libraryCode");

                // Filter by regions (check if document's region is in selected regions array)
                if (!empty($regions)) {
                    if (!in_array($regionDoc, $regions, true)) {
                        error_log("Push Notification - Skipped: region mismatch ($regionDoc not in " . implode(',', $regions) . ")");
                        continue;
                    }
                }

                // Filter by library if libraries are specified
                if (!empty($libraries)) {
                    if (!in_array($libraryCode, $libraries, true)) {
                        error_log("Push Notification - Skipped: library mismatch ($libraryCode not in " . implode(',', $libraries) . ")");
                        continue;
                    }
                }

                $tokens[] = $document['fields']['fcm']['stringValue'];
            }
        }
    } else {
        error_log('Push Notification - No documents found in Firestore response');
        error_log('Push Notification - Firestore response: ' . print_r($body, true));
    }

    error_log('Push Notification - Returning ' . count($tokens) . ' tokens');
    return $tokens;
}

// Helper function to get OAuth 2.0 access token
function get_firebase_access_token($service_account) {
    $jwt = create_jwt($service_account);

    $response = wp_remote_post('https://oauth2.googleapis.com/token', [
        'body' => [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt
        ]
    ]);

    if (is_wp_error($response)) {
        error_log('OAuth Token Error: ' . $response->get_error_message());
        return '';
    }

    $body = json_decode(wp_remote_retrieve_body($response), true);
    return $body['access_token'] ?? '';
}

// Helper function to create JWT for Google OAuth
function create_jwt($service_account) {
    $header = base64_encode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));

    $now = time();
    $payload = base64_encode(json_encode([
        'iss' => $service_account['client_email'],
        'scope' => 'https://www.googleapis.com/auth/datastore',
        'aud' => 'https://oauth2.googleapis.com/token',
        'exp' => $now + 3600,
        'iat' => $now
    ]));

    $signature_input = $header . '.' . $payload;

    openssl_sign($signature_input, $signature, $service_account['private_key'], 'SHA256');

    return $signature_input . '.' . base64_encode($signature);
}

// Get OAuth2 access token using service account
function get_fcm_access_token() {
    if (!defined('FCM_SERVICE_ACCOUNT_PATH') || !file_exists(FCM_SERVICE_ACCOUNT_PATH)) {
        error_log('FCM: Service account file not found');
        return false;
    }

    $service_account = json_decode(file_get_contents(FCM_SERVICE_ACCOUNT_PATH), true);

    if (!$service_account) {
        error_log('FCM: Invalid service account JSON');
        return false;
    }

    // Create JWT
    $now = time();
    $header = [
        'alg' => 'RS256',
        'typ' => 'JWT'
    ];

    $claim_set = [
        'iss' => $service_account['client_email'],
        'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
        'aud' => 'https://oauth2.googleapis.com/token',
        'exp' => $now + 3600,
        'iat' => $now
    ];

    $segments = [];
    $segments[] = base64_url_encode(wp_json_encode($header));
    $segments[] = base64_url_encode(wp_json_encode($claim_set));
    $signing_input = implode('.', $segments);

    openssl_sign($signing_input, $signature, $service_account['private_key'], 'SHA256');
    $segments[] = base64_url_encode($signature);
    $jwt = implode('.', $segments);

    // Exchange JWT for access token
    $response = wp_remote_post('https://oauth2.googleapis.com/token', [
        'headers' => ['Content-Type' => 'application/x-www-form-urlencoded'],
        'body' => [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt
        ],
        'timeout' => 15
    ]);

    if (is_wp_error($response)) {
        error_log('FCM OAuth error: ' . $response->get_error_message());
        return false;
    }

    $body = json_decode(wp_remote_retrieve_body($response), true);

    if (!isset($body['access_token'])) {
        error_log('FCM: No access token received');
        return false;
    }

    return $body['access_token'];
}

function base64_url_encode($data) {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

// Add this function to handle batch sending
function send_fcm_notification($tokens, $title, $body, $image) {
    // Get OAuth2 token using your service account JSON (via helper you already have)
    $access_token = get_fcm_access_token();
    if (!$access_token) {
        error_log('FCM: Unable to obtain access token');
        return 0;
    }

    // Ensure project id constant is defined
    if (!defined('FCM_PROJECT_ID') || empty(FCM_PROJECT_ID)) {
        error_log('FCM: FCM_PROJECT_ID is not defined');
        return 0;
    }

    $url          = 'https://fcm.googleapis.com/v1/projects/' . FCM_PROJECT_ID . '/messages:send';
    $sent_count   = 0;

    foreach ($tokens as $token) {
        $payload = [
            'message' => [
                'token'        => $token,
                'notification' => [
                    'title' => $title,
                    'body'  => $body,
                ],
            ],
        ];

        if (!empty($image)) {
            $payload['message']['notification']['image'] = $image;
        }

        $response = wp_remote_post($url, [
            'headers' => [
                'Authorization' => 'Bearer ' . $access_token,
                'Content-Type'  => 'application/json',
            ],
            'body'    => wp_json_encode($payload),
            'timeout' => 10,
        ]);

        if (is_wp_error($response)) {
            error_log('FCM send error: ' . $response->get_error_message());
            continue;
        }

        $code = wp_remote_retrieve_response_code($response);
        if ($code >= 200 && $code < 300) {
            $sent_count++;
        } else {
            error_log('FCM send error: HTTP ' . $code . ' - ' . wp_remote_retrieve_body($response));
        }
    }

    return $sent_count;
}

function lfk_sync_notification_status_endpoint($request) {
    error_log('Push Notification Sync API - Triggered by Firebase Cloud Function');

    $body = $request->get_json_params();

    if (empty($body['notifications']) || !is_array($body['notifications'])) {
        return new WP_REST_Response(array(
            'success' => false,
            'message' => 'Missing or invalid notifications array',
        ), 400);
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'push_notifications';
    $updated_count = 0;
    $errors = [];

    foreach ($body['notifications'] as $notification) {
        $wp_id = isset($notification['wp_id']) ? intval($notification['wp_id']) : 0;
        $status = isset($notification['status']) ? sanitize_text_field($notification['status']) : 'sent';
        $sent_count = isset($notification['sent_count']) ? intval($notification['sent_count']) : 0;

        if ($wp_id <= 0) {
            $errors[] = "Invalid wp_id: " . var_export($notification, true);
            continue;
        }

        $result = $wpdb->update(
            $table_name,
            array(
                'status' => $status,
                'sent_count' => $sent_count,
            ),
            array('id' => $wp_id),
            array('%s', '%d'),
            array('%d')
        );

        if ($result !== false) {
            $updated_count++;
            error_log("Push Notification Sync - Updated notification {$wp_id}: status={$status}, sent_count={$sent_count}");
        } else {
            $errors[] = "Failed to update wp_id {$wp_id}: " . $wpdb->last_error;
        }
    }

    return new WP_REST_Response(array(
        'success' => true,
        'message' => "Synced {$updated_count} notifications",
        'updated_count' => $updated_count,
        'errors' => $errors,
        'synced_at_utc' => gmdate('Y-m-d H:i:s'),
    ), 200);
}

/**
 * Save scheduled notification to Firestore
 * This allows Cloud Function to process notifications directly without calling WP API
 */
function save_notification_to_firestore($wp_id, $title, $body, $image, $regions, $libraries, $scheduled_at_utc) {
    if (!defined('FCM_SERVICE_ACCOUNT_PATH') || !file_exists(FCM_SERVICE_ACCOUNT_PATH)) {
        error_log('Firestore Write - FCM_SERVICE_ACCOUNT_PATH not defined or file does not exist');
        return false;
    }

    $service_account = json_decode(file_get_contents(FCM_SERVICE_ACCOUNT_PATH), true);

    if (!$service_account || !isset($service_account['project_id'])) {
        error_log('Firestore Write - Invalid service account JSON');
        return false;
    }

    $project_id = $service_account['project_id'];
    $access_token = get_firebase_access_token($service_account);

    if (empty($access_token)) {
        error_log('Firestore Write - Failed to get Firebase access token');
        return false;
    }

    // Use wp_id as document ID for easy lookup/deletion
    $document_id = strval($wp_id);
    $collection_path = "scheduled_notifications";

    // PATCH with documentId creates or updates the document
    $url = "https://firestore.googleapis.com/v1/projects/{$project_id}/databases/gamification/documents/{$collection_path}/{$document_id}";

    // Convert regions array to Firestore array format
    $regions_array = [];
    foreach ($regions as $region) {
        $regions_array[] = ['stringValue' => $region];
    }

    // Convert libraries array to Firestore array format
    $libraries_array = [];
    foreach ($libraries as $library) {
        $libraries_array[] = ['stringValue' => $library];
    }

    // Build Firestore document fields
    $fields = [
        'wp_id' => ['integerValue' => $wp_id],
        'title' => ['stringValue' => $title],
        'body' => ['stringValue' => $body],
        'image' => ['stringValue' => $image ?: ''],
        'regions' => ['arrayValue' => ['values' => $regions_array]],
        'libraries' => ['arrayValue' => ['values' => $libraries_array]],
        'scheduled_at' => ['timestampValue' => $scheduled_at_utc . 'Z'],
        'status' => ['stringValue' => 'scheduled'],
        'created_at' => ['timestampValue' => gmdate('Y-m-d\TH:i:s') . 'Z']
    ];

    $response = wp_remote_request($url, [
        'method' => 'PATCH',
        'headers' => [
            'Authorization' => 'Bearer ' . $access_token,
            'Content-Type' => 'application/json'
        ],
        'body' => wp_json_encode(['fields' => $fields]),
        'timeout' => 15
    ]);

    if (is_wp_error($response)) {
        error_log('Firestore Write Error: ' . $response->get_error_message());
        return false;
    }

    $response_code = wp_remote_retrieve_response_code($response);
    $response_body = wp_remote_retrieve_body($response);

    if ($response_code >= 200 && $response_code < 300) {
        error_log("Firestore Write - Successfully saved notification {$wp_id} to Firestore");
        return true;
    } else {
        error_log("Firestore Write Error - HTTP {$response_code}: {$response_body}");
        return false;
    }
}

/**
 * Delete scheduled notification from Firestore
 * Called when admin cancels a scheduled notification
 */
function delete_notification_from_firestore($wp_id) {
    if (!defined('FCM_SERVICE_ACCOUNT_PATH') || !file_exists(FCM_SERVICE_ACCOUNT_PATH)) {
        error_log('Firestore Delete - FCM_SERVICE_ACCOUNT_PATH not defined or file does not exist');
        return false;
    }

    $service_account = json_decode(file_get_contents(FCM_SERVICE_ACCOUNT_PATH), true);

    if (!$service_account || !isset($service_account['project_id'])) {
        error_log('Firestore Delete - Invalid service account JSON');
        return false;
    }

    $project_id = $service_account['project_id'];
    $access_token = get_firebase_access_token($service_account);

    if (empty($access_token)) {
        error_log('Firestore Delete - Failed to get Firebase access token');
        return false;
    }

    $document_id = strval($wp_id);
    $collection_path = "scheduled_notifications";
    $url = "https://firestore.googleapis.com/v1/projects/{$project_id}/databases/gamification/documents/{$collection_path}/{$document_id}";

    $response = wp_remote_request($url, [
        'method' => 'DELETE',
        'headers' => [
            'Authorization' => 'Bearer ' . $access_token,
            'Content-Type' => 'application/json'
        ],
        'timeout' => 15
    ]);

    if (is_wp_error($response)) {
        error_log('Firestore Delete Error: ' . $response->get_error_message());
        return false;
    }

    $response_code = wp_remote_retrieve_response_code($response);

    if ($response_code >= 200 && $response_code < 300) {
        error_log("Firestore Delete - Successfully deleted notification {$wp_id} from Firestore");
        return true;
    } else {
        $response_body = wp_remote_retrieve_body($response);
        error_log("Firestore Delete Error - HTTP {$response_code}: {$response_body}");
        return false;
    }
}