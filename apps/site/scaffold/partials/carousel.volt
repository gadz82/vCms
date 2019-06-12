<div class="clearfix">
    <div class="section notopmargin notopborder">
        <div class="container clearfix">
            <div class="heading-block center nomargin">
                <h3><!-- TITOLO_POST_TYPE !--> Recenti</h3>
            </div>
        </div>
    </div>

    <div class="container clear-bottommargin clearfix">
        <div class="row">
            {% for entity in rs %}

                <div class="col-md-3 col-sm-6 bottommargin">
                    <div class="ipost clearfix">
                        <div class="entry-image">
                            <a href="{{ entity.readLink }}"><img class="image_fade"
                                                                 src="/files/small/{{ entity.filename }}"
                                                                 alt="{{ entity.titolo }}"></a>
                        </div>
                        <div class="entry-title">
                            <h3><a href="{{ entity.readLink }}">{{ entity.titolo }}</a></h3>
                        </div>
                        <ul class="entry-meta clearfix">
                            <li>
                                <i class="icon-calendar3"></i> {{ date('d/m/Y', strtotime(entity.data_inizio_pubblicazione)) }}
                            </li>
                        </ul>
                        <div class="entry-content">
                            <p>{{ entity.excerpt|striptags }}</p>
                        </div>
                    </div>
                </div>
            {% endfor %}
        </div>
    </div>


</div>