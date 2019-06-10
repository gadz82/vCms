<section class="content-header">
	<h1>
		Gestioni Indici Tabelle Flat<small>{{ application.appName }}</small>
	</h1>
	<ol class="breadcrumb">
		<li><a><i class="fa fa-home"></i>Home</a></li>
	</ol>
</section>

<section class="content">
	<div id="box-container-dashboard" class="row">
		<div class="col-xs-12">
			<div class="box box-solid">
				<div class="box-header with-border">
					<h3 class="box-title">Genera Tabelle Flat - Ricostruisci Indici</h3>
				</div>

				<div class="row">
					<div class="box-body col-xs-12">
						<div class="col-xs-12">
							<p>Attenzione, rigenerando gli indici alcune informazioni potrebbero andare perse, controlla che i post siano allineati e testa il tutto in ambiente di DEV</p>
						</div>
						<div class="col-xs-12">
							<table class="table table-bordered">
								<tbody>
								<tr>
									<th style="width: 10px">ID</th>
									<th>Tipologia</th>
									<th>Nr. Post</th>
									<th>Status</th>
									<th>Azione</th>
								</tr>
								{% for pt in post_types %}
									<tr>
										<td>{{pt['id']}}</td>
										<td>{{pt['descrizione']}}</td>
										<td><span class="badge bg-green">{{pt['numero_post']}}</span></td>
										<td class="text-center">
											{% if pt['status'] == 'ok' %}
												<span class="label label-success" id="label-index-{{pt['id']}}">Indice OK</span>
											{% else %}
												<span class="label label-danger" id="label-index-{{pt['id']}}">Indice Obsoleto</span>
											{% endif %}
										</td>
										<td class="text-center">
											{% if pt['status'] == 'ok' %}
												<button type="button" class="btn btn-success btn-xs" id="index-{{pt['id']}}">Aggiorna Indice</button>
											{% else %}
												<button type="button" class="btn btn-danger btn-xs" id="reindex-{{pt['id']}}">Rigenera Indice</button>
											{% endif %}
										</td>
									</tr>
								{% endfor %}

								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

</section>