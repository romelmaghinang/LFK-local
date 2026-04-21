jQuery(function ($) {
    
    console.log('faq.js loaded!');

	$('.faq-title').addClass('active');

    /* 
        ----------------------------------------------------------------
        FAQ single toggle
        ----------------------------------------------------------------
    */

    $('.faq-title').on('click', function() {
        var $title = $(this);
        var $content = $title.next('.faq-content');
        var $icon = $title.find('.icon-toggle');

        $content.slideToggle(250);
        $title.toggleClass('active');

        // Switch + / - icon
        if ($title.hasClass('active')) 
        {
            $icon.removeClass('lni-plus').addClass('lni-minus');
        } 
        else 
        {
            $icon.removeClass('lni-minus').addClass('lni-plus');
        }
    });

    /* 
        ----------------------------------------------------------------
        Collapse/expand all button
        ----------------------------------------------------------------
    */

    $('#faq-toggle-all').on('click', function() {
        var $btn = $(this);
        var $label = $btn.find('.label');
        var $icon = $btn.find('i');

        if ($label.text() === 'Collapse All') 
        {
            // Collapse all
            $('.faq-content').slideUp();
            $('.faq-title').removeClass('active');
            $('.icon-toggle').removeClass('lni-minus').addClass('lni-plus');
            $label.text('Expand All');
            $icon.removeClass('lni-chevron-up').addClass('lni-chevron-down');
        } 
        else 
        {
            // Expand all
            $('.faq-content').slideDown();
            $('.faq-title').addClass('active');
            $('.icon-toggle').removeClass('lni-plus').addClass('lni-minus');
            $label.text('Collapse All');
            $icon.removeClass('lni-chevron-down').addClass('lni-chevron-up');
        }
    });

}); 
