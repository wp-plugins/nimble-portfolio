<link rel="stylesheet" type="text/css" href="<?php echo NIMBLE_PORTFOLIO_TEMPLATES_URL . "/$template_code/template.css"; ?>" />
<div class="nimble-portfolio-content nimble-portfolio-template-<?php echo $template_code;?>">
    <div class="nimble-portfolio-filter">
        <ul class="nimble-portfolio-ul">
            <li class="current"><a href="#" rel="all">All</a></li>
            <?php nimble_portfolio_list_categories(); ?>
        </ul>
    </div><!-- /nimble-portfolio-filter -->            
    <div class="nimble-portfolio">
        <ul class="nimble-portfolio-ul">
            <?php $portfolio = new WP_Query(array('post_type' => 'portfolio', 'posts_per_page' => '-1')); ?>
            <?php while ($portfolio->have_posts()) : $portfolio->the_post(); ?>
                <li class="<?php nimble_portfolio_get_item_classes(get_the_ID()); ?>" >
                    <div class="nimble-portfolio-title"><?php the_title(); ?></div>
                    <?php $src = nimble_portfolio_get_attachment_src(get_post_thumbnail_id(get_the_ID()), '216x217', false, ''); ?>
                    <div class="nimble-portfolio-item" style="background: url('<?php echo $src[0]; ?>') center center !important;">
                        <a href="<?php echo nimble_portfolio_get_meta('nimble-portfolio'); ?>" rel="lightbox[nimble_portfolio_gal_round_1]" >
                            <div class="nimble-portfolio-rollerbg"></div>	
                        </a>
                    </div> 
                    <div class="nimble-portfolio-links">
                        <a href="<?php the_permalink(); ?>" class="button-fixed">
                            <?php _e('Read More →') ?>
                        </a>
                        <a href="<?php echo nimble_portfolio_get_meta('nimble-portfolio-url'); ?>" class="button-fixed">
                            <?php _e('View Project →') ?>
                        </a>
                    </div>	
                </li>
                <?php
            endwhile;
            wp_reset_postdata();
            ?>
        </ul>
    </div><!-- /nimble-portfolio -->
</div><!-- /nimble-portfolio-content -->
