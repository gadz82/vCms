{% if isAdminLoggedIn %}
    {{ partial('partials/adminEdit', ['entity_id': post.id_post]) }}
{% endif %}
{% if post.immagine is not empty and post.immagine.filename is not null %}
<section id="slider" class="slider-parallax swiper_wrapper full-screen clearfix slider-pagina"
         {% if post.meta_immagini_gallery is not empty and post.meta_immagini_gallery is not null %}
         data-autoplay="4000" data-speed="650" data-loop="true"
         {% endif %}
>
    <div class="slider-parallax-inner">

        <div class="swiper-container swiper-parent">
            <div class="swiper-wrapper">
                <div class="swiper-slide dark" style="background-image: url('/files/large/{{post.immagine.filename}}');">
                    <div class="container clearfix">
                        <div class="slider-caption slider-caption-center">
                            <h2 data-caption-animate="fadeInUp">{{post.titolo}}</h2>
                            <p data-caption-animate="fadeInUp" data-caption-delay="200">{{post.excerpt}}</p>
                        </div>
                    </div>
                </div>

                {% if post.meta_immagini_gallery is not empty and post.meta_immagini_gallery is not null %}
                    {% for img in post.meta_immagini_gallery %}
                        <div class="swiper-slide dark" style="background-image: url('/files/large/{{img.filename}}');">
                            <div class="container clearfix">
                                <div class="slider-caption slider-caption-center">
                                    <h2 data-caption-animate="fadeInUp">{{img.alt}}</h2>
                                </div>
                            </div>
                        </div>
                    {% endfor %}
                {% endif %}
            </div>
            {% if post.meta_immagini_gallery is not empty and post.meta_immagini_gallery is not null %}
                <div id="slider-arrow-left"><i class="icon-angle-left"></i></div>
                <div id="slider-arrow-right"><i class="icon-angle-right"></i></div>
            {% endif %}

        </div>
        <a href="#" data-scrollto="#content" data-offset="230" class="dark one-page-arrow"><i class="icon-angle-down infinite animated fadeInDown"></i></a>
    </div>
</section>

{% endif %}

<section id="page-title" id="content">

    <div class="container clearfix">
        <h1 class="hidden-sm hidden-xs">{{post.titolo}}</h1>

        <ol class="breadcrumb" itemscope itemtype="http://schema.org/BreadcrumbList">
            <li class="breadcrumb-item home-act" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
                <a href="{{ applicationUrl }}" itemscope itemtype="http://schema.org/Thing" itemprop="item">
                    <span itemprop="name">Home</span>
                </a>
                <meta itemprop="position" content="1" />
            </li>

            <li class="breadcrumb-item active" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
                <a href="{{ applicationUrl ~ post.slug}}" itemscope itemtype="http://schema.org/Thing" itemprop="item">
                    <span itemprop="name">{{post.titolo}}</span>
                </a>
                <meta itemprop="position" content="4" />
            </li>

        </ol>
    </div>
</section>
<section id="content">

    <div class="content-wrap">

        <div class="container clearfix">
            {{ shortcodes.shortcodify(post.testo) }}
            {{ tags.renderBlock(post_type.slug~'-'~post.slug) }}
            {{ tags.renderForm(post_type.slug~'-'~post.slug, post.id_post) }}
        </div>

    </div>
</section><!-- #page-title end -->
