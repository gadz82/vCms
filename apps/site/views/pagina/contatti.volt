<section id="page-title">

    <div class="container clearfix">
        <h1>{{post.titolo}}</h1>

        <ol class="breadcrumb" itemscope itemtype="http://schema.org/BreadcrumbList">
            <li class="breadcrumb-item home-act" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
                <a href="/" itemscope itemtype="http://schema.org/Thing" itemprop="item">
                    <span itemprop="name">Home</span>
                </a>
                <meta itemprop="position" content="1" />
            </li>

            <li class="breadcrumb-item active" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
                <a href="/pagina/{{post.slug}}" itemscope itemtype="http://schema.org/Thing" itemprop="item">
                    <span itemprop="name">{{post.titolo}}</span>
                </a>
                <meta itemprop="position" content="4" />
            </li>


        </ol>
    </div>
</section>
<!-- Content
		============================================= -->
<section id="content">

    <div class="content-wrap">

        <div class="container clearfix">

            <!-- Contact Form
            ============================================= -->
            <div class="col_half">

                {{ tags.renderForm(post_type.slug~'-'~post.slug, post.id_post) }}

            </div><!-- Contact Form End -->

            <!-- Google Map
            ============================================= -->
            <div class="col_half col_last">

                <section id="google-map" class="gmap" style="height: 410px;"></section>

            </div><!-- Google Map End -->

            <div class="clear"></div>

            <!-- Contact Info
            ============================================= -->
            <div class="row clear-bottommargin">

                <div class="col-md-4 col-sm-4 bottommargin clearfix">
                    <div class="feature-box fbox-center fbox-bg fbox-plain">
                        <div class="fbox-icon">
                            <a href="#"><i class="icon-map-marker2"></i></a>
                        </div>
                        <h3>Sede<span class="subtitle">Perugia, Piazza Italia 1</span></h3>
                    </div>
                </div>

                <div class="col-md-4 col-sm-4 bottommargin clearfix">
                    <div class="feature-box fbox-center fbox-bg fbox-plain">
                        <div class="fbox-icon">
                            <a href="#"><i class="icon-phone3"></i></a>
                        </div>
                        <h3>Telefono<span class="subtitle">+39 075 572 8043</span></h3>
                    </div>
                </div>


                <div class="col-md-4 col-sm-4 bottommargin clearfix">
                    <div class="feature-box fbox-center fbox-bg fbox-plain">
                        <div class="fbox-icon">
                            <a href="#"><i class="icon-facebook2"></i></a>
                        </div>
                        <h3>Seguici su Facebook<span class="subtitle">#gustourconad</span></h3>
                    </div>
                </div>

            </div><!-- Contact Info End -->

        </div>

    </div>

</section><!-- #content end -->
<script type="text/javascript" src="https://maps.google.com/maps/api/js?key=xxx"></script>

{{tags.injectJsFromDi('/assets/site/js/jquery.gmap.js')}}
{{tags.injectJsFromDi('/assets/site/js/gmap.js')}}

