<?php 
// this path /libraries/au-demo is PUBLIC 
// this path /libraries/au-demo/trial is PUBLIC 
// this path /libraries/au-demo/competition is PUBLIC 
// this path /libraries/au-demo/webinar is PUBLIC 
// this path /libraries/au-demo/staff-training is PUBLIC 
// this path /libraries/au-demo/dashboard is PRIVATE

$uri 			= $_SERVER['REQUEST_URI'];
$isDashboard 	= (strpos($uri, '/dashboard') !== false);
$isTrial 		= (strpos($uri, '/trial') !== false);
$isCompetition 	= (strpos($uri, '/competition') !== false);
$isTraining 	= (strpos($uri, '/webinar') !== false);
$isReview 		= (strpos($uri, '/staff-training') !== false);
$staffPage 		= get_page_by_path('staff-access');

$isTeacher = (isset($_SESSION['is_teacher']) && ($_SESSION['is_teacher'] == '1')) ? true : false;
if ($isTeacher) { $teacherDetails = l4k_getTeacherDetails($_SESSION['library_barcode']); }

if (!$isTrial && !$isCompetition && !$isTraining && !$isReview && !$isDashboard) { 
	if (isset($_GET['trial-login'])) { l4k_trialFormAutoLogin(); } // if coming from trial or competition, auto generate barcode and login
	if (isset($_GET['teacher-login'])) { l4k_trialFormAutoLogin(true); } // if coming from teacher subscription, login using teacher's barcode
	l4k_checkIpAutoLogin(); // if IP is whitelisted -> create session and login automatically 
	l4k_checkMemberLoggedIn(); // check if member is logged in
} 

if ($isDashboard) {
	l4k_checkMemberLoggedIn(); // check if member is logged in
}

// if on /libraries/au-demo/trial but NO trial form was set
if (is_singular('library') && $isTrial && !get_field('trial_form')) {
	wp_safe_redirect(home_url().'/member-home'); exit; 
}

// if on /libraries/au-demo/competition but NO competition form was set
if (is_singular('library') && $isCompetition && !get_field('competition_form')) {
	wp_safe_redirect(home_url().'/member-home'); exit; 
}

// if on /libraries/au-demo/webinar but NO training form was set
if (is_singular('library') && $isTraining && !get_field('training_form')) {
	wp_safe_redirect(home_url().'/member-home'); exit; 
}

// if on /libraries/au-demo/staff-training but NO review content is set
if (is_singular('library') && $isReview && !get_field('review_content')) {
	wp_safe_redirect(home_url().'/member-home'); exit; 
}

get_header(); // display header 
?>

<div class='main-mid'>
	<div class='_maxwrap <?php echo ($isTeacher) ? 'small' : 'medium'; ?>'>

		<?php if ($isDashboard) : ?>

			<?php if ($isTeacher) : ?>

				<div class='teacher-wrap'>
					<div class='teacher-details'>
						<?php
						$user_id        = get_current_user_id();
						$avatar_att_id  = get_user_meta($user_id, 'l4k_avatar_attachment_id', true);
						$avatar_url     = $avatar_att_id
						    ? wp_get_attachment_image_url($avatar_att_id, 'thumbnail')
						    : get_stylesheet_directory_uri() . '/assets/img/logo-lote-avatar.png';
						?>
						<div class='teacher-avatar__wrap'>
						    <div class='teacher-avatar' id='teacher-avatar-trigger'>
						        <a class='edit' href='#'><i class="lni lni-file-pencil"></i><span>Ideal size: 230x230</span></a>
						        <img id='teacher-avatar-img'
						             src="<?php echo esc_url($avatar_url); ?>"
						             alt="Teacher Avatar">
						    </div>

						    <input type='file' id='teacher-avatar-input' accept='image/*' style='display:none;'>
						    <span id='l4k-avatar-nonce' data-nonce='<?php echo wp_create_nonce("l4k_avatar_nonce"); ?>' style='display:none;'></span>

						    <div class='actions'>
								<a class='_btn' target='_blank'href='<?php echo get_field('content_catalogue_link', $staffPage->ID); ?>'><?php echo get_field('content_catalogue_label', $staffPage->ID); ?></a>
								<a class='_btn btn-breakdown'>Language Breakdown</a>
								<a class='_btn'>Refer a teacher / school</a>
								<?php if ($teacherDetails['newsletter_subscription']) : ?>
									<a class='_btn'>Unsubscribe to newsletter</a>
								<?php else : ?>
									<a class='_btn'>Subscribe to newsletter</a>
								<?php endif; ?>
								<a class='_btn' href='<?php echo home_url('?member-and-staff-logout'); ?>'>Logout</a>
							</div>
						</div>
						<div class='teacher-info__wrap'>
							<div class='name'>
							    <span id='teacher-name-display'><?php echo esc_html($teacherDetails['wp_name']); ?></span>
							    <input id='teacher-name-input' type='text' value='<?php echo esc_attr($teacherDetails['wp_name']); ?>' style='display:none;' />
							    <a href='#' id='teacher-name-edit'>Edit</a>
							    <a href='#' id='teacher-name-save' style='display:none;'>Save</a><span id='teacher-name-divider' style='display: none;'> | </span><a href='#' id='teacher-name-cancel' style='display:none;'>Cancel</a>
							    <span id='l4k-name-nonce' data-nonce='<?php echo wp_create_nonce("l4k_name_nonce"); ?>' style='display:none;'></span>
							</div>
							<div>
								<p><span>Member since <?php echo date('F j, Y', strtotime($teacherDetails['time'])); ?></span></p>
								<p><span>Subscription expires on <?php echo $teacherDetails['expiration_date_nicename']; ?></span> <a href=''>Renew</a> | <a href=''>Cancel Subscription</a></p>
							</div>
							<div class='information'>
								<div class='btn-edit__wrap'>
								    <a class='btn-edit' href='#' id='teacher-info-edit'>Edit</a>
								    <a class='btn-edit' href='#' id='teacher-info-save' style='display:none;'>Save</a>
								    <span id='teacher-info-divider' style='display:none;'> | </span>
								    <a class='btn-edit' href='#' id='teacher-info-cancel' style='display:none;'>Cancel</a>
								</div>
							    <span id='l4k-info-nonce' data-nonce='<?php echo wp_create_nonce("l4k_info_nonce"); ?>' style='display:none;'></span>
								<p>
								    <span class='info-label'>Email:</span>
								    <span id='teacher-email-display'><?php echo esc_html($teacherDetails['wp_email']); ?></span>
								    <input id='teacher-email-input' type='email' value='<?php echo esc_attr($teacherDetails['wp_email']); ?>' style='display:none;' />
								</p>
								<p>
								    <span class='info-label'>School:</span>
								    <span id='teacher-school-display'><?php echo esc_html($teacherDetails['school']); ?></span>
								    <input id='teacher-school-input' type='text' value='<?php echo esc_attr($teacherDetails['school']); ?>' style='display:none;' />
								</p>
								<p>
								    <span class='info-label'>Country:</span>
								    <span id='teacher-country-display'><?php echo esc_html($teacherDetails['country']); ?></span>
								    <input id='teacher-country-input' type='text' value='<?php echo esc_attr($teacherDetails['country']); ?>' style='display:none;' />
								</p>
								<p>
								    <span class='info-label'>State:</span>
								    <span id='teacher-state-display'><?php echo esc_html($teacherDetails['state']); ?></span>
								    <input id='teacher-state-input' type='text' value='<?php echo esc_attr($teacherDetails['state']); ?>' style='display:none;' />
								</p>
								<p>
								    <span class='info-label'>Phone:</span>
								    <span id='teacher-phone-display'><?php echo esc_html($teacherDetails['phone']); ?></span>
								    <input id='teacher-phone-input' type='text' value='<?php echo esc_attr($teacherDetails['phone']); ?>' style='display:none;' />
								</p>
								<p>
								    <span class='info-label'>Currency</span>
								    <span id='teacher-currency-display'><?php echo esc_html($teacherDetails['currency']); ?></span>
								    <select id='teacher-currency-input' style='display:none;'>
								        <option value='USD' <?php selected($teacherDetails['currency'], 'USD'); ?>>USD</option>
								        <option value='AUD' <?php selected($teacherDetails['currency'], 'AUD'); ?>>AUD</option>
								        <option value='Euro' <?php selected($teacherDetails['currency'], 'Euro'); ?>>Euro</option>
								    </select>
								</p>
								<p>
								    <span class='info-label'>Payment Method:</span>
								    Credit Card
								</p>
							</div>
							<div class='teacher-form'>
								<?php echo do_shortcode('[wpforms id="'.get_field('feedback_form_teacher', $staffPage->ID).'" title="false"]'); ?>
							</div>
						</div>
					</div>
				</div>

				<?php get_template_part('template-parts/section', 'language-breakdown'); ?>

			<?php else : ?>

				<div class='dash-wrap'>
					<div class='mainbar'>
						<div class='report__wrap'>
							<iframe title='Report Frame' src='<?php echo get_field('dashboard_iframe_source'); ?>'></iframe>
						</div>
					</div>
					<?php get_template_part('template-parts/section', 'sidebar-dashboard'); ?>
				</div>

			<?php endif; ?>

		<?php elseif ($isTrial) : ?>

			<div class='trial-wrap'>
				
				<div class='intro-section'>
					<div class='main-text'><?php echo nl2br(get_field('trial_main_text')); ?></div>
					<div class='main-image'><img alt='Main Image' src='<?php echo l4k_normalizeImageUrl(get_field('trial_main_image')); ?>' /></div>
				</div>

				<div class='form-video-section'>
					<div class='form-wrap'>
						<div class='form-label'><?php echo get_field('trial_form_label'); ?></div>
						<div class='form'><?php echo do_shortcode('[wpforms id="'.get_field('trial_form').'" title="false"]'); ?></div>
					</div>
					<div class='video-wrap'>
						<div class='video-label'><?php echo get_field('trial_video_label'); ?></div>
						<div class='video'>
							<?php $videoURL = l4k_parseVimeoUrl(get_field('trial_video')); ?>
							<iframe 
								src="https://player.vimeo.com/video/<?php echo $videoURL[0]; ?>?title=0&amp;byline=0&amp;portrait=0&amp;h=<?php echo $videoURL[1]; ?>&amp;app_id=122963" 
								frameborder="0" 
								title='Video Frame'
								allow="autoplay; fullscreen; picture-in-picture" 
								allowfullscreen>
							</iframe>
						</div>
						<div class='video-buttons'>
							<div class='video-buttons-label'><?php echo get_field('trial_buttons_text'); ?></div>
							<?php if (have_rows('trial_buttons')) : ?>	
								<?php while (have_rows('trial_buttons')) : the_row(); ?>
									<a href="<?php echo get_sub_field('url'); ?>" class='_btn' target='_blank'>
										<?php echo get_sub_field('text'); ?>
									</a>
								<?php endwhile; ?>
							<?php endif; ?>
						</div>
					</div>
				</div>

			</div>

		<?php elseif ($isCompetition) : ?>

			<div class='competition-wrap'>

				<div class='left-image'>
					<img alt='Main Image' src='<?php echo l4k_normalizeImageUrl(get_field('competition_left_image')); ?>' />
				</div>

				<div class='main-content'>
					<div class='text competition-text'><?php echo nl2br(get_field('competition_main_text')); ?></div>
					<div class='banners'> <?php // for mobile only ?>
						<img src='<?php echo l4k_normalizeImageUrl(get_field('competition_left_image')); ?>' />
						<img src='<?php echo l4k_normalizeImageUrl(get_field('competition_right_image')); ?>' />
					</div>
					<div class='form'><?php echo do_shortcode('[wpforms id="'.get_field('competition_form').'" title="false"]'); ?></div>
				</div>

				<div class='right-image'>
					<img alt='Secondary Image' src='<?php echo l4k_normalizeImageUrl(get_field('competition_right_image')); ?>' />
				</div>

			</div>

		<?php elseif ($isTraining) : ?>

			<div class='training-wrap'>
				
				<div class='intro-section'>
					<div class='main-text training-text'><?php echo nl2br(get_field('training_main_text')); ?></div>
				</div>

				<div class='form-video-section'>
					<div class='form-wrap'>
						<div class='form-label'><?php echo get_field('training_form_label'); ?></div>
						<div class='form'><?php echo do_shortcode('[wpforms id="'.get_field('training_form').'" title="false"]'); ?></div>
					</div>
					<div class='video-wrap'>
						<div class='image-label'><?php echo get_field('trial_image_label'); ?></div>
						<div class='image'>
							<img alt='Training Image' src='<?php echo get_field('training_main_image'); ?>' />
						</div>
					</div>
				</div>

			</div>

		<?php elseif ($isReview) : ?>

			<div class='review-wrap'>
				
				<div class='intro-section'>
					<div class='main-text'><?php echo nl2br(get_field('review_content')); ?></div>
				</div>

				<div class='video-wrap'>
					<?php if (have_rows('review_videos')) : ?>	
						<?php while (have_rows('review_videos')) : the_row(); ?>
							<div class='video'>
								<?php $videoURL = l4k_parseVimeoUrl(get_sub_field('url')); ?>
								<iframe 
									src="https://player.vimeo.com/video/<?php echo $videoURL[0]; ?>?title=0&amp;byline=0&amp;portrait=0&amp;h=<?php echo $videoURL[1]; ?>&amp;app_id=122963" 
									frameborder="0" 
									title='Video Frame'
									allow="autoplay; fullscreen; picture-in-picture" 
									allowfullscreen>
								</iframe>
							</div>
						<?php endwhile; ?>
					<?php endif; ?>
				</div>

				<div class='video-buttons'>
					<?php if (have_rows('review_buttons')) : ?>	
						<?php while (have_rows('review_buttons')) : the_row(); ?>
							<a href="<?php echo get_sub_field('url'); ?>" class='_btn' target='_blank'>
								<?php echo get_sub_field('text'); ?>
							</a>
						<?php endwhile; ?>
					<?php endif; ?>
				</div>

			</div>

		<?php else : ?>

			<div class='lib-wrap'>
				<div class='lib-wrap__inner'>

					<div class='lib__logo'><img alt='<?php echo get_the_title(); ?>' src='<?php echo l4k_normalizeImageUrl(get_field('logo_dashboard')); ?>' /></div>
					<div class='lib__header'>Storytime, in Your Language!</div>

					<?php if (isset($_SESSION['library_barcode']) && $_SESSION['library_barcode']) : ?>

						<div class='lib__loggedin-message'>
							You are currently logged in as <span><?php echo $_SESSION['library_barcode']; ?></span>
						</div>

						<div class='lib__submit'>
							<a href='<?php echo home_url('/member-home'); ?>' class='_btn' >Go to Member Home</a>
							<a href='<?php echo home_url('/?member-logout'); ?>' class='_btn' >Logout</a>
						</div>

					<?php else : ?>

						<div class='lib__reminder'>
							<div>Please enter your Library Barcode</div>
							<div>(including all characters)</div>
						</div>

						<div class='lib__barcode'>
							<input 	id="barcode" 
									type='text' 
									name='barcode'
									autocomplete='off' 
									aria-label="Barcode"
									placeholder="" />
							<input 	id="library_id" 
									type='hidden' 
									name='library_id' 
									value='<?php echo get_the_id(); ?>' />
							<div class='lib__error'>Barcode does not match</div>
						</div>

						<div class='lib__remember'>
							<input 	id="remember_me" 
									aria-label="Remember Me"
									type="checkbox"> 
									Remember me
						</div>

						<div class='lib__submit'>
							<button id="library_submit_btn" class='_btn'>
								<i class="lni lni-locked-2"></i> Secure Login
							</button>
						</div>
						
						<div class='lib__text'>
							By logging in, you agree <br/>to our <span><a href='<?php echo home_url(); ?>/terms-of-use/' target='_blank'>Terms of Use</a></span>
						</div>

					<?php endif; ?>

				</div>
			</div>

		<?php endif; ?>

	</div>
</div>

<div class='ajax-response__wrapper'></div>

<?php if ($isTrial) : ?>

	<div class='embed__overlay terms'>
		<div class='embed__wrap'>
			<div class='embed__wrap__inner'>
				<div class='embed__decoration'><img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/blowing-leaf1.png" alt="leaf"></div>
				<div class='embed__decoration-2'><img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/blowing-leaf3.png" alt="leaf"></div>
				<a class='embed__close'><i class="lni lni-xmark"></i></a>
				<div class='embed__title'><?php echo get_field('trial_terms_and_conditions_title'); ?></div>
				<div class='embed__content terms'>
					<?php echo get_field('trial_terms_and_conditions'); ?>
				</div>
			</div>
		</div>
	</div>

<?php elseif ($isCompetition) : ?>

	<div class='embed__overlay terms'>
		<div class='embed__wrap'>
			<div class='embed__wrap__inner'>
				<div class='embed__decoration'><img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/blowing-leaf1.png" alt="leaf"></div>
				<div class='embed__decoration-2'><img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/blowing-leaf3.png" alt="leaf"></div>
				<a class='embed__close'><i class="lni lni-xmark"></i></a>
				<div class='embed__title'><?php echo get_field('competition_terms_and_conditions_title'); ?></div>
				<div class='embed__content terms'>
					<?php echo get_field('competition_terms_and_conditions'); ?>
				</div>
			</div>
		</div>
	</div>

<?php endif; ?>

<?php get_footer(); ?>