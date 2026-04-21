<?php 
/* Template Name: Home Page Template */ 

l4k_checkMemberLoggedIn(); // check if member is logged in
get_header(); // display header

$libArr = l4k_getLibraries();
?>

<div class='main-mid'>

	<div class='home-row-1'>
	<div class='_maxwrap'>

		<div class="home-hero-wrap">
			<div class="home-hero _resizable">
				<div>
					<h1><?php echo get_field('main_hero_heading'); ?></h1>
					<div>
						<div class='for-mobile'><img class='home-hero-img' src='<?php echo l4k_normalizeImageUrl(get_field('main_hero_image')); ?>' alt='Home Hero' /></div>
						<?php echo get_field('main_hero_content'); ?>
					</div>
				</div>

				<div>
					<img class='home-hero-img' src='<?php echo l4k_normalizeImageUrl(get_field('main_hero_image')); ?>' alt='Home Hero' />
				</div>
			</div>
		</div>

		<div class="home-stats-wrap">

			<div class="home-stats-numbers">
				<div class='home-stats-number'>
					<h2>Books Published</h2>
					<span class='count' data-final='<?php echo get_field('main_hero_count_books_published'); ?>'>0+</span>
				</div>
				<div class='home-stats-number'>
					<h2>Languages Available</h2>
					<span class='count' data-final='<?php echo get_field('main_hero_count_languages_available'); ?>'>0+</span>
				</div>
				<div class='home-stats-number'>
					<h2>Community Reach</h2>
					<span class='count' data-final='<?php echo get_field('main_hero_count_community_reach'); ?>' data-suffix='M'>0M+</span>
				</div>
			</div>

			<div class='home-stats-heading'>
				<h3>Find Your Library or School</h3>
			</div>

		    <form role="search" action="" class='home-stats-search'>
		        <div class='home-stats-search-wrap'>
		            <label for='lib-search' class='_hidden'>Search</label>
		            <input  autocomplete='off'
		                    id='lib-search'
		                    class='search-txt'
		                    type='search'
		                    placeholder='Start typing here'
		                    aria-label='Search'
		                    aria-autocomplete='list'
		                    aria-controls='suggestions'
		                    aria-expanded='false'
		                    aria-haspopup='listbox'
		                    role='combobox' />
		            <ul id="suggestions" translate="no" class="notranslate" role='listbox' aria-label='Suggestions'></ul>
		        </div>
		        <a class='search-btn _btn' href='' role='button' aria-label='Go to search results'>Go</a>
		    </form>

			<div class='home-stats-content'>
				Can’t find your library or school? Tap the bubble in the bottom right.
			</div>

		</div>

		<div class='home-lang-slider'>
			<div class="home-lang-slider__track"> 
			
				<?php $langArr = l4k_getLanguages(); ?>
				<?php if ($langArr): ?>
					<?php foreach ($langArr as $lang => $l): ?>

						<a 	class='home-lang-slide' 
							aria-label="Switch to <?php echo $l['title']; ?>"
							href='<?php echo home_url(); ?>/find-your-library?<?php echo $l['title']; ?>'>
							<img 	src='<?php echo $l['flag_url']; ?>' 
									aria-hidden="true"
									role="presentation"
									alt='Books in <?php echo $l['title']; ?>' />
							<span><?php echo $l['title']; ?></span>
						</a>

					<?php endforeach; ?>
				<?php endif; ?>

			</div>
		</div>

		<div class='home-lang-btn-wrap'>
			<a href='<?php echo get_field('main_hero_cta_link'); ?>' class='_btn explore-lang'><?php echo get_field('main_hero_cta_label'); ?></a>
		</div>

	</div>
	</div>

	<div class='home-row-2'>
	<div class='_maxwrap'>

		<div class="home-hero-wrap">
			<div class="home-hero">
				<div>
					<h4><?php echo get_field('slider_section_heading'); ?></h4>
					<div><?php echo get_field('slider_section_content'); ?></div>
				</div>

				<div>
					<img class='home-hero-img' src='<?php echo l4k_normalizeImageUrl(get_field('slider_section_image')); ?>' alt='Home Hero' />
				</div>
			</div>
		</div>

		<div class='home-story-slider'>
			<div class="home-story-slider__track"> 

				<?php if(have_rows('slider_section_story_slider')): ?>
				    <?php while(have_rows('slider_section_story_slider')): the_row(); ?>

						<div class='home-story-slide'>
						    <img src='<?php the_sub_field('image'); ?>' alt='' aria-hidden='true' />
						</div>

				    <?php endwhile; ?>
				<?php endif; ?>

			</div>
		</div>

	</div>
	</div>

	<div class='home-row-3'>
	<div class='_maxwrap'>

		<div class='home-video-wrap'>
			<h4><?php echo get_field('video_section_heading'); ?></h4>
			<div><?php echo get_field('video_section_content'); ?></div>
			<div class="video">
			    <div class="video-overlay">
			        <div class='play-btn-wrap'>
						<svg width="30" height="30" viewBox="0 0 24 24">
			                <polygon points="0,0 24,12 0,24" fill="#000"/>
			            </svg>
			        </div>
			    </div>
			    <iframe
			        id="promo-video"
			        src="<?php echo get_field('video_section_source'); ?>"
			        allow="autoplay; fullscreen; picture-in-picture"
			        allowfullscreen
			        title="LOTE4Kids Promotional Video"
			        style="width: 100%; height: 100%; border: 0;">
			    </iframe>
			</div>
		</div>

		<div class='home-video-btns'>
			<?php if(have_rows('video_section_buttons')): ?>
			    <?php while(have_rows('video_section_buttons')): the_row(); ?>
			    	<a href='<?php echo get_sub_field('link'); ?>' target='_blank' class='_btn explore-lang'><?php echo get_sub_field('text'); ?></a>
			    <?php endwhile; ?>
			<?php endif; ?>
		</div>

	</div>
	</div>

	<div class='home-row-4'>
	<div class='_maxwrap'>

		<div class='home-reviews-wrap'>
			<h4>Reviews</h4>
		</div>

		<div class='home-reviews-slider'>
			<div class="home-reviews-slider__track">

				<?php if(have_rows('reviews_section_slider')): ?>
				    <?php while(have_rows('reviews_section_slider')): the_row(); ?>

						<div class='review-item'>
							<div class='review-item-wrap'>
								<p>
									<?php the_sub_field('review'); ?>
									<span>- <?php the_sub_field('source'); ?></span>
								</p>
								<img 	src='<?php the_sub_field('image'); ?>' 
										alt='Review by <?php the_sub_field('source'); ?>' />
							</div>						
						</div>

				    <?php endwhile; ?>
				<?php endif; ?>

			</div>
		</div>

	</div>
	</div>

</div>

<ul id='lib-list' class='library-list notranslate' translate="no">
	<?php if ($libArr): ?>
		<?php foreach ($libArr as $lib => $l): ?>
			<li data-url='<?php echo ($l['predefined_url']) ? $l['predefined_url'] : $l['lib_permalink']; ?>'><?php echo $l['title']; ?></li>
		<?php endforeach; ?>
	<?php endif; ?>
</ul>

<?php get_footer(); ?>