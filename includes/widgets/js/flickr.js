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
            var containerId = $this.attr('id');
            var callbackName = 'jsonFlickrFeed_' + containerId;
            var feedUrl = 'https://www.flickr.com/services/feeds/photos_public.gne?id=' + userId + '&format=json&jsoncallback=' + callbackName;

            function getPhotoUrl(photo) {
                return photo.media.m.replace('_m.jpg', '_q.jpg');
            }

            window[callbackName] = function(data) {
                var counter = 0;
                $.each(data.items, function(i, item) {
                    if (counter < maxPhotos) {
                        var photoUrl = getPhotoUrl(item);
                        $this.append('<img src="' + photoUrl + '" alt="' + item.title + '">');
                        counter++;
                    }
                });
            };

            var script = document.createElement('script');
            script.src = feedUrl;
            document.head.appendChild(script);
        });
    }

    // Execute when the document is ready
    $(document).ready(function() {
        loadFlickrPhotos($(document));
    });

    // Execute when Elementor previews are loaded
    $(window).on('elementor/frontend/init', function() {
        elementorFrontend.hooks.addAction('frontend/element_ready/widget', function($scope) {
            loadFlickrPhotos($scope);
        });
    });
})(jQuery);
