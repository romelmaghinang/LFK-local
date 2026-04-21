<?php /* Template Name: Terms of Use Page Template */ ?>

<?php get_header(); ?>

<div class='main-mid'>
	<div class='_maxwrap small'>
	
		<div class="privacy-wrap">

			<h1 class='_maintxt'><?php echo get_the_title(); ?></h1>

			<div class="column main-text">
				<div class='download-wrap'>
					<i class="lni lni-download-1"></i>
					<a href='<?php echo get_field('download_pdf_link'); ?>' target='_blank'>Download</a>
				</div>

				<?php echo apply_filters('the_content', get_field('main_content')); ?>
			</div>

			<?php if (get_field('main_image')) : ?>
				<div class="column main-image">
					<img alt='Terms Main Image' src='<?php echo get_field('main_image'); ?>' /> 
				</div>
			<?php endif; ?>

		</div>

	</div>
</div>

<?php get_footer(); ?>