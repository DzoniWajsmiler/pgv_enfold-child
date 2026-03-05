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