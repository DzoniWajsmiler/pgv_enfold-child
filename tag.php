<?php
    if ( !defined('ABSPATH') ){ die(); }
    
    global $avia_config, $more;

    /*
     * get_header is a basic wordpress function, used to retrieve the header.php file in your theme directory.
     */
     get_header();

     echo avia_title(array('title' => avia_which_archive()));
     
     do_action( 'ava_after_main_title' );
    ?>

        <div class='container_wrap container_wrap_first main_color <?php avia_layout_class( 'main' ); ?>'>

            <div class='container template-blog '>

                <main class='content <?php avia_layout_class( 'content' ); ?> units' <?php avia_markup_helper(array('context' => 'content'));?>>


                    <?php
                    global $wp_query, $posts;
                    $backup_query = $wp_query;

                    $tag = get_queried_object();

                    ?>


                    <div class="avia-section main_color avia-section-default avia-no-border-styling avia-bg-style-scroll  avia-builder-el-0  el_before_av_textblock  avia-builder-el-first  ow-title-section  container_wrap fullsize ow-tag-title">
                        <div class="container">
                            <main role="main" itemprop="mainContentOfPage" class="template-page content  av-content-full alpha units">
                                <div class="post-entry post-entry-type-page post-entry-890">
                                    <div class="entry-content-wrapper clearfix">
                                        <section class="av_textblock_section" itemscope="itemscope" itemtype="https://schema.org/CreativeWork">
                                            <div class="avia_textblock" itemprop="text">
                                                <h1><?php echo $tag->name; ?></h1>
                                            </div>
                                        </section>
                                    </div>
                                </div>
                            </main>
                        </div>
                    </div>





                    <?php
                    echo do_shortcode('[ow_blog_tags tag="'.$tag->slug.'"]');

                    ?>

                <!--end content-->
                </main>

                <?php

                //get the sidebar
                $avia_config['currently_viewing'] = 'blog';
                get_sidebar();

                ?>

            </div><!--end container-->

        </div><!-- close default .container_wrap element -->


<?php get_footer(); ?>
