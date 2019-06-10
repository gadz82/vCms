$(document).ready(function(){
		
	var grid = $("#jqGrid"),
		selected_values = window.selected_files_var.split(',');

	var currentCheck = null,
		//variabili behavior
		currentUploadMultiple = window.multiple,
		parent_input = window.parent_input;


	$('#form_'+window.entity).find('input').on('keydown',function(e){
		var code = e.keyCode ? e.keyCode : e.which;
	    if(code == 13){
	    	$('#cerca').trigger('click');
	    }
	});
	
	$("#cerca").on('click',function(){
		grid.setGridParam({page:1});
		grid.trigger("reloadGrid");
		clearSelection();
	});
	
	$("#pulisci").on('click',function(){
		$('#form_'+entity).find('.selectpicker').selectpicker('deselectAll');
		$(':input','#form_'+entity).not(':button, :submit, :reset, :hidden').val('').removeAttr('checked').removeAttr('selected');
	});
	
	$("#esporta").on('click',function(){
		ajax_download(jqGrid_init.entityActions.search_url);
	});

	grid.on('contextmenu','.btn_grid',function(e) {
		e.preventDefault();
		var div = $(this).parent();
		var id = div.attr('id').replace('btn-grid-', '');

		if ($(this).hasClass('grid_open')) {
			var win = window.open(jqGrid_init.entityActions.edit_url+'/'+id, '_blank');
		}
	});
	
	grid.on('click','.btn_grid',function(){

		var div = $(this).parent();
		var id = div.attr('id').replace('btn-grid-','');

		if($(this).hasClass('grid_open')){

			window.open(jqGrid_init.entityActions.edit_url+'/'+id, '_blank');
			return;

		} else if($(this).hasClass('grid_select')){
			if(currentUploadMultiple){
				selected_values.push(id.toString());
				$('.grid_unselect',div).removeClass('hidden');
				$('.grid_select',div).addClass('hidden');
			} else {
				selected_values = [];
				if(currentCheck !== null){
					currentCheck.find('.grid_unselect').addClass('hidden');
					currentCheck.find('.grid_select').removeClass('hidden');
				}

				console.log(div);
				console.log(currentCheck);

				$('.grid_unselect',div).removeClass('hidden');
				$('.grid_select',div).addClass('hidden');
				selected_values.push(id.toString());
				currentCheck = div;
			}

		} else if($(this).hasClass('grid_unselect')){

			if(currentUploadMultiple){
				var iof = selected_values.indexOf(id.toString());
				if(iof > -1)selected_values.splice(iof, 1)
				$('.grid_unselect',div).addClass('hidden');
				$('.grid_select',div).removeClass('hidden');
			} else {
				selected_values = [];
				$('.grid_unselect', div).addClass('hidden');
				$('.grid_select', div).removeClass('hidden');
				currentCheck = null;
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

	var colModels = [];
	console.log(jqGrid_init.gridActions);
	colModels.push({ label:'Azioni', name:'azioni', sortable:true,
		formatter: function(cellvalue, options, rowObject){
			var r = '<div id="btn-grid-'+rowObject['id']+'">';

			if(selected_values.indexOf(rowObject['id']) !== -1){
				r+= '<div class="btn_grid grid_unselect" title="Deseleziona"><span class="fa fa-minus fa-fw"></span></div>';
				r+= '<div class="btn_grid grid_select hidden" title="Seleziona"><span class="fa fa-plus fa-fw"></span></div>';
			} else {
				r+= '<div class="btn_grid grid_unselect hidden" title="Deseleziona"><span class="fa fa-minus fa-fw"></span></div>';
				r+= '<div class="btn_grid grid_select" title="Seleziona"><span class="fa fa-plus fa-fw"></span></div>';
			}

			if(jqGrid_init.gridActions.indexOf('edit') > -1){
				r+= '<div class="btn_grid grid_open" title="Apri"><span class="fa fa-pencil fa-fw"></span></div>'
			}
			r+= '</div>';

			return r;
		}
   });
	
	for(var i in jqGrid_init.gridColumns){
		var col = {
			label : jqGrid_init.gridColumns[i].label,	
			name : jqGrid_init.gridColumns[i].name
		};
		if(typeof(jqGrid_init.gridColumns[i].sortable) !== 'undefined') col['sortable'] = jqGrid_init.gridColumns[i].sortable;
		colModels.push(col);
	}
	
	var grid_height = $(window).innerHeight() - $('.main-header').outerHeight(true) - $('.main-footer').outerHeight(true) - $('.content-wrapper .content-header').outerHeight(true) - $('#box-container-search').outerHeight(true);
	var grid_isLoad = false,
		filesData = selected_values = selected_values.filter(Number);

	
    grid.jqGrid({
    	prmNames: { search: "search", nd: null, rows: "rows", page: "page", sort: "sort", order: "order" },
        colModel: colModels,
        url: jqGrid_init.entityActions.search_url,
        postData: { form_search: function(){ return $('#form_'+entity).serialize(); }, files : filesData},
        mtype: 'POST',
        datatype: 'json',
        editurl: jqGrid_init.entityActions.save_url,
        ajaxRowOptions: { async: true },
        rowNum: 10,
        rowList:[10,20,30,50,100],
        sortorder: 'desc',
        pager: "#jqGridPager",
		//caption: tableCaption,
        gridview: true,
        loadonce: false,
        viewrecords: true,
    	altRows: true,
    	height: (grid_height > 400) ? grid_height : 400,
    	autowidth: true,
        shrinkToFit: true,
		onSortCol : clearSelection,
		onPaging : clearSelection,
		loadComplete : function(){
			if(!grid_isLoad){
				$(window).trigger('resize');
				grid_isLoad = true;

				if(!multiple && typeof(selected_values[0]) !== 'undefined' && selected_values[0] !== ''){
					currentCheck = $('#btn-grid-'+selected_values[0]);
				}

				var modal = $('#modalZoom');
				$('section.content').on('click', 'a[id^="image-zoom-"]', function () {
					modalImg = modal.find('#img01');

					modal.css({'display':'block'});
					modalImg.attr('src', $(this).data('image-zoom'));
				});

				modal.on('click', '#close-zoom-modal', function(){
					modal.css({'display':'none'});
				});
				modal.on('click', function(){
					modal.css({'display':'none'});
				});

			}
		}
    });

    function clearSelection() {
	}

    $(window).resize(function() {
    	
		var width = $('#box-grid').width();
		grid.jqGrid('setGridWidth', width-2);
	
		var height = $(window).innerHeight() - $('.main-header').outerHeight(true) - $('.main-footer').outerHeight(true) - $('.content-wrapper .content-header').outerHeight(true) - $('#box-container-grid .box-header').outerHeight(true) -  $('#box-container-search').outerHeight(true) - 140;
		height = (height > 400) ? height : 400;
		
		grid.jqGrid('setGridHeight', height);
    	
	})
	
	function ajax_download(url) {
		
	    var $iframe, iframe_doc, iframe_html;

	    if($('#download_iframe').length == 0){
	    	$iframe = $("<iframe id='download_iframe' style='display: none' src='about:blank'></iframe>").appendTo("body");
	    }else{
	    	$iframe = $('#download_iframe');
	    }

	    iframe_doc = $iframe[0].contentWindow || $iframe[0].contentDocument;
	    if(iframe_doc.document) iframe_doc = iframe_doc.document;

	    $(iframe_doc).find('form').remove();
	    
	    iframe_html = "<html><head></head><body><form method='POST' action='"+url+"'><input type='hidden' name='export' value='true'/><input type='hidden' name='form_search' value='"+$('#form_'+entity).serialize()+"'/></form></body></html>";

	    iframe_doc.open();
	    iframe_doc.write(iframe_html);
	    
	    $(iframe_doc).find('form').submit();
	    
	}
    
    jQuery(".ui-jqgrid").removeClass("ui-widget ui-widget-content");
	jQuery(".ui-jqgrid-view").children().removeClass("ui-widget-header ui-state-default");
	jQuery(".ui-jqgrid-labels, .ui-search-toolbar").children().removeClass("ui-state-default ui-th-column ui-th-ltr");
	jQuery(".ui-jqgrid-pager").removeClass("ui-state-default");
	jQuery(".ui-jqgrid").removeClass("ui-widget-content");

	jQuery(".ui-jqgrid-htable").addClass("table table-bordered table-hover");
	jQuery(".ui-pg-div").removeClass().addClass("btn btn-sm btn-primary");
	jQuery(".ui-icon.ui-icon-plus").removeClass().addClass("fa fa-plus");
	jQuery(".ui-icon.ui-icon-pencil").removeClass().addClass("fa fa-pencil");
	jQuery(".ui-icon.ui-icon-trash").removeClass().addClass("fa fa-trash-o");
	jQuery(".ui-icon.ui-icon-search").removeClass().addClass("fa fa-search");
	jQuery(".ui-icon.ui-icon-refresh").removeClass().addClass("fa fa-refresh");
	jQuery(".ui-icon.ui-icon-disk").removeClass().addClass("fa fa-save").parent(".btn-primary").removeClass("btn-primary").addClass("btn-success");
	jQuery(".ui-icon.ui-icon-cancel").removeClass().addClass("fa fa-times").parent(".btn-primary").removeClass("btn-primary").addClass("btn-danger");

	jQuery(".ui-icon.ui-icon-seek-prev").wrap('<div class="btn btn-flat"></div>');
	jQuery(".ui-icon.ui-icon-seek-prev").removeClass().addClass("fa fa-backward");
	jQuery(".ui-icon.ui-icon-seek-first").wrap('<div class="btn btn-flat"></div>');
	jQuery(".ui-icon.ui-icon-seek-first").removeClass().addClass("fa fa-fast-backward");		
	jQuery(".ui-icon.ui-icon-seek-next").wrap('<div class="btn"></div>');
	jQuery(".ui-icon.ui-icon-seek-next").removeClass().addClass("fa fa-forward");
	jQuery(".ui-icon.ui-icon-seek-end").wrap('<div class="btn btn-flat"></div>');
	jQuery(".ui-icon.ui-icon-seek-end").removeClass().addClass("fa fa-fast-forward");
	
	$('#jqGridPager_center').width('');

    
});