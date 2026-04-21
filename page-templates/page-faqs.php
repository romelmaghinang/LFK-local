<?php /* Template Name: FAQs Page Template */ ?>

<?php get_header(); ?>

<div class='main-mid'>
<div class='_maxwrap'>
<div class="faq-wrap">

	<div class="column">
		<button id="faq-toggle-all" class="faq-toggle-all _btn">
			<i class="lni lni-chevron-up"></i>
		    <span class="label">Collapse All</span>
		</button>

		<div class='download-wrap'>
			<i class="lni lni-download-1"></i>
			<a href='<?php echo get_field('pdf_download_link'); ?>' target='_blank'>Download</a>
		</div>

		<?php if(have_rows('main_group')): ?>
		    <?php while( have_rows('main_group') ): the_row(); ?>

				<div class='group-item'>
					<h3><?php the_sub_field('main_group_label'); ?></h3>

			       <?php if( have_rows('faq') ): ?>
	                    <div class="faq-list">
	                        <?php while( have_rows('faq') ): the_row(); ?>
	                            <div class="faq-item">
	                                <h4 class="faq-title">
	                                	<i class="icon-toggle lni lni-minus"></i>
	                                	<div><?php the_sub_field('title'); ?></div>
                                	</h4>
	                                <div class="faq-content">
	                                	<div class="faq-content-inner">
                                			<?php the_sub_field('content'); ?>
                                		</div>		
                            		</div>
	                            </div>
	                        <?php endwhile; ?>
	                    </div>
	                <?php endif; ?>
				</div>

		    <?php endwhile; ?>
		<?php endif; ?>
	</div>

	<div class="column right">
		<img alt='Lekti FAQ Image' src='https://lote4kids.com/wp-content/uploads/2023/09/lote-faq.gif' />
		<h4>Do you have another question? Please get in touch using the form below.</h4>
		<?php echo do_shortcode('[wpforms id="'.get_field('faq_contact_form').'" title="false"]'); ?>
	</div>

</div>
</div>
</div>

<?php get_footer(); ?>