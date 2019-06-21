// IIFE - Immediately Invoked Function Expression
(function(yourcode){
	// The global jQuery object is passed as a parameter
	yourcode(window.jQuery, window, document);
	
}(function($, window, document){

	// The $ is now locally scoped 
	// Listen for the jQuery ready event on the document
	$(function(){
		var bfl = $('#box-file-list');

		var options = {
			paging : true,
			searching: true,
			ordering: true,
			order: [[ 0, 'desc' ],[1, 'desc']],
			autowidth: true,
			language: {
				"sEmptyTable":     "Nessun record da visualizzare",
				"sInfo":           "Visualizzati _START_ - _END_ di _TOTAL_",
				"sInfoEmpty":      "Nessun record",
				"sInfoFiltered":   "(su _MAX_ elementi)",
				"sInfoPostFix":    "",
				"sInfoThousands":  ",",
				"sLengthMenu":     "Visualizza _MENU_ elementi",
				"sLoadingRecords": "Caricamento...",
				"sProcessing":     "Elaborazione...",
				"sSearch":         "Filtra: ",
				"sZeroRecords":    "La ricerca non ha portato alcun risultato.",
				"oPaginate": {
					"sFirst":      "<i class='fa fa-fast-backward'></i>",
					"sPrevious":   "<i class='fa fa-backward'></i>",
					"sNext":       "<i class='fa fa-forward'></i>",
					"sLast":       "<i class='fa fa-fast-forward'></i>"
				},
				"oAria": {
					"sSortAscending":  ": attiva per ordinare la colonna in ordine crescente",
					"sSortDescending": ": attiva per ordinare la colonna in ordine decrescente"
				}
			},
			drawCallback: function(){
				setTimeout(function(){
					$('img.lazy').lazy({
						effect: "fadeIn",
						effectTime: 500,
						threshold: 0
					});
				},500);
				$('input.select-immagini').on('ifChanged', function (event) { $(event.target).trigger('change'); });
			}
		};
		dataTable = $('table#table-caricamento-posts-files').DataTable(options);
		$('#table-caricamento-posts-files').on('keyup', 'input[id^="input-edit-file-"]', function () {
			var i = $(this);
			i.next('button[id^="button-edit-file-"]').removeClass('disabled')
		});

		$('#table-caricamento-posts-files').on('click', 'button[id^="button-edit-file-"]', function () {
			var btn = $(this),
				id_file = btn.data('file-id'),
				name = btn.prev().attr('name'),
				val = btn.prev().val();

			$.ajax({
				url: '/admin/files/updateFileInfo/'+id_file,
				type: 'POST',
				data: {
					key : name,
					value : val
				},
				cache: false,
				success: function(data){
					btn.addClass('disabled');
				}
			});
		});
		/**
		 * Contenitore utilizzato in caso di comportamento radio
		 */
		var currentCheck = null,
			//variabili behavior
			selected_values = window.selected_files_var.split(','),
			currentUploadMultiple = window.multiple,
			parent_input = window.parent_input;

		if(!currentUploadMultiple && selected_values.length > 0){
			currentCheck = $('#checkbox-file-'+selected_values[0]);
		}

		$('#table-caricamento-posts-files').on( 'change', 'input.select-immagini', function(){
			var isChecked = $(this).is(':checked'),
				file_id = $(this).data('file-id').toString();

			/**
			 * In caso di multi-immagine
			 */
			if(currentUploadMultiple){
				if(isChecked){
					selected_values.push(file_id.toString());
				} else {
					var iof = selected_values.indexOf(file_id.toString());
					if(iof > -1)selected_values.splice(iof, 1)
				}
			} else {
				/**
				 * In caso di immagine singola
				 */
				selected_values = [];
				if(isChecked){
					if(currentCheck !== null){
						currentCheck.iCheck('uncheck');
						currentCheck.removeAttr('checked');
					}
					selected_values.push(file_id.toString());
					currentCheck = $(this);
				}
			}

			selected_values = selected_values.filter(Number);
			window.selected_files_var = JSON.stringify(selected_values);
			/**
			 * Aggiornamento blocco thumbs
			 */
			var parent_url = (window.location != window.parent.location) ? document.referrer : document.location.href;
			$.postMessage({action : 'changed_images', value  : selected_values.join('|'), input : parent_input} , parent_url, parent );
		});
		

	});
		
}));