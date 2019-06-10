
var inputLat = document.querySelector('input#lat'),
    inputLng = document.querySelector('input#lng'),
    inputZoom = document.querySelector('input#zoom'),
    map,
    marker,
    position = {lat : 43.1197381, lng : 12.4495334},
    zoom = 8;

window.initMap = function() {
    initPosition();
    map = new google.maps.Map(document.getElementById('map'), {
        zoom: zoom,
        center: position
    });
    initMarker();
    initListener();
}

function initPosition(){
    if(inputLat.value.length > 0) position.lat = parseFloat(inputLat.value);
    if(inputLng.value.length > 0) position.lng = parseFloat(inputLng.value);
    if(inputZoom.value.length > 0) zoom = parseInt(inputZoom.value);
}

function initMarker() {
    marker = new google.maps.Marker({
        position: position,
        map: map
    });
}

function changeFromInput(){
    marker.setPosition(position);
    setTimeout(function(){
        map.panTo(position);
        map.setZoom(zoom);
    }, 1000);

}

function initListener(){
    google.maps.event.addListener(map, 'click', function(event) {
        marker.setPosition(event.latLng);
        inputLat.value = event.latLng.lat();
        inputLng.value = event.latLng.lng();
        setTimeout(function(){
            map.panTo(event.latLng);
        }, 1000);
    });
    google.maps.event.addListener(map, 'zoom_changed', function() {
        zoomLevel = map.getZoom();
        inputZoom.value = zoomLevel;
    });
}

inputLat.addEventListener("change", function(){
    initPosition();
    changeFromInput();
});
inputLng.addEventListener("change", function(){
    initPosition();
    changeFromInput();
});
inputZoom.addEventListener("change", function(){
    initPosition();
    changeFromInput();
});



var fileref = document.createElement("script");
fileref.setAttribute("type", "text/javascript");
fileref.setAttribute("async", true);
fileref.setAttribute("defer", true);
fileref.setAttribute("src", "https://maps.googleapis.com/maps/api/js?key=AIzaSyBIIbMrkbXb4S3aOzG84EUR9_DdDNyil5c&callback=initMap");
document.getElementsByTagName('body')[0].appendChild(fileref);

