<?php

if (class_exists('NimblePortfolioSkin') && !class_exists('NimblePortfolioSkinDefault')) {

    class NimblePortfolioSkinDefault extends NimblePortfolioSkin {

        function __construct() {
            $skin_default = array();
            $skin_default['readmore-flag'] = 1;
            $skin_default['readmore-text'] = "Read More &rarr;";
            $skin_default['viewproject-flag'] = 1;
            $skin_default['viewproject-text'] = "View Project &rarr;";
            $skin_default['skin-type'] = 'round';
            $skin_default['column-type'] = '-columns3';
            $skin_default['hover-icon'] = 'zoom';
            parent::__construct('default', 'Default', dirname(__FILE__) . '/', $skin_default);
            add_action('admin_head', array($this, 'admin_head'));
            add_filter('nimble_portfolio_skin_classes', array($this, 'skin_filters_classes'), 10, 2);
        }

        function admin_head() {
            wp_enqueue_style('genericons-css', $this->url . "/genericon/genericons.css");
        }

        function skin_filters_classes($classes, $portfolioObj) {
            $name = $portfolioObj->skinObj->name;
            $options = $this->getOptions();
            $skin_cols = $options['column-type'];
            $skin_type = $options['skin-type'];
            return ($name === $this->name ? " $classes -skin-default-$skin_type $skin_cols " : $classes);
        }

    }

}

new NimblePortfolioSkinDefault();
