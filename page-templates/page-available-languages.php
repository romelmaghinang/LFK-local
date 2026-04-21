<?php /* Template Name: Languages Page Template */ ?>

<?php get_header(); ?>

<div class='main-mid'>
<div class='_maxwrap medium'>

	<h1 class='_maintxt'><?php echo get_field('section_title'); ?></h1>

	<?php get_template_part('template-parts/section', 'lang-loop'); ?>

</div>
</div>

<?php get_footer(); ?>