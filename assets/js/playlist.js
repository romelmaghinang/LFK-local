/*
let currentIndex = 0;
let player = null;


const { playlist, videoIndexMap, ajaxUrl, pageId, langSlug } = window.playlistData;

window.aiovgLoadVideo = function(videoID, isManual = true) {
    const index = videoIndexMap[videoID];
    if (index === undefined) return;
    currentIndex = index;

   
    document.getElementById('js-native-title').textContent = playlist[index].native_title;
    document.getElementById('js-post-title').innerHTML = `<i class="lni lni-book-1"></i>${playlist[index].title}`;
    document.getElementById('js-description').innerHTML = playlist[index].description;
    document.getElementById('js-views').textContent = playlist[index].views;
    document.getElementById('js-date').textContent = playlist[index].date;
    
    document.querySelectorAll('.playlist-row').forEach(row => row.classList.remove('active-video'));
    const activeRow = document.querySelector(`.playlist-row[data-id="${videoID}"]`);
    if(activeRow) activeRow.classList.add('active-video');

 
    const playerWrapper = document.querySelector('.aiovg-player');
    const data = new URLSearchParams({ 
        action: 'aiovg_load_video', 
        video_id: videoID,
        page_id: pageId, 
        lang_text: langSlug
    });
    
    fetch(ajaxUrl, { method: 'POST', body: data })
    .then(response => response.json())
    .then(result => {
        if (result.success && result.data.player_html) {
            playerWrapper.innerHTML = result.data.player_html;
            setTimeout(() => { 
                setupPlayer(isManual); 
                window.dispatchEvent(new Event('resize')); 
            }, 200);
        }
    });
};

function setupPlayer(autoPlay) {
    const iframe = document.querySelector('.aiovg-player iframe');
    if (!iframe) return;
    
    player = new Vimeo.Player(iframe);
    player.on('ended', () => {
        if (currentIndex + 1 < playlist.length) {
            window.aiovgLoadVideo(playlist[currentIndex + 1].id, false);
        } else {
            document.querySelector('.lfk-second-iframe-popup').style.display = 'flex';
        }
    });
    if (autoPlay) player.play().catch(() => {});
}

document.addEventListener('DOMContentLoaded', () => {
    setupPlayer(false);
    
    const firstRow = document.querySelector('.playlist-row');
    if(firstRow) firstRow.classList.add('active-video');

  
    setTimeout(() => { window.dispatchEvent(new Event('resize')); }, 500);

  
    document.getElementById('aiovg-prev-btn').addEventListener('click', () => {
        if (currentIndex > 0) window.aiovgLoadVideo(playlist[currentIndex-1].id);
    });
    document.getElementById('aiovg-next-btn').addEventListener('click', () => {
        if (currentIndex < playlist.length - 1) window.aiovgLoadVideo(playlist[currentIndex+1].id);
    });
    document.querySelector('.lfk-close-popup').addEventListener('click', () => {
        document.querySelector('.lfk-second-iframe-popup').style.display = 'none';
    });
});
*/

jQuery(function ($) {

	console.log('playlist.js loaded!');

	incrementFeatherCount(playlist[0].id, playlist[0].permalink)

    /* 
        ----------------------------------------------------------------
        Quick copy video
        ----------------------------------------------------------------
    */

	$('.actions-wrapper .copy-link').on('click', function(e) {
	    e.preventDefault();

	    const url      = $(this).data('url');
	    const duration = 6000;
	    const toastId  = $(this).data('toast-id');

	    navigator.clipboard.writeText(url).then(function () {

	        const $toastWrap = $('._toast');

	        let $toastItem = $toastWrap.find(`._toast-item[data-toast-id="${toastId}"]`); // find existing toast for THIS button only

	        if ($toastItem.length) {
	            // RESET existing toast
				$toastItem.stop(true, true).removeClass('counting-down').show();
	        } else {
	            // CREATE new toast
	            $toastItem = $(`
	                <div class="_toast-item" data-toast-id="${toastId}">
	                    <i class="lni lni-check-circle-1"></i>
	                    <div>Link copied!<span>Paste this link into your LMS, email, or class portal.</span></div>
	                </div>
	            `).hide();

	            $toastWrap.prepend($toastItem);
	            $toastItem.fadeIn(250);
	        }

	        $toastItem.css('--duration', duration + 'ms'); // set this toast's timer
	        setTimeout(() => { $toastItem.addClass('counting-down'); }, 20); // restart countdown bar
	        clearTimeout($toastItem.data('removeTimeout')); // clear old removal timer FOR THIS toast
	        const removeTimeout = setTimeout(function () { $toastItem.fadeOut(250, function () { $(this).remove(); }); }, duration);
	        $toastItem.data('removeTimeout', removeTimeout); // store timer on element

	    }).catch(function (err) {
	        console.error('Copy failed', err);
	    });
	});

    /* 
        ----------------------------------------------------------------
        End playlist popup overlay
        ----------------------------------------------------------------
    */

	// close overlay when background is clicked
	$('.end-playlist.embed__overlay').on('click', function() {
		$(this).fadeOut();
		$('body').removeClass('_noscroll'); // restore scroll
	});

	// close overlay when X is clicked
	$('.end-playlist .embed__close').on('click', function(e) {
        e.preventDefault(); 
        $(this).closest('.embed__overlay').fadeOut();
        $('body').removeClass('_noscroll'); // restore scroll
	});

	// close overlay when ESC is pressed
	$(document).on('keyup', function(e) {
	    if (e.key === "Escape") {
	        $('.end-playlist.embed__overlay:visible').fadeOut();
	        $('body').removeClass('_noscroll'); // restore scroll
	    }
	});

	// do NOT close when the box inside the overlay is clicked
	$('.end-playlist.embed__overlay .embed__wrap').on('click', function(e) {
		e.stopPropagation();
	});

    /* 
        ----------------------------------------------------------------
        Scrolling sidebar with precise 8 elements max only
        ----------------------------------------------------------------
    */

	const $container = $('.playlist-list');
	const $items = $container.children();

	if ($items.length > 8) {
	    // Wait for all images to load
	    const images = $container.find('img').toArray();
	    
	    Promise.all(images.map(img => {
	        if (img.complete) return Promise.resolve();
	        return new Promise(resolve => {
	            img.onload = resolve;
	            img.onerror = resolve; // also resolve on error
	        });
	    })).then(() => {
	        let totalHeight = 0;
	        $items.slice(0, 8).each(function() {
	            totalHeight += $(this).outerHeight() + 12;
	        });
	        $container.css({
	            'max-height': totalHeight + 'px',
	            'overflow-y': 'auto'
	        });
	    });
	} else {
	    $container.css({
	        'max-height': 'none',
	        'overflow-y': 'visible'
	    });
	}

});