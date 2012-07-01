<link rel="stylesheet" type="text/css" href="<?php echo NIMBLE_PORTFOLIO_TEMPLATES_URL . "/3colround/template.css"; ?>" />
<div class="content group nimble-portfolio-content">
    <div class="nimble-portfolio-filter group">
        <ul class="nimble-portfolio-ul">
            <li class="current"><a href="#">All</a></li>
            <?php wp_list_categories('taxonomy=type&title_li='); ?>
        </ul>
    </div><!-- /nimble-portfolio-filter -->            
    <div class="nimble-portfolio three group">
        <ul class="nimble-portfolio-ul">
            <div class="nimble-portfolio-ul-div">
                <?php $portfolio = new WP_Query(array('post_type' => 'portfolio', 'posts_per_page' => '-1')); ?>
                <?php while ($portfolio->have_posts()) : $portfolio->the_post(); ?>
                    <?php $terms_as_text = get_the_term_list($post->ID, 'type', '', ' ', ''); ?>
                    <li class="<?php echo strtolower(strip_tags($terms_as_text)); ?> bigcard" >
                        <h6><?php the_title(); ?></h6>    
                        <div class="nimble-portfolio-holder">
                            <?php $src = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), array(303, 203), false, ''); ?>
                            <div class="nimble-portfolio-item" style="background: url(<?php echo $src[0]; ?> ) !important;">
                                <a href="<?php echo nimble_portfolio_get_meta('nimble-portfolio'); ?>" rel="<?php echo (nimble_portfolio_get_meta('nimble-portfolio-type') == 'v') ? 'youtube' : 'fancybox'; ?>" >
                                    <div class="nimble-portfolio-rollerbg"></div>	
                                </a>
                            </div> 
                            <div class="nimble-portfolio-title">
                                <a href="<?php the_permalink(); ?>" class="button-fixed">
                                    <?php _e('Read More →', 'framework') ?>
                                </a> <a href="<?php echo nimble_portfolio_get_meta('nimble-portfolio-url'); ?>" class="button-fixed">
                                    <?php _e('View Project →', 'framework') ?>
                                </a>
                            </div>	
                        </div>
                    </li>
                    <?php
                endwhile;
                wp_reset_query();
                ?>
            </div>
        </ul>
    </div>
    <!-- /nimble-portfolio -->
</div>
<!-- /nimble-portfolio-content -->