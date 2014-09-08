<?php
/*
  Plugin Name: Nimble Portfolio
  Plugin URI: http://nimble3.com/demo/nimble-portfolio-free/
  Description: Using this free plugin you can transform your portfolio in to a cutting edge jQuery powered gallery that lets you feature and sort your work like a pro.
  Version: 2.0.8
  Author: Nimble3
  Author URI: http://www.nimble3.com/
  License: GPLv2 or later
 */

include("includes/class.NimblePortfolio.php");
include("includes/class.NimblePortfolioItem.php");
include("includes/class.NimblePortfolioSkin.php");

if (!class_exists('NimblePortfolioPlugin')) {

    class NimblePortfolioPlugin {

        static private $version;
        static private $postType;
        static private $postTypeSlug;
        static private $taxonomy;
        static private $taxonomySlug;
        static private $dirPath;
        static private $dirUrl;

        static function init($params = array()) {
            self::$version = '2.0.8';
            self::$postType = 'portfolio';
            self::$postTypeSlug = apply_filters('nimble_portfolio_posttype_slug', 'portfolio');
            self::$taxonomy = 'nimble-portfolio-type';
            self::$taxonomySlug = apply_filters('nimble_portfolio_taxonomy_slug', 'portfolio-filter');
            self::$dirPath = dirname(__FILE__);
            self::$dirUrl = self::path2url(self::$dirPath);
            add_theme_support('post-thumbnails', array(self::$postType));
            add_image_size('portfolio_col_thumb', 100, 100, true);

            add_filter('attribute_escape', array(__CLASS__, 'renameMenuTitle'), 10, 2);

            register_activation_hook(__FILE__, array(__CLASS__, 'onActivate'));
            register_deactivation_hook(__FILE__, array(__CLASS__, 'onDeactivate'));

            add_action('init', array(__CLASS__, 'registerPostType'));
            add_action('init', array(__CLASS__, 'tinymceShortcodeButton'));

            add_shortcode('nimble-portfolio', array(__CLASS__, 'getPortfolio'));

            add_action('wp_head', array(__CLASS__, 'enqueueStyle'));
            add_action('wp_head', array(__CLASS__, 'enqueueScript'));

            add_filter('manage_' . self::$postType . '_posts_columns', array(__CLASS__, 'adminPostsColumns'));
            add_action('manage_' . self::$postType . '_posts_custom_column', array(__CLASS__, 'adminPostsCustomColumn'));

            // Custom Fields for Taxonomy
            add_action(self::$taxonomy . '_edit_form_fields', array(__CLASS__, 'taxonomyEditFormField'));
            add_action(self::$taxonomy . '_add_form_fields', array(__CLASS__, 'taxonomyAddFormField'));
            add_action('edited_' . self::$taxonomy, array(__CLASS__, 'saveTaxonomyValue'));
            add_action('create_' . self::$taxonomy, array(__CLASS__, 'saveTaxonomyValue'));
            add_action('manage_edit-' . self::$taxonomy . '_columns', array(__CLASS__, 'taxonomyColumnHeader'));
            add_action('manage_' . self::$taxonomy . '_custom_column', array(__CLASS__, 'taxonomyCustomValue'), 10, 3);
            add_action('quick_edit_custom_box', array(__CLASS__, 'taxonomyQuickEditField'), 10, 3);

            // Admin Handlers
            add_action('admin_head', array(__CLASS__, 'adminHead'));
            add_action('admin_menu', array(__CLASS__, 'adminOptions'));
            add_action('save_post', array(__CLASS__, 'updateData'), 1, 2);

            add_action('wp_ajax_nimble_portfolio_tinymce', array(__CLASS__, 'ajaxTinymceShortcodeParams'));
            add_action('wp_ajax_nimble_portfolio_tinymce_skin_change', array(__CLASS__, 'ajaxTinymceSkinChange'));

            do_action('nimble_portfolio_init');
        }

        function path2url($path) {
            if (!defined('ABSPATH')) {
                return false;
            }
            return trim(site_url(), '/\\') . "/" . str_replace("\\", "/", trim(substr_replace($path, '', 0, strlen(ABSPATH)), '/'));
        }

        function phpvar2htmlatt($atts) {
            $return = ' ';
            if (is_array($atts) && count($atts)) {
                foreach ($atts as $att => $val) {
                    $return .= $att . '="' . (is_array($val) ? implode(" ", $val) : $val) . '" ';
                }
            }
            return $return;
        }

        function getVersion() {
            return self::$version;
        }

        function getPostType() {
            return self::$postType;
        }

        function getPostTypeSlug() {
            return self::$postTypeSlug;
        }

        function getTaxonomy() {
            return self::$taxonomy;
        }

        function getTaxonomySlug() {
            return self::$taxonomySlug;
        }

        function getPath($tail) {
            $tail = trim($tail, '/');
            return self::$dirPath . "/$tail/";
        }

        function getUrl($tail) {
            $tail = trim($tail, '/');
            return self::$dirUrl . "/$tail/";
        }

        function updateData($post_id, $post) {

            // verify this came from the our screen and with proper authorization,
            // because save_post can be triggered at other times
            if (!wp_verify_nonce($_POST['nimble_portfolio_noncename'], plugin_basename(__FILE__))) {
                return;
            }

            if ($post->post_type == 'revision') {
                return;
            }

            if (!current_user_can('edit_post', $post->ID)) {
                return;
            }

            // OK, we're authenticated: we need to find and save the data
            // We'll put it into an array to make it easier to loop though.
            $mydata = array();
            $mydata['nimble-portfolio'] = $_POST['nimble_portfolio'];
            $mydata['nimble-portfolio-url'] = $_POST['nimble_portfolio_url'];

            $mydata = apply_filters('nimble_portfolio_update_data', $mydata);

            // Add values of $mydata as custom fields
            foreach ($mydata as $key => $value) { //Let's cycle through the $mydata array!
                update_post_meta($post->ID, $key, $value);
                if (!$value)
                    delete_post_meta($post->ID, $key); //delete if blank
            }
        }

        function renameMenuTitle($safe_text, $text) {
            if (__('Portfolio Items', 'nimble_portfolio_context') !== $text) {
                return $safe_text;
            }

            // We are on the main menu item now. The filter is not needed anymore.
            remove_filter('attribute_escape', 'renameMenuTitle');

            return __('Nimble Portfolio', 'nimble_portfolio_context');
        }

        function onActivate() {
            self::registerPostType();
            flush_rewrite_rules();
        }

        function onDeactivate() {
            flush_rewrite_rules();
        }

        function registerPostType() {

            $labels = array(
                'name' => __('Portfolio Items'),
                'singular_name' => __('Portfolio Item'),
                'add_new' => __('Add Portfolio Item'),
                'add_new_item' => __('Add New Portfolio Item'),
                'edit_item' => __('Edit Portfolio Item'),
                'new_item' => __('New Portfolio Item'),
                'view_item' => __('View Portfolio Item'),
                'search_items' => __('Search Portfolio Items'),
                'not_found' => __('No Portfolio Items found'),
                'not_found_in_trash' => __('No Portfolio Items found in Trash'),
                'parent_item_colon' => '',
                'menu_name' => __('Portfolio Items')
            );

            $args = array(
                'labels' => $labels,
                'public' => true,
                'show_ui' => true,
                'capability_type' => 'post',
                'hierarchical' => true,
                'rewrite' => array('slug' => self::$postTypeSlug),
                'supports' => array(
                    'title',
                    'thumbnail',
                    'editor',
                    'excerpt',
                ),
                'menu_position' => 23,
                'menu_icon' => self::$dirUrl . '/includes/icon.png',
                'taxonomies' => array(self::$taxonomy)
            );

            $args = apply_filters('nimble_portfolio_post_type_args', $args);

            register_post_type(self::$postType, $args);

            self::registerTaxonomy(self::$postType);
        }

        function registerTaxonomy($postType = null) {

            if ($postType === null) {
                return;
            }

            $labels = array(
                'name' => __('Filters'),
                'singular_name' => __('Filter'),
                'add_new' => __('Add Filter'),
                'add_new_item' => __('Add New Filter'),
                'edit_item' => __('Edit Filter'),
                'new_item' => __('New Filter'),
                'view_item' => __('View Filter'),
                'search_items' => __('Search Filters'),
                'not_found' => __('No Filters found'),
                'not_found_in_trash' => __('No Filters found in Trash'),
                'parent_item_colon' => '',
                'menu_name' => __('Filters')
            );

            $args = array(
                'labels' => $labels,
                'hierarchical' => true,
                'query_var' => true,
                'rewrite' => array('slug' => self::$taxonomySlug)
            );

            $args = apply_filters('nimble_portfolio_taxonomy_args', $args);

            register_taxonomy(self::$taxonomy, $postType, $args);
        }

        function tinymceShortcodeButton() {

            if (!current_user_can('edit_posts') && !current_user_can('edit_pages') && get_user_option('rich_editing') == 'true') {
                return;
            }

            add_filter("mce_external_plugins", array(__CLASS__, "registerTinymcePlugin"));
            add_filter('mce_buttons', array(__CLASS__, 'registerTinymceButton'));
        }

        function registerTinymcePlugin($plugin_array) {
            $plugin_array['nimble_portfolio_button'] = self::$dirUrl . '/includes/tinymce-plugin.js';
            return $plugin_array;
        }

        function registerTinymceButton($buttons) {
            $buttons[] = "nimble_portfolio_button";
            return $buttons;
        }

        function ajaxTinymceShortcodeParams() {
            include "includes/tinymce-plugin.php";
            exit;
        }

        function ajaxTinymceSkinChange() {
            do_action('nimble_portfolio_tinymce_skin_change', $_GET['skin']);
            exit;
        }

        function getPortfolio($atts) {
            ob_start();
            self::showPortfolio($atts);
            $content = ob_get_clean();
            return $content;
        }

        function showPortfolio($atts = array()) {

            $nimble_portfolio = new NimblePortfolio($atts);

            $nimble_portfolio->renderTemplate();
        }

        function enqueueStyle() {

            $nimblebox = apply_filters('nimble_portfolio_lightbox_style', self::getUrl('includes') . "prettyphoto/prettyphoto.css");
            if ($nimblebox) {
                wp_enqueue_style('nimblebox-style', $nimblebox);
            }

            $nimblesort = apply_filters('nimble_portfolio_sort_style', '');
            if ($nimblesort) {
                wp_enqueue_style('nimblesort-style', $nimblesort);
            }

            do_action('nimble_portfolio_enqueue_style');
        }

        function enqueueScript() {

            $nimblebox = apply_filters('nimble_portfolio_lightbox_script', self::getUrl('includes') . "prettyphoto/prettyphoto.js");
            if ($nimblebox) {
                wp_enqueue_script('nimblebox-script', $nimblebox, array('jquery'), self::$version);
            }

            $nimblesort = apply_filters('nimble_portfolio_sort_script', self::getUrl('includes') . 'sort.js');
            if ($nimblesort) {
                wp_enqueue_script('nimblesort-script', $nimblesort, array('jquery'), self::$version);
            }

            do_action('nimble_portfolio_enqueue_script');
        }

        function adminHead() {
            wp_enqueue_style('nimble-portfolio-admin', self::getUrl('includes') . "admin.css", null, null, "all");
            wp_register_script('nimble-portfolio-admin', self::getUrl('includes') . 'admin.js', array('jquery'), self::$version);
            wp_enqueue_script('nimble-portfolio-admin');
        }

        function adminOptions() {
            do_action('nimble_portfolio_create_section_before', self::$postType);
            add_meta_box('nimble-portfolio-section-options', __('Options', 'nimble_portfolio_context'), array(__CLASS__, 'renderOptions'), self::$postType, 'normal', 'high');
            do_action('nimble_portfolio_create_section_after', self::$postType);
        }

        function renderOptions($post) {
            $item = new NimblePortfolioItem($post->ID);
            ?>
            <div class="nimble-portfolio-meta-section">
                <div class="form-wrap">
                    <div class="form-field">
                        <label for="nimble_portfolio"><?php _e('Image/Video URL', 'nimble_portfolio_context') ?></label>
                        <input type="text" id="nimble_portfolio" name="nimble_portfolio" value="<?php echo esc_attr($item->getData('nimble-portfolio')); ?>" style="width:70%;" />
                        <a id="nimble_portfolio_media_lib" href="javascript:void(0);" class="button" rel="nimble_portfolio"><?php _e('URL from Media Library', 'nimble_portfolio_context') ?></a>
                        <p><?php _e('Enter URL for the full-size image or video (youtube, vimeo, swf, quicktime) you want to display in the lightbox gallery. You can also choose Image URL from your Media gallery <strong>(Please note: If this field is empty then Featured Image will be used in lightbox gallery)</strong>', 'nimble_portfolio_context') ?></p>
                    </div>            
                    <div class="form-field">
                        <label for="nimble_portfolio_url"><?php _e('Portfolio URL', 'nimble_portfolio_context') ?></label>
                        <input type="text" name="nimble_portfolio_url" value="<?php echo esc_attr($item->getData('nimble-portfolio-url')); ?>" />
                        <p><?php _e('Enter URL to the live version of the project.', 'nimble_portfolio_context') ?></p>
                    </div>            
                    <div class="form-field">
                        <label for="menu_order"><?php _e('Sort Order', 'nimble_portfolio_context') ?></label>
                        <input type="text" id="menu_order" name="menu_order" value="<?php echo esc_attr($item->menu_order); ?>" style="width: 100px;" />
                        <p><?php _e('Set the sort order for your item here with 0 being the first and so on.', 'nimble_portfolio_context') ?></p>
                    </div>
                    <?php do_action('nimble_portfolio_renderoptions_field', $item); ?>
                </div>
                <input type="hidden" name="nimble_portfolio_noncename" id="nimble_portfolio_noncename" value="<?php echo wp_create_nonce(plugin_basename(__FILE__)); ?>" />
            </div>
            <?php
        }

        function adminPostsColumns($cols) {
            $cols['filters'] = __('Filters', 'nimble_portfolio_context');
            $cols['thumbnail'] = __('Thumbnail', 'nimble_portfolio_context');
            $cols['sort-order'] = __('Sort Order', 'nimble_portfolio_context');
            return $cols;
        }

        function adminPostsCustomColumn($column_name) {
            $item = new NimblePortfolioItem(get_the_ID());
            if ($column_name == 'thumbnail') {
                echo '<img src="' . $item->getThumbnail('portfolio_col_thumb') . '" />';
            } elseif ($column_name == 'filters') {
                $_terms = $item->getFilters(self::$taxonomy, 'R');
                if (!empty($_terms) && !is_wp_error($_terms)) {
                    $terms = array();
                    foreach ($_terms as $_term) {
                        $terms[] = '<a href="' . get_admin_url() . 'edit.php?post_type=' . self::$postType . '&' . self::$taxonomy . '=' . $_term->slug . '">' . $_term->name . '</a>';
                    }
                    echo implode(", ", $terms);
                }
            } elseif ($column_name == 'sort-order') {
                echo esc_attr($item->menu_order);
            }
        }

        function getOptions() {
            return get_option('nimble-portfolio-config', array());
        }

        function setOptions($options = array()) {
            update_option('nimble-portfolio-config', $options);
        }

        function getTaxonomyMeta($term_id, $key) {
            if (!$term_id || !$key) {
                return null;
            }
            $options = self::getOptions();
            return $options['taxonomymeta'][$term_id][$key];
        }

        function updateTaxonomyMeta($term_id, $key, $val = null) {
            if (!$term_id || !$key) {
                return;
            }
            $options = self::getOptions();
            $options['taxonomymeta'][$term_id][$key] = $val;
            self::setOptions($options);
        }

        function taxonomyColumnHeader($columns) {
            $columns["sort-order"] = __("Sort Order", "nimble_portfolio_context");
            ;
            return $columns;
        }

        function taxonomyEditFormField() {
            ?>
            <tr class="form-field">
                <th valign="top" scope="row">
                    <label for="sort-order"><?php _e("Sort Order", "nimble_portfolio_context"); ?></label>
                </th>
                <td>
                    <input type="text" id="sort-order" name="sort-order" value="<?php echo self::getTaxonomyMeta($_GET["tag_ID"], 'sort-order'); ?>" style="width: 100px"/>
                    <p class="description"><?php _e('Set the sort order for your category here with 0 being the first and so on.', 'nimble_portfolio_context'); ?></p>
                </td>
            </tr>
            <?php
        }

        function taxonomyAddFormField() {
            ?>
            <div class="form-field">
                <label for="sort-order"><?php _e("Sort Order", "nimble_portfolio_context"); ?></label>
                <input type="text" id="sort-order" name="sort-order" style="width: 100px"/>
                <p><?php _e('Set the sort order for your category here with 0 being the first and so on.', 'nimble_portfolio_context'); ?></p>
            </div>        
            <?php
        }

        function saveTaxonomyValue($term_id) {
            if (isset($_POST['sort-order']))
                self::updateTaxonomyMeta($term_id, 'sort-order', $_POST['sort-order']);
        }

        function taxonomyCustomValue($empty, $custom_column, $term_id) {

            if ($custom_column == 'sort-order') {
                return self::getTaxonomyMeta($term_id, $custom_column);
            }
        }

        function taxonomyQuickEditField($column_name, $screen, $name = null) {
            if ($column_name == 'sort-order') {
                ?>  
                <fieldset>  
                    <div id="my-custom-content" class="inline-edit-col">  
                        <label>  
                            <span class="title"><?php _e("Sort Order", "nimble_portfolio_context"); ?></span>  
                            <span class="input-text-wrap"><input name="<?php echo $column_name; ?>" class="ptitle" value="" type="text"></span>  
                        </label>  
                    </div>  
                </fieldset>  
                <?php
            }
        }

    }

    add_action('widgets_init', create_function('', "register_widget('NimblePortfolioWidget');"));

    class NimblePortfolioWidget extends WP_Widget {

        function NimblePortfolioWidget() {
            $widget_ops = array('classname' => 'nimble-portfolio-widget', 'description' => 'Portfolio/Gallery grid widget');
            $control_ops = array('width' => 200, 'height' => 250, 'id_base' => 'nimble-portfolio-widget');
            $this->WP_Widget('nimble-portfolio-widget', 'Nimble Portfolio', $widget_ops, $control_ops);
        }

        function widget($args, $instance) {

            extract($args);

            echo $before_widget;

            NimblePortfolioPlugin::showPortfolio($instance);

            echo $after_widget;
        }

        function update($new_instance, $old_instance) {
            return $new_instance;
        }

        function form($instance) {


            $skins = apply_filters('nimble_portfolio_skin_register', array());
            ?>
            <p>
                <label for="<?php echo $this->get_field_id('skin'); ?>"><?php _e("Skin"); ?>:</label>
                <select id="<?php echo $this->get_field_id('skin'); ?>" name="<?php echo $this->get_field_name('skin'); ?>" style="width:95%;">
                    <?php foreach ($skins as $skin) { ?>
                        <option value="<?php echo $skin->name ?>" <?php selected($skin->name, $instance['skin']); ?>><?php echo $skin->label ?></option>
                    <?php } ?>
                </select>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('hide_filters'); ?>"><?php _e("Hide Filters"); ?>:</label>
                <input type="checkbox" id="<?php echo $this->get_field_id('hide_filters'); ?>" name="<?php echo $this->get_field_name('hide_filters'); ?>" value="1" <?php checked(1, (int) $instance['hide_filters']); ?> />
            </p>
            <?php
        }

    }

    NimblePortfolioPlugin::init();

    function nimble_portfolio($atts = array()) {
        return NimblePortfolioPlugin::getPortfolio($atts);
    }

    function nimble_portfolio_show($atts = array()) {

        NimblePortfolioPlugin::showPortfolio($atts);
    }

}
include("skins/default/skin.php"); // Includes default skin
