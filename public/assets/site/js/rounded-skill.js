// IIFE - Immediately Invoked Function Expression
(function(yourcode){
    // The global jQuery object is passed as a parameter
    yourcode(window.jQuery, window, document);

}(function($, window, document) {
    if( !$().appear ) {
        console.log('roundedSkill: Appear not Defined.');
        return true;
    }

    if( !$().easyPieChart ) {
        console.log('roundedSkill: EasyPieChart not Defined.');
        return true;
    }

    var $roundedSkillEl = $('.rounded-skill');
    if( $roundedSkillEl.length > 0 ){
        $roundedSkillEl.each(function(){
            var element = $(this);

            var roundSkillSize = element.attr('data-size');
            var roundSkillSpeed = element.attr('data-speed');
            var roundSkillWidth = element.attr('data-width');
            var roundSkillColor = element.attr('data-color');
            var roundSkillTrackColor = element.attr('data-trackcolor');

            if( !roundSkillSize ) { roundSkillSize = 140; }
            if( !roundSkillSpeed ) { roundSkillSpeed = 2000; }
            if( !roundSkillWidth ) { roundSkillWidth = 8; }
            if( !roundSkillColor ) { roundSkillColor = '#0093BF'; }
            if( !roundSkillTrackColor ) { roundSkillTrackColor = 'rgba(0,0,0,0.04)'; }

            var properties = {roundSkillSize:roundSkillSize, roundSkillSpeed:roundSkillSpeed, roundSkillWidth:roundSkillWidth, roundSkillColor:roundSkillColor, roundSkillTrackColor:roundSkillTrackColor};

            if( $body.hasClass('device-lg') || $body.hasClass('device-md') ){
                element.css({'width':roundSkillSize+'px','height':roundSkillSize+'px','line-height':roundSkillSize+'px'}).animate({opacity:0}, 10);
                element.appear( function(){
                    if (!element.hasClass('skills-animated')) {
                        var t = setTimeout( function(){ element.css({opacity: 1}); }, 100 );
                        runRoundedSkills( element, properties );
                        element.addClass('skills-animated');
                    }
                },{accX: 0, accY: -120},'easeInCubic');
            } else {
                SEMICOLON.widget.runRoundedSkills( element, properties );
            }
        });
    }
    function runRoundedSkills ( element, properties ){
        element.easyPieChart({
            size: Number(properties.roundSkillSize),
            animate: Number(properties.roundSkillSpeed),
            scaleColor: false,
            trackColor: properties.roundSkillTrackColor,
            lineWidth: Number(properties.roundSkillWidth),
            lineCap: 'square',
            barColor: properties.roundSkillColor
        });
    }
}));