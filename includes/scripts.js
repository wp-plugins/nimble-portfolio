jQuery('document').ready(function() {

	// PORTFOLIO SORTING
	
	jQuery('div.nimble-portfolio-filter ul li a').click(function() {

		jQuery(this).css('outline','none');
		jQuery('div.nimble-portfolio-filter ul .current').removeClass('current');
		jQuery(this).parent().addClass('current');
	
		var filterVal = jQuery(this).text().toLowerCase();

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
	
	// LIGHTBOX FANCYBOX
	
	jQuery("a[rel=fancybox]").fancybox();
	
	jQuery("a[rel=youtube]").click(function() {
		jQuery.fancybox({
				'padding'		: 0,
				'autoScale'		: false,
				'transitionIn'	: 'none',
				'transitionOut'	: 'none',
				'title'			: this.title,
				'width'			: 680,
				'height'		: 495,
				'href'			: this.href.replace(new RegExp("watch\\?v=", "i"), 'v/'),
				'type'			: 'swf',
				'swf'			: {
				   	 'wmode'		: 'transparent',
					'allowfullscreen'	: 'true'
				}
			});
	
		return false;
	});
	
	

});
