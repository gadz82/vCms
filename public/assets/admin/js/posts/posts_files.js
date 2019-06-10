(function(init){
    init(window.jQuery, window, document);
}(function($, window, document){
    $(document).ready(function(){

        /** lista file **/
        if($('#box-file-list').length > 0){
            newInit();
        }

        function newInit(){
            /**
             * Inizializzazione immagini in fase di edit
             */
            $('input[id^="meta"]').each(function(){
                if($(this).data('load-thumbnails') !== undefined && typeof($(this).attr('data-load-thumbnails') !== 'undefined')){
                    var fName = $(this).attr('name'),
                        val = $(this).data('load-thumbnails').toString();
                    if(val !== ''){

                        var thumbIds =  val.split(',');
                        if(thumbIds.length > 0){
                            console.log(thumbIds);
                            for(var x in thumbIds){

                                $.ajax({
                                    url: '/admin/files/getFile/'+thumbIds[x],
                                    type: 'GET',
                                    cache: false,
                                    success: function(data){
                                        $('div[id="spinner-'+fName+'"]').remove();
                                        if(data.success){
                                            $('span[id="'+fName+'-images-list"]').append('<img src="/files/thumbnail/'+data.content.filename+'">&nbsp;');
                                        }
                                    }
                                });

                            }
                        } else {
                            $('div[id="spinner-'+fName+'"]').remove();
                        }
                    } else {
                        $('div[id="spinner-'+fName+'"]').remove();
                    }
                }
            });

            $('button[id^="#post-files-modal-"]').on('click', function(){
                var currentFileInputDestination = $(this).data('input-referer'),
                    currentUploadMultiple = ($(this).data('multi-upload') == 1),
                    currentValue = $('input[name="'+currentFileInputDestination+'"]').val();
                if(currentValue !== '' && currentValue !== null){
                    if($('#iframe-file-list').attr('src') !== '/admin/files/iframeList?input='+currentFileInputDestination+'&files='+currentValue+'&multi='+currentUploadMultiple){
                        $('#iframe-file-list').attr('src', '/admin/files/iframeList?input='+currentFileInputDestination+'&files='+currentValue+'&multi='+currentUploadMultiple);
                    }
                } else {
                    if($('#iframe-file-list').attr('src') !== '/admin/files/iframeList?input='+currentFileInputDestination+'&multi='+currentUploadMultiple){
                        $('#iframe-file-list').attr('src', '/admin/files/iframeList?input='+currentFileInputDestination+'&multi='+currentUploadMultiple);
                    }
                }
            });
        }

        function initMeta(_name){
            var fName = _name,
                val = $('input[name="'+_name+'"]').attr('data-load-thumbnails').toString();

            if(val !== ''){
                var thumbIds =  val.split(',');
                $('span[id="'+fName+'-images-list"]').html("")
                if(thumbIds.length > 0){

                    for(var n in thumbIds){

                        $.ajax({
                            url: '/admin/files/getFile/'+thumbIds[n],
                            type: 'GET',
                            cache: false,
                            success: function(res){
                                if(res.success){
                                    $('span[id="'+fName+'-images-list"]').append('<img src="/files/thumbnail/'+res.content.filename+'">&nbsp;');
                                }
                            }
                        });

                    }
                }
            } else {
                $('span[id="'+fName+'-images-list"]').html("");
            }
        }

        $.receiveMessage(function(e){
            var post = JSON.parse('{"' + decodeURI(e.data).replace(/"/g, '\\"').replace(/&/g, '","').replace(/=/g,'":"') + '"}');
            if(typeof(post.action) !== 'undefined'){
                switch(post.action){
                    case 'upload_complete':
                        $('#iframe-file-list').attr( 'src', function ( i, val ) { return val; });
                        break;
                    case 'changed_images':
                        if(typeof(post.value) !== 'undefined' && typeof(post.input) !== 'undefined'){
                            var newval = post.value.replace(/\|/g, ',');
                                input = $('input[name="'+post.input+'"]');

                            console.log('newval - >'+newval)
                            if(newval.length > 0){
                                input.val(newval);
                                input.attr('data-load-thumbnails', newval)
                            } else {
                                input.val('null');
                                input.attr('data-load-thumbnails', 'null');
                            }
                            initMeta(post.input);
                        }
                        break;
                    case 'enlarge_upload':
                        if(typeof(post.value) !== 'undefined'){
                            $('#iframe-file-upload').attr('style', 'width:100%; height:'+post.value+'px;');
                        }
                        break;
                    case 'restore_upload':
                        if(typeof(post.value) !== 'undefined'){
                            $('#iframe-file-upload').attr('style', 'width:100%;height: 15vh;');
                        }
                        break;
                }
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
    });
}));