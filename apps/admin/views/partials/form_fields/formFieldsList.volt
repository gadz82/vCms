<table class="table no-margin text-center">
      <thead>
        <tr>
          	<th>Azione</th>
          	<th>Nome</th>
			<th>Stato</th>
          	<th>Label</th>
			<th>Ordine</th>
			<th>Tipologia</th>
        </tr>
      </thead>
      <tbody id="lista-applicazioni-domini">
      	{% for row in form_fields %}

      	<tr>
      		<td>
      			<div id="btn-grid-2">
      				<div class="btn_grid grid_delete" title="Elimina" id="button-elimina-form-fields-{{row.id}}" data-id-form-fields="{{row.id}}">
      					<span class="fa fa-trash fa-fw"></span>
      				</div>
      				<div class="btn_grid grid_open" title="Apri" id="button-modifica-form-fields-{{row.id}}" data-id-form-fields="{{row.id}}" data-toggle="modal" data-target="#modifica-form-fields-modal">
      					<span class="fa fa-pencil fa-fw"></span>
      				</div>
      			</div>
      		</td>
      		<td>{{row.name}}</td>
      		<td>{{row.TipologieStatoFormFields.descrizione}}</td>
      		<td>{{row.label}}</td>
			<td>{{row.ordine}}</td>
			<td>{{row.TipologieFormFields.descrizione}}</td>
      	</tr>
      	{% else %}
      	<tr><td>-</td><td>-</td><td>-</td><td>-</td></tr>
      	{% endfor %}
      </tbody>
</table>