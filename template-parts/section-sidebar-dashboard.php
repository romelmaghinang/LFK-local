<?php 
$staffPage 	= get_page_by_path('staff-access');
$user 		= wp_get_current_user(); 
$isTeacher 	= (isset($_SESSION['is_teacher']) && $_SESSION['is_teacher'] == '1') ? true : false;
?>

<div class='sidebar dashboard'>	

	<?php if (!$isTeacher) : ?>

		<div>

			<div class='logged-in-details'>
				<div class='label'>Staff Details</div>
				<div class='details'>
					<div class='item'>
						<i class="lni lni-user-4"></i> 
						<?php echo $user->user_login; ?>
					</div>
					<div class='item'>
						<i class="lni lni-buildings-1"></i>
						<?php if (current_user_can('administrator')): ?>
							Administrator
						<?php else: ?>
							<?php echo get_the_title(get_field('library', 'user_'.$user->ID)); ?>
						<?php endif; ?>
					</div>
					<div class='item'>
						<a href='<?php echo home_url('/?member-and-staff-logout'); ?>'>
							<i class="lni lni-power-button"></i> 
							Logout
						</a>
					</div>
				</div>
			</div>

			<h3>Key Links</h3>

			<ul class='links'>
				
				<li>
					<a class='_btn' href='javascript:void(0);' onclick='printDashboardReport()'>
						Export PDF Report
					</a>
				</li>

				<li>
					<a class='_btn' target='_blank' href='<?php echo get_field('promotional_collateral_link', $staffPage->ID); ?>'>
						<?php echo get_field('promotional_collateral_label', $staffPage->ID); ?>
					</a>
				</li>

				<li>
					<a class='_btn btn-marc' href='javascript: void(0);'>
						<?php echo get_field('marc_records_label', $staffPage->ID); ?>
					</a>
					<div class='embed__overlay marc'>
						<div class='embed__wrap'>
							<div class='embed__wrap__inner'>
								<div class='embed__decoration'><img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/blowing-leaf1.png" alt="leaf"></div>
								<div class='embed__decoration-2'><img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/blowing-leaf3.png" alt="leaf"></div>
								<a class='embed__close'><i class="lni lni-xmark"></i></a>
								<div class='embed__title'>
									<?php echo get_field('marc_records_popup_header', $staffPage->ID); ?>
								</div>
								<div class='embed__content'>
									<?php if(have_rows('marc_records_records', $staffPage->ID)): ?>
										<table class='marc-records-table'>
											<tr>
												<th>Release Title</th>
												<th>Release Date</th>
												<th>Link for download</th>
											</tr>
										    <?php while(have_rows('marc_records_records', $staffPage->ID)): the_row(); ?>
												<tr>
													<td><?php the_sub_field('release_title'); ?></td>
													<td><?php the_sub_field('release_date'); ?></td>
													<td align='center'><a href='<?php the_sub_field('download_link'); ?>'>Download</a></td>
												</tr>
										    <?php endwhile; ?>
										</table>
									<?php endif; ?>
								</div>
							</div>
						</div>
					</div>
				</li>
				
				<li>
					<a class='_btn' target='_blank'href='<?php echo get_field('portal_user_guide_link', $staffPage->ID); ?>'>
						<?php echo get_field('portal_user_guide_label', $staffPage->ID); ?>
					</a>
				</li>
				
				<li>
					<a class='_btn' target='_blank'href='<?php echo get_field('firewall_whitelisting_guide_link', $staffPage->ID); ?>'>
						<?php echo get_field('firewall_whitelisting_guide_label', $staffPage->ID); ?>
					</a>
				</li>
				
				<li>
					<a class='_btn' target='_blank'href='<?php echo get_field('content_catalogue_link', $staffPage->ID); ?>'>
						<?php echo get_field('content_catalogue_label', $staffPage->ID); ?>
					</a>
				</li>
				
				<li>
					<a class='_btn btn-breakdown' href='javascript: void(0);'>
						<?php echo get_field('language_breakdown_label', $staffPage->ID); ?>
					</a>
					<?php get_template_part('template-parts/section', 'language-breakdown'); ?>
				</li>
				
				<li>
					<a class='_btn' target='_blank'href='<?php echo get_field('subscription_terms_link', $staffPage->ID); ?>'>
						<?php echo get_field('subscription_terms_label', $staffPage->ID); ?>
					</a>
				</li> 

			</ul>

		</div>

		<div class='side-content'>
			<h4>What would you like to see on <em>LOTE4Kids</em></h4>
			<?php echo do_shortcode('[wpforms id="'.get_field('feedback_form_staff', $staffPage->ID).'" title="false"]'); ?>
		</div>

	<?php endif;?>

</div>