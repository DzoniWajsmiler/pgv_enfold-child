<?php
/**
 * Created by PhpStorm.
 * User: Nina Camernik
 * Date: 23.5.2019
 * Time: 8:19
 */

?>

<div class="ow-three-columns-post-wrap">
    <div class="posts-wrap">
        <?php echo do_shortcode('[show_last_posts post_number="3" show_excerpt="no"]') ?>
<!--        --><?php //echo "OLA!" ?>
    </div>
    <div class="ow_button_container">
        <?php echo do_shortcode("[av_button label='Oglej si vse članke' link='manually,http://' link_target='' color='purple' av_uid='' custom_class='' admin_preview_bg=''][/av_button]") ?>
    </div>
</div>
