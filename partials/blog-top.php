<?php
/**
 * Created by PhpStorm.
 * User: Nina Camernik
 * Date: 15.5.2019
 * Time: 12:09
 */

?>
<?php
$the_id = get_the_id();

$post_type = get_post_type($the_id);

$thumbnail_img = get_the_post_thumbnail_url();

if ($thumbnail_img) {
    $show_image_class = "ow-show-img";
}

$short_description = get_post_field('post_excerpt', $the_id);

$author = get_field('ow-avtor');
$author_img = get_field('ow-slika-avtorja');

$page = get_page_by_title("Članki");
$parent_link = get_permalink($page->ID);

$taxonomies = get_object_taxonomies($post_type);
$cats = '';
$excluded_taxonomies = array_merge(get_taxonomies(array('public' => false)), array('post_tag', 'post_format'));
$excluded_taxonomies = apply_filters('avf_exclude_taxonomies', $excluded_taxonomies, get_post_type($the_id), $the_id);

if (!empty($taxonomies)) {
    foreach ($taxonomies as $taxonomy) {
        if (!in_array($taxonomy, $excluded_taxonomies)) {
            $post_terms = wp_get_post_terms($the_id, $taxonomy);

            if ($post_terms && !empty($post_terms)) {

                $len = count($post_terms);
                $i = 0;

                foreach ($post_terms as $term) {
                    $i++;
                    if ($term->name == "Nekategorizirano") {
                        continue;
                    }
                    $name = $term->name;
                    $slug = $term->slug;
                    $term_url = $parent_link . "?kategorija=" . $slug;
                    if ($term_url) {
                        $cats .= '<a class="cat-link" href="' . $term_url . '">' . $name;
                        if ($i < $len) {
                            $cats .= "<span>,</span>";
                        }
                        $cats .= '</a>';
                    }
                }
            }

        }
    }
}

?>

<div class="ow-events-full-width" id="homepage-banner">

    <div class="homepage-banner-block-1 ow-event-banner <?php if ($show_image_class) {
        echo $show_image_class;
    } ?>">

        <div class="ow-text ow-left">
            <div class="breadcrumbs">

                <a href="<?php echo $parent_link; ?>">Članki</a>

                <?php if (!empty($cats)): ?>

                    <span class="divider"> | </span>
                    <?php echo $cats; ?>

                <?php endif; ?>

            </div>

            <div class="ow-event-title">
                <h1><?php echo get_the_title(); ?></h1>
            </div>

            <?php if (isset($short_description) and !empty($short_description)): ?>
                <div class="ow-short-description">
                    <p>
                        <?php echo $short_description; ?>
                    </p>
                </div>
            <?php endif; ?>

            <?php if (isset($author) and !empty($author)): ?>
                <div class="ow-author">

                    <?php if (isset($author_img) and !empty($author_img)): ?>
                        <img src="<?php echo $author_img['sizes']['thumbnail']; ?>">
                    <?php endif; ?>

                    <h5><?php echo $author; ?></h5>
                </div>

            <?php endif; ?>

        </div>

        <div class="ow-homepage-img-1 ow-event-img ow-right">

            <?php if ($thumbnail_img): ?>

                <div class="ow-decoration-img">
                    <img src="<?php echo $thumbnail_img; ?>"/>
                </div>

            <?php endif; ?>


        </div>
    </div>

</div>


