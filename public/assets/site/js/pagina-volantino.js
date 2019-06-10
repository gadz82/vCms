// IIFE - Immediately Invoked Function Expression
(function(yourcode){
    // The global jQuery object is passed as a parameter
    yourcode(window.jQuery, window, document);

}(function($, window, document) {
    $(function(){
        $('.portfolio-overlay > a').on('click', function(e){
           e.preventDefault();
        });

        $('div[id^="book-"]').each(function(){
            var id = parseInt($(this).attr('id').split('-')[1]);
            $(this).wowBook({
                height   : 800,
                width    : 1100,
                maxHeight : 800,
                flipSound : false,
                centeredWhenClosed : true,
                hardcovers : true,
                toolbar : "lastLeft, left, right, lastRight, zoomin, zoomout, slideshow, fullscreen, thumbnails",
                thumbnailsPosition : 'left',
                responsiveHandleWidth : 150,
                lightbox: "#show_book_"+id,
                lightboxColor: "#eee",
                toolbarPosition: "top",
                mouseWheel : "zoom"
            });
        });

    })
}));