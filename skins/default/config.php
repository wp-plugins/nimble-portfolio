<?php
$skin_default = $this->getOptions();

if (@$_POST['nimble-portfolio-skin-submit']) {
    $skin_default['readmore-flag'] = (int) @$_POST['readmore-flag'];
    $skin_default['readmore-text'] = @$_POST['readmore-text'];
    $skin_default['viewproject-flag'] = (int) @$_POST['viewproject-flag'];
    $skin_default['viewproject-text'] = @$_POST['viewproject-text'];
    $skin_default['skin-type'] = @$_POST['skin-type'];
    $skin_default['column-type'] = @$_POST['column-type'];
    $skin_default['hover-icon'] = @$_POST['hover-icon'];
    $this->setOptions($skin_default);
}

$readmore_flag = $skin_default['readmore-flag'];
$readmore_text = $skin_default['readmore-text'];
$viewproject_flag = $skin_default['viewproject-flag'];
$viewproject_text = $skin_default['viewproject-text'];
$skin_type = $skin_default['skin-type'];
$skin_cols = $skin_default['column-type'];
$hover_icon = $skin_default['hover-icon'];

$columns = array(
    '-columns2' => '2 Columns',
    '-columns3' => '3 Columns',
    '-columns4' => '4 Columns',
    '-columns5' => '5 Columns'
);
?>
<div class="wrap">
    <div id="icon-edit" class="icon32 icon32-posts-portfolio">&nbsp;</div>
    <h2><?php echo get_admin_page_title(); ?></h2>
    <br class="clear" />
    <div class="admin-main">
        <form action="" method="post" class="validate">
            <div class="admin-section">
                <label for="nimble_easing"><?php _e('Choose Skin Columns', 'nimble_portfolio') ?></label>
                <div class="nimble-radio">
                    <select id="column-type" name="column-type" >
                        <?php
                        foreach ($columns as $value => $label):
                            ?>
                            <option value="<?php echo $value; ?>" <?php echo $value == $skin_cols ? "selected='selected'" : ""; ?>>
                                <?php echo $label; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div> 

            <div class="admin-section">
                <label><?php _e('Display Post Permalink', 'nimble_portfolio') ?></label>
                <div class="nimble-radio">
                    <p>
                        <input type="radio" value="1" name="readmore-flag" id="readmore-flag-yes" <?php checked($readmore_flag, 1); ?> />
                        <label for="readmore-flag-yes" class="radio-button">YES</label>
                    </p>
                    <p>
                        <input type="radio" value="0"  name="readmore-flag" id="readmore-flag-no" <?php checked($readmore_flag, 0); ?> />
                        <label for="readmore-flag-no" class="radio-button">NO</label>
                    </p>
                </div>
            </div>

            <div class="admin-section">
                <label><?php _e('Post Permalink Text', 'nimble_portfolio') ?></label>
                <div class="nimble-radio">
                    <p>
                        <input type="text" value="<?php echo $readmore_text; ?>"  name="readmore-text" id="readmore-text" />
                    </p>
                </div>
            </div>

            <div class="admin-section">
                <label><?php _e('Display Portfolio URL Link', 'nimble_portfolio') ?></label>
                <div class="nimble-radio">
                    <p>
                        <input type="radio" value="1" name="viewproject-flag" id="viewproject-flag-yes" <?php checked($viewproject_flag, 1); ?> />
                        <label for="viewproject-flag-yes" class="radio-button">YES</label>
                    </p>
                    <p>
                        <input type="radio" value="0"  name="viewproject-flag" id="viewproject-flag-no" <?php checked($viewproject_flag, 0); ?> />
                        <label for="viewproject-flag-no" class="radio-button">NO</label>
                    </p>
                </div>
            </div>

            <div class="admin-section">
                <label><?php _e('Portfolio URL Link Text', 'nimble_portfolio') ?></label>
                <div class="nimble-radio">
                    <p>
                        <input type="text" value="<?php echo $viewproject_text; ?>"  name="viewproject-text" id="viewproject-text" />
                    </p>
                </div>
            </div>

            <div class="admin-section">
                <label><?php _e('Choose Skin Type', 'nimble_portfolio') ?></label>
                <div class="nimble-radio">
                    <p>
                        <input type="radio" value="normal"  name="skin-type" id="skin-type-normal" <?php checked($skin_type, 'normal'); ?> />
                        <label for="skin-type-normal" class="radio-button">Normal</label>
                    </p>
                    <p>
                        <input type="radio" value="round" name="skin-type" id="skin-type-round" <?php checked($skin_type, 'round'); ?> />
                        <label for="skin-type-round" class="radio-button">Round</label>
                    </p>
                    <p>
                        <input type="radio" value="square"  name="skin-type" id="skin-type-square" <?php checked($skin_type, 'square'); ?> />
                        <label for="skin-type-square" class="radio-button">Square</label>
                    </p>
                </div>
            </div>

            <div class="admin-section">
                <label><?php _e('Choose hover Icon', 'nimble_portfolio') ?></label>
                <div class="nimble-radio">
                    <p>
                        <select name="hover-icon" id="hover-icon" class="genericon" >
                            <optgroup>
                                <option value="gallery" <?php selected($hover_icon, 'gallery'); ?>>&#xf103;</option>
                                <option value="image" <?php selected($hover_icon, 'image'); ?>>&#xf102;</option>
                                <option value="video" <?php selected($hover_icon, 'video'); ?>>&#xf104;</option>
                                <option value="youtube" <?php selected($hover_icon, 'youtube'); ?>>&#xf213;</option>
                            </optgroup>
                            <optgroup>
                                <option value="search" <?php selected($hover_icon, 'search'); ?>>&#xf400;</option>
                                <option value="zoom" <?php selected($hover_icon, 'zoom'); ?>>&#xf402;</option>
                                <option value="show" <?php selected($hover_icon, 'show'); ?>>&#xf403;</option>
                                <option value="picture" <?php selected($hover_icon, 'picture'); ?>>&#xf473;</option>
                            </optgroup>
                            <optgroup>
                                <option value="cart" <?php selected($hover_icon, 'cart'); ?>>&#xf447;</option>
                                <option value="heart" <?php selected($hover_icon, 'heart'); ?>>&#xf461;</option>
                            </optgroup>
                        </select>
                    </p>
                </div>
            </div>

            <p class="submit">
                <input type="submit" value="<?php _e('Save Settings', 'nimble_portfolio') ?>" class="button button-primary" id="nimble-portfolio-skin-submit" name="nimble-portfolio-skin-submit" />
            </p>

        </form> 
    </div> 
</div> 
