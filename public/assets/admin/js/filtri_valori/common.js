// IIFE - Immediately Invoked Function Expression
(function(yourcode){
    // The global jQuery object is passed as a parameter
    yourcode(window.jQuery, window, document);

}(function($, window, document){
    var parentSelectActive = false;

    $('select#id_filtro').on('change', function(){
        $('#dynamic-filter-realtions').remove();
        var fgroup = $(this).parent().parent('.form-group').parent();
        var id_filtro = $(this).val();
        $.ajax({
            url: '/admin/filtri_valori/checkFiltro/'+id_filtro,
            type: 'GET',
            cache: false,
            success : function(data){
                if(data.has_select){
                    fgroup.after(data.data);
                    $('.selectpicker').selectpicker();
                    parentSelectActive = true;
                } else {
                    if(parentSelectActive){
                        $('#dynamic-filter-realtions').remove();
                        parentSelectActive = false;
                    }
                }
            }
        });
    });
    $(document).ready(function () {
        $('select#id_filtro').trigger('change');
    })
}));