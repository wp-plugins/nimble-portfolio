<?php
if (!class_exists('NimblePortfolio')) {

    class NimblePortfolio {

        private $atts;
        private $skin;
        private $skinObj;
        private $skinUrl;
        private $skinPath;
        private $postType;
        private $taxonomy;
        private $items_ids;
        private $items;

        function __construct($atts = array(), $params = array()) {
            $atts['skin'] = isset($atts['skin']) && $atts['skin'] ? $atts['skin'] : 'default';
            $atts['hide_filters'] = isset($atts['hide_filters']) && $atts['hide_filters'] ? 1 : 0;
            $this->items = null;
            $this->atts = $atts;
            $this->skin = $atts['skin'];
            $this->skinObj = apply_filters("nimble_portfolio_skin_get_$this->skin", array());

            // set Skin Path/Url
            if (file_exists(get_template_directory() . "/nimble-portfolio/skins/$this->skin/skin.php")) {
                $this->skinUrl = get_template_directory_uri() . "/nimble-portfolio/skins/$this->skin/";
                $this->skinPath = get_template_directory() . "/nimble-portfolio/skins/$this->skin/";
            } else {

                if (isset($params['skinUrl']) && $params['skinUrl']) {
                    $this->skinUrl = $params['skinUrl'];
                } else {
                    $this->skinUrl = NimblePortfolioPlugin::getUrl('skins') . "$this->skin/";
                }

                if (isset($params['skinPath']) && $params['skinPath']) {
                    $this->skinPath = $params['skinPath'];
                } else {
                    $this->skinPath = NimblePortfolioPlugin::getPath('skins') . "$this->skin/";
                }
            }

            // set Post Type
            if (isset($params['postType']) && $params['postType']) {
                $this->postType = $params['postType'];
            } else {
                $this->postType = NimblePortfolioPlugin::getPostType();
            }

            //set Taxonomy
            if (isset($params['taxonomy']) && $params['taxonomy']) {
                $this->taxonomy = $params['taxonomy'];
            } else {
                $this->taxonomy = NimblePortfolioPlugin::getTaxonomy();
            }

            // construct WP_Query
            $this->items_ids = array();
            $args = array();
            $args['post_type'] = $this->postType;
            $args['posts_per_page'] = -1;
            $args['fields'] = 'ids';
            $args['orderby'] = 'menu_order';
            $args['order'] = 'ASC';
            $args['post_status'] = 'publish';

            $args = apply_filters('nimble_portfolio_query_args', $args);

            $np_query = new WP_Query($args);

            if ($np_query->have_posts()) {
                $this->items_ids = $np_query->get_posts();
            }

            wp_reset_postdata();
        }

        function renderTemplate() {
            ?>
            <link rel="stylesheet" type="text/css" href="<?php echo $this->skinUrl . "skin.css"; ?>" />
            <?php do_action('nimble-portfolio-template-css', $this->atts); ?>

            <div class="nimble-portfolio <?php echo apply_filters("nimble_portfolio_skin_classes", "-skin-$this->skin", $this->skin); ?>">

                <?php do_action('nimble_portfolio_skin_before', $this->atts); ?>

                <?php if (!$this->atts['hide_filters']) { ?>
                    <div class="-filters <?php echo apply_filters("nimble_portfolio_skin_filters_classes", "", $this->skin); ?>">
                        <?php echo apply_filters('nimble_portfolio_filter_all', sprintf('<a href="#" rel="*" class="-filter active">%s</a>', __("All"))); ?>
                        <?php require ($this->skinPath . "filters.php"); ?>
                    </div>        
                <?php } ?>

                <?php do_action('nimble_portfolio_skin_between', $this->atts); ?>

                <div class="-items <?php echo apply_filters("nimble_portfolio_skin_items_classes", "", $this->skin); ?>">
                    <?php require ($this->skinPath . "items.php"); ?>
                </div>

                <?php do_action('nimble_portfolio_skin_after', $this->atts); ?>

            </div>        
            <?php
        }

        function getItems($flag_idonly = false) {

            if ($flag_idonly) {
                return $this->items_ids;
            }

            if ($this->items !== null) {
                return $this->items;
            }

            $this->items = array();
            foreach ($this->items_ids as $item_id) {
                $this->items[] = new NimblePortfolioItem($item_id);
            }

            return $this->items;
        }

        function getFilters($args = array()) {

            $args['taxonomy'] = $this->taxonomy;
            $filters = get_categories($args);

            return $filters;
        }

        function getSkin() {
            return $this->skinObj;
        }

    }

}