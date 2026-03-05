jQuery( document ).ready( function( $ ) {

    $("img").hover(function(){
        $(this).attr("rel", $(this).attr("title"));
        $(this).removeAttr("title");
    }, function(){
        $(this).attr("title", $(this).attr("rel"));
        $(this).removeAttr("rel");
    });

        //main menu - center submenu under parent menu item
        $('#avia-menu .menu-item-has-children a').on('hover', function(){

            //skip if submenu is already opened
            if($(this).hasClass('open-mega-a')){
                return;
            }

            //don't center "revije" submenu
            if($(this).parent('.menu-item-has-children').hasClass('revije-right')){
                return;
            }

            var submenu = $(this).siblings(".avia_mega_div");
            var submenu_width = parseInt(submenu.css("width"));
            var parent_width = parseInt(submenu.parent('.menu-item-has-children').css("width"));
            var move_left = parent_width / 2 - submenu_width / 2;

            submenu.css("left", move_left);

        });


        //homepage slider
        $('.ow-home-slider').slick({
            slidesToShow: 1,
            dots: false,
            speed: 500,
            infinite:true,
            arrows: true,
            fade: true,
            cssEase: 'ease-in-out',
            lazyLoad: 'ondemand',
            nextArrow: '<button type="button" class="ow-slick-next">Next</button>',
            prevArrow: '<button type="button" class="ow-slick-prev">Previous</button>',
        });


        //blockquote effect
        function textillate_quote(){
            $('.ow-quote:not(.ow-quote-no-effect)').each(function() {
                if ($(this).visible()) {
                    $(this).children('blockquote').textillate({
                        in: {
                            effect: 'fadeInRight',
                            delayScale: 1,
                            delay: 20,
                        },
                        type: 'char',
                        out: {
                            effect: 'fadeOutRight',
                            delayScale: 1.5,
                            delay: 50,
                            sync: true
                        },
                    });
                }
            });
        }

       textillate_quote();

        $(window).scroll(function() {
            textillate_quote();
        });

    //reviews
    $('.ow-reviews-slider').slick({
        slidesToShow: 1,
        initialSlide:0,
        dots: false,
        speed: 500,
        infinite:true,
        arrows: false,
        fade: true,
        asNavFor: '.ow-reviews-slider-nav',
        swipe:false,
        draggable:false,
        responsive: [
            {
                breakpoint: 920,
                settings: {
                    adaptiveHeight: true
                }
            }
        ]
    });

    $('.ow-reviews-slider-nav').slick({
        slidesToShow: 10,
        asNavFor: '.ow-reviews-slider',
        dots: false,
        centerMode: false,
        focusOnSelect: true,
        swipe:false,
        draggable:false
    });

    $('.ow-reviews-slider-nav .slick-slide').mouseover(function(){
        $(this).click();
    });

    /* gfield error */
    $(".gfield_error").on("input", function(){
        $(this).addClass("remove-error");
    });
    $(".gform_footer input[type='submit']").on("click",function(){
        $(".gfield_error").each(function(){
            $(this).removeClass("remove-error");
        });
    });

    /* gfield &checkout labels */
    var input_fields = ".gform_wrapper input[type='text'], .gform_wrapper textarea, form.woocommerce-checkout input[type='text'], form.woocommerce-checkout textarea, form.woocommerce-checkout input[type='email'], form.woocommerce-checkout input[type='tel']";

    $(input_fields).each(function(){
        if($(this).val().length > 0){
            $(this).parent().siblings("label").addClass("small-label");
        }
    });

    $('body').on("input click focus", input_fields, function(){
        $(this).parent().siblings("label").addClass("small-label");
       /* $(this).parent().find(".description").css("display", "unset");*/
    });

    $('body').on("focusout", input_fields, function(){
        if($(this).val().length < 1) {
            $(this).parent().siblings("label").removeClass("small-label");
        }
    });

    /* gfield & checkout optional fields */
    $(".gform_wrapper input[type='text']:not([aria-required='true']), form.woocommerce-checkout .form-row:not(.validate-required) input[type='text'], form.woocommerce-checkout .form-row:not(.validate-required) input[type='tel'], form.woocommerce-checkout .form-row:not(.validate-required) input[type='email']").each(function () {
        $(this).after("<span class='ow-optional'>Opcijsko</span>");
    });


    /* gfield & checkout info */
    $(".ow-info .ginput_container, .woocommerce-checkout .ow-info input").each(function () {
        $(this).after("<span class='ow-info-icon-wrap'><span class='ow-info-icon'>i</span></span>");
    });

    $("body").on("click", ".gform_wrapper .ow-info-icon-wrap", function(){
        $(this).siblings(".gfield_description").toggleClass("active");
    });


    $(".woocommerce-input-wrapper .ow-info-icon-wrap").on("click", function(){
        $(this).siblings(".description").toggleClass("active");
    });


    /* gfield button */
    var button_efect_html ='<span class="button__container"><span class="circle top-left" style="transform: matrix(0.7071, -0.7071, 0.7071, 0.7071, 0, 0);"></span><span class="circle top-left" style="transform: matrix(0.7071, -0.7071, 0.7071, 0.7071, 0, 0);"></span><span class="circle top-left" style="transform: matrix(0.7071, -0.7071, 0.7071, 0.7071, 0, 0);"></span><span class="button__bg"></span><span class="circle bottom-right" style="transform: matrix(0.7071, -0.7071, 0.7071, 0.7071, 0, 0);"></span><span class="circle bottom-right" style="transform: matrix(0.7071, -0.7071, 0.7071, 0.7071, 0, 0);"></span><span class="circle bottom-right" style="transform: matrix(0.7071, -0.7071, 0.7071, 0.7071, 0, 0);"></span></span>';
    $(".gform_wrapper .gform_footer").each(function(){
        var color = "purple";
        var forms_with_pink_button = ["gform_2", "gform_4"]; //add form id attribute for pink button

        if(forms_with_pink_button.indexOf($(this).parent("form").attr('id')) > -1){
            color = "pink";
        }
        $(this).addClass("custom_button button--1 color-"+color);
        $(this).children(".gform_button").after(button_efect_html);
    });

    function openpopup() {
        if($('#open-popup').length > 0) {
            $('#top').addClass('opened-popup');
            $('#open-popup').fadeIn(500);

            centerpopup();

            $(window).resize(function () {
                centerpopup();
            });
        }
    }

    function centerpopup(){
        var popup_height = $('#open-popup .popup-wrapper').outerHeight( true );
        var viewport_height = $( window ).height();

        var top = 0;
        var close_top = 30;

        if(popup_height <= viewport_height ){
            top = (viewport_height - popup_height) / 2;
        }

        $('#open-popup .popup-wrapper').css("top", top + "px");

        if(window.matchMedia('(max-width: 580px)').matches) {
            var close_top = top + 10 + 70; //top (10) + wrapper margin (70)
        }

        $("#close-popup").css("top", close_top + "px");
    }

    /* sponzorstvo popup */
    if($("#zelim-sponzorirati").length < 1) {
        if (window.location.href.split('#')[1] === "gf_2" ||
            window.location.href.split('#')[1] === "zelim-sponzorirati") {
            openpopup();
        }
        $('a[href="#zelim-sponzorirati"]').click(function () {
            openpopup();
        });
    }

    /* kontaktirajte nas popup */
    if($("#kontaktirajte-nas").length < 1) {
        if (window.location.href.split('#')[1] === "gf_4" ||
            window.location.href.split('#')[1] === "kontaktirajte-nas") {
            openpopup();
        }
        $('a[href="#kontaktirajte-nas"]').click(function () {
            openpopup();
        });
    }


    /* close popup */
    $('#close-popup').click(function(e) {
        $('#open-popup').fadeOut(500);

        setTimeout(function () {
            $('#top').removeClass('opened-popup');
        }, 500);

        var url = location.href.replace(location.hash,"#");
        window.history.replaceState(null, null, url);

    });


    /* nagrade sliders */
    $('.ow-nagrade-slider-img').slick({
        slidesToShow: 1,
        initialSlide:0,
        asNavFor: '.ow-nagrade-slider-text',
        dots: false,
        arrows:false,
        centerMode: false,
        focusOnSelect: true,
        swipe:false,
        draggable:false,
        fade:true,
        lazyLoad:'ondemand',
        speed: 500,

    });


    var show_all_slides = $('.ow-nagrade-slider-text .ow-nagrade-single-text').length;


    function scrollToAnchor(id){
        var anchor = $("#"+id);
        $('html,body').animate({scrollTop: anchor.offset().top},'slow');
    }


    $('.ow-nagrade-slider-text .ow-nagrade-single-text').on("click",function(){
        if($("#nagrade-anchor").length > 0) {
            scrollToAnchor('nagrade-anchor');
        }
    });

    $('.ow-nagrade-slider-text').slick({
        slidesToShow: show_all_slides,
        initialSlide:0,
        dots: false,
        arrows: false,
        asNavFor: '.ow-nagrade-slider-img',
        vertical:true,
        focusOnSelect: true,
    });


    /* predavatelji slider */
    $(".ow-predavatelji-slider").slick({
        slidesToShow: 3,
        slidesToScroll:3,
        dots: true,
        speed: 500,
        infinite: false,
        arrows: false,
        fade: false,
        lazyLoad: 'ondemand',
        responsive:[
            {
                breakpoint: 1024,
                    settings: {
                    slidesToShow: 2,
                    slidesToScroll: 2
                },
            },
            {
                breakpoint: 767,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1
                },
            },
        ]
    });


    /* sticky magazine cover */
    $(".ow-sticky-parent").ready(function() {
        if ($(".ow-sticky-parent").length > 0 && $(".ow-sticky-col").length > 0) {

            if(window.matchMedia('(min-width: 767px)').matches) {
                ow_sticky_col_calc();
            }

            $(window).resize(function () {
                if(window.matchMedia('(min-width: 767px)').matches){
                    ow_sticky_col_calc();
                } else {
                    //remove sticky
                    console.log("remove sticky");
                    $(".ow-sticky-col").css("position", "static");
                }
            });

            function ow_sticky_col_calc() {
                var sticky_el = $(".ow-sticky-col");
                var sticky_parent = $(".ow-sticky-parent .entry-content-wrapper");

                var sticky_top = sticky_el.offset().top;
                var sticky_height = sticky_el.outerHeight(true);

                var sticky_parent_height = sticky_parent.outerHeight(true);
                var sticky_parent_bottom = sticky_parent.offset().top + sticky_parent_height;

                ow_sticky_col(sticky_el, sticky_top, sticky_height, sticky_parent_bottom);

                $(window).scroll(function () {
                    if(window.matchMedia('(min-width: 767px)').matches) {
                        ow_sticky_col(sticky_el, sticky_top, sticky_height, sticky_parent_bottom);
                    }
                });
            }

            function ow_sticky_col(sticky_el, sticky_top, sticky_height, sticky_parent_bottom) {

                if (window.pageYOffset >= sticky_top) {

                    if (window.pageYOffset >= (sticky_parent_bottom - sticky_height)) {
                        sticky_el.css('top', (sticky_parent_bottom - sticky_height) + 'px');
                        sticky_el.css("position", "sticky");
                    } else {
                        sticky_el.css("position", "fixed");
                        sticky_el.css('top', 0);
                    }
                } else {
                    sticky_el.css("position", "static");
                }
            }
        }
    });


    /* category show more*/
    $("#prikazi-vec").on("click",function(e){
        e.preventDefault();
        var new_text = false;

        var more_text_to_show = $(".prikazi-vec");

        more_text_to_show.toggleClass("active");
        more_text_to_show.slideToggle();

        if( more_text_to_show.hasClass("active")){
            new_text = $(this).data("hide");
        } else {
            new_text = $(this).data("show");
        }

        if(new_text){
            $(this).html(new_text);
        }
    });

    /* naroči revijo*/
    $("a[href='#naroci-revijo']").on("click",function(e){
        e.preventDefault();

        $([document.documentElement, document.body]).animate({
            scrollTop: ($(".naroci-revijo-anchor").offset().top - 40)
        }, 1000);

    });


    /* events slider */
    $(".ow-events-slider .ow-cat-grid-wrap").slick({
        slidesToShow: 2,
        slidesToScroll:2,
        dots: true,
        speed: 500,
        infinite: false,
        arrows: false,
        fade: false,
        adaptiveHeight:false,
        responsive:[
            {
                breakpoint: 767,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1
                },
            },
        ]
    });

    /* set same height for all slides */
    function setSameHeightForSlides() {
        var trackHeight = $('.ow-events-slider .ow-cat-grid-wrap .slick-track').height();
        $('.ow-events-slider .ow-cat-grid-wrap .ow-single-event').css('height', trackHeight + 'px');
    }

    setSameHeightForSlides();

    $(window).resize(function(){
        setSameHeightForSlides();
    });


    /* checkout */
    /* conditionally show fields */
    $('#billing_on_company_field input').each(function(){
        checkout_show_company_fields(this.checked);
    });

    $('#billing_on_company_field input').change(function(){
        checkout_show_company_fields(this.checked);
    });

    function checkout_show_company_fields(checked){
        if (checked) {
            $('#billing_vat_field').slideDown();
            $('#billing_company_address_field').slideDown();
        } else {
            $('#billing_vat_field').slideUp();
            $('#billing_company_address_field').slideUp();
        }
    }


    $(".ow-single-event .ow-sublink").click(function(e) {
        e.preventDefault();

        var anchor = $(this).data("anchor");
        var link = $(this).data("link");
        var site = $(this).data("site");
        var isExternal = $(this).data("external");

        if(isExternal){
            location.href = link;
            return;
        }

        var addition, subpage = '';

         if(site === "1"){
             if(anchor === "termini"){
                 subpage = "prijava"; //page slug za prijavo na ločeni strani konference
             }
             addition = "/" + subpage;
         } else {
             addition = "#" + anchor;
         }

        location.href = link + addition;
    });

    $(".splosni_pogoji_link").on("click", function(e){
        e.preventDefault();
        var home = window.location.origin;
        var page_slug = "splosni-pogoji";

        location.href = home + "/" + page_slug;
    });


    /* move coupon form inside checkout form */
    var coupon = $(".ow-move-coupon-wrapper");
    coupon.insertBefore('#order_review');

    console.log(coupon);

    $(".btn-coupon").on("click", function(){
        coupon.children(".woocommerce-info").remove();
        coupon.children(".woocommerce-success").remove();
    });

});