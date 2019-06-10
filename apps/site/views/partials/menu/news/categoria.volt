<div class="widget clearfix">
    <h4 class="widget-title">Categorie</h4>
    <div class="list-group">
        {% for val in filtri_valori %}
            <a href="{{ baseUri }}/list/{{val['key_filtro']}}-{{val['key_filtro_valore']}}" class="list-group-item{% if val['active'] is defined and val['active'] is true %} active{% endif %}">{{val['titolo_valore']}}</a>
            {% if val['childrens'] is not empty %}

                    {% for valc in val['childrens'] %}
                        <a class="list-group-item {% if valc['active'] is defined and valc['active'] is true %} active{% endif %}" href="{{ baseUri }}/list/{{valc['key_filtro']}}-{{valc['key_filtro_valore']}}"><i class="icon-chevron-right f-6 m-r-lg-10"></i>{{valc['titolo_valore']}}</a>
                    {% endfor %}

            {% endif %}
        {% endfor %}
        {% if active_widget_show_all %}
            <div class="card-footer list-group-item show-all">
                <a href="{{ baseUri }}/"><i class="fa fa-chevron-left m-r-lg-10"></i>Mostra Tutte</a>
            </div>
        {% endif %}
    </div>
</div>