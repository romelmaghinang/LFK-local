<?php 
/* Template Name: Marketing Collateral Page Template */ 

$langArr = l4k_getLanguages($getComingSoon=false);
$langENAU = l4k_getLanguageDetails('127596'); 
$langENUS = l4k_getLanguageDetails('127598'); 
$langENUK = l4k_getLanguageDetails('127600'); 
?>

<?php get_header(); ?>

<div class='main-mid'>
	<div class='_maxwrap'>

		<div class="marketing-wrap">
			
			<div class='packs'>
				<h2><?php echo get_field('header_left_title'); ?></h2>
				<span class='last-updated'>Last updated: <?php echo get_field('last_updated'); ?></span>

				<?php if(have_rows('information_packs')): ?>
					<div class='pack-item__wrap'>
					    <?php while(have_rows('information_packs')): the_row(); ?>
					    	<div class='pack-item'>
								<img src='<?php the_sub_field('image_url'); ?>' />
								<a class='_btn' href='<?php the_sub_field('button_link'); ?>'>
									<?php the_sub_field('button_text'); ?>
								</a>
							</div>
					    <?php endwhile; ?>
				    </div>
				<?php endif; ?>
			</div>

			<div class='resources'>

				<h2><?php echo get_field('header_right_title'); ?></h2>
				<span class='last-updated'>Last updated: <?php echo get_field('last_updated'); ?></span>
				<h3><?php echo get_field('header_right_text'); ?></h3>

				<div class='lang-wrap'>

					<span></span>
					<span class='lang-item'>
						<h5><?php echo $langENAU['title']; ?></h5>
						<img src='<?php echo $langENAU['flag_url']; ?>' />
						<a class='_btn' href='<?php echo $langENAU['marketing']; ?>'>Download</a>
					</span>
					<span class='lang-item'>
						<h5><?php echo $langENUS['title']; ?></h5>
						<img src='<?php echo $langENUS['flag_url']; ?>' />
						<a class='_btn' href='<?php echo $langENUS['marketing']; ?>'>Download</a>
					</span>
					<span class='lang-item'>
						<h5><?php echo $langENUK['title']; ?></h5>
						<img src='<?php echo $langENUK['flag_url']; ?>' />
						<a class='_btn' href='<?php echo $langENUK['marketing']; ?>'>Download</a>
					</span>
					<span></span>

					<?php if ($langArr): ?>
						<?php foreach ($langArr as $lang => $l): ?>

							<span class='lang-item'>
								<h5><?php echo $l['title']; ?></h5>
								<img src='<?php echo $l['flag_url']; ?>' />
								<?php if ($l['marketing']) : ?>
									<a class='_btn' href='<?php echo $l['marketing']; ?>'>Download</a>
								<?php else : ?>
									<a class='_btn disabled'>Coming Soon</a>
								<?php endif; ?>
							</span>

						<?php endforeach; ?>
					<?php endif; ?>

				</div>

			</div>

		</div>

	</div>
</div>

<?php get_footer(); ?>