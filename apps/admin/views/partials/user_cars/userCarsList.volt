<table class="table no-margin text-center">
      <thead>
        <tr>
          	<th>Azione</th>
          	<th>Auto</th>
			<th>Targa</th>
          	<th>Stato</th>
			<th>Valore</th>
			<th>Mese e Anno</th>
        </tr>
      </thead>
      <tbody id="lista-applicazioni-domini">
      	{% for row in user_cars %}

      	<tr>
      		<td>
      			<div id="btn-grid-2">
      				<div class="btn_grid grid_delete" title="Elimina" id="button-elimina-user-cars-{{row.id}}" data-id-user-cars="{{row.id}}">
      					<span class="fa fa-trash fa-fw"></span>
      				</div>
      				<div class="btn_grid grid_open" title="Apri" id="button-modifica-user-cars-{{row.id}}" data-id-user-cars="{{row.id}}" data-toggle="modal" data-target="#modifica-user-cars-modal">
      					<span class="fa fa-pencil fa-fw"></span>
      				</div>
      			</div>
      		</td>
      		<td>{{ row.modello }}</td>
      		<td>{{ row.targa }}</td>
      		<td>{{ row.TipologieStatoUserCar.descrizione }}</td>
			<td>{{ row.valore_acquisto }}</td>
			<td>{{ row.data_acquisto }}</td>
      	</tr>
      	{% else %}
      	<tr><td>-</td><td>-</td><td>-</td><td>-</td></tr>
      	{% endfor %}
      </tbody>
</table>