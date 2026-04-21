<?php
/**
 * ----------------------------------------------------------------
 * Retrieve all libraries
 * ----------------------------------------------------------------
 */

function l4k_getLibraries($forEndpoint=false) {

    // check first if the query results has been cached
    $cache_key = 'l4k_libraries_' . $forEndpoint;
    $cached = get_transient($cache_key);
    if (!isset($_GET['purge-cache'])) { if ($cached !== false) { return $cached; } }

	$libraries = get_posts([
	    'post_type'      => 'library',
	    'post_status'    => 'publish',
	    'posts_per_page' => -1,
	    'fields'         => 'ids',
	    'orderby'        => 'title',
	    'order'          => 'ASC',
	    'post__not_in'   => [get_field('teacher_default_library', 'option')], // exclude the teacher library from the results
	    'meta_query'     => [
	        [
	            'key'     => 'library_subscription_status',
	            'value'   => 1,
	            'compare' => '=',
	        ],
	    ],
	]);

    $libArr = [];

    if ($libraries) :
        foreach ($libraries as $id) :

        	if ($forEndpoint) :

        		// do this to ouput the data on /endpoints/all-libraries/ (used by mobile app)

        		$barcodeArr = array();
				if (have_rows('library_barcodes', $id)) :
					while (have_rows('library_barcodes', $id)) : the_row();
						$barcodeArr[] = array(	'barcode_prefix' 	=> get_sub_field('barcode_prefix'),
												'barcode_length' 	=> get_sub_field('barcode_length'), 
												'start_date' 		=> get_sub_field('barcode_start_date'), 
												'end_date' 			=> get_sub_field('barcode_end_date'));
					endwhile;
				endif;

	            $libArr[] = [
	                'id'				=> $id,
	                'title'				=> get_the_title($id),
	                'link'				=> get_post_field('post_name', $id),
	                'logo'				=> get_field('logo_dashboard', $id),
	                'banner'			=> get_field('logo_welcome', $id),
	                'description'		=> get_field('library_description', $id),
	                'group_name'		=> get_field('library_group_name', $id),
	                'group_region'		=> get_field('library_group_region', $id),
	                'barcode'			=> $barcodeArr
	            ];

	        else :

	        	// do this just to get all the libraries to be used within the website
	            $libArr[] = [
	                'title'         	=> get_the_title($id),
	                'lib_permalink' 	=> get_permalink($id),
	                'predefined_url' 	=> get_field('library_predefined_url', $id)
	            ];

	        endif;

        endforeach;
    endif;

    set_transient($cache_key, $libArr, 24 * HOUR_IN_SECONDS); // cache the results for 24 hours

    return $libArr;

}

/**
 * ----------------------------------------------------------------
 * Retrieve single library details
 * ----------------------------------------------------------------
 */

function l4k_getLibraryDetails($libraryID) {

    $library = get_post($libraryID);

    $libraryDetails = [
        'library_id'	=> $library->ID,
        'title'			=> $library->post_title,
        'barcodes'		=> get_field('library_barcodes', $library->ID)
    ];

    return $libraryDetails;

}

/**
 * ----------------------------------------------------------------
 * Retrieve all languages
 * ----------------------------------------------------------------
 */

function l4k_getLanguages($getComingSoon=false, $exclude=true) {

    // check first if the query results has been cached
    $cache_key = 'l4k_languages_' . $getComingSoon . '_' . $exclude;
    $cached = get_transient($cache_key);
    if (!isset($_GET['purge-cache'])) { if ($cached !== false) { return $cached; } }

    $metaQuery = array();

    if ($exclude) { $excludeIDs = array('127596', '127598', '127600'); } // eng-au, eng-us, eng-uk

    if ($getComingSoon) 
    {
		$metaQuery = [
		    ['key' => 'lang_coming_soon','value' => 1,'compare' => '=']
		];
    }
    else
    {
		$metaQuery = [
		    'relation' => 'OR',
		    ['key' => 'lang_coming_soon', 'value' => 0],
		    ['key' => 'lang_coming_soon', 'compare' => 'NOT EXISTS']
		];
    }

    $languages = get_posts([
        'post_type'      => 'language',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'fields'         => 'ids',
        'orderby'        => 'title',
        'order'          => 'ASC',
        'meta_query'     => $metaQuery,
        'post__not_in'   => $excludeIDs,
    ]);

    $langArr = array();

    if ($languages) {
        foreach ($languages as $id) {
        	if (!$getComingSoon) { $latestBookReleased = l4k_getLatestBookReleaseByLanguage($id); }
            $langArr[] = [
                'lang_id'    		=> $id,
                'date_published'    => get_the_date('YmdHis', $id),
                'title'             => get_the_title($id),
                'native_label'      => get_field('lang_native_label', $id),
                'flag_url'          => get_field('lang_flag_image', $id),
                'book_latest'		=> ($latestBookReleased) ? $latestBookReleased->post_title : '',
                'book_latest_date'	=> date("YmdHis", strtotime($latestBookReleased->post_date)),
                'total_views'		=> (get_field('total_views', $id)) ? get_field('total_views', $id) : 0,
                'marketing'			=> get_field('marketing_collateral', $id),
                'lang_permalink'    => get_permalink($id)
            ];
        }
    }

    set_transient($cache_key, $langArr, 168 * HOUR_IN_SECONDS); // cache the results for 168 hours (7 days)

    return $langArr;

}

/**
 * ----------------------------------------------------------------
 * Retrieve languages in LEGACY taxonomy-compatible format
 * ----------------------------------------------------------------
 */
function l4k_getLanguages_legacy_format($search = '', $order = 'ASC') {

	//check first if the query results has been cached
	$cache_key = 'l4k_languages_legacy_' . $search . '_' . $order;

    $cached = l4k_cacheGet($cache_key);
	if ($cached !== false) return $cached;

    $languages = l4k_getLanguages(false, false);

    $results = [];

    foreach ($languages as $lang) {

        // Optional search filter (legacy ?search=man)
        if ($search && stripos($lang['title'], $search) === false) {
            continue;
        }

        $post = get_post($lang['lang_id']);

        $results[] = [
            'term_id'    => $lang['lang_id'],               // mapped
            'name'       => $lang['title'],                 // mapped
            'slug'       => $post->post_name,               // mapped
            'description'=> get_field('lang_description', $lang['lang_id']) ?: '',
            'symbol'     => $lang['native_label'],
            'count'      => $lang['total_views'], 
            'image_web'  => get_field('lang_flag_image', $lang['lang_id']) ?: '',
            'image'      => get_field('lang_flag_mobile_image', $lang['lang_id']) ?: '',
            'btn_play'   => get_field('mobile_button_play', $lang['lang_id']) ?: '',
            'btn_pause'  => get_field('mobile_button_pause', $lang['lang_id']) ?: '',
        ];
    }

    // Sort results based on order parameter
    if ($order === 'DESC') {
        krsort($results);
    } else {
        ksort($results);
    }

	$results = array_values($results);

	l4k_cacheSet($cache_key, $results, 168 * HOUR_IN_SECONDS);
    
	return $results;
}

/**
 * ----------------------------------------------------------------
 * Get latest book released across the board
 * ----------------------------------------------------------------
 */

function l4k_getLatestBookReleased() {

    $book = get_posts([
        'post_type'      	=> 'book',
        'post_status'    	=> 'publish',
        'numberposts' 		=> 1,
        'orderby'       	=> 'date',
        'order'          	=> 'DESC',
    ]);

    if ($book) { return $book[0]; }
    else { return; }

}

/**
 * ----------------------------------------------------------------
 * Get latest book released for the language
 * ----------------------------------------------------------------
 */

function l4k_getLatestBookReleaseByLanguage($langID) {

    $book = get_posts([
        'post_type'      	=> 'book',
        'post_status'    	=> 'publish',
        'numberposts' 		=> 1,
		'meta_query'     	=> [
            [
                'key'     	=> 'language',
                'value'   	=> $langID,
                'compare' 	=> '=',
            ],
        ],
        'orderby'       	=> 'date',
        'order'          	=> 'DESC',
    ]);

    if ($book) { return $book[0]; }
    else { return; }

}

/**
 * ----------------------------------------------------------------
 * Retrieve single language details
 * ----------------------------------------------------------------
 */

function l4k_getLanguageDetails($langID) {

   $mobile = get_field('mobile_button', $langID);

    $langDetails = [
        'lang_id'          => $langID,
        'date_published'   => get_the_date('Ymd', $langID),
        'title'            => get_the_title($langID),
        'native_label'     => get_field('lang_native_label', $langID),
        'flag_url'         => get_field('lang_flag_image', $langID),
        'variant_label'    => get_field('variant_label', $langID),
        'marketing'        => get_field('marketing_collateral', $langID),
        'lang_permalink'   => get_permalink($langID),

        'mobile_button' => [
            'play'            => $mobile['play'] ?? '',
            'pause'           => $mobile['pause'] ?? '',
            'bilingual_play'  => $mobile['bilingual_play'] ?? '',
            'bilingual_pause' => $mobile['bilingual_pause'] ?? '',
        ]
    ];

    return $langDetails;	

}

/**
 * ----------------------------------------------------------------
 * Retrieve all books based on language
 * ----------------------------------------------------------------
 */

function l4k_getBooks($langID, $isFeatured=true, $isReadingPack=false, $libraryID=null) {

   // check first if the query results has been cached
    $cache_key = 'l4k_books_' . $langID . '_' . ($isFeatured ? 'featured' : 'all') . '_' . ($isReadingPack ? 'readingpack' : 'default');
    $cached = l4k_cacheGet($cache_key);
	if ($cached !== false) return $cached;

	$metaQuery = ['relation' => 'AND'];

	// if featured, add featured filter
	if ($isFeatured) { 
		$metaQuery[] = ['key' => 'featured', 'value' => 1];
	}

	$metaQuery[] = ['key' => 'language', 'value' => $langID];

    $books = [];

    if($isReadingPack) {
		$books = l4k_getReadingPacksBooks($libraryID, $langID);	
    } 
    else 
    {

        // default all books behaviour
        $books = get_posts([
            'post_type' => 'book',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'fields' => 'ids',
            'meta_query' => $metaQuery,
            'meta_key' => 'levels_tier',
            'orderby' => [
                'meta_value_num' => 'ASC',
                'rand' => 'ASC'
            ],
        ]);

    }

    $bookArr = array();

    $langDetails = l4k_getLanguageDetails($langID);

    if ($books) {
        foreach ($books as $id) {

        	$bookButtons = array();
        	$linkedBooks = l4k_getLinkedBooks($langDetails['lang_id'], $id, get_field('native_story', $id), false);

        	if ($linkedBooks) {
        		foreach ($linkedBooks as $lb) {
        			$bookButtons[] = array(	'button_label' 		=> $langDetails['variant_label'][$lb['book_type']], 
        									'book_type' 		=> $lb['book_type'], 
        									'book_permalink' 	=> $lb['book_permalink']);
        		}
        	}

            $bookArr[] = [
                'book_id'    		=> $id,
                'date_published'    => get_the_date('YmdHis', $id),
                'image_url' 		=> l4k_normalizeImageUrl(get_field('book_image_url', $id)),
                'story_id'			=> get_field('native_story', $id),
                'english_title'		=> get_the_title(get_field('native_story', $id)),
                'native_title'		=> get_field('native_title', $id),
                'level'				=> get_field('levels_level', $id),
                'level_nicename'	=> l4k_getLevelNicename(get_field('levels_level', $id)),
                'tier'				=> get_field('levels_tier', $id),
                'views'				=> get_field('additional_details_views', $id),
				'views_3mos'		=> get_field('additional_details_views_last_3_months', $id),
                'has_quiz'			=> l4k_hasQuiz($id),
                'is_non_fiction'	=> get_field('filter_tags_non_fiction', $id),
                'book_buttons'		=> $bookButtons,
                'book_type'			=> get_field('book_type', $id),
                'book_permalink'    => get_permalink($id)
            ];
        }
    }

	l4k_cacheSet($cache_key, $bookArr, 168 * HOUR_IN_SECONDS);
    return $bookArr;

}

/**
 * ----------------------------------------------------------------
 * Sort Books by 3-month Views Descending
 * parameter: post_ids
 * returns: array of books
 * ----------------------------------------------------------------
 */

function l4k_sort_by_3mos_views_desc(array $post_ids): array {

    usort($post_ids, function ($a, $b) {
        $viewsA = (int) get_post_meta($a, 'additional_details_views_last_3_months', true);
        $viewsB = (int) get_post_meta($b, 'additional_details_views_last_3_months', true);
        return $viewsB <=> $viewsA; // DESC
    });
    return $post_ids;

}

/**		
 * ----------------------------------------------------------------
 * Retrieve all books from Reading Packs
 * ----------------------------------------------------------------
 */

function l4k_getReadingPacksBooks($libraryID, $langID, $isSimilar=false, $isFilter=false,  $excludeIDs = []) {

    $cache_key = 'l4k_reading_pack_' . $libraryID . '_' . $langID . '_' . ($isSimilar ? 'similar' : 'default') . '_' . ($isFilter ? 'filter' : 'normal');

	$cached = l4k_cacheGet($cache_key);
	if ($cached !== false) return $cached;

	$MAX_READING_PACK = get_field('reading_pack_story_count', 'option');

	// Order logic
	$orderArgs = [];

	if ($isSimilar || $isFilter) {
		$orderArgs['orderby'] = 'rand';
	} else {
		// default: Reading Pack order
		$orderArgs['orderby'] = [
			'views_3mos' => 'DESC',
		];
	}

	$baseArgs = [
		'post_type'      => 'book',
		'post_status'    => 'publish',
		'fields'         => 'ids',
		'meta_query'     => [
			'relation' => 'AND',
			['key' => 'featured', 'value' => 1],
			['key' => 'language', 'value' => $langID],
			[
				'key'     => 'levels_level',
				'compare' => 'IN',
				'value'   => ['2', '3', '4+'],
			],
			// named clause for ordering
	        'views_3mos' => [
	            'relation' => 'OR',
	            ['key' => 'additional_details_views_last_3_months', 'compare' => 'EXISTS'],
	            ['key' => 'additional_details_views_last_3_months', 'compare' => 'NOT EXISTS'],
	        ],
		],
	];


	// get the max story count of the language for that library
	// then overwrite the value of $MAX_READING_PACK set above
	if (have_rows('language_packs', $libraryID)) :
		while (have_rows('language_packs', $libraryID)) : the_row();
			if (get_sub_field('language') == $langID) { 
				$MAX_READING_PACK = get_sub_field('story_count');
			}
		endwhile;
	endif;

	if (!empty($excludeIDs)) {
		$baseArgs['post__not_in'] = (array) $excludeIDs;
	}

	// get all tier 1 books
	$tier1 = get_posts(array_merge($baseArgs, $orderArgs, [
		'posts_per_page' => -1,
		'meta_query'     => array_merge($baseArgs['meta_query'], [
			['key' => 'levels_tier', 'value' => 1, 'type' => 'NUMERIC'],
		]),
	]));
	// Enforce reading pack size
	$tier1 = array_slice($tier1, 0, $MAX_READING_PACK);
	$books = $tier1;

	// error_log('--- RAW TIER 1 ---');
	// error_log(print_r($tier1, true));
	
    // if tier 1 is less than MAX_READING_PACK, get tier 2 books to fill up
    $remaining = $MAX_READING_PACK - count($books);

    if ($remaining > 0) {

		$tier2 = get_posts(array_merge($baseArgs, $orderArgs, [
			'posts_per_page' => $remaining,
			'meta_query'     => array_merge($baseArgs['meta_query'], [
				['key' => 'levels_tier', 'value' => 2, 'type' => 'NUMERIC'],
			]),
		]));

		// error_log('--- RAW TIER 2 ---');
		// error_log(print_r($tier2, true));

		// Fill remaining slots
		$books = array_merge(
			$books,
			array_slice($tier2, 0, $remaining)
		);
	}

	l4k_cacheSet($cache_key, $books, 24 * HOUR_IN_SECONDS);
	return $books;

}

/**
 * ----------------------------------------------------------------
 * Retrieve All Books by Language for Mobile Endpoint
 * parameter: languageId
 * returns: array of book details
 * ----------------------------------------------------------------
 */
function l4k_getAllBooksByLanguageForMobile($langId) {

	$metaQuery = ['relation' => 'AND'];
	$metaQuery[] = ['key' => 'language', 'value' => $langId];

	$books = get_posts([
		'post_type'      => 'book',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'fields'         => 'ids',
		'meta_query'     => $metaQuery,
		'meta_key'       => 'levels_tier',
		'orderby'        => [
			'meta_value_num' => 'ASC',
			'rand'           => 'ASC'
		],
	]);

	$bookArr = array();
    $langDetails = l4k_getLanguageDetails($langId);

    if ($books) {
        foreach ($books as $id) {
        	$bookButtons = array();
        	$linkedBooks = l4k_getLinkedBooks($langDetails['lang_id'], $id, get_field('native_story', $id), false);
			if ($linkedBooks) {
				$bookArr[] = [
					'id'    		    => $id,
					'title'		        => get_the_title(get_field('native_story', $id)),
					'image'			    => get_field('book_image_url', $id),
					'filter_tags'		=> l4k_getLevelNicename(get_field('levels_level', $id)),
           	    ];
			}
        }
    }
	return $bookArr;

}

/**
 * ----------------------------------------------------------------
 * Retrieve Book Details
 * parameter: bookID
 * returns: array of book details
 * ----------------------------------------------------------------
 */

function l4k_getBookDetails($bookID) {

   	// check first if the query results has been cached
    $cache_key = 'l4k_book_details_' . $bookID;
    $cached = l4k_cacheGet($cache_key);
	if ($cached !== false) return $cached;

    $langDetails = l4k_getLanguageDetails(get_field('language', $bookID));
	$rawLanguageField = get_field('language', $bookID);
	$rawStoryField    = get_field('native_story', $bookID);
	$linkedBooks = l4k_getLinkedBooks(
		$rawLanguageField,   
		$bookID,
		$rawStoryField,
		false
	);
	
	// error_log('--- LINKED BOOKS DEBUG START ---');
	// error_log(print_r($linkedBooks, true));
	// error_log('--- LINKED BOOKS DEBUG END ---');

	$storyActivities = [];
	$categories = []; 
	$tag = [];
	$type      = get_field('book_type', $bookID);
    $permalink = get_permalink($bookID);
	$audioFiles = [];
	$audioFilesBilingual = [];
	$audioPlay = [];
	$coverImages = [];

	if (have_rows('audio_billingual', $bookID)) {
    	while (have_rows('audio_billingual', $bookID)) {
        	the_row();
	        $audioFilesBilingual[] = [
    	        'page_number' => get_sub_field('page_number'),
        	    'audio_file'  => get_sub_field('audio_file'),
        	];
    	}
	}

	if (have_rows('audio', $bookID)) {
    	while (have_rows('audio', $bookID)) {
        	the_row();
	        $audioFiles[] = [
    	        'page_number' => get_sub_field('page_number'),
        	    'audio_file'  => get_sub_field('audio_file'),
        	];
    	}
	}

	if (have_rows('audio_play', $bookID)) {
    	while (have_rows('audio_play', $bookID)) {
        	the_row();
	        $audioPlay[] = [
    	        'before' => get_sub_field('before'),
        	    'after'  => get_sub_field('after'),
        	];
    	}
	}
	
	// cover images
	$image1 = get_field('cover_image', $bookID);
	$image2 = get_field('cover_image_2', $bookID);

	//for mobile buttons
	$mobile = $langDetails['mobile_button'] ?? [];
	
	// for book buttons details
	$bookButtons = [];

   	if (!empty($linkedBooks)) {
		foreach ($linkedBooks as $linked) {
			$linkedID   = $linked['book_id'];
			$linkedType = $linked['book_type'];
			$label = $langDetails['variant_label'][$linkedType] ?? '';
			$icon = ($linkedType === 'flipbook') ? ' 📖' : ' ▶️';
			$bookButtons[] = array(
				'_button_id'          => $linkedID,				
				'_button_title'       => get_the_title($linkedID),				
				'_button_content'     =>  '<i>'.get_field('details_author', $linkedID). '</i><br>' .get_field('details_description', $linkedID),
				'_button_name'        => $label . $icon,				
				'_button_image'       => get_field('book_image_url', $linkedID),				
				'_button_link'        => get_permalink($linkedID),
				'_button_vimeo_video_link' => get_vimeo_progressive_url(get_field('video_source', $linkedID)),
				'is_double_page'      => (bool) get_field('is_double_page', $linkedID),
				'book_type'           => $linkedType,				
			);
		}
	}

	// for story activities
	$baseUrl = get_site_url();
	if (have_rows('download_links', $bookID)) {
		while (have_rows('download_links', $bookID)) {
			the_row();
			$urlPath = get_sub_field('url_path');  // direct PDF file    
			$pdfFile = get_sub_field('pdf'); // iframe URL
			$title   = get_sub_field('title');

			// Embedded (Quiz)
			if (!empty($urlPath)) {
				$storyActivities[] = [
					'type'     => 'Embedded',
					'pdf'      => '',
					'title'    => $title,
					'Activity' => $urlPath,
					'image'    => $baseUrl . '/wp-content/themes/lote4kids-child/assets/img/side-quiz.webp',
				];
			}

			// PDF
			if (empty($urlPath) && !empty($pdfFile)) {
				$storyActivities[] = [
					'type'     => 'PDF',
					'pdf'      => $pdfFile,
					'title'    => $title,
					'Activity' => '',
					'image'    => $baseUrl . '/wp-content/themes/lote4kids-child/assets/img/icon-pdf.png',
				];
			}
		}
	}

	// for video sources
    $video_sources[] = array(
        'views'             => get_field('additional_details_views', $bookID),
        'type'              => $type,
		'vimeo'    => (strpos($type, 'video_') === 0) ? $permalink : null,
		'flipbook' => ($type === 'flipbook') ? $permalink : '',
		'image'				=> get_field('book_image_url', $bookID),
		'duration'			=> "",
    );   
	
	$video_sources_1[] = array(       
        'type'              => $type,
        'flipbook'          => $type === 'flipbook' ? $permalink : null,
		'pdfUrl'            => get_field('pdf_file', $bookID),
		'isRTL'			    => (bool) get_field('pdf_rtl_file', $bookID),
		'pdfUrlRTL'         => get_field('pdf_rtl_file', $bookID),
		'audioFile'			=> $audioFiles,
		'audioFileBilingual'=> $audioFilesBilingual,
		'audioPlay'			=> $audioPlay,
		'is_double_page'	=> (bool) get_field('is_double_page', $bookID),
		'cover_image'   	=> $image1 ?? null,
		'cover_image_2' 	=> $image2 ?? null,
		'image_layout'		=> get_field('layout', $bookID),
		'duration'			=> get_field('duration', $bookID),
		'views'             => get_field('additional_details_views', $bookID),

    ); 
	
	$audioPlay[] = array(
		'audioFile'			=> $audioFiles,
	);

	$bookDetails = [
		'id'    		    => $bookID,
		'date_published'    => get_the_date('YmdHis', $bookID),
		'image'			    => get_field('book_image_url', $bookID),
		'story_id'			=> get_field('native_story', $bookID),
		'title'		        => get_the_title($bookID),
		'title_language'	=> get_field('native_title', $bookID),
		'content'           => '<i>'.get_field('details_author', $bookID). '</i><br>' .get_field('details_description', $bookID),
		'level'				=> get_field('levels_level', $bookID),
		'filter_tags'	    => l4k_getLevelNicename(get_field('levels_level', $bookID)),
		'tier'				=> get_field('levels_tier', $bookID),
		'views'				=> get_field('additional_details_views', $bookID),
		'has_quiz'			=> l4k_hasQuiz($bookID),
		'is_non_fiction'	=> get_field('filter_tags_non_fiction', $bookID),
		'book_type'			=> $type,
        'button_details'	=> $bookButtons,
		'book_permalink'    => get_permalink($bookID),
		'direct_video_link' => $permalink,
		'story_activities'  => $storyActivities,
        'video_sources'     => $video_sources,
		'category'			=> $categories,
		'tag'				=> $tag,
		'vimeo_video_link'  => $permalink ,
     	'button_bilingual_play'  => $mobile['bilingual_play'] ?? '',
		'button_bilingual_pause' => $mobile['bilingual_pause'] ?? '',
		'button_play'            => $mobile['play'] ?? '',
		'button_pause'           => $mobile['pause'] ?? '',
		'video_sources_1'   => $video_sources_1,	
	];

	l4k_cacheSet($cache_key, $bookDetails, 168 * HOUR_IN_SECONDS);	
	return $bookDetails;

}

/**
 * ----------------------------------------------------------------
 * Retrieve progressive URL for Vimeo video
 * ----------------------------------------------------------------
 */

function get_vimeo_progressive_url($vimeo_url) {

    $token = VIMEO_ACCESS_TOKEN;
	if (!$vimeo_url) return null;

    // Extract video ID
    //preg_match('/vimeo\.com\/(\d+)/', $vimeo_url, $matches);
	preg_match('/vimeo\.com\/(?:video\/)?(\d+)/', $vimeo_url, $matches);
    $video_id = $matches[1] ?? null;

    if (!$video_id) return null;

	$cache_key = 'vimeo_progressive_' . $video_id;
    $cached = l4k_cacheGet($cache_key);
	if ($cached !== false) return $cached;

    $response = wp_remote_get(
        "https://api.vimeo.com/videos/$video_id",
        array(
			'timeout' => 5,
            'headers' => array(
                'Authorization' => 'Bearer ' . $token,
            )
        )
    );

	if (is_wp_error($response)) {
        l4k_cacheSet($cache_key, null, 6 * HOUR_IN_SECONDS);
        return null;
    }

	$status = wp_remote_retrieve_response_code($response);

	if ($status !== 200) {
		l4k_cacheSet($cache_key, null, 6 * HOUR_IN_SECONDS);
		return null;
	}


    $body = json_decode(wp_remote_retrieve_body($response), true);
	if (!$body) {
    l4k_cacheSet($cache_key, null, 6 * HOUR_IN_SECONDS);
    	return null;
	}
	$url = null;

    if (!empty($body['play']['progressive'])) {
        foreach ($body['play']['progressive'] as $file) {
            if ($file['rendition'] === '1080p') {
                $url = $file['link'];
                break;
            }
        }

		// fallback if 1080p not available
        if (!$url) {
            $url = $body['play']['progressive'][0]['link'] ?? null;
        }
    }

	//set_transient($cache_key, $url, 7 * DAY_IN_SECONDS);
	l4k_cacheSet($cache_key, $url, 30 * DAY_IN_SECONDS);
    return $url;
}

/**
 * ----------------------------------------------------------------
 * Retrieve similar books based on reading level
 * ----------------------------------------------------------------
 */

function l4k_getSimilarBooksByLevel($langID, $bookID, $readingLevel, $bookType, $numBooks=4, $isReadingPack=false) {

    $metaQuery = ['relation' => 'AND'];
     
	$metaQuery = [
		['key' => 'language', 'value' => $langID],
	    ['key' => 'levels_level', 'value' => $readingLevel],
	    ['key' => 'book_type', 'value' => $bookType]
	];

     // Reading Pack mode - get books from the same reading pack first
	if ($isReadingPack) 
	{	    
		$libraryID =$_SESSION['library_id'];
		$books = l4k_getReadingPacksBooks($libraryID, $langID, $isSimilar=true, $isFilter=false, $excludeIDs = array($bookID));
	}else{
		$books = get_posts([
			'post_type'      => 'book',
			'post_status'    => 'publish',
			'posts_per_page' => $numBooks,
			'fields'         => 'ids',
			'orderby'        => 'rand',
			'post__not_in'   => array($bookID),
			'meta_query'     => $metaQuery
    	]);
	}
    
    $bookArr = array();

    if ($books) {
        foreach ($books as $id) {

            $bookArr[] = [
                'date_published'    => get_the_date('Ymd', $id),
                'image_url'			=> l4k_normalizeImageUrl(get_field('book_image_url', $id)),
                'book_type'			=> get_field('book_type', $id),
                'english_title'		=> get_the_title(get_field('native_story', $id)),
                'native_title'		=> get_field('native_title', $id),
                'level'				=> get_field('levels_level', $id),
                'level_nicename'	=> l4k_getLevelNicename(get_field('levels_level', $id)),
                'book_permalink'    => get_permalink($id)
            ];
        }
    }

	// if similar books are less than 4, get filler books
	// do NOT get filler books for language id 92 Cook Islands
	// do NOT get filler books for language id 135 Niue
    if ((count($bookArr) < $numBooks) && !in_array($langID, [92, 135])) {
    	$difference = $numBooks - count($bookArr);
    	$fillerBooksArr = l4k_getFillerBooks($langID, $bookID, $bookType, $difference);
    	$bookArr = array_merge($bookArr, $fillerBooksArr);
    }else{
		$bookArr = array_slice($bookArr, 0, $numBooks);
	}

    return $bookArr;

}

/**
 * ----------------------------------------------------------------
 * Retrieve filler books if similar books above is less than 4
 * ----------------------------------------------------------------
 */

function l4k_getFillerBooks($langID, $bookID, $bookType, $numBooks, $isReadingPack=false) {

    $metaQuery = array();

	$metaQuery = [
	    'relation' => 'AND',

	    ['key' => 'language', 'value' => $langID],
	    ['key' => 'book_type', 'value' => $bookType]
	];

	if($isReadingPack){
		$libraryID =$_SESSION['library_id'];
		$fillerBooks = l4k_getReadingPacksBooks($libraryID, $langID, $isSimilar=false, $isFilter=true, $excludeIDs = array($bookID));
	}else{
			$fillerBooks = get_posts([
			'post_type'      => 'book',
			'post_status'    => 'publish',
			'posts_per_page' => $numBooks,
			'fields'         => 'ids',
			'orderby'        => 'rand',
			'post__not_in'   => array($bookID),
			'meta_query'     => $metaQuery
		]);
	}    

    $fillerBooksArr = array();

    if ($fillerBooks) {
        foreach ($fillerBooks as $id) {

            $fillerBooksArr[] = [
                'date_published'    => get_the_date('Ymd', $id),
                'image_url'			=> l4k_normalizeImageUrl(get_field('book_image_url', $id)),
                'book_type'			=> get_field('book_type', $id),
                'english_title'		=> get_the_title(get_field('native_story', $id)),
                'native_title'		=> get_field('native_title', $id),
                'level'				=> get_field('levels_level', $id),
                'level_nicename'	=> l4k_getLevelNicename(get_field('levels_level', $id)),
                'book_permalink'    => get_permalink($id)
            ];
            
        }
    }

    return $fillerBooksArr;

}

/**
 * ----------------------------------------------------------------
 * Get levels nicename
 * ----------------------------------------------------------------
 */

function l4k_getLevelNicename($level) {

	switch($level) {
		case "P" 	: $level_nicename = "Picture Cards"; 	break;
		case "A" 	: $level_nicename = "ABC"; 				break;
		case "1" 	: $level_nicename = "Level 1"; 			break; 
		case "2" 	: $level_nicename = "Level 2"; 			break; 
		case "3" 	: $level_nicename = "Level 3"; 			break;
		case "4+" 	: $level_nicename = "Level 4+"; 		break;
	}

	return $level_nicename;

}

/**
 * ----------------------------------------------------------------
 * Retrieve linked books for read it your way
 * ----------------------------------------------------------------
 */

function l4k_getLinkedBooks($langID, $bookID, $storyID, $exemptCurrent=true) {

    // check first if the query results has been cached
    $cache_key = 'l4k_books_' . $bookID . '_linked';
    $cached = l4k_cacheGet($cache_key);
	if ($cached !== false) return $cached;

	$metaQuery = [
	    'relation' => 'AND',

	    ['key' => 'language', 'value' => $langID],
	    ['key' => 'native_story', 'value' => $storyID]
	];

	$exemptArr = ($exemptCurrent) ? array($bookID) : array();

    $books = get_posts([
        'post_type'      	=> 'book',
        'post_status'    	=> 'publish',
        'posts_per_page' 	=> -1,
        'fields'         	=> 'ids',
        'post__not_in'   	=> $exemptArr,
		'meta_key'       	=> 'book_type',
		'orderby'        	=> 'meta_value',
		'order'				=> 'DESC',
        'meta_query'     	=> $metaQuery
    ]);

    $bookArr = array();

    if ($books) {
        foreach ($books as $id) {

       	 	$bookArr[] = [
                'book_id'			=> $id,
                'image_url'			=> l4k_normalizeImageUrl(get_field('book_image_url', $id)),
                'book_type'			=> get_field('book_type', $id),
                'book_permalink'    => get_permalink($id)
            ];

        }
    }

	l4k_cacheSet($cache_key, $bookArr, 168 * HOUR_IN_SECONDS);
    return $bookArr;

}

/**
 * ----------------------------------------------------------------
 * Retrieve all activities
 * ----------------------------------------------------------------
 */

function l4k_getActivities($getComingSoon=false, $type='online') {

    $metaQuery = array();

    if ($getComingSoon) 
    {
		$metaQuery = [
		    ['key' => 'coming_soon', 'value' => 1, 'compare' => '=' ]
		];
    }
    else
    {
		$metaQuery = [
		    'relation' => 'AND',
		    [
		        'relation' => 'OR',
		        ['key' => 'coming_soon', 'value' => 0, 'compare' => '='],
		        ['key' => 'coming_soon', 'compare' => 'NOT EXISTS']
		    ],

		    ['key' => 'type', 'value' => $type]
		];
    }

    $activities = get_posts([
        'post_type'      => 'activity',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'fields'         => 'ids',
        'meta_key'       => 'order',
	    'orderby'        => 'meta_value_num',
	    'order'          => 'ASC',
        'meta_query'     => $metaQuery
    ]);

    $activityArr = array();

    if ($activities) {
        foreach ($activities as $id) {

            $activityArr[] = [
                'date_published'    => get_the_date('Ymd', $id),
                'title'				=> get_the_title($id),
                'type'				=> get_field('type', $id),
                'activity_image'	=> l4k_normalizeImageUrl(get_field('activity_image', $id)),
                'iframe_source'		=> get_field('iframe_source', $id),
                'collections'		=> get_field('collections', $id),
                'permalink' 	  	=> get_permalink($id)
            ];
        }
    }

    return $activityArr;

}

/**
 * ----------------------------------------------------------------
 * Retrieve the book's related activities and quizzes
 * Separate into 2 arrays, one for activities and one for quizzes
 * ----------------------------------------------------------------
 */

function l4k_getActivitiesAndQuizzes($bookID) {

	$arr = array();

	if (have_rows('download_links', $bookID)) :
		while (have_rows('download_links', $bookID)) : the_row();

			if (str_contains(get_sub_field('pdf'), '<iframe')) 
			{ 
				$arr['quizzes'][] = array(	'activity' 	=> get_sub_field('activity'),
											'embed' 	=> get_sub_field('pdf'),
											'title' 	=> get_sub_field('title'),
											'url_path' 	=> get_sub_field('url_path'));
			}
			else
			{
				$arr['activities'][] = array(	'activity' 	=> get_sub_field('activity'),
												'pdf' 		=> get_sub_field('pdf'),
												'title' 	=> get_sub_field('title'),
												'url_path' 	=> get_sub_field('url_path'));
			}

		endwhile;
	endif;

	return $arr;

}

/**
 * ----------------------------------------------------------------
 * Retrieve items to be shown on the top of the member home page
 * Recently viewed
 * Quick Playlist
 * Similar Books
 * ----------------------------------------------------------------
 */

function l4k_getMemberHomeMeta() {

	$metaArr = array();

	if ($_SESSION['last_viewed_book']) 
	{
		// recently viewed language

		$languageID = get_field('language', $_SESSION['last_viewed_book']);
		$metaArr['last_viewed_language'] 	= array(
			'permalink' 	=> get_the_permalink($languageID),
			'img_url' 		=> get_field('lang_flag_image', $languageID),
			'label' 		=> get_field('lang_native_label', $languageID),
			'label_english' => get_the_title($languageID),
			'type' 			=> 'flag');

		// recently viewed book
		
		$bookID 	= $_SESSION['last_viewed_book'];
		$metaArr['last_viewed_book'] = array(
			'permalink' => get_the_permalink($bookID),
			'img_url' 	=> l4k_normalizeImageUrl(get_field('book_image_url', $bookID)),
			'label' 	=> get_the_title(get_field('native_story', $bookID)),
			'type' 		=> 'book');

		// video playlist

		$quickPlaylist = l4k_getPlaylistsByLanguage($languageID, get_field('levels_level', $_SESSION['last_viewed_book']));
		if ($quickPlaylist) 
		{
			$metaArr['playlist'] = array(
				'permalink' => $quickPlaylist[0]['playlist_permalink'],
				'img_url' 	=> l4k_normalizeImageUrl($quickPlaylist[0]['book_image_url']),
				'label' 	=> $quickPlaylist[0]['display_title'],
				'type' 		=> 'book');
		}

		// similar books

		$numBooks = ($quickPlaylist) ? 3 : 4; // if no playlist, show 4 similar books
		$similarBooks = l4k_getSimilarBooksByLevel($languageID, $bookID, get_field('levels_level', $bookID), get_field('book_type', $bookID), $numBooks);
		if ($similarBooks) {
			foreach ($similarBooks as $book => $b) {
				$metaArr[] 	= array('permalink' => $b['book_permalink'],
									'img_url' 	=> l4k_normalizeImageUrl($b['image_url']),
									'label' 	=> $b['english_title'],
									'type' 		=> 'book');
			}
		}
	}

	return $metaArr; 

}

/**
 * ----------------------------------------------------------------
 * Retrieve activity log
 * ----------------------------------------------------------------
 */

function l4k_getActivityLog() {

    global $wpdb;
    $table = $wpdb->prefix . 'web_activity';
    $query = "	SELECT * 
    			FROM $table 
    			ORDER BY id DESC 
    			LIMIT 100";

    $webActivity = $wpdb->get_results($query);
	$webActivityArr = array();

    foreach ($webActivity as $key => $value) {
		$webActivityArr[] = array(	'id' 			=> $value->id,
									'alert_code' 	=> $value->alert_code,
									'barcode' 		=> $value->barcode,
									'library_name' 	=> $value->library_name,
									'region_name' 	=> $value->region_name,
									'ip' 			=> $value->ip,
									'time' 			=> $value->time,
									'message' 		=> json_decode($value->data));
    }

    return $webActivityArr;

}

/**
 * ----------------------------------------------------------------
 * Retrieve activity log mobile
 * ----------------------------------------------------------------
 */

function l4k_getActivityLogMobile() {

    global $wpdb;
    $table = $wpdb->prefix . 'mobile_activity';
    $query = "	SELECT * 
    			FROM $table 
    			ORDER BY id DESC 
    			LIMIT 100";

    $mobileActivity = $wpdb->get_results($query);
	$mobileActivityArr = array();

    foreach ($mobileActivity as $key => $value) {

		$raw = stripslashes($value->data ?? '');
		$decoded = json_decode($raw, true);

		$mobileActivityArr[] = array(	'id' 			=> $value->id,
										'alert_code' 	=> $value->alert_code,
										'barcode' 		=> $value->barcode,
										'library_name' 	=> $value->library_name,
										'region_name' 	=> $value->region_name,
										'ip' 			=> $value->ip,
										'time' 			=> $value->time,
										'os_type' 		=> $value->os_type,
										'device_type' 	=> $value->device_type,
										'status' 		=> $value->status,
										'message' 		=> $decoded );

    }

    return $mobileActivityArr;

}

/**
 * ----------------------------------------------------------------
 * Retrieve activity log for a single barcode
 * ----------------------------------------------------------------
 */

function l4k_getLearningDashboardContent() {

    global $wpdb;
    $table = $wpdb->prefix . 'web_activity';
    $query = "	SELECT * 
    			FROM $table 
    			WHERE barcode = '".$_POST['barcode']."' 
    			AND time >= DATE_SUB(NOW(), INTERVAL 7 DAY) 
    			ORDER BY id DESC";

    $webActivity = $wpdb->get_results($query);
	$webActivityArr = array();

	if ($webActivity) {
	    foreach ($webActivity as $key => $value) {
			$webActivityArr[] = array(	'id' 			=> $value->id,
										'alert_code' 	=> $value->alert_code,
										'barcode' 		=> $value->barcode,
										'library_name' 	=> $value->library_name,
										'region_name' 	=> $value->region_name,
										'ip' 			=> $value->ip,
										'time' 			=> $value->time,
										'message' 		=> json_decode($value->data, true));
	    }
	}

	$countBooks = $countQuizzes = $countActivities = $countFeathers = $countStreaks = 0;
	$countBooks_types = array();
	$countActivity_types = array();

	if ($webActivityArr) {
	    foreach ($webActivityArr as $key => $value) {

	    	// count books
	    	if ($value['alert_code'] == 1062) { 
	    		$countBooks++; 

	    		// count individual book types
    			switch($value['message']['Type']) {
					case "flipbook": 
						$countBooks_types['Flipbook']++;
						break;
					case "video_english": 
						$countBooks_types['English Video']++;
						break;
					case "video_monolingual": 
						$countBooks_types['Monolingual Video']++;
						break;
					case "video_bilingual": 
						$countBooks_types['Bilingual Video']++;
						break;
				}
	    	}

	    	// count quiz / activity
	    	if ($value['alert_code'] == 1060) { 
	    		if ($value['message']['Activity Type'] == 'Embedded') { $countQuizzes++; }

	    		if ($value['message']['Activity Type'] == 'Pdf') { 
	    			$countActivities++; 

	    			if (str_starts_with($value['message']['Activity Name'], 'Color-Colouring')) { $activityName = 'Color-Colouring'; } 
	    			else if (str_starts_with($value['message']['Activity Name'], 'Spot the Difference')) { $activityName = 'Spot the Difference'; } 
	    			else { $activityName = $value['message']['Activity Name']; }
	    			$countActivity_types[$activityName]++;
	    		}
			}

	    }
	}

	$countFeathers = l4k_getFeatherCount($_SESSION['ownerId']);

	$dashboardContentArr = 
		array(	'countBooks' 			=> $countBooks,
				'countQuizzes' 			=> $countQuizzes,
				'countActivities' 		=> $countActivities,
				'countFeathers' 		=> $countFeathers,
				'countStreaks' 			=> $countStreaks,
				'countBooks_types' 		=> $countBooks_types,
				'countActivity_types' 	=> $countActivity_types );

	$resultsArr = array('status' => 1,
    					'dashboardContentArr' => $dashboardContentArr);

   	wp_send_json($resultsArr);

}

/**
 * ----------------------------------------------------------------
 * Retrieve summary count of all titles per language
 * ----------------------------------------------------------------
 */

function l4k_getBookCountPerLanguage() {

	// check first if the query results has been cached
	$cache_key = 'l4k_lang_count';
	$cached = get_transient($cache_key);
	if (!isset($_GET['purge-cache'])) { if ($cached !== false) { return l4k_splitIntoThree($cached); } }

	// get all languages first
	// no need to join this to the query anymore
	$languages = l4k_getLanguages($getComingSoon=false, $exclude=false); 

	// count all books per language
	// get the count only to optimize the query
	global $wpdb;

	// count only monolingual and bilingual books
	// count all books for cook-islands, niue, and tokelauan
	$results = $wpdb->get_results("
	    SELECT 
	        pm.meta_value AS language_id, -- language ID from post meta
	        COUNT(*) AS total -- total number of books in this language
	    FROM {$wpdb->posts} p
	    INNER JOIN {$wpdb->postmeta} pm 
	        ON pm.post_id = p.ID 
	        AND pm.meta_key = 'language' -- only consider 'language' meta
	    LEFT JOIN {$wpdb->postmeta} bt 
	        ON bt.post_id = p.ID 
	        AND bt.meta_key = 'book_type' -- only consider 'book_type' meta
	    WHERE 
	        p.post_type = 'book'
	        AND p.post_status = 'publish'
	        AND (
	            pm.meta_value IN ('92', '135', '163') -- we count ALL books for cook-islands, niue, and tokelauan
	            OR bt.meta_value IN ('video_monolingual', 'video_bilingual') -- only these book types for all other languages
	        )
	    GROUP BY pm.meta_value
	");

	if ($results) {
		foreach ($results as $row) {
			$resultsArr[$row->language_id] = $row->total;
		}		
	}

	if ($languages) {
		foreach ($languages as $key => $lang) {
			$languages[$key]['book_count'] = ($resultsArr[$lang['lang_id']]) ? $resultsArr[$lang['lang_id']] : '0';
		}
	}

	//set_transient($cache_key, $languages, 168 * HOUR_IN_SECONDS); // cache the results for 168 hours (7 days)
	l4k_cacheSet($cache_key, $languages, 168 * HOUR_IN_SECONDS);
	return l4k_splitIntoThree($languages);

}

/**
 * ----------------------------------------------------------------
 * Retrieve all playlists for a particular language
 * ----------------------------------------------------------------
 */

function l4k_getPlaylistsByLanguage($langID, $byLevel='', $saveToSession=true) {

	$metaQuery = ['relation' => 'AND'];

	if ($byLevel) // get one playlist which is the same level of the recently viewed book
	{
	    $metaQuery[] = ['key' => 'level', 'value' => $byLevel, 'compare' => '='];
	    $metaQuery[] = ['key' => 'language', 'value' => $langID, 'compare' => '='];
	}
	else
	{
		$metaQuery[] = ['key' => 'language', 'value' => $langID, 'compare' => '='];
	}

    $playlists = get_posts([
        'post_type'      	=> 'playlist',
        'post_status'    	=> 'publish',
        'numberposts' 		=> -1,
        'fields'			=> 'ids',
		'meta_query'     	=> $metaQuery,
        'orderby'       	=> 'date',
        'order'          	=> 'DESC',
    ]);

    $langDetails = l4k_getLanguageDetails($langID);

    if ($playlists) {
    	foreach ($playlists as $id) {

			$bookDetails = l4k_getFirstBookOfPlaylist(
				get_field('language', $id),
				get_field('level', $id),
				get_field('level', $id) === 'P' ? 'video_bilingual' : 'video_monolingual'
			);

            $playlistArr[] = [
                'playlist_id'			=> $id,
                'language'				=> get_field('language', $id),
                'level'					=> get_field('level', $id),
                'book_image_url'		=> l4k_normalizeImageUrl($bookDetails['image_url']),
                'book_type'				=> $bookDetails['book_type'],
                'playlist_permalink'	=> get_permalink($id),
                'button_label'			=> $langDetails['variant_label'][$bookDetails['book_type']],
                'display_title'			=> get_field('display_title', $id),
            ];

            // save to session what book is showing in the playlist button so when you 
            // access the playlist page itself, this book will show up as the first one
            if ($saveToSession) { $_SESSION['first_book_level_'.get_field('level', $id)] = $bookDetails['book_id']; }

    	}
    }

    // re-order the $playlistArr
    if ($playlistArr) 
    {
		$levelOrder = [ 'A' => 0, 'P' => 1, '1' => 2, '2' => 3, '3' => 4, '4+'=> 5 ];
		usort($playlistArr, function ($a, $b) use ($levelOrder) {
		    return ($levelOrder[$a['level']] ?? 999) <=> ($levelOrder[$b['level']] ?? 999);
		});
	}

    return $playlistArr;

}

/**
 * ----------------------------------------------------------------
 * Get first book (randomized) of a particular playlist
 * ----------------------------------------------------------------
 */

function l4k_getFirstBookOfPlaylist($langID, $level, $bookType) {

	$bookID = get_posts([
	    'post_type'      	=> 'book',
	    'post_status'    	=> 'publish',
	    'numberposts'    	=> 1,
	    'fields'         	=> 'ids',
	    'meta_query'     	=> [
	        'relation' 		=> 'AND',
	        ['key' => 'language', 'value' => $langID, 'compare' => '='],
	        ['key' => 'levels_level', 'value' => $level, 'compare' => '='],
	        ['key' => 'book_type', 'value' => $bookType, 'compare' => '='],
	    ],
	    'orderby'        => 'rand',
	]);

	// if no bookID found, try the video_monolingual version
	if (!$bookID) 
	{
		$bookID = get_posts([
		    'post_type'      	=> 'book',
		    'post_status'    	=> 'publish',
		    'numberposts'    	=> 1,
		    'fields'         	=> 'ids',
		    'meta_query'     	=> [
		        'relation' 		=> 'AND',
		        ['key' => 'language', 'value' => $langID, 'compare' => '='],
		        ['key' => 'levels_level', 'value' => $level, 'compare' => '='],
		        ['key' => 'book_type', 'value' => 'video_monolingual', 'compare' => '='],
		    ],
		    'orderby'        => 'rand',
		]);
	}

	$bookDetails['book_id'] 	= $bookID[0];
	$bookDetails['image_url'] 	= get_field('book_image_url', $bookID[0]);
	$bookDetails['book_type'] 	= get_field('book_type', $bookID[0]);	

    return $bookDetails;

}

/**
 * ----------------------------------------------------------------
 * Get reading packs for the library
 * ----------------------------------------------------------------
 */

function l4k_getReadingPacks() {

	$readingPackArr = [];
	
	if (have_rows('language_packs', $_SESSION['library_id'])) :
	    while (have_rows('language_packs', $_SESSION['library_id'])) : the_row();
	        $readingPackArr[] = l4k_getLanguageDetails(get_sub_field('language'));
	    endwhile;
	endif;

	return $readingPackArr;

}

/**
 * ----------------------------------------------------------------
 * Get videos of a particular playlist
 * ----------------------------------------------------------------
 */

function l4k_getVideosByPlaylistID($playlistID) {

	$metaQuery = array();
	$books = array();
	$bookArr = array();
	$firstBookInThumbnail = $_SESSION['first_book_level_'.get_field('level', $playlistID)];

	if (get_field('level', $playlistID) == 'P') {
		$metaQuery = [
	        'relation' => 'AND',
		        ['key' => 'levels_level', 'value' => get_field('level', $playlistID), 'compare' => '='],
		        ['key' => 'language', 'value' => get_field('language', $playlistID), 'compare' => '='],
		        ['key' => 'book_type', 'value' => ['video_monolingual', 'video_bilingual'], 'compare' => 'IN'],
		    ];
	} else {
		$metaQuery = [
	        'relation' => 'AND',
		        ['key' => 'levels_level', 'value' => get_field('level', $playlistID), 'compare' => '='],
		        ['key' => 'language', 'value' => get_field('language', $playlistID), 'compare' => '='],
		        ['key' => 'book_type', 'value' => ['video_monolingual'], 'compare' => 'IN'],
		    ];
	}

	// do NOT include the book which is in the thumbnail
	$books = get_posts([
	    'post_type'      => 'book', 
	    'posts_per_page' => get_field('playlist_size', $playlistID), 
	    'post_status'    => 'publish',
	    'orderby'        => 'rand',
	    'fields'         => 'ids',
        'post__not_in'   => [$firstBookInThumbnail],
	    'meta_query'     => $metaQuery,
	]);

	// put the first book from the thumbnail as the first video in the playlist
	// then remove the last item in the array since you added 1 at the top
	if (($firstBookInThumbnail) && (get_field('language', $firstBookInThumbnail) == get_field('language'))) { 
		$books = array_merge([$firstBookInThumbnail], $books); 
		if (count($books) > get_field('playlist_size', $playlistID)) {
			array_pop($books);
		}
	}

	if ($books) {
      	foreach ($books as $id) {

		    $vimeoURL = get_field('video_source', $id); 
		    $vimeoData = l4k_parseVimeoUrl($vimeoURL); 

		    if ($vimeoData) {
		    	$appendtoDescription = (get_field('details_author', $id)) ? '<i>'.get_field('details_author', $id).'</i><br/>' : '';
		        $bookArr[] = [
		            'id'            => $id,
		            'vimeo_id'      => $vimeoData[0],    
		            'vimeo_hash'    => $vimeoData[1] ?? '',  
		            'title'         => get_the_title($id),
		            'native_title'  => get_field('native_title', $id), 
		            'english_title'	=> get_the_title(get_field('native_story', $id)),
		            'description'   => $appendtoDescription . nl2br(get_field('details_description', $id)) . "<div class='universal-meta'>".get_field('universal_meta_data', get_field('native_story', $id))."</div>", 
		            'language'  	=> get_field('language', $id), 
		            'book_type'  	=> get_field('book_type', $id), 
		            'views'         => get_field('additional_details_views', $id), 
		            'date'          => get_the_date('M j, Y', $id),
		            'author'        => get_field('details_author', $id), 
		            'image'         => l4k_normalizeImageUrl(get_field('book_image_url', $id)), 
		            'permalink'     => get_permalink($id) 
		        ];
			}

    	}
	}

	return $bookArr;

}

function l4k_getVideoIndexMap($playlistVideos) {

	$videoIndexMap = array();
    $counter = 0;

	if ($playlistVideos) {
      	foreach ($playlistVideos as $video) {
      		$videoID = $video['id'];
			$videoIndexMap[$videoID] = $counter;
			$counter++;
      	}
	}

	return $videoIndexMap;

}

/**
 * ----------------------------------------------------------------
 * Get mobile app rating record
 * ----------------------------------------------------------------
 */
function l4k_getMobileAppRating($library_name, $device_id, $os_type, $device_type)
{
    global $wpdb;

    $result = $wpdb->get_row(
        $wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}mobile_app_rating WHERE library_name = %s AND device_id = %s",
            $library_name,
            $device_id
        )
    );


    if (is_null($result)) {
        $wpdb->insert(
            $wpdb->prefix . 'mobile_app_rating',
            array(
                'library_name' => $library_name,
                'device_id' => $device_id,
                'os_type' => $os_type,
                'device_type' => $device_type,
                'is_rated' => 0,
                'last_dialog_shown' => current_time('mysql'),
                'show_dialog_next' => current_time('mysql'),
            )
        );


        $result = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}mobile_app_rating WHERE appratingid = %d",
                $wpdb->insert_id
            )
        );
    }


    return array(
        'library_name' => $result->library_name,
        'device_id' => $result->device_id,
        'is_rated' => $result->is_rated,
        'last_dialog_shown' => $result->last_dialog_shown,
        'show_dialog_next' => $result->show_dialog_next,
    );
}

/**
 * ----------------------------------------------------------------
 * Get mobile app notification records
 * ----------------------------------------------------------------
 */

function l4k_getMobileAppNotification($library_name, $device_id, $os_type, $device_type)
{
    global $wpdb;
    // Prepare the SQL query with the correct placeholders
    $result = $wpdb->get_row(
        $wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}mobile_notification WHERE library_name = %s AND device_id = %s",
            $library_name,
            $device_id
        )
    );

    // If query fails or no results are found
    if (is_null($result)) {
        // return new WP_Error('no_notification', 'No notification found for the provided parameters.');
        $wpdb->insert(
            $wpdb->prefix . 'mobile_notification',
            array(
                'library_name' => $library_name,
                'device_id' => $device_id,
                'os_type' => $os_type,
                'device_type' => $device_type,
                'is_latest_notif_shown' => 0, // Default value when creating new entry
            )
        );
        $result = $wpdb->get_row(
    		$wpdb->prepare(
        	"SELECT * FROM {$wpdb->prefix}mobile_notification WHERE id = %d",
        	$wpdb->insert_id
    )
);

    }

	$notification = get_field('mobile_announcement', 'option'); 

    return array(
        'library_name' => $result->library_name,
        'device_id' => $result->device_id,
        'is_latest_notif_shown' => $result->is_latest_notif_shown,
        'notification' =>  $notification ?: null,
    );
}



/**
 * ----------------------------------------------------------------
 * Get comments records
 * ----------------------------------------------------------------
 */

function l4k_getComments($postId) {
    $comments = get_comments([
        'post_id' => $postId,
    ]);

    $output = [];

    foreach ($comments as $comment) {

		// Get from new key first
        $library = get_comment_meta(
            $comment->comment_ID,
            'comment_library',
            true
        );

        // Fallback to old key
        if (empty($library)){
            $library = get_comment_meta(
                $comment->comment_ID,
                'custom_field_6939180959ceb',
                true
            );
        }

        $output[] = [
            'comment_ID'      => (int) $comment->comment_ID,
            'author'          => $comment->comment_author,
            'content'         => $comment->comment_content,
            'date'            => $comment->comment_date,
            'parent'          => (int) $comment->comment_parent,
            'comment_library' => $library ?: '',
        ];
    }
	return $output;
}

/**
 * ----------------------------------------------------------------
 * Get comments records
 * ----------------------------------------------------------------
 */

function l4k_getNextPlaylist($playlistID, $playlistArr) {

	$nextPlaylist = null;

	// find the current playlist index
	if (is_array($playlistArr)) {
		foreach ($playlistArr as $index => $playlist) {
		    if ($playlist['playlist_id'] == $playlistID) {

				// check if there's a next element
		        if (isset($playlistArr[$index + 1])) { $nextPlaylist = $playlistArr[$index + 1]; }
		        break;

		    }
		}
	}

	if ($nextPlaylist !== null) { return $nextPlaylist; }

	return;

}

/**
 * ----------------------------------------------------------------
 * Retrieve current feather count 
 * ----------------------------------------------------------------
 */

function l4k_getFeatherCount($ownerID) {

    global $wpdb;
    $characters_table = $wpdb->prefix . 'characters';
    
    // get the current points value
    $currentPoints = $wpdb->get_var(
        $wpdb->prepare(
            "SELECT points FROM $characters_table WHERE ownerId = %s",
            $ownerID
        )
    );
    
    // check if query failed or no record found
    if ($currentPoints === null) {
        error_log('Failed to retrieve points for ' . $ownerID . ': ' . $wpdb->last_error);
        return false;
    }
    
    return $currentPoints;

}

/**
 * ----------------------------------------------------------------
 * Get transient cache key
 * ----------------------------------------------------------------
 */

function l4k_cacheGet($key) {

    if (isset($_GET['purge-cache'])) {
        return false;
    }

    $cache = wp_cache_get($key, 'l4k');

    if ($cache !== false) {
        return $cache;
    }

    $cache = get_transient($key);

    if ($cache !== false) {
        wp_cache_set($key, $cache, 'l4k');
    }

    return $cache;

}

/**
 * ----------------------------------------------------------------
 * Set transient cache key
 * ----------------------------------------------------------------
 */

function l4k_cacheSet($key, $value, $ttl) {

    wp_cache_set($key, $value, 'l4k');
    set_transient($key, $value, $ttl);

}

/**
 * ----------------------------------------------------------------
 * Check if barcode exists in alternate_barcode table
 * This means that the barcode is a trial
 * ----------------------------------------------------------------
 */

function l4k_checkIfBarcodeIsTrial() {

    global $wpdb;
    $table = $wpdb->prefix . 'alternate_barcode';

	$row = $wpdb->get_row(
	    $wpdb->prepare(
	        "SELECT * FROM {$table} WHERE barcode = %s AND is_teacher = 0 AND time > %s",
	        $_POST['barcode'],
	        '2026-04-15 00:00:00'
	    )
	);

    // if barcode is a trial, perform the below
    if ($row) {

    	$activities = json_decode($row->data, true);
    	$doUpdate = false;

	    // check if flipbook
	    if ($_POST['page_id'] && get_post_type($_POST['page_id']) === 'book') 
	    {
	        if (get_field('book_type', $_POST['page_id']) == 'flipbook') { 
		    	if ($activities['flipbook'] != '1') { 
		    		$activities['flipbook'] = '1'; 
		    		$doUpdate = true; 
		    	} 
	        }
	    }

	    // check if quiz
	    if ($_POST['quiz']) 
	    {
	    	if ($activities['quiz'] != '1') { 
	    		$activities['quiz'] = '1'; 
	    		$doUpdate = true; 
	    	} 
	    }

	    // check if video
	    if ($_POST['page_id'] && get_post_type($_POST['page_id']) === 'book') 
	    {
			if (in_array(get_field('book_type', $_POST['page_id']), ['video_monolingual', 'video_bilingual', 'video_english'])) 
			{
				// check if video will be ticked
		    	if ($activities['video'] != '1') { 
		    		$activities['video'] = '1'; 
		    		$doUpdate = true; 
		    	} 

		    	// check if picture_card will be ticked
		    	if ((get_field('levels_level', $_POST['page_id']) == 'P') && ($activities['picture_card'] != '1')) {
		    		$activities['picture_card'] = '1'; 
		    		$doUpdate = true; 
		    	}

		    	// check if non_fiction will be ticked
	    		if (get_field('filter_tags_non_fiction', $_POST['page_id']) && ($activities['non_fiction'] != '1')) {
		    		$activities['non_fiction'] = '1'; 
		    		$doUpdate = true; 
		    	}
		    	
		    	// check if sign_language will be ticked
		    	// lang_id 118 - ISL  
		    	// lang_id 136 - NZSL
		    	// lang_id 77  - ASL
		    	// lang_id 83  - AUSLAN
		    	// lang_id 85  - BSL
		    	if (in_array(get_field('language', $_POST['page_id']), ['118', '136', '77', '83', '85']) && ($activities['sign_language'] != '1')) {
		    		$activities['sign_language'] = '1'; 
		    		$doUpdate = true; 
		    	}
			}
	    }

	    // check if fun facts
	    if ($_POST['fun_facts']) 
	    {
	    	if ($activities['fun_facts'] != '1') { 
	    		$activities['fun_facts'] = '1'; 
	    		$doUpdate = true; 
	    	} 
	    }

	    // check if avatar page
	    if (get_post_field('post_name', $_POST['page_id']) === 'avatar') {
	    	if ($activities['lekti'] != '1') { 
	    		$activities['lekti'] = '1'; 
	    		$doUpdate = true; 
	    	} 
	    } 

	    // check if activities page
	    if (get_post_field('post_name', $_POST['page_id']) === 'activities') {
	    	if ($activities['activities'] != '1') { 
	    		$activities['activities'] = '1'; 
	    		$doUpdate = true; 
	    	} 
	    } 

	    // check if activity was clicked in a book
	    if ($_POST['activity']) {
	    	if ($activities['activities'] != '1') { 
	    		$activities['activities'] = '1'; 
	    		$doUpdate = true; 
	    	} 
	    } 

	    // check if app-download page
	    if (get_post_field('post_name', $_POST['page_id']) === 'app-download') {
	    	if ($activities['mobile'] != '1') { 
	    		$activities['mobile'] = '1'; 
	    		$doUpdate = true; 
	    	} 
	    } 

	    // check if product-overview page
	    if (get_post_field('post_name', $_POST['page_id']) === 'product-overview') {
	    	if ($activities['overview_video'] != '1') { 
	    		$activities['overview_video'] = '1'; 
	    		$doUpdate = true; 
	    	} 
	    } 

	    if ($doUpdate) {
		    $wpdb->update(
		        $table,
		        ['data' => json_encode($activities)],
		        ['barcode' => $_POST['barcode']]
		    );
	    }

        wp_send_json([
            'status'  		=> 1,
            'message' 		=> 'Barcode is a trial!',
            'data'    		=> $activities
        ]);

    } else {
        wp_send_json(['status' => 0, 'message' => 'Barcode is not a trial.']);
    }
    wp_die();
    
}

/**
 * ----------------------------------------------------------------
 * Generate auto incremented barcode number based on prefix
 * ----------------------------------------------------------------
 */

function l4k_getNextBarcodeNumber($barcodePrefix) {

    global $wpdb;
    $tableName = $wpdb->prefix . 'alternate_barcode';
    
    // check if the barcodePrefix exists and get the highest barcode_number
    $result = $wpdb->get_var($wpdb->prepare(
        "SELECT barcode_number FROM $tableName WHERE barcode_prefix = %s ORDER BY barcode_number DESC LIMIT 1",
        $barcodePrefix
    ));
    
    if ($result !== null) { $nextNumber = intval($result) + 1; } // prefix exists, increment by 1
    else { $nextNumber = 799; } // prefix doesn't exist, start from 799
    
    return str_pad($nextNumber, 4, '0', STR_PAD_LEFT); // pad with zeros to make it 4 digits

}

/**
 * ----------------------------------------------------------------
 * Generate auto incremented barcode number based on teachers
 * ----------------------------------------------------------------
 */

function l4k_getNextTeacherBarcodeNumber() {

    global $wpdb;
    $tableName = $wpdb->prefix . 'alternate_barcode';
    
    // get the highest barcode_number among all teacher rows
    $result = $wpdb->get_var(
        "SELECT barcode_number FROM $tableName WHERE is_teacher = 1 ORDER BY barcode_number DESC LIMIT 1"
    );
    
    if ($result !== null) { $nextNumber = intval($result) + 1; } // teacher rows exist, increment by 1
    else { $nextNumber = 1; } // no teacher rows found, start from 1
    
    return str_pad($nextNumber, 4, '0', STR_PAD_LEFT); // pad with zeros to make it 4 digits

}

/**
 * ----------------------------------------------------------------
 * Get details of the teacher who's currently logged in
 * ----------------------------------------------------------------
 */

function l4k_getTeacherDetails($barcode) {

    global $wpdb;
    $tableName = $wpdb->prefix . 'alternate_barcode';
    
    // get the row matching the given barcode
    $result = $wpdb->get_row(
        $wpdb->prepare(
            "SELECT * FROM $tableName WHERE barcode = %s LIMIT 1",
            $barcode
        ),
        ARRAY_A
    );
    
    if ($result !== null) { 
    	$wpDetails = wp_get_current_user();
    	$result['user_id'] 					= $wpDetails->ID;
    	$result['wp_name'] 					= $wpDetails->display_name;
    	$result['wp_email'] 				= $wpDetails->user_email;
    	$result['newsletter_subscription'] 	= get_field('newsletter_subscription', 'user_' . $wpDetails->ID);
    	$result['school'] 					= get_field('school', 'user_' . $wpDetails->ID);
    	$result['country'] 					= get_field('country', 'user_' . $wpDetails->ID);
    	$result['state'] 					= get_field('state', 'user_' . $wpDetails->ID);
    	$result['phone'] 					= get_field('phone', 'user_' . $wpDetails->ID);
    	$result['currency'] 				= get_field('currency', 'user_' . $wpDetails->ID);
    	$result['newsletter_subscription'] 	= get_field('newsletter_subscription', 'user_' . $wpDetails->ID);
    	$result['newsletter_subscription'] 	= get_field('newsletter_subscription', 'user_' . $wpDetails->ID);
    	$result['expiration_date_nicename'] = date('F j, Y', strtotime($result['expiration_date']));
    	$result['last_login_nicename'] 		= date('F j, Y H:i:s', strtotime(get_field('last_login', 'user_' . $wpDetails->ID)));
    	return $result; 
    } 

    return;

}

/**
 * ----------------------------------------------------------------
 * Call back function to retrieve playlist for a particular language (used in the mobile app)
 * ----------------------------------------------------------------
 */

function l4k_getPlaylist($langID) {

    // Playlist = featured books 
    $books = l4k_getBooks($langID, true, false);

    $playlistArr = array_map(function ($book) {
        return [
            'id'          => $book['book_id'],
            'title'       => html_entity_decode($book['english_title'], ENT_QUOTES, 'UTF-8'),
            'image'       => $book['image_url'],
            'level'       => $book['level_nicename'],
            'book_type'   => $book['book_type'],
            'link'        => $book['book_permalink']
        ];
    }, $books);

    return $playlistArr;
}

/**
 * ----------------------------------------------------------------
 * Call back function to retrieve reading packs for a particular library and language (used in the mobile app)
 * ----------------------------------------------------------------
 */


function l4k_getReadingPacksforMobile($libraryID, $langID) {

    $books = l4k_getReadingPacksBooks($libraryID, $langID);

    $readingPackArr = array_map(function ($id) {

        return [
            'id'        => $id,
            'title'     => html_entity_decode(get_the_title(get_field('native_story', $id)), ENT_QUOTES, 'UTF-8'),
            'image'     => l4k_normalizeImageUrl(get_field('book_image_url', $id)),
            'level'     => l4k_getLevelNicename(get_field('levels_level', $id)),
            'book_type' => get_field('book_type', $id),
            'link'      => get_permalink($id)
        ];

    }, $books);

    return $readingPackArr;
}

/**
 * ----------------------------------------------------------------
 * Get playlists for a language (used in the mobile app)
 * Returns level-grouped playlist entries
 * ----------------------------------------------------------------
 */

function l4k_getPlaylistsForMobile($langID) {

    $playlists = l4k_getPlaylistsByLanguage($langID, '', false);

    if (!$playlists) return [];

    return array_map(function ($playlist) {
        return [
            'playlist_id'   => $playlist['playlist_id'],
            'level'         => $playlist['level'],
            'display_title' => $playlist['display_title'],
            'image'         => $playlist['book_image_url'],
            'book_type'     => $playlist['book_type'],
        ];
    }, $playlists);

}

/**
 * ----------------------------------------------------------------
 * Get books within a specific playlist (used in the mobile app)
 * Mirrors l4k_getVideosByPlaylistID but without session dependency
 * ----------------------------------------------------------------
 */

function l4k_getPlaylistBooksForMobile($playlistID) {

    $level   = get_field('level', $playlistID);
    $langID  = get_field('language', $playlistID);
    $bookArr = [];

    if (!$level || !$langID) return [];

    if ($level === 'P') {
        $metaQuery = [
            'relation' => 'AND',
            ['key' => 'levels_level', 'value' => $level,  'compare' => '='],
            ['key' => 'language',     'value' => $langID, 'compare' => '='],
            ['key' => 'book_type',    'value' => ['video_monolingual', 'video_bilingual'], 'compare' => 'IN'],
        ];
    } else {
        $metaQuery = [
            'relation' => 'AND',
            ['key' => 'levels_level', 'value' => $level,  'compare' => '='],
            ['key' => 'language',     'value' => $langID, 'compare' => '='],
            ['key' => 'book_type',    'value' => ['video_monolingual'], 'compare' => 'IN'],
        ];
    }

    $books = get_posts([
        'post_type'      => 'book',
        'posts_per_page' => get_field('playlist_size', $playlistID) ?: -1,
        'post_status'    => 'publish',
        'orderby'        => 'rand',
        'fields'         => 'ids',
        'meta_query'     => $metaQuery,
    ]);

    foreach ($books as $id) {

        $vimeoURL  = get_field('video_source', $id);
        $vimeoData = l4k_parseVimeoUrl($vimeoURL);

        if ($vimeoData) {
            $bookArr[] = [
                'id'            => $id,
                'vimeo_id'      => $vimeoData[0],
                'vimeo_hash'    => $vimeoData[1] ?? '',
                'title'         => get_the_title($id),
                'native_title'  => get_field('native_title', $id),
                'english_title' => html_entity_decode(get_the_title(get_field('native_story', $id)), ENT_QUOTES, 'UTF-8'),
                'description'   => nl2br(get_field('details_description', $id)),
                'author'        => get_field('details_author', $id),
                'language'      => $langID,
                'book_type'     => get_field('book_type', $id),
                'views'         => get_field('additional_details_views', $id),
                'date'          => get_the_date('M j, Y', $id),
                'image'         => l4k_normalizeImageUrl(get_field('book_image_url', $id)),
                'permalink'     => get_permalink($id),
            ];
        }

    }

    return $bookArr;

}
?>