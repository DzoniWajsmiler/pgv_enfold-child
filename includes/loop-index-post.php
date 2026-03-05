<?php

//BLOG POST TEMPLATE

global $avia_config, $post_loop_count;


if(empty($post_loop_count)) $post_loop_count = 1;
$blog_style = !empty($avia_config['blog_style']) ? $avia_config['blog_style'] : avia_get_option('blog_style','multi-big');
if(is_single()) $blog_style = avia_get_option('single_post_style','single-big');

$blog_global_style = avia_get_option('blog_global_style',''); //alt: elegant-blog

$blog_disabled = ( avia_get_option('disable_blog') == 'disable_blog' ) ? true : false;
if($blog_disabled)
{
    if (current_user_can('edit_posts'))
    {
        $msg = 	'<strong>'.__('Admin notice for:' )."</strong><br>".
            __('Blog Posts', 'avia_framework' )."<br><br>".
            __('This element was disabled in your theme settings. You can activate it here:' )."<br>".
            '<a target="_blank" href="'.admin_url('admin.php?page=avia#goto_performance').'">'.__("Performance Settings",'avia_framework' )."</a>";

        $content 	= "<span class='av-shortcode-disabled-notice'>{$msg}</span>";

        echo $content;
    }

    return;
}

$initial_id = avia_get_the_ID();

// check if we got posts to display:
if (have_posts()) :

    while (have_posts()) : the_post();

        /*
         * get the current post id, the current post class and current post format
          */
        $url = "";
        $current_post = array();
        $current_post['post_loop_count']= $post_loop_count;
        $current_post['the_id']	   	= get_the_ID();
        $current_post['parity']	   	= $post_loop_count % 2 ? 'odd' : 'even';
        $current_post['last']      	= count($wp_query->posts) == $post_loop_count ? " post-entry-last " : "";
        $current_post['post_type']	= get_post_type($current_post['the_id']);
        $current_post['post_class'] 	= "post-entry-".$current_post['the_id']." post-loop-".$post_loop_count." post-parity-".$current_post['parity'].$current_post['last']." ".$blog_style;
        $current_post['post_class']	.= ($current_post['post_type'] == "post") ? '' : ' post';
        $current_post['post_format'] 	= get_post_format() ? get_post_format() : 'standard';
        $current_post['post_layout']	= avia_layout_class('main', false);
        $blog_content = !empty($avia_config['blog_content']) ? $avia_config['blog_content'] : "content";

        /*If post uses builder change content to exerpt on overview pages*/
        if( Avia_Builder()->get_alb_builder_status( $current_post['the_id'] ) && !is_singular($current_post['the_id']) && $current_post['post_type'] == 'post')
        {
            $current_post['post_format'] = 'standard';
            $blog_content = "excerpt_read_more";
        }


        /*
         * retrieve slider, title and content for this post,...
         */
        $size = strpos($blog_style, 'big') ? (strpos($current_post['post_layout'], 'sidebar') !== false) ? 'entry_with_sidebar' : 'entry_without_sidebar' : 'square';

        if(!empty($avia_config['preview_mode']) && !empty($avia_config['image_size']) && $avia_config['preview_mode'] == 'custom') $size = $avia_config['image_size'];
        $current_post['slider']  	= get_the_post_thumbnail($current_post['the_id'], $size);

        if(is_single($initial_id) && get_post_meta( $current_post['the_id'], '_avia_hide_featured_image', true ) ) $current_post['slider'] = "";


        $current_post['title']   	= get_the_title();
        $current_post['content'] 	= $blog_content == "content" ? get_the_content(__('Read more','avia_framework').'<span class="more-link-arrow"></span>') : get_the_excerpt();
        $current_post['content'] 	= $blog_content == "excerpt_read_more" ? $current_post['content'].'<div class="read-more-link"><a href="'.get_permalink().'" class="more-link">'.__('Read more','avia_framework').'<span class="more-link-arrow"></span></a></div>' : $current_post['content'];
        $current_post['before_content'] = "";

        /*
         * ...now apply a filter, based on the post type... (filter function is located in includes/helper-post-format.php)
         */
        $current_post	= apply_filters( 'post-format-'.$current_post['post_format'], $current_post );
        $with_slider    = empty($current_post['slider']) ? "" : "with-slider";
        /*
         * ... last apply the default wordpress filters to the content
         */


        $current_post['content'] = str_replace(']]>', ']]&gt;', apply_filters('the_content', $current_post['content'] ));

        /*
         * Now extract the variables so that $current_post['slider'] becomes $slider, $current_post['title'] becomes $title, etc
         */
        extract($current_post);


        /*
         * render the html:
         */

        echo "<article class='".implode(" ", get_post_class('post-entry post-entry-type-'.$post_format . " " . $post_class . " ".$with_slider))."' ".avia_markup_helper(array('context' => 'entry','echo'=>false)).">";



        //default link for preview images
        $link = !empty($url) ? $url : get_permalink();

        //preview image description
        $desc = get_post( get_post_thumbnail_id() );
        if(is_object($desc))  $desc = $desc -> post_excerpt;
        $featured_img_desc = ( $desc != "" ) ? $desc : the_title_attribute( 'echo=0' );

        //on single page replace the link with a fullscreen image
        if(is_singular())
        {
            $link = avia_image_by_id(get_post_thumbnail_id(), 'large', 'url');
        }


        echo "<div class='entry-content-wrapper clearfix {$post_format}-content'>";



        //POST CONTENT
        //get content from advanced layout editor

        $content_simple = $content;

        if ( isset( $_REQUEST['avia_alb_parser'] ) && ( 'show' == $_REQUEST['avia_alb_parser'] ) && current_user_can( 'edit_post', get_the_ID() ) ) {
            /**
             * Display the parser info
             */
            $content = Avia_Builder()->get_shortcode_parser()->display_parser_info();

            /**
             * Allow e.g. codeblocks to hook properly
             */
            $content = apply_filters( 'avia_builder_precompile', $content );

            Avia_Builder()->get_shortcode_parser()->set_builder_save_location( 'none' );
            $content = ShortcodeHelper::clean_up_shortcode( $content, 'balance_only' );
            ShortcodeHelper::$tree = ShortcodeHelper::build_shortcode_tree( $content );
        }
        else if( ! is_preview() )
        {
            /**
             * Filter the content for content builder elements
             */
            $content = apply_filters( 'avia_builder_precompile', get_post_meta( get_the_ID(), '_aviaLayoutBuilderCleanData', true ) );
        }
        else
        {
            /**
             * If user views a preview we must use the content because WordPress doesn't update the post meta field
             */
            $content = apply_filters( 'avia_builder_precompile', get_the_content() );

            /**
             * In preview we must update the shortcode tree to reflect the current page structure.
             * Prior make sure that shortcodes are balanced.
             */
            Avia_Builder()->get_shortcode_parser()->set_builder_save_location( 'preview' );
            $content = ShortcodeHelper::clean_up_shortcode( $content, 'balance_only' );
            ShortcodeHelper::$tree = ShortcodeHelper::build_shortcode_tree( $content );
        }

        /**
         * @since 4.4.1
         */
        do_action( 'ava_before_content_templatebuilder_page' );

        //check first builder element. if its a section or a fullwidth slider we dont need to create the default openeing divs here
        $first_el = isset(ShortcodeHelper::$tree[0]) ? ShortcodeHelper::$tree[0] : false;
        $last_el  = !empty(ShortcodeHelper::$tree)   ? end(ShortcodeHelper::$tree) : false;
        if(!$first_el || !in_array($first_el['tag'], AviaBuilder::$full_el ) )
        {
            echo avia_new_section(array('close'=>false,'main_container'=>true, 'class'=>'main_color container_wrap_first'));
        }

        $content = apply_filters('the_content', $content);
        $content = apply_filters('avf_template_builder_content', $content);

        if($content === ""){
            $content = $content_simple;
        }

        echo $content;








        echo "<div class='post_delimiter'></div>";
        echo "</div>";
        echo "<div class='post_author_timeline'></div>";
        echo av_blog_entry_markup_helper($current_post['the_id']);

        echo '<footer class="entry-footer">';

        $avia_wp_link_pages_args = apply_filters('avf_wp_link_pages_args', array(
            'before' =>'<nav class="pagination_split_post">'.__('Pages:','avia_framework'),
            'after'  =>'</nav>',
            'pagelink' => '<span>%</span>',
            'separator'        => ' ',
        ));

        wp_link_pages($avia_wp_link_pages_args);

        if(is_single() && !post_password_required())
        {

            //share links on single post
            echo '<div class="share-links">';
            avia_social_share_links();
            echo '</div>';

            //tags on single post
            if(has_tag())
            {
                echo '<div class="blog-tags minor-meta">';
                the_tags('<span>#</span>', ' <span>#</span>');
                echo '</div>';
            }

        }

        do_action('ava_after_content', $the_id, 'post');

        $prirocniki_link = get_field("povezava_do_prirocnika");

        if($prirocniki_link != null){
            $prirocnik_id = get_field("prirocnik");

            $title = get_the_title($prirocnik_id);
            $excerpt = get_the_excerpt($prirocnik_id);
            $image = get_the_post_thumbnail($prirocnik_id);
            $link = get_permalink($prirocnik_id);

            echo '<div class="prirocniki-blog-link ow-prirocniki container-690">';

                echo '<div class="ow-row ow-img-wrap">';

                      if($image):
                        echo '<div class="ow-left">';
                            echo '<div class="ow-img-wrap">';
                            echo $image;
                            echo '</div>';
                        echo '</div>';
                      endif;

                    echo '<div class="ow-right ow-text no-p">';

                    if($title){
                        echo '<h3>'. $title . '</h3>';
                    }
                    if($excerpt){
                        echo '<p>'. $excerpt . '</p>';
                    }
                    if($link){
                        echo do_shortcode("[av_button label='Kaj mi priročnik ponuja' link='".$link."' link_target='' color='purple'][/av_button]");
                    }
                    echo '</div>';

                echo '</div>';

            echo '</div>';
        }

        echo '</footer>';

        echo "</article>";

        $post_loop_count++;
    endwhile;
else:

    ?>

    <article class="entry">
        <header class="entry-content-header">
            <h1 class='post-title entry-title'><?php _e('Nothing Found', 'avia_framework'); ?></h1>
        </header>

        <p class="entry-content" <?php avia_markup_helper(array('context' => 'entry_content')); ?>><?php _e('Sorry, no posts matched your criteria', 'avia_framework'); ?></p>

        <footer class="entry-footer"></footer>
    </article>

<?php

endif;

if(empty($avia_config['remove_pagination'] ))
{
    echo "<div class='{$blog_style}'>".avia_pagination('', 'nav')."</div>";
}
?>
