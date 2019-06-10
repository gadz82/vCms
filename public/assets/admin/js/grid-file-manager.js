$(document).ready(function () {

    var grid = $("#jqGrid");
    var grid_subgrid = $("#jqGridDetails");

    $('#form_' + entity).find('input').on('keydown', function (e) {
        var code = e.keyCode ? e.keyCode : e.which;
        if (code == 13) {
            $('#cerca').trigger('click');
        }
    });

    $("#cerca").on('click', function () {
        grid.setGridParam({page: 1});
        grid.trigger("reloadGrid");
        clearSelection();
    });

    $("#pulisci").on('click', function () {
        $('#form_' + entity).find('.selectpicker').selectpicker('deselectAll');
        $(':input', '#form_' + entity).not(':button, :submit, :reset, :hidden').val('').removeAttr('checked').removeAttr('selected');
    });

    $("#esporta").on('click', function () {
        ajax_download(jqGrid_init.entityActions.search_url);
    });

    grid.on('contextmenu', '.btn_grid', function (e) {
        e.preventDefault();
        var div = $(this).parent();
        var id = div.attr('id').replace('btn-grid-', '');

        if ($(this).hasClass('grid_open')) {
            var win = window.open(jqGrid_init.entityActions.edit_url + '/' + id, '_blank');
        }
    });

    grid.on('click', '.btn_grid', function () {

        var div = $(this).parent();
        var id = div.attr('id').replace('btn-grid-', '');

        if ($(this).hasClass('grid_open')) {

            window.location.href = jqGrid_init.entityActions.edit_url + '/' + id;

        } else if ($(this).hasClass('grid_edit')) {

            $('.grid_save, .grid_cancel', div).show();
            grid.jqGrid('editRow', id);
            $(this).hide();

        } else if ($(this).hasClass('grid_detail')) {

            $("#file-embed-modal").on('show.bs.modal', function () {
                var dynamicContentContainer = $("#file-embed-modal").find('#modal-body-embed');
                dynamicContentContainer.html("");
                $.ajax({
                    url: '/admin/files/getEmbedCode/' + id,
                    type: 'GET',
                    cache: false,
                    success: function (data) {
                        if (data.success) {
                            dynamicContentContainer.html(data.content);
                        } else {
                            dynamicContentContainer.html('<h3 class="error">Errore recupero risorsa</h3>');
                        }
                    }
                });
            });


        } else if ($(this).hasClass('grid_delete')) {

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
                function (isConfirm) {
                    if (isConfirm) {
                        swal("Cancellato!", "La riga selezionato è stata cancellata.", "success");
                        window.location.href = jqGrid_init.entityActions.delete_url + '/' + id;
                    } else {
                        swal("Cancellazione annullata", "La riga selezionata è salva :)", "error");
                    }
                }
            );
        }

        function resetInlineEdit(id) {
            $('.grid_save, .grid_cancel', div).hide();
            $('.grid_edit', div).show();
            grid.jqGrid('restoreRow', id);
        }

    });

    grid_subgrid.on('click', '.btn_grid', function () {
        var div = $(this).parent();
        var id = div.attr('id').replace('btn-grid-', '');

        if ($(this).hasClass('grid_open')) {
            window.location.href = jqGrid_init.entityActions.edit_url + '/' + id;
        } else if ($(this).hasClass('grid_delete')) {
            if (confirm("Confermi di voler eliminare questa riga?")) {
                window.location.href = jqGrid_init.entityActions.delete_url + '/' + id;
            }
        }

    });

    var colModels = [];
    colModels.push({
        label: 'Azioni', name: 'azioni', sortable: false,
        formatter: function (cellvalue, options, rowObject) {
            var r = '<div id="btn-grid-' + rowObject['id'] + '">';
            if (jqGrid_init.gridActions.indexOf('edit') > -1) {
                r += '<div class="btn_grid grid_open" title="Apri"><span class="fa fa-pencil fa-fw"></span></div>';
                r += '<div class="btn_grid grid_detail" ' +
                    'data-toggle="modal" data-target="#file-embed-modal"' +
                    'title="Dettaglio" data-file-url="' + rowObject['filename'] + '" data-file-alt="' + rowObject['alt'] + '"><span class="fa fa-code fa-fw"></span></div>';
            }
            if (jqGrid_init.gridActions.indexOf('delete') > -1) {
                r += '<div class="btn_grid grid_delete" title="Elimina"><span class="fa fa-trash fa-fw"></span></div>';
            }

            r += '</div>';

            return r;
        }
    });

    for (var i in jqGrid_init.gridColumns) {
        var col = {
            label: jqGrid_init.gridColumns[i].label,
            name: jqGrid_init.gridColumns[i].name
        };
        if (typeof(jqGrid_init.gridColumns[i].editable) !== 'undefined' && jqGrid_init.gridColumns[i].editable) col['editable'] = jqGrid_init.gridColumns[i].editable;
        if (typeof(jqGrid_init.gridColumns[i].type) !== 'undefined') col['edittype'] = jqGrid_init.gridColumns[i].type;
        if (typeof(jqGrid_init.gridColumns[i].editoptions) !== 'undefined') col['editoptions'] = jqGrid_init.gridColumns[i].editoptions;
        if (typeof(jqGrid_init.gridColumns[i].editrules) !== 'undefined') col['editrules'] = jqGrid_init.gridColumns[i].editrules;
        if (typeof(jqGrid_init.gridColumns[i].align) !== 'undefined' && jqGrid_init.gridColumns[i].align) col['align'] = jqGrid_init.gridColumns[i].align;
        if (typeof(jqGrid_init.gridColumns[i].sortable) !== 'undefined') col['sortable'] = jqGrid_init.gridColumns[i].sortable;
        colModels.push(col);
    }

    var grid_height = $(window).innerHeight() - $('.main-header').outerHeight(true) - $('.main-footer').outerHeight(true) - $('.content-wrapper .content-header').outerHeight(true) - $('#box-container-search').outerHeight(true);
    var grid_isLoad = false

    grid.jqGrid({
        prmNames: {search: "search", nd: null, rows: "rows", page: "page", sort: "sort", order: "order"},
        colModel: colModels,
        url: jqGrid_init.entityActions.search_url,
        postData: {
            form_search: function () {
                return $('#form_' + entity).serialize();
            }
        },
        mtype: 'POST',
        datatype: 'json',
        editurl: jqGrid_init.entityActions.save_url,
        ajaxRowOptions: {async: true},
        rowNum: 10,
        rowList: [10, 20, 30, 50, 100],
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
        onSortCol: clearSelection,
        onPaging: clearSelection,
        loadComplete: function () {
            if (!grid_isLoad) {
                $(window).trigger('resize');
                grid_isLoad = true;
            }
        }
    });

    function clearSelection() {
    }

    $(window).resize(function () {

        var width = $('#box-grid').width();
        grid.jqGrid('setGridWidth', width - 2);

        var height = $(window).innerHeight() - $('.main-header').outerHeight(true) - $('.main-footer').outerHeight(true) - $('.content-wrapper .content-header').outerHeight(true) - $('#box-container-grid .box-header').outerHeight(true) - $('#box-container-search').outerHeight(true) - 140;
        height = (height > 400) ? height : 400;

        grid.jqGrid('setGridHeight', height);

    })

    function ajax_download(url) {

        var $iframe, iframe_doc, iframe_html;

        if ($('#download_iframe').length == 0) {
            $iframe = $("<iframe id='download_iframe' style='display: none' src='about:blank'></iframe>").appendTo("body");
        } else {
            $iframe = $('#download_iframe');
        }

        iframe_doc = $iframe[0].contentWindow || $iframe[0].contentDocument;
        if (iframe_doc.document) iframe_doc = iframe_doc.document;

        $(iframe_doc).find('form').remove();

        iframe_html = "<html><head></head><body><form method='POST' action='" + url + "'><input type='hidden' name='export' value='true'/><input type='hidden' name='form_search' value='" + $('#form_' + entity).serialize() + "'/></form></body></html>";

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