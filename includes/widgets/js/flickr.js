jQuery(document).ready(function($) {
    var userId = flickrWidgetParams.userId;
    var maxPhotos = flickrWidgetParams.maxPhotos;
    var feedUrl = 'https://www.flickr.com/services/feeds/photos_public.gne?id=' + userId + '&format=json';

    function getPhotoUrl(photo) {
        return photo.media.m.replace('_m.jpg', '_q.jpg');
    }

    $.ajax({
        url: feedUrl,
        dataType: 'jsonp',
        jsonpCallback: 'jsonFlickrFeed',
        success: function(data) {
            var counter = 0;
            $.each(data.items, function(i, item) {
                if (counter < maxPhotos) {
                    var photoUrl = getPhotoUrl(item);
                    $('#oceanwp-flickr-photos').append('<img src="' + photoUrl + '" alt="' + item.title + '">');
                    counter++;
                } else {
                    return false;
                }
            });
        },
        error: function(xhr, status, error) {
            console.error('Error fetching Flickr feed:', error);
        }
    });
});
