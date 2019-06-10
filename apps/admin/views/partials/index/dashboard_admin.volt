<div id="box-container-dashboard" class="row">

	<div class="col-xs-12" style="margin-top:30px;">
		<div class="box box-solid">
			<div class="box-header with-border">
				<h3 class="box-title">Iscrizioni ricevute</h3>
			</div>
			<div class="row">
				<div class="box-body table-responsive">
					<div class="col-xs-12">
						<table class="table table-bordered table-hover table-striped dataTable" id="datatable">
							<thead>
							<td>#</td>
							<td>Gruppo</td>
							<td>Email</td>
							<td>Data e Ora</td>
							</thead>
							<tbody>
							{% for r in richieste %}
							<?php
							$this->session->set('disabled-soft-delete-clause', 1);
							?>
							<tr>
								<td><a href="/admin/form_requests/edit/{{r.id}}" class="btn btn-sm btn-success"><i class="fa fa-search"></i></a></td>
								<td>{{r.gruppo}}</td>
								<td>{{r.email}}</td>
								<td>{{r.data_creazione}}</td>
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