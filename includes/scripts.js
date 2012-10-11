jQuery('document').ready(function() {

	// PORTFOLIO SORTING
	
	jQuery('div.nimble-portfolio-filter ul li a').click(function() {

		jQuery(this).css('outline','none');
		jQuery('div.nimble-portfolio-filter ul .current').removeClass('current');
		jQuery(this).parent().addClass('current');
	
		var filterVal = jQuery(this).attr('rel');

		if(filterVal == 'all') {
			jQuery('div.nimble-portfolio ul li.hidden').fadeIn('normal').removeClass('hidden');
                        
		} else {
			jQuery('div.nimble-portfolio ul li').each(function() {
				if(!jQuery(this).hasClass(filterVal)) {
					jQuery(this).fadeOut('normal').addClass('hidden');
    
				} else {
					jQuery(this).fadeIn('normal').removeClass('hidden');
				}
			});
		}
	
		return false;
	});
        
        // PrettyPhoto Lightbox
	jQuery("a[rel^='lightbox'], a[rel^='youtube'], a[rel^='fancybox']").prettyPhoto();
	
	
});
