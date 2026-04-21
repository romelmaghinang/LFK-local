</main><!-- .site-content -->

<footer class="site-footer">

    <div class='_maxwrap'>

    	<div class='footer-elements'>

	        <div class='footer-contact'>
	            
	            <h3>Stay in Touch</h3>

	            <?php echo do_shortcode('[wpforms id="'.get_field('footer_contact_form', 'option').'" title="false"]'); ?>

	        </div>

	        <div class='spacer'></div>

	        <div class='footer-links'>
	            <nav class="footer-nav">
	                <?php
	                wp_nav_menu([
	                    'menu'       => 'Footer Menu',
	                    'container'  => false,
	                    'menu_class' => 'menu'
	                ]);
	                ?>

	                <div class='download-app'>

	                    <span>Download The <i>LOTE4Kids</i> App</span>

						<?php if (get_field('footer_mobile_app_links_apple_download_link', 'option')) { ?>
						    <a 	href='<?php echo get_field('footer_mobile_app_links_apple_download_link', 'option'); ?>' 
						    	target='_blank' 
						    	aria-label='Download on the App Store (opens in new tab)'>
						        <img 	src='<?php echo get_stylesheet_directory_uri(); ?>/assets/img/download-apple.png' 
						        		alt='' 
						        		aria-hidden='true' />
						    </a>
						<?php } ?>
						<?php if (get_field('footer_mobile_app_links_google_download_link', 'option')) { ?>
						    <a 	href='<?php echo get_field('footer_mobile_app_links_google_download_link', 'option'); ?>' 
						    	target='_blank' 
						    	aria-label='Get it on Google Play (opens in new tab)'>
						        <img 	src='<?php echo get_stylesheet_directory_uri(); ?>/assets/img/download-google.png' 
						        		alt='' 
						        		aria-hidden='true' />
						    </a>
						<?php } ?>

	                </div>

	                <div class='_share socials'>

						<?php if (get_field('footer_social_media_links_general_facebook_url', 'option')) { ?>
						    <a href='<?php echo get_field('footer_social_media_links_general_facebook_url', 'option'); ?>' target='_blank' class='facebook' aria-label='Visit our Facebook page (opens in new tab)'>
						        <svg style="display:block;" focusable="false" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 32 32"><path fill="#fff" d="M28 16c0-6.627-5.373-12-12-12S4 9.373 4 16c0 5.628 3.875 10.35 9.101 11.647v-7.98h-2.474V16H13.1v-1.58c0-4.085 1.849-5.978 5.859-5.978.76 0 2.072.15 2.608.298v3.325c-.283-.03-.775-.045-1.386-.045-1.967 0-2.728.745-2.728 2.683V16h3.92l-.673 3.667h-3.247v8.245C23.395 27.195 28 22.135 28 16Z"></path></svg>
						    </a>
						<?php } ?>
						<?php if (get_field('footer_social_media_links_general_linkedin_url', 'option')) { ?>
						    <a href='<?php echo get_field('footer_social_media_links_general_linkedin_url', 'option'); ?>' target='_blank' class='linkedin' aria-label='Visit our LinkedIn page (opens in new tab)'>
						        <svg style="display:block;" focusable="false" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 32 32"><path d="M6.227 12.61h4.19v13.48h-4.19V12.61zm2.095-6.7a2.43 2.43 0 0 1 0 4.86c-1.344 0-2.428-1.09-2.428-2.43s1.084-2.43 2.428-2.43m4.72 6.7h4.02v1.84h.058c.56-1.058 1.927-2.176 3.965-2.176 4.238 0 5.02 2.792 5.02 6.42v7.395h-4.183v-6.56c0-1.564-.03-3.574-2.178-3.574-2.18 0-2.514 1.7-2.514 3.46v6.668h-4.187V12.61z" fill="#fff"></path></svg>
						    </a>
						<?php } ?>
						<?php if (get_field('footer_social_media_links_general_email', 'option')) { ?>
						    <a href='<?php echo get_field('footer_social_media_links_general_email', 'option'); ?>' class='mail' aria-label='Send us an email'>
						        <svg style="display:block;" focusable="false" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="-.75 -.5 36 36"><path d="M 5.5 11 h 23 v 1 l -11 6 l -11 -6 v -1 m 0 2 l 11 6 l 11 -6 v 11 h -22 v -11" stroke-width="1" fill="#fff"></path></svg>
						    </a>
						<?php } ?>

	                </div>
	            </nav>
	        </div>

        </div>

    </div>

    <div class="container">
        <div class='_maxwrap'>
            <span><?php echo get_field('footer_copyright', 'option'); ?></span>
        </div>    
    </div>

</footer>

<?php if (!is_front_page() && !is_singular('library')) { ?>
<?php // if (false) { ?>
	<div class='embed__overlay trial-popup'>
		<div class='embed__wrap'>
			<div class='embed__wrap__inner'>
				<div class='embed__decoration'><img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/blowing-leaf1.png" alt="leaf"></div>
				<div class='embed__decoration-2'><img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/blowing-leaf3.png" alt="leaf"></div>
				<a class='embed__close'><i class="lni lni-xmark"></i></a>
				<div class='embed__title'><div class='trial-welcome'><?php echo get_field('onboarding_text_welcome', $_SESSION['library_id']); ?></div></div>
				<div class='embed__content trial-popup-content'>					
					<div class='trial-heading'>
						<div>
							<?php echo get_field('onboarding_text_text', $_SESSION['library_id']); ?>
						</div>
					</div>
					<div class='trial-items'>
						<ul>
							<li> <!-- onboarding flipbook -->
								<span>1.</span>
								<input 	type="checkbox" 
										data-url='<?php echo get_field('onboarding_flipbook_link', $_SESSION['library_id']); ?>'
										data-newtab='<?php echo get_field('onboarding_flipbook_open_in_new_tab', $_SESSION['library_id']); ?>'
										data-action='flipbook' /> 
								<a 	href='<?php echo get_field('onboarding_flipbook_link', $_SESSION['library_id']); ?>' 
									target='<?php echo (get_field('onboarding_flipbook_open_in_new_tab', $_SESSION['library_id'])) ? '_blank' : ''; ?>'>
									<?php echo get_field('onboarding_flipbook_text', $_SESSION['library_id']); ?>
								</a>
							</li>
							<li> <!-- onboarding quiz -->
								<span>2.</span>
								<input 	type="checkbox" 
										data-url='<?php echo get_field('onboarding_quiz_link', $_SESSION['library_id']); ?>'								
										data-newtab='<?php echo get_field('onboarding_quiz_open_in_new_tab', $_SESSION['library_id']); ?>'
										data-action='quiz' /> 
								<a 	href='<?php echo get_field('onboarding_quiz_link', $_SESSION['library_id']); ?>' 
									target='<?php echo (get_field('onboarding_quiz_open_in_new_tab', $_SESSION['library_id'])) ? '_blank' : ''; ?>'>
									<?php echo get_field('onboarding_quiz_text', $_SESSION['library_id']); ?>
								</a>
							</li>
							<li> <!-- onboarding video -->
								<span>3.</span>
								<input 	type="checkbox" 
										data-url='<?php echo get_field('onboarding_video_link', $_SESSION['library_id']); ?>'
										data-newtab='<?php echo get_field('onboarding_video_open_in_new_tab', $_SESSION['library_id']); ?>'
										data-action='video' /> 
								<a 	href='<?php echo get_field('onboarding_video_link', $_SESSION['library_id']); ?>' 
									target='<?php echo (get_field('onboarding_video_open_in_new_tab', $_SESSION['library_id'])) ? '_blank' : ''; ?>'>
									<?php echo get_field('onboarding_video_text', $_SESSION['library_id']); ?>
								</a>
							</li>
							<li> <!-- onboarding picture card -->
								<span>4.</span>
								<input 	type="checkbox" 
										data-url='<?php echo get_field('onboarding_picture_card_link', $_SESSION['library_id']); ?>'
										data-newtab='<?php echo get_field('onboarding_picture_card_open_in_new_tab', $_SESSION['library_id']); ?>'
										data-action='picture_card' /> 
								<a 	href='<?php echo get_field('onboarding_picture_card_link', $_SESSION['library_id']); ?>' 
									target='<?php echo (get_field('onboarding_picture_card_open_in_new_tab', $_SESSION['library_id'])) ? '_blank' : ''; ?>'>
									<?php echo get_field('onboarding_picture_card_text', $_SESSION['library_id']); ?>
								</a>
							</li>
							<li> <!-- onboarding non-fiction -->
								<span>5.</span>
								<input 	type="checkbox" 
										data-url='<?php echo get_field('onboarding_non_fiction_link', $_SESSION['library_id']); ?>'
										data-newtab='<?php echo get_field('onboarding_non_fiction_open_in_new_tab', $_SESSION['library_id']); ?>'
										data-action='non_fiction' /> 
								<a 	href='<?php echo get_field('onboarding_non_fiction_link', $_SESSION['library_id']); ?>' 
									target='<?php echo (get_field('onboarding_non_fiction_open_in_new_tab', $_SESSION['library_id'])) ? '_blank' : ''; ?>'>
									<?php echo get_field('onboarding_non_fiction_text', $_SESSION['library_id']); ?>
								</a>
							</li>
							<li> <!-- onboarding sign language-->
								<span>6.</span>
								<input 	type="checkbox" 
										data-url='<?php echo get_field('onboarding_sign_language_link', $_SESSION['library_id']); ?>'
										data-newtab='<?php echo get_field('onboarding_sign_language_open_in_new_tab', $_SESSION['library_id']); ?>'
										data-action='sign_language' /> 
								<a 	href='<?php echo get_field('onboarding_sign_language_link', $_SESSION['library_id']); ?>' 
									target='<?php echo (get_field('onboarding_sign_language_open_in_new_tab', $_SESSION['library_id'])) ? '_blank' : ''; ?>'>
									<?php echo get_field('onboarding_sign_language_text', $_SESSION['library_id']); ?>
								</a>
							</li>
							<li> <!-- onboarding language fun facts -->
								<span>7.</span>
								<input 	type="checkbox" 
										data-url='<?php echo get_field('onboarding_language_fun_facts_link', $_SESSION['library_id']); ?>'
										data-newtab='<?php echo get_field('onboarding_language_fun_facts_open_in_new_tab', $_SESSION['library_id']); ?>'
										data-action='fun_facts' /> 
								<a 	href='<?php echo get_field('onboarding_language_fun_facts_link', $_SESSION['library_id']); ?>' 
									target='<?php echo (get_field('onboarding_language_fun_facts_open_in_new_tab', $_SESSION['library_id'])) ? '_blank' : ''; ?>'>
									<?php echo get_field('onboarding_language_fun_facts_text', $_SESSION['library_id']); ?>
								</a>
							</li>
							<li> <!-- onboarding lekti -->
								<span>8.</span>
								<input 	type="checkbox" 
										data-url='<?php echo get_field('onboarding_lekti_link', $_SESSION['library_id']); ?>'
										data-newtab='<?php echo get_field('onboarding_lekti_open_in_new_tab', $_SESSION['library_id']); ?>'
										data-action='lekti' /> 
								<a 	href='<?php echo get_field('onboarding_lekti_link', $_SESSION['library_id']); ?>' 
									target='<?php echo (get_field('onboarding_lekti_open_in_new_tab', $_SESSION['library_id'])) ? '_blank' : ''; ?>'>
									<?php echo get_field('onboarding_lekti_text', $_SESSION['library_id']); ?>
								</a>
							<li> <!-- onboarding activities -->
								<span>9.</span>
								<input 	type="checkbox" 
										data-url='<?php echo get_field('onboarding_activities_link', $_SESSION['library_id']); ?>'
										data-newtab='<?php echo get_field('onboarding_activities_open_in_new_tab', $_SESSION['library_id']); ?>'
										data-action='activities' /> 
								<a 	href='<?php echo get_field('onboarding_activities_link', $_SESSION['library_id']); ?>' 
									target='<?php echo (get_field('onboarding_activities_open_in_new_tab', $_SESSION['library_id'])) ? '_blank' : ''; ?>'>
									<?php echo get_field('onboarding_activities_text', $_SESSION['library_id']); ?>
								</a>
							</li>
							<li> <!-- onboarding mobile app -->
								<span>10.</span>
								<input 	type="checkbox" 
										data-url='<?php echo get_field('onboarding_mobile_link', $_SESSION['library_id']); ?>'
										data-newtab='<?php echo get_field('onboarding_mobile_open_in_new_tab', $_SESSION['library_id']); ?>'
										data-action='mobile' /> 
								<a 	href='<?php echo get_field('onboarding_mobile_link', $_SESSION['library_id']); ?>' 
									target='<?php echo (get_field('onboarding_mobile_open_in_new_tab', $_SESSION['library_id'])) ? '_blank' : ''; ?>'>
									<?php echo get_field('onboarding_mobile_text', $_SESSION['library_id']); ?>
								</a>
							</li>
							<li> <!-- onboarding overview video -->
								<span>11.</span>
								<input 	type="checkbox" 
										data-url='<?php echo get_field('onboarding_overview_video_link', $_SESSION['library_id']); ?>'
										data-newtab='<?php echo get_field('onboarding_overview_video_open_in_new_tab', $_SESSION['library_id']); ?>'
										data-action='overview_video' /> 
								<a 	href='<?php echo get_field('onboarding_overview_video_link', $_SESSION['library_id']); ?>' 
									target='<?php echo (get_field('onboarding_overview_video_open_in_new_tab', $_SESSION['library_id'])) ? '_blank' : ''; ?>'>
									<?php echo get_field('onboarding_overview_video_text', $_SESSION['library_id']); ?>
								</a>
							</li>
							<li> <!-- onboarding staff portal -->
								<span>12.</span>
								<input 	type="checkbox" 
										data-url='<?php echo get_field('onboarding_staff_portal_guide_link', $_SESSION['library_id']); ?>'
										data-newtab='<?php echo get_field('onboarding_staff_portal_guide_open_in_new_tab', $_SESSION['library_id']); ?>'
										data-action='staff_portal' /> 
								<a 	href='<?php echo get_field('onboarding_staff_portal_guide_link', $_SESSION['library_id']); ?>' 
									target='<?php echo (get_field('onboarding_staff_portal_guide_open_in_new_tab', $_SESSION['library_id'])) ? '_blank' : ''; ?>'>
									<?php echo get_field('onboarding_staff_portal_guide_text', $_SESSION['library_id']); ?>
								</a>
							</li>
						</ul>
						<div>
							<img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/trial-avatar.webp" alt="LOTE4Kids Logo">
						</div>
					</div>
					<div class='trial-cta'>
						<button class='_btn'>Start Exploring</button>
						<?php /* <button class='_btn diminish'>Maybe later</button> */ ?>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php } ?>

<div class='fixed-elements' translate="no" class="notranslate">

	<?php 
	$uri = $_SERVER['REQUEST_URI'];
	$isDashboard = (strpos($uri, '/dashboard') !== false);
	$isTrial = (strpos($uri, '/trial') !== false);
	$isCompetition = (strpos($uri, '/competition') !== false);
	$isTraining = (strpos($uri, '/webinar') !== false);
	$isReview = (strpos($uri, '/staff-training') !== false);
	?>

	<?php if (is_front_page() || (is_singular('library') && !$isDashboard && !$isTrial && !$isCompetition && !$isTraining && !$isReview)) : ?>
		<div class='blowing-leaves' role="region" aria-label="Blowing Leaves">
		    <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/blowing-leaf1.png" class="blowing-leaf" alt="">
		    <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/blowing-leaf2.png" class="blowing-leaf" alt="">
		    <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/blowing-leaf3.png" class="blowing-leaf" alt="">
		</div>
	<?php endif; ?>

	<div class='_toast'></div>

    <div class='fixed-elements-menu-items'>

		<?php if (!is_front_page() && !is_singular('library')) { ?>
		<?php // if (false) { ?>
			<div class="toolbox-item trial-checklist">
				<div id="trial-toggle" class='toolbox-link' data-tooltip='Trial Checklist' role="button" tabindex="0"><i class="lni lni-menu-cheesburger"></i></div>
				<div id="trial-menu-status" class='toolbox-content'>
					Trial Checklist - <span class='trial-counter'></span> <span class='pulse-shadow'></span></a>
				</div>
				<div id="trial-menu" class='toolbox-content'>
					<ul aria-label="Trial Checklist">
						<li><a class='heading'>Trial Checklist - <span class='trial-counter'></span></a></li>
						<li> <!-- onboarding flipbook -->
							<a 	href='<?php echo get_field('onboarding_flipbook_link', $_SESSION['library_id']); ?>'
								target='<?php echo (get_field('onboarding_flipbook_open_in_new_tab', $_SESSION['library_id'])) ? '_blank' : ''; ?>'>
								<span class='num'>1.</span>
								<input 	type="checkbox" 
										data-url='<?php echo get_field('onboarding_flipbook_link', $_SESSION['library_id']); ?>'
										data-newtab='<?php echo get_field('onboarding_flipbook_open_in_new_tab', $_SESSION['library_id']); ?>'
										data-action='flipbook' /> 
								<span class='desc'><?php echo get_field('onboarding_flipbook_text', $_SESSION['library_id']); ?></span>
							</a>
						</li>
						<li> <!-- onboarding quiz -->
							<a 	href='<?php echo get_field('onboarding_quiz_link', $_SESSION['library_id']); ?>'
								target='<?php echo (get_field('onboarding_quiz_open_in_new_tab', $_SESSION['library_id'])) ? '_blank' : ''; ?>'>
								<span class='num'>2.</span>
								<input 	type="checkbox" 
										data-url='<?php echo get_field('onboarding_quiz_link', $_SESSION['library_id']); ?>'
										data-newtab='<?php echo get_field('onboarding_quiz_open_in_new_tab', $_SESSION['library_id']); ?>'								
										data-action='quiz' /> 
								<span class='desc'><?php echo get_field('onboarding_quiz_text', $_SESSION['library_id']); ?></span>
							</a>
						</li>
						<li> <!-- onboarding video -->
							<a 	href='<?php echo get_field('onboarding_video_link', $_SESSION['library_id']); ?>'
								target='<?php echo (get_field('onboarding_video_open_in_new_tab', $_SESSION['library_id'])) ? '_blank' : ''; ?>'>
								<span class='num'>3.</span>
								<input 	type="checkbox" 
										data-url='<?php echo get_field('onboarding_video_link', $_SESSION['library_id']); ?>'
										data-newtab='<?php echo get_field('onboarding_video_open_in_new_tab', $_SESSION['library_id']); ?>'
										data-action='video' /> 
								<span class='desc'><?php echo get_field('onboarding_video_text', $_SESSION['library_id']); ?></span>
							</a>
						</li>
						<li> <!-- onboarding picture card -->
							<a 	href='<?php echo get_field('onboarding_picture_card_link', $_SESSION['library_id']); ?>'
								target='<?php echo (get_field('onboarding_picture_card_open_in_new_tab', $_SESSION['library_id'])) ? '_blank' : ''; ?>'>
								<span class='num'>4.</span>
								<input 	type="checkbox" 
										data-url='<?php echo get_field('onboarding_picture_card_link', $_SESSION['library_id']); ?>'
										data-newtab='<?php echo get_field('onboarding_picture_card_open_in_new_tab', $_SESSION['library_id']); ?>'
										data-action='picture_card' /> 
								<span class='desc'><?php echo get_field('onboarding_picture_card_text', $_SESSION['library_id']); ?></span>
							</a>
						</li>
						<li> <!-- onboarding non-fiction -->
							<a 	href='<?php echo get_field('onboarding_non_fiction_link', $_SESSION['library_id']); ?>'
								target='<?php echo (get_field('onboarding_non_fiction_open_in_new_tab', $_SESSION['library_id'])) ? '_blank' : ''; ?>'>
								<span class='num'>5.</span>
								<input 	type="checkbox" 
										data-url='<?php echo get_field('onboarding_non_fiction_link', $_SESSION['library_id']); ?>'
										data-newtab='<?php echo get_field('onboarding_non_fiction_open_in_new_tab', $_SESSION['library_id']); ?>'
										data-action='non_fiction' /> 
								<span class='desc'><?php echo get_field('onboarding_non_fiction_text', $_SESSION['library_id']); ?></span>
							</a>
						</li>
						<li> <!-- onboarding sign language -->
							<a 	href='<?php echo get_field('onboarding_sign_language_link', $_SESSION['library_id']); ?>'
								target='<?php echo (get_field('onboarding_sign_language_open_in_new_tab', $_SESSION['library_id'])) ? '_blank' : ''; ?>'>
								<span class='num'>6.</span>
								<input 	type="checkbox" 
										data-url='<?php echo get_field('onboarding_sign_language_link', $_SESSION['library_id']); ?>'
										data-newtab='<?php echo get_field('onboarding_sign_language_open_in_new_tab', $_SESSION['library_id']); ?>'
										data-action='sign_language' /> 
								<span class='desc'><?php echo get_field('onboarding_sign_language_text', $_SESSION['library_id']); ?></span>
							</a>
						</li>
						<li> <!-- onboarding language fun facts -->
							<a 	href='<?php echo get_field('onboarding_language_fun_facts_link', $_SESSION['library_id']); ?>'
								target='<?php echo (get_field('onboarding_language_fun_facts_open_in_new_tab', $_SESSION['library_id'])) ? '_blank' : ''; ?>'>
								<span class='num'>7.</span>
								<input 	type="checkbox" 
										data-url='<?php echo get_field('onboarding_language_fun_facts_link', $_SESSION['library_id']); ?>'
										data-newtab='<?php echo get_field('onboarding_language_fun_facts_open_in_new_tab', $_SESSION['library_id']); ?>'
										data-action='fun_facts' /> 
								<span class='desc'><?php echo get_field('onboarding_language_fun_facts_text', $_SESSION['library_id']); ?></span>
							</a>
						</li>
						<li> <!-- onboarding lekti -->
							<a 	href='<?php echo get_field('onboarding_lekti_link', $_SESSION['library_id']); ?>'
								target='<?php echo (get_field('onboarding_lekti_open_in_new_tab', $_SESSION['library_id'])) ? '_blank' : ''; ?>'>
								<span class='num'>8.</span>
								<input 	type="checkbox" 
										data-url='<?php echo get_field('onboarding_lekti_link', $_SESSION['library_id']); ?>'
										data-newtab='<?php echo get_field('onboarding_lekti_open_in_new_tab', $_SESSION['library_id']); ?>'
										data-action='lekti' /> 
								<span class='desc'><?php echo get_field('onboarding_lekti_text', $_SESSION['library_id']); ?></span>
							</a>
						</li>
						<li> <!-- onboarding activities -->
							<a 	href='<?php echo get_field('onboarding_activities_link', $_SESSION['library_id']); ?>'
								target='<?php echo (get_field('onboarding_activities_open_in_new_tab', $_SESSION['library_id'])) ? '_blank' : ''; ?>'>
								<span class='num'>9.</span>
								<input 	type="checkbox" 
										data-url='<?php echo get_field('onboarding_activities_link', $_SESSION['library_id']); ?>'
										data-newtab='<?php echo get_field('onboarding_activities_open_in_new_tab', $_SESSION['library_id']); ?>'
										data-action='activities' /> 
								<span class='desc'><?php echo get_field('onboarding_activities_text', $_SESSION['library_id']); ?></span>
							</a>
						</li>
						<li> <!-- onboarding mobile app -->
							<a 	href='<?php echo get_field('onboarding_mobile_link', $_SESSION['library_id']); ?>'
								target='<?php echo (get_field('onboarding_mobile_open_in_new_tab', $_SESSION['library_id'])) ? '_blank' : ''; ?>'>
								<span class='num'>10.</span>
								<input 	type="checkbox" 
										data-url='<?php echo get_field('onboarding_mobile_link', $_SESSION['library_id']); ?>'
										data-newtab='<?php echo get_field('onboarding_mobile_open_in_new_tab', $_SESSION['library_id']); ?>'
										data-action='mobile' /> 
								<span class='desc'><?php echo get_field('onboarding_mobile_text', $_SESSION['library_id']); ?></span>
							</a>
						</li>
						<li> <!-- onboarding overview video -->
							<a 	href='<?php echo get_field('onboarding_overview_video_link', $_SESSION['library_id']); ?>'
								target='<?php echo (get_field('onboarding_overview_video_open_in_new_tab', $_SESSION['library_id'])) ? '_blank' : ''; ?>'>
								<span class='num'>11.</span>
								<input 	type="checkbox" 
										data-url='<?php echo get_field('onboarding_overview_video_link', $_SESSION['library_id']); ?>'
										data-newtab='<?php echo get_field('onboarding_overview_video_open_in_new_tab', $_SESSION['library_id']); ?>'
										data-action='overview_video' /> 
								<span class='desc'><?php echo get_field('onboarding_overview_video_text', $_SESSION['library_id']); ?></span>
							</a>
						</li>
						<li> <!-- onboarding staff portal --> 
							<a 	href='<?php echo get_field('onboarding_staff_portal_guide_link', $_SESSION['library_id']); ?>'
								target='<?php echo (get_field('onboarding_staff_portal_guide_open_in_new_tab', $_SESSION['library_id'])) ? '_blank' : ''; ?>'>
								<span class='num'>12.</span>
								<input 	type="checkbox" 
										data-url='<?php echo get_field('onboarding_staff_portal_guide_link', $_SESSION['library_id']); ?>'
										data-newtab='<?php echo get_field('onboarding_staff_portal_guide_open_in_new_tab', $_SESSION['library_id']); ?>'
										data-action='staff_portal' /> 
								<span class='desc'><?php echo get_field('onboarding_staff_portal_guide_text', $_SESSION['library_id']); ?></span>
							</a>
						</li>
					</ul>
				</div>
			</div>
		<?php } ?>

		<div class="toolbox-item accessibility-wrapper" aria-label="Accessibility">
			<div id="accessibility-toggle" aria-label="Open Tools" class='toolbox-link' data-tooltip='Accessibility Tools' role="button" tabindex="0"><i class="lni lni-layers-1"></i></div>
			<div id="accessibility-menu" class='toolbox-content'>
				<ul aria-label="Accessibility Items">
					<li><a class='heading'>Accessibility Tools</a></li>
					<li><a id='acce-increase-text'><i class="lni lni-plus"></i>Increase Text</a></li>
					<li><a id='acce-decrease-text'><i class="lni lni-minus"></i>Decrease Text</a></li>
					<li><a id='acce-grayscale'><i class="lni lni-text-paragraph"></i>Grayscale</a></li>
					<li><a id='acce-high-contrast'><i class="lni lni-colour-palette-3"></i>High Contrast</a></li>
					<li><a id='acce-reset'><i class="lni lni-refresh-circle-1-clockwise"></i>Reset</a></li>
				</ul>
			</div>
		</div>

		<div class="toolbox-item flag-wrapper">
			<div id="flag-toggle" aria-label="Switch Language" class='toolbox-link' data-tooltip='Switch Language' role="button" tabindex="0"><img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/flag-en.svg" alt=""></div>
			<div id="flag-menu" class='toolbox-content'>
				<ul aria-label="Language Items">
					<li><a class='heading'>Switch Language</a></li>
					<li><a class='gtranslate-btn' data-lang='en'><img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/flag-en.svg" alt="">English</a></li>
					<li><a class='gtranslate-btn' data-lang='ar'><img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/flag-ar.svg" alt="">العربية</a></li>
					<li><a class='gtranslate-btn' data-lang='zh-CN'><img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/flag-zh.svg" alt="">简体中文</a></li>
					<li><a class='gtranslate-btn' data-lang='zh-TW'><img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/flag-zh.svg" alt="">繁體中文</a></li>
					<li><a class='gtranslate-btn' data-lang='de'><img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/flag-de.svg" alt="">Deutsch</a></li>
					<li><a class='gtranslate-btn' data-lang='es'><img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/flag-es.svg" alt="">Español</a></li>
					<li><a class='gtranslate-btn' data-lang='fr'><img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/flag-fr.svg" alt="">Français</a></li>
					<li><a class='gtranslate-btn' data-lang='hi'><img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/flag-hi.svg" alt="">हिन्दी</a></li>
					<li><a class='gtranslate-btn' data-lang='it'><img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/flag-it.svg" alt="">Italiano</a></li>
					<li><a class='gtranslate-btn' data-lang='ja'><img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/flag-ja.svg" alt="">日本語</a></li>
					<li><a class='gtranslate-btn' data-lang='ru'><img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/flag-ru.svg" alt="">Русский</a></li>
					<li><a class='gtranslate-btn' data-lang='mi'><img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/flag-mi.svg" alt="">Te Reo Māori</a></li>
					<li><a class='gtranslate-btn' data-lang='uk'><img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/flag-uk.svg" alt="">Українська</a></li>
					<li><a class='gtranslate-btn' data-lang='vi'><img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/flag-vi.svg" alt="">Tiếng Việt</a></li>
					<li><a class='gtranslate-btn' data-lang='cy'><img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/flag-cy.svg" alt="">Cymraeg</a></li>
				</ul>
				<div id="google_translate_element"></div>
				<script>
				function googleTranslateElementInit() {
				    new google.translate.TranslateElement({
				        pageLanguage: 'en',
				        includedLanguages: 'en,ar,zh-CN,zh-TW,de,es,fr,hi,it,ja,ru,mi,uk,vi,cy',
				        autoDisplay: false
				    }, 'google_translate_element');
				}	
				</script>
			</div>
		</div>

		<?php if (is_front_page() || is_page_template('page-templates/page-find-library.php')) : ?>
			<div class="toolbox-item chat-wrapper" aria-label="Chat">
				<div id="chat-toggle" class='toolbox-link' data-tooltip='Find Your Library or School' role="button" tabindex="0"><i class="lni lni-message-2"></i></div>
				<div id="chat-menu" class='toolbox-content'>
					Can't find your library or school? Let me help! <span class='pulse-shadow'></span>
				</div>
			</div>
		<?php endif; ?>

		<?php /*
		<div class="toolbox-item cookie-notice-wrapper">
			<div id="cookie-notice-toggle" class='toolbox-link' data-tooltip='Cookie Policy'><i class="lni lni-hand-shake"></i></div>
			<div id="cookie-notice-menu" class='toolbox-content'>
			    <?php if (get_field('cookie_policy', 'option')) : ?>
					<?php echo apply_filters('the_content', get_field('cookie_policy', 'option')); ?>
			    <?php endif; ?>
			    <a href='javascript: void(0);' id='accept-cookie'>Accept & Close</a>
			</div>
		</div>
		*/ ?>

		<?php if (WP_ENVIRONMENT_TYPE != 'PRD') : ?>
			<div class="toolbox-item dev-debug-wrapper">
				<div id="dev-debug-toggle" class='toolbox-link' data-tooltip='Debug (STG only)'><i class="lni lni-monitor-code"></i></div>
				<div id="dev-debug-menu" class='toolbox-content'>
					<div>
						<div>LOGIN STATUS</div>
						<div class='item'>WP CMS logged in? - <?php var_dump(wp_get_current_user()->user_login); ?></div>
						<div class='item'>WP CMS role - <?= implode(', ', wp_get_current_user()->roles) ?></div>
						<div class='item'>Member home logged in? - <?php var_dump($_SESSION['library_barcode']); ?></div>
					</div><br/>
					<div>
					    <div class='heading'>SESSION VALUES</div>
					    <?php foreach ($_SESSION as $key => $value): ?>
					        <div class='item'><?= htmlspecialchars($key) ?> - <?= is_array($value) ? htmlspecialchars(print_r($value, true)) : htmlspecialchars($value) ?></div>
					    <?php endforeach; ?>
					</div><br/>
					<?php if ($_SESSION['is_competition__fields']) : ?>
						<div>
						    <div class='heading'>COMPETITION SESSION VALUES</div>
						    <pre><?php print_r($_SESSION['is_competition__fields']); ?></pre>
						</div><br/>
					<?php endif; ?>
				    <div>
				    	<div class='heading'>STREAKS DATA</div>
				        <div class='item'>App Usage streak</span> - <span id="daysCount">0</div>
				        <div class='item'>Books streak</span> - <span id="bookCount">0</div>
				        <div class='item'>Activities streak</span> - <span id="activityCount">0</div>
						<div class='item'>Languages streak</span> - <span id="languageCount">0</div>
				    </div>
					<!-- add more debugging code here -->
				</div>
			</div>
		<?php endif; ?>

	</div>

	<div id='global-home-url'><?php echo home_url(); ?></div>

</div>

<div class='cookie-floating-footer' role="region" aria-label="Cookie Terms">
	<?php if (get_field('cookie_policy', 'option')) : ?>
		<?php echo apply_filters('the_content', get_field('cookie_policy', 'option')); ?>
	<?php endif; ?>
	<a href='javascript: void(0);' id='accept-cookie'>Accept & Close</a>
</div>

<?php wp_footer(); ?>
</body>
</html>