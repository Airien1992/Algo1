<?php

/*
	author Maarten Slembrouck <maarten.slembrouck@gmail.com>
	made by:
	Andriyanova Anzhelina Aleksandrovna
	Grenson Jarno
	Umba Angèle
	Vasiliev Youri
*/

class PQtest extends SplPriorityQueue
{
    public function compare($priority1, $priority2)
    {
        if ($priority1 === $priority2) return 0;
        return $priority1 > $priority2 ? -1 : 1; //
    }
}

function initialize_mysql_connection(){
	global $servername;
	global $username;
	global $password;
	global $dbname;

	// Create connection
	$mysqli = new mysqli($servername, $username, $password, $dbname);

	if ($mysqli->connect_errno) {
		echo "Sorry, this website is experiencing problems.";
		echo "Error: Failed to make a MySQL connection, here is why: \n";
		echo "Errno: " . $mysqli->connect_errno . "\n";
		echo "Error: " . $mysqli->connect_error . "\n";
		exit;
	}
        return $mysqli;
}

function close_mysql_connection(){
	global $mysqli;
	$mysqli->close();
}
function voertuig($type) {
  if($type == "car") {
    return "access_drive";
  }
  else if ($type == "bicycle") {
    return "access_bike";
  }
  else if ($type == "foot") {
    return "access_walk";
  }
  else {
    throw new Exception("  Input Error: invalid type");
  }
}

function getMinMaxLatLon(){
  global $mysqli;
  $sql = "SELECT MIN( lat ) lat_min, MAX( lat ) lat_max, MIN( lon ) lon_min, MAX( lon ) lon_max FROM  `osm_nodes`";
  $retval = $mysqli->query($sql);
  if($retval && $row = $retval->fetch_assoc()){
    return array($row['lat_min'], $row['lat_max'], $row['lon_min'], $row['lon_max']);
  }
  else{
    return null;
  }
}

function checkLonLat($from_lat, $from_lon){
  $latlonbounds = getMinMaxLatLon();
  if($from_lat < $latlonbounds[0] || $from_lat > $latlonbounds[1]){
    throw new Exception("Input Error: from_lat out of bound", 6);
  }
  else if($from_lon < $latlonbounds[2] || $from_lon > $latlonbounds[3]){
    throw new Exception("Input Error: from_lon out of bound", 7);
  }
  
}

function getNodeId($from_lat, $from_lon, $transport){
	global $mysqli;
  // find the closest node_id to ($from_lat, $from_lon) on a way
  $sql = "SELECT node_id from osm_node_neighbours_latlon
	where node_lat + 0.01 > '$from_lat'
	and   node_lat - 0.01 < '$from_lat'
	and   node_lon + 0.01 > '$from_lon'
	and   node_lon - 0.01 < '$from_lon'
	and   ".$transport." = 1
	order by ('$from_lat'-node_lat)*('$from_lat'-node_lat) + ('$from_lon'-node_lon)*('$from_lon'-node_lon) limit 1";
	$retval = $mysqli->query($sql);
  if($retval && $row = $retval->fetch_assoc()){
    return $row['node_id'];
  } else{
	  echo "Unable to execute '$sql'".PHP_EOL;
	  return null;
  }
}

function getShortestPathDijkstra($from_node, $to_node, $transport){
	global $mysqli;
	$acces = 1;
  $sql = "SELECT node_id, neighbour_id, distance FROM osm_node_neighbours_latlon WHERE ".$transport." ORDER BY node_id";// WHERE '$transport' = '1' ";
	if(!$retval = $mysqli->query($sql)){
		throw new Exception("  Unable to execute '$sql'");
	}
	$end = 'true';

	//Huis, Buur, Afstand
	$visited = array();
	$possible = new PQtest();  // B->(H,A)
	$sql_array = array(); // H,B,A
  //vult de waarden van de neighbour tabel in in een acciotative table
  //heeft deze structuur: node => (0=> (buur, dist), 1=> (buur2, dist2), ...), node2 =>(...),...
  while($retval && $row = $retval->fetch_assoc()){
		$sql_array[$row['node_id']][] = array('neighbour_id' => $row['neighbour_id'], 'distance' => $row['distance']);
  }
	//initialisatie
	$current_node = $from_node;
	//echo "begin node: '$current_node'".PHP_EOL;
	$current_dist = 0.0;
	$new = true;
	$end = false;
  $timer = 0;
	while(!$end and $timer < 5000000){
		$timer = $timer + 1;
		//zoek buur van current_node als we een nieuwe current node hebben
		if($new){
			if(array_key_exists($current_node,$sql_array)){
				$buren = $sql_array[$current_node];
				foreach ($buren as $value) {
					$temp = $value;
					$temp['node_id'] = $current_node;
					$buur = $temp['neighbour_id'];
					$temp['distance'] = $current_dist + $value['distance'];
					$possible->INSERT($temp, $temp['distance']);
				}
			}
		}
		//de kortste afstand wordt uit de possible tabel gehaald
		//print_r($possible);
		if($possible->count() == 0) {
			throw new Exception(" Explored all possible paths, none found");
		}
		$kortste = $possible->extract();
		if(array_key_exists($kortste['neighbour_id'], $visited)){
			$new = false;
		} else {
			$visited[$kortste['neighbour_id']] = $kortste['node_id'];
			$current_node = $kortste['neighbour_id'];
			$current_dist = $kortste['distance'];
			$new = true;
			if($current_node == $to_node){
				$end = true;
			}
		}
	}
	$path = array();
	array_push($path, $to_node);
	while(!($current_node == $from_node)){
		$waystone = $visited[$current_node];
		array_push($path, $waystone);
		$current_node = $waystone;
	}
	return array($current_dist, $path);
}

function json_dijkstra($from_lat, $from_lon, $transport){
	global $mysqli;
	

  $access_transport = voertuig($transport); // om een mooie string met uw voertuig te krijgen
  $from_node = getNodeId($from_lat, $from_lon, $access_transport); // complete implementation in func.php
   $sql = "SELECT lat,lon from resaurants
	where lat + 0.02 > '$from_lat'
	and   lat - 0.02 < '$from_lat'
	and   lon + 0.01 > '$from_lon'
	and   lon - 0.01 < '$from_lon'
	order by ('$from_lat'-lat)*('$from_lat'-lat) + ('$from_lon'-lon)*('$from_lon'-lon) limit 1";
	$retval = $mysqli->query($sql);
  if($retval && $row = $retval->fetch_assoc()){
    $to_lat= $row['lat'];
	$to_lon= $row['lon'];
  } else{
	  echo "Unable to find lat '$sql'".PHP_EOL;
	  return null;
  }
  $to_node = getNodeId($to_lat, $to_lon, $access_transport);
  //$to_node = getNodeId($to_lat, $to_lon, $access_transport);
  // To think about: what if there is no path between from_node and to_node?
  // add a piece of code here (after you have a working Dijkstra implementation)
  // which throws an error if no path could be found -> avoid that your algorithm visits all nodes in the database

  list($distance,$path) = getShortestPathDijkstra($from_node, $to_node, $access_transport); // complete implementation in func.php
//echo(PHP_EOL."Let's find the shortest way ...".PHP_EOL);


  $output = array(
      "from_node" => $from_node,
      "to_node" => $to_node,
      "path" => $path,
      "distance" => $distance
  );
  echo "total distance is '$distance'";
  return json_encode($output);
}
?>
