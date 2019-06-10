<div class="modal modal-large modal_custom fade" id="post-files-modal" tabindex="-1" role="dialog" aria-labelledby="post-files-modal">
	<div class="modal-dialog" role="document">
		<div id="box-content-upload">
			<div class="row">
				<div class="col-xs-12">
					<div class="box box-solid">
						<div class="box-body">
							<div class="row">
								<div class="col-xs-12">
									<div id="box-fileupload-container">
										<?php /*
										<div id="upload-dropzone" class="box-upload-dropzone fade text-center">

											{{ form('', 'method':'post', 'autocomplete':'off', 'enctype':'multipart/form-data', 'class':'', 'name':'form-posts-files', 'id':'fileupload', 'action':'/admin/files/create') }}
												<p><strong>Trascina il file qui</strong></p>
												<p><small>- oppure -</small></p>
												<p class="btn btn-flat btn-primary fileinput-button">
													<i class="fa fa-plus fa-fw"></i>
													<span>Aggiungi</span>
													<input id="fileupload" type="file" name="file" data-url="/admin/files/create" accept="image/jpeg,image/png,image/gif">
												</p>
												<input type="hidden" name="id_entity" value="">

											{{ end_form() }}
										</div>
										<div id="box-upload-list">
											<ul class="products-list product-list-in-box files"></ul>
										</div>
 								*/ ?>
										<div id="box-file-list">
											<iframe frameborder="0" id="iframe-file-upload" src="/admin/files/iframeUpload" style="width:100%;height: 15vh;"></iframe>
											<iframe frameborder="0" id="iframe-file-list" src=""  style="width:100%;height: 80vh;"></iframe>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
