
	<div class="modal modal_custom fade" id="crea-user-cars-modal" tabindex="-1" role="dialog" aria-labelledby="crea-user-cars-modal">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Chiudi"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="crea-form-fields-modal-label"><i class="fa fa-car"></i>Inserisci Auto</h4>
				</div>
				
				{{ form('admin/users_car/create', 'method':'post', 'autocomplete':'off', 'id':'form-crea-user-cars', 'class':'') }}
							
				<div class="modal-body">
					    
					<div class="box-body">            
						<div class="row">
							<div class="col-xs-12">
								<div class="form_msg"></div>
							</div>
							{% for element in form_user_cars_new %}
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
					<input type="hidden" name="id_user" value="{{id_user}}" id="id_user">
					{{ submit_button("Salva", "class": "btn btn-outline btn-flat" ) }}
				</div>
				
				{{ end_form() }}
				
			</div>
		</div>
	</div>