<?php /* Template Name: Publishers Page Template */ ?>

<?php get_header(); ?>

<div class='main-mid'>
	<div class='_maxwrap'>

		<div class="publishers-wrap">

			<div class="column main-text">
				<?php echo get_field('main_content'); ?>
			</div>

			<div class="column main-image">
				<img alt='Publishers Hero Image' src='<?php echo get_field('main_image'); ?>' /> 
			</div>

		</div>

	</div>
</div>

<?php get_footer(); ?>