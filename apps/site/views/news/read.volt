{% if isAdminLoggedIn %}
    {{ partial('partials/adminEdit', ['entity_id': post.id_post]) }}
{% endif %}
<section id="page-title">

    <div class="container clearfix">


        <ol class="breadcrumb" itemscope itemtype="http://schema.org/BreadcrumbList">
            <li class="breadcrumb-item home-act" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
                <a href="{{ applicationUrl }}" itemscope itemtype="http://schema.org/Thing" itemprop="item">
                    <span itemprop="name">Home</span>
                </a>
                <meta itemprop="position" content="1" />
            </li>
            <li class="breadcrumb-item home-act" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
                <a href="{{ applicationUrl ~ post_type.slug}}/" itemscope itemtype="http://schema.org/Thing" itemprop="item">
                    <span itemprop="name">Notizie</span>
                </a>
                <meta itemprop="position" content="2" />
            </li>

            <li class="breadcrumb-item active" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
                <a href="{{ applicationUrl ~ post_type.slug}}/{{post.slug}}" itemscope itemtype="http://schema.org/Thing" itemprop="item">
                    <span itemprop="name">{{post.titolo}}</span>
                </a>
                <meta itemprop="position" content="3" />
            </li>

        </ol>
    </div>
    </section>
    <section id="content">

        <div class="content-wrap p-t-lg-30">

            <div class="container clearfix">

                <!-- Post Content
                ============================================= -->
                <div class="postcontent nobottommargin clearfix">

                    <div class="single-post nobottommargin">

                        <!-- Single Post
                        ============================================= -->
                        <div class="entry clearfix">

                            <!-- Entry Title
                            ============================================= -->
                            <div class="entry-title">
                                <h1>{{post.titolo}}</h1>
                            </div><!-- .entry-title end -->

                            <!-- Entry Meta
                            ============================================= -->
                            <ul class="entry-meta clearfix">
                                <li><i class="icon-calendar3"></i> {{date('d/m/Y', strtotime(post.data_inizio_pubblicazione))}}</li>
                            </ul><!-- .entry-meta end -->
                            <!-- Entry Image
                            ============================================= -->
                            <div class="entry-image">
                                <a href="/files/{{post.immagine.filename}}" data-lightbox="image"><img class="image_fade" src="/files/{{post.immagine.filename}}" alt="{{post.titolo}}"></a>
                            </div><!-- .entry-image end -->
                            {% if post.meta_immagini_gallery is not empty and post.meta_immagini_gallery is not null %}
                            <div id="oc-images" class="owl-carousel image-carousel carousel-widget" data-margin="20" data-nav="true" data-pagi="false" data-items-xxs="2" data-items-xs="3" data-items-sm="4" data-items-md="5">

                                {% for img in post.meta_immagini_gallery %}
                                    <div class="oc-item">
                                        <a href="/files/{{img.filename}}" data-lightbox="image"><img src="/files/small/{{img.filename}}" class="image_fade" alt="{{img.alt}}"></a>
                                    </div>
                                {% endfor %}
                            </div>
                            {% endif %}

                            <!-- Entry Content
                            ============================================= -->
                            <div class="entry-content notopmargin">

                                <p>{{ shortcodes.shortcodify(post.testo) }}</p>
                                {{ tags.renderBlock(post_type.slug~'-'~post.slug) }}
                                {{ tags.renderForm(post_type.slug~'-'~post.slug, post.id_post) }}
                            </div>

                        </div>
                    </div>
                </div>
                <div class="sidebar nobottommargin col_last clearfix">
                    <div class="sidebar-widgets-wrap">


                        <!-- Tag: banner_listing -->
                        {{ tags.renderBlock('banner_listing_'~post_type.slug~'_read') }}


                    </div>
                </div>
            </div>
        </div>
    </section><!-- #page-title end -->
