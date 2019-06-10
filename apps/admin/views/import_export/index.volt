<section class="content-header">
    <h1>
        Import / Export <small>Tools</small>
    </h1>
    <ol class="breadcrumb">
        <li><a><i class="fa fa-home"></i>Home</a></li>
    </ol>

    {{ flashSession.output() }}
</section>

<section class="content">
    <div id="box-content-edit">
        <div class="row">

            <div class="col-xs-12">
                <div class="box box-solid">
                    <div class="box-header with-border">
                        <i class="fa fa-download"></i>
                        <h3 class="box-title">Importa Contenuti</h3>
                    </div>
                    {{ form('admin/import_export/import', 'method':'post', 'autocomplete':'off', 'class':'', 'name':'form-import', 'id':'form-import', 'enctype' : 'multipart/form-data') }}

                    <div class="box-body">
                        <div class="row">
                            <div class="col-xs-12">


                                <div class="col-xs-6">

                                    <div class="form-group">
                                        {{ select("id_tipologia_post", tipologie_post, 'using': ['id', 'descrizione'], 'class': 'form-control selectpicker') }}
                                    </div>
                                </div>
                                <div class="col-xs-6">

                                    <div class="form-group">
                                        {{ file_field('file', 'class': 'form-control', 'required':1, 'accept':'.csv') }}
                                    </div>
                                </div>


                            </div>
                        </div>
                    </div>

                    <div class="box-footer text-right">
                        {{ submit_button("Importa", "class": "btn btn-success btn-flat") }}

                    </div>
                    {{ end_form() }}
                </div>
            </div>
            <div class="col-xs-12">
                <div class="box box-solid">
                    <div class="box-header with-border">
                        <i class="fa fa-upload"></i>
                        <h3 class="box-title">Esporta Contenuti</h3>
                    </div>

                    {{ form('admin/import_export/export', 'method':'post', 'autocomplete':'off', 'class':'', 'name':'form-import', 'id':'form-export') }}

                    <div class="box-body">
                        <div class="row">
                            <div class="col-xs-12">


                                <div class="col-xs-12">

                                    <div class="form-group">
                                        {{ select("id_tipologia_post", tipologie_post, 'using': ['id', 'descrizione'], 'class': 'form-control selectpicker') }}
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="box-footer text-right">
                        {{ submit_button("Esporta", "class": "btn btn-success btn-flat") }}

                    </div>
                    {{ end_form() }}
                </div>
            </div>
            <div class="col-xs-12">
                <div class="box box-solid">
                    <div class="box-header with-border">
                        <i class="fa fa-magic"></i>
                        <h3 class="box-title">Genera Modello CSV</h3>
                    </div>

                    {{ form('admin/import_export/model', 'method':'post', 'autocomplete':'off', 'class':'', 'name':'form-model', 'id':'form-model') }}

                    <div class="box-body">
                        <div class="row">
                            <div class="col-xs-12">


                                <div class="col-xs-12">

                                    <div class="form-group">
                                        {{ select("id_tipologia_post", tipologie_post, 'using': ['id', 'descrizione'], 'class': 'form-control selectpicker') }}
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="box-footer text-right">
                        {{ submit_button("Genera", "class": "btn btn-success btn-flat") }}

                    </div>
                    {{ end_form() }}
                </div>
            </div>
        </div>
    </div>
</section>