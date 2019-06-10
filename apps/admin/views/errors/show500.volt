
<section class="content">
	<div class="error-page">
		<h2 class="headline text-yellow">
			<i class="fa fa-warning text-yellow"></i>
		</h2>
		<div class="error-content">
			<h3>E/Orrore interno</h3>
			<p>È avvenuto qualcosa di orribile! Contatta il supporto per maggiori informazioni.</p>
			<p>Clicca qui per tornare alla {{ link_to('index', 'Dashboard') }}</p>
		</div>
	</div>
	<div id="box-content">
		<div class="row">

			<div class="col-xs-12">
				<div class="box box-solid">
					<div class="box-header with-border">
						<i class="fa fa-exclamation text-red"></i>
						<h3 class="box-title">Dettagli Errore</h3>
					</div>
					<div class="box-body">
						<div class="row">
							<div class="col-xs-12">
								<table class="table table-striped table-responsive">
									<tr>
										<th scope="row">Codice Errore</th>
										<td>{{ error_code }}</td>
									</tr>
									<tr>
										<th scope="row">Messaggio Errore</th>
										<td>{{ error_message }}</td>
									</tr>
									<tr>
										<th scope="row">File</th>
										<td>{{ error_file }}</td>
									</tr>
									<tr>
										<th scope="row">Stack Trace</th>
										<td><pre>{{ error_trace }}</pre></td>
									</tr>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
</section>