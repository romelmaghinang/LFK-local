<?php
/* Template Name: Home Page 2 Template */

get_header();

$libArr = l4k_getLibraries();

if (!function_exists('hs_get')) {
	function hs_get($group_name, $sub_name) {
		static $value_cache = [];
		static $object_cache = [];

		if (!array_key_exists($group_name, $value_cache)) {
			$value_cache[$group_name] = get_field($group_name);
		}
		$g = $value_cache[$group_name];

		if (is_array($g) && isset($g[$sub_name]) && $g[$sub_name] !== '' && $g[$sub_name] !== null && $g[$sub_name] !== false) {
			return $g[$sub_name];
		}

		if (!array_key_exists($group_name, $object_cache)) {
			$object_cache[$group_name] = get_field_object($group_name);
		}
		$obj = $object_cache[$group_name];

		if (!empty($obj['sub_fields'])) {
			foreach ($obj['sub_fields'] as $sf) {
				if (isset($sf['name']) && $sf['name'] === $sub_name) {
					return $sf['default_value'] ?? '';
				}
			}
		}
		return '';
	}
}
?>

<div class='main-mid'>

	<?php /* Row 1 — Hero + Stats + Language Slider (mirrors page-home.php row 1) */ ?>
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
					<h2>Languages Released</h2>
					<span class='count' data-final='<?php echo get_field('main_hero_count_languages_available'); ?>'>0+</span>
				</div>
				<div class='home-stats-number'>
					<h2>Community Reach</h2>
					<span class='count' data-final='<?php echo get_field('main_hero_count_community_reach'); ?>' data-suffix='M'>0M+</span>
				</div>
				<?php $books_per_month = get_field('main_hero_count_books_each_month'); ?>
				<?php if ($books_per_month !== '' && $books_per_month !== null): ?>
					<div class='home-stats-number'>
						<h2>Books Each Month</h2>
						<span class='count' data-final='<?php echo $books_per_month; ?>'>0+</span>
					</div>
				<?php endif; ?>
			</div>

			<div class='home-stats-heading'>
				<h3>Find My Library or School</h3>
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
				Can&rsquo;t find your library or school? Tap the bubble in the bottom right.
			</div>

		</div>

		<?php $lang_heading = get_field('main_hero_lang_heading'); ?>
		<?php if ($lang_heading): ?>
			<div class='home-lang-heading'><h3><?php echo $lang_heading; ?></h3></div>
		<?php endif; ?>

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

	<?php /* Row 2 — Who Can Use LOTE4Kids? */ ?>
	<div class='home-row-2 h2-row'>
	<div class='_maxwrap'>

		<div class='h2-section'>
			<h4><?php echo hs_get('who_can_use', 'heading'); ?></h4>

			<ul class='h2-audience-list'>
				<?php for ($i = 1; $i <= 3; $i++):
					$a_label = hs_get('who_can_use', "audience_{$i}_label");
					$a_content = hs_get('who_can_use', "audience_{$i}_description");
					if (!$a_label && !$a_content) continue;
				?>
					<li>
						<?php if ($a_label): ?><strong><?php echo $a_label; ?>:</strong><?php endif; ?>
						<span><?php echo $a_content; ?></span>
					</li>
				<?php endfor; ?>
			</ul>
		</div>

		<?php
		$has_any_card = false;
		for ($i = 1; $i <= 3; $i++) {
			if (hs_get('who_can_use', "card_{$i}_button_text") || hs_get('who_can_use', "card_{$i}_image")) {
				$has_any_card = true;
				break;
			}
		}
		?>
		<?php if ($has_any_card): ?>
			<div class='h2-card-row'>
				<?php for ($i = 1; $i <= 3; $i++):
					$c_image = hs_get('who_can_use', "card_{$i}_image");
					$c_label = hs_get('who_can_use', "card_{$i}_button_text");
					$c_link = hs_get('who_can_use', "card_{$i}_link");
					if (!$c_label && !$c_image) continue;
				?>
					<div class='h2-card'>
						<?php if ($c_image): ?>
							<div class='h2-card-img'>
								<img src='<?php echo esc_url($c_image); ?>' alt='<?php echo esc_attr($c_label); ?>' />
							</div>
						<?php endif; ?>
						<?php if ($c_link): ?>
							<a href='<?php echo esc_url($c_link); ?>' class='_btn h2-card-btn'><?php echo $c_label; ?></a>
						<?php else: ?>
							<span class='_btn h2-card-btn'><?php echo $c_label; ?></span>
						<?php endif; ?>
					</div>
				<?php endfor; ?>
			</div>
		<?php endif; ?>

	</div>
	</div>

	<?php /* Row 3 — Who Brings Stories to Life + Carousel */ ?>
	<div class='home-row-3 h2-row'>
	<div class='_maxwrap'>

		<div class='h2-section h2-stacked'>
			<h4><?php echo hs_get('stories_to_life', 'heading'); ?></h4>
			<div class='h2-section-content'><?php echo hs_get('stories_to_life', 'content'); ?></div>

			<?php
			$stl_group = get_field('stories_to_life');
			$stl_images = (is_array($stl_group) && !empty($stl_group['images'])) ? $stl_group['images'] : [];
			?>
			<?php if (!empty($stl_images)): ?>
				<div class='home-story-slider'>
					<div class="home-story-slider__track">
						<?php foreach ($stl_images as $slide): ?>
							<?php $slide_url = is_array($slide) ? ($slide['url'] ?? '') : $slide; ?>
							<?php if ($slide_url): ?>
								<div class='home-story-slide'>
									<img src='<?php echo esc_url($slide_url); ?>' alt='' aria-hidden='true' />
								</div>
							<?php endif; ?>
						<?php endforeach; ?>
					</div>
				</div>
			<?php endif; ?>
		</div>

	</div>
	</div>

	<?php /* Row 4 — Quotes */ ?>
	<div class='home-row-4 h2-row'>
	<div class='_maxwrap'>

		<div class='h2-section'>
			<h4><?php echo hs_get('quotes', 'heading'); ?></h4>

			<?php
			$quotes_group = get_field('quotes');
			$quote_items = (is_array($quotes_group) && !empty($quotes_group['items'])) ? $quotes_group['items'] : [];
			?>

			<div class='h2-quotes-list'>
				<?php if (!empty($quote_items)): ?>
					<?php foreach ($quote_items as $q): ?>
						<?php $q_image = $q['image'] ?? ''; $q_source = $q['source'] ?? ''; $q_quote = $q['quote'] ?? ''; ?>
						<div class='home-hero h2-quote-item'>
							<div>
								<div class='h2-quote-text'>
									<?php if ($q_image): ?>
										<div class='for-mobile'><img class='home-hero-img' src='<?php echo esc_url($q_image); ?>' alt='<?php echo esc_attr($q_source); ?>' /></div>
									<?php endif; ?>
									<p>&ldquo;<?php echo $q_quote; ?>&rdquo;</p>
									<span class='h2-quote-source'><?php echo $q_source; ?></span>
								</div>
							</div>
							<?php if ($q_image): ?>
								<div>
									<img class='home-hero-img' src='<?php echo esc_url($q_image); ?>' alt='<?php echo esc_attr($q_source); ?>' />
								</div>
							<?php endif; ?>
						</div>
					<?php endforeach; ?>
				<?php else: ?>
					<?php $default_q_image = hs_get('quotes', 'default_image'); ?>
					<div class='home-hero h2-quote-item'>
						<div>
							<div class='h2-quote-text'>
								<?php if ($default_q_image): ?>
									<div class='for-mobile'><img class='home-hero-img' src='<?php echo esc_url($default_q_image); ?>' alt='Quote' /></div>
								<?php endif; ?>
								<p>&ldquo;<?php echo hs_get('quotes', 'default_quote'); ?>&rdquo;</p>
								<span class='h2-quote-source'><?php echo hs_get('quotes', 'default_source'); ?></span>
							</div>
						</div>
						<?php if ($default_q_image): ?>
							<div>
								<img class='home-hero-img' src='<?php echo esc_url($default_q_image); ?>' alt='Quote' />
							</div>
						<?php endif; ?>
					</div>
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
