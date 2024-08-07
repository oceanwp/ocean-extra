jQuery(document).ready(function($) {
    $(document).on('click', '.install-now', function(e) {
        e.preventDefault();

        var button = $(this);
        var pluginSlug = button.data('slug');

        // Show loading indicator
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
                    button.text('Installed').removeClass('updating-message').addClass('updated-message');
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
});
