<?php
$skins = apply_filters('nimble_portfolio_skin_register', array());
$post_types = get_post_types(array('public' => true), 'objects');
$taxonomies = get_object_taxonomies(NimblePortfolioPlugin::getPostType(), 'objects');
?>
<html>
    <head>
        <title>Nimble Portfolio Shortcode</title>
        <script src="<?php echo site_url(); ?>/wp-includes/js/jquery/jquery.js"></script>
        <script src="<?php echo site_url(); ?>/wp-includes/js/tinymce/tiny_mce_popup.js"></script>
        <script>
            var NimblePortfolio = {
                e: '',
                init: function (e) {
                    NimblePortfolio.e = e;
                    tinyMCEPopup.resizeToInnerSize();
                },
                insert: function createNimblePortfolioShortcode(e) {
                    //Create gallery Shortcode
                    var post_type = jQuery('#nimble_portfolio_tinymce_post_type').val();
                    var taxonomy = jQuery('#nimble_portfolio_tinymce_taxonomy').val();
                    var skin = jQuery('#nimble_portfolio_tinymce_skin').val();
                    var hide_filters = jQuery('#nimble_portfolio_tinymce_hide_filters').is(':checked');
                    var orderby = jQuery('#nimble_portfolio_tinymce_orderby').val();
                    var order = jQuery('#nimble_portfolio_tinymce_order').val();

                    var params = '';

                    if (post_type) {
                        params += 'post_type="' + post_type + '" ';
                    }

                    if (taxonomy) {
                        params += 'taxonomy="' + taxonomy + '" ';
                    }

                    if (skin) {
                        params += 'skin="' + skin + '" ';
                    }

                    if (hide_filters) {
                        params += 'hide_filters="' + hide_filters + '" ';
                    }

                    if (orderby) {
                        params += 'orderby="' + orderby + '" ';
                    }

                    if (order) {
                        params += 'order="' + order + '" ';
                    }

                    jQuery(document).on("nimble_portfolio_tinymce_param", function (event, obj) {
                        params += event.result ? event.result : '';
                    });

                    jQuery(document).trigger("nimble_portfolio_tinymce_param", {});

                    var output = '[nimble-portfolio ' + params + ']';

                    tinyMCEPopup.execCommand('mceReplaceContent', false, output);

                    tinyMCEPopup.close();

                }
            }

            tinyMCEPopup.onInit.add(NimblePortfolio.init, NimblePortfolio);
jQuery(document).ready(function($){
            var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
            var loaderurl = '<?php echo admin_url('images/loading.gif'); ?>';
            $('#nimble_portfolio_tinymce_skin').change(function () {
                var skin = $(this).val();
                var _this = $(this);
                $("#nimble_portfolio_tinymce_skin_ajax_response").html('<img src="' + loaderurl + '" />');
                $(this).attr('disabled', 'disabled');
                $.get(ajaxurl,
                        {
                            'action': 'nimble_portfolio_tinymce_skin_change',
                            'skin': skin
                        },
                function (response) {
                    $('#nimble_portfolio_tinymce_skin_ajax_response').html(response);
                    _this.removeAttr('disabled');
                })
            });
            $('#nimble_portfolio_tinymce_post_type').change(function () {
                var post_type = $(this).val();
                var _this = $(this);
                $("#nimble_portfolio_tinymce_post_type_ajax_response").html('<img src="' + loaderurl + '" />');
                $(this).attr('disabled', 'disabled');
                $.get(ajaxurl,
                        {
                            'action': 'nimble_portfolio_tinymce_post_type_change',
                            'post_type': post_type
                        },
                function (response) {
                    $('#nimble_portfolio_tinymce_post_type_ajax_response').html(response);
                    _this.removeAttr('disabled');
                    var _t = $('#nimble_portfolio_tinymce_taxonomy');
                    $(document).trigger("nimble_portfolio_tinymce_taxonomy_change", {taxonomy: (_t.length ? _t.val() : ''), post_type: post_type, flag: true});
                });
            });
            $(document).on('change', '#nimble_portfolio_tinymce_taxonomy', function () {
                $(document).trigger("nimble_portfolio_tinymce_taxonomy_change", {post_type: $('#nimble_portfolio_tinymce_post_type').val(), taxonomy: $(this).val()});
            });
 });           
        </script>
    </head>
    <body>
        <?php do_action('nimble_portfolio_tinymce_params_before'); ?>
        <p>
            <label for="nimble_portfolio_tinymce_post_type"><?php _e("Post Type", 'nimble_portfolio'); ?>:</label>
            <select id="nimble_portfolio_tinymce_post_type" name="nimble_portfolio_tinymce_post_type">
                <?php foreach ($post_types as $post_type => $post_type_obj) { ?>
                    <option value="<?php echo $post_type ?>" <?php selected($post_type, 'portfolio'); ?>><?php echo "$post_type_obj->label ($post_type)"; ?></option>
                <?php } ?>
            </select>
        </p>
        <p id="nimble_portfolio_tinymce_post_type_ajax_response">
            <label for="nimble_portfolio_tinymce_taxonomy"><?php _e("Filters Type (Taxonomy)", 'nimble_portfolio'); ?>:</label>
            <select id="nimble_portfolio_tinymce_taxonomy" name="nimble_portfolio_tinymce_taxonomy">
                <?php foreach ($taxonomies as $taxonomy => $taxonomy_obj) { ?>
                    <option value="<?php echo $taxonomy ?>" <?php selected($taxonomy, 'nimble-portfolio-type'); ?>><?php echo "$taxonomy_obj->label ($taxonomy)"; ?></option>
                <?php } ?>
            </select>
        </p>
        <p>
            <label for="nimble_portfolio_tinymce_skin"><?php _e("Skin", 'nimble_portfolio'); ?>:</label>
            <select id="nimble_portfolio_tinymce_skin" name="nimble_portfolio_tinymce_skin">
                <?php foreach ($skins as $skin) { ?>
                    <option value="<?php echo $skin->name ?>"><?php echo $skin->label ?></option>
                <?php } ?>
            </select>
        </p>
        <p id="nimble_portfolio_tinymce_skin_ajax_response"> </p>
        <p>
            <label for = "nimble_portfolio_tinymce_hide_filters"><?php _e("Hide Filters", 'nimble_portfolio');
                ?>:</label>
            <input type="checkbox" id="nimble_portfolio_tinymce_hide_filters" name="nimble_portfolio_tinymce_hide_filters" value="1" />
        </p>
        <p>
            <label for="nimble_portfolio_tinymce_orderby"><?php _e("Order By", 'nimble_portfolio'); ?>:</label>
            <select id="nimble_portfolio_tinymce_orderby" name="nimble_portfolio_tinymce_orderby" >
                <option value="author"><?php _e('Author', 'nimble_portfolio'); ?></option>
                <option value="date"><?php _e('Date', 'nimble_portfolio'); ?></option>
                <option value="modified"><?php _e('Last Modified Date', 'nimble_portfolio'); ?></option>
                <option value="comment_count"><?php _e('Number of Comments', 'nimble_portfolio'); ?></option>
                <option value="ID"><?php _e('Post ID', 'nimble_portfolio'); ?></option>
                <option value="menu_order" selected="selected"><?php _e('Page Order', 'nimble_portfolio'); ?></option>
                <option value="parent"><?php _e('Post Parent ID', 'nimble_portfolio'); ?></option>
                <option value="name"><?php _e('Post Slug', 'nimble_portfolio'); ?></option>
                <option value="title"><?php _e('Post Title', 'nimble_portfolio'); ?></option>
                <option value="rand"><?php _e('Random', 'nimble_portfolio'); ?></option>
            </select>
        </p>
        <p>
            <label for="nimble_portfolio_tinymce_order"><?php _e("Order", 'nimble_portfolio'); ?>:</label>
            <select id="nimble_portfolio_tinymce_order" name="nimble_portfolio_tinymce_order" >
                <option value="ASC"><?php _e('Ascending', 'nimble_portfolio'); ?></option>
                <option value="DESC"><?php _e('Descending', 'nimble_portfolio'); ?></option>
            </select>
        </p>
        <?php do_action('nimble_portfolio_tinymce_params_after'); ?>
        <p><button onclick="javascript:NimblePortfolio.insert(NimblePortfolio.e)">Insert Shortcode</button></p>
    </body>
</html>
