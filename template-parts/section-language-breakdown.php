<?php 
$bookCount 	= l4k_getBookCountPerLanguage(); 
$summary 	= l4k_countTotalBookCountAndLatestRelease($bookCount);
$latest 	= l4k_getLatestBookReleased();
?>

<div class='embed__overlay breakdown'>
	<div class='embed__wrap'>
		<div class='embed__wrap__inner'>
			<div class='embed__decoration'><img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/blowing-leaf1.png" alt="leaf"></div>
			<div class='embed__decoration-2'><img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/blowing-leaf3.png" alt="leaf"></div>
			<a class='embed__close'><i class="lni lni-xmark"></i></a>
			<div class='embed__title'>
				Languages - Title Breakdown
			</div>
			<div class='embed__content'>
				<div class='breakdown-wrap'>
					<div class='summary'>
						<div class='total-titles'>Total Titles: <span><?php echo $summary['total_count']; ?></span></div>
						<div class='most-recent'>Latest Release: <span><?php echo date('F j, Y', strtotime($latest->post_date)); ?></span></div>
						<div class='_clear'></div>
					</div>

					<table class='breakdown-table'>
						<tr>
							<th>Language</th><th class='book-count'>Titles</th>
							<th>Language</th><th class='book-count'>Titles</th>
							<th>Language</th><th class='book-count'>Titles</th>
						</tr>
						<?php $counter = 0; ?>
						<?php while($counter < count($bookCount[0])) : ?>
							<tr>
								<td class='title'>
									<a href='<?php echo get_permalink($bookCount[0][$counter]['lang_id']); ?>' target='_blank'>
										<img src='<?php echo $bookCount[0][$counter]['flag_url']; ?>' />
										<?php echo $bookCount[0][$counter]['title']; ?>
									</a>
								</td>
								<td class='book-count'><?php echo $bookCount[0][$counter]['book_count']; ?></td>

								<td class='title'>
									<a href='<?php echo get_permalink($bookCount[1][$counter]['lang_id']); ?>' target='_blank'>
										<img src='<?php echo $bookCount[1][$counter]['flag_url']; ?>' />
										<?php echo $bookCount[1][$counter]['title']; ?>
									</a>
								</td>
								<td class='book-count'><?php echo $bookCount[1][$counter]['book_count']; ?></td>

								<td class='title'>
									<a href='<?php echo get_permalink($bookCount[2][$counter]['lang_id']); ?>' target='_blank'>
										<img src='<?php echo $bookCount[2][$counter]['flag_url']; ?>' />
										<?php echo $bookCount[2][$counter]['title']; ?>
									</a>
								</td>
								<td class='book-count'><?php echo $bookCount[2][$counter]['book_count']; ?></td>
							</tr>	
							<?php $counter++; ?>
						<?php endwhile; ?>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>