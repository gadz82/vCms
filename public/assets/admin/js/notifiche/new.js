// IIFE - Immediately Invoked Function Expression
(function(code){
    // The global jQuery object is passed as a parameter
    code(window.jQuery, window, document);

}(function($, window, document){
    $(document).ready(function(){

        $('#id_tipologia_notifica').on('change', function(){
            var value = $(this).val(),
                selectTipologieUser = $('select[name="tipologie_user[]"]').parent().parent('.form-group').parent(),
                selectSpecificUser = $('select[name="specific_user[]"]').parent().parent('.form-group').parent();

            switch(value){
                case '1':
                    selectTipologieUser.attr('class', 'hidden');
                    selectSpecificUser.attr('class', 'hidden');
                    break;
                case '2' :
                    selectTipologieUser.attr('class', 'col-xs-12');
                    selectSpecificUser.attr('class', 'hidden');
                    break;
                case '3':
                    selectTipologieUser.attr('class', 'hidden');
                    selectSpecificUser.attr('class', 'col-xs-12');
                    break;
            }
        });

        $('#id_tipologia_notifica').trigger('change');


    });

}));