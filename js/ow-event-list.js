// 📁 ow-event-list.js
jQuery(document).ready(function ($) {
    function getUrlParameter(sParam) {
        var sPageURL = window.location.search.substring(1),
            sURLVariables = sPageURL.split('&'),
            sParameterName, i;

        for (i = 0; i < sURLVariables.length; i++) {
            sParameterName = sURLVariables[i].split('=');
            if (sParameterName[0] === sParam) {
                return typeof sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
            }
        }
        return false;
    }

    function changeUrlParameter(param, oldValue, newValue) {
        var url = new URL(window.location);
        if (newValue === null || newValue === '' || newValue === 'all') {
            url.searchParams.delete(param);
        } else {
            url.searchParams.set(param, newValue);
        }
        window.history.replaceState({}, '', url);
    }

//    $('.ow_event_list').each(function () {
$('.ow_event_list[data-uid]').each(function () {

        const container = $(this);
        const uid = container.data('uid');
        let page = 2;

        function updateEvents({ overwrite = false, category = 'all', arhiv = false }) {
            let data = {
                action: 'load_posts_by_ajax',
                page: overwrite ? 1 : page,
                max_page: container.find('.loadmore').data('max-page'),
                security: ow_event_data.security,
                event_type: container.data('event-type'),
                show: container.data('show'),
                sort: arhiv ? 'DESC' : 'ASC',
                now: container.data('now'),
                child_ids: container.data('child-ids'),
                arhiv: arhiv,
                load_more_text: container.data('load-more-text'),
                overwrite: overwrite,
                kategorija: category,
                uid: uid
            };

            container.addClass('loading');

            $.post(ow_event_data.ajaxurl, data, function (response) {
                if (overwrite) {
container.find('.ow-category-events-wrap').html(response);
                    page = 2;
                } else {
                    container.find('.ow-cat-grid-wrap').append(response);
                    page++;
                }

                if (page > data.max_page) {
                    container.find('.loadmore').hide();
                } else {
                    container.find('.loadmore').show();
                }
            }).always(() => {
                container.removeClass('loading');
            });
        }

        container.on('click', '.ow-cat-control', function () {
            let cat = $(this).data('filter');
            container.find('.ow-cat-control').removeClass('ow-control-active');
            $(this).addClass('ow-control-active');
            changeUrlParameter('kategorija', getUrlParameter('kategorija'), cat);
            updateEvents({ overwrite: true, category: cat });
        });

        container.on('click', '.ow-arhiv', function (e) {
            e.preventDefault();
            let isActive = $(this).hasClass('ow-arhiv-active');
            $(this).toggleClass('ow-arhiv-active');
            updateEvents({ overwrite: true, arhiv: !isActive });
        });

        container.on('click', '.loadmore', function () {
            updateEvents({ overwrite: false });
        });

        // Init on page load?
        if (getUrlParameter('arhiv') === 'da') {
            container.find('.ow-arhiv').addClass('ow-arhiv-active');
            updateEvents({ overwrite: true, arhiv: true });
        }
    });
});
