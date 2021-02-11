jQuery(function () {
    if (jQuery('body.com_widgetkit').length) {
        jQuery('div[data-app="widgetkit"] input[ng-model="marker"]').waitUntilExists(function () {
            if (jQuery("#nav-content-map td#name-mapex").length) {
                jQuery(this).prop('disabled', true);
                jQuery(this).attr('placeholder', "__WK_FIELD_DISPLAY_DISABLED__");
                jQuery(this).attr('title', "__WK_FIELD_DISPLAY_DISABLED__");
            }
        });
    }
});

function addMapExInfo(caption, text) {
    var id = 'mapEx-dialog-' + jQuery.now();
    jQuery('#' + id).remove();

    var contents = '<div uk-modal="container: div[data-app=\'widgetkit\']" id="' + id + '" class="widgetkitClusterInfo uk-flex-top"><div class="uk-modal-dialog uk-margin-auto-vertical"><div class="uk-modal-header"><h3 class="uk-modal-title uk-text-center">' + caption + '</h3></div><div class="uk-modal-body" uk-overflow-auto>' + text + '</div><div class="uk-modal-footer uk-text-center"><button class="uk-button uk-button-primary uk-modal-close">__WK_OK__</button></div></div></div>';
    jQuery('div[data-app="widgetkit"]').prepend(contents);

    /*We force to open links in new window*/
    jQuery('#' + id + ' a').attr('target', '_blank');
    return id;
}

function loadClusterCollections() {
    var tagsToReplace = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;'
    };

    function replaceTag(tag) {
        return tagsToReplace[tag] || tag;
    }

    function safe_tags_replace(str) {
        if (str)
            return str.replace(/[&<>]/g, replaceTag);
        else
            return str;
    }

    function ValidURL(str) {
        var regExp = /^(https?|ftp):\/\/([a-zA-Z0-9.-]+(:[a-zA-Z0-9.&%$-]+)*@)*((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[1-9][0-9]?)(\.(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[1-9]?[0-9])){3}|([a-zA-Z0-9-]+\.)*[a-zA-Z0-9-]+\.(com|edu|gov|int|mil|net|org|biz|arpa|info|name|pro|aero|coop|museum|[a-zA-Z]{2}))(:[0-9]+)*(\/($|[a-zA-Z0-9.,?'\\+&%$#=~_-]+))*$/;
        return regExp.test(str);
    }

    (function ($) {
        $(function () {
            // More code using $ as alias to jQuery
            var modal = window.modalSpinnerWidgetkit__WK_SAFE_NAME__("__WK_LOADING__");
            $.ajax({
                'url': 'https://raw.githubusercontent.com/rvalitov/cluster-markers/master/config/config.json',
                'type': "GET",
                'dataType': 'json',
                'cache': false,
                success: function (data) {
                    modal.hide();
                    if (data) {
                        var clusterContainer = $('#cluster-collection');
                        clusterContainer.empty();
                        jQuery(".widgetkitClusterInfo").remove();
                        var error_list = [];
                        for (var i = 0; i < data.length; i++) {
                            /*Validation test*/
                            var is_valid = true;
                            if (('name' in data[i]) && ('levels' in data[i]) && ('info' in data[i]) && (typeof data[i]['name'] === 'string') && (typeof data[i]['levels'] === 'object') && (typeof data[i]['info'] === 'string') && (data[i]['info'].length <= 2000) && (data[i]['levels'].length >= 1)) {
                                for (var k = 0; k < data[i]['levels'].length; k++)
                                    if ((!('icon' in data[i]['levels'][k])) || (!('color' in data[i]['levels'][k])) || (!('width' in data[i]['levels'][k])) || (!('height' in data[i]['levels'][k])) || (!('size' in data[i]['levels'][k])) || (!('icon_x' in data[i]['levels'][k])) || (!('icon_y' in data[i]['levels'][k])) || (!('label_x' in data[i]['levels'][k])) || (!('label_y' in data[i]['levels'][k])) || (typeof data[i]['levels'][k]['width'] !== 'number') || (typeof data[i]['levels'][k]['height'] !== 'number') || (typeof data[i]['levels'][k]['size'] !== 'number') || ((typeof data[i]['levels'][k]['icon_x'] !== 'number') && (data[i]['levels'][k]['icon_x'] !== '')) || ((typeof data[i]['levels'][k]['icon_y'] !== 'number') && (data[i]['levels'][k]['icon_y'] !== '')) || ((typeof data[i]['levels'][k]['label_x'] !== 'number') && (data[i]['levels'][k]['label_x'] !== '')) || ((typeof data[i]['levels'][k]['label_y'] !== 'number') && (data[i]['levels'][k]['label_y'] !== '')) || (typeof data[i]['levels'][k]['size'] !== 'number') || (data[i]['levels'][k]['size'] < 1) || (data[i]['levels'][k]['width'] < 1) || (data[i]['levels'][k]['height'] < 1) || (!ValidURL(data[i]['levels'][k]['icon']))) {
                                        is_valid = false;
                                        break;
                                    }
                            } else
                                is_valid = false;

                            if (is_valid) {
                                var name, txt;
                                if (data[i].name.length > 64)
                                    name = data[i].name.substring(0, 61) + '...';
                                else
                                    name = data[i].name;
                                var tags = '<div class="uk-card uk-card-body"><h4 class="uk-text-center">#' + (i + 1) + '. ' + safe_tags_replace(name);
                                if ((data[i]['info']) && (data[i]['info'].trim().length > 0)) {
                                    txt = data[i]['info'].trim();
                                    var modalId = addMapExInfo(name, txt);
                                    tags += ' <a href="#' + modalId + '" uk-toggle><span id="cluster-collection-info-' + i + '" uk-icon="icon: info"></span></a>';
                                }
                                tags += '</h4><div uk-grid class="uk-child-width-1-' + Math.min(5, data[i]['levels'].length) + '">';
                                for (k = 0; k < data[i]['levels'].length; k++)
                                    tags += '<div class="uk-text-center"><div><img src="' + data[i]['levels'][k]['icon'] + '" alt="__WK_LEVEL__' + (k + 1) + '"></div><small>__WK_LEVEL__ ' + (k + 1) + '</small></div>';
                                /*
                                It's quite difficult to mess with angularjs when you add code with ng-click that must be compiled dynamically in the scope. So, it's better to emulate user input in to fill in the data when a collection is activated.
                                */
                                tags += '</div><div class="uk-text-center"><button class="uk-button uk-button-primary" onclick="window.modalAlertWidgetkit__WK_SAFE_NAME__(\'{__WK_ACTIVATED__}\');';
                                tags += 'jQuery(\'#mapex-clear-levels\').click();';
                                for (k = 0; k < data[i]['levels'].length; k++) {
                                    tags += 'jQuery(\'#mapex-add-level\').click();';
                                    var id = k + 1;
                                    tags += 'jQuery(\'#cluster-' + id + '-color\').val(\'' + data[i]['levels'][k]['color'] + '\').trigger(\'change\');';
                                    tags += 'jQuery(\'#cluster-' + id + '-icon input\').val(\'' + data[i]['levels'][k]['icon'] + '\').trigger(\'change\');';
                                    tags += 'jQuery(\'#cluster-' + id + '-width\').val(\'' + data[i]['levels'][k]['width'] + '\').trigger(\'change\');';
                                    tags += 'jQuery(\'#cluster-' + id + '-height\').val(\'' + data[i]['levels'][k]['height'] + '\').trigger(\'change\');';
                                    tags += 'jQuery(\'#cluster-' + id + '-size\').val(\'' + data[i]['levels'][k]['size'] + '\').trigger(\'change\');';
                                    tags += 'jQuery(\'#cluster-' + id + '-icon_x\').val(\'' + data[i]['levels'][k]['icon_x'] + '\').trigger(\'change\');';
                                    tags += 'jQuery(\'#cluster-' + id + '-icon_y\').val(\'' + data[i]['levels'][k]['icon_y'] + '\').trigger(\'change\');';
                                    tags += 'jQuery(\'#cluster-' + id + '-label_x\').val(\'' + data[i]['levels'][k]['label_x'] + '\').trigger(\'change\');';
                                    tags += 'jQuery(\'#cluster-' + id + '-label_y\').val(\'' + data[i]['levels'][k]['label_y'] + '\').trigger(\'change\');';
                                }
                                tags += '"><i class="uk-icon uk-icon-check uk-margin-small-right"></i>__WK_ACTIVATE__</button></div></div>';
                                clusterContainer.append(tags);
                            } else
                                error_list.push(i);
                        }
                        var info_text = replaceTransAll('__WK_ITEMS_INFO__', {'number': data.length});
                        if (error_list.length > 0) {
                            info_text += ' ' + replaceTransAll('__WK_FAILED_ITEMS__', {'number': error_list.length});
                            for (k = 0; k < error_list.length; k++) {
                                if (k > 0)
                                    info_text += ', ';
                                info_text += '#' + error_list[k];
                            }
                            console.log(info_text);
                        }
                        window.notifyWidgetkit__WK_SAFE_NAME__(info_text, 'success');
                    } else
                        window.modalAlertWidgetkit__WK_SAFE_NAME__("__WK_DOWNLOAD_FAILED__");
                },
                error: function (jqXHR) {
                    modal.hide();
                    window.modalAlertWidgetkit__WK_SAFE_NAME__("__WK_DOWNLOAD_FAILED__<br>" + jqXHR.status + ' ' + jqXHR.statusText);
                }
            });
        });
    })(jQuery);
}

function verifyWidgetkitMapApiKey() {
    var el = jQuery('#wk-apikey');
    if (!el.length)
        return;
    var key = el.val();
    var error_msg = [];
    var iframe = document.createElement('iframe');
    iframe.style.display = "none";
    var html = '<head><title>Test<\/title><script>console.error = function(...rest) {window.parent.postMessage({source: \'iframe\', message: rest},\'*\');};<\/script><script src="https://maps.googleapis.com/maps/api/js?key=' + key + '&callback=initMap" async defer><\/script><script>var map;function initMap() { map = new google.maps.Map(document.getElementById(\'map\'), { center: {lat: -34.397, lng: 150.644}, zoom: 8 });}<\/script><\/head><body><div id="map"><\/div><\/body>';
    document.body.appendChild(iframe);

    window.addEventListener('message', function (response) {
        // Make sure message is from our iframe, extensions like React dev tools might use the same technique and mess up our logs
        if (response.data && response.data.source === 'iframe') {
            error_msg.push(response.data.message);
        }
    });

    iframe.contentWindow.document.open();
    iframe.contentWindow.document.write(html);
    iframe.contentWindow.document.close();

    var modal = window.modalSpinnerWidgetkit__WK_SAFE_NAME__('__WK_WAIT__');

    iframe.onload = function () {
        function checkResults() {
            document.body.removeChild(iframe);
            modal.hide();
            if (error_msg.length) {
                var l = '<ul>';
                for (var i = 0; i < error_msg.length; i++)
                    l += '<li>' + error_msg[i] + '</li>';
                l += '</ul>';
                window.modalAlertWidgetkit__WK_SAFE_NAME__('<h2>__WK_ERROR__</h2><div class="uk-overflow-container"><p><i class="uk-icon-warning uk-margin-small-right uk-text-danger"></i>__WK_INVALID_KEY__</p>' + l + '</div>');
            } else {
                window.modalAlertWidgetkit__WK_SAFE_NAME__('<h2>__WK_SUCCESS__</h2><p><i class="uk-icon-check uk-margin-small-right uk-text-success"></i>__WK_VALID_KEY__</p>');
            }
        }

        setTimeout(checkResults, 5000);
    };
}
