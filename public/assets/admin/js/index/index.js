// IIFE - Immediately Invoked Function Expression
(function(yourcode){
	// The global jQuery object is passed as a parameter
	yourcode(window.jQuery, window, document);
	
}(function($, window, document){
	$(document).ready(function(){
		$('table#datatable').DataTable({
			paging : true,
			searching: true,
			ordering: true,
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
			}
		})
	});

	$('#btn-cron').on('click', function(){
		swal({
				title: "Attenzione!",
				text: "Sei sicuro di voler avviare l'importazione?",
				type: "warning",
				showCancelButton: true,
				confirmButtonColor: "#DD6B55",
				confirmButtonText: "Si",
				cancelButtonText: "No",
				closeOnConfirm: true,
				closeOnCancel: true
			},
			function(isConfirm){
				if(isConfirm){
					_import();
				}
			}
		);
	});

	$('#btn-sync').on('click', function(){
		swal({
				title: "Attenzione!",
				text: "Sei sicuro di voler avviare la sincronizzazione?",
				type: "warning",
				showCancelButton: true,
				confirmButtonColor: "#DD6B55",
				confirmButtonText: "Si",
				cancelButtonText: "No",
				closeOnConfirm: true,
				closeOnCancel: true
			},
			function(isConfirm){
				if(isConfirm){
					_sync();
				}
			}
		);
	});

	$('#btn-purge').on('click', function(){
		swal({
				title: "Attenzione!",
				text: "Sei sicuro di voler avviare la pulizia?",
				type: "warning",
				showCancelButton: true,
				confirmButtonColor: "#DD6B55",
				confirmButtonText: "Si",
				cancelButtonText: "No",
				closeOnConfirm: true,
				closeOnCancel: true
			},
			function(isConfirm){
				if(isConfirm){
					_purge();
				}
			}
		);
	});

	function _import(){
		var btn = $('#btn-cron'),
			spinner = $('<span class="btn"><i class="fa fa-refresh fa-spin fa-3x fa-fw"></i><span class="sr-only">Importazione...</span></span>');
		btn.addClass('hidden').after(spinner);
		$.ajax({
			url: '/cron/cron/import',
			cache: false,
			success : function(){
				spinner.remove();
				btn.removeClass('hidden');
			},
			error : function(){
				swal({
					title: "Attenzione!",
					text: "Errore durante l'importazione",
					type: "error"
				});
				spinner.remove();
				btn.removeClass('hidden');
			}
		});

	}

	function _sync(){
		var btn = $('#btn-sync'),
			spinner = $('<span class="btn"><i class="fa fa-refresh fa-spin fa-3x fa-fw"></i><span class="sr-only">Importazione...</span></span>');
		btn.addClass('hidden').after(spinner);

		$.ajax({
			url: '/cron/cron/sync',
			cache: false,
			success : function(){
				spinner.remove();
				btn.removeClass('hidden');
			},
			error : function(){
				swal({
					title: "Attenzione!",
					text: "Errore durante la sincronizzazione",
					type: "error"
				});
				spinner.remove();
				btn.removeClass('hidden');
			}
		});

	}

	function _purge(){
		var btn = $('#btn-purge'),
			spinner = $('<span class="btn"><i class="fa fa-refresh fa-spin fa-3x fa-fw"></i><span class="sr-only">Importazione...</span></span>');
		btn.addClass('hidden').after(spinner);

		$.ajax({
			url: '/cron/cron/purge',
			cache: false,
			success : function(){
				spinner.remove();
				btn.removeClass('hidden');
			},
			error : function(){
				swal({
					title: "Attenzione!",
					text: "Errore durante la pulizia",
					type: "error"
				});
				spinner.remove();
				btn.removeClass('hidden');
			}
		});

	}
}));