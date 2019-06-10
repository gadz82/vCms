<table class="table no-margin text-center">
      <thead>
        <tr>
          <th>Azione</th>
          <th>Tipologia</th>
          <th>Stato</th>
          <th>Nome</th>
          <th>Ord.</th>
          <th>Path</th>
          <th>Parametri</th>
        </tr>
      </thead>
      <tbody id="lista-applicazioni-routes">
      	{% for row in routes %}
      	<tr>
      		<td>
      			<div id="btn-grid-2">
      				<div class="btn_grid grid_delete" title="Elimina" id="button-elimina-applicazione-route-{{row.id}}" data-id-applicazione-route="{{row.id}}">
      					<span class="fa fa-trash fa-fw"></span>
      				</div>
      				<div class="btn_grid grid_open" title="Apri" id="button-modifica-applicazione-route-{{row.id}}" data-id-applicazione-route="{{row.id}}" data-toggle="modal" data-target="#modifica-applicazione-route-modal">
      					<span class="fa fa-pencil fa-fw"></span>
      				</div>
      			</div>
      		</td>
      		<td>{{row.TipologieRoutes.descrizione}}</td>
      		<td>{{row.TipologieStatoApplicazioneRoute.descrizione}}</td>
      		<td>{{row.nome}}</td>
      		<td>{{row.ordine}}</td>
      		<td>{{row.path}}</td>
      		<td><pre>{{row.params}}</pre></td>
      	</tr>
      	{% else %}
      	<tr>
			<td>-</td>
			<td>-</td>
			<td>-</td>
			<td>-</td>
			<td>-</td>
		</tr>
      	{% endfor %}
      </tbody>
</table>