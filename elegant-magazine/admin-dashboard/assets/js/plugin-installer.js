
var n = window.AFTHRAMPES_JS || {};

jQuery(document).ready(function ($) {

    "use strict";

    var is_loading = false;

    n.install_plugin = function (aft, plugin) {


        is_loading = true;
        aft.addClass('installing');

        $.ajax({
            type: 'POST',
            url: aft_installer_localize.ajax_url,
            data: {
                action: 'elegant_magazine_plugin_installer',
                plugin: plugin,
                nonce: aft_installer_localize.admin_nonce,
                dataType: 'json'
            },
            success: function (data) {
                if (data) {
                    if (data.status === 'success') {
                        aft.attr('class', 'activate button button-primary');
                        aft.html(aft_installer_localize.activate_btn);

                    } else {
                        aft.removeClass('installing');
                    }
                } else {
                    aft.removeClass('installing');
                }
                is_loading = false;
            },
            error: function (xhr, status, error) {
                console.log(status);
                aft.removeClass('installing');
                is_loading = false;
            }
        });


    }



    /*
   *  activate_plugin
   *  Activate the plugin
   */

    n.activate_plugin = function (aft, plugin) {
        aft.addClass('installing');
        $.ajax({
            type: 'POST',
            url: aft_installer_localize.ajax_url,
            data: {
                action: 'elegant_magazine_plugin_activation',
                plugin: plugin,
                nonce: aft_installer_localize.admin_nonce,
                dataType: 'json'
            },
            success: function (data) {
                console.log(data)
                if (data) {

                    if (data.status === 'success') {
                        aft.attr('class', 'installed button disabled');
                        aft.html(aft_installer_localize.installed_btn);
                        // if (data.plugin == 'af-companion') {
                        //     window.location = data.redirectUrl;
                        // } else {
                        aft.removeClass('installing');
                        //}

                        if (data.plugin == 'af-companion') {
                            aft.html();
                            var link = aft.attr('href');
                            var newurl = link.replace(link, data.redirectUrl)
                            aft.attr('href', newurl)
                            aft.removeClass('disabled installed');
                            aft.attr('class', 'button-primary aft-companion-link primary')
                            aft.html('Get Starter Sites');

                        }

                    }

                }
                is_loading = false;
            },
            error: function (xhr, status, error) {

                aft.removeClass('installing');
                is_loading = false;
            }
        });

    };



    /*
   *  Install/Activate Button Click
   *
   *  @since 1.0
   */

    $(document).on('click', '.aft-plugin-installer a.button', function (e) {

        var aft = $(this),
            plugin = aft.data('slug');

        e.preventDefault();

        if (!aft.hasClass('disabled')) {

            if (is_loading) return false;

            // Installation
            if (aft.hasClass('install')) {
                n.install_plugin(aft, plugin);
            }

            // Activation
            if (aft.hasClass('activate')) {
                n.activate_plugin(aft, plugin);
            }
        }
    });

    $('.aft-dismiss-notice').on('click', function () {
        $.ajax({
            type: 'POST',
            url: aft_installer_localize.ajax_url,
            data: {
                action: 'aft_notice_dismiss',
                nonce: aft_installer_localize.admin_nonce,

            },
            success: function (data) {

                if (data.status == 'success') {
                    $('.aft-notice-content-wrapper').remove();
                }

            }
        });

    })


});