{% do assets.addJs('assets/site/js/forms.js') %}

<div class="fancy-title title-dotted-border">
    <h3>{{formEntity.titolo}}</h3>
</div>

<div class="contact-widget">
    {% if formEntity.testo is not empty %}
    <p>{{formEntity.testo}}</p>
    {% endif %}
    {{ form('site/forms/handle', 'method':'post', 'autocomplete':'off', 'class':'nobottommargin', 'id':'cms-form-'~formEntity.key) }}
    <div class="contact-form-result form_msg" id="{{formEntity.key}}"></div>

    <div class="form-process"></div>

    <div class="col_one_third">
        {{ form.label('nome') }}
        {{ form.render('nome') }}
    </div>

    <div class="col_one_third">
        {{ form.label('email') }}
        {{ form.render('email') }}
    </div>

    <div class="col_one_third col_last">
        {{ form.label('telefono') }}
        {{ form.render('telefono') }}
    </div>

    <div class="clear"></div>

    <div class="col_full">
        {{ form.label('oggetto') }}
        {{ form.render('oggetto') }}
    </div>


    <div class="clear"></div>

    <div class="col_full">
        {{ form.label('messaggio') }}
        {{ form.render('messaggio') }}
    </div>
    {{ form.render('id_post') }}
    {{ form.render('id_form') }}
    {{ form.render('form_key') }}
    <div class="form-group">
        <span class="privacy">Accetto la <a href="/pagina/cookie-privacy-policy" target="_blank" rel="nofollow">privacy policy</a> &nbsp;<input type="checkbox" id="privacy" name="privacy" checked="checked" disabled="1" required="1" aria-required="1"></span>
    </div>

    {{ form.render('csrf', ['name': this.security.getTokenKey(), 'value': this.security.getToken()]) }}

    <button type="submit" class="button button-3d nomargin"
            {% for key,val in data %}
            data-{{key}}="{{val}}"
            {% endfor %}
    >{{formEntity.submit_label}}</button>

    {{ end_form() }}
</div>

