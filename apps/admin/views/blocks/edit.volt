<script type="text/javascript">
	var json_render_required = '<?= $render_required; ?>'
</script>

<section class="content-header">
	<h1>Blocks <small>dettaglio</small></h1>
	<ol class="breadcrumb">
		<li>{{ link_to('admin/index/index', '<i class="fa fa-home"></i>Home') }}</li>
		<li>{{ link_to('admin/blocks/index', 'Blocks') }}</li>
		<li class="active">Dettaglio block</li>
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
						<h3 class="box-title">Block - Id Block | <strong>{{ controller_data.id }}</strong></h3>
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
						{{ link_to('admin/blocks/index', 'Indietro', 'class':'btn btn-primary btn-flat') }}
					</div>
					
				</div>
			</div>
		</div>
	</div>
	
	{{ end_form() }}

	<div id="box-content-lavorazione-pregressa">

		<div class="row">
			<div class="col-xs-12">
				<div class="box box-solid">
					<div class="box-header with-border">
						<i class="fa fa-history"></i>
						<h3 class="box-title">Lavorazione pregressa</h3>
						<span class="fa fa-chevron-down fa-fw pull-right"></span>
					</div>

					<div class="box-body">
						<div class="row">
							<div class="col-xs-12">
								{% for history in controller_data_history %}
								{% if loop.first %}
								<div class="table-responsive table-custom">
									<table id="table-history" class="table table-responsive table-bordered table-hover text-center" cellspacing="0" width="100%">
										<thead>
										<tr>
											<th>Azione</th>
											<th>Stato</th>
											<th>Data</th>
											<th>Utente</th>
										</tr>
										</thead>
										<tbody>
										{% endif %}
										<tr>
											<td><button type="button" class="btn bg-maroon btn-sm btn-flat btn-show-history" data-history="{{ history.id }}"><i class="fa fa-search"></i></button></td>
											<td>{{ history.TipologieStatoBlock.descrizione|e }}</td>
											<td>{{ history.data_aggiornamento }}</td>
											<td>{{ history.Utenti.nome_utente }}</td>
										</tr>
										{% if loop.last %}
										</tbody>
									</table>
								</div>
								{% endif %}
								{% else %}
								<p>Nessuna lavorazione pregressa disponibile per questo <!-- TITOLO_SINGOLARE_LOWERCASE !-->.</p>
								{% endfor %}
							</div>
						</div>
					</div>

				</div>
			</div>
		</div>
	</div>

	<div id="box-modal-history"></div>
	
	
</section>