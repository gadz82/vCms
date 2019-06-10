<section class="content-header">
	<h1>
		Utenti<small>crea</small>
	</h1>
	<ol class="breadcrumb">
		<li>{{ link_to('admin/index/index', '<i class="fa fa-home"></i>Home') }}
		</li>
		<li>{{ link_to('admin/utenti/index', 'Utenti') }}</li>
		<li class="active">Crea utente</li>
	</ol>
	{{ get_content() }}
</section>

<section class="content">

	<div class="row">
		<div class="col-xs-12">
			<div class="box box-solid">
				<div class="box-header with-border">
					<i class="fa fa-user"></i>
					<h3 class="box-title">
						Utente | <strong>Nuovo</strong>
					</h3>
				</div>

				{{ form('admin/utenti/create', 'method':'post', 'autocomplete':'off',
				'class':'') }}

				<div class="row">
					<div class="box-body col-xs-12">
						<div class="col-lg-12">

							{% for element in form %} {% if element.getAttribute('hidden') is
							not true %}
							<div class="col-sm-6 col-xs-12">
								<div class="form-group">{{ element.label(['class': '']) }} {{
									element }}</div>
							</div>
							{% elseif element.getName() is not 'csrf' %} {{ element }} {%
							endif %} {% endfor %}

						</div>
					</div>
				</div>

				{{ form.render('csrf', ['value': security.getToken()]) }}

				<div class="box-footer text-right">{{ submit_button("Salva",
					"class": "btn btn-success btn-flat") }} {{ link_to('utenti/index',
					'Indietro', 'class':'btn btn-primary btn-flat') }}</div>
				{{ end_form() }}
			</div>
		</div>

	</div>

</section>
