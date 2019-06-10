
	<div class="modal modal_custom fade" id="crea-users-car-events-modal" tabindex="-1" role="dialog" aria-labelledby="crea-users-car-events-modal">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Chiudi"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="crea-form-fields-modal-label"><i class="fa fa-car"></i>Inserisci Promemoria</h4>
				</div>
				
				{{ form('admin/users_car_events/create', 'method':'post', 'autocomplete':'off', 'id':'form-crea-users-car-events', 'class':'') }}
							
				<div class="modal-body">
					    
					<div class="box-body">            
						<div class="row">
							<div class="col-xs-12">
								<div class="form_msg"></div>
							</div>
							{% for element in form_users_car_events_new %}
								{% if element.getAttribute('hidden') is not true %}
									{% set grid_class = element.getAttribute('grid_class') is not empty ? element.getAttribute('grid_class') : 'col-lg-12' %}
									<div class="{{grid_class}}">
										<div class="form-group">
											{{ element.label(['class': '']) }}
											{% if element.getName() !== 'orario_preavviso' %}
												{{ element }}
											{% else %}
												<input type="time" name="orario_preavviso" class="form-control" id="orario_preavviso" value="">
											{% endif %}
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