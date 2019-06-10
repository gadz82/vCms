{{ tags.renderBlock('home-slider') }}

<!-- Content
============================================= -->
<section id="content">

    <div class="content-wrap">
        {{ tags.renderBlock('home-under-slider') }}

        <div class="container clearfix">

            <div class="row ">
                {{ shortcodes.shortcodify(post.testo) }}
            </div>
        </div>

        {{tags.renderWidgetLastEntities('news', 'carousel', 4)}}
    </div>
</section>
