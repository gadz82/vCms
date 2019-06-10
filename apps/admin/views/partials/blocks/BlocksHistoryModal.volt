
	<div class="modal modal_custom fade" id="show-history-modal" tabindex="-1" role="dialog" aria-labelledby="show-history-modal-label">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Chiudi"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="show-history-modal-label"><i class="fa fa-history"></i>Dettaglio lavorazione pregessa</h4>
				</div>
							
				<div class="modal-body">
					    
					<div class="box-body">            
						<div class="row">
							<div class="col-xs-12">
								<div class="row">
									<div class="col-md-3 col-xs-12 text-right"><small><strong>Stato block</strong></small></div>
									<div class="col-md-9 col-xs-12"><p>{{ blocks_history.TipologieStatoBlock.descrizione|e }}</p></div>
								</div>
								<hr>
								<div class="row">
									<div class="col-md-3 col-xs-12 text-right"><small><strong>Tipo block</strong></small></div>
									<div class="col-md-9 col-xs-12"><p>{{ blocks_history.TipologieBlock.descrizione|e }}</p></div>
								</div>
								<div class="row">
									<div class="col-md-3 col-xs-12 text-right"><small><strong>Tag</strong></small></div>
									<div class="col-md-9 col-xs-12"><p>{{ blocks_history.BlocksTags.descrizione|e }}</p></div>
								</div>
								<div class="row">
									<div class="col-md-3 col-xs-12 text-right"><small><strong>Contenuto</strong></small></div>
									<div class="col-md-9 col-xs-12"><textarea class="form-control" readonly>{{ blocks_history.content|e }}</textarea></div>
								</div>
								<hr>

								<div class="row">
									<div class="col-md-3 col-xs-12 text-right"><small><strong>Data aggiornamento</strong></small></div>
									<div class="col-md-9 col-xs-12"><p>{{ blocks_history.data_aggiornamento }}</p></div>
								</div>
								<hr>
								<div class="row">
									<div class="col-md-3 col-xs-12 text-right"><small><strong>Utente</strong></small></div>
									<div class="col-md-9 col-xs-12"><p>{{ blocks_history.Utenti.nome_utente|e }}</p></div>
								</div>
							</div>
						</div>
					</div>
										
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary btn-danger" data-dismiss="modal">Chiudi</button>
				</div>				
			</div>
		</div>
	</div>