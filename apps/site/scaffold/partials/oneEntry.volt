<div class="{{ class }}">
    <div class="entry-image">
        <a href="/files/{{ entry.filename }}" data-lightbox="image"><img class="image_fade"
                                                                         src="/files/small/{{ entry.filename }}"
                                                                         alt="{{ entry.titolo }} - {{ entry.alt }}"></a>
    </div>

    <div class="entry-title">
        <h4><a href="{{ entry.readLink }}">{{ entry.titolo }}</a></h4>
    </div>
    <ul class="entry-meta clearfix">
        <li><i class="icon-calendar3"></i> {{ date('d/m/Y', strtotime(entry.data_inizio_pubblicazione)) }}</li>
    </ul>
    <div class="entry-content m-t-lg-15">
        <p class="nomargin">{{ entry.excerpt }}</p>
        <a href="{{ entry.readLink }}" class="more-link">Leggi</a>
    </div>

</div>