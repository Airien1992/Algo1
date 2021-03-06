<?php
session_start();
if($_SESSION['valid'] == true){   $gebruiker = $_SESSION['username']; }
else {   $gebruiker = "ufo";  }
?>


<!DOCTYPE html>
<html>
  <head>
  <title>Restos</title>
  <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
  <!-- Google Maps CSS -->
  <style>  html, body, #map-canvas {height: 100%; width: 100%}  </style>

  <!-- Latest compiled and minified CSS -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <!-- jQuery library -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  <!-- Latest compiled JavaScript -->
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script>

  <!-- ##################################################################### -->
  <!-- My CSS -->
  <link rel="stylesheet" type="text/css" href="../css/styles.css"/>
  <!-- My javascript -->
  <script src="../map.js"></script>

    <script>
	var myPos ;
	var Ulat;
	var Ulon;

function initialize() {

  var myMarker = new google.maps.Marker({
        position: myPos,
        icon: "../resources/MarkerWithSunglasses.png"
      });
	  var mapOptions = {
    zoom: 15,

    center: new google.maps.LatLng(51.04972991,3.7229769),
    mapTypeId: google.maps.MapTypeId.TERRAIN
  };

      // location spoofer: https://addons.mozilla.org/en-US/firefox/addon/location-guard/
      // bestaat denk ik ook voor chroom
      //insteling:
      // 1) options -> Default level op "Use fixed location zetten"
      // 2) Fixed Location -> zorg dat onderaan "Fixed location disables geolocation" aangevinkt staat
      // 3) plaats in de addon de marker waar je maar wilt
    /*  setInterval(function(){
		position=navigator.geolocation.getCurrentPosition(function(location) {
		  lat = location.coords.latitude;
		  lon = location.coords.longitude;
		  myPos = new google.maps.LatLng(lat, lon);
		  myMarker.setPosition(myPos);

	  },function() {
		  alert("Code: " + error.message);
		}, mapOptions);
	  myMarker.setPosition(myPos);
	});*/



  var map = new google.maps.Map(document.getElementById('map-canvas'),
      mapOptions);

  map.addListener('rightclick', function(event){
      myPos= new google.maps.LatLng(event.latLng.lat(),event.latLng.lng()),
			Ulat=event.latLng.lat(),
			Ulon=event.latLng.lng(),
			mySet(),
      myMarker.setPosition(myPos);
            }
        );
  myMarker.setMap(map);

}
google.maps.event.addDomListener(window, 'load', initialize);

function mySet(){
	window.alert("startup.php?user=<?php echo $gebruiker ?>&lat=" + Ulat + "&lon="+Ulon);
          $.ajax({
            type: "GET",
            url:"serverCalls.php?user=<?php echo $gebruiker ?>&lat=" + Ulat + "&lon="+Ulon,
            async: true,
            cache: false,
            success:  function(data){
              console.log(data);
              //var  response = jQuery.parseJSON(data);
              //game_update();
              //check_proximity();
            },
            error: function(XMLHttpRequest, textStatus, errorThrown){
              alert("error: " + textStatus + " (" + errorThrown + ")");
            }
          });




        }

    </script>


  </head>
  <body>
  <nav class="navbar navbar-default">
      <div class="container-fluid">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <img src="../resources/HotelQuest.png" width="100" height="50">
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
          <ul class="nav navbar-nav">
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"> Game <span class="caret"></span></a>
              <ul class="dropdown-menu">
                <li><a href="#">User: <?php echo $gebruiker ?></a></li>
                <li><a href="#"></a></li>
                <li><a href="#">Waypoints:  23/45</a></li>
                <li role="separator" class="divider"></li>
                <li><a href="#">Rules</a></li>
                <li><a href="#">Legal notice</a></li>
              </ul>
            </li>
            <li>
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Game summary<span class="caret"></span></a>
              <ul class="dropdown-menu">
                <li>
                  <table class="GameSummary">
                    <thead >
                      <tr>
                        <th style="min-width:120px;">Player</th> <th style="min-width:70px;">Score</th> <th style="min-width:50px;">Marks</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td>Master Yi</td>
                        <td>5432</td>
                        <td>44</td>
                      </tr>
                      <tr>
                        <td>Singed</td>
                        <td>4732</td>
                        <td>42</td>
                      </tr>
                      <tr>
                        <td>Sona</td>
                        <td>3214</td>
                        <td>38</td>
                      </tr>
                    </tbody>
                  </table>
                </li>
              </ul>
            </li>
            <li>
              <li><a href="#">Time left: 15m 44s</a></li>
            </li>
          </ul>
          <ul class="nav navbar-nav navbar-right">
            <li><a href="#">Link</a></li>
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Dropdown <span class="caret"></span></a>
              <ul class="dropdown-menu">
                <li><a href="logout.php">forfeit game</a></li>
                <li><a href="#">report misconduct</a></li>
				<li><a onclick="UpdateMap()">update</a></li>
              </ul>
            </li>
          </ul>
        </div><!-- /.navbar-collapse -->
      </div><!-- /.container-fluid -->
    </nav>
    <div id="map-canvas"></div>
  </body>
</html>
