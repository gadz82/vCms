<script type="text/javascript">
    var json_render_required = '<?= $render_required; ?>',
            filtersToWatch = '<?= $filtersToWatch; ?>';
</script>

<section class="content-header">
    <h1>{{ controllerName }}
        <small>modifica</small>
        {% if controller_data.id_tipologia_stato is 1 %}
            &nbsp;&nbsp;<a class="btn btn-primary"
                           href="/{{ controller_data.TipologiePost.slug }}/{{ controller_data.slug }}" target=_blank"">Vedi</a>
        {% endif %}
    </h1>

    <ol class="breadcrumb">
        <li>{{ link_to('admin/index/index', '<i class="fa fa-home"></i>Home') }}</li>
        <li>{{ link_to('admin/posts/index', 'Posts') }}</li>
        <li class="active">Dettaglio post</li>
    </ol>
    {{ get_content() }}
    {{ flashSession.output() }}
</section>

<section class="content">

    {{ form('', 'method':'post', 'autocomplete':'off', 'class':'', 'name':'form-edit', 'id':'form-edit', 'novalidate':'novalidate') }}

    <div id="box-content-edit">
        <div class="row">

            <div class="col-lg-9 col-md-8 col-sm-12">
                <div class="box box-solid">
                    <div class="box-header with-border">
                        <i class="fa fa-pencil"></i>
                        <h3 class="box-title">Contenuto Base</h3>
                        <span class="fa fa-chevron-down fa-fw pull-right"></span>
                    </div>

                    <div class="box-body">
                        <div class="row">
                            <div class="col-xs-12">


                                {% for element in arr_form['Posts'] %}
                                    {% if element.getAttribute('hidden') is not true and element.getAttribute('position') is not true %}
                                        {% set grid_class = element.getAttribute('grid_class') is not empty ? element.getAttribute('grid_class') : 'col-lg-12' %}
                                        <div class="{{ grid_class }}">

                                            <div class="form-group {{ form.hasErrorFor(element.getName()) }}">
                                                {{ form.label(element.getName()) }}
                                                {{ form.render(element.getName()) }}
                                            </div>
                                        </div>
                                    {% elseif element.getAttribute('hidden') is true %}
                                        {{ element }}
                                    {% endif %}
                                {% endfor %}

                            </div>
                        </div>
                    </div>


                </div>
                {% for metabox in metaboxes %}
                    <div class="box box-solid">
                        <div class="box-header with-border">
                            <i class="fa fa-pencil"></i>
                            <h3 class="box-title">{{ metabox }}</h3>
                            <span class="fa fa-chevron-down fa-fw pull-right"></span>
                        </div>

                        <div class="box-body">
                            <div class="row">
                                <?php
									if(isset(${$metabox . '_before'})){
										$this->partial(${$metabox . '_before'});
                                    }
                                ?>
                                <div class="col-xs-12">
                                    {% if arr_form['meta'][metabox] is defined %}
                                        {% for element in arr_form['meta'][metabox] %}

                                            {% if element.getAttribute('hidden') is not true and element.getAttribute('position') is not true %}

                                                {% if element.getAttribute('isfileupload') is not empty and element.getAttribute('isfileupload') is true %}

                                                    <div class="col-xs-12">
                                                        <div class="col-sm-6 no-padding">
                                                            <h5><strong>{{ element.getLabel() }}</strong></h5>
                                                            <span id="{{ element.getName() }}-images-list"></span>
                                                            <div id="spinner-{{ element.getName() }}">
                                                                <i class="fa fa-spinner fa-2x fa-spin fa-fw"
                                                                   aria-hidden="true"></i>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6 text-right">
                                                            <button type="button" class="btn btn-info btn-flat"
                                                                    data-toggle="modal"
                                                                    id="#post-files-modal-{{ str_replace(['[', ']'], ['-', ''], element.getName()) }}"
                                                                    data-input-referer="{{ element.getName() }}"
                                                                    data-multi-upload="{{ element.getAttribute('is_multi_upload') }}"
                                                                    data-target="#post-files-modal">
                                                                Gestisci Immagini
                                                            </button>
                                                        </div>
                                                        <input type="hidden" name="{{ element.getName() }}"
                                                               id="{{ element.getName() }}"
                                                               value="{{ element.getAttribute('value') }}"
                                                               data-load-thumbnails="{{ element.getAttribute('value') }}"/>
                                                        <br>
                                                    </div>
                                                {% elseif element.getAttribute('hidden') is not true %}

                                                    {% set grid_class = element.getAttribute('grid_class') is not empty ? element.getAttribute('grid_class') : 'col-lg-12' %}
                                                    <div class="{{ grid_class }}">

                                                        <div class="form-group {{ form.hasErrorFor(element.getName()) }}">
                                                            {{ form.label(element.getName()) }}
                                                            {{ form.render(element.getName()) }}
                                                        </div>
                                                    </div>

                                                {% else %}
                                                    {{ element }}
                                                {% endif %}

                                            {% endif %}
                                        {% else %}
                                            Nessun Meta Configurato per il gruppo {{ metabox }}
                                        {% endfor %}
                                    {% else %}
                                        Nessun Meta Configurato per il gruppo {{ metabox }}
                                    {% endif %}
                                </div>
                                <?php
									if(isset(${$metabox . '_after'})){
										$this->partial(${$metabox . '_after'});
                                    }
                                ?>
                            </div>
                        </div>


                    </div>

                {% endfor %}
            </div>
            <div class="col-lg-3 col-md-4 col-sm-12">
                <div class="box box-solid">
                    <div class="box-header with-border">
                        <i class="fa fa-pencil"></i>
                        <h3 class="box-title">Info Pubblicazione</h3>
                        <span class="fa fa-chevron-down fa-fw pull-right"></span>
                    </div>

                    <div class="box-body">
                        <div class="row">
                            <div class="col-xs-12">


                                {% for element in form %}
                                    {% if element.getAttribute('hidden') is not true and element.getAttribute('position') == 'side' %}
                                        {% set grid_class = element.getAttribute('grid_class') is not empty ? element.getAttribute('grid_class') : 'col-lg-12' %}
                                        <div class="{{ grid_class }}">

                                            <div class="form-group {{ form.hasErrorFor(element.getName()) }}">
                                                {{ form.label(element.getName()) }}
                                                {{ form.render(element.getName()) }}
                                            </div>
                                        </div>
                                    {% endif %}
                                {% endfor %}

                            </div>
                        </div>
                    </div>

                    <div class="box-footer text-right">
                        {{ submit_button("Salva", "class": "btn btn-success btn-flat") }}
                        <a href="{{ backlink }}" class="btn btn-primary btn-flat">Indietro</a>
                    </div>

                </div>

                {% for filtro_group in filtri_groups %}
                    {% if arr_form['filtri'][filtro_group] is not empty %}
                        <div class="box box-solid">
                            <div class="box-header with-border">
                                <i class="fa fa-pencil"></i>
                                <h3 class="box-title">{{ filtro_group }}</h3>
                                <span class="fa fa-chevron-down fa-fw pull-right"></span>
                            </div>

                            <div class="box-body">
                                <div class="row">
                                    <div class="col-xs-12">
                                        {% for element in arr_form['filtri'][filtro_group] %}
                                            {% set grid_class = element.getAttribute('grid_class') is not empty ? element.getAttribute('grid_class') : 'col-lg-12' %}
                                            <div class="{{ grid_class }}">

                                                <div class="form-group {{ form.hasErrorFor(element.getName()) }}">
                                                    {{ form.label(element.getName()) }}
                                                    {{ form.render(element.getName()) }}
                                                </div>
                                            </div>
                                        {% endfor %}
                                    </div>
                                </div>
                            </div>


                        </div>
                    {% endif %}
                {% endfor %}
                {% if arr_form['Tags'] is not empty %}

                    <div class="box box-solid">
                        <div class="box-header with-border">
                            <i class="fa fa-pencil"></i>
                            <h3 class="box-title">Tags</h3>
                            <span class="fa fa-chevron-down fa-fw pull-right"></span>
                        </div>
                        <div class="box-body">
                            <div class="row">

                                {% for element in arr_form['Tags'] %}

                                    {% set grid_class = element.getAttribute('grid_class') is not empty ? element.getAttribute('grid_class') : 'col-lg-12' %}
                                    <div class="{{ grid_class }}">

                                        <div class="form-group {{ form.hasErrorFor(element.getName()) }}">
                                            {{ form.label(element.getName()) }}
                                            {{ form.render(element.getName()) }}
                                        </div>
                                    </div>

                                {% endfor %}

                            </div>
                        </div>
                    </div>

                {% endif %}
                {% if arr_form['userGroups'] is not empty %}
                    <div class="box box-solid">
                        <div class="box-header with-border">
                            <i class="fa fa-users"></i>
                            <h3 class="box-title">Permessi</h3>
                            <span class="fa fa-chevron-down fa-fw pull-right"></span>
                        </div>
                        <div class="box-body">
                            <div class="row">
                                {% for element in arr_form['userGroups'] %}

                                    {% set grid_class = element.getAttribute('grid_class') is not empty ? element.getAttribute('grid_class') : 'col-lg-12' %}
                                    <div class="{{ grid_class }}">

                                        <div class="form-group {{ form.hasErrorFor(element.getName()) }}">
                                            {{ form.label(element.getName()) }}
                                            {{ form.render(element.getName()) }}
                                        </div>
                                    </div>
                                {% endfor %}
                            </div>
                        </div>
                    </div>
                {% endif %}

            </div>
        </div>
    </div>

    {{ end_form() }}
    {% if fileupload is defined and fileupload is true %}

        {{ partial("partials/posts/fileUploadModal", ['element': element]) }}

    {% endif %}
</section>
<div id="modalZoom" class="modal-image">

    <!-- The Close Button -->
    <span class="close-zoom-image" id="close-zoom-modal"
          onclick="document.getElementById('modalZoom').style.display='none'">&times;</span>

    <!-- Modal Content (The Image) -->
    <img class="modal-content-image" id="img01">

</div>