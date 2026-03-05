<?php
/**
 * Created by PhpStorm.
 * User: Nina Camernik
 * Date: 16.5.2019
 * Time: 7:06
 */

/*  */


require_once(get_stylesheet_directory() . '/custom/datetime.php');

$post = get_post();
$product_id = $post->ID;
if (isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];
}

$event_start_date = strtotime(get_post_meta($product_id, "ow_event_start_date", true));

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

$isAkademija = false;
if (!empty(get_post_meta($product_id, "ow_event_akademija", true))) {
    $isAkademija = true;
}

//check if event has child events
$parent_id = wp_get_post_parent_id($product_id);

if (isset($parent_id) && empty($parent_id)) {
    $parent_id = $product_id;
}

$args = array(
    'post_parent' => $parent_id,
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
foreach($children as $child){ //only events, that are not old
    if($old_event){
        //parent event is old - find next event to show date
        if($i == 0) {
            $product_id = $child->ID;
            //use dates from newer event
            $event_start_date = strtotime(get_post_meta($product_id, "ow_event_start_date", true));
            $event_end_date = strtotime(get_post_meta($product_id, "ow_event_end_date", true));
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

$event_start_time = eventTime(get_post_meta($product_id, "ow_event_start_time", true));
$event_end_time = eventTime(get_post_meta($product_id, "ow_event_end_time", true));

$event_date_exists = false;
$event_multiple_dates = true;

$location_data = get_event_address($product_id);

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
?>

<div class="ow-event-badge-bg">
    <div class="ow-event-badge">
        <div class="ow-event-badge-half ow-half-1">
            <?php if($old_event == false || $multiple_dates == true): ?>
			
            <?= $event_multiple_dates ? "<h5>" . eventDate($event_start_date)  . "  do " . eventDate($event_end_date) . "</h5>" :
                "<h5>" . eventDate($event_start_date) . "</h5>" ?>
            <p class="ow-event-time">
                <?php
                if($event_end_time == '' && $event_start_time != '' && !$isAkademija){
                    echo "Ob " . $event_start_time;
                } else if($event_end_time != '' && $event_start_time != '' && !$isAkademija) {
                    echo $event_start_time . " do " . $event_end_time;
                }
                ?>
            </p>
            <?php if (!empty($multiple_dates)): ?>
                <p><a class="prevent-def ow-badge-link" href="#termini"><?= "Oglejte si vse termine" ?></a></p>
            <?php endif; ?>
            <?php else: ?>
                <p><?php echo get_field('arhiviran_dogodek_tekst', 'option'); ?></p>
            <?php endif; ?>
        </div>
        <hr>
        <div class="ow-event-badge-half ow-half-2">
            <?php if ($location_data['location_short'] && $location_data['location_short'] != ''): ?>
                <h5 class="ow-event-address-1"><?php echo $location_data['location_short'] ?></h5>
            <?php endif; ?>
            <?php ?>
            <?php if ($location_data['location_full'] && $location_data['location_full'] != ''): ?>
                <p class="ow-event-address-2"><?php echo $location_data['location_full'] ?></p>
            <?php endif; ?>
        </div>
    </div>
</div>
