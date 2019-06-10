<script type="text/javascript">
	var json_render_required = '<?= $render_required; ?>'
</script>

<section class="content-header">
	<h1>Applicazioni <small>dettaglio</small></h1>
	<ol class="breadcrumb">
		<li>{{ link_to('admin/index/index', '<i class="fa fa-home"></i>Home') }}</li>
		<li>{{ link_to('admin/applicazioni/index', 'Applicazioni') }}</li>
		<li class="active">Dettaglio applicazione</li>
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
						<h3 class="box-title">Applicazione - Id Applicazione | <strong>{{ controller_data.id }}</strong></h3>
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
						{{ link_to('admin/applicazioni/index', 'Indietro', 'class':'btn btn-primary btn-flat') }}
					</div>
					
				</div>
			</div>
		</div>
	</div>
	
	{{ end_form() }}
	
	<div id="box-content-edit">
		<div class="row">

			<div class="col-xs-12">
				<div class="box box-solid">
					<div class="box-header with-border">
						<i class="fa fa-pencil"></i>
						<h3 class="box-title">Applicazione - Routes</h3>
						<span class="fa fa-chevron-down fa-fw pull-right"></span>
					</div>
					<div class="box-body">
						<div class="row">
							<div class="col-xs-12 text-right">
								<button type="button" class="btn btn-warning btn-flat" data-toggle="modal" data-target="#crea-applicazione-route-modal">Crea</button>
							</div>
							<div class="col-xs-12" id="box-content-applicazioni-routes">
								{{ partial("partials/applicazioni/applicazioniRoutesList", ['routes': routes]) }}
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div>{{ partial("partials/applicazioni/nuovoApplicazioneRoute", ['id_applicazione': controller_data.id]) }}</div>
	<div>{{ partial("partials/applicazioni/modalEditApplicazioneRoute") }}</div>





	<div id="box-content-edit">
		<div class="row">

			<div class="col-xs-12">
				<div class="box box-solid">
					<div class="box-header with-border">
						<i class="fa fa-pencil"></i>
						<h3 class="box-title">Applicazione - Domini Associati</h3>
						<span class="fa fa-chevron-down fa-fw pull-right"></span>
					</div>
					<div class="box-body">
						<div class="row">
							<div class="col-xs-12 text-right">
								<button type="button" class="btn btn-warning btn-flat" data-toggle="modal" data-target="#crea-applicazione-dominio-modal">Crea</button>
							</div>
							<div class="col-xs-12" id="box-content-applicazioni-domini">
								{{ partial("partials/applicazioni/applicazioniDominiList", ['domini': domini]) }}
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div>{{ partial("partials/applicazioni/nuovoApplicazioneDominio", ['id_applicazione': controller_data.id]) }}</div>
	<div>{{ partial("partials/applicazioni/modalEditApplicazioneDominio") }}</div>


</section>