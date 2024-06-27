/**
 * Customizer enhancements for a better user experience.
 *
 * Contains handlers to make Preloader Customizer preview reload changes asynchronously.
 */

var api = wp.customize;

api( 'ocean_preloader_content', function( value ) {
    value.bind( function( newval ) {
        var preloaderContent = document.querySelector('#preloader-content .preloader-after-content');
        preloaderContent.innerHTML = newval;
    } );
} );
