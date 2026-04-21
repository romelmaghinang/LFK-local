<div class='sidebar main'>

	<!-- sidebar library welcome logo -->
	<?php if (isset($_SESSION['library_welcome_logo']) && $_SESSION['library_welcome_logo']) : ?>
		<div class='side-image'>
			<img alt='Library Logo' src='<?php echo l4k_normalizeImageUrl($_SESSION['library_welcome_logo']); ?>' />
		</div>
	<?php endif; ?>

	<!-- lekti -->
	<div class="side-lekti-wrap">
		<p style="text-align:center;">
			<a 	href="<?php echo home_url(); ?>/avatar" 
				aria-label="Lekti Customization Link"
				style="display:inline-flex; flex-direction:column; align-items:center;">
				<img
					loading="lazy"
					class="aligncenter avatar-img"
					src="https://firebasestorage.googleapis.com/v0/b/lote4kids-gamification.firebasestorage.app/o/default_body.png?alt=media&token=57faff74-435c-43ba-8871-2ae533debcc8"
					alt="Avatar"
					role='Presentation'
					width="170"
					height="161">
				<span style="margin-top:25px; font-size:16px; font-weight:600; color:#44443D;">Click Me!</span>
			</a>
		</p>
	</div>
	<div class='side-divider'></div>

	<!-- sidebar image -->
	<?php if (get_field('sidebar_settings_image', 'option')) : ?>
		<div class='side-image'>
			<img 	alt='Side Image' 
					src='<?php echo get_field('sidebar_settings_image', 'option'); ?>' />
		</div>
	<?php endif; ?>

	<!-- sidebar paragraph 1 -->
	<?php if (get_field('sidebar_settings_content_1', 'option')) : ?>
		<div class='side-content'>
			<?php echo apply_filters('the_content', get_field('sidebar_settings_content_1', 'option')); ?>
		</div>
	<?php endif; ?>

	<!-- sidebar button 1 -->
	<?php if (get_field('sidebar_settings_content_1_button_text', 'option')) : ?>
		<a 	class='_btn'
			href='<?php echo get_field('sidebar_settings_content_1_button_link', 'option') ?>'>
			<?php echo get_field('sidebar_settings_content_1_button_text', 'option') ?>
		</a>
	<?php endif; ?>

	<!-- sidebar paragraph 2 -->
	<?php if (get_field('sidebar_settings_content_2', 'option')) : ?>
		<div class='side-divider'></div>
		<div class='side-content'>
			<?php echo apply_filters('the_content', get_field('sidebar_settings_content_2', 'option')); ?>
		</div>
	<?php endif; ?>

	<!-- sidebar languages -->
	<?php if (get_field('sidebar_settings_content_2_languages', 'option')) : ?>
		<div class='side-content'>
			<?php $sideLanguages = get_field('sidebar_settings_content_2_languages', 'option'); ?>
			<?php if ($sideLanguages) : ?>
				<div class='side-langs'>
					<?php foreach ($sideLanguages as $langID) : ?>
						<?php $l = l4k_getLanguageDetails($langID);  ?>
						<a 	class='lang-item' 
							href='<?php echo $l['lang_permalink']; ?>'
							aria-label='<?php echo $l['title']; ?> Link'
							data-published='<?php echo $l['date_published']; ?>'
							data-english-name='<?php echo $l['title']; ?>'>

							<img src='<?php echo $l['flag_url']; ?>' alt='<?php echo $l['title']; ?> Flag' />
							<!-- <h4><?php echo $l['native_label']; ?></h4> -->
							<h5><?php echo $l['title']; ?></h5>

						</a>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
	<?php endif; ?>

	<!-- sidebar button 2 -->
	<?php if (get_field('sidebar_settings_content_2_button_text', 'option')) : ?>
		<div class='side-divider'></div>
		<a class='_btn' href='<?php echo get_field('sidebar_settings_content_2_button_link', 'option') ?>'>
			<?php echo get_field('sidebar_settings_content_2_button_text', 'option') ?>
		</a>
		<div class='side-divider'></div>
	<?php endif; ?>

	<div class='side-content'>
		<h4>Feedback</h4>
		<?php echo do_shortcode('[wpforms id="'.get_field('sidebar_settings_form', 'option').'" title="false"]'); ?>
	</div>

</div>