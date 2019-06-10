<script type="text/javascript">
	var json_render_required = '<?= $render_required; ?>'
</script>

<section class="content-header">
	<h1>Files Sizes <small>crea</small></h1>
	<ol class="breadcrumb">
		<li>{{ link_to('admin/index/index', '<i class="fa fa-home"></i>Home') }}</li>
		<li>{{ link_to('admin/files_sizes/index', 'Files Sizes') }}</li>
		<li class="active">Dettaglio file size</li>
	</ol>
	{{ get_content() }}
	
	{{ flashSession.output() }}
	
</section>
	
<section class="content">
	
	{{ form('admin/files_sizes/create', 'method':'post', 'autocomplete':'off', 'class':'', 'name':'form-edit', 'id':'form-edit', 'novalidate':'novalidate') }}
	
	<div id="box-content-edit">
		<div class="row">
		
			<div class="col-xs-12">
				<div class="box box-solid">
					<div class="box-header with-border">
						<i class="fa fa-pencil"></i>
						<h3 class="box-title">File Size - Nuovo Filtro</h3>
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
						{{ link_to('admin/files_sizes/index', 'Indietro', 'class':'btn btn-primary btn-flat') }}
					</div>
					
				</div>
			</div>
		</div>
	</div>
	
	{{ end_form() }}
	
	
	
	
</section>