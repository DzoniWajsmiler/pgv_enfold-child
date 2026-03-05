<?php
// Preprečitev neposrednega dostopa do datoteke
if(!defined('ABSPATH')) {
    exit;
}

function a_ajax_filterposts_handler() {
    // Preverite nonce, če ga uporabljate (priporočano za varnost)
    // if(!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'ajax_nonce')) {
    //     wp_send_json_error('Neveljavna varnostna koda.');
    //     wp_die();
    // }

    $posts = "";
    $categories_list = array();

    $category = isset($_POST['category']) ? sanitize_text_field($_POST['category']) : '';
    $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
    $show = isset($_POST['show']) ? intval($_POST['show']) : 10;

    if (!$category || $category === "all") {
        $all_categories = get_terms(array('taxonomy' => 'category', 'hide_empty' => false));
        foreach ($all_categories as $single_category) {
            $categories_list[] = $single_category->slug;
        }
    } else {
        $categories_list[] = $category;
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
            )
        )
    );

    $the_query = new WP_Query($args);

    if ($the_query->have_posts()) {
        ob_start();
        while ($the_query->have_posts()) {
            $the_query->the_post();
            // Predpostavljam, da imate funkcijo single_blog_post_html za formatiranje prispevka
            $posts .= single_blog_post_html(true, true, "2", "purple", $category);
        }
        wp_reset_postdata();
        $posts = ob_get_clean();
    } else {
        $posts = '<p>V tej kategoriji trenutno ni prispevkov.</p>';
    }

    wp_send_json_success(array('posts' => $posts));
    wp_die();
}

// Registracija Ajax handlerjev
add_action('wp_ajax_filterposts', 'a_ajax_filterposts_handler');
add_action('wp_ajax_nopriv_filterposts', 'a_ajax_filterposts_handler');
