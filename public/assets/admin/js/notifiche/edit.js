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

		$('select[name="tipologie_user[]"]').selectpicker("val", selected_tipologie_user);
		$('select[name="specific_user[]"]').selectpicker("val", selected_user);


		$('#invio-notifica').on('click', function(){
			if(!$('input#notifica_push').parent().hasClass("checked")){
				swal({
					title: 'Controllo sui dati',
					text: 'Metti la spunta su "Invia notifica Puhs" e Salva prima di forzare l\'invio della notifica',
					type: "warning"
				});
				return;
			}
			var button = $(this);
			button.attr('disabled', 'disabled');
			$.ajax({
				url: '/admin/notifiche/enqueuecheck',
				type: 'POST',
				data: { "id_notifica":$('#id').val()},
				cache: false,
				success : function(data){
					if(data.success){
						swal({
								title: "Confermi invio notifica a "+data.content+" utenti?",
								text: "Verrà messo in coda l'invio di una notifica push. Non potrai più modificare i contenuti e i destinatari.",
								type: "info",
								showCancelButton: true,
								confirmButtonColor: "#DD6B55",
								confirmButtonText: "Si",
								cancelButtonText: "No",
								closeOnConfirm: true,
								closeOnCancel: true
							},
							function(isConfirm) {
								if (isConfirm) {
									$.ajax({
										url: '/admin/notifiche/enqueue',
										type: 'POST',
										data: {"id_notifica": $('#id').val()},
										cache: false,
										success: function (data) {
											if (data.success) {
												swal({
													title: "Invio in corso",
													text: data.content,
													type: "success"
												});
												button.hide();
												$form.find('input').each(function(){
													$(this).attr('disabled', 'disabled');
												});
											} else {
												swal({
													title: "Errore",
													text: data.content,
													type: "error"
												});
												button.removeAttr('disabled');
											}
										}
									});
								}
							}
						);
					} else {
						swal({
							title: "Errore",
							text: data.content,
							type: "warning"
						});
						button.removeAttr('disabled');
					}
				}
			});



		})



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
				
	});
		
}));