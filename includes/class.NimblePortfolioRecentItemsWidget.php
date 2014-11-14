<?php
if (!class_exists('NimblePortfolioRecentItemsWidget')) {

    class NimblePortfolioRecentItemsWidget extends WP_Widget {

        function NimblePortfolioRecentItemsWidget() {
            $widget_ops = array('classname' => 'nimble-portfolio-recent-items-widget', 'description' => 'Display recent portfolio items in List style');
            $control_ops = array('width' => 250, 'height' => 250, 'id_base' => 'nimble-portfolio-recent-items-widget');
            $this->WP_Widget('nimble-portfolio-recent-items-widget', 'Nimble Portfolio &mdash; Recent Items', $widget_ops, $control_ops);
        }

        public static function init() {
            add_action('widgets_init', create_function('', "register_widget('NimblePortfolioRecentItemsWidget');"));
        }

        function widget($args, $instance) {

            extract($args);

            echo $before_widget;

            $instance['orderby'] = 'date';
            $instance['order'] = 'DESC';
            $instance['hide_filters'] = 'DESC';

            $instance['showposts'] = isset($instance['showposts']) && $instance['showposts'] ? $instance['showposts'] : 5;

            echo '<h3 class="widget-title">' . $instance['title'] . "</h3>";
            unset($instance['title']);
            $portfolioObj = new NimblePortfolio($instance);
            $portfolioObj->renderTemplateFile("recent-items.php");

            echo $after_widget;
        }

        function update($new_instance, $old_instance) {
            return $new_instance;
        }

        function form($instance) {
            $instance['title'] = isset($instance['title']) && $instance['title'] ? $instance['title'] : __('Recent Portfolio Items', 'nimble_portfolio');
            $instance['showposts'] = isset($instance['showposts']) && $instance['showposts'] ? $instance['showposts'] : 5;
            ?>
            <p class="nimble-portfolio-widget-field">
                <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e("Title", 'nimble_portfolio'); ?></label>
                <input id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $instance['title'] ?>" />
            </p>
            <p class="nimble-portfolio-widget-field">
                <label for="<?php echo $this->get_field_id('showposts'); ?>"><?php _e("No. of Items to show", 'nimble_portfolio'); ?></label>
                <input id="<?php echo $this->get_field_id('showposts'); ?>" name="<?php echo $this->get_field_name('showposts'); ?>" value="<?php echo $instance['showposts'] ?>" />
            </p>
            <p class="nimble-portfolio-widget-field">
                <label for="<?php echo $this->get_field_id('_widget_round_thumb'); ?>"><?php _e("Round Thumbnail?", 'nimble_portfolio'); ?></label>
                <input type="checkbox" id="<?php echo $this->get_field_id('_widget_round_thumb'); ?>" name="<?php echo $this->get_field_name('_widget_round_thumb'); ?>" value="1" <?php echo checked($instance['_widget_round_thumb'], 1); ?>/>
            </p>
            <?php
        }

    }

    NimblePortfolioRecentItemsWidget::init();
}