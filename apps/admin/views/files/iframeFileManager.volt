<script type="text/javascript">
    var baseUrl = '<?= $baseUrl; ?>';
    var entity = '<?= $entityId; ?>';
    var jqGrid_init = <?= json_encode($jqGrid); ?>;
</script>
<section class="content" style="background: #fff;">
    <div id="box-container-search" class="row">
        <div class="col-xs-12">
            <div class="box box-solid">

                {{ form('files/search', 'method':'post', 'autocomplete':'off', 'id':'form_admin_files') }}

                <div class="row">
                    <div class="box-body col-xs-12 col-sm-8">

                        {% for element in form %}
                        <div class="col-xs-3">
                            <div class="form-group">
                                {{ element.label(['class': '']) }}
                                {{ element }}
                            </div>
                        </div>
                        {% endfor %}

                    </div>
                    <div class="col-xs-12 col-sm-4 text-right">
                        <div id="cerca" class="btn btn-primary btn-flat"><i class="fa fa-search fa-fw"></i> Cerca</div>
                        <div id="pulisci" class="btn btn-primary btn-flat"><i class="fa fa-eraser fa-fw"></i> Pulisci</div>
                    </div>
                </div>

                {{ end_form() }}

            </div>
        </div>
    </div>

    <div id="box-container-grid" class="row">

        <div class="box box-solid">
            <div id="box-grid" class="box-body">
                <table id="jqGrid"></table>
                <div id="jqGridPager"></div>
            </div>
        </div>

    </div>
</section>
<div id="modalZoom" class="modal-image">

    <!-- The Close Button -->
    <span class="close-zoom-image" id="close-zoom-modal" onclick="document.getElementById('modalZoom').style.display='none'">&times;</span>

    <!-- Modal Content (The Image) -->
    <img class="modal-content-image" id="img01">

</div>
<div class="modal fade" id="file-embed-modal" tabindex="-1" role="dialog" aria-labelledby="file-embed-modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <strong class="modal-title">Embed risorsa</strong>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="modal-body-embed">

            </div>
        </div>
    </div>
</div>
