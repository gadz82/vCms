<section class="content-header">
    <h1>
        Pulizia Files Orfani
    </h1>
    <ol class="breadcrumb">
        <li><a><i class="fa fa-home"></i>Home</a></li>
    </ol>
    {{ get_content() }}
    {{ flashSession.output() }}

</section>

<section class="content">
    <div id="box-container-dashboard" class="row">
        <div class="col-xs-12">
            <div class="box box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">Tool di pulizia cartelle files</h3>
                </div>

                <div class="row">
                    <div class="box-body col-xs-12">
                        <div class="col-xs-12">
                            <p>
                                Cliccando il pulsante sottostante il sistema eliminerà tutti i file della cartella /files che non hanno una corrispondenza nel db.
                            </p>
                            {{ form('admin/files/cleanFiles', 'method':'post', 'autocomplete':'off', 'class':'', 'id':'form-cleanFiles', 'novalidate':'novalidate') }}
                            <div class="text-center">
                                {{ submit_button("Effettua Pulizia", "class": "btn btn-success btn-danger btn-lg") }}
                            </div>
                            {{ end_form() }}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

</section>