<?php

/**
 *  These functions shows a number of posts related to the currently displayed post.
 *  Relations are defined by tags: if post tags match, the post will be displayed as related
 */
global $avia_config;


$rp = avia_get_option('single_post_related_entries');




if(!isset($avia_config['related_posts_config']))
{
	$avia_config['related_posts_config'] = array(
	
	'columns' => 8,
	'post_class' =>  "av_one_eighth no_margin ",
	'image_size' => 'square',
	'tooltip'	 => true,
	'title_short'=> false
	
	);
	
	if($rp == "av-related-style-full")
	{
		$avia_config['related_posts_config'] = array(
	
		'columns' => 6,
		'post_class' =>  "av_one_half no_margin ",
		'image_size' => 'square',
		'tooltip'	 => false,	
		'title_short'=> true	
		);
	}
}

if($rp == "disabled") return;







extract($avia_config['related_posts_config']);


$is_portfolio 		= false; //avia_is_portfolio_single();
$related_posts 		= false;
$this_id 			= $post->ID;
$slidecount 		= 0;
$postcount 			= ($columns * 1);
$format 			= "";
$fake_image			= "";
$tags               = wp_get_post_tags($this_id);



if (!empty($tags) && is_array($tags))
{
     $tag_ids = array();
     foreach ($tags as $tag ) {
	     
	     if($tag->slug != "portrait" && $tag->slug != "landscape")
	     {
	     	$tag_ids[] = (int)$tag->term_id;
	     }
	 }

     if(!empty($tag_ids))
     {


        $my_query = get_posts(
                            array(
                                'tag__in' => $tag_ids,
                                'post_type' => get_post_type($this_id),
                                'showposts'=>$postcount,
                                'ignore_sticky_posts'=>1,
                                'orderby'=>'rand',
                                'post__not_in' => array($this_id),
                                'numberposts' => 2,
                            )
                            );


  		if (!empty($my_query))
  		{
  			$output = "";
  			$count = 1;

     		$output .= "<div class ='related_posts clearfix {$rp}'>";

                $output .= "<h2 class='related_title'>".__('Ne spreglejte tudi')."</h2>";
                $output .= '<div class="ow-clanki-outer-wrap">';
                    $output .= '<div class="ow-clanki-wrap">';
                        $output.= '<div class="filtered-posts">';

                        foreach ($my_query as $related_post)
                        {
                            $related_posts = true;

                            if($count > 2) {
                                break;
                            }

                            $output.= single_blog_post_html("no", "no", "2", "purple", '', $related_post->ID);

                            $count++;

                        }
                        $output .= "</div>";
                    $output .= "</div>";
                $output .= "</div>";
     		$output.= "</div>";

     		if($related_posts) echo $output;

     	}

     	wp_reset_query();
    }
}

