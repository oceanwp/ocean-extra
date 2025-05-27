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

            var feedUrl = 'https://www.flickr.com/services/feeds/photos_public.gne?id=' +
                encodeURIComponent(userId) + '&format=json&nojsoncallback=1';

            function getPhotoUrl(photo) {
                return photo.media.m.replace('_m.jpg', '_q.jpg');
            }

            fetch(feedUrl)
                .then(response => response.json())
                .then(data => {
                    var counter = 0;
                    data.items.forEach(function(item) {
                        if (counter < maxPhotos) {
                            const photoUrl = getPhotoUrl(item);
                            const $img = $('<img>')
                                .attr('src', photoUrl)
                                .attr('alt', item.title);
                            $this.append($img);
                            counter++;
                        }
                    });
                })
                .catch(error => {
                    console.error('Error loading Flickr feed:', error);
                });
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
