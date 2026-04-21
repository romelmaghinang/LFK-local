<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <link rel="icon" type="image/png" href="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/favicon.png">
    <?php wp_head(); ?>

	<!-- Google tag (gtag.js) -->
	<script async src="https://www.googletagmanager.com/gtag/js?id=G-DJD1RQHT8N"></script>
	<script>
	  window.dataLayer = window.dataLayer || [];
	  function gtag(){dataLayer.push(arguments);}
	  gtag('js', new Date());
	  gtag('config', 'G-DJD1RQHT8N');
	</script>
</head>

<body 
	<?php body_class(); ?> 
	style='background-color: <?php echo l4k_getBGColor(); ?>' 
	data-owner-id="<?php echo (is_singular('book') || is_singular('playlist') || is_singular('activity')) ? $_SESSION['ownerID'] : ''; ?>"
	data-book-id="<?php echo (is_singular('book')) ? get_the_ID() : ''; ?>"
	data-book-title="<?php echo (is_singular('book')) ? get_the_title(get_the_ID()) : ''; ?>"
	data-book-lang="<?php echo (is_singular('book')) ? get_field('language', get_the_ID()) : ''; ?>"
	data-custom-bg="<?php echo l4k_getBGColor(); ?>">

<?php if (have_rows('sticky_bar_announcement', 'option')) : ?>
    <div class='announcement-bar' role="region" aria-label="Announcement">
        <div class='close'><i class="lni lni-xmark"></i></div>
        <div class='elements'>
	        <?php while (have_rows('sticky_bar_announcement', 'option')) : the_row(); ?>
	        	<span>
	        		<?php if (get_sub_field('type') == 'text') : ?>
	        			<?php if (get_sub_field('link')) : ?>
							<a 	href='<?php echo get_sub_field('link'); ?>'
								aria-label="Announcement Link" 
								tabindex="-1">
	        					<?php echo get_sub_field('value'); ?>
	        				</a>
        				<?php else : ?>
        					<?php echo get_sub_field('value'); ?>
        				<?php endif; ?>	
	        		<?php else : ?>
	        			<?php if (get_sub_field('link')) : ?>
							<a 	href='<?php echo get_sub_field('link'); ?>'
								aria-label="Announcement Link" 
								tabindex="-1">
	        					<img src='<?php echo l4k_normalizeImageUrl(get_sub_field('value')); ?>' role="presentation" alt="" />
	        				</a>
        				<?php else : ?>
    						<img src='<?php echo l4k_normalizeImageUrl(get_sub_field('value')); ?>' role="presentation" alt="" />
	        			<?php endif; ?>
	        		<?php endif; ?>
				</span>
	        <?php endwhile; ?>
	    </div>

		<?php if (get_field('sticky_bar_second_row', 'options') == 'show') : ?>
			<?php if (have_rows('sticky_bar_announcement_row_2', 'option')) : ?>
			    <div class='elements row-2'>
			        <?php while (have_rows('sticky_bar_announcement_row_2', 'option')) : the_row(); ?>
			        	<span>
			        		<?php if (get_sub_field('type') == 'text') : ?>
			        			<?php if (get_sub_field('link')) : ?>
									<a 	href='<?php echo get_sub_field('link'); ?>'
										aria-label="Announcement Link" 
										tabindex="-1">
			        					<?php echo get_sub_field('value'); ?>
									</a>
		        				<?php else : ?>
		        					<?php echo get_sub_field('value'); ?>
		        				<?php endif; ?>	
			        		<?php else : ?>
			        			<?php if (get_sub_field('link')) : ?>
									<a 	href='<?php echo get_sub_field('link'); ?>'
										aria-label="Announcement Link" 
										tabindex="-1">
			        					<img src='<?php echo get_sub_field('value'); ?>' role="presentation" alt="" />
			        				</a>
		        				<?php else : ?>
		    						<img src='<?php echo get_sub_field('value'); ?>' role="presentation" alt="" />
			        			<?php endif; ?>
			        		<?php endif; ?>
						</span>
			        <?php endwhile; ?>
			    </div>
		   	<?php endif; ?>
	   	<?php endif; ?>
    </div>
<?php endif; ?>

<header class="site-header">
    <div class='_maxwrap'>

    	<div class='logo-menu-wrap'>	
	        <h1 class="site-logo">
	            <a href="<?php echo (!empty($_SESSION['library_barcode'])) ? esc_url(home_url('/member-home')) : esc_url(home_url('/')); ?>" aria-label="LOTE4Kids - Home">
	                <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/logo-main.svg" alt="LOTE4Kids Logo">
	            </a>
	        </h1>

	        <nav class="main-nav desktop" aria-label="Desktop navigation">
	            <?php
	            wp_nav_menu([
	                'menu'       => 'Main Menu',
	                'container'  => false,
	                'menu_class' => 'menu'
	            ]);
	            ?>
	        </nav>

			<nav class="main-nav mobile" aria-label="Mobile navigation">
	        	<div class='mobile-menu-trigger'>
	        		<img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/icon-mobile-menu.png" alt='Mobile Menu' />
	        	</div>
	            <?php
	            wp_nav_menu([
	                'menu'       => 'Main Menu',
	                'container'  => false,
	                'menu_class' => 'menu'
	            ]);
	            ?>
	        </nav>
    	</div>

        <div class='mid-header'>
	        <?php $links = l4k_breadcrumbs(); ?>
	        <?php if ($links): ?>
	            <ul class='breadcrumb'>
	                <?php foreach ($links as $link => $l): ?>
	                    <li>
	                        <a href='<?php echo $l['permalink']; ?>'><?php echo $l['label']; ?></a> 
	                        <?php if ($l !== end($links)): ?> <span>></span><?php endif; ?>
	                    </li>
	                <?php endforeach; ?>        
	            </ul>
	        <?php endif; ?>
        </div>

    </div>
</header>

<main class="site-content">