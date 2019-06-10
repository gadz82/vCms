{% if lastNews is defined %}
<section class="m-b-lg-50">
    <!-- Recent news -->
    <div class="blog blog-list overl">
        <div class="heading">
            <h3>Eventi e Notizie</h3>
        </div>
        <div class="row">
            <div class="owl" data-items="3" data-itemsDesktop="3" data-itemsDesktopSmall="2" data-itemsTablet="2" data-itemsMobile="1" data-pag="false" data-buttons="true">
                {% for news in lastNews %}
                <div class="col-lg-12">
                    <!-- Blog item -->
                    <div class="blog-item">
                        <a href="{{news['link']}}" class="hover-img"><img src="{{news['immagine']}}" alt="{{news['alt_immagine']}}"></a>
                        <div class="blog-caption text-center">
                            <ul class="blog-date">
                                <li><a href="{{news['link']}}"><i class="fa fa-calendar"></i>{{news['data_inizio_pubblicazione']}}</a></li>
                            </ul>
                            <h3 class="blog-heading"><a href="{{news['link']}}">{{news['titolo']}}</a></h3>
                            <p>{{news['excerpt']|striptags}}</p>
                            <a href="{{news['link']}}" class="ht-btn ht-btn-default">Leggi</a>
                        </div>
                    </div>
                </div>
                {% endfor %}
            </div>
        </div>
</section>
{% endif %}