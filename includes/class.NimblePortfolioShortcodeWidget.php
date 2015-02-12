<?php
if (!class_exists('NimblePortfolioShortcodeWidget')) {

    class NimblePortfolioShortcodeWidget extends WP_Widget {

        function NimblePortfolioShortcodeWidget() {
            $widget_ops = array('classname' => 'nimble-portfolio-shortcode-widget', 'description' => 'Shortcode support in the widget');
            $control_ops = array('width' => 400, 'height' => 250, 'id_base' => 'nimble-portfolio-shortcode-widget');
            $this->WP_Widget('nimble-portfolio-shortcode-widget', 'Nimble Portfolio &mdash; Shortcode', $widget_ops, $control_ops);
        }

        public static function init() {
            add_action('widgets_init', create_function('', "register_widget('NimblePortfolioShortcodeWidget');"));
        }

        function widget($args, $instance) {

            extract($args);

            echo $before_widget;

            echo do_shortcode($instance['shortcode']);

            echo $after_widget;
        }

        function update($new_instance, $old_instance) {
            return $new_instance;
        }

        function form($instance) {
            ?>
            <p class="nimble-portfolio-widget-field">
                <label for="<?php echo $this->get_field_id('shortcode'); ?>"><?php _e("Shortcode", 'nimble_portfolio'); ?></label>
                <textarea id="<?php echo $this->get_field_id('shortcode'); ?>" name="<?php echo $this->get_field_name('shortcode'); ?>" rows="10" cols="20" class="widefat"><?php echo  $instance['shortcode'] ?></textarea>
            </p>
            <?php
        }

    }

    NimblePortfolioShortcodeWidget::init();
}