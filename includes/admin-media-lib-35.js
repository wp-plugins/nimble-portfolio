(function ($) {
    var file_frame;
    $('#nimble_portfolio_media_lib').live('click', function( event ){
        var imgfield = $(this).attr('rel');
        event.preventDefault();
			 
        if ( file_frame ) {
            file_frame.open();
            return;
        }

        var _states = [new wp.media.controller.Library({
            filterable: 'uploaded',
            title: 'Select an Image',
            multiple: false,
            priority:  20
        })];
			 
        file_frame = wp.media.frames.file_frame = wp.media({
            states: _states,
            button: {
                text: 'Insert URL'
            }
        });

        file_frame.on( 'select', function() {
            var attachment = file_frame.state().get('selection').first().toJSON();
            $('#'+imgfield).val(attachment.url); 
        });
		 
        file_frame.open();
    });
    
})(jQuery);
