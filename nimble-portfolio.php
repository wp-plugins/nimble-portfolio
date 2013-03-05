<?php
/*
  Plugin Name: Nimble Portfolio
  Plugin URI: http://www.nimble3.com/portfolio-demo
  Description: Using this free plugin you can transform your portfolio in to a cutting edge jQuery powered gallery that lets you feature and sort your work like a pro.
  Version: 1.2.4
  Author: Nimble3
  Author URI: http://www.nimble3.com/
  License: GPLv2 or later
 */

define('NIMBLE_PORTFOLIO_DIR', dirname(__FILE__));
define('NIMBLE_PORTFOLIO_TEMPLATES_DIR', NIMBLE_PORTFOLIO_DIR . "/templates");
define('NIMBLE_PORTFOLIO_INCLUDES_DIR', NIMBLE_PORTFOLIO_DIR . "/includes");
define('NIMBLE_PORTFOLIO_URL', WP_PLUGIN_URL . "/" . basename(NIMBLE_PORTFOLIO_DIR));
define('NIMBLE_PORTFOLIO_TEMPLATES_URL', NIMBLE_PORTFOLIO_URL . "/templates");
define('NIMBLE_PORTFOLIO_INCLUDES_URL', NIMBLE_PORTFOLIO_URL . "/includes");

add_theme_support('post-thumbnails', array('portfolio'));

function nimble_portfolio_get_meta($field) {
    global $post;
    $custom_field = get_post_meta($post->ID, $field, true);
    return $custom_field;
}

register_activation_hook(__FILE__, 'nimble_portfolio_activate');

function nimble_portfolio_activate() {
    nimble_portfolio_register();
    flush_rewrite_rules();
}

register_deactivation_hook(__FILE__, 'nimble_portfolio_deactivate');

function nimble_portfolio_deactivate() {
    flush_rewrite_rules();
}

// Register Portfolio post type
add_action('init', 'nimble_portfolio_register');

function nimble_portfolio_register() {

    $labels = array(
        'name' => __('Portfolio Items'),
        'singular_name' => __('Portfolio Item'),
        'add_new' => __('Add Portfolio Item'),
        'add_new_item' => __('Add New Portfolio Item'),
        'edit_item' => __('Edit Portfolio Item'),
        'new_item' => __('New Portfolio Item'),
        'view_item' => __('View Portfolio Item'),
        'search_items' => __('Search Portfolio Item'),
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
        'rewrite' => true,
        'supports' => array(
            'title',
            'thumbnail',
            'editor',
            'excerpt',
            //'author',
            //'trackbacks',
            //'custom-fields',
            //'comments', 
            //'revisions', 
            //'page-attributes'
        ),
        'menu_position' => 23,
        'menu_icon' => NIMBLE_PORTFOLIO_URL.'/icon.png',
        'taxonomies' => array('nimble-portfolio-type')
    );

    register_post_type('portfolio', $args);
}

add_action('init', 'nimble_portfolio_register_taxonomies', 0);

function nimble_portfolio_register_taxonomies() {
    register_taxonomy('nimble-portfolio-type', 'portfolio', array('hierarchical' => true, 'label' => 'Item Type', 'query_var' => true, 'rewrite' => true));

    if (count(get_terms('nimble-portfolio-type', 'hide_empty=0')) == 0) {
        register_taxonomy('type', 'portfolio', array('hierarchical' => true, 'label' => 'Item Type'));
        $_categories = get_categories('taxonomy=type&title_li=');
        foreach ($_categories as $_cat) {
            if (!term_exists($_cat->name, 'nimble-portfolio-type'))
                wp_insert_term($_cat->name, 'nimble-portfolio-type');
        }
        $portfolio = new WP_Query(array('post_type' => 'portfolio', 'posts_per_page' => '-1'));
        while ($portfolio->have_posts()) : $portfolio->the_post();
            $post_id = get_the_ID();
            $_terms = wp_get_post_terms($post_id, 'type');
            $terms = array();
            foreach ($_terms as $_term) {
                $terms[] = $_term->term_id;
            }
            wp_set_post_terms($post_id, $terms, 'nimble-portfolio-type');
        endwhile;
        wp_reset_query();
        register_taxonomy('type', array());
    }
}

add_filter('attribute_escape', 'rename_second_menu_name', 10, 2);

function rename_second_menu_name($safe_text, $text) {
    if (__('Portfolio Items', 'nimble_portfolio_context') !== $text) {
        return $safe_text;
    }

    // We are on the main menu item now. The filter is not needed anymore.
    remove_filter('attribute_escape', 'rename_second_menu_name');

    return __('Nimble Portfolio', 'nimble_portfolio_context');
}

// Register custom JS scripts
add_action('init', 'nimble_portfolio_enqueue_scripts');

function nimble_portfolio_enqueue_scripts() {

    wp_register_script('prettyphoto', NIMBLE_PORTFOLIO_INCLUDES_URL . '/prettyphoto/jquery.prettyPhoto.js', 'jquery');
    wp_register_script('nimble_portfolio_scripts', NIMBLE_PORTFOLIO_INCLUDES_URL . '/scripts.js', 'jquery');

    wp_enqueue_script('jquery');
    wp_enqueue_script('prettyphoto');
    wp_enqueue_script('nimble_portfolio_scripts');
}

add_action('init', 'nimble_portfolio_enqueue_styles');

function nimble_portfolio_enqueue_styles() {
    wp_enqueue_style('prettyphoto_style', NIMBLE_PORTFOLIO_INCLUDES_URL . "/prettyphoto/prettyPhoto.css", null, null, "screen");
}

add_action('admin_head', 'nimble_portfolio_write_adminstyle');

function nimble_portfolio_write_adminstyle() {
    ?><style type="text/css">
        .nimble-portfolio-meta-section input[type="text"] {
            width: 98%;
        }
        .nimble-portfolio-meta-section input{
            margin-top: 5px;
        }
    </style><?php
}

add_shortcode('nimble-portfolio', 'nimble_portfolio');

function nimble_portfolio($atts=array()) {
    ob_start();
    nimble_portfolio_show($atts);
    $content = ob_get_clean();
    return $content;
}

function nimble_portfolio_show($atts=array()) {

    $template_code = $atts;
    if (isset($atts["template"]) && $atts["template"])
        $template_code = $atts["template"];

    if (!$template_code)
        $template_code = "3colround";

    require (NIMBLE_PORTFOLIO_TEMPLATES_DIR . "/$template_code/template.php");
}

function nimble_portfolio_list_categories() {
    $_categories = get_categories('taxonomy=nimble-portfolio-type');
    foreach ($_categories as $_cat) {
        ?>
        <li class="cat-item cat-item-<?php echo $_cat->term_id; ?>">
            <a title="View all posts filed under <?php echo $_cat->name; ?>" href="<?php echo get_term_link($_cat->slug, 'nimble-portfolio-type'); ?>" rel="<?php echo $_cat->slug; ?>"><?php echo $_cat->name; ?></a>
        </li>
        <?php
    }
}

function nimble_portfolio_get_item_classes($post_id = null) {
    if ($post_id === null)
        return;
    $_terms = wp_get_post_terms($post_id, 'nimble-portfolio-type');
    foreach ($_terms as $_term) {
        echo " " . $_term->slug;
    }
}

// Add the Panels
include("includes/add-meta-boxes.php");
