<link rel="stylesheet" type="text/css" href="<?php echo NIMBLE_PORTFOLIO_TEMPLATES_URL . "/$template_code/template.css"; ?>" />
<div class="nimble-portfolio-content nimble-portfolio-template-<?php echo $template_code; ?>">
    <div class="nimble-portfolio-filter">
        <ul class="nimble-portfolio-ul">
            <li class="current"><a href="#" rel="all">All</a></li>
            <?php nimble_portfolio_list_categories(); ?>
        </ul>
    </div><!-- /nimble-portfolio-filter -->            
    <div class="nimble-portfolio">
        <ul class="nimble-portfolio-ul">
            <?php $portfolio = new WP_Query(array('post_type' => 'portfolio', 'posts_per_page' => '-1')); ?>
            <?php
            while ($portfolio->have_posts()) : $portfolio->the_post();
                $post_id = get_the_ID();
                ?>
                <li class="<?php nimble_portfolio_get_item_classes($post_id); ?>">
                    <div class="nimble-portfolio-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></div>
                    <?php $src = nimble_portfolio_get_attachment_src(get_post_thumbnail_id($post_id), '216x160', false); ?>
                    <div class="nimble-portfolio-item" style="background: url('<?php echo $src[0]; ?>') center center no-repeat !important;">
                        <a href="<?php echo nimble_portfolio_get_meta('nimble-portfolio'); ?>" rel="lightbox[nimble_portfolio_gal_rect_2]">
                            <div class="nimble-portfolio-rollerbg nimble-portfolio-rollerbg-<?php echo nimble_portfolio_get_filetype(nimble_portfolio_get_meta('nimble-portfolio'));?>"></div>
                        </a>
                        <div class="nimble-portfolio-thumb-shadow"></div>
                    </div> 
                </li>
                <?php
            endwhile;
            wp_reset_postdata();
            ?>
        </ul>
    </div><!-- /nimble-portfolio -->
</div><!-- /nimble-portfolio-content -->
