jQuery(document).ready(function() {
    // Portfolio Filtering
    jQuery('.nimble-portfolio-filter ul li a').click(function() {
        jQuery(this).css('outline','none');
        jQuery('.nimble-portfolio-filter ul .current').removeClass('current');
        jQuery(this).parent().addClass('current');
        var filterVal = jQuery(this).attr('rel');
        if(filterVal == 'all') {
            jQuery('.nimble-portfolio ul li.hidden').fadeIn('normal').removeClass('hidden');
        } else {
            jQuery('.nimble-portfolio ul li').each(function() {
                if(!jQuery(this).hasClass(filterVal)) {
                    jQuery(this).fadeOut('slow').addClass('hidden');
                } else {
                    jQuery(this).fadeIn('slow').removeClass('hidden');
                }
            });
        }
        // Apply lightbox gallery only to current items
        jQuery("a[rel^='lightbox'], a[rel^='youtube'], a[rel^='fancybox']", ".nimble-portfolio ul li:not(.hidden)" ).prettyPhoto();
        return false;
    });
    // PrettyPhoto Lightbox
    jQuery("a[rel^='lightbox'], a[rel^='youtube'], a[rel^='fancybox']").prettyPhoto();
});
