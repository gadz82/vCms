// IIFE - Immediately Invoked Function Expression
(function(yourcode){
    // The global jQuery object is passed as a parameter
    yourcode(window.jQuery, window, document);

}(function($, window, document){
    $(document).ready(function(){
       $('button[id^="reindex-"]').on('click', function(){
           var btn = $(this);
           var tipologia_post_id = parseInt(btn.attr('id').split('-')[1]);
           var spinner = $('<i class="fa fa-cog fa-spin fa-fw"></i>');
           btn.hide().after(spinner);
           swal({
                   title: "Attenzione!",
                   text: "Sei sicuro di voler rigenerare l'indice di questo Post Type e di sovrascrivere tutti i relativi flat records?",
                   type: "warning",
                   showCancelButton: true,
                   confirmButtonColor: "#DD6B55",
                   confirmButtonText: "Si, credo in me stesso!",
                   cancelButtonText: "No, ho paura della morte!",
                   closeOnConfirm: false,
                   closeOnCancel: false
               },
               function(isConfirm){
                   if(isConfirm){
                       $.ajax({
                           url: '/admin/index/rebuildIndex',
                           type: 'POST',
                           data: {"id_tipologia_post": tipologia_post_id},
                           cache: false,
                           success: function (data){
                               btn.show();
                               spinner.hide();
                               if(data.success){
                                   swal("Fatto!", "Indice rigenerato e dati aggiornati", "success");
                                   btn.attr('class', 'btn btn-success btn-xs');
                                   $('span#label-index-'+tipologia_post_id).attr('class', 'label label-success').text('Indice OK');
                               } else {
                                   swal("Ohhhh!", "Si è verificato un errore durante il processo di indicizzazione, contatta un amministratore", "success");
                                   btn.attr('class', 'btn btn-error btn-xs');
                                   $('span#label-index-'+tipologia_post_id).attr('class', 'label label-error').text('Indice Obsoleto');
                               }
                           },
                           error: function (status, data){
                               btn.show();
                               spinner.hide();
                               btn.attr('class', 'btn btn-error btn-xs');
                               swal("Errore nella richiesta!", "Si è verificato un errore nella request, pro    babilmente si tratta di un problema di permessi. Contatta l'amministratore di rete!", "success");
                               $('span#label-index-'+tipologia_post_id).attr('class', 'label label-error').text('Indice Obsoleto');
                           }
                       });

                   }else{
                       btn.show();
                       spinner.hide();
                       swal("Magna Tranquillo", "Le tue flat tables sono ancora integre", "error");
                   }
               }
           );


       });
    });
}));