<?php
/**
 * Created by PhpStorm.
 * User: Nina Camernik
 * Date: 4.6.2019
 * Time: 14:41
 */ ?>

<?php
$cart = new WC_Cart();
$images_path = get_site_url(null, '/wp-content/themes/enfold-child/images/');
$post = get_post();
$product_id = $post->ID;
$product = wc_get_product($product_id);
$product_title = $product->get_title();
if($product->is_type( 'variable' )) {
    $available_variations = $product->get_available_variations();
}
$name = "Vsi moduli";
$variations = [];

$attr_type = "akademija-moduli";

$today = date("Y-m-d");

?>


<?php foreach ($available_variations as $available_variation): ?>

    <?php $variation_id = $available_variation['variation_id']; ?>
    <?php $date = ''; ?>
    <?php $name = shorten_variation_name($product_title, $variation_id) ?>
    <?php $attributes = $available_variation['attributes']; ?>

    <?php
    if (array_key_exists('attribute_pa_akademija-moduli' , $attributes)) {
        if ($available_variation['attributes']['attribute_pa_akademija-moduli'] == '') {
            $atribute_type = "akademija-moduli";
            continue;
        }
    } else if (array_key_exists('attribute_pa_tip-prijave' , $attributes)) {
        if ($available_variation['attributes']['attribute_pa_tip-prijave'] == '') {
            $atribute_type = "tip-prijave";
            continue;
        }
    } else {
        continue;
    }

    $module_data = get_module_data($product_id, $variation_id);

    if($module_data){
        if($module_data['date'] != ''){
            $module_date = $module_data['date'];
            $formatted_date = str_replace('. ', '-', $module_date);
            if(strtotime($today) >= strtotime($formatted_date)){
                continue;
            }
            $date = date_i18n('j. F',strtotime($formatted_date));
        }
    }
    ?>
    <?php $variations[] = [
        "name" => $name,
        "variation_id" => $variation_id,
        "date" => $date,
    ];

    ?>
<?php endforeach; ?>

<?php
$class='';
if(!empty($variations)){
    $class="has-modules " . $atribute_type;
}

?>
<div id="ow-product-variations" class="ow-dates-module  <?php echo $class; ?>">
    <?php if (array_key_exists('attribute_pa_akademija-moduli' , $attributes)): ?>
    <?php $j = 0; ?>
        <?php foreach ($variations as $variation): ?>
            <?php $title = $variation['name'];
            if($variation['date'] != ''){
                $title.= ": ".$variation['date'];
            }
        ?>
            <span class="ow-event-register-name ow-timetable-button" id="<?= "ow-variable-{$j}" ?>" data-productid="<?= $product_id ?>"
                  data-variationid="<?= $variation['variation_id'] ?>"><?= $title; ?></span>
            <?php $j++; ?>
        <?php endforeach; ?>

    <?php else: ?>



    <?php endif; ?>
</div>
<div id="ow-event-details" class="ow-register-product-wrap"></div>



