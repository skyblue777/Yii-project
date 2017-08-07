var map = null;
var geocoder = null;
// Global stuff
var mymarker;
var locMarker;
var icon = new GIcon();
    icon.image = "images/inkon.png";
    icon.iconSize = new GSize(32, 39);
    icon.shadowSize = new GSize(37, 34);
    icon.iconAnchor = new GPoint(9, 34);
    icon.infoWindowAnchor = new GPoint(9, 2);
var MyIcon = new GIcon();
    MyIcon.image = "images/inkon2.png";
    MyIcon.iconSize = new GSize(19, 32);
    MyIcon.shadowSize = new GSize(37, 34);
    MyIcon.iconAnchor = new GPoint(9, 34);
    MyIcon.infoWindowAnchor = new GPoint(9, 2);

    function initializeGMap() {
    var lat=document.getElementById('lat').value;
    var lng=document.getElementById('lng').value;
	if(lat!=0 && lng!=0){	lat0=lat;lng0=lng	}

      if (GBrowserIsCompatible()) {
        map = new GMap2(document.getElementById("map_canvas"));
        map.setCenter(new GLatLng(lat0, lng0), 11);
		map.setZoom(zoom);
        if (map_zoom)
			map.addControl(new GSmallMapControl());
		if (map_type==2) map.setMapType(G_SATELLITE_MAP);
		else if (map_type==3) map.setMapType(G_HYBRID_MAP);
		else map.setMapType(G_NORMAL_MAP);
        map.addControl(new GMapTypeControl());
        geocoder = new GClientGeocoder();

	 if(lat!=0 && lng!=0){
	 var locpoint= new GLatLng(lat,lng);
        locMarker = new GMarker(locpoint);
        map.addOverlay(locMarker);
	  }

      }
     // showAddress("Wilkie road, Singapore","Inkiti :\n");

   GEvent.addListener(map, 'click', function( overlay, point )
{
 				if (locMarker)
                { map.removeOverlay(locMarker);}

                if (mymarker)
                {  map.removeOverlay(mymarker);}

                if (point)
                {
                 map.panTo(point);
                        mymarker = new GMarker(point);
                        map.addOverlay(mymarker);
            document.getElementById("lat").value=point.y;
            document.getElementById("lng").value=point.x;
                }
                latLon = point;

});

  }


    function showAddress(address,txt) {
      if (geocoder) {
        geocoder.getLatLng(
          address,
          function(point) {
            if (!point) {
              alert(address + " not found");
            } else {
              map.setCenter(point, 13);
              var marker = new GMarker(point);
              map.addOverlay(marker);
              marker.openInfoWindowHtml(txt+address);
            }
          }
        );
      }
    }
