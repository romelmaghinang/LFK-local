<?php 
/* Template Name: Staff Access Page Template */

$isForgot = (isset($_GET['forgot'])) ? true : false;

// if staff/admin is logged in, redirect to library's dashboard
// do NOT redirect if accessing via /staff-access/forgot
if (!$isForgot) { l4k_checkStaffLoggedIn(); }

get_header();
?>

<div class='main-mid'>
	<div class='_maxwrap'>

		<?php if ($isForgot) : ?>

			<div class='staff-wrap'>

				<div class='heading'>
					<img src='<?php echo get_stylesheet_directory_uri(); ?>/assets/img/logo-lote-only.png' />
					<div class='heading__text'>
						<h2>Promotions & reporting portal</h2>
						<p>Library staff can login here to view usage reports and access promotional materials</p>
					</div>
				</div>

				<div class='staff__reminder'>Lost your password? </div>

				<?php echo do_shortcode('[l4k_staffForgotPassword]'); ?>

				<div class='staff__forgot'>
					<div>Please enter your email.</div>
					<div>The password reset link will be <br/> provided in your email.</div>
				</div>
				
			</div>

		<?php else : ?>

			<div class='blowing-leaves'>
			    <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/blowing-leaf1.png" class="blowing-leaf" alt="leaf">
			    <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/blowing-leaf2.png" class="blowing-leaf" alt="leaf">
			    <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/blowing-leaf3.png" class="blowing-leaf" alt="leaf">
			</div>

			<div class='staff-wrap'>

				<div class='heading'>
					<img src='<?php echo get_stylesheet_directory_uri(); ?>/assets/img/logo-lote-only.png' />
					<div class='heading__text'>
						<h2>Promotions & reporting portal</h2>
						<p>Library staff can login here to view usage reports and access promotional materials</p>
					</div>
				</div>

				<div class='staff__reminder'>
					<div>Please enter your Username and Password</div>
					<div>(including all characters)</div>
				</div>

				<?php echo do_shortcode('[l4k_staffLogin]'); ?>

				<div class='staff__forgot'>
					<a href='<?php echo home_url(); ?>/staff-access/?forgot'>Lost your password?</a>
				</div>
				
			</div>

		<?php endif; ?>

	</div>
</div>

<?php get_footer(); ?>