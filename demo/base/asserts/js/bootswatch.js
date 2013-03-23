$('a[rel=tooltip]').tooltip({
    'placement': 'bottom'
});


$('.smartnav a').smoothScroll({offset: -40});

$(function () {

    var $container = $('.thumbnails');

    $container.imagesLoaded(function () {

        $container.masonry({

            itemSelector: '.span3'

        });

    });

});