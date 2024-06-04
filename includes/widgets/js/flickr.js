jQuery(document).ready(function($) {

    $('.oceanwp-flickr-container').each(function() {
        var $container = $(this);
        var userId = $container.data('user-id');
        var maxPhotos = $container.data('max-photos');
        var containerId = $container.attr('id');
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
                    $container.append('<img src="' + photoUrl + '" alt="' + item.title + '">');
                    counter++;
                }
            });
        };

        var script = document.createElement('script');
        script.src = feedUrl;
        document.head.appendChild(script);
    });
});
