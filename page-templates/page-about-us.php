<?php /* Template Name: About Us Page Template */ ?>

<?php get_header(); ?>

<div class='main-mid'>
	<div class='_maxwrap'>
	
		<div class="about-wrap">

			<div class="column main-text">
				<?php echo get_field('main_content'); ?>
			</div>

			<div class="column main-image">
				<img src='<?php echo get_field('main_image'); ?>' /> 
			</div>

		</div>

	</div>
</div>

<?php get_footer(); ?>