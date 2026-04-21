<?php /* Template Name: Mobile App Page Template */ ?>

<?php get_header(); ?>

<div class='main-mid'>
	<div class='_maxwrap'>

		<div class="mobile-app-wrap">

			<h1 class='_maintxt'><?php echo get_field('header'); ?></h1>

			<div class="main-text">
				<?php echo get_field('main_content'); ?>	
			</div>

			<div class="mobile-app-content">

				<div class="column download-links">
					<h3>Android</h3>
					<p>Click the Play Store button to download</p>
					<a 	href='<?php echo get_field('android_download_link'); ?>' 
						aria-label='Download on Google Play'
						target='_blank'>
						<img alt='Android Download Button' src='<?php echo get_stylesheet_directory_uri(); ?>/assets/img/download-google-no-line.webp' />
					</a>
					<p>Scan the QR code to download</p>
					<img alt='Android QR Code' src='<?php echo get_field('android_qr_code'); ?>' /> 
				</div>

				<div class="column main-image">
					<img alt='Lekti Hero Image' src='<?php echo get_field('main_image'); ?>' /> 
				</div>	

				<div class="column download-links">
					<h3>Apple</h3>
					<p>Click the App Store button to download</p>
					<a 	href='<?php echo get_field('apple_download_link'); ?>' 
						aria-label='Download on Apple Store'
						target='_blank'>
						<img alt='Apple Download Button' src='<?php echo get_stylesheet_directory_uri(); ?>/assets/img/download-apple-no-line.webp' />
					</a>
					<p>Scan the QR code to download</p>
					<img alt='Apple QR Code' src='<?php echo get_field('apple_qr_code'); ?>' /> 
				</div>

			</div>

		</div>		

	</div>
</div>

<?php get_footer(); ?>