<script type="text/javascript">
    var users_groups = '<?= $usersGroups; ?>'
</script>
<div id="box-content-upload" style="background: #fff;">
    <div class="row">
        <div class="col-xs-12">
            <div class="box-body">
                <div class="row">
                    <div class="col-xs-12">
                        <div id="box-fileupload-container" class="col-xs-12">

                            <div id="upload-dropzone" class="box-upload-dropzone fade text-center">

                                {{ form('admin/files/create', 'method':'post', 'autocomplete':'off', 'enctype':'multipart/form-data', 'class':'', 'name':'form-posts-files', 'id':'fileupload') }}
                                <p><strong>Trascina il file qui</strong></p>
                                <p>
                                    <small>- oppure -</small>
                                </p>
                                <p class="btn btn-flat btn-primary fileinput-button">
                                    <i class="fa fa-plus fa-fw"></i>
                                    <span>Aggiungi</span>
                                    <input id="fileupload" type="file" name="file[]" data-url="/admin/files/create"
                                            {% if metaInput is not empty %}
                                           data-meta-input="{{ metaInput }}"
                                           {% endif %}multiple
                                           data-users-groups='{{ usersGroups }}'
                                    >
                                </p>
                                <input type="hidden" name="id_entity" value="">
                                {{ end_form() }}

                            </div>

                            <div id="box-upload-list">
                                <ul class="products-list product-list-in-box files"></ul>
                            </div>

                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>