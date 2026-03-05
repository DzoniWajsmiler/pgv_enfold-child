<?php



/* slider on homepage */

function homepage_slider($content = null) {



if( have_rows('homepage_slider') ):



    $link = get_field('homepage_slider_link');



    $content.= '<div class="ow-home-slider-wrap">';

    $content.= '<div class="ow-home-slider">';



    while ( have_rows('homepage_slider') ) : the_row();

        $image = get_sub_field('homepage_slider_image');

        $title = get_sub_field('homepage_slider_title');

        $text = get_sub_field('homepage_slider_text');



        $content.= '<div class="ow-home-slide-wrap">';

        $content.= '<div class="ow-home-slide">';



        $content.= '<div class="left-half">';

        $content.= '<img data-lazy="'.$image["sizes"]["large"].'" />';

        $content.= '</div>';



        $content.= '<div class="right-half">';

        $content .= do_shortcode('[ow-prednosti]');

        //$content.= '<h2>'.$title.'</h2>';

        $content.= '<p>'.$text.'</p>';
        
        if($link):

        $content.= '<a class="homepage-slider-link" href="'.$link['url'].'" target="'.$link['target'].'">';

        $content.= $link['title'];

        $content.= '</a>';

        endif;

        $content.= '</div>';



        $content.= '</div>';

        $content.= '</div>';



    endwhile;



    $content.= '</div>';







    $content.= '</div>';



endif;



return $content;

}

add_shortcode('homepage-slider', 'homepage_slider');



/* reviews */

function reviews($args, $content = null) {

$defaults = array(

    "product_id" => false,

    "type" => "",

);



$params = shortcode_atts($defaults, $args);

$get_data_from = $params['product_id'];

if(!$get_data_from || $get_data_from == false){

    $get_data_from = 'option';

}



$type = $params['type'];

if($type === "sponzor"){ //use type="sponzor" to get data from current page, else use reviews from default site (planetgv.si)

    $get_data_from = get_the_ID();

}



if( have_rows('mnenja_udelezencev', $get_data_from) ):



    $naslov = get_field('naslov_mnenje_udelezencev', $get_data_from);



    $content.= '<div class="ow-reviwes-slider-container">';



    $content.= '<h2 class="show-on-mobile">'.$naslov.'</h2>';

    $content.= '<div class="ow-reviwes-slider-wrap">';



    $content.='<div class="ow-circles-wrap">';

    $content.='<div class="ow-circle-big"></div>';

    $content.='<div class="ow-circle-small"></div>';

    $content.= '</div>';



    $content.= '<div class="ow-reviews-slider-nav">';



    $index_nav = 0;



    while ( have_rows('mnenja_udelezencev', $get_data_from) ) : the_row();



        $index_nav++;



        if($index_nav > 7){

            continue;

        }



        $image = get_sub_field('slika_udelezenca');



        $content.= '<div class="ow-img-thumbnail">';

        $content.= '<img class="circle-image" src="'.$image["sizes"]["medium"].'" />';

        $content.= '</div>';



    endwhile;



    $content.= '</div>';



    $content.= '<div class="ow-reviews-slider">';



    $index_slider = 0;



    while ( have_rows('mnenja_udelezencev', $get_data_from) ) : the_row();



        $index_slider++;



        if($index_slider > 7){

            continue;

        }



        $image = get_sub_field('slika_udelezenca');

        $review = get_sub_field('citat');

        $name = get_sub_field('ime_in_priimek');

        $company = get_sub_field('podjetje');



        $content.= '<div class="ow-review">';



        $content.= '<div class="img-wrap">';

        $content.= '<img class="circle-image" src="'.$image["sizes"]["large"].'" />';

        $content.= '</div>';



        $content.= '<div class="review-wrap ow-quote ow-quote-no-effect">';

        $content.= '<h2>'.$naslov.'</h2>';

        $content.= '<blockquote class="text">'.$review.'</blockquote>';

        $content.= '<blockquote class="no-image author"><p class="quote-author">'.$name;

        if($company){

            $content.= ' | '. $company;

        }

        $content.= '</p></blockquote>';

        $content.= '</div>';

        $content.= '</div>';



    endwhile;



    $content.= '</div>';



    $content.= '</div>';



    $content.= '</div>';



endif;





return $content;

}

add_shortcode('reviews', 'reviews');





/* magazines banner */

function single_magazine($group_field, $content = null){

if( have_rows($group_field, 'option') ):



    while( have_rows($group_field, 'option') ): the_row();



        $img1 = get_sub_field('slika_revije_1');

        $img2 = get_sub_field('slika_revije_2');

        $badge_text = get_sub_field('tekst_v_znacki');

        $badge_icon = get_sub_field('ikona_v_znacki');

        $title = get_sub_field('naslov_revije');

        $description = get_sub_field('kratek_opis');

        $link = get_sub_field('povezava');



        



        $content.= '<div class="magazine-texts">';

        $content.= '<h3>'.$title.'</h3>';

        $content.= '<p>'.$description.'</p>';

        if($link):

            $content.= do_shortcode("[av_button label='".$link['title']."' link='".$link['url']."' link_target='".$link['target']."' color='purple'][/av_button]");

        endif;



        $content.= '</div>';


$content.= '<div class="magazine-images">';

        $content.= '<img class="first-img" src="'.$img1.'" />';

        $content.= '<img class="second-img" src="'.$img2.'" />';

        $content .= '<div class="ow-badge">';

        $content.= '<img src="'.$badge_icon.'" />';

        $content.= '<p>'.$badge_text.'</p>';

        $content.='</div>';

        $content.= '</div>';


    endwhile;



endif;



return $content;

}


add_shortcode('homepage-countdown', 'homepage_countdown');
function homepage_countdown($args) {
$event_id = get_field('homepage_product');

$date = get_field('ow_event_start_date', $event_id);

add_action('wp_footer', function() {
   ?>
   <style>
    #countdown {
      text-align: center;
      margin-top: 20px;
    }
       #countdown li {
display: inline-block;
    text-align: center;
    list-style-type: none;
    padding: 1em 0.5em 0.5em 0.5em;
    text-transform: uppercase;
    background-color: #EAEAEA;
    border-radius: 6px;
    width: 90px;
}

#countdown li span.time {
  font-size: 2.5em !important;
}

#countdown li span.txt {
display: block;
font-size:0.8rem !important;
}

@media screen and (max-width: 767px) {
#countdown li {
    width: 64px;
    margin-left: 5px !important;
}
#countdown li:first-child {
    margin-left: 0 !important;
}

#countdown li span.time {
  font-size: 2.0em !important;
}

#countdown li span.txt {
font-size:0.7rem !important;
}
}
   </style>
   <?php
});

ob_start();
echo '<div id="countdown">
<ul>
  <li><span class="time" id="days"></span><span class="txt">DNI</span></li>
  <li><span class="time" id="hours"></span><span class="txt">UR</span></li>
  <li><span class="time" id="minutes"></span><span class="txt">MINUT</span></li>
  <li><span class="time" id="seconds"></span><span class="txt">SEKUND</span></li>
</ul>
</div>';

?>
<script>
    (function () {
const second = 1000,
    minute = second * 60,
    hour = minute * 60,
    day = hour * 24;

let today = new Date(),
  dd = String(today.getDate()).padStart(2, "0"),
  mm = String(today.getMonth() + 1).padStart(2, "0"),
  yyyy = today.getFullYear(),
  nextYear = yyyy + 1,
  event_date = '<?php echo $date; ?>';
  console.log(event_date)
  //event_date = '12/11/2022'

today = mm + "/" + dd + "/" + yyyy;
if (today > event_date) {
  document.getElementById("countdown").style.display = "none";
//event_date = dayMonth + nextYear;
}


const countDown = new Date(event_date).getTime(),
  x = setInterval(function() {    

    const now = new Date().getTime(),
          distance = countDown - now;

    document.getElementById("days").innerText = Math.floor(distance / (day)),
      document.getElementById("hours").innerText = Math.floor((distance % (day)) / (hour)),
      document.getElementById("minutes").innerText = Math.floor((distance % (hour)) / (minute)),
      document.getElementById("seconds").innerText = Math.floor((distance % (minute)) / second);

    //do something later when date is reached
    if (distance < 0) {
     document.getElementById("countdown").style.display = "none";
     // document.getElementById("content").style.display = "block";
      clearInterval(x);
    }
    //seconds
  }, 0)
}());
</script>
<?php
return ob_get_clean();

}

function magazines_banner($content = null) {



$title = get_field('strokovne_revije_naslov', 'option');

$subtitle = get_field('strokovne_revije_podnaslov', 'option');



$content.= '<div class="magazines-wrap">';
/*
$content.= '<h2>'.$title.'</h2>';

$content.= '<p>'.$subtitle.'</p>';

*/

$content.= '<div class="magazines-inner-wrap">';



$content.= '<div class="magazine-1 single-magazine">';



$content.= single_magazine('revija_hrm');



$content.= '</div>';



$content.= '<div class="magazine-2 single-magazine">';



$content.= single_magazine('revija_adma');



$content.= '</div>';



$content.= '</div>';



$content.= '</div>';







return $content;

}

add_shortcode('strokovne_revije_banner', 'magazines_banner');





/* footer - social icons*/

function ow_social_icons($content = null) {



if( have_rows('socialna_omrezja') ):



    while( have_rows('socialna_omrezja') ): the_row();



        $icon = get_sub_field('ikona');

        $link = get_sub_field('povezava');



        $content.= '<div class="ow-socials-wrap">';



        $content.= '<a class="ow-socials-link" href="'.$link.'" target="_blank">';



        $content.= '<div class="ow-social-icon">';



        $content.= '<img src="'.$icon.'">';



        $content.= '</div>';



        $content.= '</a>';



        $content.= '</div>';



    endwhile;



endif;



return $content;

}

add_shortcode('ow-social-icons', 'ow_social_icons');





/* prednosti */
function ow_prednosti($content='') {
    $index = 0;

    if( have_rows('prednosti') ): // <- brez 'option'!

        $content.= '<div class="ow-prednosti">';

        while( have_rows('prednosti') ): the_row();

            $index++;
            $icon = get_sub_field('ikona');
            $title = get_sub_field('naslov');
            $text = get_sub_field('teskt');

            $content .= '<div class="ow-prednost slideanim slide-'.$index.'">';
            $content .= '<img src="'.$icon.'">';
            $content .= '<h4>'.$title.'</h4>';
            $content .= '<p>'.$text.'</p>';
            $content .= '</div>';

        endwhile;

        $content .= '</div>';
    endif;

    return $content;
}

add_shortcode('ow-prednosti', 'ow_prednosti');





/* popup sponzorstvo */

function ow_sponzorstvo_popup($content=''){

$content.='<div id="open-popup" class="popup-container">

    <div id="close-popup"><img src="'.get_home_url().'/wp-content/themes/enfold-child/images/close.svg"></div>

    <div class="popup-wrapper">

        <div class="popup-bg">

            <div class="popup-content">'.do_shortcode("[gravityform id='2' title='true' description='true']").'

            </div>

        </div>

    </div>

</div>';



return $content;

}



add_shortcode('sponzorstvo_popup', 'ow_sponzorstvo_popup');





add_shortcode('ow-prednosti', 'ow_prednosti');





/* zaposleni */

function prikaz_zaposlenih($content=''){



if( have_rows('zaposleni') ) {



    $content.= '<div class="ow-zaposleni-wrap">';



    while (have_rows('zaposleni')) {

        the_row();



        $image = get_sub_field('slika_zaposlenega');

        $name = get_sub_field('ime_in_priimek');

        $position = get_sub_field('pozicija');

        $phone = get_sub_field('telefonska_stevilka');



        $content.= '<div class="ow-zaposleni">';

        $content.= '<img src="'.$image.'">';

        $content.= '<p><b>'.$name.'</b></p>';

        $content.= '<p>'.$position.'</p>';

        $content.= '<p><b>'.$phone.'</b></p>';

        $content.= '</div>';



    }



    $content.= '</div>';

}



return $content;

}



add_shortcode('prikaz_zaposlenih', 'prikaz_zaposlenih');







/* nagrade */

function prikaz_nagrad($content=''){



$block_title = get_field("podeljujemo_nagrade_naslov");

$block_subtitle = get_field("podeljujemo_nagrade_podnaslov");



if( have_rows('nagrade') ) {



    $content.= '<div class="ow-nagrade-wrap">';



    $content.= '<div class="ow-nagrade-left">';



    $content.= '<div id="nagrade-anchor" class="ow-nagrade-slider-img">';



    while (have_rows('nagrade')) {

        the_row();



        $image = get_sub_field('slika');

        $desc = get_sub_field('kratek_opis_slike');

        $link = get_sub_field('povezava_do_strani_o_nagradi');



        $content.= '<div class="ow-nagrade-single-img">';



        if($link) {

            $content .= '<a class="prevent-def" href="'.$link["url"].'">';

        }



        $content.= '<div class="ow-nagrade-inner-wrap">';

        $content.= '<img  data-lazy="'.$image.'" />';

        $content.= '<div class="ow-nagrade-overlay"></div>';

        $content.= '<p class="ow-nagrade-img-text">'.$desc.'</p>';

        $content.= '</div>';



        if($link) {

            $content .= '</a>';

        }

        $content.= '</div>';





    }



    $content.= '</div>';



    $content.= '</div>';



    $content.= '<div class="ow-nagrade-right ow-text">';



    $content.= '<h2>'.$block_title.'</h2>';

    $content.= '<p>'.$block_subtitle.'</p>';



    $content.= '<div class="ow-nagrade-slider-text">';



    while (have_rows('nagrade')) {

        the_row();



        $title = get_sub_field('naslov_nagrade');





        $content.= '<div class="ow-nagrade-single-text">';

        $content.= '<p class="ow-link">'.$title.'</p>';

        $content.= '</div>';





    }



    $content.= '</div>';



    $content.= '</div>';



    $content.= '</div>';

}



return $content;

}



add_shortcode('prikaz_nagrad', 'prikaz_nagrad');









/* delavnica na ključ */

function delavniceNaKljuc($atts,$content = null)

{

$defaults = array(

    "podrocje"=> "",

);



$params = shortcode_atts($defaults, $atts);

$field = $params['podrocje'];



if($field === "delavnica" || $field === "delavnice"){

    $image = get_field('delavnica_na_kljuc_slika', 'option');

    $title = get_field('delavnica_na_kljuc_naslov', 'option');

    $option_content = get_field('delavnica_na_kljuc_vsebina', 'option');

    $link = get_field('delavnica_na_kljuc_povezava', 'option');



} else if($field === "konferenca" || $field === "konference"){

    $image = get_field('konferenca_na_kljuc_slika', 'option');

    $title = get_field('konferenca_na_kljuc_naslov', 'option');

    $option_content = get_field('konferenca_na_kljuc_vsebina', 'option');

    $link = get_field('konferenca_na_kljuc_povezava', 'option');

} else {

    $image = get_field('izobrazevanje_na_kljuc_slika', 'option');

    $title = get_field('izobrazevanje_na_kljuc_naslov', 'option');

    $option_content = get_field('izobrazevanje_na_kljuc_vsebina', 'option');

    $link = get_field('izobrazevanje_na_kljuc_povezava', 'option');

    $link2 = get_field('izobrazevanje_na_kljuc_povezava2', 'option');

}



$content .= '<div class="delavnica-na-kljuc-wrap">';



$content .= '<div class="flex_column av_one_third  flex_column_div av-zero-column-padding first  avia-builder-el-5  el_before_av_two_third  avia-builder-el-first  ow-delavnice-banner-img">';

$content.= '<div class="avia-image-container">';

$content .= "<img src='{$image["url"]}' />";

$content .= '</div>';

$content .= '</div>';



$content .= '<div class="flex_column av_two_third  flex_column_div av-zero-column-padding   avia-builder-el-7  el_after_av_one_third  avia-builder-el-last  ow-text no-p ow-cat-desc-right">';

$content .= '<h2>'.$title.'</h2>';



$content .= '<p>'.$option_content.'</p>';



if($link) {

    $title = $link['title'];

    $url = $link['url'];

    $target = $link['target'];

    $content .= do_shortcode("[av_button label='".$title."' link='manually,".$url."' link_target='".$target."' color='purple']");

}



if (isset($link2)) {
    // uporabi $link2
    $title = $link2['title'];

    $url = $link2['url'];

    $target = $link2['target'];

    $content .= do_shortcode("[av_button label='".$title."' link='manually,".$url."' link_target='".$target."' color='purple']");

}

$content .= '</div>';



$content .= '</div>';



return $content;

}



add_shortcode('delavnice_na_kljuc', 'delavniceNaKljuc');









/* blog categories list */





function add_template($args)

{



if (!isset($args['path'])) {

    return null;

}



return get_template_part( $args['path'] );



}



add_shortcode('add_template','add_template');


function ow_blog_categories($atts, $content = null){
    $defaults = array(
        "cat"              => "",
        "cat-filter"       => "no",
        "show"             => "10",
        "sort"             => "DESC",
        "related-mode"     => "no",
        "excerpt"          => "yes",
        "link"             => "yes",
        "link_color"       => "purple",
        "posts_in_row"     => "2",
        "use_ajax"         => "no",
        "source"           => "query" // novo: "query" ali "acf"
    );

    $params         = shortcode_atts($defaults, $atts);
    $cat            = $params['cat'];
    $cat_f          = $params['cat-filter'];
    $show           = $params['show'];
    $sort           = $params['sort'];
    $related_mode   = $params['related-mode'];
    $show_excerpt   = $params['excerpt'];
    $show_link      = $params['link'];
    $link_color     = $params['link_color'];
    $posts_in_row   = $params['posts_in_row'];
    $use_ajax       = $params['use_ajax'];
    $source         = $params['source'];

    $post_id = '';
    if($related_mode == "yes"){
        $post_id = get_the_id();
    }

    $content .= '<div class="ow-clanki-outer-wrap">';
    $content .= '<div class="ow-clanki-wrap">';
	
$categories = get_categories(array('hide_empty' => false));

$content .= '<div class="filter-wrap">';
$content .= '<button class="ow-single-cat ow-clanki-cat-clicked" data-cat="all" data-show="' . intval($show) . '">Vsi</button>';
foreach ($categories as $category) {
    $content .= '<button class="ow-single-cat" data-cat="' . esc_attr($category->slug) . '" data-show="' . intval($show) . '">' . esc_html($category->name) . '</button>';
}
$content .= '</div>';
	
	
    $content .= '<div id="clanki-anchor" class="filtered-posts">';

    if($use_ajax == "no") {

        // ACF logika:
        if ($source === "acf") {
            if (!is_singular('product')) {
                return '<p>Shortcode [ow_blog_categories source="acf"] deluje samo na strani produkta.</p>';
            }

            $local_product_id = get_the_ID();
            $event_id = get_field('kotizacija_id', $local_product_id);

            if (empty($event_id)) {
                return '<p>Dogodek (kotizacija_id) ni definiran.</p>';
            }

            switch_to_blog(1);
error_log('[DEBUG] Blog ID: ' . get_current_blog_id());
error_log('[DEBUG] Main product ID: ' . $main_product_id);
error_log('[DEBUG] Related post IDs: ' . print_r($related_posts, true));
            $main_products = get_posts(array(
                'post_type' => 'product',
                'meta_key' => 'kotizacija_id',
                'meta_value' => $event_id,
                'posts_per_page' => 1
            ));

            if (!empty($main_products)) {
                $main_product_id = $main_products[0]->ID;
                $related_posts = get_field('izbira_clankov', $main_product_id);
error_log('🎯 ACF POST IDS: ' . print_r($related_posts, true));


                if (!empty($related_posts)) {
                    foreach (array_slice($related_posts, 0, (int) $show) as $acf_post_id) {
                        $content .= single_blog_post_html($show_excerpt, $show_link, $posts_in_row, $link_color, '', $acf_post_id);
                    }
                } else {
                    $content .= '<p>Ni izbranih člankov za konferenco.</p>';
                }
            } else {
                $content .= '<p>Ni povezanega produkta na glavni strani.</p>';
            }

            restore_current_blog();

        } else {
            // standardni WP_Query del
            $paged = get_query_var("paged")?get_query_var("paged"):1;

            $args_search_qqq = array(
                'post_type' => 'post',
                'post_status' => 'publish',
                'paged' => $paged,
                'posts_per_page' => $show,
                'order' => $sort,
                'post__not_in' => array($post_id),
            );

            if (!empty($cat)) {
                $args_search_qqq['tax_query'] = array(
                    array(
                        'taxonomy' => 'category',
                        'field'    => 'slug',
                        'terms'    => $cat
                    )
                );
            }

            $the_query = new WP_Query($args_search_qqq);

            if ($the_query->have_posts()):
                while ($the_query->have_posts()) : $the_query->the_post();
                    $content .= single_blog_post_html($show_excerpt, $show_link, $posts_in_row, $link_color);
                endwhile;
            endif;
        }
    }

    $content.= '</div>'; // filtered-posts
    $content.= '</div>'; // ow-clanki-wrap
    $content.= '</div>'; // ow-clanki-outer-wrap
    
    add_action('wp_footer', function() {
    ?>
    <script>
        jQuery(document).ready(function($){

    $('.filter-wrap').ready(function() {
        if($('.filter-wrap').length > 0) {
            var category = $(".ow-clanki-cat-clicked").data('cat');
            var show = $(".ow-clanki-cat-clicked").data('show');

            load_blog_posts(category, 0, show);
        }
    });


    jQuery( "body" ).on( "click", ".ow-single-cat", function() {
        var category = $(this).data('cat');
        var show = $(this).data('show');

        var old_category = getUrlParameter('kategorija');
        changeUrlParameter ('kategorija', old_category, category);

        load_blog_posts(category, 0, show);

        $(".ow-single-cat").removeClass("ow-clanki-cat-clicked");
        $(this).addClass("ow-clanki-cat-clicked");

    });

    $("body").on("click", ".pagination-ajax .page-numbers", function(e){
        e.preventDefault();

        if($(this).hasClass("current")){
            return;
        }

        var category = $(".ow-clanki-cat-clicked").data('cat');
        var tag = $(".pagination-ajax").data("tag");

        var page = $(this).attr("href");
        var page_num = page.replace('?paged=','');

        if($.type(category) !== "undefined") {
            var show = $(".ow-clanki-cat-clicked").data('show');
            load_blog_posts(category, 0, show, page_num);
        } else if($.type(tag) !== "undefined"){
            var show = $(".pagination-ajax").data('show');
            load_blog_posts(0, tag, show, page_num);
        }
    });

    function load_blog_posts(category, tag, show, page){
        if(!page){ page = 1;}

        data = {
            'action': 'filterposts',
            'category': category,
            'tag':tag,
            'show': show,
            'page':page
        };

        $.ajax({
            url : avia_framework_globals.ajaxurl,
            data : data,
            type : 'POST',
            beforeSend : function ( xhr ) {
                $( ".ow-clanki-wrap").addClass("ow-loader-active"); //show loader
                $('.js-category').attr( 'disabled', 'disabled' );
            },
            success : function( data ) {
                if ( data ) {
                    $('.filtered-posts').html( data.posts );
                    $('.js-category').removeAttr('disabled');

                } else {
                    $('.filtered-posts').html( 'No posts found.' );
                }

                if($("#clanki-anchor").length > 0) {
                    var anchor = $("#clanki-anchor");
                    $('html,body').animate({scrollTop: anchor.offset().top}, 'slow');
                }
            },
            complete :function() {
                $( ".ow-clanki-wrap").removeClass("ow-loader-active");
            }
        });
    }

});
jQuery(document).ready(function($) {

    /* function for getting url parameters */
    window.getUrlParameter = function (sParam){
        var sPageURL = window.location.search.substring(1),
            sURLVariables = sPageURL.split('&'),
            sParameterName,
            i;

        for (i = 0; i < sURLVariables.length; i++) {
            sParameterName = sURLVariables[i].split('=');

            if (sParameterName[0] === sParam) {
                return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
            }
        }
    };

    /* function for changing/removing url parameters */
    window.changeUrlParameter = function (param, old_category, new_category){

        var newUrl = document.location.href;
        var remove = false;
        if (new_category === "all" || new_category === "ne") {
            remove = true;
        }

        if (old_category) { //param exists in url

            if (remove) { //remove param from url
                if (document.location.href.indexOf("?" + param) !== -1) { //this param is first of all params
                    if (document.location.href.indexOf("&") !== -1) { //this param is not only param -> leave ? and remove & after
                        newUrl = location.href.replace(param + "=" + old_category + "&", "");
                    } else { //remove everything
                        newUrl = location.href.replace("?" + param + "=" + old_category, "");
                    }
                } else { //this param is not first of all params -> remove with &
                    newUrl = location.href.replace("&" + param + "=" + old_category, "");
                }
            } else { //replace param in url
                newUrl = location.href.replace(param + "=" + old_category, param + "=" + new_category);
            }

        } else { //param doesn't exist in url

            if (!remove) { //add param
                if (document.location.href.indexOf('?') !== -1) { //no params in url yet
                    newUrl = document.location.href + "&" + param + "=" + new_category;
                } else { //other params already in url
                    newUrl = document.location.href + "?" + param + "=" + new_category;
                }
            }
        }

        window.history.replaceState(null, null, newUrl);
    }

});
    </script>
    <?php
});

    return $content;
}

add_shortcode('ow_blog_categories', 'ow_blog_categories');




function ow_blog_tags($atts, $content = null){



$defaults = array(

    "tag"					=> "",

);



$params 					= shortcode_atts($defaults, $atts);

$tag						= $params['tag'];

$show = "10";





$content .= '<div class="ow-clanki-outer-wrap">';
$content .= '<div class="ow-clanki-wrap">';
$content.= '<div id="clanki-anchor" class="filtered-posts">';





if(isset($_GET['stran'])) {

    $paged = $_GET['stran'];

} else {

    $paged = get_query_var("paged") ? get_query_var("paged") : 1;

}



$args_search_qqq = array(

    'post_type' => 'post',

    'post_status' => 'publish',

    'paged' => $paged,

    'posts_per_page' => $show,

    'order' => 'rand',

    'tag' => $tag

);



wp_enqueue_script('ow-url-functions', get_stylesheet_directory_uri() . '/js/ow-url-functions.js', array('jquery'), '1.0.0', true);

wp_enqueue_script('ow-clanki', get_stylesheet_directory_uri() . '/js/ow-clanki.js', array('jquery'), '1.0.0', true);





$the_query = new WP_Query($args_search_qqq);



if ($the_query->have_posts()):



    while ($the_query->have_posts()) :



        $the_query->the_post();



        $content .= single_blog_post_html();



    endwhile;



endif;



$content.= "<div class='ow-custom-pagination pagination-ajax' data-show='".$show."' data-tag='".$tag."'>";



$pagination_args = array(

    'base'               => '%_%',

    'format'             => '?paged=%#%',

    'total'              => $the_query->max_num_pages,

    'current'            => $paged,

    'show_all'           => false,

    'end_size'           => 1,

    'mid_size'           => 2,

    'prev_next'          => true,

    'prev_text'          => __('<span class="prev"></span>'),

    'next_text'          => __('<span class="next"></span>'),

    'add_args'           => true,

    'add_fragment'       => '',

    'before_page_number' => '<span>',

    'after_page_number'  => '</span>'

);



$content.= paginate_links( $pagination_args );



$content.= "</div>";





$content.= '</div>';

$content.= '</div>';

$content.= '</div>';



return $content;

}



add_shortcode('ow_blog_tags', 'ow_blog_tags');











function single_blog_post_html($show_excerpt = "no", $more_link = "no", $posts_in_row = "2", $link_color = "purple", $category_slug = '', $post_id = false, $content = null){



if($post_id == false){

    $post_id = get_the_id();

}



$author = get_field('ow-avtor', $post_id);

$author_img = get_field('ow-slika-avtorja', $post_id);

//$author_img_thumbnail = $author_img['sizes']['thumbnail'];
$author_img_thumbnail = '';
if (!empty($author_img) && isset($author_img['sizes']) && isset($author_img['sizes']['thumbnail'])) {
    $author_img_thumbnail = $author_img['sizes']['thumbnail'];
}



$post_url = get_the_permalink($post_id);

$categories =  get_the_terms($post_id, 'category');

$category_name = '';



if($category_slug != '' && $category_slug != 'all'){

    foreach ($categories as $category) {

        if ($category_slug == $category->slug) {

            $category_name = $category->name;

            break;

        }

    }

} else {

    if($categories) {

        foreach ($categories as $category) {

            if ($category->name != "Nekategorizirano") {

                $category_name = $category->name;

                break;

            }

        }

    }

}



$content.= "<div class='ow-single-post-wrap ow-".$posts_in_row."-posts'>";

$content.= "<a class='prevent-def' href='".$post_url."'>";

$content.= "<div class='ow-single-post-inner'>";

$content.= "<div class='ow-post-img-wrap'>";



if ($category_name != '') {

    $content.= "<div class='ow-single-post-category'>" . $category_name . "</div>";

}



if(get_the_post_thumbnail($post_id)) {

    $content .= get_the_post_thumbnail($post_id);

} else {

    $content.= '<div class="placeholder-img"></div>';

}



$content.= "<div class='ow-author-wrap'>";

if ($author) {

    if ($author_img_thumbnail) {

        $content.= "<img src='{$author_img_thumbnail}' />";

    } else {

        $content.= "<span class='ow-speaker-img-replacement'></span>";

    }

    $content.= "<p class='ow-single-post-author'>" . $author . "</p>";

}

$content.= "</div>";



$content.= "</div>";



if (get_the_title($post_id)) {

    $content .= "<div class='ow-single-post-title'><h3>" . get_the_title($post_id) . "</h3></div>";

}



if ($show_excerpt == "yes" && get_the_excerpt($post_id)) {

    $content.= "<div class='ow-single-post-excerpt'><p>" . get_the_excerpt($post_id) . "</p></div>";

}



if($more_link == "yes") {



    $button_txt = 'Preberite več';



    if($link_color != "pink"){

        $link_color = "purple";

    }



    if ($post_url) {

        $content.= "<p class='ow-single-post-link ow-".$link_color."-link'>" . $button_txt . "</p>";

    }

}

$content.= "</div>";

$content.= '</a>';

$content.= "</div>";



return $content;



}



/* plan dogodka */

function ow_plan_dogodka($content = '')

{

$index = 0;



$plan_field = get_field('plan_dogodka');



$max = sizeof($plan_field);



if (have_rows('plan_dogodka')):



    $content .= '<div class="ow-plan-dogodka-wrap">';



    while (have_rows('plan_dogodka')): the_row();

        $index++;



        $title = get_sub_field('naslov');

        $text = get_sub_field('opis');



        $content .= '<div class="ow-plan-single ow-text slideanim slide-' . $index . '">';

        $content .= '<div class="ow-number">' . $index . '</div>';

        $content .= '<h3>' . $title . '</h3>';

        $content .= '<p>' . $text . '</p>';

        $content .= '</div>';



    endwhile;



    $content .= '<div class="mt-30 ow-button-outer-wrap slideanim slide-' . $index . '">';

    $content .= do_shortcode("[av_button label='Kontaktirajte nas' link='#kontaktirajte-nas' color='purple']");

    $content .= '</div>';



    $content .= '</div>';

endif;



return $content;

}



add_shortcode('ow-plan-dogodka', 'ow_plan_dogodka');





/* galerija pretekli dogodki */

function ow_pretekli_galerija($content = '')

{

$now = date('Y-m-d');



$args_search_qqq = array(

    'post_type' => array('product'),

    'post_status' => 'publish',

    'post_parent' => 0,

    'posts_per_page' => 8,

    'order' => 'DESC',

    'orderby' => 'meta_value',

    'meta_key' => 'ow_event_start_date',

    'suppress_filters' => false,

    'meta_query' => array(

        array(

            'key' => 'ow_event_start_date',

            'value' => $now,

            'compare' => '<',

            'type' => 'DATE',

        )

    ),

);



$old_events = new WP_Query($args_search_qqq);



if ($old_events->have_posts()) :



    $content .= '<div class="ow-old-events-wrap">';



    while ($old_events->have_posts()) : $old_events->the_post();



        $image = get_the_post_thumbnail_url();

        $title = get_the_title();



        $content .= '<div class="ow-old-events-single">';



        $content .= '<div class="ow-old-events-inner">';



        if ($image) {

            $content .= '<img src="' . $image . '">';

        }



        $content .= '<div class="ow-overlay">';

        $content .= '<h3 class="ow-text-white ow-center">' . $title . '</h3>';

        $content .= '</div>';



        $content .= '</div>';



        $content .= '</div>';



    endwhile;



    $content .= '</div>';



endif;



wp_reset_postdata();



return $content;

}



add_shortcode('ow-pretekli-dogodki-galerija', 'ow_pretekli_galerija');





/* popup kontaktirajte nas */

function ow_kontakt_popup($atts, $content = '')

{

$defaults = array(

    "podrocje" => "",

);



$params = shortcode_atts($defaults, $atts);

$contact_area = $params['podrocje'];



$content .= '<div id="open-popup" class="popup-container">

    <div id="close-popup"><img src="' . get_home_url() . '/wp-content/themes/enfold-child/images/close.svg"></div>

    <div class="popup-wrapper">

        <div class="popup-bg">

            <div class="popup-content">' . do_shortcode("[gravityform id='4' title='true' description='true' field_values='podrocje=" . $contact_area . "']") . '

            </div>

        </div>

    </div>

</div>';



return $content;

}



add_shortcode('ow_kontakt_popup', 'ow_kontakt_popup');





/* dynamically populated  submenu */

function ow_submenu($atts, $content = '')

{

$defaults = array(

    "parent" => "",

    "parent_text" => "Vse"

);



$params = shortcode_atts($defaults, $atts);

$parent_term = $params['parent'];

$parent_name = $params['parent_text'];



$taxonomy = 'product_cat';



$cat_parent_obj = get_term_by('slug', $parent_term, $taxonomy);

$cat_id = $cat_parent_obj->term_id;



$terms_ids = array($cat_id);

$child_terms_ids = get_term_children($cat_id, $taxonomy);



$all_terms = array_merge($terms_ids, $child_terms_ids);



$content .= '<ul class="sub-menu">';

foreach ($all_terms as $term_id) {

    $term = get_term_by('term_id', $term_id, $taxonomy);

    $name = $term->name;

    $url = get_home_url() . '/' . $parent_term . '/?kategorija=' . $term->slug;



    if ($term_id === $cat_id) {

        $name = $parent_name;

        $url = get_home_url() . '/' . $parent_term;

    }



    $content .= '<li class="menu-item">';

    $content .= '<a href="' . $url . '">';

    $content .= '<span class="avia-bullet"></span>';

    $content .= '<span class="avia-menu-text">' . $name . '</span>';

    $content .= '</a>';

    $content .= '</li>';

}



$content .= '</ul>';





return $content;

}



add_shortcode('ow-custom-submenu', 'ow_submenu');





/* pdf magazine */

function ow_pdf_magazine($content = '')

{



$file = get_field('pdf_izvod_revije');



if ($file && !empty($file)) {

    $content .= '<div class="ow-pdf-wrap">';



    $content .= '<a href="' . $file . '" target="_blank">';

    $content .= '<img src="' . get_stylesheet_directory_uri() . '/images/search.svg">';

    $content .= 'OGLEJTE SI PDF IZVOD';

    $content .= '</a>';



    $content .= '</div>';



}



return $content;

}



add_shortcode('ow-pdf-izvod', 'ow_pdf_magazine');









/* magazine release dates */

function ow_magazine_dates($content = '')

{



if (have_rows('datumi_izida')):



    $content .= '<div class="ow-magazine-dates-wrap">';



    while (have_rows('datumi_izida')): the_row();



        $date = get_sub_field('datum');

        $info = get_sub_field('info_tekst');



        if ($date) {

            $content .= '<div class="ow-magazine-date">';



            $content .= '<p><b>' . $date . '</b>';



            if ($info && !empty($info)) {

                $content .= "<span class='ow-info-icon-wrap ow-tooltip'><span class='ow-info-icon'>i</span></span>";

                $content .= "<span class='ow-info-text'>";

                $content .= $info;

                $content .= "</span>";



                $content .= "<span class='ow-info-text-arrow'></span>";

            }



            $content .= '</p>';



            $content .= '</div>';

        }



    endwhile;



    $content .= '</div>';



endif;



return $content;

}



add_shortcode('ow-datumi-izida', 'ow_magazine_dates');





/* category show more */

function ow_show_more($atts, $content = '')

{

$defaults = array(

    "prikazi" => "PRIKAŽI VEČ OPISA",

    "skrij" => "PRIKAŽI MANJ OPISA"

);



$params = shortcode_atts($defaults, $atts);

$show = $params['prikazi'];

$hide = $params['skrij'];



$content .= '<a id="prikazi-vec" data-show="' . $show . '" data-hide="' . $hide . '">' . $show . '</a>';



return $content;

}



add_shortcode('ow-show-more', 'ow_show_more');




function list_order_information($order) {




    echo '<table style="width:100%; text-align:left; border-collapse: collapse; color:#636363;">';

    if(get_post_meta($order->get_id(), '_applicant_first_name', true)) {

        echo '<tr>';
    
        echo '<th style="color:rebeccapurple; border:0px solid black!important; padding-left:0px; font-size:18px;"><strong>Prijavitelj</strong></th>';
    
        echo '</tr>';
    
    
    
        echo '<tr>';
    
        echo '<th style="border:solid 2px #e5e5e5; font-weight: inherit"><strong>' . __('Ime') . '</strong></th>';
    
        echo '<th style="border:solid 2px #e5e5e5; font-weight: inherit">' . get_post_meta($order->get_id(), '_applicant_first_name', true) . '</th>';
    
        echo '</tr>';
        
        echo '<tr>';
    
        echo '<th style="border:solid 2px #e5e5e5; font-weight: inherit"><strong>' . __('Priimek') . '</strong></th>';
    
        echo '<th style="border:solid 2px #e5e5e5; font-weight: inherit">' . get_post_meta($order->get_id(), '_applicant_last_name', true) . '</th>';
    
        echo '</tr>';
    
    
    
        echo '<tr>';
    
        echo '<th style="border:solid 2px #e5e5e5; font-weight: inherit"><strong>' . __('Delovno mesto') . '</strong></th>';
    
        echo '<th style="border:solid 2px #e5e5e5; font-weight: inherit">' . get_post_meta($order->get_id(), '_applicant_company', true) . '</th>';
    
        echo '</tr>';
    
    
    
        echo '<tr>';
    
        echo '<th style="border:solid 2px #e5e5e5; font-weight: inherit"><strong>' . __('E-pošta') . '</strong></th>';
    
        echo '<th style="border:solid 2px #e5e5e5; font-weight: inherit">' . get_post_meta($order->get_id(), '_applicant_email', true) . '</th>';
    
        echo '</tr>';
    
    
    
        echo '<tr>';
    
        echo '<th style="border:solid 2px #e5e5e5; font-weight: inherit"><strong>' . __('Telefon') . '</strong></th>';
    
        echo '<th style="border:solid 2px #e5e5e5; font-weight: inherit">' . get_post_meta($order->get_id(), '_applicant_phone', true) . '</th>';
    
        echo '</tr>';
    
    }



    echo '<tr>';

    echo '<th style="color:rebeccapurple; border:0px solid black!important; padding-left:0px; font-size:18px;"><strong>Udeleženci</strong></th>';

    echo '</tr>';


    for ($i = 1; $i < 999; $i++) {
        if (get_post_meta($order->get_id(), '_additional_first_name_' . $i, true)) {

            //if (strpos($data_key, '_additional_first_name_') > -1) {
    
                //$i++;
    
    
    
                //echo '<tr>';
    
                //echo '<th style="color:rebeccapurple; border:0px solid black!important; padding-left:0px; font-size:18px;"><strong>Dodatna oseba ' . $i . ':</strong></th>';
    
                //echo '</tr>';
    
    
    
    
    
                echo '<tr>';
    
                echo '<th style="border:solid 2px #e5e5e5; font-weight: inherit"><strong>' . __('Ime in priimek') . '</strong></th>';
    
                echo '<th style="border:solid 2px #e5e5e5; font-weight: inherit">' . get_post_meta($order->get_id(), '_additional_first_name_' . $i, true) . ' ' . get_post_meta($order->get_id(), '_additional_last_name_' . $i, true) . '</th>';
    
                echo '</tr>';
    
    
    
    
    
            //}
    
    
    
            //if (strpos($data_key, '_additional_company_') > -1) {
    
                echo '<tr>';
    
                echo '<th style="border:solid 2px #e5e5e5; font-weight: inherit"><strong>' . __('Delovno mesto') . '</strong></th>';
    
                echo '<th style="border:solid 2px #e5e5e5; font-weight: inherit">' . get_post_meta($order->get_id(), '_additional_company_' . $i, true) . '</th>';
    
                echo '</tr>';
    
            //}
    
    
    
           // if (strpos($data_key, '_additional_email_') > -1) {
    
                echo '<tr>';
    
                echo '<th style="border:solid 2px #e5e5e5; font-weight: inherit"><strong>' . __('E-pošta') . '</strong></th>';
    
                echo '<th style="border:solid 2px #e5e5e5; font-weight: inherit">' . get_post_meta($order->get_id(), '_additional_email_' . $i, true) . '</th>';
    
                echo '</tr>';
    
          //  }
    
    
    
          //  if (strpos($data_key, '_additional_phone_') > -1) {
    
                echo '<tr>';
    
                echo '<th style="border:solid 2px #e5e5e5; font-weight: inherit"><strong>' . __('Telefonska številka') . '</strong></th>';
    
                echo '<th style="border:solid 2px #e5e5e5; font-weight: inherit">' . get_post_meta($order->get_id(), '_additional_phone_' . $i, true) . '</th>';
    
                echo '</tr>';
    
          //  }

        }
    }



    echo '<tr>';

    echo '<th style="color:rebeccapurple; border:0px solid black!important; padding-left:0px; font-size:18px;"><strong>Naslov za plačilo</strong></th>';

    echo '</tr>';



    if (get_post_meta($order->get_id(), '_billing_on_company', true) == true) {

        $payment_on_company = "DA";

    } else {

        $payment_on_company = "NE";

    }



    echo '<tr>';

    echo '<th style="border:solid 2px #e5e5e5; font-weight: inherit"><strong>' . __('Plačilo na podjetje') . '</strong></th>';

    echo '<th style="border:solid 2px #e5e5e5; font-weight: inherit">' . $payment_on_company . '</th>';

    echo '</tr>';



    if ($payment_on_company == "DA") {



        echo '<tr>';

        echo '<th style="border:solid 2px #e5e5e5; font-weight: inherit"><strong>' . __('Davčna številka') . '</strong></th>';

        echo '<th style="border:solid 2px #e5e5e5; font-weight: inherit">' . get_post_meta($order->get_id(), '_billing_vat', true) . '</th>';

        echo '</tr>';



        echo '<tr>';

        echo '<th style="border:solid 2px #e5e5e5; font-weight: inherit"><strong>' . __('Podjetje') . '</strong></th>';

        echo '<th style="border:solid 2px #e5e5e5; font-weight: inherit">' . get_post_meta($order->get_id(), '_billing_company_address', true) . '</th>';

        echo '</tr>';



    }


    echo '<tr>';

    echo '<th style="border:solid 2px #e5e5e5; font-weight: inherit"><strong>' . __('Ime in priimek') . '</strong></th>';

    echo '<th style="border:solid 2px #e5e5e5; font-weight: inherit">' . $order->get_billing_first_name() . ' ' . $order->get_billing_last_name() . '</th>';

    echo '</tr>';
    
    echo '<tr>';

    echo '<th style="border:solid 2px #e5e5e5; font-weight: inherit"><strong>' . __('Naslov') . '</strong></th>';

    echo '<th style="border:solid 2px #e5e5e5; font-weight: inherit">' . get_post_meta($order->get_id(), '_billing_address_1', true) . '</th>';

    echo '</tr>';

    echo '<tr>';

    echo '<th style="border:solid 2px #e5e5e5; font-weight: inherit"><strong>' . __('Poštna št.') . '</strong></th>';

    echo '<th style="border:solid 2px #e5e5e5; font-weight: inherit">' . get_post_meta($order->get_id(), '_billing_postcode', true) . '</th>';

    echo '</tr>';

    echo '<tr>';

    echo '<th style="border:solid 2px #e5e5e5; font-weight: inherit"><strong>' . __('Mesto') . '</strong></th>';

    echo '<th style="border:solid 2px #e5e5e5; font-weight: inherit">' . get_post_meta($order->get_id(), '_billing_city', true) . '</th>';

    echo '</tr>';



    echo '<tr>';

    echo '<th style="color:rebeccapurple; border:0px solid black!important; padding-left:0px; font-size:18px;"><strong>Opombe</strong></th>';

    echo '</tr>';



    echo '<tr>';

    echo '<th style="border:solid 2px #e5e5e5; font-weight: inherit"><strong>' . __('Opombe') . '</strong></th>';

    echo '<th style="border:solid 2px #e5e5e5; font-weight: inherit">' . $order->get_customer_note() . '</th>';

    echo '</tr>';



    echo '<tr>';

    echo '<th style="color:rebeccapurple; border:0px solid black!important; padding-left:0px; font-size:18px;"><strong>Način plačila</strong></th>';

    echo '</tr>';



    echo '<tr>';

    echo '<th style="border:solid 2px #e5e5e5; font-weight: inherit"><strong>' . __('Način plačila') . '</strong></th>';

    echo '<th style="border:solid 2px #e5e5e5; font-weight: inherit">' . get_post_meta($order->get_id(), '_payment_method_title', true) . '</th>';

    echo '</tr>';



   /* echo '<tr>';

    echo '<th style="color:rebeccapurple; border:0px solid black!important; padding-left:0px; font-size:18px;"><strong>Soglasje za prejemanje novic</strong></th>';

    echo '</tr>';



    if (get_post_meta($order->get_id(), '_privacy-policy-checkbox', true) == true) {

        $agreement = "DA";

    } else {

        $agreement = "NE";

    }



    echo '<tr>';

    echo '<th style="border:solid 2px #e5e5e5; font-weight: inherit"><strong>' . __('Soglasje') . '</strong></th>';

    echo '<th style="border:solid 2px #e5e5e5; font-weight: inherit">' . $agreement . '</th>';

    echo '</tr>';*/



    echo '</table>';

	echo '<table style="width:100%; text-align:left; border-collapse: collapse; color: #636363; margin-bottom:20px;">';

	echo '<tr>';

    echo '<th style="color:rebeccapurple; border:0px solid black!important; padding-left:0px; font-size:18px;"><strong>Naslov plačnika</strong></th>';

    echo '</tr>';

	if (get_post_meta($order->get_id(), '_billing_on_company', true) == true) {

		echo '<tr>';

        echo '<th style="border:solid 2px #e5e5e5; font-weight: inherit">' . get_post_meta($order->get_id(), '_billing_company_address', true) . '</th>';

        echo '</tr>';

		if (get_post_meta($order->get_id(), '_billing_vat', true) == true) {

        echo '<tr>';

        echo '<th style="border:solid 2px #e5e5e5; font-weight: inherit">' . get_post_meta($order->get_id(), '_billing_vat', true) . '</th>';

        echo '</tr>';

		}

    } else {

		echo '<tr>';

		echo '<th style="border:solid 2px #e5e5e5; font-weight: inherit">' . $order->get_billing_first_name() . ' ' . $order->get_billing_last_name() . '</th>';

		echo '</tr>';

		if (get_post_meta($order->get_id(), '_billing_company', true) == true) {

		echo '<tr>';

		echo '<th style="border:solid 2px #e5e5e5; font-weight: inherit">' . get_post_meta($order->get_id(), '_billing_company', true) . '</th>';

		echo '</tr>';

		}
    }

    echo '<tr>';

    echo '<th style="border:solid 2px #e5e5e5; font-weight: inherit">' . get_post_meta($order->get_id(), '_billing_address_1', true) . '</th>';

    echo '</tr>';

    echo '<tr>';

    echo '<th style="border:solid 2px #e5e5e5; font-weight: inherit">' . get_post_meta($order->get_id(), '_billing_postcode', true) . '</th>';

    echo '</tr>';

    echo '<tr>';

    echo '<th style="border:solid 2px #e5e5e5; font-weight: inherit">' . get_post_meta($order->get_id(), '_billing_city', true) . '</th>';

    echo '</tr>';
    
    echo '<tr>';

    echo '<th style="border:solid 2px #e5e5e5; font-weight: inherit">' . $order->get_billing_country() . '</th>';

    echo '</tr>';

    echo '<tr>';

    echo '<th style="border:solid 2px #e5e5e5; font-weight: inherit">' . get_post_meta($order->get_id(), '_billing_phone', true) . '</th>';

    echo '</tr>';

    echo '<tr>';

    echo '<th style="border:solid 2px #e5e5e5; font-weight: inherit">' . get_post_meta($order->get_id(), '_billing_email', true) . '</th>';

    echo '</tr>';

	echo '</table>';



}

function menu_sites() {
	
	$subsites = get_sites(['status' => 'public']);
	
	if ( ! empty ( $subsites ) ) {
		ob_start();
		echo '<div class="subsites-container">';
		
			echo '<ul class="subsites">';
	
			foreach( $subsites as $subsite ) {
			
				$subsite_id = get_object_vars( $subsite )["blog_id"];
				$subsite_name = get_blog_details( $subsite_id )->blogname;
				if(stripos($subsite_name, 'template') !== false)
				    continue;
				$subsite_link = get_blog_details( $subsite_id )->siteurl;
				echo '<li class="site-' . $subsite_id . '"><a href="' . $subsite_link . '">' . $subsite_name . '</a></li>';
		
			}
			
			echo '</ul>';
			
		echo '</div>';
		
		$content = ob_get_clean();
		
		add_action('wp_footer', function() {
		    ?>
		    <style>
		        ul.subsites {
		            display: flex !important;
		            flex-wrap: wrap;
		            margin-left: 0 !important;
		        }
		        ul.subsites li {
		            border: 1px solid #636363;
		            border-radius: 4px;
		            padding: 2px 5px;
		            margin: 4px;
		            list-style: none !important;
		        }
		        ul.subsites li a {
		            color: #636363 !important;
		            font-size: 12px !important;
		        }
		    </style>
		    <?php
		});
		
		return $content;
	}
	
}
add_shortcode( 'menu-sites', 'menu_sites' );

function homepage_buttons() {
    $pid = get_field('homepage_product');
    $site_id = get_field('ow_konferenca_site', $pid);
    $permalink1 = get_blog_details( $site_id )->siteurl.'/prijava';
    $permalink2 = get_blog_details( $site_id )->siteurl.'/program';
    
    $sc1 = "[av_button label='PRIJAVA' link='".$permalink1."' link_target='' color='pink' av_uid='av-1mjtj' custom_class=''color-red admin_preview_bg=''][/av_button]";
    $sc2 = "[av_button label='PROGRAM' link='".$permalink2."' link_target='' color='' av_uid='av-3bbf' custom_class='' admin_preview_bg=''][/av_button]";
    ob_start();
    echo do_shortcode($sc1);
    echo do_shortcode($sc2);
    return ob_get_clean();
}
add_shortcode('homepage-buttons', 'homepage_buttons');