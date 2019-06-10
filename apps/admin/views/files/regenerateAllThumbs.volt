<section class="content-header">
    <h1>
        Rigenera Thumbnails
    </h1>
    <ol class="breadcrumb">
        <li><a><i class="fa fa-home"></i>Home</a></li>
    </ol>
    {{ flashSession.output() }}
</section>

<section class="content">
    <div id="box-container-dashboard" class="row">
        <div class="col-xs-12">
            <div class="box box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">Rigenera tutte le miniature di tutte le foto</h3>
                </div>

                <div class="row">
                    <div class="box-body col-xs-12">
                        <div class="col-xs-12">
                            <p>Clicca sul pulsante per dare inizio al processo e attendi che lo script completi il
                                lavoro. La durata dipende dal numero delle foto da rigenerare</p>
                        </div>
                        {% for size in imageVersions %}
                            <div class="col-sm-3 col-xs-12" style="margin:30px 0;">
                                {{ form('admin/files/regenerateAllThumbs', 'method':'post', 'autocomplete':'off', 'class':'', 'id':'form-regenerateAllThumbs-~size', 'novalidate':'novalidate') }}
                                <div class="text-center">
                                    <input type="hidden" name="key" value="{{ size }}">
                                    {{ submit_button("Rigenera "~size, "class": "btn btn-success btn-primary btn-lg") }}
                                </div>
                                {{ end_form() }}
                            </div>
                        {% endfor %}
                        <div class="col-xs-12">
                            {{ form('admin/files/regenerateAllThumbs', 'method':'post', 'autocomplete':'off', 'class':'', 'id':'form-regenerateAllThumbs', 'novalidate':'novalidate') }}
                            <div class="text-center">
                                {{ submit_button("Rigenera Tutto", "class": "btn btn-success btn-danger btn-lg") }}
                            </div>
                            {{ end_form() }}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

</section>