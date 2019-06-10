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
                        <!-- Entry Image
                                           ============================================= -->
                        <div class="entry-image alignleft">
                            <a href="/files/{{post.immagine.filename}}" data-lightbox="image"><img class="image_fade" src="/files/small/{{post.immagine.filename}}" alt="{{post.titolo}}"></a>
                        </div><!-- .entry-image end -->

                        <p>{{ shortcodes.shortcodify(post.testo) }}</p>
                        {{ tags.renderBlock(post_type.slug~'-'~post.slug) }}
                        {{ tags.renderForm(post_type.slug~'-'~post.slug, post.id_post) }}
                    </div>

                    {{tags.renderRelatedPostsWidget(post, post_type.slug)}}

                </div>
            </div>

        </div>
    </div>
</section><!-- #page-title end -->
