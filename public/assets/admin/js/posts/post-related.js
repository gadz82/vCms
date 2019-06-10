(function(init){
    init(window.jQuery, window, document);
}(function($, window, document) {
    $(document).ready(function () {
        $('input[name^="meta[Contenuti Correlati]["]').each(function () {
            var current_input = $(this),
                id_post_type = current_input.data('id_post_type'),
                select = $('<select class="form-control selectpicker" id="post-list-'+id_post_type+'" data-live-search="true" multiple="true"></select>');

            var searchq = {
                id_tipologia_stato : 1,
                id_tipologia_post : id_post_type,
                search : false,
                rows : 5,
                page : 1,
                sort : 'id',
                order : 'desc'
            };
            if(current_input.val().length > 0){
                searchq['id'] = current_input.val().split(',');
            }

            $.ajax({
                url: '/admin/posts/search',
                type: 'POST',
                data: searchq,
                cache: false,
                success : function(res){
                    if(res.hasOwnProperty('records') && res.hasOwnProperty('rows')){
                        var len = res.rows.length;
                        for(var i = 0; i < len; i++){
                            var curr = res.rows[i];
                            var o = new Option(curr.titolo, curr.id);
                            if(searchq['id'] !== undefined && searchq['id'].indexOf(curr.id) > -1){
                                o.selected = true;
                            }
                            select.append(o);
                        }
                        current_input.after(select);
                        initSelAjax(select, current_input, id_post_type);
                    } else {
                        current_input.after(select);
                        select.selectpicker();
                    }
                }
            });

        });

        function initSelAjax(select, current_input, id_post_type){
            var cur_val = null;
            if(current_input.val().length > 0){
                cur_val = current_input.val().split(',');
            }
            select.selectpicker({
                liveSearch: true
            })
            .ajaxSelectPicker({
                emptyRequest : true,
                locale : {
                    currentlySelected : 'Selezione Corrente',
                    emptyTitle : 'Inizia a scrivere',
                    errorText : 'Impossibile recuperare i risultati',
                    searchPlaceholder : 'Cerca...',
                    statusInitialized : 'Inizia a scrivere una query di ricerca...',
                    statusNoResults : 'Nessun Risultato',
                    statusSearching : 'Sto Cercando...',
                    currentlySelected : 'Selezione Recente'
                },
                ajax: {
                    url: '/admin/posts/search',
                    data: function () {
                        var params = {
                            titolo: '{{{q}}}',
                            id_tipologia_post : id_post_type,
                            id_tipologia_stato : 1,
                            search : false,
                            rows : 150,
                            page : 1,
                            order : 'desc'
                        };

                        return params;
                    }
                },
                preprocessData: function(data){
                    var rs = [];
                    if(data.hasOwnProperty('records') && data.hasOwnProperty('rows')){
                        var len = data.rows.length;
                        for(var i = 0; i < len; i++){
                            var curr = data.rows[i];
                            rs.push(
                                {
                                    'value': curr.id,
                                    'text': curr.titolo,
                                    'disabled': false
                                }
                            );
                        }
                    }
                    return rs;
                },
                preserveSelected: true
            });
            select.on('change', function(){
                var val = select.val();
                if(typeof(val) !== 'undefined'){
                    if(val == null || val.length == 0){
                        current_input.val('');
                    } else {
                        var string_value = select.val().join(',')
                        current_input.val(string_value);
                    }
                }
            });
        }
    });
}));
