// IIFE - Immediately Invoked Function Expression
(function(yourcode){
    // The global jQuery object is passed as a parameter
    yourcode(window.jQuery, window, document);

}(function($, window, document) {
    $(function(){
        $(document).ready(function(){
            var gapi = $('section#content').data('google_api_key');
            $('section#content').after('<script async defer src="https://maps.googleapis.com/maps/api/js?key='+gapi+'&callback=initMap"></script>')
        });
        window.initMap = function(){

           var map = new google.maps.Map(document.getElementById('map'), {
               zoom: 6,
               center: {lat: 40.681354, lng: 14.891344}
           });

           // Create an array of alphabetical characters used to label the markers.

           // Add some markers to the map.
           // Note: The code uses the JavaScript Array.prototype.map() method to
           // create an array of markers based on a given "locations" array.
           // The map() method here has nothing to do with the Google Maps API.
           // Add a marker clusterer to manage the markers.
            var locations = []
            var labels = [];

            $.ajax({
                url: window.location.pathname,
                type: 'POST',
                data: {},
                cache: false,
                success: function (data){
                    if(data.success == true && data.pdv !== undefined){
                        var nr = data.pdv.length;
                        for(var i = 0; i < nr; i++){
                            locations.push({
                                lat: parseFloat(data.pdv[i].lat),
                                lng : parseFloat(data.pdv[i].lng)
                            });
                            labels.push(data.pdv[i].nome);
                        }
                        var markers = locations.map(function(location, i) {
                            return new google.maps.Marker({
                                position: location,
                                label: labels[i % labels.length]
                            });
                        });
                       var markerCluster = new MarkerClusterer(map, markers, {
                           imagePath: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m',
                           gridSize: 80
                       });
                        console.log(markerCluster.getGridSize());
                    }
                },
                error: function(){
                    form.find('.form_msg').append('<div class="row"><div class="alert alert-danger alert-dismissible" role="alert"><span class="fa fa-exclamation-circle" aria-hidden="true"></span><span class="sr-only">Errore!</span></div></div>').fadeIn('fast');
                    loader.fadeOut('fast', function(){
                        submit.remove();
                    });
                }

            });
        }

    });
}));