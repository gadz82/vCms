{% if hasParent %}
    <div class="col-lg-12" id="dynamic-filter-realtions">
        <div class="form-group ">
            <label for="id_filtro_valore_parent">Tassonomia filtro genitore collegato
                - {{ titolo_filtro_parent }}</label>
            <select id="id_filtro_valore_parent" name="id_filtro_valore_parent" class="form-control selectpicker">
                {% for element in valori %}
                    <option value="{{ element.id }}">{{ element.valore }}</option>
                {% endfor %}
            </select>
        </div>
    </div>
{% else %}
    <div class="col-lg-12" id="dynamic-filter-realtions">
        <div class="form-group ">
            <label for="id_filtro_valore_parent">Tassonomia collegata - {{ titolo_filtro_parent }}</label>
            <select id="id_filtro_valore_parent" name="id_filtro_valore_parent" class="form-control selectpicker"
                    data-style="btn-flat btn-white"
                    data-size="15"
                    data-width="100%"
                    data-live-search="1"
                    data-actions-box="1"
                    data-use-empty="1"
                    data-empty-text="---"
                    data-selected-text-format="count>1">
                <option value="">---</option>
                {% for element in valori %}
                    <option value="{{ element.id }}">{{ element.valore }}</option>
                {% endfor %}
            </select>
        </div>
    </div>
{% endif %}