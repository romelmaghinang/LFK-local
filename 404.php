<?php get_header(); ?>

<div class='main-mid'>
	<div class='_maxwrap extra-small'>

		<div class='error-wrap'>
			<img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/logo-lote-avatar.png">
			<div class='error-head'>Oops!</div>
			<div class='error-sub'>Something went wrong</div>
			<div class='error-msg'>The page you are looking for is no longer here, or never existed in the first place (bummer). You can try searching for what you are looking for using the form below. If that still doesn't provide the results you are looking for, you can always start over from the <a href='<?php echo home_url(); ?>'>Home</a> page.</div>

			<?php /*
			<form role="search" method="get" id="searchform" class="filter search-form" action="<?php echo esc_url(home_url('/')); ?>">
				<div class='filter__item search'>
					<input type="text" value="" name="s" id="s" placeholder="Search and press enter" />
				</div>
				<div class='search-error'>Please enter a search term</div>
			</form>
			*/ ?>
		</div>

	</div>
</div>

<?php get_footer(); ?>