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

    }

}