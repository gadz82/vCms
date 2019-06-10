//IIFE - Immediately Invoked Function Expression
(function(yourcode){
	// The global jQuery object is passed as a parameter
	yourcode(window.jQuery, window, document);
	
}(function($, window, document){

	// The $ is now locally scoped 
	// Listen for the jQuery ready event on the document
	$(function(){
		
		$('input[type="checkbox"]').iCheck({
    		checkboxClass: 'icheckbox_square-blue',
    		tap: true,
    		inheritClass: true,
    		increaseArea: '20%' // optional
    	});
		
		$('#form-builder').on('submit', function(ev){
			
			ev.preventDefault();
			
			swal({
				  title: "Attenzione!",
				  text: "Sei sicuro di voler dare inizio alla crazione dello scaffold?",
				  type: "warning",
				  showCancelButton: true,
				  confirmButtonColor: "#DD6B55",
				  confirmButtonText: "Si, crea!",
				  cancelButtonText: "No, scusa!",
				  closeOnConfirm: false,
				  closeOnCancel: false,
				  showLoaderOnConfirm: true
				},
				function(isConfirm){
					
					if(isConfirm){
						
						$.ajax({
				        	url : window.location.href,
				        	data: $('#form-builder').serialize(),
							type : 'POST',
							success : function(data){
								swal("Fatto!", "Lo scaffold è stato creato con successo.", "success");
				        		//$(':input','#form-builder').not(':button, :submit, :reset, :hidden :checkbox').val('');
				        		//$('input[type="checkbox"]').iCheck('uncheck');
							}
				        });
						
					}else{
						swal("Creazione annullata", "Non ha fatto danni :)", "error");
					}
					
				});
		});
		
	});
		
}));