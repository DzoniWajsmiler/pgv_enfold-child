<?php
function delavnice_shortcode($atts) {
    // Privzete atribute
    $atts = shortcode_atts(array(
        'posts_per_page' => -1, // Privzeto prikaži vse delavnice
        'taxonomy' => '', // Dodan nov atribut za filtriranje po taxonomy
    ), $atts, 'delavnice');

    // Argumenti za WP_Query
    $args = array(
        'post_type' => 'delavnica', // Post type je "delavnica"
        'posts_per_page' => $atts['posts_per_page'],
    );

    // Če je določen filter za taxonomy, dodaj tax_query
    if (!empty($atts['taxonomy'])) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'delavnica', // Uporabite vašo taxonomy "delavnica"
                'field'    => 'slug',
                'terms'    => $atts['taxonomy'],
            ),
        );
    }

    $query = new WP_Query($args);

    // Začetek izhoda
    $output = '<div class="delavnice-container">';

    if ($query->have_posts()) :
        while ($query->have_posts()) : $query->the_post();
            $output .= '<div class="delavnica">';
            
            // Prva vrstica: "DELAVNICA"
            $output .= '<p class="delavnica-oznaka">DELAVNICA</p>';

            // Preveri, ali je v shortcode naveden parameter taxonomy="top4"
            if ($atts['taxonomy'] === 'top4') {
                // Slika delavnice (samo, če je parameter taxonomy="top4")
                $slika = get_field('slika');
                if ($slika) {
                    // Uporabi WordPressovo funkcijo za pridobivanje slike z največ 300px širine
                    $slika_url = wp_get_attachment_image_src($slika['ID'], 'delavnica-slika')[0];
                    $output .= '<a href="' . get_field('ext_url') . '">'; // Uporabi polje ext_url za povezavo
                    $output .= '<img src="' . esc_url($slika_url) . '" alt="' . esc_attr($slika['alt']) . '" class="delavnica-slika">';
                    $output .= '</a>'; // Zaprite povezavo
                }
            }

            // Ime delavnice
            $output .= '<h2 class="delavnica-naslov">' . get_the_title() . '</h2>';

            // Termin po dogovoru
            $output .= '<p class="delavnica-termin">TERMIN PO DOGOVORU</p>';

            // Gumb za več informacij (uporabi polje ext_url za povezavo)
            $ext_url = get_field('ext_url'); // Pridobi vrednost polja ext_url
            $output .= '<a href="' . esc_url($ext_url) . '" class="ow-button custom_button button--1 color-purple">VEČ INFORMACIJ</a>';

            $output .= '</div>';
        endwhile;
        wp_reset_postdata();
    else :
        $output .= '<p>Ni delavnic.</p>';
    endif;

    $output .= '</div>';

    return $output;
}
// Registriraj shortcode
add_shortcode('delavnice', 'delavnice_shortcode');