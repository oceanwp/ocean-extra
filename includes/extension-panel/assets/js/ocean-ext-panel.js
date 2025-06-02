jQuery(document).ready(function($) {
    // Filter functionality
    $('.ocean-ext-panel-filter-btn[data-filter="All"]').addClass('active');

    $('.ocean-ext-panel-filter-btn').on('click', function() {
        var filter = $(this).data('filter');

        $('.ocean-ext-panel-filter-btn').removeClass('active');
        $(this).addClass('active');

        $('.ocean-ext-panel-item').show();

        if (filter !== 'All') {
            $('.ocean-ext-panel-item').each(function() {
                var cats = JSON.parse($(this).attr('data-categories'));
                if (cats.indexOf(filter) === -1) {
                    $(this).hide();
                }
            });
        }
    });

    // AJAX install/activate plugin
    $(document).on('click', '.ocean-ext-panel-action-btn[data-plugin-action]', function(e) {
        e.preventDefault();
        var $btn = $(this);
        var actionType = $btn.data('plugin-action');
        var filePath   = $btn.data('plugin-file_path');

        if (actionType === 'install') {
            var slug = filePath.split('/')[0];

            $btn.text(OCEAN_EXT_PANEL.strings.installing);
            $.ajax({
                url: OCEAN_EXT_PANEL.ajax_url,
                method: 'POST',
                data: {
                    action: 'ocean_install_plugin',
                    security: OCEAN_EXT_PANEL.nonce,
                    slug: slug
                },
                success: function(response) {
                    if (response.success) {
                        $btn.data('plugin-action', 'activate');
                        $btn.data('plugin-file_path', response.data.file_path);
                        $btn.text(OCEAN_EXT_PANEL.strings.activate);
                        $btn.removeClass('install-button').addClass('activate-button');
                    } else {
                        $btn.text(OCEAN_EXT_PANEL.strings.install);
                        alert(response.data.message);
                    }
                },
                error: function() {
                    $btn.text(OCEAN_EXT_PANEL.strings.install);
                    alert(OCEAN_EXT_PANEL.strings.install_error);
                }
            });
        } else if (actionType === 'activate') {
            $btn.text(OCEAN_EXT_PANEL.strings.activating);
            $.ajax({
                url: OCEAN_EXT_PANEL.ajax_url,
                method: 'POST',
                data: {
                    action: 'ocean_activate_plugin',
                    security: OCEAN_EXT_PANEL.nonce,
                    file_path: filePath
                },
                success: function(response) {
                    if (response.success) {
                        $btn.text(OCEAN_EXT_PANEL.strings.active)
                            .prop('disabled', true)
                            .removeClass('activate-button install-button')
                            .addClass('active-button');
                    } else {
                        $btn.text(OCEAN_EXT_PANEL.strings.activate);
                        alert(response.data.message);
                    }
                },
                error: function() {
                    $btn.text(OCEAN_EXT_PANEL.strings.activate);
                    alert(OCEAN_EXT_PANEL.strings.activate_error);
                }
            });
        }
    });
});