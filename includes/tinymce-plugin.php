<?php
$skins = apply_filters('nimble_portfolio_skin_register', array());
?>
<html>
    <head>
        <title>Nimble Portfolio Shortcode</title>
        <script src="<?php echo site_url(); ?>/wp-includes/js/jquery/jquery.js"></script>
        <script src="<?php echo site_url(); ?>/wp-includes/js/tinymce/tiny_mce_popup.js"></script>
        <script>
            var NimblePortfolio = {
                e: '',
                init: function(e) {
                    NimblePortfolio.e = e;
                    tinyMCEPopup.resizeToInnerSize();
                },
                insert: function createNimblePortfolioShortcode(e) {
                    //Create gallery Shortcode
                    var skin = jQuery('#nimble_portfolio_tinymce_skin').val();
                    var hide_filters = jQuery('#nimble_portfolio_tinymce_hide_filters').is(':checked');

                    var output = '[nimble-portfolio ';

                    if (skin) {
                        output += 'skin="' + skin + '" ';
                    }

                    if (hide_filters) {
                        output += 'hide_filters="' + hide_filters + '" ';
                    }

                    output += ']';

                    tinyMCEPopup.execCommand('mceReplaceContent', false, output);

                    tinyMCEPopup.close();

                }
            }

            tinyMCEPopup.onInit.add(NimblePortfolio.init, NimblePortfolio);

            var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
            jQuery(document).ready(function($) {
                $('#nimble_portfolio_tinymce_skin').change(function() {
                    var skin = $(this).val();
                    $.get(ajaxurl,
                            {
                                'action': 'nimble_portfolio_tinymce_skin_change',
                                'skin': skin
                            },
                    function(response) {
                       $('#nimble_portfolio_tinymce_skin_ajax_response').html(response);
                    })
                });
            });
        </script>
    </head>
    <body>
        <?php do_action('nimble_portfolio_tinymce_params_before'); ?>
        <p>
            <label for="nimble_portfolio_tinymce_skin"><?php _e("Skin"); ?>:</label>
            <select id="nimble_portfolio_tinymce_skin" name="nimble_portfolio_tinymce_skin">
                <?php foreach ($skins as $skin) { ?>
                    <option value="<?php echo $skin->name ?>"><?php echo $skin->label ?></option>
                <?php } ?>
            </select>
        </p>
        <p id="nimble_portfolio_tinymce_skin_ajax_response">
        </p>
        <p>
            <label for="nimble_portfolio_tinymce_hide_filters"><?php _e("Hide Filters"); ?>:</label>
            <input type="checkbox" id="nimble_portfolio_tinymce_hide_filters" name="nimble_portfolio_tinymce_hide_filters" value="1" />
        </p>
        <?php do_action('nimble_portfolio_tinymce_params_after'); ?>
        <p><button onclick="javascript:NimblePortfolio.insert(NimblePortfolio.e)">Insert Shortcode</button></p>
    </body>
</html>
