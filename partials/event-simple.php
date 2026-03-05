<?php
/**
 * Created by PhpStorm.
 * User: Nina Camernik
 * Date: 10.6.2019
 * Time: 9:50
 */
?>

<?php
$post = get_post();
$product_id = $post->ID;
?>

<?php $ow_event_options = get_product_options($product_id); ?>

<div class="ow-register-product-wrap"></div>