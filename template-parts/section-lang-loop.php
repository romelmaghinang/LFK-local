<?php 
$langArr 			= l4k_getLanguages($getComingSoon=false); 
$langComingSoonArr 	= l4k_getLanguages($getComingSoon=true); 
?>

<div class='filter'>
					
	<div class='filter__item sort'>
		<strong>Sort</strong>
		<a href="#" class='sort-latest'>Latest Release</a>
		<a href="#" class='sort-popular'>Most Popular</a>
		<a href="#" class='sort-az active'>Sort A-Z</a>
		<a href="#" class='sort-za'>Sort Z-A</a> 
	</div>

	<div class='filter__item search'>
		<input type='text' placeholder="Search" />
	</div>
	
</div>

<div class='lang-wrap'>

	<?php if ($langArr): ?>
		<?php foreach ($langArr as $lang => $l): ?>

			<a 	class='lang-item' 
				href='<?php echo $l['lang_permalink']; ?>'
				data-published='<?php echo $l['date_published']; ?>'
				data-latest-book='<?php echo $l['book_latest']; ?>'
				data-latest-book-date='<?php echo $l['book_latest_date']; ?>'
				data-total-views='<?php echo $l['total_views']; ?>'
				data-native-name='<?php echo $l['native_label']; ?>'
				data-english-name='<?php echo $l['title']; ?>'>
				<img alt='<?php echo $l['title']; ?>' src='<?php echo $l['flag_url']; ?>' />

				<h4><?php echo $l['native_label']; ?></h4>
				<h5><?php echo $l['title']; ?></h5>
				<?php /*
				<?php if (!l4k_compareStringsNoSpecial($l['native_label'], $l['title'])) : ?>
					<h5><?php echo $l['title']; ?></h5>
				<?php endif; ?> 
				*/ ?>
			</a>

		<?php endforeach; ?>
	<?php endif; ?>

</div>

<div id="no-results">No results found</div>

<span class='coming-soon-label'>Coming Soon</span>

<div class='lang-coming-wrap'>

	<?php if ($langComingSoonArr): ?>
		<?php foreach ($langComingSoonArr as $lang => $l): ?>

			<a 	class='lang-item' 
				href='javascript: void(0);'
				data-published='<?php echo $l['date_published']; ?>'
				data-english-name='<?php echo $l['title']; ?>'>
				<img alt='<?php echo $l['title']; ?>' src='<?php echo $l['flag_url']; ?>' />
				<h4><?php echo $l['native_label']; ?></h4>
				<h5><?php echo $l['title']; ?></h5>
			</a>

		<?php endforeach; ?>
	<?php endif; ?>

</div>