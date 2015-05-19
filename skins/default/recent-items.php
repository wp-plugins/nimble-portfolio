<div class="-skin-default-recent-items">
    <?php
    $skin = $this->getSkin();
    $skin_options = $skin->getOptions();
    $items = $this->getItems();
    foreach ($items as $item) {
        $item_atts = array();
        $item_atts['href'] = $item->getpermalink();
        $item_atts['class'] = $item->getFilters($this->taxonomy);
        $item_atts['class'][] = "-recent-item";
        $item_atts['id'] = "recent-item-" . $item->ID;
        $item_atts = apply_filters('nimble_portfolio_recent_item_atts', $item_atts, $item, $this);
        ?>
        <a <?php echo NimblePortfolioPlugin::phpvar2htmlatt($item_atts); ?>>
            <img class="<?php echo isset($this->atts['_widget_round_thumb']) && $this->atts['_widget_round_thumb'] ? '-round-tmb' : ''; ?>" src="<?php echo $item->getThumbnail('50x50'); ?>" alt="<?php echo $item->getTitle(); ?>">
            <?php echo $item->getTitle(); ?>
        </a>    
        <?php
    }
    ?>
</div>
<div style="clear: both;"></div>