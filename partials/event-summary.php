<?php
/**
 * Created by PhpStorm.
 * User: Nina Camernik
 * Date: 15.5.2019
 * Time: 9:28
 */
?>

<?php $custom = get_post_custom(); ?>
<?php $images_path = get_site_url(null, '/wp-content/themes/enfold-child/images/'); ?>
<?php $podrocje_img = $images_path . 'Podrocje.svg'; ?>
<?php $termin_img = $images_path . 'termin.svg'; ?>
<?php $trajanje_img = $images_path . 'cas_trajanja.svg'; ?>
<?php $znanje_img = $images_path . 'predhodno_znanje.svg'; ?>
<?php //setlocale(LC_TIME, "sl_SI.UTF8"); ?>
<?php
$post = get_post();
$product_id = $post->ID;
if (isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];
}

$event_start_date = strtotime(get_post_meta($product_id, "ow_event_start_date", true));
$event_start_time = eventTime(get_post_meta($product_id, "ow_event_start_time", true));
$event_end_time = eventTime(get_post_meta($product_id, "ow_event_end_time", true));

$isAkademija = false;
if (!empty(get_post_meta($product_id, "ow_event_akademija", true))) {
    $isAkademija = true;
}

//check if event has child events
$end_date = get_post_meta($product_id, "ow_event_end_date", true);
$event_end_date = strtotime($end_date);
$now = date('Y-m-d');

$old_event = false;
if($end_date) {
    if (strtotime($now) > $event_end_date) {
        $old_event = true;
    }
} else {
    if (strtotime($now) > $event_start_date) {
        $old_event = true;
    }
}

$args = array(
    'post_parent' => $product_id,
    'post_type'   => 'product',
    'numberposts' => -1,
    'post_status' => 'publish',
    'meta_key' => 'ow_event_start_date',
    'meta_query' => array(
        'relation' => 'AND',
        array(
            'key' => 'ow_event_start_date',
            'value' => $now,
            'compare' => '>=',
            'type' => 'DATE',
        ),
    ),
    'orderby' => array(
        'meta_value' => 'ASC',
    ),
);
$children = get_children( $args );

$i = 0;
foreach($children as $child){
    if($old_event){
        if($i == 0) {
            $new_product_id = $child->ID;
            $event_start_date = strtotime(get_post_meta($new_product_id, "ow_event_start_date", true));
            $event_end_date = strtotime(get_post_meta($new_product_id, "ow_event_end_date", true));
            $event_start_time = eventTime(get_post_meta($new_product_id, "ow_event_start_time", true));
            $event_end_time = eventTime(get_post_meta($new_product_id, "ow_event_end_time", true));
            $i++;
            $multiple_dates = true;
            continue;
        } else if($i >= 1){
            $multiple_dates = true;
            break;
        }
    } else {
        $multiple_dates = true;
        break;
    }
}

?>
<?php $event_date = 'Oglejte si vse termine'; ?>
<?php $event_multiple_dates = true; ?>
<?php if ($event_start_date === $event_end_date) {
    $event_date = $event_start_date;
    $event_multiple_dates = false;
}

$terms = get_the_terms($product_id, 'product_tag');
$product_dates = get_all_product_dates($product_id, $terms);

if (trim($event_end_date) == '' || $event_start_date === $event_end_date) {
    $event_date = $event_start_date;
    $event_multiple_dates = false;
}

$terms = get_the_terms($product_id, 'product_tag');
$product_dates = get_all_product_dates($product_id, $terms);

$today = strtotime(date('Y-m-d H:i:s'));
$dates = [];
$valid_dates = true;

foreach ($product_dates as $key => $product_date) {
    if ($today < $product_date['date']) {
        $dates[] = $product_date['date'];
    }
}

if (count($dates) <= 1) {
    $valid_dates = false;
}

$today = strtotime(date('Y-m-d H:i:s'));
$dates = [];
$valid_dates = true;

foreach ($product_dates as $key => $product_date) {
    if ($today < $product_date['date']) {
        $dates[] = $product_date['date'];
    }
}

if (count($dates) <= 1) {
    $valid_dates = false;
}

?>

<?php if ($event_start_date === $event_end_date): ?>
    <?php $event_date = $event_start_date; ?>
<?php endif; ?>

<!-- icons row -->
<div class="ow-icons-row-wrap">
    <div class="ow-icons-row">

        <?php if (isset($custom['podrocje']) and !empty($custom['podrocje'][0])): ?>
            <div class="ow-podrocje ow-icons-single">
                <img src="<?php echo $podrocje_img ?>"/>
                <p><b><?php echo "Področje" ?></b></p>
                <p><?php echo $custom['podrocje'][0] ?></p>
            </div>
        <?php endif; ?>

        <div class="ow-termin ow-icons-single">
            <img src="<?php echo $termin_img ?>"/>
            <p><b><?php echo "Termin" ?></b></p>
            <?php if($old_event == false || $multiple_dates == true): ?>
                <?php if (!$multiple_dates  or !$valid_dates): ?>
                    <?= $event_multiple_dates ? "<p>" . eventDate($event_start_date) . " do " . eventDate($event_end_date) . "</p>" :
                        "<p>" . eventDate($event_start_date) . "</p>" ?>
                <?php else: ?>
                    <p><a class="text-def" href="#termini">Več terminov</a></p>
                <?php endif; ?>
            <?php else: ?>
                <p><?php echo get_field('arhiviran_dogodek_tekst', 'option'); ?></p>
            <?php endif; ?>
        </div>

        <?php if($event_start_time && trim($event_start_time) != '' && !$isAkademija && ($old_event == false || $multiple_dates == true)): ?>
        <div class="ow-trajanje ow-icons-single">
            <img src="<?php echo $trajanje_img ?>"/>
            <p><b><?php echo "Čas trajanja" ?></b></p>
            <p> <?php
                if($event_end_time == ''){
                    echo "Ob " . $event_start_time;
                } else {
                    echo $event_start_time . " do " . $event_end_time;
                }
                ?></p>
        </div>
        <?php endif; ?>

        <?php if (isset($custom['predhodno_znanje']) and !empty($custom['predhodno_znanje'][0])): ?>
            <div class="ow-znanje ow-icons-single">
                <img src="<?php echo $znanje_img ?>"/>
                <p><b><?php echo "Predhodno znanje" ?></b></p>
                <p><?php echo $custom['predhodno_znanje'][0] ?></p>
            </div>
        <?php endif; ?>

    </div>
</div>
