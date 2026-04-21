<?php 
l4k_checkMemberLoggedIn(); 
get_header(); 

$playlistID = get_the_ID();
$playlistArr = l4k_getPlaylistsByLanguage(get_field('language', $playlistID), '', false);
$nextPlaylist = l4k_getNextPlaylist($playlistID, $playlistArr);

$playlist_js_all = l4k_getVideosByPlaylistID($playlistID);
$video_index_map = l4k_getVideoIndexMap($playlist_js_all);

l4k_updateBookNumViews($playlist_js_all[0]['id']); // increment views for the 1st book
l4k_updateLastViewedBook($playlist_js_all[0]['id']); // for last viewed book in recent videos

// on load, log 1062 event
$dataArr = array(
    "Story ID"    => $playlist_js_all[0]['id'],
    "Story Title" => get_the_title($playlist_js_all[0]['id']) . ' Playlist',
    "Language"    => get_field('language', $playlist_js_all[0]['id']),
    "Type"        => get_field('book_type', $playlist_js_all[0]['id'])
);
l4k_addWebActivity(1062, $dataArr);
?>

<div class='main-mid'> 
<div class='_maxwrap medium'> 
<div class='book-wrap'> 
    
    <div class="book-parent__wrapper"> 
        <?php if (!empty($playlist_js_all)) : ?>
            <div class="video-wrapper">
                <?php 
                    $v_init = $playlist_js_all[0];
                    $src = "https://player.vimeo.com/video/" . $v_init['vimeo_id'] . "?title=0&byline=0&portrait=0&muted=0&autoplay=0&app_id=122963";
                    if ($v_init['vimeo_hash']) $src .= "&h=" . $v_init['vimeo_hash'];
                ?>
                <div class="aiovg-player">
                    <iframe id="vimeo-main-player" src="<?php echo esc_url($src); ?>" frameborder="0"allow="autoplay; fullscreen; picture-in-picture"  allowfullscreen ></iframe>
                </div>
            </div>

            <div class='book-info__wrapper'>
                <div class='actions-wrapper'>
                	<div class="playlist-nav-btns">
                        <button id="playlist-prev-btn" class="_btn small"><i class="lni lni-arrow-left"></i> <span>Previous</span></button>
                        <button id="playlist-next-btn" class="_btn small"><span>Next</span> <i class="lni lni-arrow-right"></i></button>
                    </div>
                    <div class='action-item playback-reminder'>You can change the playback speed using the settings menu in the bottom right corner of the player.</div>
                    <a href="#" class='_btn copy-link' id="js-copy-link" data-url="<?php echo l4k_generateQuickCopyLink($playlist_js_all[0]['id']); ?>">
                        <i class="lni lni-share-1"></i> Copy Video Link
                    </a>
                </div>

                <div class='book-info'>
                    <h1 id="js-native-title"><?php echo $playlist_js_all[0]['native_title']; ?></h1>
                    <div class='meta'>
                        <span class='post-title' id="js-post-title"><i class="lni lni-book-1"></i><?php echo $playlist_js_all[0]['title']; ?></span>
                        <span class='views'><i class="lni lni-eye"></i> <span id="js-views"><?php echo $playlist_js_all[0]['views']+1; ?></span> views</span>
                        <span class='date'><i class="lni lni-calendar-days"></i> <span id="js-date"><?php echo $playlist_js_all[0]['date']; ?></span></span>
                        <?php /*<span class='author' <?php echo ($playlist_js_all[0]['author']) ? '' : 'style="display: none;"';?>><i class="lni lni-pencil-1"></i> <span id="js-author"><?php echo $playlist_js_all[0]['author']; ?></span></span>*/ ?>
                    </div>
                    <div class='description' id="js-description">
                        <?php echo $playlist_js_all[0]['description']; ?>
						<div class='universal-meta'>
							<?php echo get_field('universal_meta_data', get_field('native_story')); ?>
						</div>
                    </div>
                    <iframe id="comments-iframe" src='' style="background:transparent;width:100%;border:none;display:block;" scrolling="no"></iframe>
                </div>
            </div>
        <?php else : ?>
            <p>No books found for the selected language.</p>
        <?php endif; ?>
    </div>

    <div class="book-sidebar">
        <div class="sidebar-section">
            <h3 class="sidebar-title">Playlist Items</h3>
            <div class="playlist-list">
                <?php foreach ($playlist_js_all as $v) : ?>
                    <div class="playlist-row" 
                         data-id="<?php echo $v['id']; ?>"
                         data-permalink="<?php echo $v['permalink']; ?>"
                         onclick="window.aiovgLoadVideo(<?php echo $v['id']; ?>)">
                        
                        <div class='img-wrap'>
                            <img src="<?php echo esc_url($v['image']); ?>">
							<i class="lni lni-play"></i>
                        </div>

                        <div class='title-wrap'>
                        	<div class="video-playing"><div class="playing"></div></div>
                            <div class="video-title"><?php echo esc_html($v['native_title']); ?></div>
                            <div class='video-title-english'><?php echo esc_html($v['english_title']); ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div> 

</div> 
</div> 
</div> 

<div class='embed__overlay end-playlist'>
	<div class='embed__wrap'>
		<div class='embed__wrap__inner'>
			<div class='embed__decoration'><img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/blowing-leaf1.png" alt="leaf"></div>
			<div class='embed__decoration-2'><img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/blowing-leaf3.png" alt="leaf"></div>
			<a class='embed__close'><i class="lni lni-xmark"></i></a>
			<div class='embed__title'>Congratulations!</div>
			<div class='embed__content'>
				<a href='<?php echo home_url(); ?>/avatar'>
					<img class='avatar' src='https://lote4kids.com/wp-content/uploads/2025/10/567B4B78-D21A-435B-8925-C25CC70865EF.gif' />
				</a>
				<div class='text'>
					<p>You watched all the stories!</p>
					<p>Want to dress your Lekti or watch again?</p>
				</div>
				<?php if ($nextPlaylist['playlist_permalink']) : ?>
					<a class='_btn' href='<?php echo $nextPlaylist['playlist_permalink']; ?>'><i class="lni lni-rocket-5"></i> Try next level!</a>
				<?php endif; ?>
				<a class='_btn' href='<?php echo home_url(); ?>/avatar'><i class="lni lni-shirt-1"></i> Dress Lekti</a>
				<a class='_btn' id='watch-again-btn' href='<?php echo get_permalink(); ?>'><i class="lni lni-play"></i> Watch again</a>
			</div>
		</div>
	</div>
</div>

<?php if (!empty($playlist_js_all)) : ?>

    <script src="https://player.vimeo.com/api/player.js"></script>
    <script>
	    const prevBtn = document.getElementById('playlist-prev-btn');
	    const nextBtn = document.getElementById('playlist-next-btn');

        const playlist = <?php echo wp_json_encode($playlist_js_all); ?>;
        const videoIndexMap = <?php echo wp_json_encode($video_index_map); ?>;
        
        let currentIndex = 0;
        let player = null;

        window.aiovgLoadVideo = function(videoID, isManual = true) {

            const index = videoIndexMap[videoID];
            if (index === undefined) return;
            currentIndex = index;
           
			updateButtonStates(currentIndex, playlist, prevBtn, nextBtn);

			recordActivityLogToDB(	playlist[index].id, 
									playlist[index].title, 
									playlist[index].language, 
									playlist[index].book_type);

			incrementBookview(playlist[index].id);

			incrementFeatherCount(playlist[index].id, playlist[index].permalink);

            document.getElementById('js-native-title').textContent = playlist[index].native_title;
            document.getElementById('js-post-title').innerHTML = `<i class="lni lni-book-1"></i>${playlist[index].title}`;
            document.getElementById('js-description').innerHTML = playlist[index].description;
            document.getElementById('js-views').textContent = playlist[index].views;
            document.getElementById('js-date').textContent = playlist[index].date;

            /*
            if (playlist[index].author) {
            	document.querySelector('.author').style.display = 'inline-flex';
            	document.getElementById('js-author').textContent = playlist[index].author;	
            } else {
            	document.querySelector('.author').style.display = 'none';
            }
            */
            
            document.querySelectorAll('.playlist-row').forEach(row => row.classList.remove('active-video'));
            const activeRow = document.querySelector(`.playlist-row[data-id="${videoID}"]`);
            if(activeRow) activeRow.classList.add('active-video');

            // scroll the current playing video to the top of the playlist-list 
            // to make sure it is seen for extra long video playlists
			const container = document.querySelector('.playlist-list');
			if (container && activeRow) {
			    container.scrollTo({
			        top: activeRow.offsetTop-65,
			        behavior: 'smooth'
			    });
			}
         
            const playerWrapper = document.querySelector('.aiovg-player');
			playerWrapper.innerHTML = '';

            const data = new URLSearchParams({ 
                action: 'aiovg_load_video', 
                video_id: videoID,
                page_id: '<?php echo $playlistID; ?>'
            });
            
            fetch('<?php echo admin_url('admin-ajax.php'); ?>', { method: 'POST', body: data })
            .then(response => response.json())
            .then(result => {
                if (result.success && result.data.player_html) {
                    playerWrapper.innerHTML = result.data.player_html;
                    setTimeout(() => { 
                        setupPlayer(isManual); 
                        window.dispatchEvent(new Event('resize')); 
						setupCommentsIframe(playlist[index].permalink + '?comments-only'); // moved here
                    }, 200);
                }
            });
        };

        // re-height the iframe of the comments
		let commentsObserver = null;
		let commentsResizeObserver = null;

		function setupCommentsIframe(src) {
		    const iframe = document.getElementById('comments-iframe');
		    
		    // Disconnect previous observers
		    if (commentsObserver) commentsObserver.disconnect();
		    if (commentsResizeObserver) commentsResizeObserver.disconnect();
		    
		    iframe.src = src;

			iframe.onload = function() {
			    const doc = iframe.contentDocument || iframe.contentWindow.document;

				function syncHeight() {
				    const h = Math.max(doc.body.scrollHeight, doc.body.offsetHeight);
					console.log('syncHeight called:', h);
				    iframe.style.height = h + 'px';
				}

			    // Give the inner page time to render its JS-driven content
			    setTimeout(syncHeight, 300);

			    commentsObserver = new MutationObserver(syncHeight);
			    commentsObserver.observe(doc.body, {
			        childList: true, subtree: true, attributes: true
			    });

			    commentsResizeObserver = new ResizeObserver(syncHeight);
			    commentsResizeObserver.observe(doc.body);
			};
		}

        function incrementBookview(bookID) 
        {
			fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
			    method: 'POST',
			    headers: {
			        'Content-Type': 'application/x-www-form-urlencoded',
			    },
			    body: new URLSearchParams({
			        action: 'l4k_incrementViewsViaAjax',
			        book_id: bookID,
			    })
			})
			.then(response => response.json())
			.then(data => { 
				console.log(data); 
				if (data.view_count) {
                    document.getElementById('js-views').textContent = data.view_count;
                }
			})
			.catch(error => { console.error('Error:', error); });
        }

        function recordActivityLogToDB(bookID, storyTitle, lang, type) 
        {
			fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
			    method: 'POST',
			    headers: {
			        'Content-Type': 'application/x-www-form-urlencoded',
			    },
			    body: new URLSearchParams({
			        action: 'l4k_addWebActivityViaAjax',
			        alert_code: '1062',
			        story_id: bookID,
			        story_title: storyTitle + ' Playlist',
			        language: lang,
			        type: type,
			    })
			})
			.then(response => response.json())
			.then(data => { console.log(data); })
			.catch(error => { console.error('Error:', error); });
        }

		function incrementFeatherCount(bookID, bookPermalink) 
		{
		    setTimeout(function() {

		    	// check if the current video being played is still the bookID in this function
		    	// this might not match if the user quickly clicks or navigates through the videos in the playlist

		    	if (bookID == playlist[currentIndex].id) {

					const bookIdentifier = `book-${Date.now()}-${Math.random().toString(36).substr(2, 9)}`;

				    fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
				        method: 'POST',
				        headers: {
				            'Content-Type': 'application/x-www-form-urlencoded',
				        },
				        body: new URLSearchParams({
				            action: 'l4k_addFeatherCountViaAjax',
				            owner_id: document.body.dataset.ownerId,
				            url: bookPermalink,
				        })
				    })
				    .then(response => response.json())
				    .then(data => { 
				        console.log(data); 
				        // if successfully added to database, show toast
				        if (data.status == 1) {  
				            const duration = 5000;
				            const toastId = 'feather-book-toast-'+bookIdentifier;
				            const toastWrap = document.querySelector('._toast');
				            const homeURL = document.getElementById('global-home-url').innerHTML;
				            let toastItem = toastWrap.querySelector(`._toast-item[data-toast-id="${toastId}"]`); // find existing toast for THIS button only
				            
				            if (toastItem) {
				                // RESET existing toast
				                toastItem.style.display = 'block';
				                toastItem.classList.remove('counting-down');
				            } else {
				                // CREATE new toast
				                const toastHTML = `
				                    <a class="_toast-item feather" data-toast-id="${toastId}" href="${homeURL}/avatar">
				                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" transform="rotate(0 0 0)"> <path d="M21.8303 3.82936C20.7331 3.1767 19.2508 2.83203 17.5403 2.83203C14.9242 2.83203 12.0403 3.63595 9.44983 5.03478L9.4315 5.02836L9.42783 5.04761C8.64408 5.47295 7.88417 5.94686 7.171 6.47578C2.96808 9.59336 1.79108 13.293 2.78475 15.2712C1.69667 17.0789 1.0275 18.9498 1 21.1654C3.09 17.2851 4.344 14.1409 11.153 8.8472C9.20783 9.25328 5.8455 11.1801 3.58317 14.1043C3.36958 12.3938 4.7565 9.63186 7.84567 7.3402C8.238 7.05053 8.64408 6.7792 9.05933 6.52161C8.73483 7.50336 8.83933 7.25953 8.16558 8.63636C9.16108 7.71878 9.81558 7.15136 10.7992 5.57928C12.0476 4.97723 13.3635 4.5264 14.7188 4.23636C14.5007 4.93945 14.0863 6.13111 13.5253 7.07345C13.5253 7.07345 14.9489 6.77645 16.1259 6.84428C15.4833 7.53453 14.904 8.28253 14.3173 9.04703C13.5143 10.0939 12.6838 11.1755 11.6196 12.1829C11.4912 12.3049 11.3693 12.4149 11.2456 12.5285C9.61025 12.3754 8.53133 12.9713 7.51933 14.0227C8.31683 13.6606 9.38933 13.3627 10.0603 13.5424C8.82283 14.5259 6.87308 15.8221 5.27167 15.7149C4.96733 16.1659 4.94808 16.1796 4.61442 16.7232C7.21317 17.3539 10.4765 14.7817 12.3969 12.9621C13.5235 11.8951 14.3815 10.7777 15.2111 9.69878C16.9179 7.47403 18.3928 5.5527 21.6644 4.82211L23 4.5242L21.8303 3.82936Z" fill="#343C54"/> </svg>
				                        <div>Gained 1 feather!<span>Earned by viewing a book.</span><span>You now have <p>${data.result}</p> feathers.</span></div>
				                    </a>
				                `;
				                toastWrap.insertAdjacentHTML('afterbegin', toastHTML);
				                toastItem = toastWrap.querySelector(`._toast-item[data-toast-id="${toastId}"]`);
				                toastItem.style.display = 'none';
				                // Fade in
				                toastItem.style.display = 'grid';
				                toastItem.style.opacity = '0';
				                setTimeout(() => { toastItem.style.transition = 'opacity 250ms'; toastItem.style.opacity = '1'; }, 10);
				            }
				            
				            toastItem.style.setProperty('--duration', duration + 'ms'); // set this toast's timer
				            setTimeout(() => { toastItem.classList.add('counting-down'); }, 20); // restart countdown bar
				            
				            // Clear old removal timer
				            if (toastItem.removeTimeout) {
				                clearTimeout(toastItem.removeTimeout);
				            }
				            
				            // Set new removal timer
				            toastItem.removeTimeout = setTimeout(function () { 
				                toastItem.style.transition = 'opacity 250ms';
				                toastItem.style.opacity = '0';
				                setTimeout(() => { toastItem.remove(); }, 250);
				            }, duration);
				        }
				    })
				    .catch(error => { console.error('Error:', error); });

			   }

	    	}, 5000);
		}

		function setupPlayer(autoPlay) 
		{
		    const iframe = document.querySelector('.aiovg-player iframe');
		    if (!iframe) return;
		    player = new Vimeo.Player(iframe);

			// wait until player is ready before setting volume
		    player.ready().then(() => {
		        player.setVolume(1).catch(() => {});
		        if (autoPlay) player.play().catch(() => {});
		    });

		    player.on('ended', () => {
		        if (currentIndex + 1 < playlist.length) {
		            // Create countdown overlay
		            let countdownOverlay = document.querySelector('.countdown-overlay');
		            if (!countdownOverlay) {
		                countdownOverlay = document.createElement('div');
		                countdownOverlay.className = 'countdown-overlay';
		                document.querySelector('.video-wrapper').appendChild(countdownOverlay);
		            }
		            
		            let countdown = 5;
		            // Create static structure with separate span for countdown
		            countdownOverlay.innerHTML = `
		            	<div class='countdown-content'>
		            		<div class='embed__decoration'><img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/blowing-leaf1.png" alt="leaf"></div>
							<div class='embed__decoration-2'><img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/blowing-leaf3.png" alt="leaf"></div>
		                	<img src='https://lote4kids.com/wp-content/uploads/2025/04/l4k-libraries.png' />
		                	<div>Next video in <span class="countdown-number">${countdown}</span>...</div>
		                </div>
		            `;
		            countdownOverlay.style.display = 'flex';
		            
		            const countdownNumber = countdownOverlay.querySelector('.countdown-number');
		            const countdownInterval = setInterval(() => {
		                countdown--;
		                if (countdown > 0) {
		                    // Only update the number
		                    countdownNumber.textContent = countdown;
		                } else {
		                    clearInterval(countdownInterval);
		                    countdownOverlay.style.display = 'none';
		                    document.querySelector('.aiovg-player').innerHTML = '';
		                    window.aiovgLoadVideo(playlist[currentIndex + 1].id, false);
		                }
		            }, 1000);
		        } else {
		            document.querySelector('.embed__overlay.end-playlist').style.display = 'block';
		        }
		    });
		}

		// reusable function to update button states
		const updateButtonStates = (currentIndex, playlist, prevBtn, nextBtn) => {
		    if (currentIndex <= 0) { prevBtn.disabled = true; } 
		    else { prevBtn.disabled = false; }
		    
		    if (currentIndex >= playlist.length - 1) { nextBtn.disabled = true; } 
		    else { nextBtn.disabled = false; }
		};

		document.addEventListener('DOMContentLoaded', () => {
		    setupPlayer(false);

		    setupCommentsIframe('<?php echo get_permalink($playlist_js_all[0]['id']); ?>?comments-only');
		 
		    const firstRow = document.querySelector('.playlist-row');
		    if (firstRow) { firstRow.classList.add('active-video') };
		    
		    // initial button state
		    updateButtonStates(currentIndex, playlist, prevBtn, nextBtn);
		    
		    // previous button is clicked
		    prevBtn.addEventListener('click', () => {
		        if (currentIndex > 0) {
		            window.aiovgLoadVideo(playlist[currentIndex-1].id);
		            updateButtonStates(currentIndex, playlist, prevBtn, nextBtn);
		        }
		    });
		    
		    // next button is clicked
		    nextBtn.addEventListener('click', () => {
		        if (currentIndex < playlist.length - 1) {
		            window.aiovgLoadVideo(playlist[currentIndex+1].id);
		            updateButtonStates(currentIndex, playlist, prevBtn, nextBtn);
		        }
		    });
		});
    </script>

<?php endif; ?>

<?php get_footer(); ?>