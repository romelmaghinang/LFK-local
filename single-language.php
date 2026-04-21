<?php 
l4k_checkMemberLoggedIn(); // check if member is logged in

if (get_query_var('reading-pack', false) !== false) { $bookArr = l4k_getBooks(get_the_ID(), true, true, $_SESSION['library_id']); }
else { $bookArr = l4k_getBooks(get_the_ID()); }

$playlistArr = l4k_getPlaylistsByLanguage(get_the_ID());
$isReadingPack = l4k_isReadingPackContext();

get_header();
global $post;
?>

<div class='main-mid language'>
<div class='_maxwrap'>

	<div class='lang-wrap'>

		<div class='mainbar'>

			<div class='heading'>

				<div class='fun-facts'>
					<div class='fun-facts__inner'>
						<?php if (get_field('fun_facts_enabled')) : ?>
							<?php if (get_field('fun_facts_position') == 'top-left') : ?>
								<a 	href='javascript: void(0);' 
									id='fun-facts-btn'
									data-language='<?php echo get_the_ID(); ?>'
									data-html-src='<?php echo l4k_normalizeImageUrl(get_field('fun_facts_media_html')); ?>'>
									<div class='dialog'>
										<?php echo get_field('fun_facts_cta_text'); ?>
										<?php if (get_field('fun_facts_cta_text_sub')) : ?>
											<span><?php echo get_field('fun_facts_cta_text_sub'); ?></span>
										<?php endif; ?>
									</div>
									<img src='<?php echo get_field('fun_facts_media_lekti'); ?>' />
								</a>
							<?php endif; ?>
						<?php else : ?>
							<a 	href='javascript: void(0);' class='disabled' aria-disabled="true">
								<div class='dialog'>Language Fun Facts<span>Coming Soon!</span></div>
								<img src='<?php echo get_stylesheet_directory_uri(); ?>/assets/img/fun-facts-coming-soon.png' />
							</a>
						<?php endif; ?>
					</div>

					<div class='embed__overlay'>
						<div class='embed__wrap'>
							<div class='embed__wrap__inner'>
								<div class='embed__decoration'><img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/blowing-leaf1.png" alt="leaf"></div>
								<div class='embed__decoration-2'><img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/blowing-leaf3.png" alt="leaf"></div>
								<a class='embed__close'><i class="lni lni-xmark"></i></a>
								<div class='embed__title'><?php echo get_field('fun_facts_cta_text'); ?></div>
								<div class='embed__content'>
									<iframe id='fun-facts-iframe'></iframe>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class='title__wrap'>
					<h1>
						<img alt='<?php echo get_the_title(); ?> Flag' src='<?php echo get_field('lang_flag_image'); ?>' />
						<div class='title'>
							<?php echo get_field('lang_description'); ?>
							<span>
								<?php echo get_the_title(); ?>
								<?php 
								$showFunFactsBelowSearch = false;
								if ((get_field('fun_facts_enabled') && get_field('fun_facts_position') == 'below-search')) {
									$showFunFactsBelowSearch = true;
								}
								?>

								<?php if (get_field('fun_facts_enabled')) : ?>
									<div class='fun-facts-below-search <?php echo ($showFunFactsBelowSearch) ? 'active' : ''; ?>'>
										&nbsp;|&nbsp; 
										<a 	href='javascript: void(0);' 
											id='fun-facts-btn-search'
											data-language='<?php echo get_the_ID(); ?>'
											data-html-src='<?php echo l4k_normalizeImageUrl(get_field('fun_facts_media_html')); ?>'>
											<?php echo get_field('fun_facts_cta_text'); ?>
										</a>
									</div>
								<?php endif; ?>		
							</span>
						</div>
					</h1>

					<div class='filter'>

						<?php if (get_field('filter_section')) : ?>
							<div class='filter__item type'>
								<div class='filter__item-inner desktop'>
									<strong>Filter</strong>
									<a href="javascript: void(0);" class='filter-all active'>All</a>
									<a href="javascript: void(0);" class='filter-A' data-tooltip="Fingerspelling">ABC</a>
									<a href="javascript: void(0);" class='filter-P' data-tooltip="Words by Topic">Picture Cards</a>
									<a href="javascript: void(0);" class='filter-1' data-tooltip="1-45 words">Level 1</a>
									<a href="javascript: void(0);" class='filter-2' data-tooltip="45-90 words">Level 2</a>
									<a href="javascript: void(0);" class='filter-3' data-tooltip="90-380 words">Level 3</a>
									<a href="javascript: void(0);" class='filter-4+' data-tooltip="380+ words">Level 4+</a>
									<a href="javascript: void(0);" class='filter-nf'>Non-fiction</a>
									<?php /*<a href="javascript: void(0);" class='filter-q'>Quiz</a>*/ ?>
								</div>
								<div class='filter__item-inner mobile'>
									<select>
										<option class='filter-all'>Filter</a>
										<option class='filter-all'>All</a>
										<option class='filter-A' data-tooltip="Fingerspelling">ABC (Fingerspelling)</a>
										<option class='filter-P' data-tooltip="Words by Topic">Picture Cards (Words by Topic)</a>
										<option class='filter-1' data-tooltip="1-45 words">Level 1 (1-45 words)</a>
										<option class='filter-2' data-tooltip="45-90 words">Level 2 (45-90 words)</a>
										<option class='filter-3' data-tooltip="90-380 words">Level 3 (90-380 words)</a>
										<option class='filter-4+' data-tooltip="380+ words">Level 4+ (380+ words)</a>
										<option class='filter-nf'>Non-fiction</a>
										<?php /*<option class='filter-q'>Quiz</a>*/ ?>
									</select>
								</div>
							</div>	
						<?php endif; ?>

						<div class='filter__item sort'>
							<div class='filter__item-inner desktop'>
								<strong>Sort</strong>
								<a href="#" class='sort-latest'>Latest Release</a>
								<a href="#" class='sort-views'>Most Popular</a>
								<a href="#" class='sort-az'>Sort A-Z</a>
								<a href="#" class='sort-za'>Sort Z-A</a> 
							</div>
							<div class='filter__item-inner mobile'>
								<select>
									<option class=''>Sort</option>
									<option class='sort-latest'>Latest Release</option>
									<option class='sort-views'>Most Popular</v>
									<option class='sort-az'>Sort A-Z</option>
									<option class='sort-za'>Sort Z-A</option> 
								</select>
							</div>							
						</div>

						<div class='filter__item search'>
							<div class='filter__item-inner'>
								<input type='text' placeholder="Search" />
							</div>
						</div>
					
					</div>

					<?php if (l4k_isLanguagePartOfReadingPacks(get_the_ID()) && $isReadingPack) : ?>
						<div class='heading-folder'>
							<a href="<?php echo get_permalink(); ?>" class='<?php echo (!$isReadingPack) ? 'active' : ''; ?>'>All Books</a> &nbsp;|&nbsp; 
							<a href="<?php echo get_permalink(); ?>reading-pack" class='<?php echo ($isReadingPack) ? 'active' : ''; ?>'>Reading Pack</a>
						</div>
					<?php endif; ?>

				</div>
				<div><!-- extra element for grid --></div>
			</div>

			<?php if ($playlistArr && !$isReadingPack): ?>
				<div class='playlist-wrap__outer'>
				<div class='playlist-wrap col-<?php echo count($playlistArr ?? []); ?>'>
					<?php foreach ($playlistArr as $playlist => $p): ?>

						<div class='playlist-item'>
							<div class='title'>
								<h4><?php echo $p['display_title'];?></h4>
							</div>

							<div class='book'>
								<a href='<?php echo $p['playlist_permalink']; ?>' aria-label='<?php echo $p['display_title'];?>'>
									<img alt='<?php echo $p['display_title'];?>' src='<?php echo $p['book_image_url']; ?>' />
								</a>
							</div>

							<div class='featured-buttons'>
								<a href='<?php echo $p['playlist_permalink']; ?>' class='_btn'>
									<?php echo $p['button_label']; ?>
									<img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/icon-play.png?t=3" alt="">
								</a>
							</div>
						</div>

					<?php endforeach; ?>
				</div>
				</div>
			<?php endif; ?>

			<div class='books-wrap <?php echo (get_query_var('reading-pack', false) !== false) ? 'reading-pack' : ''; ?> col-<?php echo get_field('layout'); ?> <?php echo get_post_field('post_name', get_the_ID()); ?>'>

				<?php if ($bookArr): ?>
					<?php foreach ($bookArr as $book => $b): ?>

						<div	class='book-item' 
								data-level='<?php echo $b['level']; ?>'
								data-published='<?php echo $b['date_published']; ?>'
								data-views='<?php echo $b['views']; ?>'
								data-views-3mos='<?php echo $b['views_3mos']; ?>'
								data-tier='<?php echo $b['tier']; ?>'
								data-quiz='<?php echo $b['has_quiz']; ?>'
								data-non-fiction='<?php echo $b['is_non_fiction']; ?>'
								data-english-title='<?php echo $b['english_title']; ?>'
								data-native-title='<?php echo $b['native_title']; ?>'>

							<div class='title'> 
								<?php /*<h4><?php echo $b['native_title']; ?> (<?php echo $b['tier']; ?>)</h4>*/ ?>
								<?php /*<h5><?php echo $b['views']; ?></h5>*/ ?>
								<h4><?php echo $b['native_title']; ?></h4>
								<?php if (!in_array(get_the_ID(), array('127596', '127598', '127600', '85', '77', '83', '136', '118'))): ?>
									<h5><span><?php echo $b['english_title']; ?></span></h5>
								<?php endif; ?>
							</div>

							<div class='book'>
								<a href='<?php echo ($isReadingPack) ? $b['book_permalink'].'reading-pack' : $b['book_permalink']; ?>'>
									<img alt='<?php echo $b['english_title']; ?>' src='<?php echo $b['image_url']; ?>' loading='lazy' />
									<div class='book-level'><?php echo $b['level_nicename']; ?></div>
								</a>
							</div>

							<div class='featured-buttons'>
								<?php if ($b['book_buttons']) : ?>
									<?php foreach ($b['book_buttons'] as $button => $btn) : ?>
										<a href='<?php echo ($isReadingPack) ? $btn['book_permalink'].'reading-pack' : $btn['book_permalink']; ?>' class='_btn'>
											<?php echo $btn['button_label']; ?>
											<?php if ($btn['book_type'] == 'flipbook') : ?>
												<img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/icon-book.png?t=3" alt="">
											<?php else : ?>
												<img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/icon-play.png?t=3" alt="">
											<?php endif; ?>
										</a>
									<?php endforeach; ?>
								<?php endif; ?>
							</div>

						</div>

					<?php endforeach; ?>
				<?php endif; ?>

			</div>

			<div id="no-results">No results found</div>

		</div>

		<?php get_template_part('template-parts/section', 'sidebar-main'); ?>

	</div>
	
</div>
</div>

<?php get_footer(); ?>