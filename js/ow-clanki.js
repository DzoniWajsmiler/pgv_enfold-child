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