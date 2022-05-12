// Document Ready
jQuery(document).ready(function ($) {

    window['owpSystemInfoGetter'] = function () {

        $('[data-oceanwp-ajax]').each(function () {
            var $this = $(this);
            var type = $this.data('oceanwpAjax');
            var feedbackIcon = $this.find('.status-state');
            var feedbackText = $this.find('.status-text');

            wp.ajax.send('oceanwp_cp_system_status', {
                data: {
                    nonce: ExtraThemePanelOptions.nonce,
                    type: type
                },
                success: function success() {
                    feedbackIcon.html('<span class="status-invisible">True</span><span class="status-state status-true"></span>');
                },
                error: function error(res) {
                    feedbackIcon.html('<span class="status-invisible">False</span><span class="status-state status-false"></span>');
                    feedbackText.html(res);
                }
            });
        });
    }

    $(document.body).on('submit', '#ocean-customizer-control form', function (event) {
        event.preventDefault();
        runSavingCustomizerSettings($(this));
    });

    $(document.body).on('submit', 'form.save_panel_settings', function (event) {
        event.preventDefault();
        runSavingPanelSettings($(this));
    });


    $(document.body).on('submit', 'form.integration-settings', function (event) {
        event.preventDefault();
        runSaveIntegrationSettings($(this));
    });

    $(document.body).on('change', '.oe-switcher-bulk', function (event) {
        event.preventDefault();
        var checkedVal = $(this).prop('checked');
        $(this).closest('.column-wrap.clr').siblings().each(function () {
            $(this).find('input[type="checkbox"]').prop('checked', checkedVal);
        });
    });

    $(document.body).on('change', '#oceanwp-switch-customizer-search', function (event) {
        event.preventDefault();
        var $form = $('#ocean-customizer-control form');
        $form.submit();
    });

    $(document.body).on('change', '#oceanwp-switch-library-disable', function (event) {
        event.preventDefault();
        var optionVal = $(this).prop('checked') ? 'yes' : 'no';
        runSaveSingleOption('oe_library_active_status', optionVal);
    });

    $(document.body).on('change', '#oceanwp-switch-svg-support-disable', function (event) {
        event.preventDefault();
        var optionVal = $(this).prop('checked') ? 'yes' : 'no';
        runSaveSingleOption('oe_svg_support_active_status', optionVal);
    });

    $(document.body).on('change', '#oceanwp-switch-install-demos-active', function (event) {
        event.preventDefault();
        var optionVal = $(this).prop('checked') ? 'yes' : 'no';
        runSaveSingleOption('oe_install_demos_active', optionVal);
        if ($(this).prop('checked')) {
            $('#install-demos').fadeIn();
        } else {
            $('#install-demos').fadeOut();
        }
    });


    $(document.body).on('click', '#ocean-customizer-reset .btn', function (event) {
        event.preventDefault();
        resetCustomizerSettings();
    });

    $(document.body).on('click', '#oceanwp-export-button', function (event) {
        event.preventDefault();
        exportCustomizerSettings();
    });

    $(document.body).on('submit', '.form-oceanwp_import', function (event) {
        event.preventDefault();
        importCustomizerSettings($(this));
    });

    $(document.body).on('click', '.oceanwp-button--get-system-report', function () {
        var report = '';

        $('#oceanwp-tp-system-info thead, #oceanwp-tp-system-info tbody').each(function () {
            var $this = $(this);

            if ($this.is('thead')) {
                var label = $this.find('th:eq(0)').data('export-label') || $this.text();
                report = report + "\n### " + $.trim(label) + " ###\n\n";
            } else {
                $('tr', $this).each(function () {
                    var $this = $(this);
                    var label = $this.find('td:eq(0)').data('export-label') || $this.find('td:eq(0)').text();
                    var name = $.trim(label).replace(/(<([^>]+)>)/ig, '');

                    var value = $.trim($this.find('td:eq(1)').text().replace(/(\r\n\t|\n|\r|\t)/gm, ''));
                    var valArr = value.split(', ');

                    if (valArr.length > 1) {
                        var tempLine = '';

                        $.each(valArr, function (key, line) {
                            tempLine = tempLine + line + '\n';
                        });

                        value = tempLine;
                    }

                    report = report + '' + name + ': ' + value + "\n";
                });
            }
        });

        try {
            if( copyToClipboard(report) ) {
                if( window['showNotify'] !== undefined ) {
                    window['showNotify']('success', oceanwp_cp_textdomain.copied_system_info, true, 6000);
                } else {
                    alert( oceanwp_cp_textdomain.copied_system_info );
                }
            } else {
                $('#oceanwp-textarea--get-system-report textarea').val(report).focus().select();
                $('#oceanwp-textarea--get-system-report').slideDown();
            }
            return false;
        } catch (e) {
            console.log(e);
        }

        return false;
    });

    $(document.body).on('change', '#owp_recaptcha_version', function () {
        let selected_option = $(this).find('option:selected');
        if( selected_option.length ) {
            $(this).find('option').each( function () {
				jQuery( '#owp_google_recaptcha-' + jQuery( this ).val() ).hide( 0 );
			} );
            jQuery( '#owp_google_recaptcha-' + selected_option.val() ).show( 0 );
        }
    });
    if($('#owp_recaptcha_version').length) {
        $('#owp_recaptcha_version').trigger('change');
    }


    $(document.body).on('change', '#owp_api_images_integration', function () {
            jQuery(this).val() === '0' ? jQuery('.api-ingegrations').hide() : jQuery('.api-ingegrations').show();
    });

    $(document.body).on('change', '#owp_freepik_integration', function () {
            jQuery(this).val() === '0' ? jQuery('#owp_freepik_image_width_tr').hide() : jQuery('#owp_freepik_image_width_tr').show();
        });

    $(document.body).on('change', '#owp_freepik_image_width', function () {
            jQuery(this).val() !== 'custom' ? jQuery('#owp_freepik_image_width_custom_tr').hide() : jQuery('#owp_freepik_image_width_custom_tr').show();
        });
    

    function runSavingCustomizerSettings($form) {
        var $customizerSearchElement = $('#oceanwp-switch-customizer-search');
        if ($customizerSearchElement.prop('checked')) {
            if ($form.find('input[name="oe_panels_settings[customizer-search]"]').length) {
                $form.find('input[name="oe_panels_settings[customizer-search]"]').val("true");
            } else {
                $form.append('<input type="hidden" name="oe_panels_settings[customizer-search]" value="true" />');
            }
        } else {
            if ($form.find('input[name="oe_panels_settings[customizer-search]"]').length) {
                $form.find('input[name="oe_panels_settings[customizer-search]"]').remove();
            }
        }
        $.ajax({
            url: ajaxurl,
            method: "POST",
            data: {
                form_fields: $form.serialize(),
                action: 'oceanwp_cp_save_customizer_settings',
            },
            beforeSend: function () {
                window['showNotify']('success', oceanwp_cp_textdomain.saving_settings);
            },
            success: function (data) {
                window['showNotify'](data.success, data.data.message);
            },
            error: function (xhr, status, error) {
            },
            complete: function () {
            }
        });
    }

    function runSavingPanelSettings($form) {
        $.ajax({
            url: ajaxurl,
            method: "POST",
            data: {
                form_fields: $form.serialize(),
                action: 'oceanwp_cp_save_panel_settings',
                nonce: ExtraThemePanelOptions.nonce
            },
            beforeSend: function () {
                window['showNotify']('success', oceanwp_cp_textdomain.saving_settings);
            },
            success: function (data) {
                window['showNotify'](data.success, data.data.message);
            },
            error: function (xhr, status, error) {
            },
            complete: function () {
            }
        });
    }

    function runSaveIntegrationSettings($form) {
        $.ajax({
            url: ajaxurl,
            method: "POST",
            data: {
                form_fields: $form.serialize(),
                action: 'oceanwp_cp_save_integrations_settings',
                settings_for: $form.data('settings-for'),
                nonce: ExtraThemePanelOptions.nonce
            },
            beforeSend: function () {
                window['showNotify']('success', oceanwp_cp_textdomain.saving_settings);
            },
            success: function (data) {
                window['showNotify'](data.success, data.data.message);
            },
            error: function (xhr, status, error) {
            },
            complete: function () {
            }
        });
    }

    function runSaveSingleOption(optionName, value) {
        $.ajax({
            url: ajaxurl,
            method: "POST",
            data: {
                _nonce: ExtraThemePanelOptions.ocean_save_single_option_nonce,
                option_name: optionName,
                value: value,
                action: 'oceanwp_cp_save_single_option',
            },
            beforeSend: function () {
                window['showNotify']('success', oceanwp_cp_textdomain.saving_settings);
            },
            success: function (data) {
                window['showNotify'](data.success, data.data.message);
            },
            error: function (xhr, status, error) {
            },
            complete: function () {
            }
        });
    }

    function resetCustomizerSettings() {
        var confirmReset = confirm('Do you really want to reset customizer settings?');
        if (confirmReset) {
            $.ajax({
                url: ajaxurl,
                method: "POST",
                data: {
                    _nonce: ExtraThemePanelOptions.customizer_reset_nonce,
                    action: 'oceanwp_cp_customizer_reset',
                },
                beforeSend: function () {
                    window['showNotify']('success', oceanwp_cp_textdomain.reseting);
                },
                success: function (data) {
                    window['showNotify'](data.success, data.data.message);
                },
                error: function (xhr, status, error) {
                },
                complete: function () {
                }
            });
        }
    }

    function exportCustomizerSettings() {
        $.ajax({
            url: ajaxurl,
            method: "POST",
            dataType: 'binary',
            xhrFields: {
                'responseType': 'blob'
            },
            data: {
                _nonce: ExtraThemePanelOptions.customizer_export_nonce,
                action: 'oceanwp_cp_customizer_export',
            },
            beforeSend: function () {
                window['showNotify']('success', oceanwp_cp_textdomain.exporting);
            },
            success: function (data) {
                var link = document.createElement('a'),
                    filename = ExtraThemePanelOptions.customizer_export_filename;
                link.href = URL.createObjectURL(data);
                link.download = filename;
                link.click();
            },
            error: function (xhr, status, error) {
                console.log('Something went wrong');
            },
            complete: function () {
            }
        });
    }

    function importCustomizerSettings($form) {

        var fd = new FormData();
        var files = $('#oceanwp-import-file')[0].files;

        // Check file selected or not
        if (files.length > 0) {
            fd.append('file', files[0]);
            fd.append('action', 'oceanwp_cp_customizer_import');
            fd.append('_nonce', ExtraThemePanelOptions.customizer_import_nonce);

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: fd,
                contentType: false,
                processData: false,
                beforeSend: function () {
                    window['showNotify']('success', oceanwp_cp_textdomain.importing);
                },
                success: function (data) {
                    window['showNotify'](data.success, data.data.message);
                },
                error: function (xhr, status, error) {
                },
                complete: function () {
                }
            });
        } else {
            alert("Please select a file.");
        }
    }

    function copyToClipboard(text) {
        if (window.clipboardData && window.clipboardData.setData) {
            // Internet Explorer-specific code path to prevent textarea being shown while dialog is visible.
            return window.clipboardData.setData("Text", text);
        }
        else if (document.queryCommandSupported && document.queryCommandSupported("copy")) {
            var textarea = document.createElement("textarea");
            textarea.textContent = text;
            textarea.style.position = "fixed";  // Prevent scrolling to bottom of page in Microsoft Edge.
            document.body.appendChild(textarea);
            textarea.select();
            try {
                return document.execCommand("copy");  // Security exception may be thrown by some browsers.
            }
            catch (ex) {
                console.warn("Copy to clipboard failed.", ex);
                return prompt("Copy to clipboard: Ctrl+C, Enter", text);
            }
            finally {
                document.body.removeChild(textarea);
            }
        }
    }
});