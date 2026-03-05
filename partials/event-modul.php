<?php
/**
 * Created by PhpStorm.
 * User: Nina Camernik
 * Date: 3.6.2019
 * Time: 8:30
 */

wp_enqueue_script('ow-event-modules', get_stylesheet_directory_uri() . '/js/ow-event-modules.js', array('jquery'), '1.0.0', true); ?>

<?php $post = get_post(); ?>
<?php $product_id = $post->ID; ?>
<?php $product = wc_get_product($product_id); ?>
<?php $product_title = $product->get_title(); ?>
<?php $product_custom_fields = get_fields($product_id, true); ?>
<?php $moduli = $product_custom_fields['ow_event_akademija_moduli']; ?>
<?php $dates = get_module_name_and_date($product_title, $moduli); ?>

<?php if(!empty($dates)): ?>
<div id="moduli" class="ow-bg-pink">
    <div class="container">
        <h2>Sklopi srečanj</h2>
        <div class="ow-dates-module">
        <?php $i = 1; ?>
        <?php foreach ($dates as $date => $name): ?>
            <?php
            if(trim($name) == '' || !$name){
                $name = $i . ". srečanje";
            }
            ?>
            <a class="<?= "ow_event_timetable ow-timetable-button prevent-def" ?>" id='<?= "srecanje_{$i}" ?>' data-date="<?= $date ?>"
               data-product="<?= $product_id ?>"><span><?= $name . ": " . date_i18n('j. F',strtotime($date)); ?></span></a>
            <?php $i++; ?>
        <?php endforeach; ?>
        </div>
        <div class="ow-timetable-wrap">
            <div id="ow_event_timetable_arrow"></div>
            <div id="ow_event_timetable_details"></div>
        </div>
    </div>
</div>
<?php endif; ?>
