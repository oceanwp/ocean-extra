(function($) {
    function loadFlickrPhotos($scope) {
        var $container = $scope.find('.oceanwp-flickr-container');
        if (!$container.length) {
            return;
        }

        $container.each(function() {
            var $this = $(this);
            var userId = $this.data('user-id');
            var maxPhotos = $this.data('max-photos');
            var containerId = $this.attr('id') || Math.random().toString(36).substring(2);

            // Sanitize the containerId
            containerId = containerId.replace(/[^a-zA-Z0-9_-]/g, '');
            var callbackName = 'jsonFlickrFeed_' + containerId;

            window[callbackName] = function(data) {
                var counter = 0;
                data.items.forEach(function(item) {
                    if (counter < maxPhotos) {
                        var photoUrl = item.media.m.replace('_m.jpg', '_q.jpg');
                        var $img = $('<img>')
                            .attr('src', photoUrl)
                            .attr('alt', item.title || 'Flickr image');
                        $this.append($img);
                        counter++;
                    }
                });

                // Clean up
                delete window[callbackName];
            };

            var feedUrl = 'https://www.flickr.com/services/feeds/photos_public.gne?' +
                'id=' + encodeURIComponent(userId) +
                '&format=json&jsoncallback=' + callbackName;

            var script = document.createElement('script');
            script.src = feedUrl;
            script.onerror = function() {
                console.error('Error loading Flickr feed.');
            };
            document.head.appendChild(script);
        });
    }

    $(document).ready(function() {
        loadFlickrPhotos($(document));
    });

    $(window).on('elementor/frontend/init', function() {
        elementorFrontend.hooks.addAction('frontend/element_ready/widget', function($scope) {
            loadFlickrPhotos($scope);
        });
    });
})(jQuery);