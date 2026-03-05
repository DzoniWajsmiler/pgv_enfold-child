<?php

/*
* Add your own functions here. You can also copy some of the theme functions into this file. 
* Wordpress will use those functions instead of the original functions then.
*/

function clean_payload_array($data) {
    foreach ($data as $key => $value) {
        if (is_array($value)) {
            // Rekurzivno očisti pod-arraye
            $data[$key] = clean_payload_array($value);
        } elseif (is_string($value)) {
            // Očisti string vrednosti: nevidni znaki (ASCII < 32 in 127) + trim
            $cleaned = preg_replace('/[\x00-\x1F\x7F]/u', '', $value);
            $data[$key] = trim($cleaned);
        } else {
            // Ostale tipe pusti nedotaknjene (int, float, bool, null)
            $data[$key] = $value;
        }
    }
    return $data;
}



// Includes scripts, styles


function my_scripts()
{
    wp_enqueue_style('urska', get_stylesheet_directory_uri() . '/css/style-urska.css');
    wp_enqueue_style('button_css_main', get_stylesheet_directory_uri() . '/css/button_css/main.css');
    wp_enqueue_style('button_css_normalize', get_stylesheet_directory_uri() . '/css/button_css/normalize.css');

    wp_enqueue_script('ow-scripts', get_stylesheet_directory_uri() . '/js/ow-scripts.js', array('jquery'), '1.0.0', true);
    wp_enqueue_script('button_js_tween', get_stylesheet_directory_uri() . '/js/button_js/TweenMax.min.js', array('jquery'), '1.0.0', true);
    wp_enqueue_script('button_js_main', get_stylesheet_directory_uri() . '/js/button_js/main.js', array('jquery'), '1.0.0', true);
    wp_enqueue_script('float_panel', get_stylesheet_directory_uri() . '/js/float-panel.js', array('jquery'), '1.0.0', true);

}

add_action('wp_enqueue_scripts', 'my_scripts');

$role_object = get_role( 'editor' );
$role_object->add_cap( 'delete_users' );
$role_object->add_cap( 'create_users' );
$role_object->add_cap( 'list_users' );
$role_object->add_cap( 'delete_users' );
$role_object->add_cap( 'add_users' );
$role_object->add_cap( 'promote_users' );
$role_object->add_cap( 'enroll_users' );
$role_object->add_cap( 'edit_users' );
$role_object->add_cap( 'manage_network' );
$role_object->add_cap( 'manage_sites' );
$role_object->add_cap( 'manage_network_options' );
$role_object->add_cap( 'manage_network_themes' );
$role_object->add_cap( 'manage_network_users' );
$role_object->add_cap( 'manage_network_plugins' );
$role_object->add_cap( 'install_plugins' );
$role_object->add_cap( 'activate_plugins' );
$role_object->add_cap( 'manage_options' );
$role_object->add_cap( 'import' );

// $role2 = get_role('super_administrator');
// $role2->add_cap("update_plugins");

//$user = new WP_User( 0, 71);
$user = get_user_by('id', 71);

    $user->add_cap( 'update_plugins' );
    //$user->add_cap( 'my_second_cap' );



// 
//include functions-urska.php
require_once(get_stylesheet_directory() . '/functions-urska.php');
require_once(get_stylesheet_directory() . '/ow-checkout.php');
require_once(get_stylesheet_directory() . '/shortcodes/shortcodes.php');
//require_once(get_stylesheet_directory() . '/shortcodes/shortcodes-kne.php');

function assign_new_user_to_main_site($user_id) {
    // Get the main site ID, usually 1
    $main_site_id = get_main_network_id();
    
    // Check if user is already assigned to the main site
    if (!is_user_member_of_blog($user_id, $main_site_id)) {
        // Add the user to the main site
        add_user_to_blog($main_site_id, $user_id, 'subscriber'); // Adjust the role if needed
    }
}
add_action('wpmu_new_user', 'assign_new_user_to_main_site');

//change default shortcodes
add_filter('avia_load_shortcodes', 'avia_include_shortcode_template', 15, 1);

function avia_include_shortcode_template($paths)
{
    $template_url = get_stylesheet_directory();
    array_unshift($paths, $template_url . '/shortcodes/');

    return $paths;
}

// Slick slider styles
function slick_slider_styles()
{
    wp_enqueue_style('slick-slider-styles', get_stylesheet_directory_uri() . '/slick/slick.css');
    wp_enqueue_style('slick-slider-theme-styles', get_stylesheet_directory_uri() . '/slick/slick-theme.css');
}

add_action('wp_enqueue_scripts', 'slick_slider_styles');

// Slick slider js
function slick_slider_js()
{
    wp_enqueue_script('slick-slider-js', get_stylesheet_directory_uri() . '/slick/slick.min.js', array('jquery'), '', true);
    wp_enqueue_script('slick-slider-js', get_stylesheet_directory_uri() . '/slick/slick.js', array('jquery'), '', true);
}

add_action('wp_enqueue_scripts', 'slick_slider_js');

// Textillate slider styles
function textillate_styles()
{
    wp_enqueue_style('textillate-style', get_stylesheet_directory_uri() . '/textillate/textillate-style.css');
    wp_enqueue_style('textillate-animate', get_stylesheet_directory_uri() . '/textillate/textillate-animate.css');
}

add_action('wp_enqueue_scripts', 'textillate_styles');

// Textillate slider js
function textillate_js()
{
    wp_enqueue_script('textillate-fittext', get_stylesheet_directory_uri() . '/textillate/textillate-jquery.fittext.js', array('jquery'), '', true);
    wp_enqueue_script('textillate-lettering', get_stylesheet_directory_uri() . '/textillate/textillate-jquery.lettering.js', array('jquery'), '', true);
    wp_enqueue_script('textillate-textillate', get_stylesheet_directory_uri() . '/textillate/textillate-jquery.textillate.js', array('jquery'), '', true);
}

add_action('wp_enqueue_scripts', 'textillate_js');


//add options page for acf
if (function_exists('acf_add_options_page')) {
    // add parent
    $parent = acf_add_options_page(array(
        'page_title' => 'Dodatne nastavitve',
        'menu_title' => 'Dodatne nastavitve',
        'menu_slug' => 'Dodatne nastavitve',
        'position' => 20,
        'redirect' => true
    ));

    acf_add_options_sub_page(array(
        'page_title' => 'Mnenja udeležencev',
        'menu_title' => 'Mnenja udeležencev',
        'parent_slug' => $parent['menu_slug'],
    ));
    acf_add_options_sub_page(array(
        'page_title' => 'Strokovne revije',
        'menu_title' => 'Banner strokovne revije',
        'parent_slug' => $parent['menu_slug'],
    ));
    acf_add_options_sub_page(array(
        'page_title' => 'Prednosti',
        'menu_title' => 'Banner prednosti',
        'parent_slug' => $parent['menu_slug'],
    ));
/*    acf_add_options_sub_page(array(
        'page_title' => 'Dogodki - skupne vsebine',
        'menu_title' => 'Dogodki - skupne vsebine',
        'parent_slug' => $parent['menu_slug'],
    ));*/
    acf_add_options_sub_page(array(
        'page_title' => 'Nastavitve dogodkov na ključ',
        'menu_title' => 'Nastavitve dogodkov na ključ',
        'parent_slug' => $parent['menu_slug'],
    ));
    acf_add_options_sub_page(array(
        'page_title' => 'Priročniki - vsebina emaila',
        'menu_title' => 'Priročniki - vsebina emaila',
        'parent_slug' => $parent['menu_slug'],
    ));

    acf_add_options_sub_page(array(
        'page_title' => 'Checkout',
        'menu_title' => 'Checkout',
        'parent_slug' => $parent['menu_slug'],
    ));
    acf_add_options_sub_page(array(
        'page_title' => 'Banner - plačilo na obroke',
        'menu_title' => 'Banner - plačilo na obroke',
        'parent_slug' => $parent['menu_slug'],
    ));

    acf_add_options_sub_page(array(
        'page_title' => 'Email naročila',
        'menu_title' => 'Email naročila',
        'parent_slug' => $parent['menu_slug'],
    ));
}


function ajax_filterposts_handler()
{
    $posts = "";

    $categories_list = array();

    if (isset($_POST['category']) && $_POST['category'] != "" && $_POST['category'] != "0") {
        $category = $_POST['category'];
    }

    if (isset($_POST['page']) && $_POST['page'] != "") {
        $page = $_POST['page'];
    } else {
        $page = 1;
    }

    if (!$category || $category === "all") {
        $all_categories = get_terms(array(
            'taxonomy' => 'category',
            'hide_empty' => false,
        ));

        foreach ($all_categories as $single_category) {
            $categories_list[] = $single_category->slug;
        }
    } else {
        $categories_list[] = $category;
    }


    if (isset($_POST['show'])) {
        $show = esc_attr($_POST['show']);
    }
    $args = array(
        'post_type' => 'post',
        'post_status' => 'publish',
        'paged' => $page,
        'posts_per_page' => $show,
        'orderby' => 'date',
        'tax_query' => array(
            array(
                'taxonomy' => 'category',
                'field' => 'slug',
                'terms' => $categories_list,
            ),
        )
    );

    if (isset($_POST['tag']) && $_POST['tag'] != "" && $_POST['tag'] != "0") {
        $tag = $_POST['tag'];
        $args['tag'] = $tag;
    }

    $the_query = new WP_Query($args);

    if ($the_query->have_posts()) :
        ob_start();

        while ($the_query->have_posts()) : $the_query->the_post();
            $posts .= single_blog_post_html(true, true, "2", "purple", $category);
        endwhile;

        ob_get_clean();
    else:
        $posts .= '<div class="text-center"><p>V tej kategoriji trenutno ni prispevkov.</p></div>';
    endif;

    /* pagination is refreshed every time posts are loaded */

    $posts .= "<div class='ow-custom-pagination pagination-ajax' data-tag='" . $tag . "' data-show='" . $show . "'>";

    $pagination_args = array(
        'base' => '%_%',
        'format' => '?paged=%#%',
        'total' => $the_query->max_num_pages,
        'current' => $page,
        'show_all' => false,
        'end_size' => 1,
        'mid_size' => 2,
        'prev_next' => true,
        'prev_text' => __('<span class="prev"></span>'),
        'next_text' => __('<span class="next"></span>'),
        'add_args' => true,
        'add_fragment' => '',
        'before_page_number' => '<span>',
        'after_page_number' => '</span>'
    );

    $posts .= paginate_links($pagination_args);

    $posts .= "</div>";

    $return = array(
        'posts' => $posts
    );

    wp_send_json($return);

}

add_action('wp_ajax_filterposts', 'ajax_filterposts_handler');
add_action('wp_ajax_nopriv_filterposts', 'ajax_filterposts_handler');


/* PRODUCT PAGE */

/* Event - top */
add_action('woocommerce_before_single_product', 'event', 10);

function event()
{

    $path = "partials/event";

    return get_template_part($path);
}


remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart');
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);

remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_title', 5);
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 10);
remove_action('woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20);
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20);
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40);
remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20);
add_filter('woocommerce_product_related_posts_query', '__return_empty_array', 100);

add_filter('woocommerce_product_description_heading', 'remove_product_description_heading');
function remove_product_description_heading()
{
    return '';
}

add_filter('woocommerce_product_tabs', 'ow_remove_description_tab', 20, 1);

function ow_remove_description_tab($tabs)
{

    // Remove the description tab
    if (isset($tabs['description'])) unset($tabs['description']);
    return $tabs;
}


add_filter('woocommerce_sale_flash', 'woo_custom_hide_sales_flash');
function woo_custom_hide_sales_flash()
{
    return false;
}

function woo_remove_product_tabs($tabs)
{
    unset($tabs['additional_information']);
    return $tabs;
}

add_filter('woocommerce_product_tabs', 'woo_remove_product_tabs', 98);

function ajax_event_timetable_handler()
{

    $response = "";

    if (!isset($_POST['product_id']) and !(isset($_POST['date']))) {
        return "ajax error";
    }

    $product_id = $_POST['product_id'];
    $date = $_POST["date"];

    $moduli = get_field("ow_event_akademija_moduli", $product_id);

    $moduli_dates = [];
    foreach ($moduli as $modul) {
        $urnik = $modul['ow_event_srecanje_urnik'];
        $i = 0;
        foreach ($urnik as $parts) {
            $moduli_dates[$modul['ow_event_srecanje_date']][$i] = [
                "from" => $parts['ow_event_srecanje_urnik_from'],
                "to" => $parts['ow_event_srecanje_urnik_to'],
                "title" => $parts["ow_event_srecanje_urnik_title"],
                "text" => $parts['ow_event_srecanje_urnik_desc'],
                "speaker" => $parts["ow_event_srecanje_urnik_speaker"]
            ];
            $i++;
        }
    }

    $module_timetable = $moduli_dates[$date];

    foreach ($module_timetable as $key => $value) {
        $response .= "<div class='timetable-row'>";
        $response .= "<div class='timetable-col timetable-col-left'>";
        $response .= "<p class='timetable-time'>";
        $response .= "<span>" . "OD " . $value['from'] . " DO " . $value['to'] . "</span>";
        $response .= "</p>";
        $response .= "<h3 id='timetable_title'>" . $value['title'] . "</h3>";
        $response .= "<p id='timetable_speaker'>" . $value['speaker'] . "</p>";
        $response .= "</div>";
        $response .= "<div class='timetable-col timetable-col-right'>";
        $response .= $value['text'];
        $response .= "</div>";
        $response .= "</div>";
        $response .= "<hr>";
    }

    $return = array(
        'timetable' => $response
    );

    wp_send_json($return);
    die();

}

add_action('wp_ajax_get_event_timetable', 'ajax_event_timetable_handler');
add_action('wp_ajax_nopriv_get_event_timetable', 'ajax_event_timetable_handler');

function ajax_event_variations_handler()
{
    $response = "";
    $event_sale_details = [];
    $rules = [];
    $today = $date_now = date("Y-m-d");
    $images_path = get_site_url(null, '/wp-content/themes/enfold-child/images/');
    $attribute_name = '';
    $attribute_value = '';
    $cart_params = [];

    if (!isset($_POST['product_id']) and !(isset($_POST['variation_id']))) {
        return "ajax error";
    }

    $product_id = $_POST['product_id'];
    $variation_id = $_POST["variation_id"];
    $event_ticket_rules = get_post_meta($product_id, "_pricing_rules", true);

    $product = wc_get_product($product_id);
    if ($product->is_type('variable')) {
        $available_variations = $product->get_available_variations();
    }
    $attr = get_post_meta($product_id, '_product_attributes', true);


    if (!$available_variations) {
        return "Something went wrong, variations are not available at the moment";
    }

    foreach ($available_variations as $available_variation) {
        $variation = new WC_Product_Variation($available_variation['variation_id']);

        $variation_sale_to = $variation->get_date_on_sale_to($context = 'view');
        $variation_sale_to_timestamp = strtotime($variation_sale_to);

        $currency = get_woocommerce_currency_symbol();
        $regular_price = $variation->get_regular_price();

        if ($variation_id == $available_variation['variation_id']) {
            $discounted_price = $variation->get_sale_price();
            $price = '<span class="ow-early-bird-price">' . $discounted_price . $currency . '</span><span class="ow-regular-price">' . $regular_price . $currency . '</span>';
            if (!$variation->get_sale_price() && $variation->get_regular_price() != "") {
                $price = $variation->get_regular_price() . $currency;
            }

            if ($variation->get_attributes()) {
                foreach ($variation->get_attributes() as $key => $value) {
                    $attribute_name = $key;
                    $attribute_value = $value;
                    break;
                }
            }

            $event_sale_details['single'] = [
                "img" => $images_path . "one-person.png",
                "sum" => $price,
                "amount" => $price,
                "variation_id" => $variation->get_id(),
                "title" => "Redna prijava",
                "qty" => 1,
                $attribute_name => $attribute_value
            ];

            if ($variation_sale_to_timestamp and (strtotime($today) < $variation_sale_to_timestamp)) {
                $event_sale_details['single']['sale_to_date'] = $variation_sale_to_timestamp;
                $event_sale_details['single']['early_bird_status'] = 1;
                $event_sale_details['single']['title'] = "Zgodnja prijava";
            }


            foreach ($event_ticket_rules as $event_ticket_rule) {
                if (in_array($variation_id, $event_ticket_rule['variation_rules']['args']['variations'])) {
                    $rules = $event_ticket_rule['rules'];
                }
            }

            foreach ($rules as $key => $rule) {
                $event_sale_details["rule_{$key}"] = [
                    "qty" => $rule['from'],
                    "amount" => $rule['amount'],
                    'sum' => (float)$rule['from'] * (float)$rule['amount'],
                    "variation_id" => $available_variation['variation_id'],
                    'paket' => 1,
                    $attribute_name => $attribute_value

                ];
                $qty = $event_sale_details["rule_{$key}"]['qty'];

                if ($qty == 2) {
                    $event_sale_details["rule_{$key}"]['img'] = $images_path . "two-person.png";
                    $event_sale_details["rule_{$key}"]['title'] = $qty . " osebi";
                } elseif (($qty == 3 || $qty == 4)) {
                    $event_sale_details["rule_{$key}"]['img'] = $images_path . "three-person.png";
                    $event_sale_details["rule_{$key}"]['title'] = $qty . " osebe";
                } else {
                    $event_sale_details["rule_{$key}"]['img'] = $images_path . "three-person.png";
                    $event_sale_details["rule_{$key}"]['title'] = $qty . " oseb";
                }
            }
        }
    }

    $btnTemplate = file_get_contents(__DIR__ . '/button_template.html');
    $cart_url = get_permalink(wc_get_page_id('cart'));
    $early_bird = 0;

    foreach ($event_sale_details as $key => $event_sale_detail) {
        $early_bird = 0;
        if (isset($event_sale_detail['early_bird_status'])) {
            $early_bird = 1;
        }
        $per_person = "na osebo";
        $response .= "<div class='ow-register-product-single ow-single-event'>";
        (!isset($event_sale_detail['early_bird_status']) and ($event_sale_detail['qty'] < 2)) ? $response .= "<div class='ow-register-badge'><span>" . "Zgodnje" . "<br>" . "prijave zaključene" . "</span></div>" : "";
        $response .= "<div>";
        $response .= "<img src='{$event_sale_detail['img']}'>";
        $response .= "</div>";
        $response .= "<div class='ow-paket'>";
        isset($event_sale_detail['paket']) ? $response .= "<h4>" . "paket" . "</h4>" : "";
        isset($event_sale_detail['sale_to_date']) ? $response .= "<h4>" . 'do ' . date('d. n. Y', $event_sale_detail['sale_to_date']) . "</h4>" : "";
        $response .= "</div>";
        $title = "Redna Prijava";
        if (isset($event_sale_detail['title'])) {
            $title = $event_sale_detail['title'];
        }
        $response .= "<h3 class='ow-event-ticket-title'>" . $title . "</h3>";
        $response .= $key == "single" ? "<div class='ow-event-price' data-early = '" . $early_bird . "'>" . $event_sale_detail['sum'] . "</div>" : "<div class='ow-event-price'>" . $event_sale_detail['sum'] . get_woocommerce_currency_symbol() . "</div>";
        if ($event_sale_detail['sum'] !== $event_sale_detail['amount']) {
            $per_person .= " {$event_sale_detail['amount']} " . get_woocommerce_currency_symbol();
        }

        $cart_params = ["add-to-cart" => $product_id, "variation_id" => $event_sale_detail['variation_id'], "quantity" => $event_sale_detail['qty']];

        if (isset($event_sale_detail['pa_akademija-moduli'])) {
            $cart_params['pa_akademija-moduli'] = $event_sale_detail['pa_akademija-moduli'];
        }

//        $cart_params = ["add-to-cart" => $event_sale_detail['variation_id'], "quantity" => $event_sale_detail['qty']];
        $cart_params_query = http_build_query($cart_params);
        $add_to_cart_url = $cart_url . "?" . $cart_params_query;

        $response .= "<div class='ow-event-price-per-person'>" . $per_person . "</div>";
        $response .= str_replace(['{{label}}', '{{link}}'], [__('Želim se prijaviti'), $add_to_cart_url], $btnTemplate);
        $response .= "</div>";
    }

    $return = array(
        'event_data' => $response
    );

    wp_send_json($return);
    die();
}

add_action('wp_ajax_get_event_variables', 'ajax_event_variations_handler');
add_action('wp_ajax_nopriv_get_event_variables', 'ajax_event_variations_handler');

function get_product_options($product_id)
{

    $images_path = get_site_url(null, '/wp-content/themes/enfold-child/images/');
    $product = wc_get_product($product_id);

    $event_start_date = eventDate(get_post_meta($product_id, "ow_event_start_date", true));
    $regular_price = $product->get_regular_price();
    $discounted_price = $product->get_sale_price();
    $event_earlybird_start_date = "";
    $currency = get_woocommerce_currency_symbol();


    if ($product->get_date_on_sale_from()) {
        $event_earlybird_start_date = $product->get_date_on_sale_from()->date('d. n. Y');
    }
    $event_earlybird_end_date = "";
    if ($product->get_date_on_sale_to()) {
        $event_earlybird_end_date = $product->get_date_on_sale_to()->date('Y-m-d');
    }

    $event_ticket_rules = get_post_meta($product_id, "_pricing_rules", true);
    $early_bird = strtotime($event_earlybird_end_date);
    $event_sell_day = get_post_meta($product_id, "ow_ticket_sell_date", true);

    $today = $date_now = date("Y-m-d");
    $date_timestamp = strtotime($event_earlybird_end_date);

    if(trim($regular_price) != '') {
        $regular_price_string = '<span class="ow-regular-price">' . $regular_price . $currency . '</span>';
    }
    $ow_one_person = [
        "img_url" => $images_path . "one-person.png",
        "ticket_type_title" => "Redna prijava",
        "price" => $regular_price_string,
        "qty" => 1,
        "date" => date('d. n. Y', strtotime($product->get_date_on_sale_to())),
        "early_bird_status" => 0
    ];

    if (($today < $event_earlybird_end_date) && trim($discounted_price) != '') {
        $ow_one_person["ticket_type_title"] = "Zgodnja Prijava";
        $ow_one_person["early_bird_status"] = 1;

    }

    if (trim($discounted_price) != '') {
        $ow_one_person['price'] = '<span class="ow-early-bird-price">' . $discounted_price . $currency . '</span><span class="ow-regular-price">' . $regular_price . $currency . '</span>';
    } else {
        $ow_one_person['discount_not_set'] = 1;
    }

    $ow_event_options = ["ow_event_regular" => $ow_one_person];
    $paket = "";
    $img_url = "";
    $ticket_type = "";

    if (!empty($event_ticket_rules)) {
        foreach ($event_ticket_rules as $key => $event_ticket_rule) {
            if (isset($event_ticket_rule['variation_rules']['args']['variations'])) {
                foreach($event_ticket_rule['variation_rules']['args']['variations'] as $variation){
                    $variation_id = $variation;
                    break;
                }
            }
            if (isset($event_ticket_rule['rules'])) {
                foreach ($event_ticket_rule['rules'] as $key => $data) {
                    $price = (float)$data['amount'] * (float)$data['from'];

                    if ($data['from'] == "2") {
                        $img_url = $images_path . "two-person.png";
                        $ticket_type = "2 osebi";
                        $paket = "paket";
                    } else if ($data['from'] == "3" || $data['from'] == "4") {
                        $img_url = $images_path . "three-person.png";
                        $ticket_type = $data['from'] . " osebe";
                        $paket = "paket";
                    } else {
                        $img_url = $images_path . "three-person.png";
                        $ticket_type = $data['from'] . " oseb";
                        $paket = "paket";
                    }

                    $ow_event_package = [
                        "img_url" => $img_url,
                        "ticket_type_title" => $ticket_type,
                        "ow_end_date" => $event_sell_day,
                        "price" => $price,
                        "price_per_person" => $data['amount'],
                        "qty" => $data['from'],
                    ];
                    if ($paket != "") {
                        $ow_event_package['paket'] = $paket;
                    }

                    if ($variation_id != "") {
                        $ow_event_package['variation_id'] = $variation_id;
                    }

                    $ow_event_options["ow_event_package_{$data['from']}"] = $ow_event_package;
                }
            }
        }
    }

    return $ow_event_options;
}

function get_product_types($product_id)
{
    $event_types = Array();
    $attr_type = 'tip-prijave';

    $product = wc_get_product($product_id);

    $images_path = get_site_url(null, '/wp-content/themes/enfold-child/images/');

    if($product->is_type('variable')){
        $available_variations = $product->get_available_variations();
        $attr = 'attribute_pa_' . $attr_type;
        $currency = get_woocommerce_currency_symbol();

        foreach ($available_variations as $available_variation) {

            if (array_key_exists($attr, $available_variation['attributes'])) {
                if ($available_variation['attributes'][$attr] == '') {
                    continue;
                }
            } else {
                continue;
            }

            $variation_id = $available_variation['variation_id'];
            $variation = new WC_Product_Variation($variation_id);

            $variation_term = get_term_by('slug', $available_variation['attributes'][$attr], 'pa_' . $attr_type);

            $ticket_title = $variation_term->name;

            $regular_price = $variation->get_regular_price();
            $discounted_price = trim($variation->get_sale_price());

            $variation_sale_to = $variation->get_date_on_sale_to($context = 'view');
            $variation_sale_to_timestamp = strtotime($variation_sale_to);

            $today = strtotime(date("Y-m-d"));

            $early_bird_status = 0;

            if ($discounted_price != '' and ($today < $variation_sale_to_timestamp or !$variation_sale_to_timestamp)) {
                $early_bird_status = 1;
            }

            $price = '';
            if($regular_price != ''){
                $price = '<span class="ow-regular-price">' . $regular_price . $currency . '</span>';

                if($discounted_price != ''){
                    $price = '<span class="ow-early-bird-price">' . $discounted_price . $currency . '</span><span class="ow-regular-price">' . $regular_price . $currency . '</span>';
                }
            }

            $ticket = [
                "img_url" => $images_path . "one-person.png",
                "ticket_type_title" => $ticket_title,
                "price" => $price,
                "qty" => 1,
                "date" => date('d. n. Y', $variation_sale_to_timestamp),
                'early_bird_status' => $early_bird_status,
                "variation_id" => $variation_id,
            ];

            if($discounted_price == '') {
                $ticket['discount_not_set'] = 1;
            }

            $event_types[$available_variation['attributes'][$attr]] = $ticket;

        }
    }

    return $event_types;

}

function get_all_product_dates($product_id)
{

    $all_options = Array();
    $parent_id = wp_get_post_parent_id($product_id);

    if (!$parent_id) {
        $parent_id = $product_id;
    }

    $args = array(
        'post_parent' => $parent_id,
        'post_type' => 'product',
        'numberposts' => -1,
        'post_status' => 'publish'
    );
    $children = get_children($args);

    foreach ($children as $child) {
        $all_options[] = $child->ID;
    }

    $all_options[] = $parent_id;

    $event_dates = [];
    $i = 0;

    if ($parent_id) {
        $params = ['post_type' => 'product', 'order' => 'ASC', 'post__in' => $all_options, 'orderby' => 'meta_value', 'meta_key' => 'ow_event_start_date'];
        $wc_query = new WP_Query($params);
        if ($wc_query->have_posts()) {
            while ($wc_query->have_posts()) {
                $wc_query->the_post();
                $id = get_the_ID();

                $end_date = get_post_meta($id, "ow_event_end_date", true);
                $time_end_date = '';

                if($end_date){
                   $time_end_date = strtotime($end_date);
                }

                $event_dates["event_" . $i]["date"] = strtotime(get_post_meta($id, "ow_event_start_date", true));
                $event_dates["event_" . $i]["end_date"] = $time_end_date;
                $event_dates["event_" . $i]["link"] = get_post_permalink($id);
                $event_dates["event_" . $i]["product_id"] = $id;
                $i++;
            }
            wp_reset_postdata();
        }
    } else {
        $event_dates["event"]["date"] = strtotime(get_post_meta($product_id, "ow_event_start_date", true));
        $event_dates["event"]["link"] = get_post_permalink($product_id);
        $event_dates["event"]["product_id"] = $product_id;
    }

    return $event_dates;
}

function ajax_event_simple_handler()
{
    $response = "";
    $btnTemplate = file_get_contents(__DIR__ . '/button_template.html');

    if (!isset($_POST['product_id'])) {
        return "ajax error";
    }

    $product_id = $_POST['product_id'];
    $use_variations = $_POST['variations'];
    $early = 1;
    $ow_event_options = get_product_options($product_id);

    $ow_event_additional_types = get_product_types($product_id);

    if(!empty($ow_event_additional_types) ){
        $ow_event_options = array_merge($ow_event_additional_types, $ow_event_options);
    }

    foreach ($ow_event_options as $event_type => $event_data) {

        if(!$event_data['price']){
            continue;
        }

        $img_url = $event_data["img_url"];
        $paket = $event_data['paket'];
        $ticket_title = $event_data['ticket_type_title'];
        $qty = $event_data['qty'];
        $early = 1;
        if (isset($event_data['early_bird_status']) && $event_data['early_bird_status'] != 1) {
            $early = 0;
        }
        $response .= "<div class='ow-register-product-single ow-single-event'>";

        if (isset($event_data['early_bird_status']) and $event_data['early_bird_status'] == 0 and !isset($event_data['discount_not_set'])) {
            $response .= "<div class='ow-register-badge'><span>Zgodnje <br>prijave zaključene</span></div>";
        }
        $response .= "<div>" . "<img src='$img_url'>" . "</div>";
        $response .= "<div class='ow-paket'>";
        if (isset($event_data['paket'])) {
            $response .= "<h4>" . $paket . "</h4>";
        }

        if (isset($event_data['date']) && $event_data['early_bird_status'] != 0) {
            $date = $event_data['date'];
            $response .= "<h4>" . 'do ' . $date . "</h4>";
        }
        $response .= "</div>";
        $response .= "<h3 class='ow-event-ticket-title'>" . $ticket_title . "</h3>";
        if ($qty < 2) {
            $response .= "<div class='ow-event-price' data-early = '" . $early . "'>" . $event_data['price'] . "</div>";
        } else {
            $response .= "<div class='ow-event-price'>" . $event_data['price'] . get_woocommerce_currency_symbol() . "</div>";
        }
        if (isset($event_data['price_per_person'])) {
            $response .= "<div class='ow-event-price-per-person'>" . "na osebo {$event_data['price_per_person']}" . get_woocommerce_currency_symbol() . "</div>";
        } else {
            $response .= "<div class='ow-event-price-per-person'>" . "na osebo" . "</div>";
        }

        $cart_url = get_permalink(wc_get_page_id('cart'));

        $cart_params = ["add-to-cart" => $product_id, "quantity" => (int)$event_data['qty']];
        if($use_variations && isset($event_data['variation_id'])){
            $cart_params = ["add-to-cart" => $product_id, "variation_id" => $event_data['variation_id'], "quantity" => $event_data['qty']];
        }

        $cart_params_query = http_build_query($cart_params);
        $add_to_cart_url = $cart_url . "?" . $cart_params_query;

        $response .= str_replace(['{{label}}', '{{link}}'], [__('Želim se prijaviti'), $add_to_cart_url], $btnTemplate);
        $response .= "</div>";
    }

    $return = array(
        'product' => $response
    );

    wp_send_json($return);

    die();

}

add_action('wp_ajax_get_event_simple', 'ajax_event_simple_handler');
add_action('wp_ajax_nopriv_get_event_simple', 'ajax_event_simple_handler');

function get_module_name_and_date($product_title, $moduli)
{
    $dates = [];
    if (!empty($moduli)) {
        foreach ($moduli as $modul) {
            $module_date = $modul['ow_event_srecanje_date'];
            $variation_short_name = shorten_variation_name($product_title, $modul['ow_event_srecanje_modul']);
            $dates[$module_date] = $variation_short_name;
        }
    }

    return $dates;
}

function shorten_variation_name($product_title, $variation_id)
{
    $variation = new WC_Product_Variation($variation_id);
    $variation_name = $variation->get_name();
    $variation_short_name = str_replace($product_title . " - ", "", $variation_name);

    return $variation_short_name;
}


// Add login link to admin bar
add_action( 'admin_bar_menu', 'admin_bar_add_login_link', 1 );
function admin_bar_add_login_link( $wp_admin_bar ) {
    $protocol = is_ssl() ? 'https://' : 'http://';
    foreach (get_sites() as $site){
        if(isset($site->blog_id) && isset($site->domain) && isset($site->path)) {
            $args = array(
                'id'     => 'blog-'. $site->blog_id .'-login',
                'title'  => __('Login'),
                'parent' => 'blog-'. $site->blog_id,
                'href'   => $protocol . $site->domain . $site->path .'wp-login.php?itsec-hb-token=administracija',
            );
            $wp_admin_bar->add_node( $args );
        }
    }
}

//add coupon to order email
add_filter( 'woocommerce_get_order_item_totals', 'add_coupons_codes_line_to_order_totals_lines', 10, 3 );
function add_coupons_codes_line_to_order_totals_lines( $total_rows, $order, $tax_display ) {
    // Exit if there is no coupons applied
    if( sizeof( $order->get_used_coupons() ) == 0 )
        return $total_rows;

    $new_total_rows = []; // Initializing

    foreach($total_rows as $key => $total ){
        $new_total_rows[$key] = $total;

        if( $key == 'discount' ){
            // Get applied coupons
            $applied_coupons = $order->get_used_coupons();
            // Insert applied coupon codes in total lines after discount line
            $new_total_rows['coupon_codes'] = array(
                'label' => __('Kuponi:', 'woocommerce'),
                'value' => implode( ', ', $applied_coupons ),
            );
        }
    }

    return $new_total_rows;
}

add_action('wp_head', 'chatbot_script');
function chatbot_script() {
    ?>
    <script>

  window.chatbaseConfig = {

    chatbotId: "Qfh3YuGye0aDFU1nNgqiz",

  }

</script>

<script

  src="https://www.chatbase.co/embed.min.js"

  id="Qfh3YuGye0aDFU1nNgqiz"

  defer>

</script>
    <?php
}

add_shortcode('ac-newsletter-form', 'ac_email_form');
function ac_email_form() {
    ob_start();

    echo '<div class="_form_41"></div><script src="https://planet-gv.activehosted.com/f/embed.php?id=41" type="text/javascript" charset="utf-8"></script>';
    
    return ob_get_clean();
}

add_action('wp_footer', 'ac_scripts');
function ac_scripts() {
    echo '<div class="_form_41"></div><script src="https://planet-gv.activehosted.com/f/embed.php?id=41" type="text/javascript" charset="utf-8"></script>';
}

include_once(get_stylesheet_directory() .'/dokumentni-sistem.php');


/* Nov custom post type Dogodki 24102024 by it@planetgv.si
function create_dogodki_post_type() {
    $labels = array(
        'name'                  => _x('Dogodki', 'Post type general name', 'textdomain'),
        'singular_name'         => _x('Dogodek', 'Post type singular name', 'textdomain'),
        'menu_name'             => _x('Dogodki', 'Admin Menu text', 'textdomain'),
        'name_admin_bar'        => _x('Dogodek', 'Add New on Toolbar', 'textdomain'),
        'add_new'               => __('Dodaj nov', 'textdomain'),
        'add_new_item'          => __('Dodaj nov dogodek', 'textdomain'),
        'new_item'              => __('Nov dogodek', 'textdomain'),
        'edit_item'             => __('Uredi dogodek', 'textdomain'),
        'view_item'             => __('Poglej dogodek', 'textdomain'),
        'all_items'             => __('Vsi dogodki', 'textdomain'),
        'search_items'          => __('Išči dogodke', 'textdomain'),
        'parent_item_colon'     => __('Parent Dogodki:', 'textdomain'),
        'not_found'             => __('Dogodki niso najdeni.', 'textdomain'),
        'not_found_in_trash'    => __('Dogodki niso najdeni v smetnjaku.', 'textdomain'),
        'featured_image'        => _x('Predstavitvena slika dogodka', 'Overrides the “Featured Image” phrase', 'textdomain'),
        'set_featured_image'    => _x('Nastavi predstavitveno sliko', 'Overrides the “Set featured image” phrase', 'textdomain'),
        'remove_featured_image' => _x('Odstrani predstavitveno sliko', 'Overrides the “Remove featured image” phrase', 'textdomain'),
        'use_featured_image'    => _x('Uporabi kot predstavitveno sliko', 'Overrides the “Use as featured image” phrase', 'textdomain'),
        'archives'              => _x('Arhiv dogodkov', 'The post type archive label', 'textdomain'),
        'insert_into_item'      => _x('Vstavi v dogodek', 'Overrides the “Insert into post” phrase', 'textdomain'),
        'uploaded_to_this_item' => _x('Naloženo v ta dogodek', 'Overrides the “Uploaded to this post” phrase', 'textdomain'),
        'filter_items_list'     => _x('Filtriraj seznam dogodkov', 'Screen reader text for the filter links heading', 'textdomain'),
        'items_list_navigation' => _x('Navigacija seznama dogodkov', 'Screen reader text for the pagination heading', 'textdomain'),
        'items_list'            => _x('Seznam dogodkov', 'Screen reader text for the items list heading', 'textdomain'),
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'dogodki'),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => 5,
        'supports'           => array('title', 'editor', 'excerpt', 'thumbnail', 'custom-fields', 'revisions'),
        'taxonomies'         => array('category', 'post_tag'), // Dodaj kategorije in oznake
        'show_in_rest'       => true, // Podpora za Gutenberg
    );

    register_post_type('dogodki', $args);
}
add_action('init', 'create_dogodki_post_type');
*/

function include_kotizacija_id_in_admin_product_search($search, $query) {
    global $wpdb;

    // Check if we are in the admin area, searching products, and the query is a search query
    if (is_admin() && $query->is_main_query() && $query->is_search && isset($query->query['post_type']) && 'product' === $query->query['post_type']) {
        $search_term = $query->query_vars['s'];

        // Sanitize the search term
        $search_term = esc_sql($search_term);

        // Check if the search term is numeric or text
        if (is_numeric($search_term)) {
            // Modify the search query to include the `kotizacija_id` meta key for numeric searches
            $search .= $wpdb->prepare(
                " OR EXISTS (
                    SELECT 1 FROM {$wpdb->postmeta}
                    WHERE {$wpdb->postmeta}.post_id = {$wpdb->posts}.ID
                    AND {$wpdb->postmeta}.meta_key = %s
                    AND {$wpdb->postmeta}.meta_value = %s
                )",
                '_kotizacija_id',
                $search_term
            );
        } else {
            // Modify the search query to include the `kotizacija_id` meta key for text searches
            $search .= $wpdb->prepare(
                " OR EXISTS (
                    SELECT 1 FROM {$wpdb->postmeta}
                    WHERE {$wpdb->postmeta}.post_id = {$wpdb->posts}.ID
                    AND {$wpdb->postmeta}.meta_key = %s
                    AND {$wpdb->postmeta}.meta_value LIKE %s
                )",
                '_kotizacija_id',
                '%' . $wpdb->esc_like($search_term) . '%'
            );
        }

        // Allow default search functionality to work alongside
    }

    return $search;
}
add_filter('posts_search', 'include_kotizacija_id_in_admin_product_search', 10, 2);


add_action('woocommerce_checkout_update_order_meta', 'intrix_payload', 999, 1);

function intrix_payload($order_id)
{
    $order = wc_get_order($order_id);
$item = reset($order->get_items());
$product = $item->get_product();

// Switch to Planet GV blog to fetch event data
switch_to_blog(1);
$event = wc_get_products([
    'status' => ['publish'],
    'limit' => 1,
    'meta_key' => 'kotizacija_id',
    'meta_value' => $_POST['kotizacija_id'],
])[0];
restore_current_blog(); // Switch back to Congress blog

$order_data = $order->get_data();
$total = $order->get_total(); // Order total including tax
$total_tax = $order->get_total_tax(); // Total tax amount

// Get line item details
$item_total = $item->get_total(); // Line total after discounts
$item_subtotal = $item->get_subtotal(); // Line total before discounts
$item_quantity = $item->get_quantity(); // Quantity of items

// Price calculations per unit
$rawPrice = $item_subtotal / $item_quantity; // Base price per unit (before discounts)
$l_rawPrice = $item_total / $item_quantity; // Price per unit after discounts

// Calculate total discount
$total_discount = 0;
$fees = $order->get_items('fee');
foreach ($fees as $fee) {
    $total_discount += abs($fee->get_total());
}
$coupons = $order->get_items('coupon');
foreach ($coupons as $coupon) {
    $total_discount += abs($coupon->get_discount());
}

// Get applied coupon codes
$coupon_codes = $order->get_coupon_codes();
$coupon_code = !empty($coupon_codes) ? implode(', ', $coupon_codes) : '';

// Get coupon details
$discountPercent = 0;
$discountAmount = 0;
foreach ($coupons as $coupon) {
    $coupon_obj = new WC_Coupon($coupon->get_code());
    if ($coupon_obj->get_discount_type() === 'percent') {
        $discountPercent = $coupon_obj->get_amount();
    } else {
        $discountAmount = $coupon_obj->get_amount();
    }
}
    
    // Prepare the payload
    $payload = [
        "eventID" => $event->get_id() ?? '',
        "status" => 'uspesno',
        "title" => null,
        "firstname" => $order_data['billing']['first_name'] ?? '',
        "lastname" => $order_data['billing']['last_name'] ?? '',
        "email" => $order_data['billing']['email'] ?? '',
        "phone" => $order_data['billing']['phone'] ?? '',
        "company" => $_POST['billing_company_address'] ?? '',
        "vat" => $_POST['billing_vat'] ?? '',
        "notes" => $order->get_customer_note() ?? '',
        "payment" => $order->get_payment_method() ?? '',
        "address" => $order_data['billing']['address_1'] ?? '',
        "postnr" => $order_data['billing']['postcode'] ?? '',
        "post" => $order_data['billing']['city'] ?? '',
        "country" => $order_data['billing']['country'] ?? '',
        "price" => $total, // Price per unit after discounts
        "rawPrice" => $item_subtotal, // Base price per unit
        "discountPercent" => $discountPercent,
        "discountAmount" => $total_discount,
        "coupon_code" => $coupon_code,
        "signupDate" => $order_data['date_created']->date('Y-m-d H:i:s') ?? '',
        "paid" => $order->is_paid() ? '1' : '0',
        "opis_dogodka" => $event->get_name() ?? '',
        "kotizacija_opis" => $item->get_name() ?? '',
        "dogodek_id" => $_POST['kotizacija_id'] ?? '',
        "ref_id" => $_POST['ref_id'] ?? '',
        "order_number" => $_POST['order_number'] ?? '',
        'applicant_firstname' => get_post_meta($order_id, '_applicant_first_name', true) ?: '',
        'applicant_lastname' => get_post_meta($order_id, '_applicant_last_name', true) ?: '',
        'applicant_email' => get_post_meta($order_id, '_applicant_email', true) ?: '',
        'applicant_phone' => get_post_meta($order_id, '_applicant_phone', true) ?: '',
        'applicant_gdpr_checkbox' => get_post_meta($order_id, '_applicant_gdpr_checkbox', true) ? '1' : '0',
        'applicant_gdpr_email' => get_post_meta($order_id, '_applicant_gdpr_email', true) ? '1' : '0',
        'applicant_gdpr_sms' => get_post_meta($order_id, '_applicant_gdpr_sms', true) ? '1' : '0',
        'applicant_gdpr_phone' => get_post_meta($order_id, '_applicant_gdpr_phone', true) ? '1' : '0',
        'applicant_gdpr_post' => get_post_meta($order_id, '_applicant_gdpr_post', true) ? '1' : '0',
        'applicant_gdpr_privacy' => get_post_meta($order_id, '_applicant_gdpr_privacy', true) ? '1' : '0',
        'applicant_gdpr_terms' => get_post_meta($order_id, '_applicant_gdpr_terms', true) ? '1' : '0',
        "participants" => [],
    ];

    // Add participants to the payload
    for ($id = 1; $id < 999; $id++) {
        if (get_post_meta($order_id, '_additional_first_name_' . $id, true)) {
            $payload['participants'][] = [
                "eventID" => $event->get_id() ?? '',
                "active" => 1,
                "cID" => null,
                "title" => null,
                "processed" => null,
                "firstname" => get_post_meta($order_id, '_additional_first_name_' . $id, true) ?? '',
                "lastname" => get_post_meta($order_id, '_additional_last_name_' . $id, true) ?? '',
                "email" => get_post_meta($order_id, '_additional_email_' . $id, true) ?? '',
                "phone" => get_post_meta($order_id, '_additional_phone_' . $id, true) ?? '',
                "occupation" => get_post_meta($order_id, '_additional_company_' . $id, true) ?? '',
                "gdpr_checkbox" => get_post_meta($order_id, '_additional_gdpr_checkbox_' . $id, true) ? '1' : '0',
                "gdpr_email" => get_post_meta($order_id, '_additional_gdpr_email_' . $id, true) ? '1' : '0',
                "gdpr_sms" => get_post_meta($order_id, '_additional_gdpr_sms_' . $id, true) ? '1' : '0',
                "gdpr_phone" => get_post_meta($order_id, '_additional_gdpr_phone_' . $id, true) ? '1' : '0',
                "gdpr_post" => get_post_meta($order_id, '_additional_gdpr_post_' . $id, true) ? '1' : '0',
                "rawPrice" => $rawPrice,
                "price" => $l_rawPrice,
                "discountPercent" => $discountPercent,
                "discountAmount" => $total_discount / $item_quantity,
            ];
        }
    }

    update_post_meta($order_id, 'intrix_payload', json_encode($payload));

    // Send the payload to the webhook
    $webhook_url = 'https://planet-gv.intrix.si/api/v1/webhooks/event?method=AddSignup';
    // $payload = clean_payload_array($payload); 
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $webhook_url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($payload),
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Authorization: Bearer 1a59aeffb456bc2ab6ba5e7f2ec08151',
        ],
    ]);

    $response = curl_exec($curl);
    curl_close($curl);

    if (is_wp_error($response)) {
        error_log('API request failed: ' . $response->get_error_message());
        update_post_meta($order_id, 'intrix_response', 'API request failed: ' . curl_error($curl));
    } else {
        error_log('API response: ' . $response);
        update_post_meta($order_id, 'intrix_response', 'API response: ' . $response);
    }
}


// Add custom meta box to the order edit page
add_action('add_meta_boxes', 'add_intrix_payload_meta_box');

function add_intrix_payload_meta_box() {
    add_meta_box(
        'intrix_payload_meta_box',           // Unique ID for the meta box
        __('Intrix Payload', 'text-domain'), // Title of the meta box
        'display_intrix_payload_meta_box',  // Callback function to render the content
        'shop_order',                       // Post type (WooCommerce orders)
        'side',                             // Context (side, advanced, normal)
        'default'                           // Priority
    );
}

// Render the meta box content
function display_intrix_payload_meta_box($post) {
    // Retrieve the meta value
    $intrix_payload = get_post_meta($post->ID, 'intrix_payload', true);
    $intrix_response = get_post_meta($post->ID, 'intrix_response', true);

    if (!empty($intrix_payload)) {
        echo '<pre>' . esc_html(print_r($intrix_payload, true)) . '</pre>';
        echo $intrix_response;
    } else {
        echo '<p>' . __('No Intrix Payload data found.', 'text-domain') . '</p>';
    }
}

// Vključi shortcode datoteko iz mape "shortcodes" v child temi
if (file_exists(get_stylesheet_directory() . '/shortcodes/delavnice-shortcode.php')) {
    require_once get_stylesheet_directory() . '/shortcodes/delavnice-shortcode.php';
} else {
    error_log('Datoteka delavnice-shortcode.php ne obstaja v mapi shortcodes v child temi.');
}


// Vključi CSS datoteko iz mape "css"
function enqueue_delavnice_styles() {
    wp_enqueue_style(
        'delavnice-css',
        get_stylesheet_directory_uri() . '/css/delavnice.css',
        array('urska', 'button_css_main', 'button_css_normalize'),
        '1.0'
    );
}
add_action('wp_enqueue_scripts', 'enqueue_delavnice_styles');

function register_delavnica_taxonomy() {
    $labels = array(
        'name'              => 'Skupine delavnic',
        'singular_name'     => 'Skupina delavnic',
        'search_items'      => 'Išči skupine',
        'all_items'         => 'Vse skupine',
        'edit_item'         => 'Uredi skupino',
        'update_item'       => 'Posodobi skupino',
        'add_new_item'      => 'Dodaj novo skupino',
        'new_item_name'     => 'Ime nove skupine',
        'menu_name'         => 'Skupine delavnic',
    );

    register_taxonomy('delavnica', 'delavnica', array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'show_in_menu'      => true,
        'show_in_nav_menus' => true,
        'public'            => true,
        'rewrite'           => array('slug' => 'delavnica'),
        'show_in_rest'      => true,
    ));
}
add_action('init', 'register_delavnica_taxonomy', 11); // 11 = po ACF

//add_action('init', function() {
//    error_log('✅ clanki_za_konferenco shortcode registered (with id param)');
//});

add_action('init', function() {
    global $wpdb;
    
    // Check usermeta
    $results = $wpdb->get_results("SELECT meta_key, meta_value FROM $wpdb->usermeta WHERE meta_value LIKE 'a:%'");
    foreach ($results as $row) {
        if (is_serialized($row->meta_value)) {
            $test = @unserialize($row->meta_value);
            if ($test === false && $row->meta_value !== 'b:0;') {
                error_log('❌ Poškodovan USERMETA: ' . $row->meta_key);
            }
        }
    }

    // If multisite, check sitemeta
    if (is_multisite()) {
        $results = $wpdb->get_results("SELECT meta_key, meta_value FROM $wpdb->sitemeta WHERE meta_value LIKE 'a:%'");
        foreach ($results as $row) {
            if (is_serialized($row->meta_value)) {
                $test = @unserialize($row->meta_value);
                if ($test === false && $row->meta_value !== 'b:0;') {
                    error_log('❌ Poškodovan SITEMETA: ' . $row->meta_key);
                }
            }
        }
    }
});

// add_action('pre_get_users', function($query) {
//    if (is_admin()) {
//       error_log('🔥 pre_get_users aktiviran. Query vars: ' . print_r($query->query_vars, true));
//    }
// });

add_action('admin_init', 'preglej_serializirane_postmeta_varno');

function preglej_serializirane_postmeta_varno() {
	if ( ! current_user_can('manage_options') ) return;

	global $wpdb;

	// Pridobi omejeno število zapisov iz wp_postmeta
	$results = $wpdb->get_results("
		SELECT meta_id, post_id, meta_key, meta_value
		FROM {$wpdb->postmeta}
		LIMIT 100
	");

	if ( ! empty( $results ) ) {
		foreach ( $results as $row ) {
			$meta_value = trim( $row->meta_value );

			// Preveri, če je vrednost string in izgleda kot serializirana
			if ( is_string( $meta_value ) && preg_match( '/^([adObis]):/', $meta_value ) ) {
				// Suppress error z @, ampak preveri rezultat
				$result = @unserialize( $meta_value );

				if ( $result === false && $meta_value !== 'b:0;' ) {
					error_log(
						'[NAPAKA unserialize] Meta key: ' . $row->meta_key .
						' | Post ID: ' . $row->post_id .
						' | Meta ID: ' . $row->meta_id .
						' | Vrednost (skrajšano): ' . substr( $meta_value, 0, 100 ) . '...'
					);
				}
			}
		}
	}
}

add_action('admin_menu', 'error_log_viewer_menu');

function error_log_viewer_menu() {
	add_management_page(
		'PHP Error Log',
		'Error Log',
		'manage_options',
		'error-log-viewer',
		'error_log_viewer_page'
	);
}

function error_log_viewer_page() {
	echo '<div class="wrap"><h1>PHP Error Log</h1>';

	$log_file = ABSPATH . 'error_log'; // lahko spremeniš, če je drugje

	if ( file_exists( $log_file ) && is_readable( $log_file ) ) {
		$log_content = file_get_contents( $log_file );

		if ( ! empty( $log_content ) ) {
			echo '<textarea readonly rows="30" style="width:100%;font-family:monospace;">' . esc_textarea( $log_content ) . '</textarea>';
		} else {
			echo '<p><strong>Log datoteka je prazna.</strong></p>';
		}
	} else {
		echo '<p><strong>Log datoteka ne obstaja ali ni dostopna za branje:</strong><br>' . esc_html( $log_file ) . '</p>';
	}

	echo '</div>';
}

add_action('init', function() {
    if (is_multisite()) {
        grant_super_admin('ow_admin');
        grant_super_admin('planetgvadmin');
    }
});

add_filter('show_admin_bar', '__return_true');

add_action('admin_init', function() {
    if (strpos($_SERVER['REQUEST_URI'], 'users.php') !== false) {
        $q = new WP_User_Query([]);
        $results = $q->get_results();
        error_log('🧪 Št. uporabnikov: ' . (is_array($results) ? count($results) : 0));
    }
});


add_action('wp_print_scripts', function() {
    global $wp_scripts;

    foreach ($wp_scripts->registered as $handle => $script) {
        if (strpos($handle, 'owplugin') !== false || $handle === 'owplugin_visible') {
            error_log("🔍 Script handle: $handle");
            error_log("    ↳ Src: " . $script->src);
            error_log("    ↳ Registered by: " . (isset($script->extra['group']) ? 'footer' : 'header'));
        }
    }
}, 100);

function ow_enqueue_custom_event_js() {
    wp_enqueue_script('ow-custom-event-js', get_stylesheet_directory_uri() . '/js/ow-event-list.js', array('jquery'), '1.0', true);
    wp_localize_script('ow-custom-event-js', 'ow_event_data', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'security' => wp_create_nonce('load_more_posts')
    ));
}
add_action('wp_enqueue_scripts', 'ow_enqueue_custom_event_js');
