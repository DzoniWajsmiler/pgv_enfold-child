<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once("../../../wp-load.php");
global $sitepress;

if( !is_user_logged_in()){
    exit();
}

$bloginfo = get_bloginfo();
$bloginfo = preg_replace('~[^\pL\d]+~u', '-', $bloginfo);
$bloginfo = iconv('utf-8', 'us-ascii//TRANSLIT', $bloginfo);
$bloginfo = preg_replace('~[^-\w]+~', '', $bloginfo);
$bloginfo = trim($bloginfo, '-');
$bloginfo = preg_replace('~-+~', '-', $bloginfo);
$bloginfo = strtolower($bloginfo);

$args = array(
    'post_type' => 'shop_order',
    'posts_per_page' => -1,
    'post_status' => 'any',
    'meta_query' => array(
        array(
            'key' => '_privacy-policy-checkbox',
            'value' => '1',
            'compare' => 'LIKE'
        )
    )
);

$query = new WP_Query($args);

$emails_to_subscribe = array();

if( $query->have_posts() ):
    while( $query->have_posts() ): $query->the_post();
        $id = get_the_ID();
        $email = get_post_meta($id, '_billing_email', true);
        $subscribe = get_post_meta($id, '_privacy-policy-checkbox', true);

        $order = wc_get_order( $id );
        foreach ($order->get_items() as $item_id => $item_data) {
            $product = $item_data->get_product();
            $product_name = $product->name;
            $product_categories = get_the_terms( $product->id, 'product_cat' );
        }

        if(!$product_name) { continue; }

        $cats = "";
        if($product_categories && !$product_categories->errors){
            $len = count($product_categories);
            foreach($product_categories as $index => $cat){
                if($cat->name != "Nekategorizirano"){ $cats.= $cat->name; }
                if($len != $index + 1){ $cats.= ", "; }
            }
        }

        $subscriber = array(
            'email' => $email,
            'subscribe' => ($subscribe)? "DA": "NE",
            'product' => $product_name,
            'category' => $cats
        );

        $add_row = true;

        if(!empty($emails_to_subscribe)) {
            foreach ($emails_to_subscribe as $row) {
                if ($row['email'] !== $email) {
                    continue;
                }
                if ($row == $subscriber) {
                    $add_row = false;
                }
            }
        }

        if($add_row) {
            $emails_to_subscribe[] = $subscriber;
        }
    endwhile;
endif;
wp_reset_postdata();

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="export_subscribers_'.$bloginfo.'_'.date('d-m-Y', time()).'.csv"');

$fp = fopen('php://output', 'wb');

fputcsv($fp, array("Email", "Subscribe", "Event", "Event category")); //col titles
foreach ( $emails_to_subscribe as $line ) {
    fputcsv($fp, $line);
}
fclose($fp);

?>