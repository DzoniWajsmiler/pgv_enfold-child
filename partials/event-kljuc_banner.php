<?php
$terms = get_the_terms(get_the_ID(), 'product_cat');
$parent_cat_slug = "";

foreach($terms as $term){
    $parent_cat_id = $term->parent;

    if(!empty($parent_cat_id)){
        $parent_cat_slug =  get_term_by( 'term_id', $parent_cat_id, 'product_cat')->slug;
        break;
    }
}
?>

<div class="avia-section main_color avia-section-default avia-no-border-styling avia-bg-style-scroll m-100 container_wrap fullsize">
    <div class="container">
        <div class="template-page content  av-content-full alpha units">
            <div class="post-entry post-entry-type-page">
                <div class="entry-content-wrapper clearfix">
                    <section class="av_textblock_section ">
                        <div class="avia_textblock" itemprop="text">
                            <?php echo do_shortcode("[delavnice_na_kljuc podrocje='".$parent_cat_slug."']") ?>
                        </div>
                    </section>

                </div>
            </div>
        </div>
    </div>
</div>