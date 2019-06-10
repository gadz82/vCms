// IIFE - Immediately Invoked Function Expression
(function(yourcode){
	// The global jQuery object is passed as a parameter
	yourcode(window.jQuery, window, document);
	
}(function($, window, document){

	// The $ is now locally scoped 
	// Listen for the jQuery ready event on the document
	$(function(){
		
		if($('.alert-dismissable').length){
			$('.alert-dismissable').slideDown('fast').delay(3000).slideUp('fast', function(){ $(this).remove(); });
		} 
				
		if(/Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test(navigator.userAgent)){
			$('.selectpicker').selectpicker('mobile');
	    }else{
	    	$('.selectpicker').selectpicker();
	    }
		
		var substringMatcher = function(strs){
			return function findMatches(q,cb){
				var matches, substrRegex;
				matches = [];
				substrRegex = new RegExp(q, 'i');
			 
				$.each(strs, function(i, str){
					if(substrRegex.test(str)){
						matches.push({ value: str });
					}
				});
			 
				cb(matches);
			};
		};
			 
		var list_menu = $('.sidebar-menu').find('li > a > span.menu_desc').map(function(){ return $(this).text(); }).get().sort();
		$.unique(list_menu);
		
		$('.typeahead').typeahead({
			hint: true,
			highlight: true,
			minLength: 1
		},
		{
			name: 'list_menu',
			displayKey: 'value',
			source: substringMatcher(list_menu)
		}).on('typeahead:selected',function(evt,data){
			
		    $('.sidebar-menu').find('li.menu_item').hide();
		    $('.sidebar-menu').find('li.header').hide();
		    
		    var item = $('.sidebar-menu').find('li > a > span.menu_desc:contains("'+data.value+'")');
		    var menu_item = item.closest('li.menu_item');
		    menu_item.each(function(){
		    	$(this).show();
		    	$(this).prevAll('li.header').first().show();
		    });
		    
		}).on('typeahead:closed',function(){
		    if($(this).val() == '') $('.sidebar-menu').find('li').show(); 
		});
		
		
		$('.sidebar-form .typeahead').focusin(function(){
			$(this).closest('.sidebar-form').addClass('focus');
		}).focusout(function(){
			$(this).closest('.sidebar-form').removeClass('focus');
		}).on('input',function(e){
			if($(e.target).val() == '') $('.sidebar-menu').find('li').show(); 
		});
		
		
	});
	
	$('body').on('click', '.box-header > span', function(){
		$(this).parent().toggleClass('box-close').next().slideToggle('fast', function(){
			$(window).trigger('resize');
		});		
	});
	
    $('.date-format:input[type="text"]').datepicker({
    	format: "yyyy-mm-dd",
    	todayBtn: "linked",
    	clearBtn: true,
    	language: "it",
    	autoclose: true,
    	todayHighlight: true
    });
    
    $('input[type="checkbox"], input[type="radio"]').on('ifCreated', function(e){
    	if($(this).hasClass('icheck-group-addon')){
    		if(!$(this).is(':checked')){    			
    			$('.icheck-state:input[type="text"]',$(this).closest('.input-group.icheck')).val('').attr('disabled','disabled');
    		}
    	}
    }).iCheck({
    	checkboxClass: 'icheckbox_square-green',
    	radioClass: 'iradio_square-green',
    	tap: true,
    	inheritClass: true,
    	increaseArea: '20%' // optional
    });    
        
    $('.input-group input[type="checkbox"]').on('ifChecked', function(e){
    	$('.icheck-state:input[type="text"]',$(this).closest('.input-group.icheck')).val('').removeAttr('disabled');
	}).on('ifUnchecked', function(e){
    	$('.icheck-state:input[type="text"]',$(this).closest('.input-group.icheck')).val('').attr('disabled','disabled');
  	});
	
    $("[data-toggle='offcanvas']").click(function (e) {
    	$(this).animate({
			'opacity':'1'
		},{
			duration: 500,
			step: function(now,fx){
				$(window).trigger('resize');
			},
			complete: function(){
				$(window).trigger('resize');
			}
		});
	 });
	 
	 $('.range-datepicker:input[type="text"]').daterangepicker({
		 locale: {
				 format: 'DD-MM-YYYY',
				 separator: ' - ',
				 applyLabel: 'Applica',
				 cancelLabel: 'Pulisci',
				 weekLabel: 'W',
				 customRangeLabel: 'Personalizza',
				 daysOfWeek: moment.weekdaysMin(),
				 monthNames: moment.monthsShort(),
				 firstDay: moment.localeData().firstDayOfWeek()
		 },
		 applyClass: 'btn-flat btn-success',
		 cancelClass: 'btn-flat btn-warning',
		 autoApply: false,
		 autoUpdateInput: false,
		 opens: 'left',
		 ranges: {
			 'Oggi': [moment(), moment()],
			 'Ieri': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
			 'Ultimi 7 giorni': [moment().subtract(6, 'days'), moment()],
			 'Ultimi 30 giorni': [moment().subtract(29, 'days'), moment()],
			 'Questo mese': [moment().startOf('month'), moment().endOf('month')],
			 'Mese scorso': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
		 }
	 });
	 
	 $('.range-datepicker:input[type="text"]').on('apply.daterangepicker', function(ev, picker) {		 
		 $(this).val('Dal ['+picker.startDate.format('YYYY-MM-DD')+'] al ['+picker.endDate.format('YYYY-MM-DD')+']');
	});
	
	$('.range-datepicker:input[type="text"]').on('cancel.daterangepicker', function(ev, picker) {
		$(this).val('');
	});

	/**
	 * File Manager
     */
	$("#file-manager-control").on('click', function(){
		$('#iframe-filemanager-list').attr('src', '/admin/files/iframeFileManager');
	});

	$.receiveMessage(function(e){
		var post = JSON.parse('{"' + decodeURI(e.data).replace(/"/g, '\\"').replace(/&/g, '","').replace(/=/g,'":"') + '"}');
		if(typeof(post.action) !== 'upload_complete'){
			$('#iframe-filemanager-list').attr( 'src', function ( i, val ) { return val; });
		}
	});
	var modal = $('#modalZoom');
	$('section.content').on('click', 'img', function () {
		var img = $(this),
			modalImg = modal.find('#img01');

		modal.css({'display':'block'});
		if($(this).data('zoom-src') !== undefined){
			modalImg.attr('src', $(this).data('zoom-src'));
		} else {
			modalImg.attr('src', '/files/'+$(this).attr('src').split('/').pop());
		}
	});
	modal.on('click', '#close-zoom-modal', function(){
		modal.css({'display':'none'});
	});
	modal.on('click', function(){
		modal.css({'display':'none'});
	});
	console.log(window.location);

	$('ul#appSwitcher > li > a:not(.active)').on('click', function(e){
		e.preventDefault();
		var id_app = $(this).data('idapp');

		if($('select[name="Posts[id_applicazione]"]').length > 0 || $('select[name="id_applicazione"]').length > 0){
			if(window.location.pathname.includes('new') && !window.location.pathname.includes('edit')){
				swal({
						title: "APP SWITCH",
						text: "Perderai eventuali dati già inseriti, vuoi procedere?",
						type: "warning",
						showCancelButton: true,
						confirmButtonColor: "#DD6B55",
						cancelButtonColor: "#DD6B55",
						confirmButtonText: "Si procedi",
						cancelButtonText: "No fermati",
						closeOnConfirm: false,
						closeOnCancel: true
					},
					function(isConfirm){
						if(isConfirm){
							callAppSwitch(true);
						}
					});
			} else if(window.location.pathname.includes('edit')) {
				swal({
					title: "APP SWITCH",
					text: "Non puoi cambiare App mentre stai modificando un contenuto legato ad un'altra Applicazione.",
					type: "info"
				});
			} else {
				callAppSwitch(false);
			}
		} else {
			callAppSwitch(false);
		}

		function callAppSwitch(reload){

			$.ajax({
				url: '/admin/index/setCurrentApp',
				type: 'POST',
				data: {"id_applicazione": id_app},
				cache: false,
				success: function (data) {
					window.location.reload();
					return;
				}
			});
		}
	});

}));