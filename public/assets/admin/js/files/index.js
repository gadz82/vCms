// IIFE - Immediately Invoked Function Expression
(function(yourcode){
    // The global jQuery object is passed as a parameter
    yourcode(window.jQuery, window, document);

}(function($, window, document){

    var modal = $('#modalZoom');
    $('section.content').on('click', 'a[id^="image-zoom-"]', function () {
        modalImg = modal.find('#img01');

        modal.css({'display':'block'});
        modalImg.attr('src', $(this).data('image-zoom'));
    });

    modal.on('click', '#close-zoom-modal', function(){
        modal.css({'display':'none'});
    });
    modal.on('click', function(){
        modal.css({'display':'none'});
    });
}));