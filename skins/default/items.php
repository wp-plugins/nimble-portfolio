<?php
$skin = $this->getSkin();
$skin_options = $skin->getOptions();
$readmore_flag = $skin_options['readmore-flag'];
$readmore_text = $skin_options['readmore-text'];
$viewproject_flag = $skin_options['viewproject-flag'];
$viewproject_text = $skin_options['viewproject-text'];
$skin_type = $skin_options['skin-type'];
$skin_cols = $skin_options['column-type'];
$hover_icon = $skin_options['hover-icon'] ? $skin_options['hover-icon'] : 'zoom';
$items = $this->getItems();
foreach ($items as $item) {
    $item_atts = array();
    $item_atts['class'] = $item->getFilters($this->taxonomy);
    $item_atts['class'][] = "-item";
    $item_atts['id'] = "item-" . $item->ID;
    $item_atts = apply_filters('nimble_portfolio_item_atts', $item_atts, $item, $this);
    $item_link = array();
    $item_link['href'] = esc_url($item->getData('nimble-portfolio'));
    $item_link['rel'] = apply_filters('nimble_portfolio_lightbox_galleryname', 'nimblebox[nimble_portfolio_gal_default]', $item);
    $item_link['title'] = esc_attr($item->getTitle());
    $item_link = apply_filters('nimble_portfolio_lightbox_link_atts', $item_link, $item, $this);
    ?>
    <div <?php echo NimblePortfolioPlugin::phpvar2htmlatt($item_atts); ?>>
        <div class="title"><?php echo $item->getTitle(); ?></div>    
        <div class="itembox">
            <a <?php echo NimblePortfolioPlugin::phpvar2htmlatt($item_link); ?>>
                <img src="<?php echo $item->getThumbnail('480x480', true); ?>" />
                <div class="-mask"> </div>
                <div class="genericon genericon-<?php echo $hover_icon; ?>"></div>
            </a>    
        </div>
        <?php if ($readmore_flag || $viewproject_flag) { ?> 
            <div class="-links">
                <?php if ($readmore_flag) { ?>
                    <div class="-link -readmore <?php echo $viewproject_flag && $item->getData('nimble-portfolio-url') ? '' : '-onlyonelink'; ?>">
                        <a href="<?php echo $item->getPermalink(); ?>" class="button-fixed">
                            <?php _e($readmore_text, 'nimble_portfolio_context') ?>
                        </a>
                    </div>
                <?php } ?>
                <?php if ($viewproject_flag && $item->getData('nimble-portfolio-url')) { ?>
                    <div class="-link -viewproject <?php echo $readmore_flag ? '' : '-onlyonelink'; ?>"> 
                        <a href="<?php echo $item->getData('nimble-portfolio-url'); ?>" class="button-fixed">
                            <?php _e($viewproject_text, 'nimble_portfolio_context') ?>
                        </a>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>
    </div>
    <?php
}
?>
<div style="clear: both;"></div>