function verifyFiles__WK_SAFE_NAME__() {
    var modal = window.modalSpinnerWidgetkit__WK_SAFE_NAME__();
    jQuery.ajax({
        'url': '__WK_API____WK_DIST_NAME__/tags',
        'dataType': 'json',
        'type': "GET",
        /**
         * @typedef {Object} GitCommit
         * @property {!string} sha
         * @property {!string} url
         *
         * @typedef {Object} GitTags
         * @property {!string} name
         * @property {!string} zipball_url
         * @property {!string} tarball_url
         * @property {!GitCommit} commit
         *
         * @param {!GitTags[]} data
         */
        success: function (data) {
            if (data) {
                var found = false;
                jQuery.each(data, function (index, value) {
                    if (value.name === '__WK_VERSION__') {
                        var filesTree = '__WK_API____WK_DIST_NAME__/git/trees/' + value.commit.sha + '?recursive=1';
                        found = true;
                        jQuery.ajax({
                            'url': filesTree,
                            'dataType': 'json',
                            'type': "GET",
                            /**
                             * @typedef {Object} GitFile
                             * @property {!string} path
                             * @property {!string} mode
                             * @property {!string} type
                             * @property {!string} sha
                             * @property {!string} url
                             * @property {number} size
                             *
                             * @typedef {Object} GitTree
                             * @property {!string} sha
                             * @property {!string} url
                             * @property {!GitFile[]} tree
                             * @property {boolean} truncated
                             *
                             * @param data
                             */
                            success: function (data) {
                                if (data) {
                                    var error_list = '';
                                    try {
                                        /**
                                         * @typedef {Object} LocalFile
                                         * @property {!string} name
                                         * @property {number} size
                                         * @property {!string} hash
                                         *
                                         * @type {LocalFile[]}
                                         */
                                        var localFiles = JSON.parse(jQuery('#files-__WK_SAFE_NAME__').html());
                                    } catch (err) {
                                        window.modalAlertWidgetkit__WK_SAFE_NAME__('__WK_FAILED_JSON_PARSE__');
                                        return;
                                    }
                                    console.log("Local files __WK_SAFE_NAME__", localFiles);
                                    console.log("Remote files __WK_SAFE_NAME__", data.tree);
                                    jQuery.each(data.tree, function (index, value) {
                                        if ((value.type === 'blob') && (value.path.indexOf('__WK_SAFE_NAME__/') === 0)) {
                                            var isFound = false;
                                            var localSha = '';
                                            var localSize = 0;

                                            jQuery.each(localFiles, function (indexFile, fileInfo) {
                                                if (fileInfo.name === value.path) {
                                                    isFound = true;
                                                    localSha = fileInfo.hash;
                                                    localSize = fileInfo.size;
                                                    return false;
                                                }
                                            });
                                            if (isFound) {
                                                if ((localSize !== value.size) || (localSha !== value.sha))
                                                    error_list += '<tr><td>' + value.path + '</td><td>__WK_FILE_ALTERED__</td></tr>';
                                            } else
                                                error_list += '<tr><td>' + value.path + '</td><td>__WK_FILE_MISSING__</td></tr>';
                                        }
                                    });
                                    modal.hide();
                                    if (error_list)
                                        window.modalAlertWidgetkit__WK_SAFE_NAME__('<div class="uk-overflow-container"><table class="uk-table"><thead><tr><th>__WK_FILE__</th><th>__WK_PROBLEM__</th></tr></thead><tbody>' + error_list + '</tbody></table></div>');
                                    else
                                        window.modalAlertWidgetkit__WK_SAFE_NAME__('__WK_NO_PROBLEMS__');
                                } else {
                                    modal.hide();
                                    window.modalAlertWidgetkit__WK_SAFE_NAME__('__WK_NO_INFO__');
                                }
                            },
                            error: function () {
                                modal.hide();
                                window.modalAlertWidgetkit__WK_SAFE_NAME__('__WK_NO_RESPONSE__');
                            }
                        });
                    }
                });
                if (!found) {
                    modal.hide();
                    window.modalAlertWidgetkit__WK_SAFE_NAME__('__WK_NO_RELEASE_INFO__');
                }
            } else {
                modal.hide();
                window.modalAlertWidgetkit__WK_SAFE_NAME__('__WK_NO_RESPONSE__');
            }
        },
        error: function () {
            modal.hide();
            window.modalAlertWidgetkit__WK_SAFE_NAME__('__WK_NO_RESPONSE__');
        }
    });
}

jQuery(function () {
    if (typeof window.notifyWidgetkit__WK_SAFE_NAME__ !== "function") {
        /**
         * Shows notification that auto hides
         * @param {!string} text
         * @param {'success'|'primary'|'warning'|'danger'} [status="primary"]
         */
        window.notifyWidgetkit__WK_SAFE_NAME__ = function (text, status) {
            try {
                if (typeof UIkit.notification === "function")
                    UIkit.notification({
                        message: text,
                        status: status ? status : 'primary',
                        pos: 'top-center',
                        timeout: 3500
                    });
            } catch (e) {
            }
        }
    }

    if (typeof window.modalSpinnerWidgetkit__WK_SAFE_NAME__ !== "function") {
        /**
         * Shows loading indicator
         * @param {?string} [text]
         * @returns {*}
         */
        window.modalSpinnerWidgetkit__WK_SAFE_NAME__ = function (text) {
            try {
                var dialog = UIkit.modal.dialog('<div class="uk-modal-body uk-text-center"><h2 class="uk-modal-title">' + (text ? text : '__WK_WAIT__') + '</h2><div uk-spinner="ratio: 2"></div></div>', {container: 'div[data-app="widgetkit"]'});
                dialog.$el.firstElementChild.classList.add('uk-margin-auto-vertical');
                return dialog;
            } catch (e) {
            }
        }
    }

    if (typeof window.modalAlertWidgetkit__WK_SAFE_NAME__ !== "function") {
        /**
         * Shows alert dialog
         * @param {!string} text
         * @returns {*}
         */
        window.modalAlertWidgetkit__WK_SAFE_NAME__ = function (text) {
            try {
                var dialog = UIkit.modal.alert(text, {
                    container: 'div[data-app="widgetkit"]',
                    labels: {ok: '__WK_OK__'}
                });
                dialog.dialog.$el.firstElementChild.classList.add('uk-margin-auto-vertical');
                return dialog;
            } catch (e) {
            }
        }
    }

    /**
     * Display modal dialog with update info
     * @param {!string} urlDownload
     * @param {!string} buildDate
     * @param {!string} buildVersion
     * @param {!string} releaseInfo
     */
    function showUpdateInfo(urlDownload, buildDate, buildVersion, releaseInfo) {
        var dialogTemplate = '__WK_MODAL_TEMPLATE__';
        dialogTemplate = dialogTemplate.replace('%URL_DOWNLOAD%', urlDownload);
        dialogTemplate = dialogTemplate.replace('%DATE_REMOTE%', buildDate);
        dialogTemplate = dialogTemplate.replace('%VERSION_REMOTE%', buildVersion);
        dialogTemplate = dialogTemplate.replace('%RELEASE_INFO%', releaseInfo);
        dialogTemplate = dialogTemplate.replace(/(\\r|\\n)/gm, '');
        window.modalAlertWidgetkit__WK_SAFE_NAME__(dialogTemplate);
    }

    /* Filling the about info */
    jQuery('#name-__WK_SAFE_NAME__').waitUntilExists(function () {
        jQuery('#name-__WK_SAFE_NAME__').empty().append('__WK_NAME__');
        jQuery('#build-__WK_SAFE_NAME__').empty().append('__WK_DATE__');
        // noinspection HtmlUnknownTarget
        jQuery('#website-__WK_SAFE_NAME__').empty().append('<a href="__WK_WEBSITE__" target="_blank">__WK_WEBSITE__</a>');
        jQuery('#version-__WK_SAFE_NAME__').empty().append('__WK_VERSION__');
        // noinspection HtmlUnknownTarget
        jQuery('#logo-__WK_SAFE_NAME__').empty().append('<img class="uk-width-1-1" src="__WK_LOGO__" style="max-width:300px;" alt="Widget Logo">');
        // noinspection HtmlUnknownTarget
        jQuery('#wiki-__WK_SAFE_NAME__').empty().append('<a href="__WK_WIKI_URL__" target="_blank">__WK_WIKI_URL__</a>');
        jQuery('#version-jquery-__WK_SAFE_NAME__').empty().append(jQuery.fn.jquery);

        if (UIkit && UIkit.version) {
            jQuery('.version-uikit-__WK_SAFE_NAME__').empty().append(UIkit.version);
            var blockValid = jQuery('#version-uikit-valid-__WK_SAFE_NAME__').removeClass("uk-hidden");
            var blockInvalid = jQuery('#version-uikit-invalid-__WK_SAFE_NAME__').removeClass("uk-hidden");
            if (versioncompare(UIkit.version, "__WK_MIN_UIKIT_VERSION__") < 0)
                blockValid.addClass("uk-hidden");
            else
                blockInvalid.addClass("uk-hidden");
        }

        if (angular && angular.version && angular.version.full) {
            jQuery('#version-angularjs-__WK_SAFE_NAME__').empty().append(angular.version.full);
        }

        jQuery.ajax({
            'url': '__WK_CONFIG_FILE__',
            'type': "GET",
            'dataType': 'json',
            'cache': false,
            success: function (data) {
                if (data) {
                    /*Update all elements*/
                    jQuery.each(data, function (index, value) {
                        var e = jQuery('[ng-model="widget.data.global[\'' + index + '\']"]');
                        console.info("Updated global settings for option " + index + " (" + e.length + " items)");
                        e.val(value);
                        e.trigger("change");
                    });
                }
            }
        });
    });

    /**
     * Prints date in nice format
     * @param {!Date} MyDate
     * @param {!string} [dateSeparator='/']
     * @returns {string}
     */
    function printNiceDate(MyDate, dateSeparator) {
        if (typeof dateSeparator != 'string') {
            dateSeparator = '/';
        }
        return ('0' + MyDate.getDate()).slice(-2) + dateSeparator + ('0' + (MyDate.getMonth() + 1)).slice(-2) + dateSeparator + MyDate.getFullYear();
    }

    /**
     * Shows failed to update info
     * @param {!string} [info=""]
     */
    function failedToUpdate(info) {
        if (sessionStorage) {
            var d = new Date();
            sessionStorage.setItem('date-__WK_SAFE_NAME__', d.getTime().toString());
            sessionStorage.setItem('version-__WK_SAFE_NAME__', '__WK_INFO_VERSION__');
            sessionStorage.setItem('status-__WK_SAFE_NAME__', "-1");
            sessionStorage.setItem('error-__WK_SAFE_NAME__', info);
        }
        jQuery('#update-__WK_SAFE_NAME__').waitUntilExists(function () {
            jQuery('div.update-info-__WK_SAFE_NAME__').addClass('uk-hidden');
            jQuery('#update-problem-__WK_SAFE_NAME__').removeClass('uk-hidden');
            var blockProblem = jQuery('#update-problem-text-__WK_SAFE_NAME__').empty();
            if (info)
                blockProblem.html(info);
        });
    }

    /*We only show check for updates on the Widgetkit page*/
    if (!((window.location.href.indexOf('com_widgetkit') > 0) || (window.location.href.indexOf('page=widgetkit') > 0)))
        return;

    //Checking the cache
    if (sessionStorage) {
        //Browser supports cache
        var dataStatus = parseInt(sessionStorage.getItem('status-__WK_SAFE_NAME__'));
        var dataDate = parseInt(sessionStorage.getItem('date-__WK_SAFE_NAME__'));
        var dataVersion = sessionStorage.getItem('version-__WK_SAFE_NAME__');
        var dataBody = sessionStorage.getItem('body-__WK_SAFE_NAME__');
        var dataDateRemote = sessionStorage.getItem('date-remote-__WK_SAFE_NAME__');
        var dataTagName = sessionStorage.getItem('tag-name-__WK_SAFE_NAME__');
        var dataURL = sessionStorage.getItem('url-__WK_SAFE_NAME__');
        var dataError = sessionStorage.getItem('error-__WK_SAFE_NAME__');
        var d = new Date();
        if ((dataDate) && (dataVersion) && (dataVersion === '__WK_INFO_VERSION__') && (dataDate - d.getTime() < 35 * 60 * 1000)) {
            //We have a cached value
            if (dataStatus === 1) {
                //Update is available
                jQuery('#update-__WK_SAFE_NAME__').waitUntilExists(function () {
                    jQuery('div.update-info-__WK_SAFE_NAME__').addClass('uk-hidden');
                    jQuery('#update-available-__WK_SAFE_NAME__').removeClass('uk-hidden');

                    jQuery('#version-local-__WK_SAFE_NAME__').empty().append('__WK_VERSION__');
                    jQuery('#version-remote-__WK_SAFE_NAME__').empty().append(dataTagName);
                    jQuery('#date-local-__WK_SAFE_NAME__').empty().append('__WK_DATE__');

                    var remoteDate = jQuery('#date-remote-__WK_SAFE_NAME__').empty();
                    if ((dataDateRemote) && (dataDateRemote.length))
                        remoteDate.append(dataDateRemote);

                    jQuery('#release-info-__WK_SAFE_NAME__').empty().append(dataBody);
                    jQuery('#update-logo-__WK_SAFE_NAME__').attr('src', '__WK_LOGO__');
                    jQuery('#download-__WK_SAFE_NAME__').attr('href', dataURL);
                    jQuery('#instructions-__WK_SAFE_NAME__').attr('href', '__WK_WIKI_URL__');
                    jQuery('#update-details-__WK_SAFE_NAME__').click(function () {
                        showUpdateInfo(dataURL, dataDateRemote, dataTagName, dataBody);
                    });
                });
            }
            if (dataStatus === 0) {
                //No updates available
                jQuery('#update-__WK_SAFE_NAME__').waitUntilExists(function () {
                    jQuery('div.update-info-__WK_SAFE_NAME__').addClass('uk-hidden');
                    jQuery('#update-ok-__WK_SAFE_NAME__').removeClass('uk-hidden');
                });
            }
            if (dataStatus < 0) {
                //Failed to receive updates
                failedToUpdate(dataError);
            }
            return;
        }
    }

    jQuery.ajax({
        'url': '__WK_API____WK_DIST_NAME__/releases/latest',
        'type': "GET",
        'cache': false,
        'dataType': 'json',
        /**
         * @typedef {Object} ReleaseInfo
         * @property {!string} url
         * @property {!string} html_url
         * @property {!string} tag_name
         * @property {!string} name
         * @property {!string} published_at
         * @param {ReleaseInfo} data
         */
        success: function (data) {
            if (data) {
                if (sessionStorage) {
                    var d = new Date();
                    sessionStorage.setItem('date-__WK_SAFE_NAME__', d.getTime().toString());
                    sessionStorage.setItem('version-__WK_SAFE_NAME__', '__WK_INFO_VERSION__');
                }
                if (versioncompare('__WK_VERSION__', data.tag_name) < 0) {
                    var date_remote = Date.parse(data.published_at);
                    if (date_remote > 0) {
                        date_remote = printNiceDate(new Date(date_remote));
                    } else {
                        date_remote = '';
                    }
                    var infoText = '<div class="wk-noconflict"><p class="uk-margin-remove">__WK_NEW_RELEASE__ ' + data.tag_name + '.</p><p class="uk-text-center uk-margin-remove"><button class="uk-button uk-button-mini uk-button-default" id="info-__WK_DIST_NAME__">__WK_UPDATE_DETAILS__</button></p></div>';

                    window.notifyWidgetkit__WK_SAFE_NAME__(infoText);

                    var dataBody = marked(data.body);
                    if (sessionStorage) {
                        sessionStorage.setItem('body-__WK_SAFE_NAME__', dataBody);
                        sessionStorage.setItem('status-__WK_SAFE_NAME__', "1");
                        sessionStorage.setItem('date-remote-__WK_SAFE_NAME__', date_remote);
                        sessionStorage.setItem('tag-name-__WK_SAFE_NAME__', data.tag_name);
                        sessionStorage.setItem('url-__WK_SAFE_NAME__', data.html_url);
                    }

                    jQuery('#info-__WK_DIST_NAME__').click(function () {
                        showUpdateInfo(data.html_url, date_remote, data.tag_name, dataBody);
                    });
                    jQuery('#update-__WK_SAFE_NAME__').waitUntilExists(function () {
                        jQuery('div.update-info-__WK_SAFE_NAME__').addClass('uk-hidden');
                        jQuery('#update-available-__WK_SAFE_NAME__').removeClass('uk-hidden');
                        jQuery('#version-local-__WK_SAFE_NAME__').empty().append('__WK_VERSION__');
                        jQuery('#version-remote-__WK_SAFE_NAME__').empty().append(data.tag_name);
                        jQuery('#date-local-__WK_SAFE_NAME__').empty().append('__WK_DATE__');

                        var remoteDate = jQuery('#date-remote-__WK_SAFE_NAME__').empty();
                        if (date_remote.length)
                            remoteDate.append(date_remote);

                        jQuery('#release-info-__WK_SAFE_NAME__').empty().append(dataBody);
                        jQuery('#update-logo-__WK_SAFE_NAME__').attr('src', '__WK_LOGO__');
                        jQuery('#download-__WK_SAFE_NAME__').attr('href', data.html_url);
                        jQuery('#instructions-__WK_SAFE_NAME__').attr('href', '__WK_WIKI_URL__');

                        jQuery('#update-details-__WK_SAFE_NAME__').click(function () {
                            showUpdateInfo(data.html_url, date_remote, data.tag_name, dataBody);
                        });
                    });
                } else {
                    if (sessionStorage) {
                        sessionStorage.setItem('status-__WK_SAFE_NAME__', "0");
                    }
                    jQuery('#update-__WK_SAFE_NAME__').waitUntilExists(function () {
                        jQuery('div.update-info-__WK_SAFE_NAME__').addClass('uk-hidden');
                        jQuery('#update-ok-__WK_SAFE_NAME__').removeClass('uk-hidden');
                    });
                }
            } else {
                failedToUpdate();
            }
        },
        error: function (jqXHR) {
            failedToUpdate(jqXHR.status + ' ' + jqXHR.statusText);
        }
    });
});
