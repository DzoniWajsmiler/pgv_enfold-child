<?php
// Template Name: Event

?>
<?php $images_path = get_site_url(null, '/wp-content/themes/enfold-child/images/'); ?>
<?php $custom = get_post_custom(); ?>

<?php
$product_id = $post->ID;

$isAkademija = false;
if (!empty(get_post_meta($product_id, "ow_event_akademija", true))) {
    $isAkademija = true;
}
?>

<div class="ow-events-wrapper container">
<?php get_template_part( 'partials/event', 'top' ); ?>
<?php get_template_part( 'partials/event', 'description' ); ?>
<?php get_template_part( 'partials/event', 'summary' ); ?>
<?php get_template_part( 'partials/event', 'content' ); ?>
</div>

<?php if ($isAkademija): ?>
    <div class="ow-full-width-container">
        <?php get_template_part('partials/event', 'modul'); ?>
    </div>
<?php endif; ?>

<div class="ow-events-wrapper container">
<?php get_template_part( 'partials/event', 'speakers' ); ?>
<?php echo do_shortcode("[reviews product_id='".$product_id."']") ?>
</div>

<div class="ow-full-width-container">
    <?php get_template_part( 'partials/event', 'register' ); ?>
<?php get_template_part( 'partials/event', 'komunikacija' ); ?>
<?php get_template_part( 'partials/event', 'kljuc_banner' ); ?>

</div>