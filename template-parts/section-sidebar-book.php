<?php 
$passedBookID 		= $args['book_id'];
$passedLangID 		= $args['lang_id'];
$passedStoryID 		= $args['story_id'];
$passedLevel 		= $args['level'];
$passedBookType 	= $args['book_type'];

$activitiesQuizzes 	= l4k_getActivitiesAndQuizzes($passedBookID);
$activitiesArr     	= isset($activitiesQuizzes['activities']) ? $activitiesQuizzes['activities'] : [];
$quizzesArr        	= isset($activitiesQuizzes['quizzes'])    ? $activitiesQuizzes['quizzes']    : [];
$linkedBooks 		= l4k_getLinkedBooks($passedLangID, $passedBookID, $passedStoryID, true);
$isReadingPack 		= l4k_isReadingPackContext();	
$similarBooks 		= l4k_getSimilarBooksByLevel($passedLangID, $passedBookID, $passedLevel, $passedBookType, 4, $isReadingPack);

?>

<div class='sidebar book'>

	<!-- reward-driven learning -->

	<div class='sidebar__item reward-driven'>
		<div class='sidebar__label'>Reward-Driven Learning</div>
		<div class='sidebar__links'>
			<a href='<?php echo home_url(); ?>/avatar' title='Avatar Dress-Up'>
				<img alt='Avatar Dress Up' src='<?php echo get_stylesheet_directory_uri(); ?>/assets/img/side-avatar-dressup.webp' />
			</a>

			<a href='javascript: void(0);' title='Learning Dashboard' class='ld__btn' data-barcode='<?php echo $_SESSION['library_barcode']; ?>'>
				<img alt='Learning Dashboard' src='<?php echo get_stylesheet_directory_uri(); ?>/assets/img/side-learning-dashboard.webp' />
			</a>

			<?php if ($quizzesArr) : ?>
				<?php if (count($quizzesArr) > 1) : ?><a href='#' class='filler'></a><?php endif; ?>
				<?php foreach ($quizzesArr as $index => $q) : ?>
					<a 	href='javascript: void(0);' 
						title='<?php echo $q['title']; ?>' 
						class='quiz__btn'
						data-activity-title='<?php echo get_the_title($passedBookID); ?>' 
						data-book-id='<?php echo $passedBookID; ?>'
						data-book-quiz='quiz-<?php echo $passedBookID; ?>'
						data-book-title='<?php echo get_the_title($passedBookID); ?><?php if ($isReadingPack) { echo ' - Reading Pack'; } ?>'
						data-book-language='<?php echo $passedLangID; ?>'
						data-book-type='<?php echo $passedBookType; ?>'
						data-index='<?php echo $index; ?>'>
						<img alt='Quiz' src='<?php echo get_stylesheet_directory_uri(); ?>/assets/img/side-quiz.webp' />
						<?php if (count($quizzesArr) > 1) : ?><span><?php echo $q['title']; ?></span><?php endif; ?>
					</a>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>

		<!-- learning dashboard popup -->		
		<div class='embed__overlay learning-dashboard' data-index='learning-dashboard'>
			<div class='embed__wrap'>
				<div class='embed__wrap__inner'>
					<div class='embed__decoration'><img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/blowing-leaf1.png" alt="leaf"></div>
					<div class='embed__decoration-2'><img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/blowing-leaf3.png" alt="leaf"></div>
					<a class='embed__close'><i class="lni lni-xmark"></i></a>
					<div class='embed__title'>
						<!-- Learning Dashboard -->
						Your Engagement
					</div>
					<div class='embed__content ld-details__wrap'>
						<!-- <div class='heading'>Engagement in the last 7 days</div> -->
						<table>
							<tr>
								<th>Books <br/>Enjoyed</th>
								<th>Quizzed <br/>Started</th>
								<th>Activities <br/>Explored</th>
								<th>Your Total <br/>Feathers</th>
								<?php /*<th>Your Total Streaks</th>*/ ?>
							</tr>
							<tr>
								<td><span class='count-data' id='count-books'><div class="_loader-small"></div></td>
								<td><span class='count-data' id='count-quizzes'><div class="_loader-small"></div></td>
								<td><span class='count-data' id='count-activities'><div class="_loader-small"></div></td>
								<td><span class='count-data' id='count-feathers'><div class="_loader-small"></div></td>
								<?php /*<td><span class='count-data' id='count-streaks'><div class="_loader-small"></div></td>*/ ?>
							</tr>
						</table>

						<div class='ld-details__charts'>
							<div class='chart'>
								<div class='chart-label'>Usage by Book Type</div>
								<canvas id="chart-book" width='300' height='300'></canvas>
							</div>
							<div class='chart'>
								<div class='chart-label'>Usage by Activity</div>
								<canvas id="chart-activity" width='300' height='300'></canvas>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- quizzes popup -->		
		<?php if ($quizzesArr) : ?>
			<?php foreach ($quizzesArr as $index => $q) : ?>
				<div class='embed__overlay' data-index='<?php echo $index; ?>'>
					<div class='embed__wrap'>
						<div class='embed__wrap__inner'>
							<div class='embed__decoration'><img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/blowing-leaf1.png" alt="leaf"></div>
							<div class='embed__decoration-2'><img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/blowing-leaf3.png" alt="leaf"></div>
							<a class='embed__close'><i class="lni lni-xmark"></i></a>
							<div class='embed__title'>
								<?php echo get_the_title($passedStoryID) ?> - <?php echo $q['title']; ?>
							</div>
							<div 
								class='iframe__wrap' 
								data-index='<?php echo $index; ?>' 
								data-iframe='<?php echo htmlspecialchars(l4k_normalizeImageUrl($q['embed']), ENT_QUOTES, 'UTF-8'); ?>'>
							</div>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		<?php endif; ?>
	</div>

	<!-- read it your way -->

	<?php 
	$found = false;
	$showLinked = true;

	if ($linkedBooks):
		foreach ($linkedBooks as $book => $b):
			if ($passedBookID == $b['book_id']) :
				$found = true;
			endif;
		endforeach;
	endif; 

	if ((count($linkedBooks) == 1) && ($found)) { $showLinked = false; }
	?>

	<?php if (($linkedBooks) && ($showLinked)): ?>
	<?php // if there's only one linked book, this means it is the current book ?>
	<?php // then don't show the read it your way section ?>
		<div class='sidebar__item read-it-your-way'>
			<div class='sidebar__label'>Read it your way</div>
			<div class='sidebar__links'>
				<?php foreach ($linkedBooks as $book => $b): ?>
					<?php if ($passedBookID != $b['book_id']) : ?>
						<!-- <a href='<?php // echo $b['book_permalink']; ?>'> -->
						<?php $link = $b['book_permalink']; ?>
						<?php if ($isReadingPack) { $link = trailingslashit($link) . 'reading-pack/'; } ?>
						<a href='<?php echo esc_url($link); ?>'>
							<div class='img__wrap <?php echo ($b['book_type'] == 'flipbook') ? 'flipbook' : ''; ?>'>
								<img src='<?php echo $b['image_url']; ?>' alt='<?php echo get_field('variant_label_'.$b['book_type'], $passedLangID); ?>' />
								<?php if ($b['book_type'] != 'flipbook') : ?>
									<i class="lni lni-play"></i>
								<?php endif; ?>
							</div>
							<span>
								<p class='book-type'>
									<?php echo get_field('variant_label_'.$b['book_type'], $passedLangID); ?>
								</p>
							</span>
						</a>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
		</div>
	<?php endif; ?>

	<!-- related activities -->

	<?php if ($activitiesArr) : ?>
		<div class='sidebar__item related-activities'>
			<div class='sidebar__label'>Related Activities</div>
			<div class='sidebar__links'>
				<?php foreach ($activitiesArr as $activity => $a) : ?>
					<a 	href='<?php echo $a['pdf']; ?>' 
						class='activities__btn'
						data-activity-name='<?php echo $a['title']; ?>' 
						data-activity-title='<?php echo get_the_title($passedBookID); ?>' 
						target='_blank'>
						<img src='<?php echo get_stylesheet_directory_uri(); ?>/assets/img/icon-pdf.png' alt='PDF Icon' />
						<span><?php echo $a['title']; ?></span>
					</a>
				<?php endforeach; ?>
			</div>

		</div>
	<?php endif; ?>

	<!-- similar books -->

	<?php if ($similarBooks): ?>
		<div class='sidebar__item similar-books'>
			<div class='sidebar__label'>Similar Books</div>
			<div class='sidebar__links'>
				<?php foreach ($similarBooks as $book => $b): ?>
					<!-- <a href='<?php //echo $b['book_permalink']; ?>'> -->
					<?php $link = $b['book_permalink']; ?>
					<?php if ($isReadingPack) { $link = trailingslashit($link) . 'reading-pack/'; } ?>
					<a href='<?php echo esc_url($link); ?>'>
						<div class='img__wrap <?php echo ($b['book_type'] == 'flipbook') ? 'flipbook' : ''; ?>'>
							<img src='<?php echo $b['image_url']; ?>' alt='<?php echo $b['english_title']; ?>' />
							<?php if ($b['book_type'] != 'flipbook') : ?>
								<i class="lni lni-play"></i>
							<?php endif; ?>
						</div>
						<span>
							<p class='english-title'><?php echo $b['english_title']; ?></p>
						</span>
					</a>
				<?php endforeach; ?>
			</div>
		</div>
	<?php endif; ?>

</div>