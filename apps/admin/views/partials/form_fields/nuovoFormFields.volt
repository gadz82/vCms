
	<div class="modal modal_custom fade" id="crea-form-fields-modal" tabindex="-1" role="dialog" aria-labelledby="crea-form-fields-modal">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Chiudi"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="crea-form-fields-modal-label"><i class="fa fa-pencil"></i>Crea Campo per il Form</h4>
				</div>
				
				{{ form('admin/form_fields/create', 'method':'post', 'autocomplete':'off', 'id':'form-crea-form-fields', 'class':'') }}
							
				<div class="modal-body">
					    
					<div class="box-body">            
						<div class="row">
							<div class="col-xs-12">
								<div class="form_msg"></div>
							</div>
							{% for element in form_form_fields_new %}
								{% if element.getAttribute('hidden') is not true %}
									<div class="col-xs-12">
										<div class="form-group">
											{{ element.label(['class': '']) }}
											{{ element }}
										</div>
									</div>
								{% else %}
									{{ element }}										
								{% endif %}
							{% endfor %}
							
						</div>
					</div>
										
				</div>
				<div class="modal-footer">
					<input type="hidden" name="id_form" value="{{id_form}}" id="id_form">
					{{ submit_button("Salva", "class": "btn btn-success btn-flat" ) }}
				</div>
				
				{{ end_form() }}
				
			</div>
		</div>
	</div>