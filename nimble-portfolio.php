<?php
/*
  Plugin Name: Nimble Portfolio
  Plugin URI: http://nimble3.com/demo/nimble-portfolio-free/
  Description: Using this free plugin you can transform your portfolio in to a cutting edge jQuery powered gallery that lets you feature and sort your work like a pro.
  Version: 2.1.1
  Author: Nimble3
  Author URI: http://www.nimble3.com/
  License: GPLv2 or later
 */

include("includes/class.NimblePortfolio.php");
include("includes/class.NimblePortfolioItem.php");
include("includes/class.NimblePortfolioSkin.php");
include("includes/class.NimblePortfolioShortcodeWidget.php");
include("includes/class.NimblePortfolioRecentItemsWidget.php");

if (!class_exists('NimblePortfolioPlugin')) {

    class NimblePortfolioPlugin {

        static private $version;
        static private $postType;
        static private $postTypeSlug;
        static private $taxonomy;
        static private $taxonomySlug;
        static private $dirPath;
        static private $dirUrl;
        static private $options;

        static function init($params = array()) {
            self::$version = '2.1.1';
            self::$postType = 'portfolio';
            self::$postTypeSlug = apply_filters('nimble_portfolio_posttype_slug', 'portfolio');
            self::$taxonomy = 'nimble-portfolio-type';
            self::$taxonomySlug = apply_filters('nimble_portfolio_taxonomy_slug', 'portfolio-filter');
            self::$dirPath = dirname(__FILE__);
            self::$dirUrl = self::path2url(self::$dirPath);
            self::$options = null;

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
            add_action('current_screen', array(__CLASS__, 'adminScreen'));
            add_action('save_post', array(__CLASS__, 'updateData'), 1, 2);

            add_action('wp_ajax_nimble_portfolio_tinymce', array(__CLASS__, 'ajaxTinymceShortcodeParams'));
            add_action('wp_ajax_nimble_portfolio_tinymce_skin_change', array(__CLASS__, 'ajaxTinymceSkinChange'));
            add_action('wp_ajax_nimble_portfolio_tinymce_post_type_change', array(__CLASS__, 'ajaxTinymcePostTypeChange'));

            add_action('wp_ajax_nimble_portfolio_shortcode_skin_change', array(__CLASS__, 'ajaxShortcodeGenSkinChange'));
            add_action('wp_ajax_nimble_portfolio_shortcode_post_type_change', array(__CLASS__, 'ajaxShortcodeGenPostTypeChange'));

            do_action('nimble_portfolio_init');
        }

        static function path2url($path) {
            if (!defined('ABSPATH')) {
                return false;
            }
            return trim(site_url(), '/\\') . "/" . str_replace("\\", "/", trim(substr_replace($path, '', 0, strlen(ABSPATH)), '/'));
        }

        static function phpvar2htmlatt($atts) {
            $return = ' ';
            if (is_array($atts) && count($atts)) {
                foreach ($atts as $att => $val) {
                    $return .= $att . '="' . (is_array($val) ? implode(" ", $val) : $val) . '" ';
                }
            }
            return $return;
        }

        static function getVersion() {
            return self::$version;
        }

        static function getPostType() {
            return self::$postType;
        }

        static function getPostTypeSlug() {
            return self::$postTypeSlug;
        }

        static function getTaxonomy() {
            return self::$taxonomy;
        }

        static function getTaxonomySlug() {
            return self::$taxonomySlug;
        }

        static function getPath($tail) {
            $tail = trim($tail, '/');
            return self::$dirPath . ($tail ? "/$tail/" : "/");
        }

        static function getUrl($tail) {
            $tail = trim($tail, '/');
            return self::$dirUrl . ($tail ? "/$tail/" : "/");
        }

        static function updateData($post_id, $post) {

            if (!wp_verify_nonce(@$_POST['nimble_portfolio_noncename'], plugin_basename(__FILE__))) {
                return;
            }

            if ($post->post_type == 'revision') {
                return;
            }

            if (!current_user_can('edit_post', $post->ID)) {
                return;
            }

            $mydata = array();
            $mydata['nimble-portfolio'] = $_POST['nimble_portfolio'];
            $mydata['nimble-portfolio-url'] = $_POST['nimble_portfolio_url'];

            $mydata = apply_filters('nimble_portfolio_update_data', $mydata);

            foreach ($mydata as $key => $value) { //Let's cycle through the $mydata array!
                update_post_meta($post->ID, $key, $value);
                if (!$value)
                    delete_post_meta($post->ID, $key); //delete if blank
            }
        }

        static function renameMenuTitle($safe_text, $text) {
            if (__('Portfolio Items', 'nimble_portfolio_context') !== $text) {
                return $safe_text;
            }

            remove_filter('attribute_escape', 'renameMenuTitle');

            return __('Nimble Portfolio', 'nimble_portfolio_context');
        }

        static function onActivate() {
            self::registerPostType();
            flush_rewrite_rules();
        }

        static function onDeactivate() {
            flush_rewrite_rules();
        }

        static function registerPostType() {

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

        static function registerTaxonomy($postType = null) {

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

        static function tinymceShortcodeButton() {

            if (!current_user_can('edit_posts') && !current_user_can('edit_pages') && get_user_option('rich_editing') == 'true') {
                return;
            }

            add_filter("mce_external_plugins", array(__CLASS__, "registerTinymcePlugin"));
            add_filter('mce_buttons', array(__CLASS__, 'registerTinymceButton'));
        }

        static function registerTinymcePlugin($plugin_array) {
            $plugin_array['nimble_portfolio_button'] = self::$dirUrl . '/includes/tinymce-plugin.js';
            return $plugin_array;
        }

        static function registerTinymceButton($buttons) {
            $buttons[] = "nimble_portfolio_button";
            return $buttons;
        }

        static function ajaxTinymceShortcodeParams() {
            include "includes/tinymce-plugin.php";
            exit;
        }

        static function ajaxTinymceSkinChange() {
            do_action('nimble_portfolio_tinymce_skin_change', $_GET['skin']);
            exit;
        }

        static function ajaxTinymcePostTypeChange() {
            $post_type = $_GET['post_type'];
            $all_taxonomies = get_taxonomies(array('public' => true), 'names');
            $taxonomies = get_object_taxonomies($post_type, 'objects');
            if (count($taxonomies)) {
                ?>
                <label for="nimble_portfolio_tinymce_taxonomy"><?php _e("Filters Type (Taxonomy)", 'nimble_portfolio'); ?>:</label>
                <select id="nimble_portfolio_tinymce_taxonomy" name="nimble_portfolio_tinymce_taxonomy">
                    <?php
                    foreach ($taxonomies as $taxonomy => $taxonomy_obj) {
                        if (!in_array($taxonomy, $all_taxonomies)) {
                            continue;
                        }
                        ?>
                        <option value="<?php echo $taxonomy ?>"><?php echo "$taxonomy_obj->label ($taxonomy)"; ?></option>
                    <?php } ?>
                </select>
                <?php
            } else {
                _e("No taxonomy found under <strong>$post_type</strong> post type.", "nimble_portfolio");
            }
            exit;
        }

        static function ajaxShortcodeGenSkinChange() {
            do_action('nimble_portfolio_shortcode_skin_change', $_GET['skin']);
            exit;
        }

        static function ajaxShortcodeGenPostTypeChange() {
            $post_type = $_GET['post_type'];
            $all_taxonomies = get_taxonomies(array('public' => true), 'names');
            $taxonomies = get_object_taxonomies($post_type, 'objects');
            $option_set = "";
            if (count($taxonomies)) {
                foreach ($taxonomies as $taxonomy => $taxonomy_obj) {
                    if (!in_array($taxonomy, $all_taxonomies)) {
                        continue;
                    }
                    $option_set .= "<option value='$taxonomy' " . selected($taxonomy, 'nimble-portfolio-type', false) . " >$taxonomy_obj->label ($taxonomy)</option>";
                }
            }
            ?>
            <label for="nimble_portfolio_shortcode_taxonomy"><?php _e("Filters Type (Taxonomy)", 'nimble_portfolio'); ?>:</label>
            <select id="nimble_portfolio_shortcode_taxonomy" name="nimble_portfolio_shortcode_taxonomy" <?php disabled($option_set, ""); ?>>
                <?php
                if ($option_set) {
                    echo $option_set;
                } else {
                    ?>
                    <option value=""><?php _e("No Taxnomy Found under the Post Type!", "nimble_portfolio"); ?></option>
                <?php } ?>
            </select>
            <?php
            exit;
        }

        static function getPortfolio($atts) {
            ob_start();
            self::showPortfolio($atts);
            $content = ob_get_clean();
            return $content;
        }

        static function showPortfolio($atts = array()) {

            $nimble_portfolio = new NimblePortfolio($atts);

            $nimble_portfolio->renderTemplate();
        }

        static function enqueueStyle() {

            $nimblebox = apply_filters('nimble_portfolio_lightbox_style', self::getUrl('includes') . "prettyphoto/prettyphoto.css");
            if ($nimblebox) {
                wp_enqueue_style('nimblebox-style', $nimblebox);
            }

            $nimblesort = apply_filters('nimble_portfolio_sort_style', '');
            if ($nimblesort) {
                wp_enqueue_style('nimblesort-style', $nimblesort);
            }

            wp_enqueue_style('nimble-portfolio-style', file_exists(get_template_directory() . "/nimble-portfolio/nimble-portfolio.css") ? get_template_directory_uri() . "/nimble-portfolio/nimble-portfolio.css" : self::getUrl('includes') . "nimble-portfolio.css");

            do_action('nimble_portfolio_enqueue_style');
        }

        static function enqueueScript() {

            $nimblebox = apply_filters('nimble_portfolio_lightbox_script', self::getUrl('includes') . "prettyphoto/prettyphoto.js");
            if ($nimblebox) {
                wp_enqueue_script('nimblebox-script', $nimblebox, array('jquery'), self::$version);
            }

            $nimblesort = apply_filters('nimble_portfolio_sort_script', self::getUrl('includes') . 'sort.js');
            if ($nimblesort) {
                wp_enqueue_script('nimblesort-script', $nimblesort, array('jquery'), self::$version);
            }

            $nimblesort = apply_filters('nimble_portfolio_sort_script', self::getUrl('includes') . 'nimble-portfolio.js');
            do_action('nimble_portfolio_enqueue_script');
        }

        static function adminHead() {
            wp_enqueue_style('nimble-portfolio-admin', self::getUrl('includes') . "admin.css", null, null, "all");
            wp_enqueue_style('nimble-portfolio-spectrum', self::getUrl('includes') . "spectrum/spectrum.css", null, null, "all");
            wp_register_script('nimble-portfolio-spectrum', self::getUrl('includes') . 'spectrum/spectrum.js', array('jquery'), '1.6.0');
            wp_register_script('nimble-portfolio-admin', self::getUrl('includes') . 'admin.js', array('jquery', 'nimble-portfolio-spectrum'), self::$version);
            wp_enqueue_script('nimble-portfolio-spectrum');
            wp_enqueue_script('nimble-portfolio-admin');
        }

        static function adminScreen() {

            $currentScreen = get_current_screen();

            if ($currentScreen->post_type === "portfolio" && $currentScreen->base === $currentScreen->id) {
                require_once("includes/class.NimblePortfolioLessC.php");
            }
        }

        static function adminOptions() {
            add_submenu_page('edit.php?post_type=' . self::$postType, 'Generate Shortcode', 'Generate Shortcode', 'manage_options', 'nimble-portfolio-gen-shortcode', array(__CLASS__, 'shortcodeGeneratorPage'));
            add_submenu_page('edit.php?post_type=' . self::$postType, 'Global Settings', 'Global Settings', 'manage_options', 'nimble-portfolio-global-settings', array(__CLASS__, 'globalSettings'));
            do_action('nimble_portfolio_create_section_before', self::$postType);
            add_meta_box('nimble-portfolio-section-options', __('Options', 'nimble_portfolio_context'), array(__CLASS__, 'renderOptions'), self::$postType, 'normal', 'high');
            do_action('nimble_portfolio_create_section_after', self::$postType);
        }

        static function shortcodeGeneratorPage() {
            include("includes/shortcode-generator.php");
        }

        static function globalSettings() {
            include("includes/global-settings.php");
        }

        static function renderOptions($post) {
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

        static function adminPostsColumns($cols) {
            $cols['filters'] = __('Filters', 'nimble_portfolio_context');
            $cols['thumbnail'] = __('Thumbnail', 'nimble_portfolio_context');
            $cols['sort-order'] = __('Sort Order', 'nimble_portfolio_context');
            return $cols;
        }

        static function adminPostsCustomColumn($column_name) {
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

        static function getOptions() {
            if (self::$options === null) {
                self::$options = get_option('nimble-portfolio-config', array());
            }
            return self::$options;
        }

        static function setOptions($options = array()) {
            update_option('nimble-portfolio-config', $options);
            self::$options = $options;
        }

        static function getGlobalSettings() {
            if (self::$options === null) {
                self::$options = self::getOptions();
            }
            return self::$options['global-settings'];
        }

        static function getTaxonomyMeta($term_id, $key) {
            if (!$term_id || !$key) {
                return null;
            }
            $options = self::getOptions();
            return @$options['taxonomymeta'][$term_id][$key];
        }

        static function updateTaxonomyMeta($term_id, $key, $val = null) {
            if (!$term_id || !$key) {
                return;
            }
            $options = self::getOptions();
            $options['taxonomymeta'][$term_id][$key] = $val;
            self::setOptions($options);
        }

        static function taxonomyColumnHeader($columns) {
            $columns["sort-order"] = __("Sort Order", "nimble_portfolio_context");
            return $columns;
        }

        static function taxonomyEditFormField() {
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

        static function taxonomyAddFormField() {
            ?>
            <div class="form-field">
                <label for="sort-order"><?php _e("Sort Order", "nimble_portfolio_context"); ?></label>
                <input type="text" id="sort-order" name="sort-order" style="width: 100px"/>
                <p><?php _e('Set the sort order for your category here with 0 being the first and so on.', 'nimble_portfolio_context'); ?></p>
            </div>        
            <?php
        }

        static function saveTaxonomyValue($term_id) {
            if (isset($_POST['sort-order']))
                self::updateTaxonomyMeta($term_id, 'sort-order', $_POST['sort-order']);
        }

        static function taxonomyCustomValue($empty, $custom_column, $term_id) {

            if ($custom_column == 'sort-order') {
                return self::getTaxonomyMeta($term_id, $custom_column);
            }
        }

        static function taxonomyQuickEditField($column_name, $screen, $name = null) {
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

    NimblePortfolioPlugin::init();

    function nimble_portfolio($atts = array()) {
        return NimblePortfolioPlugin::getPortfolio($atts);
    }

    function nimble_portfolio_show($atts = array()) {
        NimblePortfolioPlugin::showPortfolio($atts);
    }

}

include("skins/default/skin.php"); // Includes default skin
