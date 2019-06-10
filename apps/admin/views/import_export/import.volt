<section class="content-header">
    <h1>
        Import / Export <small>Tools</small>
    </h1>
    <ol class="breadcrumb">
        <li><a><i class="fa fa-home"></i>Home</a></li>
    </ol>

    {{ flash.output() }}
    {{ flashSession.output() }}
</section>

<section class="content">
    <div id="box-content-edit">
        <div class="row">

            <div class="col-xs-12">
                <div class="box box-solid">
                    <div class="box-header with-border">
                        <h3 class="box-title">Log di debug</h3>
                        {{ link_to('admin/import_export/index', 'Indietro', "class" : 'btn btn-danger btn-flat pull-right') }}
                    </div>


                    <div class="box-body">
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="callout callout-warning">
                                    <h4><i class="icon fa fa-warning"></i> Attenzione!</h4>
                                    Importazione fallita o eseguita solo parzialmente, consulta gli errori nella lista sottostante.
                                </div>
                                <div class="callout callout-success">
                                    <p>Importati correttamente <b>{{success}}</b> contenuti.</p>
                                </div>

                                {% if errors is defined %}
                                    <ul class="list-unstyled">
                                    {% for error in errors%}
                                        <li>{{error}}</li>
                                    {% endfor %}
                                    </ul>
                                {% endif %}

                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</section>