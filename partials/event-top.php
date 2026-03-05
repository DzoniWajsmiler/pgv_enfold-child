<?php
/**
 * Created by PhpStorm.
 * User: Nina Camernik
 * Date: 15.5.2019
 * Time: 12:09
 */

$post = get_post();
$product_id = $post->ID;
$short_description = get_post_meta($product_id, "povzetek_izdelka", true);
$thumbnail_img = get_the_post_thumbnail_url();

if($thumbnail_img){
    $show_image_class = "ow-show-img";
}

$isAkademija = false;
if(!empty(get_post_meta($product_id, "ow_event_akademija", true))) {
    $isAkademija = true;
}
$certifikat_name = "";
$event_title = get_the_title();
if ($isAkademija) {
    $certifikat_name = get_post_meta($product_id, "ow_akademija_priznanje", true);
}

?>

<div class="ow-events-full-width" id="homepage-banner">

    <div class="homepage-banner-block-1 ow-event-banner <?php if($show_image_class) { echo $show_image_class; } ?>">

        <div class="ow-text ow-left">

            <div class="ow-event-title">
                <h1><?= $event_title ?></h1>
            </div>

            <?php if(isset($short_description) and !empty($short_description)): ?>
                <div class="ow-short-description">
                    <?php echo $short_description; ?>
                </div>
            <?php endif; ?>

            <div>
                <?php echo do_shortcode("[av_button label='Želim se prijaviti' link='#termini' color='pink']"); ?>
            </div>

            <?php if($certifikat_name): ?>

                <div class="ow-certifikat">
                    <img src="<?php echo get_home_url().'/wp-content/themes/enfold-child/images/certifikat.svg'; ?>">
                    <div class="ow-certifikat-content">
                        <h5>OB KONCU PREJMETE CERTIFIKAT</h5>
                        <p><?php echo $certifikat_name; ?></p>
                    </div>
                </div>

            <?php endif; ?>

        </div>

        <div class="ow-homepage-img-1 ow-event-img ow-right">

            <?php if($thumbnail_img): ?>

            <div class="ow-decoration-img">
                <img src="<?php echo $thumbnail_img;  ?>" />
            </div>

            <?php endif; ?>

            <?php get_template_part( 'partials/event', 'badge' ); ?>

        </div>
    </div>

</div>



