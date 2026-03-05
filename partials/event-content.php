<?php
/**
 * Created by PhpStorm.
 * User: Nina Camernik
 * Date: 15.5.2019
 * Time: 13:30
 */

$post = get_post();
$product_id = $post->ID;

$product_description = get_post($product_id)->post_content;
$event_content_title = get_post_meta($product_id, "ow_naslov_povzetka", true);
$event_content = do_shortcode(wpautop(get_post_meta($product_id, "ow_povzetek_vsebine", true)));

?>
<?php if($event_content_title || $event_content): ?>

    <div class="ow-row">
        <div class="ow-left">
            <?php if($event_content_title): ?>
                <h2 class="ow-event-title"><?php echo $event_content_title; ?></h2>
            <?php endif; ?>
        </div>


        <div class="ow-right">
            <?php if($event_content): ?>
                <div class="ow-event-text-wrap">
                    <div class="ow-event-text"><?php echo $event_content; ?></div>
                </div>
                <div>
                    <?php echo do_shortcode("[av_button label='Želim se prijaviti' link='manually,#termini' link_target='' color='pink' av_uid='' custom_class='' admin_preview_bg=''][/av_button]"); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>