<?php
if ( !defined('ABSPATH') ){ die(); }

global $avia_config;

/*
 * get_header is a basic wordpress function, used to retrieve the header.php file in your theme directory.
 */
get_header();

$title  = __('Blog - Latest News', 'avia_framework'); //default blog title
$t_link = home_url('/');
$t_sub = "";

if(avia_get_option('frontpage') && $new = avia_get_option('blogpage'))
{
    $title 	= get_the_title($new); //if the blog is attached to a page use this title
    $t_link = get_permalink($new);
    $t_sub =  avia_post_meta($new, 'subtitle');
}

if( get_post_meta(get_the_ID(), 'header', true) != 'no') echo avia_title(array('heading'=>'strong', 'title' => $title, 'link' => $t_link, 'subtitle' => $t_sub));

do_action( 'ava_after_main_title' );

$title = get_the_title();
$description = get_field("prirocniki_opis");
$subtitle = get_field("prirocniki_podnaslov");
$list = get_field("prirocniki_seznam_vsebine");
$file = get_field("prenos_prirocnika");
$image = get_the_post_thumbnail();


?>

    <div class='ow-prirocniki container_wrap container_wrap_first main_color <?php avia_layout_class( 'main' ); ?>'>


       <!-- <div class='container'>-->




                <div class="avia-section main_color avia-section-default avia-no-border-styling avia-bg-style-scroll  avia-builder-el-0  el_before_av_textblock  avia-builder-el-first  ow-title-section  container_wrap fullsize">
                    <div class="container">
                        <main role="main" itemprop="mainContentOfPage" class="template-page content  av-content-full alpha units">
                            <div class="post-entry post-entry-type-page post-entry-890">
                                <div class="entry-content-wrapper clearfix">
                                    <section class="av_textblock_section" itemscope="itemscope" itemtype="https://schema.org/CreativeWork">
                                        <div class="avia_textblock" itemprop="text">
                                            <h1><?php echo $title; ?></h1>
                                            <p><?php echo $description; ?></p>
                                        </div>
                                    </section>
                                </div>
                            </div>
                        </main>
                    </div>
                </div>

                <div class="avia-section main_color avia-section-default avia-no-border-styling avia-bg-style-scroll  el_before_av_textblock   container_wrap fullsize">
                    <div class="container">
                        <main role="main" itemprop="mainContentOfPage" class="template-page  av-content-full alpha units">
                            <div class="ow-row">

                                <!-- left section -->
                                <?php if($image): ?>
                                <div class="ow-left">
                                    <div class="ow-img-wrap">
                                        <?php echo $image; ?>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <!-- right section -->
                                <div class="ow-right">

                                    <!-- text -->
                                    <div class="ow-text no-p">
                                        <?php if(isset($subtitle) && !empty($subtitle)): ?>
                                            <h2 class="ow-subtitle"><?php echo $subtitle; ?></h2>
                                        <?php endif; ?>
                                        <?php if($list): ?>
                                            <ul class="ow-kljukice">
                                                <?php foreach($list as $list_item): ?>
                                                    <?php if(isset($list_item['prirocniki_element_seznama'])): ?>
                                                        <li><?php echo $list_item['prirocniki_element_seznama']; ?></li>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php endif; ?>
                                    </div>

                                    <!-- form -->
                                    <?php if(isset($file['prirocnik']['ID'])): ?>
                                        <div class="ow-prirocniki-form-wrap">
                                            <?php echo do_shortcode("[gravityform id='7' title='true' description='true' field_values='ow-datoteka=".$file['prirocnik']['ID']."&ow-ime-prirocnika=".$title."']"); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </main>
                    </div>
                </div>

                <!--end content-->


            <?php
            $avia_config['currently_viewing'] = "blog";
            //get the sidebar
            get_sidebar();


            ?>


        <!--</div>--><!--end container-->

    </div><!-- close default .container_wrap element -->


<?php get_footer(); ?>