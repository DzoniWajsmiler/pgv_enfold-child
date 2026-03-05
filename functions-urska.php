<?php

//submenu template
add_filter('wp_nav_menu_objects', 'my_wp_nav_menu_objects', 10, 2);

function my_wp_nav_menu_objects($items)
{
    foreach ($items as &$item) {
        $image = get_field('menu_image', $item);
        $description = get_field('menu_description', $item);
        $link_text = get_field('menu_link_text', $item);

        $menu_item_title_template = '';
        $menu_item_description_template = '';

        if ($image) {
            $menu_item_title_template .= ' <div class="ow-menu-img" style="background-image:url(' . $image['url'] . ');"></div>';
        }

        $menu_item_title_template .= $item->title;

        if ($description) {
            $menu_item_description_template .= ' <div class="ow-menu-description">' . $description . '</div>';
        }

        if ($link_text) {
            $menu_item_description_template .= ' <div class="ow-menu-link">' . $link_text . '<img src="' . get_home_url() . '/wp-content/themes/enfold-child/images/arrow.svg"></div>';
        }

        if ($menu_item_title_template) {
            $item->title = $menu_item_title_template;
        }
        if ($menu_item_description_template) {
            $item->description = $menu_item_description_template;
        }

    }

    return $items;
}


if (!class_exists('avia_responsive_mega_menu')) {

    /**
     * The avia walker is the frontend walker and necessary to display the menu, this is a advanced version of the wordpress menu walker
     * @package WordPress
     * @since 1.0.0
     * @uses Walker
     */
    class avia_responsive_mega_menu extends Walker
    {
        /**
         * @see Walker::$tree_type
         * @var string
         */
        var $tree_type = array('post_type', 'taxonomy', 'custom');

        /**
         * @see Walker::$db_fields
         * @todo Decouple this.
         * @var array
         */
        var $db_fields = array('parent' => 'menu_item_parent', 'id' => 'db_id');

        /**
         * @var int $columns
         */
        var $columns = 0;

        /**
         * @var int $max_columns maximum number of columns within one mega menu
         */
        var $max_columns = 0;

        /**
         * @var int $rows holds the number of rows within the mega menu
         */
        var $rows = 1;

        /**
         * @var array $rowsCounter holds the number of columns for each row within a multidimensional array
         */
        var $rowsCounter = array();

        /**
         * @var string $mega_active hold information whetever we are currently rendering a mega menu or not
         */
        var $mega_active = 0;

        /**
         * @var array $grid_array holds the grid classes that get applied to the mega menu depending on the number of columns
         */
        var $grid_array = array();

        /**
         * @var stores if we already have an active first level main menu item.
         */
        var $active_item = false;


        /**
         * @var stores if we got a top or a sidebar main menu.
         */
        var $top_menu = true;


        /**
         * @var stores if we got a text menu or a single burger icon
         */
        var $icon_menu = true;

        /**
         * @var stores if we got a top or a sidebar main menu.
         */
        var $blog_id = false;

        /**
         * @var stores the number of first level menu items
         */
        var $first_level_count = 0;

        /**
         * @var stores if mega menu is active
         */
        var $mega_allowed = true;


        /**
         *
         * Constructor that sets the grid variables
         *
         */
        function __construct($options = array())
        {
            $this->grid_array = array(

                1 => "three units",
                2 => "six units",
                3 => "nine units",
                4 => "twelve units",
                5 => "twelve units",
                6 => "twelve units"
            );

            $this->top_menu = avia_get_option('header_position', 'header_top') == 'header_top' ? true : false;

            /**
             * Allows to alter default settings Enfold-> Main Menu -> General -> Menu Items for Desktop
             * @since 4.4.2
             */
            $this->icon_menu = apply_filters('avf_burger_menu_active', avia_is_burger_menu(), $this);

            if (avia_get_option('frontpage') && avia_get_option('blogpage')) {
                $this->blog_id = avia_get_option('blogpage');
            }

            if (isset($options['megamenu']) && $options['megamenu'] == "disabled") $this->mega_allowed = false;

            if ($this->icon_menu) {
                //$this->mega_active = false;
                //$this->mega_allowed = false;
            }
        }


        /**
         * @param string $output Passed by reference. Used to append additional content.
         * @param int $depth Depth of page. Used for padding.
         * @see Walker::start_lvl()
         *
         */
        function start_lvl(&$output, $depth = 0, $args = array())
        {
            $indent = str_repeat("\t", $depth);
            if ($depth === 0) $output .= "\n{replace_one}\n";
            $output .= "\n$indent<ul class=\"sub-menu\">\n";
        }

        /**
         * @param string $output Passed by reference. Used to append additional content.
         * @param int $depth Depth of page. Used for padding.
         * @see Walker::end_lvl()
         *
         */
        function end_lvl(&$output, $depth = 0, $args = array())
        {
            $indent = str_repeat("\t", $depth);
            $output .= "$indent</ul>\n";

            if ($depth === 0) {
                if ($this->mega_active && $this->mega_allowed) {

                    $output .= "\n</div>\n";
                    $output = str_replace("{replace_one}", "<div class='avia_mega_div avia_mega" . $this->max_columns . " " . $this->grid_array[$this->max_columns] . "'>", $output);
                    $output = str_replace("{last_item}", "avia_mega_menu_columns_last", $output);

                    foreach ($this->rowsCounter as $row => $columns) {
                        $output = str_replace("{current_row_" . $row . "}", "avia_mega_menu_columns_" . $columns . " " . $this->grid_array[1], $output);
                    }

                    $this->columns = 0;
                    $this->max_columns = 0;
                    $this->rowsCounter = array();

                } else {
                    $output = str_replace("{replace_one}", "", $output);
                }
            }
        }

        /**
         * @param string $output Passed by reference. Used to append additional content.
         * @param object $item Menu item data object.
         * @param int $depth Depth of menu item. Used for padding.
         * @param int $current_page Menu item ID.
         * @param object $args
         * @see Walker::start_el()
         *
         */
        function start_el(&$output, $item, $depth = 0, $args = array(), $current_object_id = 0)
        {
            global $wp_query;

            //set maxcolumns
            if (!isset($args->max_columns)) $args->max_columns = 6;

            $item = apply_filters('avf_menu_items', $item);
            $item_output = $li_text_block_class = $column_class = "";

            if ($depth === 0) {
                $this->first_level_count++;
                $this->mega_active = get_post_meta($item->ID, '_menu-item-avia-megamenu', true);
                $style = get_post_meta($item->ID, '_menu-item-avia-style', true);
            }


            if (!empty($item->url) && strpos($item->url, "[domain]") !== false) {
                $replace = str_replace("http://", "", get_home_url());
                $replace = str_replace("https://", "", $replace);
                $item->url = str_replace("[domain]", $replace, $item->url);
            }


            if ($depth === 1 && $this->mega_active && $this->mega_allowed) {
                $this->columns++;

                //check if we have more than $args['max_columns'] columns or if the user wants to start a new row
                if ($this->columns > $args->max_columns || (get_post_meta($item->ID, '_menu-item-avia-division', true) && $this->columns != 1)) {
                    $this->columns = 1;
                    $this->rows++;
                    $output .= "\n</ul><ul class=\"sub-menu avia_mega_hr\">\n";
                    $output = str_replace("{last_item}", "avia_mega_menu_columns_last", $output);
                } else {
                    $output = str_replace("{last_item}", "", $output);
                }

                $this->rowsCounter[$this->rows] = $this->columns;

                if ($this->max_columns < $this->columns) $this->max_columns = $this->columns;


                $title = apply_filters('the_title', $item->title, $item->ID);


                if ($title != "&#8211;" && trim($title) != "-" && $title != '"-"') //fallback for people who copy the description o_O
                {
                    $heading_title = do_shortcode($title);

                    if (!empty($item->url) && $item->url != "#" && $item->url != 'http://') {
                        $heading_title = "<a href='" . $item->url . "'>{$title}</a>";
                    }

                    $item_output .= "<span class='mega_menu_title heading-color av-special-font'>" . $heading_title . "</span>";

                    if (!empty($item->description)) {
                        $item_description = $item->description;

                        if (!empty($item->url) && $item->url != "#" && $item->url != 'http://') {
                            $item_description = "<a href='" . $item->url . "'>{$item->description}</a>";
                        }
                        $item_output .= "<span class='mega_menu_ow_description'>" . $item_description . "</span>";
                    }
                }


                $column_class = ' {current_row_' . $this->rows . '} {last_item}';

                if ($this->columns == 1) {
                    $column_class .= " avia_mega_menu_columns_first";
                }
            } else if ($depth >= 2 && $this->mega_active && $this->mega_allowed && get_post_meta($item->ID, '_menu-item-avia-textarea', true)) {
                $li_text_block_class = 'avia_mega_text_block ';

                $item_output .= do_shortcode($item->post_content);


            } else {
                $attributes = !empty($item->attr_title) ? ' title="' . esc_attr($item->attr_title) . '"' : '';
                $attributes .= !empty($item->target) ? ' target="' . esc_attr($item->target) . '"' : '';
                $attributes .= !empty($item->xfn) ? ' rel="' . esc_attr($item->xfn) . '"' : '';
                $attributes .= !empty($item->url) ? ' href="' . esc_attr($item->url) . '"' : '';

                if ('inactive' != avia_get_option('markup')) {
                    $attributes .= ' itemprop="url"';
                }


                $item_output .= $args->before;
                $item_output .= '<a' . $attributes . '><span class="avia-bullet"></span>';
                $item_output .= $args->link_before . '<span class="avia-menu-text">' . do_shortcode(apply_filters('the_title', $item->title, $item->ID)) . "</span>" . $args->link_after;
                if ($depth === 0) {
                    if (!empty($item->description)) {
                        $item_output .= '<span class="avia-menu-subtext">' . do_shortcode($item->description) . "</span>";
                    }

                    $item_output .= '<span class="avia-menu-fx"><span class="avia-arrow-wrap"><span class="avia-arrow"></span></span></span>';
                }

                $item_output .= '</a>';
                $item_output .= $args->after;
            }


            $class_names = $value = '';
            $indent = ($depth) ? str_repeat("\t", $depth) : '';
            $classes = empty($item->classes) ? array() : (array)$item->classes;
            if (isset($style)) $classes[] = $style;

            if ($depth === 0 && $key = array_search('current-menu-item', $classes)) {
                if ($this->active_item) {
                    unset($classes[$key]);
                } else {
                    $this->active_item = true;
                }
            }


            $class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item));
            if ($depth === 0 && $this->mega_active && $this->mega_allowed) $class_names .= " menu-item-mega-parent ";
            if ($depth === 0) $class_names .= " menu-item-top-level menu-item-top-level-" . $this->first_level_count;

            //highlight correct blog page
            if ($depth === 0 && $this->blog_id && $this->blog_id == $item->object_id && is_singular('post')) {
                $class_names .= " current-menu-item";
            }


            $class_names = ' class="' . $li_text_block_class . esc_attr($class_names) . $column_class . '"';

            $output .= $indent . '<li id="menu-item-' . $item->ID . '"' . $value . $class_names . '>';


            $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
        }

        /**
         * @param string $output Passed by reference. Used to append additional content.
         * @param object $item Page data object. Not used.
         * @param int $depth Depth of page. Not Used.
         * @see Walker::end_el()
         *
         */
        function end_el(&$output, $item, $depth = 0, $args = array())
        {
            $output .= "</li>\n";
        }
    }
}


/*
 * Function creates post duplicate as a draft and redirects then to the edit post screen
 */
function rd_duplicate_post_as_draft()
{
    global $wpdb;
    if (!(isset($_GET['post']) || isset($_POST['post']) || (isset($_REQUEST['action']) && 'rd_duplicate_post_as_draft' == $_REQUEST['action']))) {
        wp_die('No post to duplicate has been supplied!');
    }

    /*
     * Nonce verification
     */
    if (!isset($_GET['duplicate_nonce']) || !wp_verify_nonce($_GET['duplicate_nonce'], basename(__FILE__)))
        return;

    /*
     * get the original post id
     */
    $post_id = (isset($_GET['post']) ? absint($_GET['post']) : absint($_POST['post']));
    /*
     * and all the original post data then
     */
    $post = get_post($post_id);

    /*
     * if you don't want current user to be the new post author,
     * then change next couple of lines to this: $new_post_author = $post->post_author;
     */
    $current_user = wp_get_current_user();
    $new_post_author = $current_user->ID;

    /*
     * if post data exists, create the post duplicate
     */
    if (isset($post) && $post != null) {

        /*
         * new post data array
         */
        $args = array(
            'comment_status' => $post->comment_status,
            'ping_status' => $post->ping_status,
            'post_author' => $new_post_author,
            'post_content' => $post->post_content,
            'post_excerpt' => $post->post_excerpt,
            'post_name' => $post->post_name,
            'post_parent' => $post->post_parent,
            'post_password' => $post->post_password,
            'post_status' => 'draft',
            'post_title' => $post->post_title,
            'post_type' => $post->post_type,
            'to_ping' => $post->to_ping,
            'menu_order' => $post->menu_order
        );

        /*
         * insert the post by wp_insert_post() function
         */
        $new_post_id = wp_insert_post($args);

        /*
         * get all current post terms ad set them to the new post draft
         */
        $taxonomies = get_object_taxonomies($post->post_type); // returns array of taxonomy names for post type, ex array("category", "post_tag");
        foreach ($taxonomies as $taxonomy) {
            $post_terms = wp_get_object_terms($post_id, $taxonomy, array('fields' => 'slugs'));
            wp_set_object_terms($new_post_id, $post_terms, $taxonomy, false);
        }

        /*
         * duplicate all post meta just in two SQL queries
         */
        $post_meta_infos = $wpdb->get_results("SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id=$post_id");
        if (count($post_meta_infos) != 0) {
            $sql_query = "INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) ";
            foreach ($post_meta_infos as $meta_info) {
                $meta_key = $meta_info->meta_key;
                if ($meta_key == '_wp_old_slug') continue;
                $meta_value = addslashes($meta_info->meta_value);
                $sql_query_sel[] = "SELECT $new_post_id, '$meta_key', '$meta_value'";
            }
            $sql_query .= implode(" UNION ALL ", $sql_query_sel);
            $wpdb->query($sql_query);
        }


        /*
         * finally, redirect to the edit post screen for the new draft
         */
        wp_redirect(admin_url('post.php?action=edit&post=' . $new_post_id));
        exit;
    } else {
        wp_die('Post creation failed, could not find original post: ' . $post_id);
    }
}

add_action('admin_action_rd_duplicate_post_as_draft', 'rd_duplicate_post_as_draft');

/*
 * Add the duplicate link to action list for post_row_actions
 */
function rd_duplicate_post_link($actions, $post)
{
    if (current_user_can('edit_posts')) {
        $actions['duplicate'] = '<a href="' . wp_nonce_url('admin.php?action=rd_duplicate_post_as_draft&post=' . $post->ID, basename(__FILE__), 'duplicate_nonce') . '" title="Duplicate this item" rel="permalink">Duplicate</a>';
    }
    return $actions;
}

add_filter('post_row_actions', 'rd_duplicate_post_link', 10, 2);


//scroll to form if error
add_filter('gform_confirmation_anchor', '__return_true');

add_filter('gform_phone_formats', 'custom_phone_format');
function custom_phone_format($phone_formats)
{
    $phone_formats['sl'] = array( //only allow number in phone number
        'label' => 'OW Custom format',
        'regex' => '/^[0-9]*$/'
    );

    return $phone_formats;
}


// minimum characters count for phone number field
add_filter("gform_field_validation_3_3", "validate_chars_count", 10, 4);
add_filter("gform_field_validation_2_4", "validate_chars_count", 10, 4);
function validate_chars_count($result, $value, $form, $field)
{
    if (strlen($value) > 0 && strlen($value) < 6) { //Minimum number of characters
        $result["is_valid"] = false;
        $result["message"] = __("Nepravilni format telefonske številke", "ow-translations");
    }
    return $result;
}


/* category list */
require_once(get_stylesheet_directory() . '/custom/datetime.php');

add_shortcode('ow-event-list', 'ow_event_list');

function ow_event_list($atts, $content = null)
{
    $defaults = array(
        "cat" => "",
        "style" => "grid",
        "cat-filter" => "yes",
        "show" => "24",
        "pagination" => "yes",
        'sort' => 'ASC',
        "event_type" => "",
        "all_cats_text" => "Vse",
        "load_more_text" => "Prikaži več",
        "open_arhiv_text" => "Arhiv",
        "close_arhiv_text" => "Prihajajoča izobraževanja",
        "related-mode" => "no",
        "cat_ids" => ""
    );

    $params = shortcode_atts($defaults, $atts);
    $cat = $params['cat'];
    $style = $params['style'];
    $cat_f = $params['cat-filter'];
    $show = $params['show'];
    $pagination = $params['pagination'];
    $sort = $params['sort'];
    $event_type = $params['event_type'];
    $all_cats_text = $params['all_cats_text'];
    $load_more_text = $params['load_more_text'];
    $open_arhiv_text = $params['open_arhiv_text'];
    $close_arhiv_text = $params['close_arhiv_text'];
    $related_mode = $params['related-mode'];
    $cat_ids = $params['cat_ids'];
    $all_cats_class = '';
    $active_cat_id = null;
    $excluded_ids = array();

    $taxonomy = 'product_cat';
    $post_id = get_the_id();


    if ($cat_ids !== "") {
        $terms_ids = array_filter(explode("+", $cat_ids));

    } else if($style == "slider"){
        $terms_ids = get_field("kategorije_izobrazevanj");
    } else {
        $cat_parent_obj = get_term_by('slug', $cat, $taxonomy);
        $cat_id = $cat_parent_obj->term_id;

        if ($related_mode == "yes") {
            $terms_ids = $cat_id;
        } else {
            $terms_ids = get_term_children($cat_id, $taxonomy);
        }
    }

    if ($related_mode == "yes") {
        //exclude current post + all child variations
        $excluded_ids = array();
        $excluded_ids[] = $post_id;

        $parent_id = wp_get_post_parent_id($post_id);

        if (!$parent_id || $parent_id < 1) {
            $parent_id = $post_id;
        }

        $args = array(
            'post_parent' => $parent_id,
            'post_type' => 'product',
            'numberposts' => -1,
            'post_status' => 'publish'
        );
        $children = get_children($args);

        foreach ($children as $child) {
            $excluded_ids[] = $child->ID;
        }
    }

    if ($cat_f == "yes") {
        if (isset($_GET['kategorija']) && $_GET['kategorija'] != "") {
            $active_cat = $_GET['kategorija'];

            $cat_parent_obj = get_term_by('slug', $active_cat, $taxonomy);
            $active_cat_id = $cat_parent_obj->term_id;
        }

        if ((isset($active_cat_id) && $active_cat_id === $cat_id) || !isset($active_cat_id)) {
            $all_cats_class = "ow-control-active";
        }
    }

    $slider_class = '';
    if ($style == "slider") {
        $slider_class = "ow-events-slider";
    }

    ob_start();
    ?>
<!--    <div class='ow_event_list <?php echo $slider_class; ?>'> -->
<?php $uid = uniqid(); ?>
<!-- <div class='ow_event_list <?php echo $slider_class; ?>' data-uid='<?php echo $uid; ?>'> --> 
<div class='ow_event_list <?php echo $slider_class; ?>' data-uid='<?php echo $uid; ?>'>
  <div class="ow-category-events-wrap-<?php echo $uid; ?>">

        <?php if ($cat_f == 'yes') { ?>
            <!-- tabs -->
            <div class="ow-events-cats-list">

                <div class="ow-event-cat-controls">
			    <?php $all_cats_class = isset($all_cats_class) ? $all_cats_class : ''; ?>
                    <button type="button" class="ow-cat-button ow-cat-control ow-control-all <?php echo $all_cats_class; ?>"
                            data-filter="all"><?php echo $all_cats_text; ?></button><?php
                    foreach ($terms_ids as $term_id) {
                        $term = get_term_by('term_id', $term_id, $taxonomy);

                        $class = "";

                        if ($active_cat_id === $term_id) {
                            $class = "ow-control-active";
                        }
                        ?>
                        <button type="button" class="ow-cat-button ow-cat-control <?php echo $class; ?>"
                                data-filter="<?php echo $term->slug; ?>"><?php echo $term->name; ?></button><?php
                    }
                    ?>

                </div>
            </div>

            <div class="ow-custom-controls">
                <a class="ow-arhiv" href="#arhiv"><?php echo $open_arhiv_text; ?></a>
            </div>

        <?php } ?>


        <div class="ow-category-events-wrap">
<div class="ow-category-events-wrap ow-category-events-wrap-<?php echo $uid; ?>">


            <div class="ow_event_list_sec ow-cat-grid-wrap">
                <!-- events -->
                <?php
                $now = date('Y-m-d');
                $paged = get_query_var("paged") ? get_query_var("paged") : 1;

                //dogodki, ki se še niso začeli
                //dogodki, ki imajo nastavljen končni datum (akademije) in še niso zaključeni
                $meta_query = array(
                    'relation' => 'OR',
                    array(
                        'key'       => 'ow_event_start_date',
                        'value'     => $now,
                        'compare'   => '>=',
                        'type' => 'DATE',
                    ),
                    array(
                        'relation' => 'AND',
                        array(
                            'key'     => 'ow_event_end_date',
                            'value' => $now,
                            'compare' => '>=',
                            'type' => 'DATE',
                        ),
                        array(
                            'key'     => 'ow_event_akademija',
                            'value' => '1',
                            'compare' => 'LIKE'
                        )
                    )
                );

                if ($active_cat_id || ($terms_ids && $terms_ids !== "") || $cat_f == "yes") {

                    //selected category
                    if ($active_cat_id) {
                        $search_terms = array($active_cat_id);
                    } else if ($terms_ids) {
                        $search_terms = $terms_ids;
                    } else {
                        $search_terms = '';
                    }

                    //related posts
                    if (!$excluded_ids || empty($excluded_ids)) {
                        $excluded_ids = array($post_id);
                    }

                    $args_search_qqq = array(
                        'post_type' => array('product'),
                        'paged' => $paged,
                        'posts_per_page' => $show,
                        'post_status' => 'publish',
                        'order' => 'ASC',
                        'orderby' => 'meta_value',
                        'post__not_in' => $excluded_ids,
                        'meta_query' => $meta_query,
                        'meta_key' => 'ow_event_start_date',
                        'tax_query' => array(
                            array(
                                'taxonomy' => $taxonomy,
                                'field' => 'term_id',
                                'terms' => $search_terms
                            )
                        )
                    );

                } else {

                    $args_search_qqq = array(
                        'post_type' => array('product'),
                        'paged' => $paged,
                        'posts_per_page' => $show,
                        'post_status' => 'publish',
                        'order' => 'ASC',
                        'orderby' => 'meta_value',
                        'meta_key' => 'ow_event_start_date',
                        'meta_query' => $meta_query
                    );
                }

                $the_query = new WP_Query($args_search_qqq);

                if ($the_query->have_posts()):

                    while ($the_query->have_posts()) :
                        $the_query->the_post();

                        $tt = get_the_terms(get_the_id(), $taxonomy);

                        $default_event_type = $event_type;

                        if (!$event_type || $event_type == '') {

                            $terms = get_the_terms(get_the_id(), $taxonomy);
                            $term_parent_id = $terms[0]->parent;

                            $term_parent = get_term_by('term_id', $term_parent_id, $taxonomy);

                            $default_event_type = $term_parent->name;
                        }

                        $id = get_the_id();
                        $title = get_the_title();
                        $location = get_field('ow_venue_city');
                        $date = date('d. m. Y', strtotime(get_post_meta($id, "ow_event_start_date", true)));

                        if(get_post_meta($id, "ow_event_akademija", true) != ''){
                            $end_date = date('d. m. Y', strtotime(get_post_meta($id, "ow_event_end_date", true)));
                            $date = $date . " - " . $end_date;
                        }

                        $show_badge = get_field("prikazi_znacko");
                        $badge_content = "";
                        if (!empty($show_badge) && $show_badge[0] == "DA") {
                            $badge_content = get_field('kratka_vsebina_znacke');
                        }

					// Preveri če je zunanja konferenca it@planetgv.si 08022026
					$is_external = get_field('ow_is_external');
					$external_url = get_field('ow_external_url');

					if ($is_external && $external_url) {
						// Zunanja konferenca
						$special_layout = 1;
						$link = $external_url;
						
					} else {
						// Multisite konferenca ali navadna
						$has_special_layout = get_field('ow_konferenca_site');

						if(!empty($has_special_layout)){
							$special_layout = 1;
							$site_id = $has_special_layout;

							switch_to_blog( $site_id);
							$link = get_home_url();
							restore_current_blog();

						} else {
							$special_layout = 0;
							$link = get_permalink();
							$parent_id = wp_get_post_parent_id($id);

							if ($parent_id) {
								$params = Array('product_id' => $id);
								$link = get_permalink($parent_id) . "?" . http_build_query($params);
								$title = get_the_title($parent_id);
							}
						}
}

                        echo ow_single_event($tt, $badge_content, $default_event_type, $date, $link, $title, $location, $special_layout, false, true, "grid", $is_external)

                        ?>


                    <?php endwhile; ?>

                <?php else: ?>

                    <?php if ($related_mode == "yes"): ?>
                        <script>
                            jQuery(document).ready(function ($) {
                                $("#ow-related").hide();
                            });
                        </script>

                    <?php else: ?>

                        <div><p class="ow-text-white text-center">Trenutno ni izobraževanj iz tega področja.</p></div>

                    <?php endif; ?>

                <?php endif; ?>

                <?php wp_reset_postdata(); ?>

            </div>

            <?php if ($pagination === "yes" && $the_query->max_num_pages > 1): ?>

                <div class="loadmore ow-loadmore ow-loadmore-default"
                     data-max-page="<?php echo $the_query->max_num_pages; ?>"><p><?php echo $load_more_text; ?></p>
                </div>

            <?php endif; ?>

        </div>

    </div>

    <?php if ($cat_f == 'yes' || $pagination == "yes"): ?>

    <?php wp_enqueue_script('ow-url-functions', get_stylesheet_directory_uri() . '/js/ow-url-functions.js', array('jquery'), '1.0.0', true); ?>

    <script>
        jQuery(document).ready(function () {

            var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
            var page_load_more = 2;
            var open_arhiv_text = "<?php echo $open_arhiv_text; ?>";
            var close_arhiv_text = "<?php echo $close_arhiv_text; ?>";

            jQuery(function ($) {

                /* check param arhiv in url on load */
                if (getUrlParameter("arhiv") === "da") {
                    ow_events_archive(true, true, close_arhiv_text);
                    $('.ow-arhiv').addClass("ow-arhiv-active");
                }

                /* load more button */
                $('body').on('click', '.loadmore', function () {
                    if (getUrlParameter("arhiv") === "da") {
                        ow_events_archive(true, false);
                    } else {
                        ow_events_archive(false, false);
                    }
                });

                /* toggle arhiv */
                $('body').on('click', '.ow-arhiv', function (e) {
                    e.preventDefault();

                    var old_param = getUrlParameter("arhiv");
                    var new_param = "da";
                    if (old_param === "da") {
                        new_param = "ne";
                    }

                    changeUrlParameter("arhiv", old_param, new_param);

                    if (getUrlParameter("arhiv") === "da") {
                        /* open */
                        ow_events_archive(true, true, close_arhiv_text);
                        $(this).addClass("ow-arhiv-active");

                    } else {
                        /* close */
                        ow_events_archive(false, true, open_arhiv_text);
                        $(this).removeClass("ow-arhiv-active");
                    }
                });

                /* change categories */
                $('body').on('click', '.ow-cat-control', function (e) {
                    var open_arhiv = false;
                    if ($(".ow-arhiv").hasClass("ow-arhiv-active")) {
                        open_arhiv = true;
                    }

                    var category = $(this).data("filter");

                    var old_category = getUrlParameter('kategorija');
                    changeUrlParameter('kategorija', old_category, category);

                    ow_events_archive(open_arhiv, true, false, category);
                    $(".ow-cat-control").removeClass("ow-control-active");
                    $(this).addClass("ow-control-active");
                });


                /* function for loading results with ajax */
                function ow_events_archive($open_arhiv, $overwrite, $new_arhiv_text, $category) {
                    if ($overwrite) {
                        page_load_more = 1; //reset pagination
                    }

                    if ($new_arhiv_text) {
                        $(".ow-arhiv").text($new_arhiv_text);
                    }

                    if (!$category) {
                        $category = $(".ow-control-active").data("filter");
                    }
                    var sort = 'ASC';
                    if ($open_arhiv) {
                        sort = 'DESC';
                    }

                    var max_page = $(".loadmore").data("max-page");

                    var data = {
                        'action': 'load_posts_by_ajax',
                        'page': page_load_more,
                        'max_page': max_page,
                        'security': '<?php echo wp_create_nonce("load_more_posts"); ?>',
                        'event_type': '<?php echo $event_type; ?>',
                        'show': '<?php echo $show; ?>',
                        'sort': sort,
                        'now': '<?php echo $now; ?>',
                        'child_ids': '<?php echo json_encode($terms_ids); ?>',
                        'arhiv': $open_arhiv,
                        'load_more_text': '<?php echo $load_more_text; ?>',
                        'overwrite': $overwrite,
                        'kategorija': $category
                    };

                    $(".ow-bg-pink").addClass("ow-loader-active"); //show loader

                    var start = Date.now();

                    $.post(ajaxurl, data, function (response) {
                        if (data['overwrite']) {
                            $('.ow-category-events-wrap').html("");
                            $('.ow-category-events-wrap').html(response);

                            if (data['arhiv']) {
                                $('.ow-loadmore-default').show();
                            } else {
                                $('.ow-loadmore-default').hide();
                            }

                        } else {
                            $('.ow-cat-grid-wrap').append(response);

                        }

                        if (page_load_more >= data['max_page']) {
                            $('.loadmore').hide();
                        } else {
                            $('.loadmore').show();
                        }

                        $('.ow-single-event').show(300); //animated effect

                        page_load_more++;
                    })
                        .fail(function () {
                            console.log("ajax error");
                        })
                        .always(function () {
                            var end = Date.now();
                            console.log((end - start)/1000 + "s");
                            $(".ow-bg-pink").removeClass("ow-loader-active");
                        });
                }
            });
        });
    </script>

<?php endif; ?>

    <?php
    $content = ob_get_clean();
    return $content;
}


/* load more event ajax function */
add_action('wp_ajax_load_posts_by_ajax', 'load_posts_by_ajax_callback');
add_action('wp_ajax_nopriv_load_posts_by_ajax', 'load_posts_by_ajax_callback');

function load_posts_by_ajax_callback()
{
    check_ajax_referer('load_more_posts', 'security');

    $taxonomy = 'product_cat';
    $paged = $_POST['page'] ? $_POST['page'] : 1;
    $event_type = $_POST['event_type'];

    $categories_list = array();

    if (isset($_POST['kategorija']) && $_POST['kategorija'] != "") {
        $category = $_POST['kategorija'];
    }

    if (!$category || $category === "all") {
        $all_categories = get_terms(array(
            'taxonomy' => $taxonomy,
            'hide_empty' => false,
        ));

        foreach ($all_categories as $single_category) {
            $categories_list[] = $single_category->slug;
        }
    } else {
        $categories_list[] = $category;
    }

    $now = $_POST['now'];

    $meta_query = array(
        'relation' => 'OR',
        array(
            'key'       => 'ow_event_start_date',
            'value'     => $now,
            'compare'   => '>=',
            'type' => 'DATE',
        ),
        array(
            'relation' => 'AND',
            array(
                'key'     => 'ow_event_end_date',
                'value' => $now,
                'compare' => '>=',
                'type' => 'DATE',
            ),
            array(
                'key'     => 'ow_event_akademija',
                'value' => '1',
                'compare' => 'LIKE'
            )
        )
    );

    if (isset($_POST['arhiv']) && $_POST['arhiv'] == "true") {
        $ow_arhiv = true;

        /* arhiv */
        $args1 = array(
            'post_type' => array('product'),
            'paged' => $paged,
            'posts_per_page' => $_POST['show'],
            'order' => $_POST['sort'],
            'post_status' => 'publish',
            'tax_query' => array(
                array(
                    'taxonomy' => $taxonomy,
                    'field' => 'term_id',
                    'terms' => json_decode($_POST['child_ids'])
                ),
                array(
                    'taxonomy' => $taxonomy,
                    'field' => 'slug',
                    'terms' => $categories_list,
                ),
            )
        );

        $old_posts = new WP_Query($args1);

        $filtered_posts = Array();

        //query all products
        if ($old_posts->have_posts()) :
            while ($old_posts->have_posts()) : $old_posts->the_post();
                $product_id = get_the_id();
                $parent_id = wp_get_post_parent_id($product_id);

                if(get_field("ow_event_akademija", $product_id) != ''){
                    $date = get_field('ow_event_end_date', $product_id);
                } else {
                    $date = get_field('ow_event_start_date', $product_id);
                }
                $now = $_POST['now'];
                $date = str_replace('/', '-', $date);


                if (strtotime($date) != '' && (strtotime($now) > strtotime($date))) {
                    $expired = 1;
                } else {
                    $expired = 0;
                }

                if ($parent_id > 0) {
                    //post has parent
                    if (!isset($filtered_posts[$parent_id]) || (isset($filtered_posts[$parent_id]) && $filtered_posts[$parent_id] != 0)) {
                        $filtered_posts[$parent_id] = $expired;
                    }
                } else {
                    //post doesn't have parent
                    if (!isset($filtered_posts[$product_id]) || (isset($filtered_posts[$product_id]) && $filtered_posts[$product_id] != 0)) {
                        $filtered_posts[$product_id] = $expired;
                    }
                }

            endwhile;
        endif;

        $expired_posts = Array();

        foreach ($filtered_posts as $post_id => $expired) {
            if ($expired == 1) {
                $expired_posts[] = $post_id;
            }
        }

        $args = array(
            'post_type' => array('product'),
            'paged' => $paged,
            'post_status' => 'publish',
            'posts_per_page' => $_POST['show'],
            'order' => $_POST['sort'],
            'post__in' => ((!isset($expired_posts) || empty($expired_posts)) ? array(-1) : $expired_posts),
            'tax_query' => array(
                array(
                    'taxonomy' => $taxonomy,
                    'field' => 'term_id',
                    'terms' => json_decode($_POST['child_ids'])
                ),
                array(
                    'taxonomy' => $taxonomy,
                    'field' => 'slug',
                    'terms' => $categories_list,
                ),
            )
        );


    } else {
        /* pagination, categories */
        $args = array(
            'post_type' => array('product'),
            'paged' => $paged,
            'post_status' => 'publish',
            'posts_per_page' => $_POST['show'],
            'order' => $_POST['sort'],
            'orderby' => 'meta_value',
            'meta_key' => 'ow_event_start_date',
            'suppress_filters' => false,
            'tax_query' => array(
                array(
                    'taxonomy' => $taxonomy,
                    'field' => 'term_id',
                    'terms' => json_decode($_POST['child_ids'])
                ),
                array(
                    'taxonomy' => $taxonomy,
                    'field' => 'slug',
                    'terms' => $categories_list,
                ),
            ),
            'meta_query' => $meta_query
        );

    }

    $new_posts = new WP_Query($args);

    if (isset($_POST['overwrite']) && $_POST['overwrite'] == "true" && (int)$paged == 1) {
        // don't add wrap, when using function for pagination!
        echo '<div class="ow_event_list_sec ow-cat-grid-wrap">';
    }

    if ($new_posts->have_posts()) :

        while ($new_posts->have_posts()) : $new_posts->the_post();

            $tt = get_the_terms(get_the_id(), $taxonomy);
            $id = get_the_id();
            $title = get_the_title();
            $location = get_field('ow_venue_city');
            $date = date('d. m. Y', strtotime(get_post_meta($id, "ow_event_start_date", true)));

            if(get_post_meta($id, "ow_event_akademija", true) != ''){
                $end_date = date('d. m. Y', strtotime(get_post_meta($id, "ow_event_end_date", true)));
                $date = $date . " - " . $end_date;
            }

            $show_badge = get_field("prikazi_znacko");
            $badge_content = "";
            if (!empty($show_badge) && $show_badge[0] == "DA") {
                $badge_content = get_field('kratka_vsebina_znacke');
            }

			// Preveri če je zunanja konferenca it@planetgv 08022026
			$is_external = get_field('ow_is_external');
			$external_url = get_field('ow_external_url');

			if ($is_external && $external_url) {
				// Zunanja konferenca
				$special_layout = 1;
				$link = $external_url;
				
			} else {
				// Multisite konferenca ali navadna
				$has_special_layout = get_field('ow_konferenca_site');

				if(!empty($has_special_layout)){
					$special_layout = 1;
					$site_id = $has_special_layout;

					switch_to_blog( $site_id);
						$link = get_home_url();
					restore_current_blog();

				} else {
					$special_layout = 0;
					$link = get_permalink();
					
					$parent_id = wp_get_post_parent_id($id);

					if ($parent_id) {
						$params = Array('product_id' => $id);
						$link = get_permalink($parent_id) . "?" . http_build_query($params);
						$title = get_the_title($parent_id);
					}
				}
			}

            if (!$ow_arhiv) {
                echo ow_single_event($tt, $badge_content, $event_type, $date, $link, $title, $location, $special_layout, true, true, "grid", $is_external);
            } else {
                echo ow_single_event($tt, '', $event_type, '', $link, $title, '', $special_layout, true, false, "grid", $is_external);
            }

        endwhile;

    else:

        echo '<div><p class="ow-text-white text-center">Trenutno ni izobraževanj iz tega področja.</p></div>';

    endif;

    wp_reset_postdata();

    if (isset($_POST['overwrite']) && $_POST['overwrite'] == "true" && (int)$paged == 1) {
        echo '</div>';
    }

    if (isset($_POST['load_more_text']) && $new_posts->max_num_pages > 1):
        echo '<div class="loadmore ow-loadmore ow-loadmore-created" data-max-page="' . $new_posts->max_num_pages . '"><p>' . $_POST['load_more_text'] . '</p></div>';

    endif;

    wp_die();
}


function ow_check_badge_settings($badge_settings, $available_seat)
{
    $badge_content = '';

    foreach ($badge_settings as $setting) {
        $max = $setting['limit_max'];
        $min = $setting['limit_min'];

        if ((int)$available_seat <= (int)$max && (int)$available_seat >= (int)$min) {
            $badge_content = $setting['text'];
            break;
        }
    }

    return $badge_content;
}


function ow_single_event($tt, $badge_content, $event_type, $date, $link, $title, $location, $special_layout, $hide = false, $registration_link = true, $style = "grid", $is_external = false)


{
    if(!$title) return;

// ===============================
// ✅ OW CUSTOM: Prikaz "HR&M ZAJTRK" namesto "Delavnica"
// ===============================
// Preverimo, ali ima dogodek ACF checkbox 'hrm_zajtrk_check' označen
// Če je označen, zamenjamo $event_type z "HR&M ZAJTRK"
$is_hrm_zajtrk = get_field('hrm_zajtrk_check', get_the_ID());
if ($is_hrm_zajtrk) {
    $event_type = 'HR&M ZAJTRK';
}

    // ===============================
    // ✅ OW CUSTOM: Prikaz "HR&M ZAJTRK" namesto "Delavnica" END
    // ===============================


    $content = "";

    $styles = "";

    if ($hide) {
        $styles .= "style='display:none'";
    }

    $styles .= "class='ow-single-event prevent-def ow_event_" . $style . "_item mix ";
    if ($tt) {
        $styles .= $tt[0]->slug;
    }


    $styles .= "'";

    $content .= "<a " . $styles . " href='" . $link . "'>";

    $content .= '<div class="ow_list_event_details">';

    $content .= '<div class="ow-list-header">';
    /* badge */
    $content .= '<div class="first-row">';
    if (trim($badge_content) !== ''):
        $content .= '<div class="ow-badge">' . $badge_content . '</div>';
    endif;
    $content .= '</div>';

    
    /* event location & time */
    $content .= '<div class="third-row">';
    // $content .= '<span class="ow-location">' . $location . '</span>';

    // if ($location):
    //     $content .= '<span class="ow-divide">|</span>';
    // endif;

    /* event type */
    $content .= '<div class="second-row ow-event-type"><p>' . $event_type . '</p></div>';

    $content .= '<span class="ow-day">' . $date . '</span>';
    $content .= '</div>';
    $content .= '</div>';


    $content .= '<div class="ow-list-content">';
    $content .= "<h2 class='ow_list_title'>" . $title . "</h2>";
    $content .= '</div>';


    $content .= '<div class="ow-footer-bottom">';

    if ($registration_link):
        //$content .= '<span class="ow-divide">|</span>';
        if($special_layout == 1)
           $link1 = trailingslashit($link).'prijava';
        else
            $link1 = $link;

        $content .= '<span class="ow-button button--1 ow-prijava ow-sublink" data-anchor="termini" data-site="'.$special_layout.'" data-link="' . $link1 . '" data-external="' . ($is_external ? '1' : '0') . '">' . __("Prijava", "ow_tran") . '</span>';
    endif;
    if($is_external)
        $link2 = $link;
    elseif($special_layout == 1)
           $link2 = trailingslashit($link).'program';
    else
        $link2 = $link;
    $content .= '<span class="ow-purple-link ow-sublink" data-anchor="vec" data-site="'.$special_layout.'" data-link="' . $link2 . '" data-external="' . ($is_external ? '1' : '0') . '">' . __("Več o dogodku", "ow_tran") . '</span>';
    $content .= '</div>';

    $content .= '</div>';

    $content .= '</a>';

    return $content;
}

add_action('init', 'my_rem_editor_from_post_type');
function my_rem_editor_from_post_type()
{
    remove_post_type_support('product', 'editor');
}




if (!class_exists('avia_social_share_links')) {
    class avia_social_share_links
    {
        var $args;
        var $options;
        var $links = array();
        var $html = "";
        var $title = "";
        var $counter = 0;

        /*
         * constructor
         * initialize the variables necessary for all social media links
         */

        function __construct($args = array(), $options = false, $title = false)
        {
            $default_arguments = array
            (
                'facebook' => array("encode" => true, "encode_urls" => false, "pattern" => "https://www.facebook.com/sharer.php?u=[permalink]&amp;t=[title]"),
                'twitter' => array("encode" => true, "encode_urls" => false, "pattern" => "https://twitter.com/share?text=[title]&url=[shortlink]"),
                'gplus' => array("encode" => true, "encode_urls" => false, "pattern" => "https://plus.google.com/share?url=[permalink]", 'label' => __("Share on Google+", 'avia_framework')),
                'pinterest' => array("encode" => true, "encode_urls" => true, "pattern" => "https://pinterest.com/pin/create/button/?url=[permalink]&amp;description=[title]&amp;media=[thumbnail]"),
                'linkedin' => array("encode" => true, "encode_urls" => false, "pattern" => "https://linkedin.com/shareArticle?mini=true&amp;title=[title]&amp;url=[permalink]"),
                'tumblr' => array("encode" => true, "encode_urls" => true, "pattern" => "https://www.tumblr.com/share/link?url=[permalink]&amp;name=[title]&amp;description=[excerpt]"),
                'vk' => array("encode" => true, "encode_urls" => false, "pattern" => "https://vk.com/share.php?url=[permalink]"),
                'reddit' => array("encode" => true, "encode_urls" => false, "pattern" => "https://reddit.com/submit?url=[permalink]&amp;title=[title]"),
                'mail' => array("encode" => true, "encode_urls" => false, "pattern" => "mailto:?subject=[title]&amp;body=[permalink]", 'label' => __("Share by Mail", 'avia_framework')),
            );

            $this->args = apply_filters('avia_social_share_link_arguments', array_merge($default_arguments, $args));

            if (empty($options)) $options = avia_get_option();
            $this->options = $options;
            $this->build_share_links();
        }

        /*
         * filter social icons that are disabled in the backend. everything that is left will be displayed.
         * that way the user can hook into the "avia_social_share_link_arguments" filter above and add new social icons without the need to add a new backend option
         */
        function build_share_links()
        {
            $thumb = wp_get_attachment_image_src(get_post_thumbnail_id(), 'masonry');
            $replace['permalink'] = !isset($this->post_data['permalink']) ? get_permalink() : $this->post_data['permalink'];
            $replace['title'] = !isset($this->post_data['title']) ? get_the_title() : $this->post_data['title'];
            $replace['excerpt'] = !isset($this->post_data['excerpt']) ? get_the_excerpt() : $this->post_data['excerpt'];
            $replace['shortlink'] = !isset($this->post_data['shortlink']) ? wp_get_shortlink() : $this->post_data['shortlink'];
            $replace['thumbnail'] = is_array($thumb) && isset($thumb[0]) ? $thumb[0] : "";
            $replace['thumbnail'] = !isset($this->post_data['thumbnail']) ? $replace['thumbnail'] : $this->post_data['thumbnail'];

            $replace = apply_filters('avia_social_share_link_replace_values', $replace);
            $charset = get_bloginfo('charset');

            foreach ($this->args as $key => $share) {
                $share_key = 'share_' . $key;
                $url = $share['pattern'];

                //if the backend option is disabled skip to the next link. in any other case generate the share link
                if (isset($this->options[$share_key]) && $this->options[$share_key] == 'disabled') continue;

                foreach ($replace as $replace_key => $replace_value) {
                    if (!empty($share['encode']) && $replace_key != 'shortlink' && $replace_key != 'permalink') $replace_value = rawurlencode(html_entity_decode($replace_value, ENT_QUOTES, $charset));
                    if (!empty($share['encode_urls']) && ($replace_key == 'shortlink' || $replace_key == 'permalink')) $replace_value = rawurlencode(html_entity_decode($replace_value, ENT_QUOTES, $charset));

                    $url = str_replace("[{$replace_key}]", $replace_value, $url);
                }

                $this->args[$key]['url'] = $url;
                $this->counter++;
            }
        }


        /*
         * function html
         * builds the html, based on the available urls
         */

        function html()
        {
            global $avia_config;

            if ($this->counter == 0) return;

            $this->html .= "<div class='av-share-box'>";
            if ($this->title) {
                $this->html .= "<h5 class='av-share-link-description av-no-toc'>";
                $this->html .= apply_filters('avia_social_share_title', $this->title, $this->args);
                $this->html .= "</h5>";
            }
            $this->html .= "<ul class='av-share-box-list noLightbox'>";

            foreach ($this->args as $key => $share) {
                if (empty($share['url'])) continue;

                $icon = isset($share['icon']) ? $share['icon'] : $key;

                $blank = strpos($share['url'], 'mailto') !== false ? "" : "target='_blank'";

                $icon_path = get_theme_root() . "/enfold-child/images/" . $key . '.svg';
                $add_class = $icon_non_svg = $icon_svg = '';
                if ($key) {
                    $icon_svg = file_get_contents($icon_path);
                } else {
                    $icon_non_svg = av_icon_string($icon);
                    $add_class = 'av-social-link-' . $key;
                }

                $this->html .= "<li class='av-share-link " . $add_class . "' >";
                $this->html .= "<a {$blank} class='prevent-def' href='" . $share['url'] . "' " . $icon_non_svg . ">" . $icon_svg . "</a>";
                $this->html .= "</li>";
            }

            $this->html .= "</ul>";
            $this->html .= "</div>";

            return $this->html;
        }
    }
}


/* custom posty type PRIROČNIK */
if (!function_exists('custom_post_type_prirocnik')) {
    function custom_post_type_prirocnik()
    {

        $labels = array(
            'name' => _x('Priročniki', 'Post Type General Name', 'text_domain'),
            'singular_name' => _x('Priročnik', 'Post Type Singular Name', 'text_domain'),
            'menu_name' => __('Priročniki', 'ow-translations'),
            'name_admin_bar' => __('Priročniki', 'text_domain'),
            'archives' => __('Arhiv priročnikov', 'text_domain'),
            'all_items' => __('Vsi priročniki', 'text_domain'),
            'add_new_item' => __('Dodaj nov priročnik', 'text_domain'),
            'add_new' => __('Dodaj nov', 'text_domain'),
            'new_item' => __('Nov priročnik', 'text_domain'),

        );
        $args = array(
            'label' => __('Priročniki', 'text_domain'),
            'labels' => $labels,
            'supports' => array('title', 'thumbnail', 'excerpt'),
            'hierarchical' => false,
            'public' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'menu_position' => 5,
            'show_in_admin_bar' => true,
            'show_in_nav_menus' => true,
            'can_export' => true,
            'has_archive' => true,
            'exclude_from_search' => false,
            'publicly_queryable' => true,
            'capability_type' => 'page',
            'menu_icon' => 'dashicons-book-alt',
        );
        register_post_type('prirocniki', $args);

    }

    add_action('init', 'custom_post_type_prirocnik', 0);
}


/* send email with attached pdf when user send form for free manual */
add_action('gform_after_submission_7', 'send_email_with_file', 10, 2);
function send_email_with_file()
{

    if (isset($_POST) && isset($_POST['input_1']) && isset($_POST['input_2']) && isset($_POST['input_3'])) {
        $input_email = $_POST['input_1'];
        $file_id = $_POST['input_3'];
        $post_name = $_POST['input_5'];

        $option_subject = get_field("prirocniki_subject", "option");
        $option_content = get_field("prirocniki_body", "option");

        if (isset($post_name)) {
            $option_subject = str_replace("{{ime_prirocnika}}", $post_name, $option_subject);
            $option_content = str_replace("{{ime_prirocnika}}", $post_name, $option_content);
        }

        $to = $input_email;
        $subject = $option_subject;
        $body = $option_content;
        $headers = array();
        $headers[] = 'Content-Type: text/html';
        $headers[] = 'charset=UTF-8';
        $attachments = get_attached_file($file_id);

        $sender = get_field("e-naslov_posiljatelja", "option");

        if (isset($sender['user_email'])) {
            $sender_email = $sender['user_email'];
            $headers[] = 'From: Planet GV <' . $sender_email . '>';
        }

        wp_mail($to, $subject, $body, $headers, $attachments);
    }

}




add_filter('register_post_type_args', 'add_hierarchy_support', 10, 2);
function add_hierarchy_support($args, $post_type)
{

    if ($post_type === 'product') { // <-- enter desired post type here

        $args['hierarchical'] = true;
        $args['supports'] = array_merge($args['supports'], array('page-attributes'));
    }

    return $args;
}


// Add the custom columns to the product post type:
add_filter('manage_product_posts_columns', 'set_custom_edit_product_columns');
function set_custom_edit_product_columns($columns)
{
    $columns['ow_trajanje'] = __('Trajanje dogodka', 'your_text_domain');
    $columns['ow_tip'] = __('Tip dogodka', 'your_text_domain');

    return $columns;
}

// Add the data to the custom columns for the product post type:
add_action('manage_product_posts_custom_column', 'custom_product_column', 10, 2);
function custom_product_column($column, $post_id)
{
    switch ($column) {

        case 'ow_trajanje' :
            $start_date = get_post_meta($post_id, 'ow_event_start_date', true);
            $end_date = get_post_meta($post_id, 'ow_event_end_date', true);

            $current_date = date("Ymd");

            if (trim($start_date) != '') {
                if ($current_date >= $start_date) {
                    if ($current_date >= $end_date || trim($end_date) == '') {
                        $status = 'expired';
                        $css = 'background:red;color:white;';
                    } else {
                        $status = 'in_progress';
                        $css = 'background:yellow;color:black;';
                    }
                } else {
                    $status = 'incoming';
                    $css = 'background:green;color:white;';
                }

                $start_date = date("d. m. Y", strtotime($start_date));
            }

            if (trim($end_date) != '') {
                $end_date = date("d. m. Y", strtotime($end_date));
            }

            if ($start_date != $end_date && $end_date != '') {
                $date = $start_date . ' - ' . $end_date;
            } else {
                $date = $start_date;
            }

            echo '<div class="' . $status . '" style="text-align:center;margin:auto;padding:10px;width:80px;' . $css . '">' . $date . '</div>';

            break;

        case 'name' :
            $parent_id = wp_get_post_parent_id($post_id);

            if ($parent_id > 0) {
                echo "<span class='ow-child-post'> ---- </span>";
            }

            break;

        case 'ow_tip':
            $akademija = get_post_meta($post_id, 'ow_event_akademija', true);
            $konferenca = get_post_meta($post_id, 'ow_konferenca_site', true);

            $parent_id = wp_get_post_parent_id($post_id);
            $args = array(
                'post_parent' => $post_id,
                'post_type' => 'product',
                'numberposts' => -1,
                'order' => 'ASC',
                'orderby' => 'meta_value',
                'meta_key' => 'ow_event_start_date'
            );
            $children = get_children($args);

            echo "<div style='padding:0 5px;'>";

            if ($akademija) {
                echo "Akademija";

            } else if ($konferenca && !empty($konferenca)){
                switch_to_blog($konferenca);
                $link = get_home_url();
                restore_current_blog();

                echo "Konferenca (<a href='".$link."'>". $link . "</a>)";

            } else if($parent_id){
                $parent_title = get_the_title($parent_id);
                $parent_link = get_edit_post_link($parent_id);
                echo "Nov datum osnovnega dogodka (<a href='".$parent_link."'>" . $parent_title . "</a>)";

            } else if($children){
                echo "Osnovni dogodek z več datumi:";
                echo "<ul>";

                    foreach ($children as $child) {
                        $child_title = get_the_title($child->ID);
                        $child_date = get_post_meta($child->ID, 'ow_event_start_date', true);
                        $child_date = date("d. m. Y", strtotime($child_date));
                        $child_link = get_edit_post_link($child->ID);
                        echo "<li><a href='".$child_link."' title='".$child_title."'>" . $child_date . "</a></li>";
                    }

                echo "</ul>";
            }
            echo "</div>";

            break;
    }
}


function acf_load_field_choices($field)
{

    $field['choices'] = array();

    $product_id = get_the_id();
    $product = wc_get_product($product_id);

    if (isset($product) && $product != '') {
        if ($product->is_type('variable')) {
            $available_variations = $product->get_available_variations();

            if (isset($available_variations) && !empty($available_variations)) {

                foreach ($available_variations as $available_variation) {
                    $variation_id = $available_variation['variation_id'];
                    $variation = wc_get_product($variation_id);
                    $variation_name = $variation->get_name();

                    $field['choices'][$variation_id] = $variation_name;
                }
            }
        }
    }
    return $field;
}

add_filter('acf/load_field/name=ow_event_srecanje_modul', 'acf_load_field_choices');


function ow_product_type_options($options)
{
    $options['virtual']     ['default'] = "yes";

    if (isset($options['downloadable'])) {
        unset($options['downloadable']);
    }
    return $options;
}

add_filter('product_type_options', 'ow_product_type_options', 100, 1);


function remove_linked_products($tabs)
{
    unset($tabs['shipping']);
    unset($tabs['linked_product']);
    unset($tabs['advanced']);

    return ($tabs);
}

add_filter('woocommerce_product_data_tabs', 'remove_linked_products', 10, 1);


add_action('admin_head', 'my_custom_admin_styles');
function my_custom_admin_styles()
{
// just add the css selectors below to hide each field as required
    echo '<style>
        body.wp-admin ._tax_status_field, 
        body.wp-admin ._tax_class_field,
        body.wp-admin ._stock_status,
        body.wp-admin .stock_status_field,
        body.wp-admin ._sold_individually_field, 
        body.wp-admin #menu_order,
        body.wp-admin .post-attributes-label[for="menu_order"],
        .woocommerce_variable_attributes .options label.tips:nth-of-type(2),
        .woocommerce_variable_attributes .variable_is_downloadable,
        .woocommerce_variable_attributes .upload_image,
        .woocommerce_variable_attributes .form-field[class*="variable_description"],
        .woocommerce_variable_attributes .form-field[class*="variable_tax_class"],
        .woocommerce_variable_attributes .form-field[class*="variable_stock_status"],
        #woocommerce-pricing-rules-wrap .pricing_rule_apply_to,
        #woocommerce-pricing-rules-wrap label[for*="pricing_rule_apply_to"],
        #woocommerce-pricing-rules-wrap div[id*="woocommerce-pricing-collector-set"],
        #woocommerce-pricing-rules-wrap div[id*="woocommerce-pricing-mode-set"],
        #woocommerce-pricing-rules-wrap .pricing-rule-date-fields/*,
        #woocommerce-pricing-rules-wrap select[id*="pricing_rule_type_value_set"] option[value="price_discount"],
        #woocommerce-pricing-rules-wrap select[id*="pricing_rule_type_value_set"] option[value="percentage_discount"]*/{
            display: none !important; 
        }
    </style>';


}


//editor settings
function add_theme_caps()
{
    $role = get_role('editor');
    $role->add_cap("manage_options");
    $role->add_cap("manage_woocommerce");
    $role->add_cap("view_woocommerce_reports");
    $role->add_cap("edit_product");
    $role->add_cap("read_product");
    $role->add_cap("delete_product");
    $role->add_cap("edit_products");
    $role->add_cap("edit_others_products");
    $role->add_cap("publish_products");
    $role->add_cap("read_private_products");
    $role->add_cap("delete_products");
    $role->add_cap("delete_private_products");
    $role->add_cap("delete_published_products");
    $role->add_cap("delete_others_products");
    $role->add_cap("edit_private_products");
    $role->add_cap("edit_published_products");
    $role->add_cap("manage_product_terms");
    $role->add_cap("edit_product_terms");
    $role->add_cap("delete_product_terms");
    $role->add_cap("assign_product_terms");
    $role->add_cap("edit_shop_order");
    $role->add_cap("read_shop_order");
    $role->add_cap("delete_shop_order");
    $role->add_cap("edit_shop_orders");
    $role->add_cap("edit_others_shop_orders");
    $role->add_cap("publish_shop_orders");
    $role->add_cap("read_private_shop_orders");
    $role->add_cap("delete_shop_orders");
    $role->add_cap("delete_private_shop_orders");
    $role->add_cap("delete_published_shop_orders");
    $role->add_cap("delete_others_shop_orders");
    $role->add_cap("edit_private_shop_orders");
    $role->add_cap("edit_published_shop_orders");
    $role->add_cap("manage_shop_order_terms");
    $role->add_cap("edit_shop_order_terms");
    $role->add_cap("delete_shop_order_terms");
    $role->add_cap("assign_shop_order_terms");
    $role->add_cap("edit_shop_coupon");
    $role->add_cap("read_shop_coupon");
    $role->add_cap("delete_shop_coupon");
    $role->add_cap("edit_shop_coupons");
    $role->add_cap("edit_others_shop_coupons");
    $role->add_cap("publish_shop_coupons");
    $role->add_cap("read_private_shop_coupons");
    $role->add_cap("delete_shop_coupons");
    $role->add_cap("delete_private_shop_coupons");
    $role->add_cap("delete_published_shop_coupons");
    $role->add_cap("delete_others_shop_coupons");
    $role->add_cap("edit_private_shop_coupons");
    $role->add_cap("edit_published_shop_coupons");
    $role->add_cap("manage_shop_coupon_terms");
    $role->add_cap("edit_shop_coupon_terms");
    $role->add_cap("delete_shop_coupon_terms");
    $role->add_cap("assign_shop_coupon_terms");
    $role->add_cap("edit_shop_webhook");
    $role->add_cap("read_shop_webhook");
    $role->add_cap("delete_shop_webhook");
    $role->add_cap("edit_shop_webhooks");
    $role->add_cap("edit_others_shop_webhooks");
    $role->add_cap("publish_shop_webhooks");
    $role->add_cap("read_private_shop_webhooks");
    $role->add_cap("delete_shop_webhooks");
    $role->add_cap("delete_private_shop_webhooks");
    $role->add_cap("delete_published_shop_webhooks");
    $role->add_cap("delete_others_shop_webhooks");
    $role->add_cap("edit_private_shop_webhooks");
    $role->add_cap("edit_published_shop_webhooks");
    $role->add_cap("manage_shop_webhook_terms");
    $role->add_cap("edit_shop_webhook_terms");
    $role->add_cap("delete_shop_webhook_terms");
    $role->add_cap("assign_shop_webhook_terms");
}

add_action('admin_init', 'add_theme_caps');


/* remove product meta boxes */
function remove_meta_boxes()
{
    remove_meta_box('postexcerpt', 'product', 'side');
    remove_meta_box('layout', 'product', 'side');
    remove_meta_box('avia_product_hover', 'product', 'side');
    remove_meta_box('woocommerce-product-images', 'product', 'side');
    remove_meta_box('tagsdiv-product_tag', 'product', 'side');
}

add_action('admin_head', 'remove_meta_boxes'); //admin_init & admin_menu don't work for certain meta boxes


/*remove tags for products */
add_action('init', function () {
    unregister_taxonomy('product_tag');
}, 100);


function get_event_address($product_id){
    $event_location_name = get_post_meta($product_id, "ow_venue_name", true);
    $event_location_street = get_post_meta($product_id, "ow_venue_street", true);
    $event_location_postcode = get_post_meta($product_id, "ow_venue_postcode", true);
    $event_location_city = get_post_meta($product_id, "ow_venue_city", true);
    $event_location_country = get_post_meta($product_id, "ow_venue_country", true);

    if ($event_location_city) {
        $address_short = $event_location_city;
    }

    if ($event_location_country) {
        $address_country = $event_location_country;
        if ($address_short) {
            $address_short .= ", " . $address_country;
        } else {
            $address_short = $address_country;
        }
    }

    if ($event_location_name) {
        $event_location = $event_location_name;
    }

    if ($event_location_street) {
        $event_street = $event_location_street;
        if($event_location){
            $event_location .= ", " . $event_street;
        } else {
            $event_location = $event_street;
        }
    }

    if ($event_location_postcode) {
        $event_postcode = $event_location_postcode;
        if ($event_location) {
            $event_location .= ", " . $event_postcode;
        } else {
            $event_location = $event_postcode;
        }
    }

    if ($event_location_city) {
        $event_city = $event_location_city;
        if ($event_postcode ) {
            $event_location .= " " . $event_city;
        } elseif ($event_location) {
            $event_location .= ", " . $event_city;
        } else {
            $event_location = $event_city;
        }
    }

    $return = array(
        'location_short' => $address_short,
        'location_full' => $event_location
    );

    return $return;
}




/* prevent selection of parent category on product page*/
add_action( 'admin_footer-post.php', 'ow_remove_top_categories_checkbox' );
add_action( 'admin_footer-post-new.php', 'ow_remove_top_categories_checkbox' );

function ow_remove_top_categories_checkbox()
{
    global $post_type;

    if ( 'product' != $post_type )
        return;
    ?>
    <script type="text/javascript">
        jQuery("#product_catchecklist>li>label input").each(function(){
            jQuery(this).remove();
        });
    </script>
    <?php
}



/* redirects from default category pages */
add_action('template_redirect', 'template_redirect_filter'); // Spremeni add_filter v add_action
function template_redirect_filter() {
    if ( is_tax( 'product_cat' ) || is_category() || is_post_type_archive( 'prirocniki' ) ) {
        $url = get_home_url();
        wp_safe_redirect($url, 301);
        exit;
    }
}


/* Popravki 08042025
add_filter('template_redirect', 'template_redirect_filter', 10, 3);
function template_redirect_filter() {
    if ( is_tax('product_cat') || is_category() ) {
        $url = get_home_url();
        wp_safe_redirect($url, 301);
        exit;
    }
}


add_filter('template_redirect', 'template_redirect_filter', 10, 3);
function template_redirect_filter() {
    if ( is_main_site() && ( is_tax('product_cat') || is_category() ) ) {
        $url = get_home_url();
        wp_safe_redirect($url, 301);
        exit;
    }
}
*/


function acf_load_site_choices($field)
{

    $field['choices'] = array();
    $sites = wp_get_sites();

    if (isset($sites) && $sites != '') {

        foreach ( $sites as $i => $site ) {
            $site_id = $site[ 'blog_id' ];
            if($site_id == 1){
                continue;
            }

            switch_to_blog( $site_id);
            $field['choices'][$site_id] = get_bloginfo();
            restore_current_blog();
        }
    }
    return $field;
}

add_filter('acf/load_field/name=ow_konferenca_site', 'acf_load_site_choices');



add_action('init', 'wpse_74054_add_author_woocommerce', 999 );

function wpse_74054_add_author_woocommerce() {
    add_post_type_support( 'product', 'author' );
}


add_action('admin_head', 'my_admin_column_width');
function my_admin_column_width() {
    echo '<style type="text/css">
        .column-name { text-align: left; width:150px !important; overflow:hidden }
        .column-sku { text-align: left; width:50px !important; overflow:hidden }
        .column-date, .column-ow_trajanje { text-align: left; width:90px !important; overflow:hidden }
        table.wp-list-table .column-product_cat, .column-author { text-align: left; width:80px !important; overflow:hidden }
        table.wp-list-table .column-product_tag {display:none;}
        .column-featured { text-align: left; width:15px !important; overflow:hidden }
        .column-ow_tip { text-align: left; width:100px !important; overflow:hidden }
    </style>';
}

/* === ZUNANJA KONFERENCA - HELPER FUNKCIJA === it@planetgv 08022025 */
function ow_get_conference_data($product_id) {
    // Preveri, ali je to zunanja konferenca
    $is_external = get_post_meta($product_id, 'ow_is_external', true);
    
    if ($is_external) {
        // Zunanja konferenca
        return array(
            'is_external' => true,
            'title' => get_the_title($product_id),
            'url' => get_post_meta($product_id, 'ow_external_url', true),
            'date_start' => get_post_meta($product_id, 'ow_datum_zacetek', true),
            'date_end' => get_post_meta($product_id, 'ow_datum_konec', true),
            'location' => get_event_address($product_id),
            'image' => get_the_post_thumbnail_url($product_id, 'full'),
            'excerpt' => get_the_excerpt($product_id),
        );
    } else {
        // Navadna konferenca (obstoječa logika)
        $site_id = get_post_meta($product_id, 'ow_konferenca_site', true);
        
        if ($site_id && $site_id != get_current_blog_id()) {
            switch_to_blog($site_id);
            
            $conference_data = array(
                'is_external' => false,
                'title' => get_the_title($product_id),
                'url' => get_permalink($product_id),
                'date_start' => get_post_meta($product_id, 'ow_datum_zacetek', true),
                'date_end' => get_post_meta($product_id, 'ow_datum_konec', true),
                'location' => get_event_address($product_id),
                'image' => get_the_post_thumbnail_url($product_id, 'full'),
                'excerpt' => get_the_excerpt($product_id),
            );
            
            restore_current_blog();
            return $conference_data;
        }
    }
    
    return null;
}