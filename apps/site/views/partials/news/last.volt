
<div class="widget widget-twitter-feed clearfix">
    {% for entity in rs %}
        <div class="spost clearfix">
            <div class="entry-image">
                <a href="{{entity.readLink}}" class="nobg">
                    <div class="lazy display-block p-lg-30" data-src="/files/thumb_square/{{entity.filename}}"></div>
                </a>
            </div>
            <div class="entry-c p-l-lg-10">
                <div class="entry-title">
                    <h4><a href="{{entity.readLink}}">{{entity.titolo}}</a></h4>
                </div>
            </div>
        </div>
    {% else %}
        <p>Non sono presenti <!-- TITOLO_POST_TYPE !--></p>
    {% endfor %}

</div>