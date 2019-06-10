// IIFE - Immediately Invoked Function Expression
(function(yourcode){
    // The global jQuery object is passed as a parameter
    yourcode(window.jQuery, window, document);

}(function($, window, document) {
    if( typeof Instafeed === 'undefined' ) {
        console.log('Instafeed not Defined.');
        return true;
    }

    var $instagramPhotosEl = $('.instagram-photos');
    if( $instagramPhotosEl.length > 0 ){

        $instagramPhotosEl.each(function() {
            var element = $(this),
                instaGramTarget = element.attr('id'),
                instaGramUserId = element.attr('data-user'),
                instaGramTag = element.attr('data-tag'),
                instaGramLocation = element.attr('data-location'),
                instaGramCount = element.attr('data-count'),
                instaGramType = element.attr('data-type'),
                instaGramSortBy = element.attr('data-sortBy'),
                instaGramRes = element.attr('data-resolution');

            if( !instaGramCount ) { instaGramCount = 9; }
            if( !instaGramSortBy ) { instaGramSortBy = 'none'; }
            if( !instaGramRes ) { instaGramRes = 'thumbnail'; }

            if( instaGramType == 'user' ) {

                var feed = new Instafeed({
                    target: instaGramTarget,
                    get: instaGramType,
                    userId: Number(instaGramUserId),
                    limit: Number(instaGramCount),
                    sortBy: instaGramSortBy,
                    resolution: instaGramRes,
                    accessToken: c_accessToken,
                    clientId: c_clientID
                });

            } else if( instaGramType == 'tagged' ) {

                var feed = new Instafeed({
                    target: instaGramTarget,
                    get: instaGramType,
                    tagName: instaGramTag,
                    limit: Number(instaGramCount),
                    sortBy: instaGramSortBy,
                    resolution: instaGramRes,
                    clientId: c_clientID
                });

            } else if( instaGramType == 'location' ) {

                var feed = new Instafeed({
                    target: instaGramTarget,
                    get: instaGramType,
                    locationId: Number(instaGramUserId),
                    limit: Number(instaGramCount),
                    sortBy: instaGramSortBy,
                    resolution: instaGramRes,
                    clientId: c_clientID
                });

            } else {

                var feed = new Instafeed({
                    target: instaGramTarget,
                    get: 'popular',
                    limit: Number(instaGramCount),
                    sortBy: instaGramSortBy,
                    resolution: instaGramRes,
                    clientId: c_clientID
                });

            }

            feed.run();
        });
    }
}));
