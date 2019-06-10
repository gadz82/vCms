
<section class="content" style="min-height: auto;" id="file-manager-container">

    <div class="filemanager">

        <div class="search">
            <input type="search" placeholder="Cerca per filename" />
        </div>

        <div class="breadcrumbs"></div>

        <ul class="data"></ul>

        <div class="nothingfound">
            <div class="nofiles"></div>
            <span>Nessun risultato.</span>
        </div>

    </div>

</section>

<section class="content editor hidden" id="editor-container">
    <div id="box-content-edit">
        <div class="row">

            <div class="col-xs-12">
                <div class="box-body" style="padding:0px;">
                    <div class="box box-solid">
                        <div class="row">
                            <div class="col-xs-12" style="padding:20px">
                                <div class="col-xs-12 form-group">
                                    <label>Editor</label>
                                    <textarea class="form-control" rows="50" cols="50" id="editor-textarea" name="editor-textare"></textarea>
                                </div>
                                <div class="col-xs-12 text-right">

                                    <button class="btn btn-danger" id="editor-cancel-file-edit">Annulla</button>

                                    <button class="btn btn-primary" id="editor-save-file-edit">Salva Modifica</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>