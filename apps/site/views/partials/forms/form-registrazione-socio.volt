{% do assets.addJs('assets/site/js/forms.js') %}

<div class="contact-widget">
    {% if formEntity.testo is not empty %}
        <p>{{formEntity.testo}}</p>
    {% endif %}
    {{ form('/forms/submit', 'method':'post', 'autocomplete':'off', 'class':'nobottommargin', 'id':'cms-form-'~formEntity.key) }}
        <div class="contact-form-result form_msg" id="{{formEntity.key}}"></div>

        <div class="form-process"></div>
        <div class="row">
            {% for index,element in form %}

                {% if index == 0 or index == 4 %}
                    <div class="col-sm-6">
                {% endif %}

                    {% if element.getAttribute('hidden') is not true and element.getName() !== 'csrf' %}

                        <div class="form-group">
                            {{ form.label(element.getName()) }}
                            {{ form.render(element.getName()) }}
                        </div>

                    {% else %}
                        {% if element.getName() !== 'csrf' %}
                            {{ element }}
                        {% endif %}
                    {% endif %}

                {% if index == 3 or index == 7 %}
                    </div>
                {% endif %}

            {% endfor %}
        </div>

        <div class="form-group">
            <span class="privacy">Accetto la <a href="/pagina/cookie-privacy-policy" target="_blank" rel="nofollow">privacy policy</a> &nbsp;<input type="checkbox" id="privacy" name="privacy" checked="checked" required="1" aria-required="1"></span>
        </div>
        {{ form.render('csrf', ['name': this.security.getTokenKey(), 'value': this.security.getToken()]) }}

        <button type="submit" class="button button-3d nomargin"
                {% for key,val in data %}
                data-{{key}}="{{val}}"
                {% endfor %}
        >{{formEntity.submit_label}}</button>

    {{ end_form() }}
</div>

