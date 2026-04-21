<?php /* Template Name: Contact Us Page Template */ ?>

<?php get_header(); ?>

<div class='main-mid'>
	<div class='_maxwrap'>
	
		<div class="contact-wrap">

			<div class="column main-text">
				<?php echo apply_filters('the_content', get_field('main_content')); ?>

	            <div class="_share contact-share">
		        	<a 	href='<?php echo get_field('social_media_links_facebook_url'); ?>' 
		        		aria-label='Facebook Link'
		        		target='_blank' 
		        		class='facebook'>
		        		<svg style="display:block;" focusable="false" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 32 32"><path fill="#fff" d="M28 16c0-6.627-5.373-12-12-12S4 9.373 4 16c0 5.628 3.875 10.35 9.101 11.647v-7.98h-2.474V16H13.1v-1.58c0-4.085 1.849-5.978 5.859-5.978.76 0 2.072.15 2.608.298v3.325c-.283-.03-.775-.045-1.386-.045-1.967 0-2.728.745-2.728 2.683V16h3.92l-.673 3.667h-3.247v8.245C23.395 27.195 28 22.135 28 16Z"></path></svg>
		        	</a>

					<a 	href='<?php echo get_field('social_media_links_linkedin_url'); ?>' 
						aria-label='Linkedin Link'
						target='_blank' 
						class='linkedin'>
		        		<svg style="display:block;" focusable="false" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 32 32"><path d="M6.227 12.61h4.19v13.48h-4.19V12.61zm2.095-6.7a2.43 2.43 0 0 1 0 4.86c-1.344 0-2.428-1.09-2.428-2.43s1.084-2.43 2.428-2.43m4.72 6.7h4.02v1.84h.058c.56-1.058 1.927-2.176 3.965-2.176 4.238 0 5.02 2.792 5.02 6.42v7.395h-4.183v-6.56c0-1.564-.03-3.574-2.178-3.574-2.18 0-2.514 1.7-2.514 3.46v6.668h-4.187V12.61z" fill="#fff"></path></svg>
		        	</a>

					<a 	href='<?php echo get_field('social_media_links_email'); ?>' 
						aria-label="Mail Link"
						target='_blank' 
						class='mail'>
		        		<svg style="display:block;" focusable="false" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="-.75 -.5 36 36"><path d="M 5.5 11 h 23 v 1 l -11 6 l -11 -6 v -1 m 0 2 l 11 6 l 11 -6 v 11 h -22 v -11" stroke-width="1" fill="#fff"></path></svg>
		        	</a>
		        </div>
			</div>

			<div class="column main-image">
				
				<div class='form'>
					<?php echo do_shortcode('[wpforms id="'.get_field('contact_form').'" title="false"]'); ?>
				</div>

			</div>

		</div>
		
	</div>
</div>

<?php get_footer(); ?>