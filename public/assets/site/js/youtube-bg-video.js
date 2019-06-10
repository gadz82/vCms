// IIFE - Immediately Invoked Function Expression
(function(yourcode){
    // The global jQuery object is passed as a parameter
    yourcode(window.jQuery, window, document);

}(function($, window, document) {
    if( !$().mb_YTPlayer ) {
        console.log('youtubeBgVideo: YoutubeBG Plugin not Defined.');
        return true;
    }

    var $youtubeBgPlayerEl = $('.yt-bg-player');
    if( $youtubeBgPlayerEl.hasClass('customjs') ) { return true; }

    if( $youtubeBgPlayerEl.length > 0 ){
        $youtubeBgPlayerEl.each( function(){
            var element = $(this),
                ytbgVideo = element.attr('data-video'),
                ytbgMute = element.attr('data-mute'),
                ytbgRatio = element.attr('data-ratio'),
                ytbgQuality = element.attr('data-quality'),
                ytbgOpacity = element.attr('data-opacity'),
                ytbgContainer = element.attr('data-container'),
                ytbgOptimize = element.attr('data-optimize'),
                ytbgLoop = element.attr('data-loop'),
                ytbgVolume = element.attr('data-volume'),
                ytbgStart = element.attr('data-start'),
                ytbgStop = element.attr('data-stop'),
                ytbgAutoPlay = element.attr('data-autoplay'),
                ytbgFullScreen = element.attr('data-fullscreen');

            if( ytbgMute == 'false' ) { ytbgMute = false; } else { ytbgMute = true; }
            if( !ytbgRatio ) { ytbgRatio = '16/9'; }
            if( !ytbgQuality ) { ytbgQuality = 'hd720'; }
            if( !ytbgOpacity ) { ytbgOpacity = 1; }
            if( !ytbgContainer ) { ytbgContainer = 'self'; }
            if( ytbgOptimize == 'false' ) { ytbgOptimize = false; } else { ytbgOptimize = true; }
            if( ytbgLoop == 'false' ) { ytbgLoop = false; } else { ytbgLoop = true; }
            if( !ytbgVolume ) { ytbgVolume = 1; }
            if( !ytbgStart ) { ytbgStart = 0; }
            if( !ytbgStop ) { ytbgStop = 0; }
            if( ytbgAutoPlay == 'false' ) { ytbgAutoPlay = false; } else { ytbgAutoPlay = true; }
            if( ytbgFullScreen == 'true' ) { ytbgFullScreen = true; } else { ytbgFullScreen = false; }

            element.mb_YTPlayer({
                videoURL: ytbgVideo,
                mute: ytbgMute,
                ratio: ytbgRatio,
                quality: ytbgQuality,
                opacity: Number(ytbgOpacity),
                containment: ytbgContainer,
                optimizeDisplay: ytbgOptimize,
                loop: ytbgLoop,
                vol: Number(ytbgVolume),
                startAt: Number(ytbgStart),
                stopAt: Number(ytbgStop),
                autoplay: ytbgAutoPlay,
                realfullscreen: ytbgFullScreen,
                showYTLogo: false,
                showControls: false
            });
        });
    }
}));
