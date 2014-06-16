<?php
$filters = $this->getFilters();
foreach ($filters as $filter) {
    ?>
    <a href="<?php echo apply_filters('nimble_portfolio_filter_href', get_term_link($filter->slug, $this->taxonomy), $filter); ?>" rel="<?php echo apply_filters('nimble_portfolio_filter_rel', $filter->slug, $filter); ?>" class="-filter <?php echo apply_filters('nimble_portfolio_filter_class', "", $filter); ?>" id='<?php echo apply_filters('nimble_portfolio_filter_id', "filter-" . $filter->term_id, $filter); ?>' <?php do_action('nimble_portfolio_filter_atts', $filter); ?>><?php echo apply_filters('nimble_portfolio_filter_name', $filter->name, $filter); ?></a>
    <?php
}
