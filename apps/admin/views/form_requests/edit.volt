<script type="text/javascript">
	var json_render_required = '<?= $render_required; ?>'
</script>
<?php
$this->session->set('disabled-soft-delete-clause', 1);
?>

<section class="content-header">
	<h1>Richieste Form <small>dettaglio</small></h1>
	<ol class="breadcrumb">
		<li>{{ link_to('admin/index/index', '<i class="fa fa-home"></i>Home') }}</li>
		<li>{{ link_to('admin/form_requests/index', 'Richieste Form') }}</li>
		<li class="active">Dettaglio richiesta form</li>
	</ol>
	{{ get_content() }}
	
	{{ flashSession.output() }}
	
</section>
	
<section class="content">
	<div id="box-content-edit">
		<div class="row">
			{% set class = controller_data.Posts ? 'col-md-7 col-sm-12' : 'col-sm-12' %}
			<div class="{{class}}">
				<div class="box box-solid">
					<div class="box-header with-border">
						<i class="fa fa-pencil"></i>
						<h3 class="box-title">{{ controller_data.Forms.titolo }} - <strong>{{ controller_data.Posts ? controller_data.Posts.titolo : 'Auto non più disponibile'  }}</strong></h3>
						<span class="fa fa-chevron-down fa-fw pull-right"></span>
					</div>

					<div class="box-body">
						<div class="row">
							<div class="col-xs-12">
								<table class="table table-striped table-bordered">
									{% for field in fields %}
										{% if field.FormFields is true %}
										<tr>
											<td>
												<strong>{{field.FormFields.label}}</strong>
											</td>
											<td>
												{% if field.FormFields.TipologieFormFields.id is '2' %}
													<a href="mailto:{{field.input_value}}?subject=Re: {{ controller_data.Forms.titolo }} - {{  controller_data.Posts ? controller_data.Posts.titolo : '' }}">{{field.input_value}}</a>
												{% elseif field.FormFields.TipologieFormFields.id is '3' %}
													<a href="tel:{{field.input_value}}">{{field.input_value}}</a>
												{% else %}
													{{field.input_value}}
												{% endif %}
											</td>
										</tr>
										{% endif %}
									{% endfor %}
									<tr>
										<td>
											<strong>Data e Ora</strong>
										</td>
										<td>
											{{ date('d/m/Y H:i:s', strtotime(controller_data.data_creazione)) }}
										</td>
									</tr>
								</table>
					{{ form('', 'method':'post', 'autocomplete':'off', 'class':'', 'name':'form-edit', 'id':'form-edit', 'novalidate':'novalidate') }}
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
						{{ link_to('admin/form_requests/index', 'Indietro', 'class':'btn btn-primary btn-flat') }}
					</div>


					{{ end_form() }}

				</div>
			</div>

			{% if  controller_data.Posts %}
			<div class="col-md-5 col-sm-12">
				<div class="box box-solid">
					<div class="box-header with-border">
						<i class="fa fa-pencil"></i>
						<h3 class="box-title">Oggetto della richiesta</h3>
						<span class="fa fa-chevron-down fa-fw pull-right"></span>
					</div>
					<div class="box-body">
						<div class="row">
							<div class="col-xs-12">
								<table class="table table-striped table-bordered">
									<tr>
										<td>
											<strong>Titolo</strong>
										</td>
										<td>
											{{ controller_data.Posts ? controller_data.Posts.titolo : ''}}
										</td>
									</tr>
									<tr>
										<td>
											<strong>Descrizione</strong>
										</td>
										<td>
											{{ controller_data.Posts ? controller_data.Posts.testo : ''}}
										</td>
									</tr>

									<tr>
										<td>
											<strong>Anteprima</strong>
										</td>
										<td class="text-center">
											<a class="btn btn-success" href="/{{controller_data.Posts.TipologiePost.slug}}/{{controller_data.Posts.slug}}" target="_blank">Apri</a>&nbsp;
											<a class="btn btn-primary" href="/admin/posts/edit/{{controller_data.Posts.id}}" target="_blank">Admin</a>
										</td>
									</tr>

								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
			{% endif %}
		</div>
	</div>

	
	
	
</section>