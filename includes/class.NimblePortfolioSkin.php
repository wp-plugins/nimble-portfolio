<?php

if (!class_exists('NimblePortfolioSkin')) {

    class NimblePortfolioSkin {

        private $name;
        private $label;
        private $path;
        private $url;
        private $defaults;

        function __construct($name, $label, $path, $defaults = array()) {

            $this->name = $name;
            $this->label = $label;
            $this->path = $path;
            $this->url = NimblePortfolioPlugin::path2url($path);
            $this->defaults = $defaults;

            add_filter('nimble_portfolio_skin_register', array($this, 'register'));
            add_filter('nimble_portfolio_skin_get_' . $this->name, array($this, 'get'));
            add_action('nimble_portfolio_create_section_before', array($this, 'registerMenu'));

            add_action('nimble_portfolio_enqueue_style', array($this, 'enqueueStyle'));
        }

        public function __get($name) {
            return $this->$name;
        }

        public function get() {
            return $this;
        }

        function register($skins) {
            $skins[] = $this;
            return $skins;
        }

        function getTemplatePath($filepath = "") {
            if (file_exists(get_template_directory() . "/nimble-portfolio/skins/$this->name/$filepath")) {
                return get_template_directory() . "/nimble-portfolio/skins/$this->name/$filepath";
            }
            return $this->path . "/$filepath";
        }

        function getTemplateUrl($filepath = "") {
            if (file_exists(get_template_directory() . "/nimble-portfolio/skins/$this->name/$filepath")) {
                return get_template_directory_uri() . "/nimble-portfolio/skins/$this->name/$filepath";
            }
            return $this->url . "/$filepath";
        }

        function getOptions() {
            $options = NimblePortfolioPlugin::getOptions();
            $skin_options = @$options["-skin-" . $this->name];
            if (empty($skin_options)) {
                foreach ($this->defaults as $key => $value) {
                    $skin_options[$key] = $value;
                }
            }
            return $skin_options;
        }

        function setOptions($skin_options = array()) {
            $options = NimblePortfolioPlugin::getOptions();
            $options["-skin-" . $this->name] = $skin_options;
            NimblePortfolioPlugin::setOptions($options);
        }

        function registerMenu($post_type) {
            add_submenu_page('edit.php?post_type=' . ($post_type ? $post_type : 'portfolio'), 'Skin Settings: ' . $this->label, 'Skin: ' . $this->name, 'manage_options', 'nimble-portfolio-skin-setting-' . $this->name, array($this, 'adminConfigPage'));
        }

        function adminConfigPage() {
            include ( $this->path . 'config.php');
        }

        function enqueueStyle() {
            wp_enqueue_style("nimble-portfolio-$this->name-stylesheet", $this->getTemplateUrl("skin.css"));
        }

    }

}