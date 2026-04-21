jQuery(function ($) {
    
    console.log('main.js loaded!');

    /* 
        ----------------------------------------------------------------
        Add aria-hidden to g-recaptcha-hidden for WCAG
        ----------------------------------------------------------------
    */

	document.querySelectorAll('input[name="g-recaptcha-hidden"]').forEach(function(el) {
        el.setAttribute('aria-hidden', 'true');
    });

    /* 
        ----------------------------------------------------------------
        Main menu on mobile
        ----------------------------------------------------------------
    */

    $(document).on('click', '.mobile-menu-trigger', function() {
        $('.main-nav.mobile ul.menu').slideToggle(250);
    });

    /*
    ----------------------------------------------------------------
    Single-device URL parameter toast
    ----------------------------------------------------------------
	*/

	(function () {
	    const urlParams = new URLSearchParams(window.location.search);
	    if (!urlParams.has('single-device')) return;

	    const duration = 5000;
	    const toastId = 'single-device-toast';
	    const $toastWrap = $('._toast');
	    let $toastItem = $toastWrap.find(`._toast-item[data-toast-id="${toastId}"]`);

	    if ($toastItem.length) {
	        $toastItem.stop(true, true).removeClass('counting-down').show();
	    } else {
	        $toastItem = $(`
	            <div class="_toast-item warning" data-toast-id="${toastId}">
	                <i class="lni lni-ban-2"></i>
	                <div>You've been logged out!<span>Your account has been logged in on another device.</span></div>
	            </div>
	        `).hide();
	        $toastWrap.prepend($toastItem);
	        $toastItem.fadeIn(250);
	    }

	    $toastItem.css('--duration', duration + 'ms');
	    setTimeout(() => { $toastItem.addClass('counting-down'); }, 20);
	    clearTimeout($toastItem.data('removeTimeout'));

	    const removeTimeout = setTimeout(function () {
	        $toastItem.fadeOut(250, function () { $(this).remove(); });
	    }, duration);
	    $toastItem.data('removeTimeout', removeTimeout);
	})();

    /* 
        ----------------------------------------------------------------
        Prevent right click
        ----------------------------------------------------------------
    */

	$(document).on("contextmenu", function(e) {
	    e.preventDefault();

	    const duration = 5000;
	    const toastId = 'global-context-toast';
        const $toastWrap = $('._toast');

        let $toastItem = $toastWrap.find(`._toast-item[data-toast-id="${toastId}"]`); // find existing toast for THIS button only

        if ($toastItem.length) {
            // RESET existing toast
            $toastItem.stop(true, true).removeClass('counting-down').show();
        } else {
            // CREATE new toast
            $toastItem = $(`
                <div class="_toast-item error" data-toast-id="${toastId}">
                    <i class="lni lni-xmark-circle"></i>
                    <div>Content protected!<span>You are not allowed to copy content or view source.</span></div>
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
	});

    /* 
        ----------------------------------------------------------------
        Announcement bar
        ----------------------------------------------------------------
    */

    // only show if not previously closed
    if (!localStorage.getItem('announcementClosed')) {
        $('.announcement-bar').show();
    }

    // when user clicks the close button
    $(document).on('click', '.announcement-bar .close', function() {
        $('.announcement-bar').slideUp(250);
        localStorage.setItem('announcementClosed', 'true');
    });

    /* 
        ----------------------------------------------------------------
        Toolbox menu items --> via keyboard
        ----------------------------------------------------------------
    */

	// Trigger click on Enter/Space for all toggles
	$('#trial-toggle, #accessibility-toggle, #flag-toggle, #chat-toggle').on('keydown', function(e) {
	    if (e.key === 'Enter' || e.key === ' ') {
	        e.preventDefault();
	        $(this).trigger('click');

	        // make items in the now-open menu focusable
	        const $menu = $(this).closest('.toolbox-item').find('.toolbox-content');
	        $menu.find('a, button').not('.heading').each(function() {
	            if (!$(this).attr('tabindex')) $(this).attr('tabindex', '0');
	        });
	    }
	});

	// Arrow keys from the toggle — move focus into the open menu
	$('#trial-toggle, #accessibility-toggle, #flag-toggle, #chat-toggle').on('keydown', function(e) {
	    if (e.key !== 'ArrowDown' && e.key !== 'ArrowUp') return;
	    e.preventDefault();

	    const $menu = $(this).closest('.toolbox-item').find('.toolbox-content.active');
	    if (!$menu.length) return;

	    const $items = $menu.find('a, button').filter(':visible').not('.heading');
	    if (!$items.length) return;

	    if (e.key === 'ArrowDown') $items.first().focus();
	    else $items.last().focus();
	});

	// Arrow keys while already inside the menu
	$(document).on('keydown', '.toolbox-content a, .toolbox-content button', function(e) {
	    if (e.key !== 'ArrowDown' && e.key !== 'ArrowUp') return;
	    e.preventDefault();

	    const $menu = $(this).closest('.toolbox-content');
	    const $items = $menu.find('a, button').filter(':visible').not('.heading');
	    const currentIndex = $items.index($(this));
	    let nextIndex;

	    if (e.key === 'ArrowDown') {
	        nextIndex = currentIndex < $items.length - 1 ? currentIndex + 1 : 0;
	    } else {
	        nextIndex = currentIndex > 0 ? currentIndex - 1 : $items.length - 1;
	    }

	    $items.eq(nextIndex).focus();
	});

	// Enter on focused menu items
	$(document).on('keydown', '.toolbox-content a, .toolbox-content button', function(e) {
	    if (e.key === 'Enter') {
	        e.preventDefault();
	        $(this).trigger('click');
	    }
	});

	// Escape — close menu and return focus to toggle
	$(document).on('keydown', '.toolbox-content a, .toolbox-content button', function(e) {
	    if (e.key === 'Escape') {
	        const $item = $(this).closest('.toolbox-item');
	        $item.find('.toolbox-content').removeClass('active');
	        $item.find('.toolbox-link').removeClass('active').focus();
	    }
	});

    /* 
        ----------------------------------------------------------------
        Toolbox menu item actions
        ----------------------------------------------------------------
    */

    // show or hide trial checklist
	$('#trial-toggle, #trial-menu ul li a.heading').on('click', function (e) {
	    e.stopPropagation();

	    $('.toolbox-link').not('#trial-toggle').removeClass('active');
	    $('#trial-toggle').addClass('active');

	    $('.toolbox-content').not('#trial-menu').removeClass('active');
	    $('#trial-menu').toggleClass('active');

	    // Show status badge only when the menu is closed
	    const menuIsOpen = $('#trial-menu').hasClass('active');
	    if (menuIsOpen) {
	        $('#trial-menu-status').removeClass('active');
	    } else {
	        // Only re-show the badge if trial isn't fully complete
	        const total = $('.trial-popup-content li input[type="checkbox"][data-action]').length;
	        const checked = $('.trial-popup-content li input[type="checkbox"][data-action]:checked').length;
	        if (checked < total) {
	            $('#trial-menu-status').addClass('active');
	        }
	    }

	    if (window.botpress) { window.botpress.close(); }
	});

	// show or hide trial checklist - when clicking the floating message
	$('#trial-menu-status').on('click', function (e) {
	    e.stopPropagation();
	    $('#trial-toggle').trigger('click');
	});

    // show or hide accessiblity menu
	$('#accessibility-toggle').on('click', function (e) {
		e.stopPropagation();

		$('.toolbox-link').not('#accessibility-toggle').removeClass('active'); // remove active status from all other toolbox menus
		$(this).toggleClass('active');

		$('.toolbox-content').not('#accessibility-menu').removeClass('active'); // hide all other toolbox menus
		$('#accessibility-menu').toggleClass('active');

		if (window.botpress) { window.botpress.close(); }
	});

	// show or hide flag picker
	$('#flag-toggle').on('click', function (e) {
		e.stopPropagation();
	
		$('.toolbox-link').not('#flag-toggle').removeClass('active'); // remove active status from all other toolbox menus
		$(this).toggleClass('active');

		$('.toolbox-content').not('#flag-menu').removeClass('active'); // hide all other toolbox menus
		$('#flag-menu').toggleClass('active');

		if (window.botpress) { window.botpress.close(); }
	});

	// show or hide botpress
	$('#chat-toggle').on('click', function (e) {
		e.stopPropagation();
	
		$('.toolbox-link').not('#chat-toggle').removeClass('active'); // remove active status from all other toolbox menus
		$('.toolbox-content').not('#chat-menu').removeClass('active'); // hide all other toolbox menus
		
		if (window.botpress) { 
			if ($('iframe.bpWebchat').hasClass('bpClose')) { 
				window.botpress.open(); 
				$('#chat-toggle').addClass('active');
				$('#chat-menu').removeClass('active');
				if ($('.bpWebchat')[0]) { $('.bpWebchat')[0].style.setProperty('bottom', getBotpressBottom(), 'important'); }
			}
			else { 
				// test comment here
				window.botpress.close(); 
				$('#chat-menu').addClass('active');
				$('#chat-toggle').addClass('active');
				if ($('.bpWebchat')[0]) { $('.bpWebchat')[0].style.setProperty('bottom', getBotpressBottom(), 'important'); }
			}
		} 
	});

	// show or hide botpress - when clicking the floating message
	$('#chat-menu').on('click', function (e) {
	    e.stopPropagation();
	    $('#chat-toggle').trigger('click');
	});

	// show or dev debug
	$('#dev-debug-toggle').on('click', function (e) {
		e.stopPropagation();
	
		$('.toolbox-link').not('#dev-debug-toggle').removeClass('active'); // remove active status from all other toolbox menus
		$(this).toggleClass('active');

		$('.toolbox-content').not('#dev-debug-menu').removeClass('active'); // hide all other toolbox menus
		$('#dev-debug-menu').toggleClass('active');

		if (window.botpress) { window.botpress.close(); }
	});

	/*
	// show or hide cookie policy
	$('#cookie-notice-toggle').on('click', function (e) {
		e.stopPropagation();
	
		$('.toolbox-link').not('#cookie-notice-toggle').removeClass('active'); // remove active status from all other toolbox menus
		$(this).toggleClass('active');

		$('.toolbox-content').not('#cookie-notice-menu').removeClass('active'); // hide all other toolbox menus
		$('#cookie-notice-menu').toggleClass('active');

		if (window.botpress) { window.botpress.close(); }
	});
	*/

	/*
	// by default, the cookie policy is shown
	// hide it in the succeeding visits if the accept button is clicked
	$('#accept-cookie').on('click', function () {
		localStorage.setItem('cookiePolicy', '1'); 
		$('.toolbox-link').removeClass('active');
		$('.toolbox-content').not('#cookie-notice-menu').removeClass('active'); // hide all other toolbox menus
		$('#cookie-notice-menu').toggleClass('active');
		$('#cookie-notice-menu').one('transitionend webkitTransitionEnd oTransitionEnd', function() {
    		$('.toolbox-item.cookie-notice-wrapper').remove();
		});
	});
	*/

    /* 
        ----------------------------------------------------------------
        Accessibility menu items
        ----------------------------------------------------------------
    */

	var textScaleStep = 0; // -1, 0, 1, 2
	var grayscaleOn = false;
	var highContrastOn = false;
	var selectedLangImg = $('#flag-toggle img').attr('src');

	// determine what HTML elements to apply the increase/decrease text to
	const contentSelectors = [
		'p',
		'span',
		'a',
		'li',
		'h1',
		'h2',
		'h3',
		'h4',
		'h5',
		'h6',
		'button',
		'label',
		'td',
		'th',
		'small',
		'strong',
		'em',
		'input',
		'textarea',
		'div'
	];

	const contentExclusions = [
		'#wpadminbar',
		'#wpadminbar *',
		'.fixed-elements',
		'.fixed-elements *'
	];

	const $contentElements = $(contentSelectors.join(', ')).not(contentExclusions.join(', '));

	// call on page load for all existing elements
	initElementSizes($contentElements);

	// store original font sizes and line-heights for a given set of elements
	function initElementSizes($elements) {
	    $elements.each(function() {
	        const $el = $(this);
	        // skip if already initialized
	        if ($el.data('original-size')) return;
	        $el.data('original-size', parseInt($el.css('font-size')));
	        $el.data('current-size', parseInt($el.css('font-size')));
	        const lineHeight = parseFloat($el.css('line-height'));
	        $el.data('original-line-height', isNaN(lineHeight) ? $el.data('original-size') * 1.2 : lineHeight);
	        $el.data('current-line-height', $el.data('original-line-height'));
	    });
	}

	// increase text size and line-height proportionally
	$('#acce-increase-text').on('click', function () {
		if (isNaN(textScaleStep)) { textScaleStep = 0; }
	    if (textScaleStep < 4) {
	        textScaleStep++;
	        applyTextScale();
	        saveAccessibilityState();
	    }
	});

	// decrease text size and line-height proportionally
	$('#acce-decrease-text').on('click', function () {
	    if (textScaleStep > -1) {
	        textScaleStep--;
	        applyTextScale();
	        saveAccessibilityState();
	    }
	});

	// perform increase or decrease in text size
	function applyTextScale() {
	    const $allElements = $contentElements.add($('button'));

	    $allElements.each(function() {
	        const $el = $(this);
	        const originalSize = $el.data('original-size');
	        const originalLineHeight = $el.data('original-line-height');
	        const newSize = originalSize + (textScaleStep * 2);
	        const scaleRatio = newSize / originalSize;
	        const newLineHeight = originalLineHeight * scaleRatio;
	        //this.style.setProperty('font-size', newSize + 'px', 'important');
	        //this.style.setProperty('line-height', newLineHeight + 'px', 'important');
	        this.style.setProperty('font-size', newSize + 'px');
	        this.style.setProperty('line-height', newLineHeight + 'px');
	        $el.data('current-size', newSize);
	        $el.data('current-line-height', newLineHeight);
	    });
	}

	let resizeTimeout;
	$(window).on('resize', function () {
	    clearTimeout(resizeTimeout);
	    resizeTimeout = setTimeout(function () {
	        if (textScaleStep !== 0) {
	            applyTextScale();
	        }
	        $('.fixed-elements-menu-items').css('bottom', getMenuItemsBottom()); // recalculate on resize
	        if ($('.bpWebchat')[0]) { $('.bpWebchat')[0].style.setProperty('bottom', getBotpressBottom(), 'important'); }
	        $('._toast').css('bottom', getMenuItemsBottom()); // recalculate on resize
	        if (!localStorage.getItem('cookiePolicy')) {
			    $('.cookie-floating-footer').css('display', $(window).width() <= 600 ? 'block' : 'flex');
			}
	    }, 150);
	});

	// determine what HTML elements to apply the grayScale and highContrast to
	const grayHighSelectors = [
		'header',
		'footer',
		'.announcement-bar',
		'.main-mid',
		'.book-parent__wrapper',
		'.sidebar.main',
		'.sidebar__item.read-it-your-way',
		'.sidebar__item.related-activities',
		'.sidebar__item.similar-books',
		'.sidebar__item.reward-driven .sidebar__links',
		'.playlist-wrap',
		'.books-wrap',
		'.heading > *:not(.fun-facts)',
		'.heading .fun-facts .fun-facts__inner > *'
	];

	const grayHighExclusions = [
		'.main-mid.book',
		'.main-mid.language'
	];

	const $grayHighElements = $(grayHighSelectors.join(', ')).not(grayHighExclusions.join(', '));

	// grayscale
    $('#acce-grayscale').on('click', function() {
        if (!grayscaleOn) { removeHighContrast(); applyGrayscale(); } 
        else { removeGrayscale(); }

	    $('.toolbox-link').removeClass('active');
	    $('#accessibility-menu').toggleClass('active');
    });

    function applyGrayscale() {
    	$('body').css('background-color', '#ccc');
    	$('.blowing-leaves').css('display', 'none'); // hide blowing leaves

    	/*
    	$('header').css('filter', 'grayscale(100%)');
        $('main').css('filter', 'grayscale(100%)');
        $('.announcement-bar').css('filter', 'grayscale(100%)');
        $('footer').css('filter', 'grayscale(100%)');
        */

		$grayHighElements.each(function() { $(this).css('filter', 'grayscale(100%)'); });

        grayscaleOn = true;
        saveAccessibilityState();
    }

    function removeGrayscale() {
        if (!highContrastOn) {
        	if ($('body').data('custom-bg')) { $('body').css('background-color', $('body').data('custom-bg')); } 
        	else { $('body').css('background-color', 'var(--default-body-color)'); }
        	$('.blowing-leaves').css('display', 'block'); // show blowing leaves

        	/*
			$('main').css('filter', 'none');
		    $('header').css('filter', 'none');
	        $('.announcement-bar').css('filter', 'none');		
	        $('footer').css('filter', 'none');
	        */

	        $grayHighElements.each(function() { $(this).css('filter', 'none'); });
	    }

        grayscaleOn = false;
        saveAccessibilityState();
    }

    // high contrast
    $('#acce-high-contrast').on('click', function() {
        if (!highContrastOn) { removeGrayscale(); applyHighContrast(); } 
        else { removeHighContrast(); }

	    $('.toolbox-link').removeClass('active');
	    $('#accessibility-menu').toggleClass('active');
    });

    function applyHighContrast() {
    	$('.blowing-leaves').css('display', 'none'); // hide blowing leaves
		$('body').css('background-color', '#000');
		$('#flipbook').css('filter', 'invert(1)');

    	/*
        $('main').css('filter', 'invert(1) contrast(150%)');
        $('main img').css('filter', 'invert(1)');
        $('header').css('filter', 'invert(1) contrast(150%)');
        $('header img').css('filter', 'invert(1)');
        $('.announcement-bar').css('filter', 'invert(1) contrast(150%)');
        $('.announcement-bar img').css('filter', 'invert(1)');
        $('footer').css('filter', 'invert(1) contrast(150%)');
        $('footer img').css('filter', 'invert(1)');
        */

        $grayHighElements.each(function() { 
        	$(this).css('filter', 'invert(1) contrast(150%)'); 
        	$(this).find('img').css('filter', 'invert(1)'); 
        });

        highContrastOn = true;
        saveAccessibilityState();
    }

    function removeHighContrast() {
        if (!grayscaleOn) {
	        if ($('body').data('custom-bg')) { $('body').css('background-color', $('body').data('custom-bg')); } 
        	else { $('body').css('background-color', 'var(--default-body-color)'); }

        	$('.blowing-leaves').css('display', 'block'); // show blowing leaves
        	$('#flipbook').css('filter', 'none');

        	/*
	        $('main').css('filter', 'none');
	        $('main img').css('filter', 'none');
	        $('header').css('filter', 'none');
	        $('header img').css('filter', 'none');
	        $('.announcement-bar').css('filter', 'none');
	        $('.announcement-bar img').css('filter', 'none');
	        $('footer').css('filter', 'none');
	        $('footer img').css('filter', 'none');
	        */
	       
	        $grayHighElements.each(function() { 
	        	$(this).css('filter', 'none'); 
	        	$(this).find('img').css('filter', 'none'); 
	        });
	    }

        highContrastOn = false;
        saveAccessibilityState();
    }

	// reset all changes to default
	$('#acce-reset').on('click', function () {
	    textScaleStep = 0;
	    applyTextScale();
	    removeGrayscale();
	    removeHighContrast();
	    saveAccessibilityState();

	    $('.toolbox-link').removeClass('active');
	    $('#accessibility-menu').toggleClass('active');
	});

    /* 
        ----------------------------------------------------------------
        Google translate
        ----------------------------------------------------------------
    */

	function setTranslateCookie(lang) {
	    document.cookie = `googtrans=/en/${lang};path=/`;
	    document.cookie = `googtrans=/en/${lang};domain=${location.hostname};path=/`;
	}

	function triggerTranslate(lang, imgSrc) {
	    waitForGTranslate(($select) => {
	        setTranslateCookie(lang);
	        $select.val(lang);
	        $select[0].dispatchEvent(new Event('change'));

	        $('#flag-toggle img').attr('src', imgSrc);
	        $('#flag-menu').removeClass('active');
	    });
	}

	$('.gtranslate-btn').on('click', function () {
		selectedLangImg = $(this).children('img').attr('src');
		triggerTranslate($(this).data('lang'), $(this).children('img').attr('src'));
		saveAccessibilityState();
	});

	function waitForGTranslate(callback) {
	    const interval = setInterval(() => {
	        const $select = $('.goog-te-combo');
	        if ($select.length) {
	            clearInterval(interval);
	            callback($select);
	        }
	    }, 100); // check every 100ms
	}

    /* 
        ----------------------------------------------------------------
        Botpress
        ----------------------------------------------------------------
    */

	if (window.botpress) {

		window.botpress.init({
			"botId": "ebe8822d-b22b-4ba2-b9f4-3b8dc8b708db",
			"configuration": {
				"hideWidget": true,
				"composerPlaceholder": "Type your response here...",
				"botName": "Find your library or school here!",
				"botAvatar": "https://files.bpcontent.cloud/2025/04/15/07/20250415072155-TGJELYZR.png",
				"botDescription": "",
				"website": {},
				"email": {},
				"phone": {},
				"termsOfService": {},
				"privacyPolicy": {},
				"color": "#F19E38",
				"variant": "solid",
				"themeMode": "light",
				"fontFamily": "inter",
				"radius": 1,
				"additionalStylesheetUrl": "https://files.bpcontent.cloud/2025/04/15/23/20250415231841-NVE7DNAU.css",
				"storageLocation": "sessionStorage"
			},
			"clientId": "0c4e01ac-74ee-4058-8099-a5e3301a71ae"
		});

		// detect if bpWebchat was closed from the widget itself

		$.fn.onClassChange = function(callback) {
		    return this.each(function() {
		        const target = this;
		        let hadClass = $(target).hasClass('bpClose'); // initial state

		        const observer = new MutationObserver(function(mutations) {
		            mutations.forEach(function(mutation) {
		                if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
		                    const hasClassNow = $(target).hasClass('bpClose');

		                    if (hasClassNow !== hadClass) { // class added or removed
		                        hadClass = hasClassNow;
		                        callback.call(target, hasClassNow); // pass true/false
		                    }
		                }
		            });
		        });

		        observer.observe(target, { attributes: true });
		    });
		};

		$('.bpWebchat').onClassChange(function(isBpClose) {
		    if (isBpClose && $('#chat-toggle').hasClass('active')) { 
		    	$('#chat-menu').addClass('active');
		    }
		});

	}

    /* 
        ----------------------------------------------------------------
        Local storage
        ----------------------------------------------------------------
    */

	// persist accessibility preferences to localstorage
	function saveAccessibilityState() {
        localStorage.setItem('grayscaleOn', grayscaleOn ? '1' : '0');
        localStorage.setItem('highContrastOn', highContrastOn ? '1' : '0');
    	localStorage.setItem('textScaleStep', textScaleStep ? textScaleStep : '0');
    	localStorage.setItem('selectedLangImg', selectedLangImg);
	}

	loadToolboxState();

	// check and load saved values from localstorage
	function loadToolboxState() {
		// const savedCookiePolicy = localStorage.getItem('cookiePolicy');
	    const savedGrayscale = localStorage.getItem('grayscaleOn');
	    const savedHighContrast = localStorage.getItem('highContrastOn');
	    const savedTextScale = parseInt(localStorage.getItem('textScaleStep'), 10);
	    const savedSelectedLangImg = localStorage.getItem('selectedLangImg');

		textScaleStep = savedTextScale;
		grayscaleOn = savedGrayscale;
		highContrastOn = savedHighContrast;
		selectedLangImg = savedSelectedLangImg;

		// check what current gtranslate language is selected
	    if (savedSelectedLangImg != null && savedSelectedLangImg !== '' && savedSelectedLangImg != 'null') {
	    	$('#flag-toggle img').attr('src', savedSelectedLangImg);
	    }

	    /*
	    // check if cookie policy should be shown
	    if (savedCookiePolicy) {
			$('.toolbox-item.cookie-notice-wrapper').remove();
	    } else {
	    	$('#cookie-notice-menu').addClass('active');
	    	$('#cookie-notice-toggle').addClass('active');
	    }
	    */

	    // check if chat menu should be shown
	    // chat section is shown by default if the cookie policy has been accepted 
	    /*
	    if (savedCookiePolicy) {
	    	$('#chat-menu').addClass('active');
	    	$('#chat-toggle').addClass('active');
	    }
	    */

    	$('#chat-menu').addClass('active');
    	$('#chat-toggle').addClass('active');

	    // check if grayscale is applied
	    if (savedGrayscale === '1') { applyGrayscale(); }
	    else { removeGrayscale(); }

	    // check if highContrast is applied
	    if (savedHighContrast === '1') { applyHighContrast(); }
	    else { removeHighContrast(); }

	    // check if text scale is increase/decreased
	    if (!isNaN(savedTextScale)) {
	        textScaleStep = savedTextScale;
	        applyTextScale();
	    }
	}

    /* 
        ----------------------------------------------------------------
        Accept cookie in floating footer cookie policy
        ----------------------------------------------------------------
    */

	function getMenuItemsBottom() {
	    const cookieVisible = $('.cookie-floating-footer').is(':visible');
	    const width = $(window).width();
	    if (cookieVisible) {
	        return $('.cookie-floating-footer').outerHeight(true) + 20 + 'px';
	    }
	    if (width <= 800) return '20px';
	    if (width <= 1400) return '30px';

	    const atBottom = $(window).scrollTop() + $(window).height() >= $(document).height() - 10;
	    return atBottom ? '40px' : '50px';
	}

	function getBotpressBottom() {
	    const menuBottom = parseInt(getMenuItemsBottom()); // get the same base value
	    const width = $(window).width();
	    const bpOffset = width <= 800 ? 60 : 70; // account for botpress widget height
	    return (menuBottom + bpOffset) + 'px';
	}

	/*
	function getBotpressBottom() {
	    const cookieVisible = $('.cookie-floating-footer').is(':visible');
	    const width = $(window).width();
	    if (cookieVisible) {
			if (width <= 800) return $('.cookie-floating-footer').outerHeight(true) + 80 + 'px';
	    	if (width > 801) return $('.cookie-floating-footer').outerHeight(true) + 90 + 'px';
	    }
	    if (width <= 800) return '100px';
	    if (width > 801) return '120px';

	    const atBottom = $(window).scrollTop() + $(window).height() >= $(document).height() - 10;
	    return atBottom ? '100px' : '110px';
	}
	*/

	$('#accept-cookie').on('click', function () {
	    localStorage.setItem('cookiePolicy', '1'); 
	    $('.cookie-floating-footer').fadeOut(400, function() {
	        $('._toast').css('bottom', getMenuItemsBottom());
	        if ($('.bpWebchat')[0]) { $('.bpWebchat')[0].style.setProperty('bottom', getBotpressBottom(), 'important'); }
	        $('.fixed-elements-menu-items').css('bottom', getMenuItemsBottom());
	    });
	});

	// check if cookie policy bottom floating bar is shown
	if (localStorage.getItem('cookiePolicy')) {
		$('.cookie-floating-footer').hide();
		$('._toast').css('bottom', getMenuItemsBottom());
		if ($('.bpWebchat')[0]) { $('.bpWebchat')[0].style.setProperty('bottom', getBotpressBottom(), 'important'); }
		$('.fixed-elements-menu-items').css('bottom', getMenuItemsBottom());
	} else {
		$('.cookie-floating-footer').css('display', $(window).width() <= 600 ? 'block' : 'flex');
		$('._toast').css('bottom', getMenuItemsBottom());
		if ($('.bpWebchat')[0]) { $('.bpWebchat')[0].style.setProperty('bottom', getBotpressBottom(), 'important'); }
		$('.fixed-elements-menu-items').css('bottom', getMenuItemsBottom());
	}

    /* 
        ----------------------------------------------------------------
        When you reach the bottom of the page, adjust toolbox position
        ----------------------------------------------------------------
    */

	$(window).on('scroll', function () {
		/*
	    const scrollTop = $(window).scrollTop();
	    const windowHeight = $(window).height();
	    const docHeight = $(document).height();
	    const atBottom = scrollTop + windowHeight >= docHeight - 10;
	    const cookieVisible = $('.cookie-floating-footer').is(':visible');

	    if (cookieVisible) {
            $('.fixed-elements-menu-items').css('bottom', getMenuItemsBottom());
	    } else {
	        $('.fixed-elements-menu-items').css('bottom', atBottom ? '30px' : '50px');
	    }
	    */ 
		$('.fixed-elements-menu-items').css('bottom', getMenuItemsBottom());
	}).trigger('scroll');

    /* 
        ----------------------------------------------------------------
		Back stretch custom subtle background
        ----------------------------------------------------------------
    */

	if (!window.location.search.includes('comments-only')) {
	    $.backstretch("https://lote4kids.com/wp-content/uploads/2022/10/lote-background-101422-1.png");
	}

    /* 
        ----------------------------------------------------------------
        Specific for avatar page, observe if there are new elements 
        added to DOM and apply changing of text size from toolbox
        ----------------------------------------------------------------
    */

	const observer = new MutationObserver(function(mutations) {
	    mutations.forEach(function(mutation) {
	        mutation.addedNodes.forEach(function(node) {
	            if (node.nodeType !== 1) return;

	            const $buttons = $(node).find('button').addBack('button');

	            if ($buttons.length) {
	                initElementSizes($buttons);
	                if (textScaleStep !== 0) {
	                    $buttons.each(function() {
	                        const $el = $(this);
	                        const originalSize = $el.data('original-size');
	                        const originalLineHeight = $el.data('original-line-height');
	                        const newSize = originalSize + (textScaleStep * 2);
	                        const newLineHeight = originalLineHeight * (newSize / originalSize);
	                        this.style.setProperty('font-size', newSize + 'px', 'important');
	                        this.style.setProperty('line-height', newLineHeight + 'px', 'important');
	                        $el.data('current-size', newSize);
	                        $el.data('current-line-height', newLineHeight);
	                    });
	                }
	            }
	        });
	    });
	});

	const avatarSection = $('.avatar-wrap .items-section')[0];
	if (avatarSection) {
	    observer.observe(avatarSection, {
	        childList: true,
	        subtree: true
	    });
	}

	/* 
	    ----------------------------------------------------------------
	    Check if session library_barcode is set
	    If yes, check if barcode is in the alternate_barcode table
	    If yes, show popup (only if not previously dismissed)
	    ----------------------------------------------------------------
	*/

	if (l4k_vars.library_barcode && !window.location.search.includes('comments-only')) {
	// if (false) {

		// determine if barcode is a trial
		// if yes, check the checkboxes associated with it
		$.ajax({
		    url: main_ajax.url,
		    type: 'POST',
		    data: {
		        action: 'l4k_checkIfBarcodeIsTrial', 
		        page_id: l4k_vars.page_id, 
		        barcode: l4k_vars.library_barcode,
		    },
		    success: function(response) {
		        if (response.status === 1) {
		        	const showTrial = JSON.parse(localStorage.getItem('trialPopupShown') || '[]');
		        	if (!showTrial.includes(l4k_vars.library_barcode)) {
		        		$('.embed__overlay.trial-popup').fadeIn();	
		        		addToTrialPopupShown(l4k_vars.library_barcode); // add value to localstorage
		        	}

		            // if data exists, check the boxes in the popup that are marked as '1'
		            if (response.data) {
		                $.each(response.data, function(action, value) {
		                    if (value === '1') { $('input[data-action="' + action + '"]').prop('checked', true); }
		                });
		            }

					populateTrialCounter();
               	 	$('#trial-menu-status, #trial-toggle').addClass('active');
               	 	populateTrialCounter();
		        } else {
		        	$('.toolbox-item.trial-checklist').remove();
		        }
		        console.log(response);
		    }
		});

		// on click of checkbox in popup, send an ajax request to update the status
		// on click of checkbox in toolbox, send an ajax request to update the status
		$('.trial-popup-content li input[type="checkbox"], #trial-menu ul li a input[type="checkbox"]').on('click', function() {
		    $(this).prop('checked', true); // prevent unchecking
		    const url = $(this).data('url');
		    if (url) { 
			    if ($(this).data('newtab')) { window.open(url, '_blank'); }
			    else { window.location.href = url; }
		    }

		   	const $checkbox = $(this).closest('li').find('input[type="checkbox"]');
    		const action = $checkbox.data('action');
    		$('input[data-action="' + action + '"]').prop('checked', true); // check both the popup and toolbox checkboxes with the same data-action
		    sendTrialActivityData();
		});

		// on click of link in popup, send an ajax request to update the status
		// on click of link in toolbox, send an ajax request to update the status
		$('.trial-popup-content li a, #trial-menu ul li a').on('click', function() {
		    const $li = $(this).closest('li');
		    $li.find('input[type="checkbox"]').prop('checked', true);
			
			const $checkbox = $(this).closest('li').find('input[type="checkbox"]');
    		const action = $checkbox.data('action');
    		$('input[data-action="' + action + '"]').prop('checked', true); // check both the popup and toolbox checkboxes with the same data-action
		    sendTrialActivityData();
		});

		// build activity data from all checkboxes with a data-action
		function sendTrialActivityData() {

			populateTrialCounter();

		    const activityData = {};
		    $('.trial-popup-content li input[type="checkbox"][data-action]').each(function() {
		        const action = $(this).data('action');
		        activityData[action] = $(this).prop('checked') ? '1' : '0';
		    });

			$.ajax({
			    url: main_ajax.url,
			    type: 'POST',
			    data: $.extend({
			        action: 'l4k_updateTrialActivityStatus',
			        barcode: l4k_vars.library_barcode,
			    }, activityData),
			    success: function(response) {
			        console.log(response);
			    }
			});
		}

		// fill in the trial counter like 2/8 for example
		function populateTrialCounter() {
		    const total = $('.trial-popup-content li input[type="checkbox"][data-action]').length;
		    const checked = $('.trial-popup-content li input[type="checkbox"][data-action]:checked').length;
		    if (checked == total) {
		        $('.trial-counter').html('All steps completed <i class="lni lni-check"></i>');
		        $('#trial-menu-status').removeClass('active'); // hide the status badge
		    } else {
		        $('.trial-counter').html(checked + '/' + total + ' completed');
		        $('#trial-menu-status').addClass('active'); // ensure it's visible while incomplete
		    }
		}

		// adding values over time to trialPopupShown localstorage
		function addToTrialPopupShown(value) {
		    const current = JSON.parse(localStorage.getItem('trialPopupShown')) || [];
		    if (!current.includes(value)) {
		        current.push(value);
		        localStorage.setItem('trialPopupShown', JSON.stringify(current));
		    }
		}

	} else {

		$('.toolbox-item.trial-checklist').remove();

	}

	/* 
	    ----------------------------------------------------------------
	    Functions for ALL overlays in the sidebar
	    ----------------------------------------------------------------
	*/

	// close overlay when background is clicked
	$('.embed__overlay.trial-popup').on('click', function() {
	    addToTrialPopupShown(l4k_vars.library_barcode);
	    $('.trial-menu-status').addClass('active');
	    $('#trial-toggle').addClass('active');
	    $(this).fadeOut();
	    $('body').removeClass('_noscroll');
	});

	// close overlay when X is clicked
	$('.embed__overlay.trial-popup .embed__close, .embed__overlay.trial-popup .trial-cta ._btn').on('click', function(e) {
	    e.preventDefault(); 
	    const $overlay = $(this).closest('.embed__overlay');
	    if ($overlay.hasClass('trial-popup')) { addToTrialPopupShown(l4k_vars.library_barcode); }
	    $overlay.fadeOut();
	    $('body').removeClass('_noscroll');
	});

	// close overlay when ESC is pressed
	$(document).on('keyup', function(e) {
	    if (e.key === "Escape") {
	        const $visible = $('.embed__overlay.trial-popup:visible');
	        if ($visible.hasClass('trial-popup')) { addToTrialPopupShown(l4k_vars.library_barcode); }
	        $visible.fadeOut();
	        $('body').removeClass('_noscroll');
			$('.iframe__wrap iframe').attr('src', '');
	    }
	});

	// do NOT close when the box inside the overlay is clicked
	$('.embed__overlay.trial-popup .embed__wrap').on('click', function(e) {
	    e.stopPropagation();
	});

}); 