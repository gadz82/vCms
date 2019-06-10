<div class="line"></div>
<h4>Contenuti collegati</h4>
<div class="related-posts clearfix">
    {% for index, related in rs %}
        {% if index is even %}
        <div class="row clearfix bottommargin-sm">
        {% endif %}
        <div class="col-sm-6 nobottommargin">
            <div class="mpost clearfix">
                <div class="entry-image">
                    <a href="{{related.slug}}"><img src="/files/small/{{related.filename}}" alt="{{related.titolo}} - {{related.alt}}"></a>
                </div>
                <div class="entry-c">
                    <div class="entry-title">
                        <h4><a href="{{related.slug}}">{{related.titolo}}</a></h4>
                    </div>
                    <div class="entry-content">{{ tags.wordwrapString(related.excerpt, 60) }}...</div>
                </div>
            </div>
        </div>
        {% if index is odd or (index is even and loop.last) %}
        </div>
        {% endif %}
    {% endfor %}
</div>