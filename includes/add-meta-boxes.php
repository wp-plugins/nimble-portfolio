<?php

function nimble_portfolio_create_section() {
    add_meta_box('nimble-portfolio-section', 'Image/Video URL', 'nimble_portfolio_section', 'portfolio', 'normal', 'high');
    add_meta_box('nimble-portfolio-url-section', 'Portfolio URL', 'nimble_portfolio_url_section', 'portfolio', 'normal', 'high');
}

add_action('admin_menu', 'nimble_portfolio_create_section');

function nimble_portfolio_section() {
    ?>
    <div class="nimble-portfolio-meta-section">
        <label><input type="radio" name="nimble_portfolio_type" value="v" <?php echo htmlspecialchars(nimble_portfolio_get_meta('nimble-portfolio-type')) == "v" ? " checked='checked' " : ""; ?> /> Video (Only youtube URL is supported)</label><br />
        <label><input type="radio" name="nimble_portfolio_type" value="i" <?php echo htmlspecialchars(nimble_portfolio_get_meta('nimble-portfolio-type')) != "v" ? " checked='checked' " : ""; ?> /> Image</label><br />
        <input type="text" name="nimble_portfolio" value="<?php echo htmlspecialchars(nimble_portfolio_get_meta('nimble-portfolio')); ?>" /><br />
        <p>Enter URL for the full-size image or a link to a youtube video you want to display in this portfolio.</p>
    </div>
    <?php
}

function nimble_portfolio_url_section() {
    ?>
    <div class="nimble-portfolio-meta-section">
        <input type="text" name="nimble_portfolio_url" value="<?php echo htmlspecialchars(nimble_portfolio_get_meta('nimble-portfolio-url')); ?>" /><br />
        <p>Enter URL to the live version of the project.</p>
        <input type="hidden" name="nimble_portfolio_noncename" id="nimble_portfolio_noncename" value="<?php echo wp_create_nonce(plugin_basename(__FILE__)); ?>" />
    </div>
    <?php
}

add_action('save_post', 'nimble_portfolio_save_data', 1, 2);
function nimble_portfolio_save_data($post_id, $post) {

    // verify this came from the our screen and with proper authorization,
    // because save_post can be triggered at other times
    if (!wp_verify_nonce($_POST['nimble_portfolio_noncename'], plugin_basename(__FILE__)))
        return $post->ID;

    if ($post->post_type == 'revision')
        return; //don't store custom data twice

    if (!current_user_can('edit_post', $post->ID))
        return $post->ID;

    // OK, we're authenticated: we need to find and save the data
    // We'll put it into an array to make it easier to loop though.
    $mydata = array();
    $mydata['nimble-portfolio'] = $_POST['nimble_portfolio'];
    $mydata['nimble-portfolio-type'] = $_POST['nimble_portfolio_type'];
    $mydata['nimble-portfolio-url'] = $_POST['nimble_portfolio_url'];

    // Add values of $mydata as custom fields
    foreach ($mydata as $key => $value) { //Let's cycle through the $mydata array!
        update_post_meta($post->ID, $key, $value);
        if (!$value)
            delete_post_meta($post->ID, $key); //delete if blank
    }
}