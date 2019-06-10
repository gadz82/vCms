<table class="table no-margin text-center">
      <thead>
        <tr>
          	<th>Azione</th>
          	<th>Auto</th>
			<th>Tipologia Evento</th>
          	<th>Data Evento</th>
          	<th>Notifica</th>
			<th>Titolo</th>
        </tr>
      </thead>
      <tbody id="lista-applicazioni-domini">
      	{% for row in users_car_events %}

      	<tr>
      		<td>
      			<div id="btn-grid-2">
      				<div class="btn_grid grid_delete" title="Elimina" id="button-elimina-users-car-events-{{row.id}}" data-id-users-car-events="{{row.id}}">
      					<span class="fa fa-trash fa-fw"></span>
      				</div>
      				<div class="btn_grid grid_open" title="Apri" id="button-modifica-users-car-events-{{row.id}}" data-id-users-car-events="{{row.id}}" data-toggle="modal" data-target="#modifica-users-car-events-modal">
      					<span class="fa fa-pencil fa-fw"></span>
      				</div>
      			</div>
      		</td>
      		<td>{{ row.UsersCar.modello }}</td>
      		<td>{{ row.TipologieUserCarEvent.descrizione }}</td>
      		<td>{{ row.data_event }}</td>
			<td>{{ row.notifica }}</td>
			<td>{{ row.titolo }}</td>
      	</tr>
      	{% else %}
      	<tr><td>-</td><td>-</td><td>-</td><td>-</td></tr>
      	{% endfor %}
      </tbody>
</table>