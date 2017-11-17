<?php
require_once 'config.php';
require_once 'func.php';
session_start();
if($_SESSION['valid'] == true){   $gebruiker = $_SESSION['username']; }
else {   $gebruiker = "ufo";  }
$mysqli = initialize_mysql_connection();

try{
	if(isset($_GET['from_lat'])) $from_lat = $_GET['from_lat']; else throw new Exception("Input Error: from_lat not set", 1);
	if(isset($_GET['from_lon'])) $from_lon = $_GET['from_lon']; else throw new Exception("Input Error: from_lon not set", 2);
	if(isset($_GET['transport'])) $transport = $_GET['transport']; else throw new Exception("Input Error: transport not set", 3);
	echo "HI";
	$to_node="'1634943259'";
	echo "good";
	$data = json_dijkstra($from_lat, $from_lon ,$to_node, $transport);
	echo "LO";
}
catch(Exception $e){
	echo "<p>".$e->getMessage()."</p>";
	echo "nooooo";
	exit();
}
$dijkstra = json_decode($data);

$pathlength = count($dijkstra->path);
echo $pathlength;
$lats = array();
$lons = array();

function getLonLat($node_id){
  global $mysqli;
  $sql = "SELECT lat, lon
          FROM `osm_nodes`
          WHERE `id` = '$node_id'";
  $retval = $mysqli->query($sql);
  $lonlat = $retval->fetch_assoc();
  return $lonlat;
}

for ($i = 0; $i < $pathlength; $i++) {
    $lonlat = getLonLat($dijkstra->path[$i]);
    array_push($lats, $lonlat['lat']);
    array_push($lons, $lonlat['lon']);
}

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
    
    

<?php
$middle_lat = ($lats[0] + $lats[count($lats)-1])/2;
$middle_lon = ($lons[0] + $lons[count($lons)-1])/2;
?>

    <script>

function initialize() {
var myPos = new google.maps.LatLng(51.04972991,3.7229769);
  
  var myMarker = new google.maps.Marker({
        position: myPos,
        icon: "../resources/MarkerWithSunglasses.png"
      });;
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
      setInterval(function(){
        position=navigator.geolocation.getCurrentPosition(function(location) {
          lat = location.coords.latitude;
          lon = location.coords.longitude;
          myPos = new google.maps.LatLng(lat, lon);
          myMarker.setPosition(myPos);
      },function() {
		  alert("Code: " + error.message);
		}, mapOptions);
	  myMarker.setPosition(myPos);
    });

  

  var map = new google.maps.Map(document.getElementById('map-canvas'),
      mapOptions);


  var PathCoordinates = [
  ];



  <?php
    for ($i = 0; $i < $pathlength; $i++) {


      echo "PathCoordinates.push(new google.maps.LatLng(";
      echo $lats[$i];
      echo ",";
      echo $lons[$i];
      echo "));";
    }
  ?>


  var ShortestPath = new google.maps.Polyline({
    path: PathCoordinates,
    geodesic: true,
    strokeColor: '#FF0000',
    strokeOpacity: 1.0,
    strokeWeight: 2
  });

  ShortestPath.setMap(map);
  myMarker.setMap(map);
}

google.maps.event.addDomListener(window, 'load', initialize);

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
				<li><a href="#">Lat: <?php echo $latit = $_GET['Lati']; ?></a></li>
				<li><a href="#">Lat: <?php echo $longit = $_GET['Longi']; ?></a></li>
                <li><a href="#">My score:  2504</a></li>
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
                <li><a href="#">forfeit game</a></li>
                <li><a href="#">report misconduct</a></li>
              </ul>
            </li>
          </ul>
        </div><!-- /.navbar-collapse -->
      </div><!-- /.container-fluid -->
    </nav>
    <div id="map-canvas"></div>
  </body>
</html>
