// IIFE - Immediately Invoked Function Expression
(function(yourcode){
    // The global jQuery object is passed as a parameter
    yourcode(window.jQuery, window, document);

}(function($, window, document){
    $('document').ready(function(){
        //---- FILEUPLOAD ----//
        var fi = $('#fileupload');

        //console.log(fi);
        var uploadAllBtn = $('#upload_start');
        var uploadBtn = $('<span/>').addClass('btn upload hide').html('Carica');
        var $file_upload_list = $('#box-content-upload').find('ul.files');

        uploadAllBtn.on('click', function(){
            $(this).fadeOut();
            var btnList = $file_upload_list.find('.btn.upload'),
                count = btnList.length,
                uploaded = 0;

            if(count == 0) {
                $(this).fadeIn();
                swal( "Le immagini?" ,  "Carica qualche immagine" ,  "error" );
            }
            $file_upload_list.find('.btn.upload').each(function(){
                var $this = $(this);
                var data = $this.data();
                data.submit().success(function(data){
                    uploaded = uploaded+1;
                    if(uploaded == count){
                        setTimeout(function(){
                            window.location.reload();
                        }, 2000);
                    }
                });
            });
        });

        fi.fileupload({
            dataType: 'json',
            autoUpload: false,
            acceptFileTypes: /(\.|\/)(jpe?g|png|gif|pdf)$/i,
            disableValidation: false,
            maxChunkSize: 1024*1024*1,
            dropZone: $('.box-upload-dropzone'),
            maxNumberOfFiles: 50,
            /*limitConcurrentUploads: 1,*/
            formData: fi.serializeArray()
        });

        fi.on('fileuploadsubmit', function (e, data) {
            var inputs = data.context.find(':input');

            if (inputs.filter(function () {
                    return !this.value && $(this).prop('required');
                }).first().focus().length) {
                data.context.find('button').prop('disabled', false);
                return false;
            }
            data.formData = inputs.serializeArray();
            uploadBtn.attr('disabled',true);
            uploadBtn.parent().find('.remove').attr('disabled',true);
        });

        fi.on('fileuploadadd', function (e, data) {
            data.context = $('<li/>').addClass('item').appendTo($file_upload_list);

            $.each(data.files, function(index,file){

                var removeBtn  = $('<span/>').addClass('btn btn-danger btn-sm btn-flat remove').html('<i class="fa fa-trash fa-fw"></i>');
                var preview = $('<div/>').addClass('product-img');
                var info = $('<div/>').addClass('product-info');
                var action = $('<div/>').addClass('col-md-1 col-sm-12 text-center upload-action').append(uploadBtn.clone(true).data(data)).append(removeBtn);

                info.append('<div class="col-md-10 col-sm-12"><p class="product-title">'+file.name+'<span class="label label-success pull-right">'+format_size(file.size)+'</span></p><div class="progress product-description"><div class="progress-bar progress-bar-success" style="width:0%;" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div></div></div>')
                    .append(action)
                    .append('<div class="row">');

                removeBtn.on('click', function(e, data){
                    $(this).closest('li').slideUp('fast',function(){
                        $(this).remove();
                    });
                });

                if(!index) preview.append(file.preview);

                data.context.append(preview).append(info);

            });

        });

        fi.on('fileuploadprocessalways', function (e, data) {
            var index = data.index;
            var file = data.files[index];
            var node = $(data.context[index]);

            if(file.preview){
                node.find('.product-img').append(file.preview);
            }else{
                if(!file.error){
                    node.find('.product-img').append('<span class="fa fa-file-text-o fa-fw fa-3x text-light-blue"></span>');
                }else{
                    node.find('.product-img').append('<span class="fa fa-exclamation-triangle fa-fw fa-3x text-red"></span>');
                }
            }
            if(file.error){
                node.find('.product-title > span.label').addClass('label-danger').removeClass('label-success').text(file.error+' - '+format_size(file.size));
            }
            if(index + 1 === data.files.length){
                data.context.find('button.upload').prop('disabled', !!data.files.error);
            }
        });

        fi.on('fileuploadprogress', function (e, data) {
            var progress = parseInt(data.loaded / data.total * 100, 10);
            if (data.context) {
                data.context.each(function () {
                    $(this).find('.progress-bar').attr('aria-valuenow', progress).css('width',progress + '%').find('span').text(progress + '%');
                });
            }
        });

        fi.on('fileuploaddone', function (e, data) {
            $.each(data.result.file, function (index, file) {
                if(file.url){
                    var link = $('<a>').attr('target', '_blank').attr('href',file.url);
                    if(fi.data('meta-input') !== undefined)$('input[name="'+fi.data('meta-input')+'"]').val(link);
                    $(data.context[index]).addClass('file-uploaded');
                    $(data.context[index]).find('canvas').wrap(link);
                    $(data.context[index]).find('.product-title > span.label').text('Fatto!');

                    setTimeout(function(){
                        $(data.context[index]).slideUp('fast',function(){
                            $(this).remove();
                        });
                    }, 500);
                }else if(file.error){
                    //$(data.context[index]).find('.error.text-danger').text(error);
                    $(data.context[index]).find('.product-title').append(' - <span class="text-red">'+file.error+'</span>');
                    $(data.context[index]).find('.progress-bar').removeClass('progress-bar-succes').addClass('progress-bar-danger');
                }
            });
        });
        fi.on('fileuploadfail', function (e, data) {
            console.log(data.jqXHR.responseText);
        });
        $('.box-upload-dropzone').bind('dragover dragenter', function(){
            $(this).addClass('in');
        });
        $('.box-upload-dropzone').bind('dragleave dragend drop', function(){
            $(this).removeClass('in');
        });
        $(document).bind('drop dragover', function (e) {
            e.preventDefault();
        });

        function format_size(bytes){

            if(typeof bytes !== 'number'){
                return '';
            }

            if(bytes >= 1073741824){
                bytes = (bytes / 1073741824).toFixed(2)+' GB';
            }else if(bytes >= 1048576){
                bytes = (bytes / 1048576).toFixed(2)+' MB';
            }else if(bytes >= 1024){
                bytes = (bytes / 1024).toFixed(2)+' KB';
            }else if(bytes > 1){
                bytes = bytes+' bytes';
            }else if(bytes == 1){
                bytes = bytes+' byte';
            }else{
                bytes = '0 bytes';
            }

            return bytes;
        }
    });
}));