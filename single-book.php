<?php 
l4k_checkMemberLoggedIn(); // check if member is logged in

// -------------------------------------------------------
// COMMENTS-ONLY MODE
// If ?comments-only is present in the URL, output just
// the comments section and exit — no header/footer/layout.
// -------------------------------------------------------
if (isset($_GET['comments-only'])) {
	if (comments_open() || get_comments_number()) {
	    add_filter('show_admin_bar', '__return_false'); // remove wp admin bar header
	    remove_action('wp_head', '_admin_bar_bump_cb'); // remove wp admin bar header
	    add_filter('qm/dispatch/html', '__return_false'); // remove query monitor footer
	    wp_head();
	    wp_footer();
		?>
		<style> html, body { background: transparent !important; } .wpd-form-wrap { margin: 0 !important; } </style>
		<input id='comment-library-name' type='hidden' value='<?php echo $_SESSION['library_group']; ?>' />
		<?php comments_template();
	}
	exit;
}
// -------------------------------------------------------

get_header(); // display header
l4k_updateBookNumViews(get_the_ID()); // every time the page loads, increment views count
l4k_updateLastViewedBook(get_the_ID()); // every time the page loads, save this book to a session (for last viewed)

$languageDetails = l4k_getLanguageDetails(get_field('language')); // get language details
$displayRTL = l4k_determineRTL(get_the_ID());
$isReadingPack = l4k_isReadingPackContext(); //check if in reading pack context
$bookTitle = get_the_title();

if ($isReadingPack) { $bookTitle = $bookTitle . ' - Reading Pack'; }

// on load, log 1062 event
l4k_addWebActivity(1062, array(
	"Story ID" 		=> get_the_ID(), 
	"Story Title" 	=> $bookTitle, 
	"Language" 		=> get_field('language', get_the_ID()), 
	"Type" 			=> get_field('book_type', get_the_ID()) ));
?>

<div class='main-mid book'>
<div class='_maxwrap medium'>

	<div class='book-wrap'>

		<div class="book-parent__wrapper">

			<?php if (in_array(get_field('book_type'), ['video_english', 'video_bilingual', 'video_monolingual'])) : ?>

				<?php $videoURL = l4k_parseVimeoUrl(get_field('video_source')); ?>

				<div class="video-wrapper">
					<iframe 
						title="Video player"
						src="https://player.vimeo.com/video/<?php echo $videoURL[0]; ?>?title=0&amp;byline=0&amp;portrait=0&amp;h=<?php echo $videoURL[1]; ?>&amp;app_id=122963" 
						frameborder="0" 
						allow="autoplay; fullscreen; picture-in-picture" 
						allowfullscreen>
					</iframe>
				</div>

			<?php else : ?>

				<div class="flipbook-parent" style='background-image: url("<?php echo get_field('flipbook_background_image', 'option'); ?>")'>

					<div class="flipbook-wrapper">

						<div class="flipbook-wrapper__loading"><div class="_loader"></div></div>

						<div 	id='flipbook-details'
								data-pdf='<?php echo get_field('pdf_file'); ?>'
								data-rtl='<?php echo get_field('pdf_rtl_file'); ?>'
								data-layout='<?php echo get_field('layout'); ?>'
								data-doublepage='<?php echo get_field('is_double_page'); ?>'></div>

						<div id="flipbook"></div>

					</div>

					<div class='flipbook-controls'>
						<button id="flipbook-btn-refresh" data-tooltip='Reset'>
							<i class="lni lni-refresh-circle-1-clockwise"></i>
						</button>

						<?php if (have_rows('audio')) : ?>

							<button id="flipbook-btn-audio" data-tooltip='Play Audio'>
								<img src='<?php echo $languageDetails['flag_url']; ?>' />
								<i class="lni lni-play"></i>
							</button>

							<div class='flipbook-audio'>	
							    <?php while (have_rows('audio')) : the_row(); ?>

							    	<!-- play 1st audio in the 1st page of the book -->
    								<?php if (get_row_index() === 1): ?>
    									<?php if ((get_field('is_double_page') == true) && (get_sub_field('page_number') != 2)) : ?>
		    							 	<audio 	controls=""
										        	controlslist="nodownload" 
										        	data-page-number="<?php echo get_field('is_double_page') ? '2' : '1'; ?>"
										        	src="<?php echo l4k_normalizeImageUrl(get_sub_field('audio_file')); ?>">
									        </audio>
										<?php elseif (get_field('is_double_page') == false) : ?>
		    							 	<audio 	controls=""
										        	controlslist="nodownload" 
										        	data-page-number="1"
										        	src="<?php echo l4k_normalizeImageUrl(get_sub_field('audio_file')); ?>">
									        </audio>
										<?php endif; ?>
									<?php endif; ?>

							        <audio 	controls=""
							        	 	controlslist="nodownload" 
							        	 	data-page-number="<?php echo get_sub_field('page_number'); ?>"
							        	 	src="<?php echo l4k_normalizeImageUrl(get_sub_field('audio_file')); ?>">
							        </audio>

							    <?php endwhile; ?>
							</div>

						<?php endif; ?>

						<?php if (have_rows('audio_billingual')) : ?>

							<button id="flipbook-btn-audio-bilingual" data-tooltip='Play Audio Bilingual'>
								<img src='<?php echo get_stylesheet_directory_uri(); ?>/assets/img/english-us.svg' />
								<i class="lni lni-play"></i>
							</button>

							<div class='flipbook-audio-bilingual'>	
							    <?php while (have_rows('audio_billingual')) : the_row(); ?>

									<!-- play 1st audio in the 1st page of the book -->
    								<?php if (get_row_index() === 1): ?>
    									<?php if ((get_field('is_double_page') == true) && (get_sub_field('page_number') != 2)) : ?>
		    							 	<audio 	controls=""
										        	controlslist="nodownload" 
										        	data-page-number="<?php echo get_field('is_double_page') ? '2' : '1'; ?>"
										        	src="<?php echo l4k_normalizeImageUrl(get_sub_field('audio_file')); ?>">
									        </audio>
										<?php elseif (get_field('is_double_page') == false) : ?>
		    							 	<audio 	controls=""
										        	controlslist="nodownload" 
										        	data-page-number="1"
										        	src="<?php echo l4k_normalizeImageUrl(get_sub_field('audio_file')); ?>">
									        </audio>
										<?php endif; ?>
									<?php endif; ?>

							        <audio 	controls=""
							        	 	controlslist="nodownload" 
							        	 	data-page-number="<?php echo get_sub_field('page_number'); ?>"
							        	 	src="<?php echo l4k_normalizeImageUrl(get_sub_field('audio_file')); ?>">
							        </audio>

							    <?php endwhile; ?>
							</div>

						<?php endif; ?>

						<button id="flipbook-btn-prev" class='disabled' data-tooltip='Go To Previous Page'><i class="lni lni-arrow-left"></i></button>
						<button id="flipbook-btn-next" class='active' data-tooltip='Go To Next Page'><i class="lni lni-arrow-right"></i></button>

						<?php if (get_field('pdf_rtl_file')) : ?>

							<button id="flipbook-btn-rtl" data-tooltip='Right to Left'><i class="lni lni-direction-rtl"></i></button>
							<button id="flipbook-btn-ltr" class='disabled' data-tooltip='Left to Right'><i class="lni lni-direction-ltr"></i></button>

						<?php endif; ?>

						<button id="flipbook-btn-fullscreen" data-tooltip='Maximize'><i class="lni lni-expand-arrow-1"></i></button>
					</div>

				</div>

			<?php endif; ?>

			<div class='book-info__wrapper'>
				<div class='actions-wrapper'>
					<?php if (in_array(get_field('book_type'), ['video_english', 'video_bilingual', 'video_monolingual'])) : ?>
						<div class='action-item playback-reminder'>You can change the playback speed using the settings menu in the bottom right corner of the player.</div>
					<?php endif; ?>
					<a href="" class='_btn copy-link' data-toast-id="copy-link" data-url="<?php echo l4k_generateQuickCopyLink(get_the_ID()); ?>">
						<i class="lni lni-share-1"></i> Copy <?php echo (get_field('book_type') == 'flipbook') ? 'Book' : 'Video'; ?> Link
					</a>
				</div>
				<div class='book-info'>
					<h1>
						<?php echo (get_field('native_title')) ? get_field('native_title') : get_the_title(get_field('native_story')); ?>
					</h1>

					<div class='meta'>
						<span class='post-title'><i class="lni lni-book-1"></i><?php echo str_replace('(Level 0) ', '', get_the_title()); ?></span>
						<span class='views'><i class="lni lni-eye"></i> <?php echo get_field('additional_details_views'); ?> views</span>
						<span class='date'><i class="lni lni-calendar-days"></i><?php echo get_the_date('M j, Y'); ?></span>
						<?php /* if (get_field('details_author')) : ?><span class='author'><i class="lni lni-pencil-1"></i><?php echo get_field('details_author'); ?></span><?php endif; */ ?>
					</div>

					<div class='description'>
						<?php echo (get_field('details_author')) ? '<i>'.get_field('details_author').'</i><br/>' : '' ; ?>
						<?php echo nl2br(get_field('details_description')); ?>
						<div class='universal-meta'>
							<?php echo get_field('universal_meta_data', get_field('native_story')); ?>
						</div>
					</div>
					
					<div class='comments'>
						<input id='comment-library-name' type='hidden' value='<?php echo $_SESSION['library_group']; ?>' />
						<?php if (comments_open() || get_comments_number()) { comments_template(); } ?>
					</div>
				</div>
			</div>

		</div>

		<?php 
		get_template_part(	'template-parts/section', 
							'sidebar-book', 
							[	'book_id' 		=> get_the_ID(), 
								'lang_id' 		=> get_field('language'), 
								'level' 		=> get_field('levels_level'), 
								'book_type' 	=> get_field('book_type'), 
								'story_id' 		=> get_field('native_story'),]); 
		?>

	</div>

</div>
</div>

<?php get_footer(); ?>