<?php 
/* Template Name: Member Home Page Template */

l4k_checkMemberLoggedIn(); // check if member is logged in
get_header(); // display header
$metaArr = l4k_getMemberHomeMeta(); 
$readingPackArr = l4k_getReadingPacks();
?>

<div class='main-mid'>
<div class='_maxwrap'>

	<div class='member-home-wrap'>

		<div class='mainbar'>

			<?php if ($metaArr): ?>

				<div class='meta-wrap recent desktop'>

					<div class='heading recently-viewed'><span>Recently Viewed</span></div>
					<span><!-- spacer --></span>

					<?php if (array_key_exists('playlist', $metaArr)) : ?>
						<div class='heading video-playlist'><span><p class='long'>Quick Playlist</p><p class='short'>Playlist</p></span></div>
						<div class='heading similar-books'><span>Similar Books</span></div>
					<?php else : ?>
						<div class='heading similar-books'><span>Similar Books</span></div>
						<span><!-- spacer --></span>
					<?php endif; ?>

					<span><!-- spacer --></span>
					<span><!-- spacer --></span>

					<?php foreach ($metaArr as $meta => $m): ?>
						<a 	class='lang-item <?php echo $m['type']; ?>' 
							href='<?php echo $m['permalink']; ?>'>
							<img src='<?php echo $m['img_url']; ?>' alt='<?php echo $m['label']; ?>' />
							<h4><?php echo $m['label']; ?></h4> 
							<h5><?php echo $m['label_english']; ?></h5> 
						</a>
					<?php endforeach; ?>

				</div>

				<div class='meta-wrap recent mobile'>

					<div class='heading similar-books'><span>Recently Viewed</span></div>
					<span><!-- spacer --></span>
					<div class='heading similar-books'><span>Playlist</span></div>

					<?php foreach (array_slice($metaArr, 0, 3) as $meta => $m): ?>
						<a 	class='lang-item <?php echo $m['type']; ?>' 
							href='<?php echo $m['permalink']; ?>'>
							<img src='<?php echo $m['img_url']; ?>' alt='<?php echo $m['label_english']; ?>' />
							<h4><?php echo $m['label']; ?></h4> 
							<h5><?php echo $m['label_english']; ?></h5> 
						</a>
					<?php endforeach; ?>

					<div class='heading similar-books'><span>Similar Books</span></div>
					<span><!-- spacer --></span>
					<span><!-- spacer --></span>

					<?php foreach (array_slice($metaArr, 3, 3) as $meta => $m): ?>
						<a 	class='lang-item <?php echo $m['type']; ?>' 
							href='<?php echo $m['permalink']; ?>'>
							<img src='<?php echo $m['img_url']; ?>' alt='<?php echo $m['label_english']; ?>' />
							<h4><?php echo $m['label']; ?></h4> 
							<h5><?php echo $m['label_english']; ?></h5> 
						</a>
					<?php endforeach; ?>

				</div>
			
			<?php endif; ?>
				
			<?php if (!empty($readingPackArr)) : ?>

				<div class='meta-wrap reading-pack'>

					<div class='heading recently-viewed'><span><?php echo $_SESSION['library_name']; ?> Reading Packs</span></div>
					<span><!-- spacer --></span>
					<span><!-- spacer --></span>
					<span><!-- spacer --></span>
					<span><!-- spacer --></span>
					<span><!-- spacer --></span>

					<?php foreach ($readingPackArr as $langDetails) : ?>
						<a class='lang-item'
						href='<?php echo $langDetails['lang_permalink']; ?>reading-pack'>
							<img src='<?php echo $langDetails['flag_url']; ?>' />
							<h4><?php echo $langDetails['native_label']; ?></h4>
							<h5><?php echo $langDetails['title']; ?></h5>
						</a>
					<?php endforeach; ?>

				</div>

			<?php endif; ?>

			<?php get_template_part('template-parts/section', 'lang-loop'); ?>

		</div>

		<?php get_template_part('template-parts/section', 'sidebar-main'); ?>

	</div>

</div>
</div>

<?php get_footer(); ?>