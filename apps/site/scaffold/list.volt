<section id="page-title">

    <div class="container clearfix">
        <h1>{{ post_type.descrizione }}</h1>
        {% if hasFilters is true %}
            <span class="title">
                {% for valore in filters %}
                    {% if loop.first is false %} - {% endif %}{{ __.t(valore.valore) }}
                {% endfor %}
            </span>
        {% endif %}
        <ol class="breadcrumb" itemscope itemtype="http://schema.org/BreadcrumbList">
            <li class="home-act breadcrumb-item" itemprop="itemListElement" itemscope
                itemtype="http://schema.org/ListItem">
                <a href="{{ applicationUrl }}" itemscope itemtype="http://schema.org/Thing" itemprop="item">
                    <span itemprop="name">Home</span>
                </a>
                <meta itemprop="position" content="1"/>
            </li>
            {% if hasFilters is true %}
                {% set urlstring = '' %}
                <li class="breadcrumb-item" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
                    <a href="{{ applicationUrl ~ post_type.slug }}/" itemscope itemtype="http://schema.org/Thing"
                       itemprop="item">
                        <span itemprop="name">{{ __.t(post_type.descrizione) }}</span>
                    </a>
                    <meta itemprop="position" content="2"/>
                </li>
                {% for valore in filters %}
                    {% set urlstring = urlstring~'/'~valore.Filtri.key~'-'~valore.key %}
                    <li class="{% if loop.last %} active{% endif %} breadcrumb-item" itemprop="itemListElement"
                        itemscope itemtype="http://schema.org/ListItem">
                        <a href="{{ applicationUrl ~ post_type.slug }}/list{{ urlstring }}" itemscope
                           itemtype="http://schema.org/Thing" itemprop="item">
                            <span itemprop="name">{{ __.t(valore.valore) }}</span>
                        </a>
                        <meta itemprop="position" content="{{ loop.index+2 }}"/>
                    </li>
                {% endfor %}
            {% else %}
                <li class="active breadcrumb-item" itemprop="itemListElement" itemscope
                    itemtype="http://schema.org/ListItem">

                    <a href="{{ applicationUrl ~ post_type.slug }}/" itemscope itemtype="http://schema.org/Thing"
                       itemprop="item">
                        <span itemprop="name">{{ __.t(post_type.descrizione) }}</span>
                    </a>
                    <meta itemprop="position" content="2"/>
                </li>
            {% endif %}
        </ol>
    </div>
</section><!-- #page-title end -->
{{ flashSession.output() }}
<section id="content">

    <div class="content-wrap">

        <div class="container clearfix">

            <!-- Post Content
            ============================================= -->
            <div class="postcontent nobottommargin clearfix">

                <!-- Posts
                ============================================= -->
                <div id="posts" class="small-thumbs">
                    {% for index, post in results %}

                        <div class="entry clearfix">

                            {% if post.file %}
                                <div class="entry-image">
                                    {% if post.file.private == 0 %}
                                        <a href="/files/{{ post.file.filename }}" data-lightbox="image"><img
                                                    class="image_fade" src="/files/16:9/{{ post.file.filename }}"
                                                    alt="{{ post.titolo }} - {{ post.file.alt }}"></a>
                                    {% else %}
                                        <a href="/media/render/{{ post.file.filename }}" data-lightbox="image"><img
                                                    class="image_fade"
                                                    src="/media/render/{{ post.file.filename }}?size=16:9"
                                                    alt="{{ post.titolo }} - {{ post.file.alt }}"></a>
                                    {% endif %}
                                </div>
                            {% endif %}

                            <div class="entry-c">
                                <div class="entry-title">
                                    <h3><a href="{{ post.readLink }}">{{ post.titolo }}</a></h3>
                                </div>
                                <ul class="entry-meta clearfix">
                                    <li>
                                        <i class="icon-calendar3"></i> {{ date('d/m/Y', strtotime(post.data_inizio_pubblicazione)) }}
                                    </li>
                                </ul>
                                <div class="entry-content m-t-lg-15">
                                    <p class="nomargin">{{ post.excerpt }}</p>
                                    <a href="{{ post.readLink }}" class="more-link">Leggi</a>
                                </div>
                            </div>
                        </div>

                    {% else %}
                        <div class="row">
                            <div class="col-sm-12 text-center">
                                <h4>Nessun contenuto</h4>
                            </div>
                        </div>
                    {% endfor %}

                    {% if total_pages > 1 %}
                        <nav aria-label="Page navigation">
                            <ul class="pagination">
                                {% if current_page > 1 %}
                                    <li class="page-item">
                                        <a href="{{ prevPageUrl }}" aria-label="Previous" ref="prev" class="page-link">
                                            <span aria-hidden="true"><i class="fa fa-chevron-left"></i></span>
                                        </a>
                                    </li>
                                {% else %}
                                    <li class="page-item disabled">
                                        <span aria-hidden="true" class="page-link"><i
                                                    class="fa fa-chevron-left"></i></span>
                                    </li>
                                {% endif %}

                                {% for i in 1..total_pages %}
                                    <li
                                            {% if i == current_page %}
                                                class="page-item active"
                                            {% endif %}
                                    >
                                        <a href="{{ pagingUrl~i }}"
                                                {% if i == (current_page+1) %}
                                                    rel="next"
                                                {% elseif i == (current_page-1) %}
                                                    rel="prev"
                                                {% endif %}
                                           class="page-link"
                                        >{{ i }}</a>
                                    </li>

                                {% endfor %}

                                {% if current_page < total_pages %}
                                    <li class="page-item">
                                        <a href="{{ nextPageUrl }}" aria-label="Next" rel="next" class="page-link">
                                            <span aria-hidden="true"><i class="fa fa-chevron-right"></i></span>
                                        </a>
                                    </li>
                                {% else %}
                                    <li class="page-item disabled">
                                        <span aria-hidden="true" class="page-link"><i
                                                    class="fa fa-chevron-right"></i></span>
                                    </li>
                                {% endif %}

                            </ul>
                        </nav>
                    {% endif %}
                </div>
            </div>
            <div class="sidebar nobottommargin col_last clearfix">
                <div class="sidebar-widgets-wrap">
                    <!-- Tag: banner_listing -->
                    {{ tags.renderBlock('banner_listing_'~post_type.slug) }}
                </div>
            </div>

        </div>
    </div>
</section>