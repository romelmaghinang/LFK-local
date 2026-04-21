<?php 
/* Template Name: Activities Page Template */ 

l4k_checkMemberLoggedIn(); // check if member is logged in
get_header(); // display header
$activityArr = l4k_getActivities($getComingSoon=false);
$comingSoonActivityArr = l4k_getActivities($getComingSoon=true);
?>

<div class='main-mid'>
	<div class='_maxwrap'>

		<div class='activity-wrap'>

			<div class='mainbar'>

				<h1>
					<img alt='Activities Hero Image' src='<?php echo get_field('main_image'); ?>' />
					<?php echo get_the_title(); ?>
				</h1>

				<div class='activities-wrap'>

					<?php if ($activityArr): ?>
						<?php foreach ($activityArr as $activity => $a): ?>

							<div class='activity-item'>

								<div class='title'>
									<h4><?php echo $a['title']; ?></h4>
								</div>

								<div class='activity'>
									<a href='<?php echo $a['permalink']; ?>'>
										<img alt='Activity Image' src='<?php echo $a['activity_image']; ?>' />
									</a>
								</div>

								<div class='featured-buttons'>
									<?php if ($a['collections']) : ?>
										<a href='<?php echo $a['collections']; ?>' class='_btn'>Downloads</a>
									<?php endif; ?>
									<a href='<?php echo $a['permalink']; ?>' class='_btn'>Online</a>
								</div>

							</div>

						<?php endforeach; ?>
					<?php endif; ?>

				</div>

				<h1>
					Coming Soon
				</h1>

				<div class='activities-wrap coming-soon'>

					<?php if ($comingSoonActivityArr): ?>
						<?php foreach ($comingSoonActivityArr as $activity => $a): ?>

							<div class='activity-item'>

								<div class='title'>
									<h4><?php echo $a['title']; ?></h4>
								</div>

								<div class='activity'>
									<a href='javascript: void(0);'>
										<img alt='Activity Image' src='<?php echo $a['activity_image']; ?>' />
									</a>
								</div>

							</div>

						<?php endforeach; ?>
					<?php endif; ?>

				</div>

			</div>

			<?php get_template_part('template-parts/section', 'sidebar-main'); ?>

		</div>

	</div>
</div>

<?php get_footer(); ?>