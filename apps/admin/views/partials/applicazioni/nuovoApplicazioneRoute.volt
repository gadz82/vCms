
	<div class="modal modal_custom fade" id="crea-applicazione-route-modal" tabindex="-1" role="dialog" aria-labelledby="crea-applicazione-route-modal">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Chiudi"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="crea-applicazione-route-modal-label"><i class="fa fa-pencil"></i>Crea Route associata all'Applicazione</h4>
				</div>
				
				{{ form('admin/applicazioni_routes/create', 'method':'post', 'autocomplete':'off', 'id':'form-crea-applicazione-route', 'class':'') }}
							
				<div class="modal-body">
					    
					<div class="box-body">            
						<div class="row">
							<div class="col-xs-12">
								<div class="form_msg"></div>
							</div>
							{% for element in form_applicazione_route_new %}
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
					<input type="hidden" name="id_applicazione" value="{{id_applicazione}}" id="id_applicazione">
					{{ submit_button("Salva", "class": "btn btn-outline btn-flat" ) }}
				</div>
				
				{{ end_form() }}
				
			</div>
		</div>
	</div>