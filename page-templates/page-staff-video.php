<?php /* Template Name: Staff Video Page Template */ ?>

<?php get_header(); ?>

<div class='main-mid'>
<div class='_maxwrap '>

	<div class="staff-video-wrap">

		<div class='text'>
			<?php echo get_field('main_content'); ?>
		</div>

		<div class='video'>
	        <video controls poster="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/staff-video-bg.png">
	            <source src="<?php echo get_field('video_source'); ?>" type="video/mp4">
	            Your browser does not support the video tag.
	        </video>
		</div>

	</div>

</div>
</div>

<?php get_footer(); ?>