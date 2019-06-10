// IIFE - Immediately Invoked Function Expression
(function(code){
    // The global jQuery object is passed as a parameter
    code(window.jQuery, window, document);

}(function($, window, document){
    var cm;
    $(document).ready(function(){
        cm = CodeMirror.fromTextArea(document.getElementById('content'), {
            mode: $('#id_tipologia_block').val() == 1 ? "text/html" : ($('#id_tipologia_block').val() == 2 ? "text/css" : { name : 'javascript', json: true }),
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
    });

    $('#id_tipologia_block').on('change', function(){
        cm.toTextArea();
        cm = CodeMirror.fromTextArea(document.getElementById('content'), {
            mode: $(this).val() == 1 ? "text/html" : ($('#id_tipologia_block').val() == 2 ? "text/css" : { name : 'javascript', json: true }),
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

    });

    $(window).keydown(function (e){
        if ((e.metaKey || e.ctrlKey) && e.keyCode == 83) { /*ctrl+s or command+s*/
            $('#form-edit').find('input[type="submit"]').trigger('click');
            e.preventDefault();
            return false;
        }
    });
}));

