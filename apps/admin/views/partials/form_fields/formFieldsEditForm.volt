{{ form('admin/form_fields/edit/'~id_form_field, 'method':'post', 'autocomplete':'off', 'id':'form-edit-form-fields', 'class':'') }}
			
<div class="modal-body">
	    
	<div class="box-body">            
		<div class="row">
			<div class="col-xs-12">
				<div class="form_msg"></div>
			</div>
			{% for element in form_form_fields_edit %}
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
	{{ submit_button("Salva", "class": "btn btn-success btn-flat" ) }}
</div>

{{ end_form() }}