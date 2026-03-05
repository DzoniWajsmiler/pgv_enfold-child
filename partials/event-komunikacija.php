<?php
/**
 * Created by PhpStorm.
 * User: Nina Camernik
 * Date: 15.5.2019
 * Time: 14:15
 */

$custom = get_post_custom();

$post_terms = get_the_terms(get_the_id(), 'product_cat');

$categories = "";

foreach($post_terms as $term){
    if($term->name != "Nekategorizirano") {
        $categories .= $term->term_id;
        $categories .= "+";
    }
}
?>

<div id="ow-related" class="avia-section main_color avia-section-default avia-no-border-styling avia-bg-style-scroll  el_after_av_textblock  avia-builder-el-last  ow-bg-pink  container_wrap fullsize">
    <div class="container">
        <div class="template-page content  av-content-full alpha units">
            <div class="post-entry post-entry-type-page">
                <div class="entry-content-wrapper clearfix">
                    <section class="av_textblock_section " itemscope="itemscope" itemtype="https://schema.org/CreativeWork">
                        <div class="avia_textblock m-100" itemprop="text">

                            <h2 class="ow-text-white ow-center mb-40"><?php echo "Ostala izobraževanja iz tega področja" ?></h2>

                            <?php echo do_shortcode("[ow-event-list cat_ids='".$categories."' show='3' pagination='no' cat-filter='no' related-mode='yes']") ?>

                        </div>
                    </section>

                </div>
            </div>
            </div>
        </div>
</div>