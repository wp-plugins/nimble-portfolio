jQuery(document).ready(function($) {

    tinymce.create('tinymce.plugins.nimble_portfolio', {
        init: function(ed, url) {
            ed.addCommand('nimble_portfolio_shortcode', function() {
                ed.windowManager.open({
                    file: ajaxurl + '?action=nimble_portfolio_tinymce',
                    width: 450 + parseInt(ed.getLang('example.delta_width', 0)),
                    height: 450 + parseInt(ed.getLang('example.delta_height', 0)),
                    inline: 1
                }, {
                    plugin_url: url
                });
            });
            ed.addButton('nimble_portfolio_button', {title: 'Insert Nimble Portfolio Shortcode', cmd: 'nimble_portfolio_shortcode', image: url + '/icon.png'});
        },
    });

    tinymce.PluginManager.add('nimble_portfolio_button', tinymce.plugins.nimble_portfolio);

});

