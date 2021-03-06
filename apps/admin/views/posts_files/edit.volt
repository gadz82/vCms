<script type="text/javascript">
	var json_render_required = '<?= $render_required; ?>'
</script>

<section class="content-header">
	<h1>Posts Files <small>dettaglio</small></h1>
	<ol class="breadcrumb">
		<li>{{ link_to('admin/index/index', '<i class="fa fa-home"></i>Home') }}</li>
		<li>{{ link_to('admin/posts_files/index', 'Posts Files') }}</li>
		<li class="active">Dettaglio post file</li>
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
						<h3 class="box-title">Post File - Id Post File | <strong>{{ controller_data.id }}</strong></h3>
						<span class="fa fa-chevron-down fa-fw pull-right"></span>
	                </div>
	                
	                <div class="box-body">
						<div class="row">
							<div class="col-xs-12">
																					
								{% for element in form %}
									{% if element.getAttribute('hidden') is not true %}
										<div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
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
						{{ link_to('admin/posts_files/index', 'Indietro', 'class':'btn btn-primary btn-flat') }}
					</div>
					
				</div>
			</div>
		</div>
	</div>
	
	{{ end_form() }}
	
	
	
	
</section>