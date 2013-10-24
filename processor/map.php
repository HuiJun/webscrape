<html>
<head>
<meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=true&amp;key=ABQIAAAA6uwJ54jNwNrtY9uvzuqmfhToMRMtC4-ayUI-8O7TEOm8Ew5IyBRzWY2msD611N2a0q7k4Gw0Hq5N_Q"></script>
<script type="text/javascript">
  var geocoder;
  var map;
  function initialize() {
    geocoder = new google.maps.Geocoder();
    var latlng = new google.maps.LatLng(0, 0);
    var myOptions = {
      zoom: 18,
      center: latlng,
      mapTypeId: google.maps.MapTypeId.HYBRID
    }
    map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
  }

  function codeAddress() {
    var address = document.getElementById("address").value;
    if (geocoder) {
      geocoder.geocode( { 'address': address}, function(results, status) {
        if (status == google.maps.GeocoderStatus.OK) {
          map.setCenter(results[0].geometry.location);
          var marker = new google.maps.Marker({
              map: map, 
              position: results[0].geometry.location
          });
        } else {
          alert("Geocode was not successful for the following reason: " + status);
        }
      });
    }
  }
</script>
<style>
body {
  font-family:Tahoma,Helvetica,sans-serif;
  text-align:center;
  background-color:#fffdd7;
  font-size:24px;
  font-weight:bolder;
  color:#be1a20;
}
.comps {
  margin:30px;
  padding: 15px;
  border-style:solid;
  border-width: 1px;
  border-color: #000;
  background-color:#eaeaea;
  text-align:left;
}
</style>
</head>
<?php
if($_GET[address]) {
?>
<body onload="initialize(),codeAddress()">
<div style="margin:0 auto;">
<?php
  if($_GET[comps]) {
    echo '<a href="javascript:history.go(-1)" style="float:right;">Back to Comps</a>';
    echo '<div style="clear:both;height:10px;"></div>';
  }
?>
 <div id="map_canvas" style="width: 100%; height:90%;"></div>
</div>
<input id="address" type="hidden" value="<?php echo $_GET[address]; ?>">
</body>
</html>
<?php
} else {
?>
    <body>
      <div style="width:600px;margin:0 auto;">
        <p style="text-align:left;">
          Please input an address on the left.
        </p>
      </div>
    </body>
</html>
<?php
}
?>
