<?php
if (!class_exists('NimblePortfolio')) {

    class NimblePortfolio {

        private $ID;
        private $atts;
        private $skin;
        private $skinObj;
        private $postType;
        private $taxonomy;
        private $items_ids;
        private $items;
        private $queryObj;

        function __construct($atts = array(), $params = array()) {
            $atts['skin'] = isset($atts['skin']) && $atts['skin'] ? $atts['skin'] : 'default';
            $atts['hide_filters'] = isset($atts['hide_filters']) && $atts['hide_filters'] ? 1 : 0;
            $atts['orderby'] = isset($atts['orderby']) && $atts['orderby'] ? $atts['orderby'] : 'menu_order';
            $atts['order'] = isset($atts['order']) && $atts['order'] ? $atts['order'] : 'ASC';
            $atts['maxposts'] = isset($atts['maxposts']) && $atts['maxposts'] ? $atts['maxposts'] : 20;
            $this->ID = isset($atts['id']) && $atts['id'] ? $atts['id'] : uniqid("np-");
            $this->items = null;
            $this->atts = $atts;
            $this->skin = $atts['skin'];
            $this->skinObj = apply_filters("nimble_portfolio_skin_get_$this->skin", null);

            // set Post Type
            if (isset($params['post_type']) && $params['post_type']) {
                $this->postType = $params['post_type'];
            } else {
                $this->postType = isset($atts['post_type']) && $atts['post_type'] ? $atts['post_type'] : NimblePortfolioPlugin::getPostType();
            }

            //set Taxonomy
            if (isset($params['taxonomy']) && $params['taxonomy']) {
                $this->taxonomy = $params['taxonomy'];
            } else {
                $this->taxonomy = isset($atts['taxonomy']) ? $atts['taxonomy'] : NimblePortfolioPlugin::getTaxonomy();
            }

            // construct WP_Query
            $this->items_ids = array();
            $args = array();
            $args['post_type'] = $this->postType;
            $args['posts_per_page'] = -1;
            $args['fields'] = 'ids';
            $args['orderby'] = $atts['orderby'];
            $args['order'] = $atts['order'];
            $args['post_status'] = 'publish';

            if (isset($atts['showposts']) && $atts['showposts']){
                $args['showposts'] = $atts['showposts'];
            }

            $args = apply_filters('nimble_portfolio_query_args', $args, $this);

            $this->queryObj = new WP_Query($args);

            if ($this->queryObj->have_posts()) {
                $this->items_ids = $this->queryObj->get_posts();
            }

            wp_reset_postdata();
        }

        public function __get($name) {
            return $this->$name;
        }

        public function __set($name, $value) {
            return;
        }

        function renderTemplate() {
            $global_settings = NimblePortfolioPlugin::getGlobalSettings();
            $loader_flag = isset($global_settings['loader_flag']) && $global_settings['loader_flag'];

            do_action('nimble-portfolio-template-css', $this);
            ?>
            <div class="nimble-portfolio <?php echo $loader_flag ? "-isloading" : ""; ?> <?php echo apply_filters("nimble_portfolio_skin_classes", "-skin-$this->skin", $this); ?>" id="<?php echo $this->ID; ?>">

                <?php if ($loader_flag) { ?>
                    <div class="-loading"><div class="-loader">Loading...</div></div>
                <?php } ?>

                <?php do_action('nimble_portfolio_skin_before', $this); ?>

                <?php if (!$this->atts['hide_filters']) { ?>
                    <div class="-filters <?php echo apply_filters("nimble_portfolio_skin_filters_classes", "", $this); ?>">
                        <?php echo apply_filters('nimble_portfolio_filter_all', sprintf('<a href="#" rel="*" class="-filter active">%s</a>', __("All", "nimble_portfolio"))); ?>
                        <?php $this->renderTemplateFile("filters.php"); ?>
                    </div>        
                <?php } ?>

                <?php do_action('nimble_portfolio_skin_between', $this); ?>

                <div class="-items <?php echo apply_filters("nimble_portfolio_skin_items_classes", "", $this); ?>">
                    <?php $this->renderTemplateFile("items.php"); ?>
                </div>

                <?php do_action('nimble_portfolio_skin_after', $this); ?>

            </div>        
            <?php
        }

        function renderTemplateFile($file) {
            require ($this->skinObj->getTemplatePath($file));
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

            if (!$this->taxonomy) {
                return array();
            }

            $args['taxonomy'] = $this->taxonomy;
            $_filters = get_categories($args);

            $term_sort_orders = array();
            $_terms = array();
            foreach ($_filters as $_filter) {
                $_terms[$_filter->slug] = $_filter;
                $term_sort_orders[$_filter->slug] = NimblePortfolioPlugin::getTaxonomyMeta($_filter->term_id, 'sort-order');
            }

            asort($term_sort_orders, SORT_NUMERIC);

            $filters = array();
            foreach ($term_sort_orders as $slug => $order) {
                $filters[] = $_terms[$slug];
            }

            return $filters;
        }

        function getSkin() {
            return $this->skinObj;
        }

    }

}