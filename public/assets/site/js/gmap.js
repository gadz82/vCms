// IIFE - Immediately Invoked Function Expression
(function(yourcode){
	// The global jQuery object is passed as a parameter
	yourcode(window.jQuery, window, document);

}(function($, window, document) {


	$('#google-map').gMap({
		latitude: 43.129922,
		longitude: 12.453157,
		zoom: 14,
		markers: [
			{
				latitude: 43.129922,
				longitude: 12.453157
			}
		],
		icon: {
			image: "/assets/site/images/icons/map-icon.png",
			iconsize: [32, 39],
			iconanchor: [20,39]
		},
		doubleclickzoom: false,
		controls: {
			panControl: true,
			zoomControl: true,
			mapTypeControl: false,
			scaleControl: false,
			streetViewControl: false,
			overviewMapControl: false
		}
	});
}));
