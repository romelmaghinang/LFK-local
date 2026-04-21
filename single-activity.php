<?php 
l4k_checkMemberLoggedIn(); // check if member is logged in
get_header(); 
global $post;
?>

<div 	class='main-mid' 
		data-activity-title='<?php echo get_the_title(); ?>'
		data-activity-type='<?php echo get_field('type'); ?>'
		data-tracking-status='<?php echo get_field('tracking_status'); ?>' 
		data-tracking-duration='<?php echo get_field('tracking_duration'); ?>' 
		data-tracking-max-duration='<?php echo get_field('tracking_maximum_duration'); ?>'
		data-tracking-refresh-period='<?php echo get_field('tracking_refresh_period'); ?>'>

	<div class='_maxwrap'>

		<?php if (get_field('type') == 'collection') : ?>

			<div class='single-activity-collection-wrap'>

				<div class='mainbar'>
					<h1>
						<img alt='Activities Hero Image' src='<?php echo l4k_normalizeImageUrl(get_field('activity_image')); ?>' />
						<?php echo get_the_title(); ?>
					</h1>

					<div class='activities'>
						<?php if (have_rows('activities')) : ?>
						    <?php while (have_rows('activities')) : the_row(); ?>

						    	<div class='activity-item'>
						    		<div class='title'>
										<h4><?php echo get_sub_field('title'); ?></h4>
									</div>

									<div class='activity'>
										<img alt='<?php echo get_sub_field('title'); ?> Image' src='<?php echo l4k_normalizeImageUrl(get_sub_field('image')); ?>' />
									</div>

									<div class='featured-buttons'>
										<?php if (get_sub_field('download_link')) : ?>
											<a 	href='<?php echo get_sub_field('download_link'); ?>' 
												class='_btn perform-activity' 
												data-activity-name='<?php echo get_the_title(); ?>'
												data-activity-title='<?php echo get_sub_field('title'); ?>'
												target='_blank'>Download</a>
										<?php endif; ?>
										<?php if (get_sub_field('watch_link')) : ?>
											<a href='<?php echo get_permalink(get_sub_field('watch_link')); ?>' class='_btn' target='_blank'>Watch</a>
										<?php endif; ?>
									</div>
								</div>

						    <?php endwhile; ?>
						<?php endif; ?>
					</div>
				</div>

				<?php get_template_part('template-parts/section', 'sidebar-main'); ?>
			</div>

		<?php else : ?>

			<div class='single-activity-wrap'>
				<div class='mainbar'>
					<iframe title='Activity Frame' src='<?php echo l4k_normalizeImageUrl(get_field('iframe_source')); ?>'></iframe>
				</div>
				<?php get_template_part('template-parts/section', 'sidebar-main'); ?>
			</div>

		<?php endif; ?>

	</div>

</div>

<?php get_footer(); ?>