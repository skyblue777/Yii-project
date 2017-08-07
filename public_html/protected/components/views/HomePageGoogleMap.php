<?php Yii::app()->clientScript->registerScriptFile('http://maps.googleapis.com/maps/api/js?sensor=false&key='.MapSettings::GAPI,CClientScript::POS_HEAD); ?>
<div id="map_canvas" style="width: 270px; height: 168px; margin-bottom: 5px;"></div>
<script type="text/javascript">
var arrMapAds = new Array();
<?php foreach($ads as $ad) : ?>
    <?php if($ad->lat!=0 && $ad->lng!=0) :
      $urlParams = array('id'=>$ad->id,
                         'alias'=>str_replace(array(' ','/','\\'),'-',$ad->title));
      if ($ad->area != '')
        $urlParams['area'] = $ad->area;
     ?>
        arrMapAds.push({
            'url':'<?php echo Yii::app()->createUrl('/Ads/ad/viewDetails',$urlParams); ?>',
            'title':'<?php echo getFirstWordsFromString(str_replace("'",'',$ad->title),5); ?>',
            'lat':'<?php echo str_replace("'",'',$ad->lat); ?>',
            'lng':'<?php echo str_replace("'",'',$ad->lng); ?>',
            'price':'<?php if (isset($ad->arrNotPaymentPriceOptions[$ad->opt_price])) echo Language::t(Yii::app()->language,'Frontend.Ads.Form',$ad->arrNotPaymentPriceOptions[$ad->opt_price]); else echo AdsSettings::CURRENCY.' '.$ad->price; ?>',
            'marker' : null,
            'infoWindow' : null
        });
    <?php endif; ?>        
<?php endforeach; ?>

var lat0 = "<?php echo $lat; ?>";
var lng0 = "<?php echo $lng; ?>";
var zoom = '<?php echo MapSettings::ZOOM; ?>';
var map_zoom = '<?php echo MapSettings::MAP_ZOOM; ?>';
var map_type = '<?php echo MapSettings::MAP_TYPE; ?>';
var map = null;
var geocoder = null;
// Global stuff
var mymarker;
var locMarker;

function initializeGoogleMap()
{
    var map_type_id = google.maps.MapTypeId.ROADMAP;
    if (map_type==2) map_type_id = google.maps.MapTypeId.SATELLITE;
    else if (map_type==3) map_type_id = google.maps.MapTypeId.HYBRID;
    
    var myLatlng = new google.maps.LatLng(lat0, lng0);
    var myOptions = {
      zoom: parseInt(zoom),
      zoomControl: (map_zoom=='1') ? true : false,
      center: myLatlng,
      mapTypeId: map_type_id
    }; 
    
    map = new google.maps.Map(document.getElementById("map_canvas"),myOptions);
    /*if (GBrowserIsCompatible())
    {
        map = new GMap2(document.getElementById("map_canvas"));
        map.setCenter(new GLatLng(lat0, lng0), 10);
        map.enableContinuousZoom();
        map.enableDoubleClickZoom();
        map.setZoom(zoom);
        if (map_zoom) map.addControl(new GSmallMapControl());
        if (map_type==2) map.setMapType(G_SATELLITE_MAP);
        else if (map_type==3) map.setMapType(G_HYBRID_MAP);
        else map.setMapType(G_NORMAL_MAP);
        map.addControl(new GMapTypeControl());
        geocoder = new GClientGeocoder();
    }
    // showAddress("Wilkie road, Singapore","Inkiti :\n");
    var mapDiv = document.getElementById("map_canvas");
    var CopyrightDiv = mapDiv.firstChild.nextSibling;
    CopyrightDiv.style.font = "8px Arial";*/
    // add ads into map
    if (arrMapAds.length > 0)
    {
        for (var i = 0; i < arrMapAds.length; i++)
        {
            var adLatLng = new google.maps.LatLng(parseFloat(arrMapAds[i].lat),parseFloat(arrMapAds[i].lng));
            arrMapAds[i].marker = new google.maps.Marker({
                position: adLatLng
            });
            arrMapAds[i].marker.setMap(map);
            var adInfo = "<a href='"+arrMapAds[i].url+"'>"+arrMapAds[i].title+"</a><br />"+arrMapAds[i].price+"<br />";
            arrMapAds[i].infoWindow = new google.maps.InfoWindow({
                content: adInfo
            });
            google.maps.event.addListener(arrMapAds[i].marker, 'click', $.proxy(function() {
                this.infoWindow.open(map,this.marker);
            }, arrMapAds[i]));
        }
    }
}

initializeGoogleMap();
</script>