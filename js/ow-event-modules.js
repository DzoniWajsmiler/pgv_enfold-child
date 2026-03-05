jQuery(document).ready(function ($) {

    var getUrlParameter = function getUrlParameter(sParam) {
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

    var product_param = getUrlParameter('product_id'), date_element = $(".ow-date-button[data-productid='"+ product_param + "']").attr('id'), date_element_id = "";



    if (typeof date_element == 'undefined') {
        date_element_id = "#ow-date-button_0";
    } else {
        date_element_id = "#" + date_element;
    }

    var array_ids = ['#srecanje_1', '#ow-variable-0', date_element_id];

    $.each(array_ids, function(index, val) {
        setTimeout(function() {
            $(val).trigger('click');
            $(val).addClass('active');
        }, 10);
    });

    $(window).resize(function(){
         var el = $(".ow_event_timetable.active");
         if(el.length > 0 ) {
             var el_left = el.offset().left + (el.width() / 2) - $(".ow-dates-module").offset().left - 10;
             $("#ow_event_timetable_arrow").css("left", el_left);
         }
    });

    $(".ow_event_timetable").click(function () {
        var date = $(this).data("date");
        var product_id = $(this).data("product");

        $(".ow_event_timetable").removeClass("active");
        $(this).addClass("active");

        var el_left = $(this).offset().left + ($(this).width() / 2) - $(".ow-dates-module").offset().left - 10;

        $("#ow_event_timetable_arrow").css("left", el_left);

        data = {
            'action': 'get_event_timetable',
            'product_id': product_id,
            'date': date
        };

        $.ajax({
            url: avia_framework_globals.ajaxurl,
            data: data,
            type: 'POST',
            beforeSend : function ( xhr ) {
                 $('#ow_event_timetable_details').addClass( 'ow-loader-active ow-light-white ow-module-loader' );
            },
            success: function (data) {
                if (data) {
                    $('#ow_event_timetable_details').html(data.timetable);
                }
            },
            error: function(xhr, status, error) {
                var err = eval("(" + xhr.responseText + ")");
                console.log(err.Message);
            },
            complete : function ( xhr ) {
                $('#ow_event_timetable_details').removeClass( 'ow-loader-active ow-light-white ow-module-loader' );
            },
        });
    });

    $(".ow-event-register-name").click(function () {

        if($("#ow-product-variations").length > 0) {
            if($("#ow-product-variations").hasClass("tip-prijave")){
                //for variation tip-prijave, use simple product function
                return;
            }
        }

        var product_id = $(this).data("productid");
        var variation_id = $(this).data("variationid");

        $(".ow-event-register-name").removeClass("active");
        $(this).addClass("active");

        data = {
            'action': 'get_event_variables',
            'product_id': product_id,
            'variation_id': variation_id,
        };

        console.log("loading variable event with modules");

        $.ajax({
            url: avia_framework_globals.ajaxurl,
            data: data,
            type: 'POST',
            beforeSend : function ( xhr ) {
                $('#ow-event-details').addClass( 'ow-loader-active ow-module-loader' );
            },
            success: function (data) {
                if (data) {
                    $('#ow-event-details').html(data.event_data);
                }
                initBt()
            },
            complete : function ( xhr ) {
                $('#ow-event-details').removeClass( 'ow-loader-active ow-module-loader' );
            },
        });
    });

    $(".ow-date-button").click(function() {
        var variations = false;
        if($(this).hasClass("variable-data")){
            if($("#ow-product-variations").length > 0) {
                if($("#ow-product-variations").hasClass("akademija-moduli")){
                    return;
                }
                if($("#ow-product-variations").hasClass("tip-prijave")){
                    variations = true;
                }
            }
        }

        var product_id = $(this).data('productid');

        $(".ow-date-button").removeClass("active");
        $(this).addClass("active");

        data = {
            'action': 'get_event_simple',
            'product_id': product_id,
            'variations': variations
        };

        console.log("loading simple event");

        $.ajax({
            url: avia_framework_globals.ajaxurl,
            data: data,
            type: 'POST',
            beforeSend : function ( xhr ) {
                $('.ow-register-product-wrap').addClass( 'ow-loader-active ow-module-loader' );
            },
            success: function (data) {
                if (data) {
                    $('.ow-register-product-wrap').html(data.product);
                }
                initBt()
            },
            complete : function ( xhr ) {
                $('.ow-register-product-wrap').removeClass( 'ow-loader-active ow-module-loader' );
            },
        });
    });
});