<?php 
/* Template Name: Find Your Library Page Template */

l4k_checkMemberLoggedIn(); // check if member is logged in
get_header();
$libArr = l4k_getLibraries(); 
?>

<div class='main-mid'>
<div class='_maxwrap medium'>

	<div class='lib-wrap'>
		<div class='lib-wrap__inner'>

			<div class='lib__head'>
				<?php echo get_field('main_content'); ?>
			</div>

			<div class='lib__search'>
				<div class='lib__search-wrap'>
					<input 	autocomplete='off'
							id='lib-search' 
							class='search-txt' 
							type='text' 
							placeholder='Start typing here' />
					<ul id="suggestions" translate="no" class="notranslate"></ul>
				</div>
				<a class='search-btn _btn' href=''>Go</a>
			</div>
			
			<div class='lib__text'>
				Library or school not listed? Tap below.
			</div>

		</div>
	</div>

</div>
</div>

<ul id='lib-list' class='library-list notranslate' translate="no">
	
	<?php if ($libArr) : ?>
		<?php foreach ($libArr as $lib => $l) : ?>
			<li data-url='<?php echo ($l['predefined_url']) ? $l['predefined_url'] : $l['lib_permalink']; ?>'><?php echo $l['title']; ?></li>
		<?php endforeach; ?>
	<?php endif; ?>

</ul>

<?php get_footer(); ?>