jQuery(document).ready(function($) {
    // Target only the plugin list in your custom tab
    $('#oceanwp-plugin-list').on('click', '.install-now', function(e) {
        e.preventDefault();

        var button = $(this);
        var pluginSlug = button.data('slug');

        button.text('Installing...').addClass('updating-message');

        $.ajax({
            url: oceanwpPluginInstall.ajax_url,
            type: 'POST',
            data: {
                action: 'oceanwp_install_plugin',
                slug: pluginSlug,
                _ajax_nonce: oceanwpPluginInstall.nonce
            },
            success: function(response) {
                if (response.success) {
                    button.text('Activate').removeClass('updating-message').addClass('activate-now button-primary');
                    button.data('action', 'activate'); 
                } else {
                    button.text('Install Now').removeClass('updating-message');
                    alert(response.data);
                }
            },
            error: function() {
                button.text('Install Now').removeClass('updating-message');
                alert('An error occurred. Please try again.');
            }
        });
    });

    $('#oceanwp-plugin-list').on('click', '.activate-now', function(e) {
        e.preventDefault();

        var button = $(this);
        var pluginSlug = button.data('slug');

        button.text('Activating...').addClass('updating-message');

        $.ajax({
            url: oceanwpPluginInstall.ajax_url,
            type: 'POST',
            data: {
                action: 'oceanwp_activate_plugin',
                slug: pluginSlug,
                _ajax_nonce: oceanwpPluginInstall.nonce
            },
            success: function(response) {
                if (response.success) {
                    button.text('Active').removeClass('updating-message').addClass('button-disabled').prop('disabled', true);
                } else {
                    button.text('Activate').removeClass('updating-message');
                    alert(response.data);
                }
            },
            error: function() {
                button.text('Activate').removeClass('updating-message');
                alert('An error occurred. Please try again.');
            }
        });
    });
});
