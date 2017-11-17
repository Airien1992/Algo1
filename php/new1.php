<?php
require_once 'config.php';
global $servername;
global $username;
global $password;
global $dbname;
$mysqli = new mysqli($servername, $username, $password, $dbname);
try{
	if(isset($_GET['from_lat'])) $from_lat = $_GET['from_lat']; else throw new Exception("Input Error: from_lat not set", 1);
	if(isset($_GET['from_lon'])) $from_lon = $_GET['from_lon']; else throw new Exception("Input Error: from_lon not set", 2);
	if(isset($_GET['transport'])) $transport = $_GET['transport']; else throw new Exception("Input Error: transport not set", 3);

	

	//$data = json_dijkstra($_GET['from_lat'], $_GET['from_lon'], $_GET['transport']);
}
catch(Exception $e){
	echo "<p>".$e->getMessage()."</p>";
	exit();
}
global $mysqli;
  // find the closest node_id to ($from_lat, $from_lon) on a way
$sql = "SELECT node_id from osm_node_neighbours_latlon where node_lat + 0.01 > '$from_lat' and   node_lat - 0.01 < '$from_lat' and   node_lon + 0.01 > '$from_lon' and   node_lon - 0.01 < '$from_lon' and   ".$transport." = 1;";
$retval = $mysqli->query($sql);
if($retval && $row = $retval->fetch_assoc()){
	$from_node = $row['node_id'];
  } else{
		echo "Unable to execute '$sql'".PHP_EOL;
	  
	}

echo "'$from_node'".PHP_EOL;
?>