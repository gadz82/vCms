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

		$('#box-content-applicazioni-domini').on('click', 'div[id^="button-modifica-applicazione-dominio-"]', function(){
			var id_app_dominio = $(this).data('id-applicazione-dominio');
			$.ajax({
				url: '/admin/applicazioni_domini/edit/'+id_app_dominio,
				type: 'GET',
				cache: false,
				success : function(data){
					$('#applicazioni-domini-form-edit').empty().html(data.data);
				}
			});
		});
		$('#box-content-applicazioni-domini').on('click', 'div[id^="button-elimina-applicazione-dominio-"]', function(){
			var id_app_dominio = $(this).data('id-applicazione-dominio');
			
			swal({
				  title: "Attenzione!",
				  text: "Sei sicuro di voler cancellare questa riga?",
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
							url: '/admin/applicazioni_domini/delete/'+id_app_dominio,
							type: 'POST',
							data: {'id_applicazione' : $('input[name="id"]').val()},
							cache: false,
							success : function(data){
								$('#box-content-applicazioni-domini').empty().html(data.data);
								swal("Cancellato!", "La riga selezionato è stata cancellata.", "success");
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
		
		$('#modifica-applicazione-dominio-modal').on('submit', '#form-edit-applicazione-dominio', function(e){
			e.preventDefault();
			
			var form = this;
			
			$.ajax({
				url: $(this).attr('action')+'/'+$('input[name="id"]').val(),
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
							$('#box-content-applicazioni-domini').empty().html(data.data);
						}).delay(2000).slideUp('fast',function(){
							$('#modifica-applicazione-dominio-modal').modal('hide');
						});
					}
					
				}
			});
			
		});
		$('#form-crea-applicazione-dominio').submit(function(e){
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
							$('#box-content-applicazioni-domini').empty().html(data.data);
						}).delay(2000).slideUp('fast',function(){
							$('#crea-applicazione-dominio-modal').modal('hide');
						});
					}
					
				}
			});
			
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


        $('#box-content-applicazioni-routes').on('click', 'div[id^="button-modifica-applicazione-route-"]', function () {
            var id_app_route = $(this).data('id-applicazione-route');
            $.ajax({
                url: '/admin/applicazioni_routes/edit/' + id_app_route,
                type: 'GET',
                cache: false,
                success: function (data) {
                    $('#applicazioni-routes-form-edit').empty().html(data.data);
                    $('#applicazioni-routes-form-edit').find('.selectpicker').selectpicker();
                    $('#applicazioni-routes-form-edit').find('.ichek').iCheck({
                        checkboxClass: 'icheckbox_square-green',
                        radioClass: 'iradio_square-green',
                        tap: true,
                        inheritClass: true,
                        increaseArea: '20%' // optional
                    });
                }
            });
        });
        $('#box-content-applicazioni-routes').on('click', 'div[id^="button-elimina-applicazione-route-"]', function () {
            var id_app_route = $(this).data('id-applicazione-route');

            swal({
                    title: "Attenzione!",
                    text: "Sei sicuro di voler cancellare questa route?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Si, cancella!",
                    cancelButtonText: "No, scusa!",
                    closeOnConfirm: false,
                    closeOnCancel: false
                },
                function (isConfirm) {
                    if (isConfirm) {
                        $.ajax({
                            url: '/admin/applicazioni_routes/delete/' + id_app_route,
                            type: 'POST',
                            data: {'id_applicazione': $('input[name="id"]').val()},
                            cache: false,
                            success: function (data) {
                                $('#box-content-applicazioni-routes').empty().html(data.data);
                                swal("Cancellato!", "La route selezionato è stata cancellata.", "success");
                            },
                            error: function () {
                                swal("Errore", "Errore durante il processo di eliminazione.", "error");
                            }
                        });
                    } else {
                        swal("Cancellazione annullata", "La route selezionata è salva :)", "error");
                    }
                }
            );

        });

        $('#modifica-applicazione-route-modal').on('submit', '#form-edit-applicazione-route', function (e) {
            e.preventDefault();

            var form = this;

            $.ajax({
                url: $(this).attr('action') + '/' + $('input[name="id"]').val(),
                type: 'POST',
                data: $(this).serialize(),
                cache: false,
                success: function (data) {

                    if (data.error != undefined) {
                        $('.form_msg', form).html('<div class="alert alert-danger alert-dismissable-modal">' + data.error + '</div>');
                        $('.form_msg > .alert', form).slideDown('fast').delay(2000).slideUp('fast');
                    } else {
                        $('.form_msg', form).html('<div class="alert alert-success alert-dismissable-modal">' + data.success + '</div>');
                        $('.form_msg > .alert', form).slideDown('fast', function () {
                            $(':input', form).not(':button, :submit, :reset, :hidden').val('').removeAttr('checked').removeAttr('selected');
                            $('#box-content-applicazioni-routes').empty().html(data.data);
                        }).delay(2000).slideUp('fast', function () {
                            $('#modifica-applicazione-route-modal').modal('hide');
                        });
                    }

                }
            });

        });
        $('#form-crea-applicazione-route').submit(function (e) {
            e.preventDefault();

            var form = this;

            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: $(this).serialize(),
                cache: false,
                success: function (data) {

                    if (data.error != undefined) {
                        $('.form_msg', form).html('<div class="alert alert-danger alert-dismissable-modal">' + data.error + '</div>');
                        $('.form_msg > .alert', form).slideDown('fast').delay(2000).slideUp('fast');
                    } else {
                        $('.form_msg', form).html('<div class="alert alert-success alert-dismissable-modal">' + data.success + '</div>');
                        $('.form_msg > .alert', form).slideDown('fast', function () {
                            $(':input', form).not(':button, :submit, :reset, :hidden').val('').removeAttr('checked').removeAttr('selected');
                            $('#box-content-applicazioni-routes').empty().html(data.data);
                        }).delay(2000).slideUp('fast', function () {
                            $('#crea-applicazione-route-modal').modal('hide');
                        });
                    }

                }
            });

        });
				
	});
		
}));