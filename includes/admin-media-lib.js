(function ($) {
    var imgfield;
    $('#nimble_portfolio_media_lib').click(function() {
        imgfield = $(this).attr('rel');
        tb_show('', 'media-upload.php?type=image&amp;tab=library&amp;TB_iframe=true');
        $("#TB_iframeContent").load(function(){
            var _this = $(this);
            $("#tab-type_url", frames[_this.attr('name')].document).hide();
            $("input[id^='image-size-full']", frames[_this.attr('name')].document).attr('checked','checked');
            $("input[value='Insert into Post']", frames[_this.attr('name')].document).each(function(){
                $(this).val('Use URL in "Image/Video URL" field. ');
            });
        });
        return false;
    });

    window.send_to_editor = function(html) {
        imgurl = $('img',html).attr('src');
        $('#'+imgfield).val(imgurl);
        tb_remove();
    }
    
})(jQuery);