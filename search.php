<?php get_header(); ?>

<div class='main-mid'>
	<div class='_maxwrap'>

		<div class='search-results-wrap'>

			<div>

				<h1>Search Results for: '<?php echo get_search_query(); ?>'</h1>

				<?php if (have_posts()) : ?>

				    <div class='search-result-items'>
				        <?php while (have_posts()) : the_post(); ?>
			                <a class='result' href="<?php the_permalink(); ?>">
			                	<div class='result-img-wrap'>
				                	<?php if (get_post_type() == 'library') : ?>
				                		<img class='result-img' src='<?php echo get_field('logo_dashboard', get_the_ID()); ?>' />
									<?php elseif (get_post_type() == 'book') : ?>
										<img class='result-img' src='<?php echo get_field('book_image_url', get_the_ID()); ?>' />
										<img class='flag' src='<?php echo get_field('lang_flag_image', get_field('language', get_the_ID())); ?>' />
									<?php elseif (get_post_type() == 'language') : ?>
										<img class='result-img' src='<?php echo get_field('lang_flag_image', get_the_ID()); ?>' />
									<?php else: ?>
										<img class='result-img' src='<?php echo get_the_post_thumbnail_url(get_the_ID(), 'medium'); ?>' />
									<?php endif; ?>
								</div>

		                		<h4>
		                			<?php the_title(); ?>
								</h4>
				            </a>
				        <?php endwhile; ?>
				    </div>

				<?php else : ?>

				    <p>No results found.</p>

				<?php endif; ?>

			</div>

			<?php get_template_part('template-parts/section', 'sidebar-main'); ?>

		</div>

	</div>
</div>

<?php get_footer(); ?>