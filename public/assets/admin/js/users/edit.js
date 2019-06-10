// IIFE - Immediately Invoked Function Expression
(function(yourcode){
    // The global jQuery object is passed as a parameter
    yourcode(window.jQuery, window, document);

}(function($, window, document){

    // The $ is now locally scoped
    // Listen for the jQuery ready event on the document
    $(function(){
        // The DOM is ready!

        var $form = $('form[name="form-edit"]');
        var jrr = (json_render_required != '') ? JSON.parse(json_render_required) : [];

        $('select[name="id_tipologia_stato').on('change',function(){

            required = get_required($(this).val());

            $form.find('label:not(:has(input))').each(function(){
                var $this = $(this);

                $this.html($this.html().replace(' *',''));
                if(required.indexOf($this.attr('for')) !== -1){
                    $this.append(' *');
                }
            });

            $form.find('input, select, textarea').each(function(){
                var $this = $(this);

                $this.removeAttr('required');
                if(required.indexOf($this.attr('name')) !== -1){
                    $this.attr('required', true);
                }
            });

        });

        $('.selectpicker').on('change', function() {
            if($(this).val() != 0){
                $(this).closest('.form-group').removeClass('has-error');
            }else{
                $(this).closest('.form-group').addClass('has-error');
            }
        });

        var form_confirm = false;
        $form.on('submit', function(ev){

            $.validator.setDefaults({
                ignore: []
            });

            $form.validate({
                highlight: function(element) {
                    $(element).closest('.form-group').addClass('has-error');
                },
                unhighlight: function(element) {
                    $(element).closest('.form-group').removeClass('has-error');
                },
                success: function(element) {
                    $(element).closest('.form-group').removeClass('has-error');
                },
                invalidHandler: function(event, validator){},
                errorPlacement: function(error, element) {}
            });

            if(!$form.valid()){
                ev.preventDefault();

                var $first_error = $form.find('.form-group.has-error:first');

                if($first_error.length > 0){
                    $('html,body').animate({
                        scrollTop: $first_error.offset().top - 60
                    },'fast');
                }

                return false;
            }

        });


        $('select[name="id_tipologia_stato').trigger('change');

        function get_required(id_tipologia_stato){

            var required = [];
            var id_item = 'default';

            for(var item in jrr){

                var arr_item_elem = [];
                if(item.indexOf('|') !== -1) arr_item_elem = item.split('|');

                if(id_tipologia_stato == item || arr_item_elem.indexOf(id_tipologia_stato) !== -1){
                    id_item = item;
                    break;
                }
            }

            required = [];
            for(var val in jrr[id_item]){
                required.push(jrr[id_item][val]);
            }

            return required;

        }

        /**
         * Custom
         */

        $('#box-content-user-cars').on('click', 'div[id^="button-modifica-user-cars-"]', function(){
            var id_user_cars = $(this).data('id-user-cars');
            $.ajax({
                url: '/admin/users_car/edit/'+id_user_cars,
                type: 'GET',
                cache: false,
                success : function(data){
                    $('#user-cars-form-edit').empty().html(data.data);
                    $('#user-cars-form-edit').find('.selectpicker').selectpicker();
                    $('.date-format:input[type="text"]').datepicker({
                        format: "yyyy-mm-dd",
                        todayBtn: "linked",
                        clearBtn: true,
                        language: "it",
                        autoclose: true,
                        todayHighlight: true
                    });
                    $('#user-cars-form-edit').find('.ichek').iCheck({
                        checkboxClass: 'icheckbox_square-green',
                        radioClass: 'iradio_square-green',
                        tap: true,
                        inheritClass: true,
                        increaseArea: '20%' // optional
                    });
                }
            });
        });
        $('#box-content-user-cars').on('click', 'div[id^="button-elimina-user-cars-"]', function(){
            var id_form_fields = $(this).data('id-user-cars');

            swal({
                    title: "Attenzione!",
                    text: "Sei sicuro di voler cancellare questo campo?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Si, cancella!",
                    cancelButtonText: "No, scusa!",
                    closeOnConfirm: false,
                    closeOnCancel: false
                },
                function(isConfirm){
                    if(isConfirm){
                        $.ajax({
                            url: '/admin/users_car/delete/'+id_form_fields,
                            type: 'POST',
                            data: {'id_user_cars' : $('input[name="id"]').val()},
                            cache: false,
                            success : function(data){
                                $('#box-content-user-cars').empty().html(data.data);
                                swal("Cancellato!", "Il campo selezionato è stata cancellata.", "success");
                            },
                            error: function(){
                                swal("Errore", "Errore durante il processo di eliminazione.", "error");
                            }
                        });
                    }else{
                        swal("Cancellazione annullata", "La riga selezionata è salva :)", "error");
                    }
                }
            );

        });

        $('#modifica-user-cars-modal').on('submit', '#form-edit-user-cars', function(e){
            e.preventDefault();

            var form = this;

            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: $(this).serialize(),
                cache: false,
                success: function(data){

                    if(data.error != undefined){
                        $('.form_msg',form).html('<div class="alert alert-danger alert-dismissable-modal">'+data.error+'</div>');
                        $('.form_msg > .alert',form).slideDown('fast').delay(2000).slideUp('fast');
                    }else{
                        $('.form_msg',form).html('<div class="alert alert-success alert-dismissable-modal">'+data.success+'</div>');
                        $('.form_msg > .alert',form).slideDown('fast', function(){
                            $(':input',form).not(':button, :submit, :reset, :hidden').val('').removeAttr('checked').removeAttr('selected');
                            $('#box-content-user-cars').empty().html(data.data);
                        }).delay(1000).slideUp('fast',function(){
                            $('#modifica-user-cars-modal').modal('hide');
                        });
                    }

                }
            });

        });
        $('#form-crea-user-cars').submit(function(e){
            e.preventDefault();

            var form = this;

            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: $(this).serialize(),
                cache: false,
                success: function(data){

                    if(data.error != undefined){
                        $('.form_msg',form).html('<div class="alert alert-danger alert-dismissable-modal">'+data.error+'</div>');
                        $('.form_msg > .alert',form).slideDown('fast').delay(2000).slideUp('fast');
                    }else{
                        $('.form_msg',form).html('<div class="alert alert-success alert-dismissable-modal">'+data.success+'</div>');
                        $('.form_msg > .alert',form).slideDown('fast', function(){
                            $(':input',form).not(':button, :submit, :reset, :hidden').val('').removeAttr('checked').removeAttr('selected');
                            $('.selectpicker',form).selectpicker('refresh');
                            $('#box-content-user-cars').empty().html(data.data);
                        }).delay(2000).slideUp('fast',function(){
                            $('#crea-form-fields-modal').modal('hide');
                        });
                    }

                }
            });

        });

        $('#box-content-users-car-events').on('click', 'div[id^="button-modifica-users-car-events-"]', function(){
            var id_user_cars_events = $(this).data('id-users-car-events');
            $.ajax({
                url: '/admin/users_car_events/edit/'+id_user_cars_events,
                type: 'GET',
                cache: false,
                success : function(data){
                    $('#users-car-events-form-edit').empty().html(data.data);
                    $('#users-car-events-form-edit').find('.selectpicker').selectpicker();
                    $('.date-format:input[type="text"]').datepicker({
                        format: "yyyy-mm-dd",
                        todayBtn: "linked",
                        clearBtn: true,
                        language: "it",
                        autoclose: true,
                        todayHighlight: true
                    });
                    $('#users-car-events-form-edit').find('.ichek').iCheck({
                        checkboxClass: 'icheckbox_square-green',
                        radioClass: 'iradio_square-green',
                        tap: true,
                        inheritClass: true,
                        increaseArea: '20%' // optional
                    });
                }
            });
        });

        /**
         * Eventi Users Cars
         */
        $('#box-content-users-car-events').on('click', 'div[id^="button-elimina-users-car-events"]', function(){
            var id_form_fields = $(this).data('id-users-car-events');

            swal({
                    title: "Attenzione!",
                    text: "Sei sicuro di voler cancellare questo promemoria?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Si, cancella!",
                    cancelButtonText: "No, scusa!",
                    closeOnConfirm: false,
                    closeOnCancel: false
                },
                function(isConfirm){
                    if(isConfirm){
                        $.ajax({
                            url: '/admin/users_car_events/delete/'+id_form_fields,
                            type: 'POST',
                            data: {'id_users_car_events' : $('input[name="id"]').val()},
                            cache: false,
                            success : function(data){
                                $('#box-content-users-car-events').empty().html(data.data);
                                swal("Cancellato!", "Il campo selezionato è stata cancellata.", "success");
                            },
                            error: function(){
                                swal("Errore", "Errore durante il processo di eliminazione.", "error");
                            }
                        });
                    }else{
                        swal("Cancellazione annullata", "La riga selezionata è salva :)", "error");
                    }
                }
            );

        });

        $('#modifica-users-car-events-modal').on('submit', '#form-edit-users-car-events', function(e){
            e.preventDefault();

            var form = this;

            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: $(this).serialize(),
                cache: false,
                success: function(data){

                    if(data.error != undefined){
                        $('.form_msg',form).html('<div class="alert alert-danger alert-dismissable-modal">'+data.error+'</div>');
                        $('.form_msg > .alert',form).slideDown('fast').delay(2000).slideUp('fast');
                    }else{
                        $('.form_msg',form).html('<div class="alert alert-success alert-dismissable-modal">'+data.success+'</div>');
                        $('.form_msg > .alert',form).slideDown('fast', function(){
                            $(':input',form).not(':button, :submit, :reset, :hidden').val('').removeAttr('checked').removeAttr('selected');
                            $('#box-content-users-car-events').empty().html(data.data);
                        }).delay(1000).slideUp('fast',function(){
                            $('#modifica-users-car-events-modal').modal('hide');
                        });
                    }

                }
            });

        });

        $('#form-crea-users-car-events').submit(function(e){
            e.preventDefault();

            var form = this;

            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: $(this).serialize(),
                cache: false,
                success: function(data){

                    if(data.error != undefined){
                        $('.form_msg',form).html('<div class="alert alert-danger alert-dismissable-modal">'+data.error+'</div>');
                        $('.form_msg > .alert',form).slideDown('fast').delay(2000).slideUp('fast');
                    }else{
                        $('.form_msg',form).html('<div class="alert alert-success alert-dismissable-modal">'+data.success+'</div>');
                        $('.form_msg > .alert',form).slideDown('fast', function(){
                            $(':input',form).not(':button, :submit, :reset, :hidden').val('').removeAttr('checked').removeAttr('selected');
                            $('.selectpicker',form).selectpicker('refresh');
                            $('#box-content-users-car-events').empty().html(data.data);
                        }).delay(2000).slideUp('fast',function(){
                            $('#crea-form-fields-modal').modal('hide');
                        });
                    }

                }
            });

        });

    });

}));