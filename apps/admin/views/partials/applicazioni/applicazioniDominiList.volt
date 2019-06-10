<table class="table no-margin text-center">
      <thead>
        <tr>
          <th>Azione</th>
          <th>Dominio</th>
          <th>Stato</th>
          <th>IP</th>
        </tr>
      </thead>
      <tbody id="lista-applicazioni-domini">
      	{% for row in domini %}
      	{% set attivo = row.attivo  == 1 ? 'Attivo' : 'Non attivo' %} 
      	<tr>
      		<td>
      			<div id="btn-grid-2">
      				<div class="btn_grid grid_delete" title="Elimina" id="button-elimina-applicazione-dominio-{{row.id}}" data-id-applicazione-dominio="{{row.id}}">
      					<span class="fa fa-trash fa-fw"></span>
      				</div>
      				<div class="btn_grid grid_open" title="Apri" id="button-modifica-applicazione-dominio-{{row.id}}" data-id-applicazione-dominio="{{row.id}}" data-toggle="modal" data-target="#modifica-applicazione-dominio-modal">
      					<span class="fa fa-pencil fa-fw"></span>
      				</div>
      			</div>
      		</td>
      		<td>{{row.referer}}</td>
      		<td>{{attivo}}</td>
      		<td>{{row.ip_autorizzati}}</td>
      	</tr>
      	{% else %}
      	<tr><td>-</td><td>-</td><td>-</td><td>-</td></tr>
      	{% endfor %}
      </tbody>
</table>