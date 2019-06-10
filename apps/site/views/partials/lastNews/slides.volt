{% if lastNews is defined %}
<section class="m-b-lg-50">
    <!-- Recent news -->
    <div class="blog blog-list overl">
        <div class="heading">
            <h3>Eventi e Notizie</h3>
        </div>
        <div class="row">
            <div class="owl" data-items="2" data-itemsDesktop="2" data-itemsDesktopSmall="1" data-itemsTablet="2" data-itemsMobile="1" data-pag="false" data-buttons="true">
                {% for news in lastNews %}
                <div class="col-lg-12">
                    <div class="blog-item">
                        <div class="row">
                            <div class="col-sm-6 col-md-6 col-lg-6">
                                <a href="{{news['link']}}" class="hover-img"><img src="{{news['immagine']}}" alt="{{news['alt_immagine']}}"></a>
                            </div>
                            <div class="col-sm-6 col-md-6 col-lg-6">
                                <div class="blog-caption text-left">
                                    <ul class="blog-date blog-date-left p-t-lg-0">
                                        <li><a href="{{news['link']}}"><i class="fa fa-calendar"></i>{{news['data_inizio_pubblicazione']}}</a></li>
                                    </ul>
                                    <h3 class="blog-heading wrapped-text"><a href="{{news['link']}}">{{news['titolo']}}</a></h3>
                                    <p>{{news['excerpt']}}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {% endfor %}
            </div>
        </div>
</section>
{% endif %}