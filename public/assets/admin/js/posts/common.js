(function(init){
	init(window.jQuery, window, document);
}(function($, window, document){
	$(document).ready(function(){
		var $form = $('form[name="form-edit"]');
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

		$('select[name="Posts[id_tipologia_post]"], select[name="Posts[id_applicazione]"]').on('change', function(){
			window.location.href = '/admin/posts/new/'+$('select[name="Posts[id_tipologia_post]"]').val()+'/'+$('select[name="Posts[id_applicazione]"]').val();
		});



		if(filtersToWatch !== undefined && filtersToWatch.length > 0){

			$('select[name^="filtri["]').on('change', function(){
				var selectedVal = $(this).find('option:selected'),
					selected = [];

				selectedVal.each(function(index, option){
					selected.push($(option).val());
				});
				var sp = $(this).attr('name')
					.replace('filtri', '')
					.replace(/]/g, '')
					.split('[')
					.filter(Boolean);

				if(filtersToWatch.indexOf(sp[1]) > -1){
					$.ajax({
						url: '/admin/filtri_valori/getChildrenFilterValues',
						type: 'POST',
						data: { "id_filtri_valore":selected, "id_filtro": sp[1]},
						cache: false,
						success : function(data){
							for(var inputName in data){
								var sel = $('select[name="'+inputName+'"]');
								if(sel.length > 0) sel.find('option').remove();
								if(sel.length > 0){
									if(sel.attr('required') == undefined || sel.attr('required') !== '0'){
										sel.append('<option value>---</option>');
									}
									for(var i in data[inputName]){
										sel.append('<option value="'+data[inputName][i]['id']+'">'+data[inputName][i]['value']+'</option>');
									}
								}
								sel.selectpicker('refresh');
							}
						}
					});
				}

			});

			$('select[name^="filtri["]').trigger('change');
		}

		/**
		 * Gestione textarea contenuto con switch tra editor wysiwyg e editor html5
         */

		var toggleButton = $('<div class="col-xs-12 text-right" style="margin-top:10px;"><span class="btn btn-primary btn-small" id="toggle-editor">Wysiwyg/Html</span></div>');
		$('.textarea-container').after(toggleButton);

		var mode = 'wysiwyg'; //wysiwyg || html
		var cm;

		//init
		initWysiwyg($('textarea.wysiwyg').val());

		toggleButton.on('click', function(){
			if(mode == 'wysiwyg'){
				initHtmlEditor($('textarea.wysiwyg').val());
				mode = 'html';
			} else {
				if(confirm('Attenzione, modificando la modalità dell\'editor potresti perdere gli attributi html impostati')){
					cm.toTextArea();
					initWysiwyg($('textarea.wysiwyg').val());
					mode = 'wysiwyg';
				}
			}
		});

		function initWysiwyg(text){
			var text_fill = text || '';
			$('textarea.wysiwyg').remove();
			$('.textarea-container').html('<textarea id="Posts[testo]" name="Posts[testo]" class="form-control wysiwyg" rows="12" cols="50" grid_class="col-xs-12 textarea-container" required="required" placeholder="Testo">'+text_fill+'</textarea>');
			$('textarea.wysiwyg').wysihtml5('deepExtend',{
				toolbar : {
					'font-styles': true,
					'emphasis': true,
					'html' : true,
					'lists': true,
					'image': true,
					'link': true,
					'blockquote' : true
				},
				autoLink: false,
				parserRules: {
					classes: 'any'
				},
				'events': {
					'load': function() {
						/*var some_wysi = $('#box-content-descrizione textarea').data('wysihtml5').editor;
						 var str = some_wysi.getValue(true).replace(/(<br>)|(&nbsp;)/g, ' ').replace(/<\/?[^>]+(>|$)/g, '');
						 var count = str.length > 0 ? str.length-1 : 0;
						 $('#box-content-descrizione-counter').html(count+'/800');

						 $(some_wysi.composer.element).bind('keyup', function(){
						 str = some_wysi.getValue(true).replace(/(<br>)|(&nbsp;)/g, ' ').replace(/<\/?[^>]+(>|$)/g, '');
						 count = str.length > 0 ? str.length-1 : 0;
						 $('#box-content-descrizione-counter').html(count+'/800');
						 });*/
					}
				}
			});
		}

		var tHtml = $('textarea#html_code');
		if(tHtml.length > 0){
			var cmH = CodeMirror.fromTextArea(document.getElementById('html_code'), {
				mode: "text/html",
				extraKeys: {
					"Ctrl-Space": "autocomplete",
					"F11": function(cmH) {
						cmH.setOption("fullScreen", !cmH.getOption("fullScreen"));
					},
					"Esc": function(cmH) {
						if (cmH.getOption("fullScreen")) cmH.setOption("fullScreen", false);
					}
				},
				lineNumbers: true,
				theme : 'dracula',
				matchTags: {bothTags: true},
				autoCloseBrackets : true,
				autoCloseTags : true
			});
		}

		function initHtmlEditor(text){
			var text_fill = text || '';
			$('textarea.wysiwyg').remove();
			$('.textarea-container').html('<label for="Posts[testo]">Testo</label><textarea id="Posts[testo]" name="Posts[testo]" class="form-control wysiwyg" rows="12" cols="50" grid_class="col-xs-12 textarea-container" required="required" placeholder="Testo">'+text_fill+'</textarea>');

			cm = CodeMirror.fromTextArea(document.getElementById('Posts[testo]'), {
				mode: "text/html",
				extraKeys: {
					"Ctrl-Space": "autocomplete",
					"F11": function(cm) {
						cm.setOption("fullScreen", !cm.getOption("fullScreen"));
					},
					"Esc": function(cm) {
						if (cm.getOption("fullScreen")) cm.setOption("fullScreen", false);
					}
				},
				lineNumbers: true,
				theme : 'dracula',
				matchTags: {bothTags: true},
				autoCloseBrackets : true,
				autoCloseTags : true
			});
		}

	});
}));