$(document).ready(function () {

    var $container = $('.thumbnails');

    $container.imagesLoaded(function () {

        $container.masonry({

            itemSelector: '.span3',
            callback: function() {
                $('.smartnav a').smoothScroll({offset: -40});
            }
        });

    });

    $('a[rel=tooltip]').tooltip({
        'placement': 'bottom'
    });

});