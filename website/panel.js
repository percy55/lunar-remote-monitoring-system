var map;
function initialize()
{
  var mapOptions = {
    zoom: 8,
    center: new google.maps.LatLng(-26.79086, 153.13439),
    mapTypeId: google.maps.MapTypeId.ROADMAP
  };
  map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
}

google.maps.event.addDomListener(window, 'load', initialize);
