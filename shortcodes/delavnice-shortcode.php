<?php
function delavnice_shortcode($atts) {
    ob_start();

    // INLINE CSS za grid layout in meni
    echo '<style>
      html {
          scroll-behavior: smooth;
      }
      .ow_event_list_sec.ow-cat-grid-wrap {
          display: grid;
          grid-gap: 44px;
          grid-template-columns: repeat(4, 1fr);
      }
      @media (max-width:1024px) {
          .ow_event_list_sec.ow-cat-grid-wrap {
              grid-template-columns: repeat(2, 1fr);
          }
      }
      @media (max-width:600px) {
          .ow_event_list_sec.ow-cat-grid-wrap {
              grid-template-columns: 1fr;
              padding-left: 20px;
              padding-right: 20px;
          }
      }
      .ow-cat-menu {
          display: flex;
          flex-wrap: wrap;
          gap: 10px;
          margin: 20px 0 40px 0;
      }
      .ow-cat-button.ow-cat-control {
          padding: 10px 20px;
          background-color: #6a1b9a;
          color: #fff;
          text-decoration: none;
          border-radius: 5px;
          font-weight: 600;
          font-family: "Montserrat", sans-serif;
          transition: background-color 0.3s ease;
      }
      .ow-cat-button.ow-cat-control:hover {
          background-color: #4a148c;
      }
    </style>';

    echo '<div class="ow-category-events-wrap">';

    // === MENI GUMBI

$menu_terms = get_terms([
    'taxonomy'   => 'delavnica',
    'hide_empty' => true,
    'orderby'    => 'name' // FIX: prepreči SQL napako
]);


    if (!empty($menu_terms) && !is_wp_error($menu_terms)) {
        echo '<div class="ow-cat-menu">';
        foreach ($menu_terms as $term) {
            echo '<a href="#' . esc_attr($term->slug) . '" class="ow-cat-button ow-anchor-link">' . esc_html($term->name) . '</a>';
        }
        echo '</div>';
    }

    // === IZPOSTAVLJENO (po tagu)
    $izpostavljeno_query = new WP_Query([
        'post_type' => 'delavnica',
        'posts_per_page' => 4,
        'tax_query' => [[
            'taxonomy' => 'post_tag',
            'field'    => 'slug',
            'terms'    => 'izpostavljeno',
        ]]
    ]);

    if ($izpostavljeno_query->have_posts()) {
        echo '<h3 class="delavnica-sklop-naslov">Izpostavljeno</h3>';
        echo '<div class="ow_event_list_sec ow-cat-grid-wrap">';
        while ($izpostavljeno_query->have_posts()) {
            $izpostavljeno_query->the_post();
            echo delavnica_card_html();
        }
        echo '</div>';
        wp_reset_postdata();
    }

    // === PO TAXONOMY: delavnica
    foreach ($menu_terms as $term) {
        $term_query = new WP_Query([
            'post_type' => 'delavnica',
            'posts_per_page' => -1,
            'tax_query' => [[
                'taxonomy' => 'delavnica',
                'field'    => 'term_id',
                'terms'    => $term->term_id
            ]]
        ]);

        if ($term_query->have_posts()) {
            echo '<h3 id="' . esc_attr($term->slug) . '" class="delavnica-sklop-naslov ' . esc_attr($term->slug) . '">' . esc_html($term->name) . '</h3>';
            echo '<div class="ow_event_list_sec ow-cat-grid-wrap">';
            while ($term_query->have_posts()) {
                $term_query->the_post();
                echo delavnica_card_html();
            }
            echo '</div>';
            wp_reset_postdata();
        }
    }

    echo '</div>'; // .ow-category-events-wrap

    return ob_get_clean();
}
add_shortcode('delavnice', 'delavnice_shortcode');

// === FUNKCIJA za prikaz ENE kartice delavnice ===
function delavnica_card_html() {
    $workshop_name = get_field('workshop-name');
    $ext_url       = get_field('ext_url');
    $slika         = get_field('slika');
    $img_src       = $slika ? wp_get_attachment_image_url($slika['ID'], 'full') : '';

    // Preveri, če obstaja slika – ustrezno nastavi inline style
    if ($img_src) {
        $item_style = 'style="height:440px; display:flex; flex-direction: column;"';
    } else {
        $item_style = 'style="min-height:200px; display:flex; flex-direction: column;"';
    }

    ob_start();
    ?>
<div class="ow-single-event prevent-def ow_event_grid_item" <?php echo $item_style; ?>>
        <div class="ow_list_event_details" style="flex:1; display:flex; flex-direction: column; margin:0; padding:0;">
            <div class="ow-list-content" style="flex:1; margin:0; padding:0;">
                <?php if ($img_src): ?>
                    <img src="<?php echo esc_url($img_src); ?>" alt="<?php echo esc_attr($workshop_name); ?>" class="delavnica-slika" style="display:block; width:100%;">
                <?php endif; ?>
                <h2 class="ow_list_title" style="margin:0 0 10px 0; padding:0;"><?php echo esc_html($workshop_name); ?></h2>
                <p class="delavnica-termin" style="margin:0; padding:0; line-height:1;">TERMIN PO DOGOVORU</p>
            </div>
            <?php if ($ext_url): ?>
                <div class="ow_button_container" style="margin-top: 0; padding:0;">
                    <a class="ow-button custom_button button--1 color-purple" href="<?php echo esc_url($ext_url); ?>" target="_self">
                        Več informacij
                        <span class="button__container">
                            <span class="circle top-left"></span>
                            <span class="circle top-left"></span>
                            <span class="circle top-left"></span>
                            <span class="button__bg"></span>
                            <span class="circle bottom-right"></span>
                            <span class="circle bottom-right"></span>
                            <span class="circle bottom-right"></span>
                        </span>
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
