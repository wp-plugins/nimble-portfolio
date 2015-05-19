<?php
$options = self::getOptions();
$settings = @$options['global-settings'];
if (isset($_POST['nimble_portfolio_save'])) {
    $settings = @$_POST['settings'];
    $options['global-settings'] = $settings;
    if (@$settings['loader_flag']) {
        try {
            $less = new NimblePortfolioLessC();
            $less->setVariables(array(
                "loader_color" => $settings['loader_color'],
                "loader_size" => $settings['loader_size']
            ));
            if ($less->compileFile(self::getPath("includes") . "nimble-portfolio.less", get_template_directory() . "/nimble-portfolio/nimble-portfolio.css") === false) {
                echo "<div class='error'><p><strong>LESS Compiler:</strong> <span style='color:red'>" . get_template_directory() . "/nimble-portfolio/nimble-portfolio.css</span> is not writtable! Loader color won't be saved.</p></div>";
            }
        } catch (Exception $e) {
            echo "<div class='error'><p><strong>LESS Compiler:</strong> " . $e->getMessage() . "</p></div>";
        }
    }
    self::setOptions($options);
}
?>
<div id="nimble-portfolio-global-settings">
    <h2>Nimble Portfolio - Global Settings</h2>
    <hr />
    <form method="post" action="">
        <?php do_action('nimble_portfolio_global_settings_before'); ?>
        <p>
            <label for = "nimble_portfolio_loader_flag"><?php _e("Enable Loader", 'nimble_portfolio'); ?>:</label>
            <input type="checkbox" id="nimble_portfolio_loader_flag" name="settings[loader_flag]" value="1" <?php checked($settings['loader_flag'], 1); ?> />
        </p>
        <p>
            <label for = "nimble_portfolio_loader_color"><?php _e("Loader Color", 'nimble_portfolio'); ?>:</label>
            <input type="text" id="nimble_portfolio_loader_color" name="settings[loader_color]" class="color-rgb" value="<?php echo $settings['loader_color']; ?>" />
        </p>
        <p>
            <label for = "nimble_portfolio_loader_size"><?php _e("Loader Size", 'nimble_portfolio'); ?>:</label>
            <select id="nimble_portfolio_loader_size" name="settings[loader_size]">
                <option value="10px" <?php selected($settings['loader_size'], '10px'); ?>>Small</option>
                <option value="20px" <?php selected($settings['loader_size'], '20px'); ?>>Normal</option>
                <option value="30px" <?php selected($settings['loader_size'], '30px'); ?>>Large</option>
                <option value="40px" <?php selected($settings['loader_size'], '40px'); ?>>Extra Large</option>
            </select>
        </p>
        <p>
            <label for="nimble_portfolio_thumb_nocache"><?php _e('Force No Cache for Thumbnails', 'nimble_portfolio') ?>:</label>
            <input type="checkbox" id="nimble_portfolio_thumb_nocache" name="settings[thumb_nocache]" value="1"  <?php checked($settings['thumb_nocache'], 1); ?> />
        </p>
        <p>
            <label for="nimble_portfolio_thumb_exact_size"><?php _e('Force Exact Thumbnail Size Generation', 'nimble_portfolio') ?>:</label>
            <input type="checkbox" id="nimble_portfolio_thumb_exact_size" name="settings[thumb_exact_size]" value="1"  <?php checked($settings['thumb_exact_size'], 1); ?> />
        </p>
        <?php do_action('nimble_portfolio_global_settings_after'); ?>
        <p><input type="submit" name="nimble_portfolio_save" id="nimble_portfolio_save" value="Save Settings" class="button button-primary"/></p>
    </form>
</div>
