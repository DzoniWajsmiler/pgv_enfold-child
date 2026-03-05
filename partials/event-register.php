<?php

require_once(get_stylesheet_directory() . '/custom/datetime.php');
wp_enqueue_script('ow-event-modules', get_stylesheet_directory_uri() . '/js/ow-event-modules.js', array('jquery'), '1.0.0', true);

$cart = new WC_Cart();
$images_path = get_site_url(null, '/wp-content/themes/enfold-child/images/');
$post = get_post();
$product_id = $post->ID;
if (isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];
}
$product_dates = get_all_product_dates($product_id);

$product = wc_get_product($product_id);
$permalink = $product->get_permalink();
$terms = get_the_terms($product_id, 'product_tag');
$today = strtotime(date('m/d/Y'));

$isAkademija = false;
if (!empty(get_post_meta($product_id, "ow_event_akademija", true))) {
    $isAkademija = true;
}

$parent_id = wp_get_post_parent_id($product_id);

if(!$parent_id){
    $parent_id = $product_id;
}

$product_title = get_the_title($parent_id);

$i = 0;
?>

<div id="termini"
     class="ow-register-wrap avia-section main_color avia-section-default avia-no-border-styling avia-bg-style-scroll  el_after_av_textblock  avia-builder-el-last  ow-bg-grey  container_wrap fullsize">
    <div class="container">

        <div class="container-690">
            <h4><?= __("Prijava in kotizacija"); ?></h4>
            <h2 class="ow-register-title"><?= $product_title; ?></h2>
        </div>


        <?php if(isset($product_dates) && !empty($product_dates)): ?>
        <?php $len = count($product_dates); ?>
        <?php if($len > 1): ?>
            <p class="text-center m-20"><?php echo "Delavnica je razpisana v več terminih, izberite vam ustrezen datum."; ?></p>
        <?php endif; ?>
            <div class="ow-dates">
                <?php foreach ($product_dates as $key => $product_date):
                    if($product_date['end_date'] != '' && $product_date['end_date'] != $product_date['date']) {
                        $compare_to_date = $product_date['end_date'];
                        $label = date('d. n. Y', $product_date['date']) . " - " . date('d. n. Y', $product_date['end_date']);
                    } else {
                        $compare_to_date = $product_date['date'];
                        $label = date('d. n. Y', $product_date['date']);
                    }
                    ?>
                    <?php if ($today < $compare_to_date): ?>
                        <a class="ow-date-button <?php if ($product->is_type('variable')){ echo 'variable-data'; }; ?>" id="<?= 'ow-date-button_' . $i ?>" data-productid = "<?= $product_date['product_id'] ?>">
                            <span><?= $label; ?></span>
                        </a>
                    <?php $i++; ?>
                    <?php endif ?>
                <?php endforeach; ?>
            </div>

            <?php if($i > 0): ?>
                <?php if ($product->is_type('variable')): ?>
                    <?php get_template_part('partials/event', 'configurable'); ?>
                <?php elseif ($product->is_type('simple')): ?>
                    <?php get_template_part('partials/event', 'simple'); ?>
                <?php endif; ?>

                <div class="ow-product-additional container-690">
                    <p><?= get_post_meta($product_id, "ow_event_extra", true) ?></p>
                    <p>V ceno ni vključen DDV.</p>
                </div>

                <div class="ow-product-payment-banner">
                    <img src="<?= $images_path . "obroki.svg" ?>"/>
                    <div class="payment-banner-text">
                        <h4><?php echo get_field('credit_banner_title', 'options'); ?></h4>
                        <h3><?php echo get_field('credit_banner_text', 'options'); ?></h3>
                    </div>
                </div>
            <?php else: ?>
                <h4 class="text-center"><?php echo get_field('arhiviran_dogodek_tekst', 'option'); ?></h4>
            <?php endif; ?>

        <?php else: ?>
            <h4 class="text-center"><?php echo get_field('arhiviran_dogodek_tekst', 'option'); ?></h4>
        <?php endif; ?>
    </div>
</div>