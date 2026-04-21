<?php /* Template Name: Teacher Subscription Page Template */ ?>

<?php get_header(); ?>

<div class='main-mid'>
<div class='_maxwrap medium'>

	<div class="teacher-wrap">

		<div class='left'>
			<div class='text'><?php echo nl2br(get_field('text')); ?></div>
			<div class='video'>
		        <video controls poster="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/staff-video-bg.png">
		            <source src="<?php echo get_field('video'); ?>" type="video/mp4">
		            Your browser does not support the video tag.
		        </video>
			</div>
		</div>

		<div class='right'>
			<div class='form'>
				<?php echo do_shortcode('[wpforms id="'.get_field('form').'" title="false"]'); ?>
			</div>
		</div>

	</div>

</div>
</div>

<div class='embed__overlay terms'>
	<div class='embed__wrap'>
		<div class='embed__wrap__inner'>
			<div class='embed__decoration'><img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/blowing-leaf1.png" alt="leaf"></div>
			<div class='embed__decoration-2'><img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/blowing-leaf3.png" alt="leaf"></div>
			<a class='embed__close'><i class="lni lni-xmark"></i></a>
			<div class='embed__title'><?php echo get_field('terms_and_conditions_title'); ?></div>
			<div class='embed__content terms'>
				<?php echo get_field('terms_and_conditions'); ?>
			</div>
		</div>
	</div>
</div>

<?php get_footer(); ?>