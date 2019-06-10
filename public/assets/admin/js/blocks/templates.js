$(function () {

    var filemanager = $('.filemanager'),
        breadcrumbs = $('.breadcrumbs'),
        filemanagerContainer = $('#file-manager-container'),
        fileList = filemanager.find('.data');

    // Start by fetching the file data from scan.php with an AJAX request
    var cm = CodeMirror.fromTextArea(document.getElementById('editor-textarea'), {
        mode: "text/html",
        extraKeys: {"Ctrl-Space": "autocomplete"},
        lineNumbers: true,
        theme: 'dracula'
    });

    $.get('/admin/blocks/scan', function (data) {
        console.log(data);
        var response = [data],
            currentPath = '',
            breadcrumbsUrls = [];

        var folders = [],
            files = [];

        // This event listener monitors changes on the URL. We use it to
        // capture back/forward navigation in the browser.

        $(window).on('hashchange', function () {

            goto(window.location.hash);

            // We are triggering the event. This will execute
            // this function on page load, so that we show the correct folder:

        }).trigger('hashchange');


        // Hiding and showing the search box

        filemanager.find('.search').click(function () {

            var search = $(this);

            search.find('span').hide();
            search.find('input[type=search]').show().focus();

        });


        // Listening for keyboard input on the search field.
        // We are using the "input" event which detects cut and paste
        // in addition to keyboard input.

        filemanager.find('input').on('input', function (e) {

            folders = [];
            files = [];

            var value = this.value.trim();

            if (value.length) {

                filemanager.addClass('searching');

                // Update the hash on every key stroke
                window.location.hash = 'search=' + value.trim();

            }

            else {

                filemanager.removeClass('searching');
                window.location.hash = encodeURIComponent(currentPath);

            }

        }).on('keyup', function (e) {

            // Clicking 'ESC' button triggers focusout and cancels the search

            var search = $(this);

            if (e.keyCode == 27) {

                search.trigger('focusout');

            }

        }).focusout(function (e) {

            // Cancel the search

            var search = $(this);

            if (!search.val().trim().length) {

                window.location.hash = encodeURIComponent(currentPath);
                search.hide();
                search.parent().find('span').show();

            }

        });


        // Clicking on folders

        fileList.on('click', 'li.folders', function (e) {
            e.preventDefault();

            var nextDir = $(this).find('a.folders').attr('href');

            if (filemanager.hasClass('searching')) {

                // Building the breadcrumbs

                breadcrumbsUrls = generateBreadcrumbs(nextDir);

                filemanager.removeClass('searching');
                filemanager.find('input[type=search]').val('').hide();
                filemanager.find('span').show();
            }
            else {
                breadcrumbsUrls.push(nextDir);
            }

            window.location.hash = encodeURIComponent(nextDir);
            currentPath = nextDir;
        });


        // Clicking on breadcrumbs

        breadcrumbs.on('click', 'a', function (e) {
            e.preventDefault();

            var index = breadcrumbs.find('a').index($(this)),
                nextDir = breadcrumbsUrls[index];

            breadcrumbsUrls.length = Number(index);

            window.location.hash = encodeURIComponent(nextDir);

        });


        // Navigates to the given hash (path)

        function goto(hash) {

            hash = decodeURIComponent(hash).slice(1).split('=');

            if (hash.length) {
                var rendered = '';

                // if hash has search in it

                if (hash[0] === 'search') {

                    filemanager.addClass('searching');
                    rendered = searchData(response, hash[1].toLowerCase());

                    if (rendered.length) {
                        currentPath = hash[0];
                        render(rendered);
                    }
                    else {
                        render(rendered);
                    }

                }

                // if hash is some path

                else if (hash[0].trim().length) {

                    rendered = searchByPath(hash[0]);

                    if (rendered.length) {

                        currentPath = hash[0];
                        breadcrumbsUrls = generateBreadcrumbs(hash[0]);
                        render(rendered);

                    }
                    else {
                        currentPath = hash[0];
                        breadcrumbsUrls = generateBreadcrumbs(hash[0]);
                        render(rendered);
                    }

                }

                // if there is no hash

                else {
                    currentPath = data.path;
                    breadcrumbsUrls.push(data.path);
                    render(searchByPath(data.path));
                }
            }
        }

        // Splits a file path and turns it into clickable breadcrumbs

        function generateBreadcrumbs(nextDir) {
            console.log(nextDir);
            var path = nextDir.split('/').slice(0);
            for (var i = 1; i < path.length; i++) {
                path[i] = path[i - 1] + '/' + path[i];
            }
            return path;
        }


        // Locates a file by path

        function searchByPath(dir) {
            var path = dir.split('/'),
                demo = response,
                flag = 0;

            for (var i = 0; i < path.length; i++) {
                for (var j = 0; j < demo.length; j++) {
                    if (demo[j].name === path[i]) {
                        flag = 1;
                        demo = demo[j].items;
                        break;
                    }
                }
            }

            demo = flag ? demo : [];
            return demo;
        }


        // Recursively search through the file tree

        function searchData(data, searchTerms) {

            data.forEach(function (d) {
                if (d.type === 'folder') {

                    searchData(d.items, searchTerms);

                    if (d.name.toLowerCase().match(searchTerms)) {
                        folders.push(d);
                    }
                }
                else if (d.type === 'file') {
                    if (d.name.toLowerCase().match(searchTerms)) {
                        files.push(d);
                    }
                }
            });
            return {folders: folders, files: files};
        }


        // Render the HTML for the file manager

        function render(data) {

            var scannedFolders = [],
                scannedFiles = [];

            if (Array.isArray(data)) {

                data.forEach(function (d) {

                    if (d.type === 'folder') {
                        scannedFolders.push(d);
                    }
                    else if (d.type === 'file') {
                        scannedFiles.push(d);
                    }

                });

            }
            else if (typeof data === 'object') {

                scannedFolders = data.folders;
                scannedFiles = data.files;

            }


            // Empty the old result and make the new one

            fileList.empty().hide();

            if (!scannedFolders.length && !scannedFiles.length) {
                filemanager.find('.nothingfound').show();
            }
            else {
                filemanager.find('.nothingfound').hide();
            }

            if (scannedFolders.length) {

                scannedFolders.forEach(function (f) {

                    var itemsLength = f.items.length,
                        name = escapeHTML(f.name),
                        icon = '<span class="icon folder"></span>';

                    if (itemsLength) {
                        icon = '<span class="icon folder full"></span>';
                    }

                    if (itemsLength == 1) {
                        itemsLength += ' item';
                    }
                    else if (itemsLength > 1) {
                        itemsLength += ' items';
                    }
                    else {
                        itemsLength = 'Empty';
                    }

                    var folder = $('<li class="folders col-lg-4 col-sm-6 col-xs-12"><a href="' + f.path + '" title="' + f.path + '" class="folders">' + icon + '<span class="name">' + name + '</span> <span class="details">' + itemsLength + '</span></a></li>');
                    folder.appendTo(fileList);
                });

            }

            if (scannedFiles.length) {

                scannedFiles.forEach(function (f) {

                    var fileSize = bytesToSize(f.size),
                        name = escapeHTML(f.name),
                        fileType = name.split('.'),
                        icon = '<span class="icon file"></span>';

                    fileType = fileType[fileType.length - 1];

                    icon = '<span class="icon file f-' + fileType + '">.' + fileType + '</span>';

                    var file = $('<li class="files col-lg-4"><a id="edit-file-' + f.path + '" href="' + f.path + '" title="' + f.path + '" class="files">' + icon + '<span class="name">' + name + '</span> <span class="details">' + fileSize + '</span></a></li>');
                    file.appendTo(fileList);

                    file.find('a').on('click', function (e) {
                        e.preventDefault();
                        if (!$('#editor-container').hasClass('hidden'))$('#editor-container').addClass('hidden');
                        var path = $(this).attr('href');
                        console.log(path);
                        $.ajax({
                            url: '/admin/blocks/readTemplate',
                            type: 'POST',
                            data: {"path": path},
                            cache: false,
                            success: function (data) {

                                if (!data.success) {
                                    swal({
                                        title: "Attenzione!",
                                        text: "Impossibile leggere i contenuti del file, possibile problema legato ai permessi dell'utenza o al chmod dei files.",
                                        type: "error"
                                    });
                                } else {
                                    if ($('#editor-container').hasClass('hidden'))$('#editor-container').removeClass('hidden');
                                    cm.toTextArea();
                                    $('textarea#editor-textarea').val(data.content);

                                    cm = CodeMirror.fromTextArea(document.getElementById('editor-textarea'), {
                                        mode: "text/html",
                                        extraKeys: {"Ctrl-Space": "autocomplete"},
                                        lineNumbers: true,
                                        theme: 'dracula'
                                    });
                                    cm.setSize(null, 700);

                                    filemanagerContainer.slideUp();

                                    $('#editor-cancel-file-edit').on('click', function(){
                                        filemanagerContainer.slideDown('fast', function(){
                                            $('#editor-container').addClass('hidden');
                                        });
                                    });
                                    $('#editor-save-file-edit').on('click', function(){
                                        saveEdits(f, cm.getValue());
                                    });

                                    if(typeof(data.vars) !=='undefined'){
                                        if(typeof(data.vars.filters) !=='undefined' && data.vars.filters.length > 0){
                                            var filtri = $("<div class='col-xs-12' style='margin-bottom:15px;'><h5 style='width:100%;'>Variabili Utilizzabili - Filtri</h5></div>")
                                            for(var i = 0; i < data.vars.filters.length; i++){
                                                filtri.append('<span class="btn btn-outline" style="color:#333;border-color:#333;">'+data.vars.filters[i]+'</span>&nbsp;');
                                            }
                                            $('#box-content-edit').before(filtri);
                                        }

                                        if(typeof(data.vars.meta) !=='undefined' && data.vars.meta.length > 0){
                                            var meta = $("<div class='col-xs-12' style='margin-bottom:15px;'><h5 style='width:100%;'>Variabili Utilizzabili - Meta</h5></div>")
                                            for(var i = 0; i < data.vars.meta.length; i++){
                                                meta.append('<span class="btn btn-outline" style="color:#333;border-color:#333;">'+data.vars.meta[i]+'</span>&nbsp;');
                                            }
                                            $('#box-content-edit').before(meta);
                                        }
                                    }
                                }
                            }
                        });
                    });
                });

            }

            // Generate the breadcrumbs

            var url = '';

            if (filemanager.hasClass('searching')) {

                url = '<span>Search results: </span>';
                fileList.removeClass('animated');

            }
            else {

                fileList.addClass('animated');
                var show = false;
                breadcrumbsUrls.forEach(function (u, i) {

                    var name = u.split('/');
                    if (name[name.length - 1] == response[0].name) show = true;


                    if (i !== breadcrumbsUrls.length - 1) {
                        if (show) {
                            url += '<a href="' + u + '"><span class="folderName">' + name[name.length - 1] + '</span></a> <span class="arrow">→</span> '
                        } else {
                            url += '<a href="' + u + '" class="hidden"><span class="folderName">' + name[name.length - 1] + '</span></a>';
                        }
                    }
                    else {
                        url += '<span class="folderName">' + name[name.length - 1] + '</span>';
                    }


                });

            }

            breadcrumbs.text('').append(url);


            // Show the generated elements
            fileList.show();
            fileList.animate({'display': 'inline-block'});

        }


        // This function escapes special html characters in names

        function escapeHTML(text) {
            return text.replace(/\&/g, '&amp;').replace(/\</g, '&lt;').replace(/\>/g, '&gt;');
        }


        // Convert file sizes from bytes to human readable units

        function bytesToSize(bytes) {
            var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
            if (bytes == 0) return '0 Bytes';
            var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
            return Math.round(bytes / Math.pow(1024, i), 2) + ' ' + sizes[i];
        }

        function saveEdits(file, content){

            $.ajax({
                url: '/admin/blocks/editTemplate',
                type: 'POST',
                data: {"file": file, "content" :content},
                cache: false,
                success: function (data) {
                    if (!data.success) {
                        swal({
                            title: "Attenzione!",
                            text: "Impossibile modificare i contenuti del file, possibile problema legato ai permessi dell'utenza o al chmod dei files.",
                            type: "error"
                        });
                    } else {
                        swal({
                            title: "File Modificato!",
                            text: "Modifiche apportate con successo.",
                            type: "success"
                        });
                    }
                }
            });
        }

        function copyToClipboard(content) {
            var $temp = $("<input>");
            $("body").append($temp);
            $temp.val(content).select();
            document.execCommand("copy");
            $temp.remove();
        }

    });
});
