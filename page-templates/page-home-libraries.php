<?php
/* Template Name: Home Page Libraries Template */

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

	<div class='home-row-1'>
	<div class='_maxwrap'>

		<?php $intro_image = hs_get('intro', 'image'); ?>
		<div class='home-hero-wrap'>
			<div class='home-hero'>
				<div>
					<h1><?php echo hs_get('intro', 'heading'); ?></h1>
					<div>
						<?php if ($intro_image): ?>
							<div class='for-mobile'><img class='home-hero-img' src='<?php echo esc_url($intro_image); ?>' alt='LOTE4Kids Intro' /></div>
						<?php endif; ?>
						<?php echo hs_get('intro', 'content'); ?>
					</div>
				</div>

				<?php if ($intro_image): ?>
					<div>
						<img class='home-hero-img' src='<?php echo esc_url($intro_image); ?>' alt='LOTE4Kids Intro' />
					</div>
				<?php endif; ?>
			</div>
		</div>

		<div class='hs-find-school'>
			<h3><?php echo hs_get('find_library', 'heading'); ?></h3>

			<form role="search" action="" class='hs-find-school-form'>
				<div class='hs-find-school-form-wrap'>
					<label for='lib-search' class='_hidden'>Search</label>
					<input  autocomplete='off'
							id='lib-search'
							class='search-txt'
							type='search'
							placeholder=''
							aria-label='Search'
							aria-autocomplete='list'
							aria-controls='suggestions'
							aria-expanded='false'
							aria-haspopup='listbox'
							role='combobox' />
					<ul id="suggestions" translate="no" class="notranslate" role='listbox' aria-label='Suggestions'></ul>
				</div>
				<a class='_btn search-btn' href='' role='button' aria-label='Login'><?php echo hs_get('find_library', 'button_label'); ?></a>
			</form>

			<div class='hs-find-school-helper'>
				<?php echo hs_get('find_library', 'helper_text'); ?>
			</div>
		</div>

	</div>
	</div>

	<?php /* Row 2 — Why Libraries Choose (text + buttons only, no video, no supporting list) */ ?>
	<div class='home-row-2 hs-row'>
	<div class='_maxwrap'>

		<div class='hs-section'>
			<h4><?php echo hs_get('why_choose', 'heading'); ?></h4>
			<div class='hs-section-content'><?php echo hs_get('why_choose', 'content'); ?></div>

			<?php $why_video = hs_get('why_choose', 'video_source'); ?>
			<?php if ($why_video): ?>
				<div class="hs-video">
					<div class="video-overlay">
						<div class='play-btn-wrap'>
							<svg width="30" height="30" viewBox="0 0 24 24">
								<polygon points="0,0 24,12 0,24" fill="#000"/>
							</svg>
						</div>
					</div>
					<iframe
						id="promo-video"
						src="<?php echo esc_url($why_video); ?>"
						allow="autoplay; fullscreen; picture-in-picture"
						allowfullscreen
						title="LOTE4Kids Promotional Video"
						style="width: 100%; height: 100%; border: 0;">
					</iframe>
				</div>
			<?php endif; ?>

			<div class='hs-btn-row'>
				<?php for ($i = 1; $i <= 2; $i++):
					$btn_text = hs_get('why_choose', "button_{$i}_text");
					$btn_link = hs_get('why_choose', "button_{$i}_link");
					if ($btn_text && $btn_link): ?>
						<a href='<?php echo esc_url($btn_link); ?>' target='_blank' class='_btn hs-btn'><?php echo $btn_text; ?></a>
				<?php endif; endfor; ?>
			</div>
		</div>

	</div>
	</div>

	<?php /* Row 3 — Combined: Reach Community + Learning Play + Staff Support + Privacy */ ?>
	<div class='home-row-3 hs-row'>
	<div class='_maxwrap'>

		<?php // Community Reach — stacked layout with slideshow below content ?>
		<div class='hs-section hs-stacked'>
			<h4><?php echo hs_get('community_reach', 'heading'); ?></h4>
			<div class='hs-section-content'><?php echo hs_get('community_reach', 'content'); ?></div>

			<?php
			$community_group = get_field('community_reach');
			$community_images = (is_array($community_group) && !empty($community_group['images'])) ? $community_group['images'] : [];
			?>
			<?php if (!empty($community_images)): ?>
				<div class='home-story-slider'>
					<div class="home-story-slider__track">
						<?php foreach ($community_images as $slide): ?>
							<?php $slide_url = $slide['url'] ?? ''; ?>
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

		<?php // Learning Play ?>
		<?php $learning_image = hs_get('learning_play', 'image'); ?>
		<div class='hs-section'>
			<div class='home-hero'>
				<div>
					<h4><?php echo hs_get('learning_play', 'heading'); ?></h4>
					<div>
						<?php if ($learning_image): ?>
							<div class='for-mobile'><img class='home-hero-img' src='<?php echo esc_url($learning_image); ?>' alt='Learning Feel Like Play' /></div>
						<?php endif; ?>
						<?php echo hs_get('learning_play', 'content'); ?>
					</div>
				</div>

				<?php if ($learning_image): ?>
					<div>
						<img class='home-hero-img' src='<?php echo esc_url($learning_image); ?>' alt='Learning Feel Like Play' />
					</div>
				<?php endif; ?>
			</div>
		</div>

		<?php // Staff Support ?>
		<?php $staff_image = hs_get('staff_support', 'image'); ?>
		<div class='hs-section'>
			<div class='home-hero'>
				<div>
					<h4><?php echo hs_get('staff_support', 'heading'); ?></h4>
					<div>
						<?php if ($staff_image): ?>
							<div class='for-mobile'><img class='home-hero-img' src='<?php echo esc_url($staff_image); ?>' alt='Staff Support' /></div>
						<?php endif; ?>
						<?php echo hs_get('staff_support', 'content'); ?>
					</div>
				</div>

				<?php if ($staff_image): ?>
					<div>
						<img class='home-hero-img' src='<?php echo esc_url($staff_image); ?>' alt='Staff Support' />
					</div>
				<?php endif; ?>
			</div>
		</div>

		<?php // Privacy ?>
		<?php $privacy_image = hs_get('privacy', 'image'); ?>
		<div class='hs-section'>
			<div class='home-hero'>
				<div>
					<h4><?php echo hs_get('privacy', 'heading'); ?></h4>
					<div>
						<?php if ($privacy_image): ?>
							<div class='for-mobile'><img class='home-hero-img' src='<?php echo esc_url($privacy_image); ?>' alt='Privacy and Accessibility' /></div>
						<?php endif; ?>
						<?php echo hs_get('privacy', 'content'); ?>
					</div>
				</div>

				<?php if ($privacy_image): ?>
					<div>
						<img class='home-hero-img' src='<?php echo esc_url($privacy_image); ?>' alt='Privacy and Accessibility' />
					</div>
				<?php endif; ?>
			</div>

			<div class='hs-btn-row'>
				<?php for ($i = 1; $i <= 2; $i++):
					$btn_text = hs_get('privacy', "button_{$i}_text");
					$btn_link = hs_get('privacy', "button_{$i}_link");
					if ($btn_text && $btn_link): ?>
						<a href='<?php echo esc_url($btn_link); ?>' target='_blank' class='_btn hs-btn'><?php echo $btn_text; ?></a>
				<?php endif; endfor; ?>
			</div>
		</div>

	</div>
	</div>

	<?php /* Row 4 — Budget */ ?>
	<div class='home-row-4 hs-row'>
	<div class='_maxwrap'>

		<div class='hs-section'>
			<h4><?php echo hs_get('budget', 'heading'); ?></h4>
			<div class='hs-section-content'><?php echo hs_get('budget', 'content'); ?></div>

			<div class='hs-btn-row'>
				<?php for ($i = 1; $i <= 2; $i++):
					$btn_text = hs_get('budget', "button_{$i}_text");
					$btn_link = hs_get('budget', "button_{$i}_link");
					if ($btn_text && $btn_link): ?>
						<a href='<?php echo esc_url($btn_link); ?>' target='_blank' class='_btn hs-btn'><?php echo $btn_text; ?></a>
				<?php endif; endfor; ?>
			</div>
		</div>

	</div>
	</div>

	<?php /* Row 5 — Quotes */ ?>
	<div class='home-row-5 hs-row'>
	<div class='_maxwrap'>

		<div class='hs-section'>
			<h4><?php echo hs_get('quotes', 'heading'); ?></h4>

			<?php
			$quotes_group = get_field('quotes');
			$quote_items = (is_array($quotes_group) && !empty($quotes_group['items'])) ? $quotes_group['items'] : [];
			?>

			<div class='hs-quotes-list'>
				<?php if (!empty($quote_items)): ?>
					<?php foreach ($quote_items as $q): ?>
						<?php $q_image = $q['image'] ?? ''; $q_source = $q['source'] ?? ''; $q_quote = $q['quote'] ?? ''; ?>
						<div class='home-hero hs-quote-item'>
							<div>
								<div class='hs-quote-text'>
									<?php if ($q_image): ?>
										<div class='for-mobile'><img class='home-hero-img' src='<?php echo esc_url($q_image); ?>' alt='<?php echo esc_attr($q_source); ?>' /></div>
									<?php endif; ?>
									<p>&ldquo;<?php echo $q_quote; ?>&rdquo;</p>
									<span class='hs-quote-source'><?php echo $q_source; ?></span>
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
					<div class='home-hero hs-quote-item'>
						<div>
							<div class='hs-quote-text'>
								<?php if ($default_q_image): ?>
									<div class='for-mobile'><img class='home-hero-img' src='<?php echo esc_url($default_q_image); ?>' alt='Quote' /></div>
								<?php endif; ?>
								<p>&ldquo;<?php echo hs_get('quotes', 'default_quote'); ?>&rdquo;</p>
								<span class='hs-quote-source'><?php echo hs_get('quotes', 'default_source'); ?></span>
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
