{% if isAdminLoggedIn %}
    {{ partial('partials/adminEdit', ['entity_id': post.id_post]) }}
{% endif %}
<section id="page-title">

    <div class="container clearfix">


        <ol class="breadcrumb" itemscope itemtype="http://schema.org/BreadcrumbList">
            <li class="breadcrumb-item home-act" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
                <a href="/" itemscope itemtype="http://schema.org/Thing" itemprop="item">
                    <span itemprop="name">Home</span>
                </a>
                <meta itemprop="position" content="1" />
            </li>
            <li class="breadcrumb-item home-act" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
                <a href="/news/" itemscope itemtype="http://schema.org/Thing" itemprop="item">
                    <span itemprop="name">Notizie</span>
                </a>
                <meta itemprop="position" content="2" />
            </li>


            <li class="breadcrumb-item active" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
                <a href="/news/{{post.slug}}" itemscope itemtype="http://schema.org/Thing" itemprop="item">
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

            <!-- Post Content
            ============================================= -->
            <div class="postcontent nobottommargin col_last clearfix">

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

                        <!-- Entry Content
                        ============================================= -->
                        <div class="entry-content notopmargin">

                            <p>{{ shortcodes.shortcodify(post.testo) }}</p>
                            {{ tags.renderBlock(post_type.slug~'-'~post.slug) }}
                            {{ tags.renderForm(post_type.slug~'-'~post.slug, post.id_post) }}
                        </div>
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

                        {{tags.renderRelatedPostsWidget(post, post_type.slug)}}

                    </div>
                </div>
            </div>
            <div class="sidebar nobottommargin clearfix">
                <div class="sidebar-widgets-wrap">

                    <div class="widget clearfix">

                        <div class="tabs nobottommargin clearfix ui-tabs ui-widget ui-widget-content ui-corner-all" id="sidebar-tabs">

                            <ul class="tab-nav clearfix ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all" role="tablist">
                                <li class="ui-state-default ui-corner-top ui-tabs-active ui-state-active" role="tab" tabindex="0" aria-controls="tabs-1" aria-labelledby="ui-id-1" aria-selected="true" aria-expanded="true"><a href="#tabs-1" class="ui-tabs-anchor" role="presentation" tabindex="-1" id="ui-id-1">News</a></li>
                                <li class="ui-state-default ui-corner-top" role="tab" tabindex="-1" aria-controls="tabs-2" aria-labelledby="ui-id-2" aria-selected="false" aria-expanded="false"><a href="#tabs-2" class="ui-tabs-anchor" role="presentation" tabindex="-1" id="ui-id-2">Eventi</a></li>
                                <li class="ui-state-default ui-corner-top" role="tab" tabindex="-1" aria-controls="tabs-3" aria-labelledby="ui-id-3" aria-selected="false" aria-expanded="false"><a href="#tabs-3" class="ui-tabs-anchor" role="presentation" tabindex="-1" id="ui-id-3">Press</a></li>
                            </ul>

                            <div class="tab-container">

                                <div class="tab-content clearfix ui-tabs-panel ui-widget-content ui-corner-bottom" id="tabs-1" aria-labelledby="ui-id-1" role="tabpanel" aria-hidden="false" style="display: block;">
                                    <div id="popular-post-list-sidebar">
                                        {{ tags.renderWidgetLastEntities('news', 'last', 6) }}


                                    </div>
                                </div>
                                <div class="tab-content clearfix ui-tabs-panel ui-widget-content ui-corner-bottom" id="tabs-2" aria-labelledby="ui-id-2" role="tabpanel" aria-hidden="true" style="display: none;">
                                    <div id="recent-post-list-sidebar">

                                        {{ tags.renderWidgetLastEntities('eventi', 'last', 6) }}

                                    </div>
                                </div>


                            </div>

                        </div>

                    </div>

                    <!-- Tag: banner_listing -->
                    {{ tags.renderBlock('banner_listing') }}


                </div>
            </div>
        </div>
    </div>
</section><!-- #page-title end -->
