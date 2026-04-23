<?php
/* Template Name: Home Page Teachers Template */

get_header();

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

	<?php /* Row 1 — Intro */ ?>
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

	</div>
	</div>

	<?php /* Row 2 — Why Teachers Choose + Supporting */ ?>
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
		</div>

		<div class='hs-section hs-supporting'>
			<h4><?php echo hs_get('supporting', 'heading'); ?></h4>
			<ul class='hs-supporting-list'>
				<?php for ($i = 1; $i <= 3; $i++):
					$label = hs_get('supporting', "item_{$i}_label");
					$content = hs_get('supporting', "item_{$i}_content");
					if ($label || $content): ?>
						<li>
							<?php if ($label): ?><strong><?php echo $label; ?>:</strong><?php endif; ?>
							<span><?php echo $content; ?></span>
						</li>
				<?php endif; endfor; ?>
			</ul>
		</div>

	</div>
	</div>

	<?php /* Row 3 — Lesson Planning + Privacy */ ?>
	<div class='home-row-3 hs-row'>
	<div class='_maxwrap'>

		<div class='hs-section hs-stacked'>
			<h4><?php echo hs_get('lesson_planning', 'heading'); ?></h4>
			<div class='hs-section-content'><?php echo hs_get('lesson_planning', 'content'); ?></div>

			<?php
			$lesson_group = get_field('lesson_planning');
			$lesson_images = (is_array($lesson_group) && !empty($lesson_group['images'])) ? $lesson_group['images'] : [];
			?>
			<?php if (!empty($lesson_images)): ?>
				<div class='home-story-slider'>
					<div class="home-story-slider__track">
						<?php foreach ($lesson_images as $slide): ?>
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

	<?php /* Row 4 — Teachers Subscription + Comparison Table */ ?>
	<div class='home-row-4 hs-row'>
	<div class='_maxwrap'>

		<div class='hs-section'>
			<h4><?php echo hs_get('subscription', 'heading'); ?></h4>
			<div class='hs-section-content'><?php echo hs_get('subscription', 'content'); ?></div>

			<?php
			$has_any_price = false;
			for ($i = 1; $i <= 3; $i++) {
				if (hs_get('subscription', "price_{$i}_amount") || hs_get('subscription', "price_{$i}_flag_image")) {
					$has_any_price = true;
					break;
				}
			}
			?>
			<?php if ($has_any_price): ?>
				<div class='ht-price-row'>
					<?php for ($i = 1; $i <= 3; $i++):
						$p_flag = hs_get('subscription', "price_{$i}_flag_image");
						$p_label = hs_get('subscription', "price_{$i}_label");
						$p_amount = hs_get('subscription', "price_{$i}_amount");
						$p_unit = hs_get('subscription', "price_{$i}_unit");
						$p_link = hs_get('subscription', "price_{$i}_link");
						if (!$p_amount && !$p_flag) continue;
					?>
						<a href='<?php echo esc_url($p_link); ?>' class='_btn ht-price-card' target='_blank'>
							<?php if ($p_flag): ?>
								<div class='ht-price-flag'><img src='<?php echo esc_url($p_flag); ?>' alt='' /></div>
							<?php endif; ?>
							<div class='ht-price-label'><?php echo $p_label; ?></div>
							<div class='ht-price-amount'><?php echo $p_amount; ?></div>
							<?php if ($p_unit): ?><div class='ht-price-unit'><?php echo $p_unit; ?></div><?php endif; ?>
						</a>
					<?php endfor; ?>
				</div>
			<?php endif; ?>

			<?php $helper = hs_get('subscription', 'helper_text'); ?>
			<?php if ($helper): ?>
				<div class='ht-price-helper'><?php echo $helper; ?></div>
			<?php endif; ?>

			<div class='hs-btn-row'>
				<?php for ($i = 1; $i <= 2; $i++):
					$btn_text = hs_get('subscription', "button_{$i}_text");
					$btn_link = hs_get('subscription', "button_{$i}_link");
					if ($btn_text && $btn_link): ?>
						<a href='<?php echo esc_url($btn_link); ?>' target='_blank' class='_btn hs-btn'><?php echo $btn_text; ?></a>
				<?php endif; endfor; ?>
			</div>
		</div>

		<?php
		$compare_group = get_field('comparison');
		$compare_rows = (is_array($compare_group) && !empty($compare_group['rows'])) ? $compare_group['rows'] : [];
		$compare_heading = hs_get('comparison', 'heading');
		$compare_teacher_col = hs_get('comparison', 'teacher_column_label');
		$compare_school_col = hs_get('comparison', 'school_column_label');
		$compare_check_icon = hs_get('comparison', 'check_icon');

		if (empty($compare_rows)) {
			$compare_rows = [
				['feature' => 'Content and Feature Updates', 'teacher_has' => 1, 'school_has' => 1, 'teacher_value' => '', 'school_value' => ''],
				['feature' => 'Website and App Access',      'teacher_has' => 1, 'school_has' => 1, 'teacher_value' => '', 'school_value' => ''],
				['feature' => 'User Base',                    'teacher_has' => 0, 'school_has' => 0, 'teacher_value' => 'Single User', 'school_value' => 'School Wide'],
				['feature' => 'Building IP Whitelisting',    'teacher_has' => 0, 'school_has' => 1, 'teacher_value' => '', 'school_value' => ''],
				['feature' => 'Home Access',                  'teacher_has' => 0, 'school_has' => 1, 'teacher_value' => '', 'school_value' => ''],
				['feature' => 'Staff Portal Access',         'teacher_has' => 0, 'school_has' => 1, 'teacher_value' => '', 'school_value' => ''],
				['feature' => 'Usage Reports',                'teacher_has' => 0, 'school_has' => 1, 'teacher_value' => '', 'school_value' => ''],
				['feature' => 'Promotional Collateral',       'teacher_has' => 0, 'school_has' => 1, 'teacher_value' => '', 'school_value' => ''],
			];
		}
		?>

		<?php if ($compare_heading || !empty($compare_rows)): ?>
			<div class='hs-section ht-compare-section'>
				<table class='ht-compare'>
					<thead>
						<tr>
							<th class='ht-compare-title'><?php echo $compare_heading; ?></th>
							<th><?php echo $compare_teacher_col ?: 'Teacher'; ?></th>
							<th><?php echo $compare_school_col ?: 'School'; ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($compare_rows as $row): ?>
							<?php
								$feature = $row['feature'] ?? '';
								$teacher_value = $row['teacher_value'] ?? '';
								$school_value = $row['school_value'] ?? '';
								$teacher_has = !empty($row['teacher_has']);
								$school_has = !empty($row['school_has']);
							?>
							<tr>
								<td class='ht-compare-feature'><?php echo $feature; ?></td>
								<td class='ht-compare-cell'>
									<?php if ($teacher_value !== ''): ?>
										<span class='ht-compare-text'><?php echo $teacher_value; ?></span>
									<?php elseif ($teacher_has): ?>
										<?php if ($compare_check_icon): ?>
											<img class='ht-compare-check-img' src='<?php echo esc_url($compare_check_icon); ?>' alt='Yes' />
										<?php else: ?>
											<span class='ht-compare-check' aria-label='Yes'>&#10003;</span>
										<?php endif; ?>
									<?php endif; ?>
								</td>
								<td class='ht-compare-cell'>
									<?php if ($school_value !== ''): ?>
										<span class='ht-compare-text'><?php echo $school_value; ?></span>
									<?php elseif ($school_has): ?>
										<?php if ($compare_check_icon): ?>
											<img class='ht-compare-check-img' src='<?php echo esc_url($compare_check_icon); ?>' alt='Yes' />
										<?php else: ?>
											<span class='ht-compare-check' aria-label='Yes'>&#10003;</span>
										<?php endif; ?>
									<?php endif; ?>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		<?php endif; ?>

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

<?php get_footer(); ?>
