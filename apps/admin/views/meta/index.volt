<script type="text/javascript">
	var entity = '<?= $entityId; ?>';
	var jqGrid_init = <?= json_encode($jqGrid); ?>;
</script>

<section class="content-header">
	<h1>Meta <small>ricerca</small></h1>
	<ol class="breadcrumb">
		<li><a href="<?= $this->url->get("admin/index/index"); ?>"><i class="fa fa-home"></i>Home</a></li>
		<li class="active">Meta</li>
	</ol>
	{{ flashSession.output() }}
</section>

<section class="content">

	<div id="box-container-search" class="row">
		<div class="col-xs-12">
			<div class="box box-solid">
				<div class="box-header with-border">
					<i class="fa fa-search"></i>
					<h3 class="box-title">Form ricerca</h3>
					<span class="fa fa-chevron-down fa-fw pull-right"></span>
                </div>
                
                {{ form('meta/search', 'method':'post', 'autocomplete':'off', 'id':'form_admin_meta') }}

					<div class="row">
						<div class="box-body col-xs-12">
											
							{% for element in form %}
								<div class="col-lg-3">
									<div class="form-group">
										{{ element.label(['class': '']) }}
										{{ element }}
									</div>
								</div>
							{% endfor %}
							
						</div>
					</div>
					<div class="box-footer text-right">
						<div id="cerca" class="btn btn-primary btn-flat"><i class="fa fa-search fa-fw"></i> Cerca</div>
						<div id="pulisci" class="btn btn-primary btn-flat"><i class="fa fa-eraser fa-fw"></i> Pulisci</div>
						<div id="esporta" class="btn btn-primary btn-flat"><i class="fa fa-mail-forward fa-fw"></i> Esporta</div>
					</div>
					
				{{ end_form() }}
				
			</div>
		</div>
	</div>

	<div id="box-container-grid" class="row">
		<div class="col-xs-12">
			<div class="box box-solid">
				<div class="box-header with-border">
					<i class="fa fa-bars"></i>
					<h3 class="box-title">Risultato ricerca</h3>
                  	<span class="fa fa-chevron-down fa-fw pull-right"></span>
                </div>
				<div id="box-grid" class="box-body">
					<table id="jqGrid"></table>
					<div id="jqGridPager"></div>
				</div>
			</div>
		</div>
	</div>	

</section>