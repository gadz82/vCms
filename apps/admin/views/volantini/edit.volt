<script type="text/javascript">
	var json_render_required = '<?= $render_required; ?>'
</script>

<section class="content-header">
	<h1>Volantini <small>dettaglio</small></h1>
	<ol class="breadcrumb">
		<li>{{ link_to('admin/index/index', '<i class="fa fa-home"></i>Home') }}</li>
		<li>{{ link_to('admin/volantini/index', 'Volantini') }}</li>
		<li class="active">Dettaglio volantino</li>
	</ol>
	{{ get_content() }}
	
	{{ flashSession.output() }}
	
</section>
	
<section class="content">
	
	{{ form('', 'method':'post', 'autocomplete':'off', 'class':'', 'name':'form-edit', 'id':'form-edit', 'novalidate':'novalidate') }}
	
	<div id="box-content-edit">
		<div class="row">
		
			<div class="col-xs-12">
				<div class="box box-solid">
					<div class="box-header with-border">
						<i class="fa fa-pencil"></i>
						<h3 class="box-title">Volantino - Id Volantino | <strong>{{ controller_data.id }}</strong></h3>
						<span class="fa fa-chevron-down fa-fw pull-right"></span>
	                </div>
	                
	                <div class="box-body">
						<div class="row">
							<div class="col-xs-12">
																					
								{% for element in form %}
                                    {% if element.getAttribute('hidden') is not true %}
                                        {% set grid_class = element.getAttribute('grid_class') is not empty ? element.getAttribute('grid_class') : 'col-lg-12' %}
                                        <div class="{{grid_class}}">

                                            <div class="form-group {{ form.hasErrorFor(element.getName()) }}">
                                                {{ form.label(element.getName()) }}
                                                {{ form.render(element.getName()) }}
                                            </div>
                                        </div>
                                    {% else %}
                                        {{ element }}
                                    {% endif %}
                                {% endfor %}
																										
							</div>
						</div>
					</div>
						
					<div class="box-footer text-right">
						{{ submit_button("Salva", "class": "btn btn-success btn-flat") }}
						{{ link_to('admin/volantini/index', 'Indietro', 'class':'btn btn-primary btn-flat') }}
					</div>
					
				</div>
			</div>
		</div>
	</div>
	
	{{ end_form() }}

</section>
<section class="content">
	{% if files is false %}

	<div id="box-content-edit">
		<div class="row">
			<div class="col-xs-12">
				<div class="box box-solid">
					<div class="box-header with-border">
						<i class="fa fa-image"></i>
						<h3 class="box-title">Carica le pagine del volantino</h3>
						<span class="fa fa-chevron-down fa-fw pull-right"></span>
					</div>
					<div class="box-body" id="box-content-upload">
						<div class="row">
							<div class="col-xs-12">
								<div class="col">
									<small>Ricorda di rinominare i file in ordine alfabetico prima di uploadarli. Concentrati e tieni presente che pagina-10.jpg, nell'ordinamento alfabetico, viene prima di pagina-2.jpg. Rinomina i file con un solo intero effettuando un pad con 0 -> il file pagina-2.jpg diventa pagina-02.jpg</small>
									<hr>
								</div>
								<div id="box-fileupload-container" class="col-xs-12">
									<div id="upload-dropzone" class="box-upload-dropzone fade text-center">

										{{ form('/admin/volantini/fileupload/'~controller_data.id, 'method':'post', 'autocomplete':'off', 'enctype':'multipart/form-data', 'class':'', 'name':'form-volantini-files', 'id':'fileupload') }}
										<p><strong>Trascina il file qui</strong></p>
										<p><small>- oppure -</small></p>
										<p class="btn btn-flat btn-primary fileinput-button">
											<i class="fa fa-plus fa-fw"></i>
											<span>Aggiungi</span>
											<input id="fileupload" type="file" name="file[]" data-url="/admin/volantini/fileupload"
												   multiple
												   accept="image/jpeg">
										</p>
										<input type="hidden" name="id_entity" value="">
										{{ end_form() }}

									</div>
									<br>
									<div class="col-xs-12 text-center">
										<span class="btn btn-primary" id="upload_start">
											<i class="glyphicon glyphicon-upload"></i>
											<span>Carica Volantino</span>
										</span>
									</div>
									<div id="box-upload-list">
										<ul class="products-list product-list-in-box files"></ul>
									</div>
								</div>

							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	{% else %}
	<div id="box-content-images">
		<div class="row">
			<div class="col-xs-12">
				<div class="box box-solid">
					<div class="box-header with-border">
						<i class="fa fa-eye"></i>
						<h3 class="box-title">Foto del volantino</h3>
						&nbsp;&nbsp;&nbsp;
						<a href="/admin/volantini/trashImages/{{controller_data.id}}" class="btn btn-danger" id="trash-images">Elimina Immagini <span class="fa fa-trash fa-fw"></span></a>
						<span class="fa fa-chevron-down fa-fw pull-right"></span>
					</div>
					<div class="box-body" id="box-content-upload">
						<div class="row">
							<div class="col-xs-12">
								<div class="box-body">
								{% for file in files %}
									<div class="col-md-2 col-sm-3 col-xs-4" style="margin-bottom:15px;">
										<a href="#box-content-images" id="image-zoom-{{file}}" data-image-zoom="{{upload_url}}{{file}}">
											<img src="{{upload_url}}{{file}}" class="img-responsive">
										</a>
										<p class="text-center"><small>{{file}}</small></p>
									</div>
								{% else %}
									<h4 class="text-danger text-center">
										Qualcosa è andato storto... chiama Witchland!
									</h4>
								{% endfor %}
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	{% endif %}
</section>
<div id="modalZoom" class="modal-image">

	<!-- The Close Button -->
	<span class="close-zoom-image" id="close-zoom-modal" onclick="document.getElementById('modalZoom').style.display='none'">&times;</span>

	<!-- Modal Content (The Image) -->
	<img class="modal-content-image" id="img01">

</div>