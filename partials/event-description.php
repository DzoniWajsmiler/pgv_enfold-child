<?php
/**
 * Created by PhpStorm.
 * User: Nina Camernik
 * Date: 15.5.2019
 * Time: 12:23
 */

$post = get_post();
$product_id = $post->ID;

$event_subtitle = get_post_meta($product_id, "podnaslov", true);

if (!$event_subtitle) {
    $event_subtitle = get_post($product_id)->post_title;
}

$content = do_shortcode(wpautop(get_post_meta($product_id, 'daljsi_opis', true)));

?>

<?php if($event_subtitle || $content): ?>
    <div class="ow-row" id="vec">
        <div class="ow-left">
            <div class="ow-event-title">
                <?php if($event_subtitle): ?>
                    <h2 class="ow-subtitle"><?php echo $event_subtitle; ?></h2>
                <?php endif; ?>
            </div>
        </div>
        <div class="ow-right">
            <div class="ow-event-text">
                <?php echo $content; ?>
            </div>
        </div>
    </div>
<?php endif; ?>


