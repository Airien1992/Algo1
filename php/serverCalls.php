<?php
$result = [];
$to_lat = [];
$to_lon = [];
$to_node = [];
$distansie = [];
$paden = [];
$allePaden = [];
class PQtest extends SplPriorityQueue
{
  public function compare($priority1, $priority2)
  {
    if ($priority1 === $priority2) return 0;
    return $priority1 > $priority2 ? -1 : 1; //
  }
}
function creat_players_table(){
  global $mysqli;
  $sql = "CREATE TABLE IF NOT EXISTS Spelers (user VARCHAR(24) PRIMARY KEY, status VARCHAR(20), start_point BIGINT);";
  $mysqli->query($sql);
}
function new_player($user){
  global $mysqli;
  $sql = "INSERT INTO Spelers (user, status) VALUES ('$user', 'startup');";
  $mysqli->query($sql);
}
function check_player($user){
  global $mysqli;
  $sql = "SELECT count(*) as res  FROM Spelers WHERE user = '$user';";
  if($retval = $mysqli->query($sql)){
    return $retval->fetch_assoc()['res'];
  }else {
    return 0;
  }
}
function getNodeId($from_lat, $from_lon){
  global $mysqli;
  // find the closest node_id to ($from_lat, $from_lon) on a way
  $sql = "SELECT node_id, node_lat, node_lon from osm_node_neighbours_latlon
  where node_lat + 0.01 > '$from_lat'
  and   node_lat - 0.01 < '$from_lat'
  and   node_lon + 0.01 > '$from_lon'
  and   node_lon - 0.01 < '$from_lon'
  order by ('$from_lat'-node_lat)*('$from_lat'-node_lat) + ('$from_lon'-node_lon)*('$from_lon'-node_lon) limit 1";
  $retval = $mysqli->query($sql);
  if($retval && $row = $retval->fetch_assoc()){
    return $row['node_id'];
  } else{
    echo "Unable to execute '$sql'".PHP_EOL;
    return null;
  }
}
function create_candidate_table(){
  global $mysqli;
  $sql = "CREATE TABLE IF NOT EXISTS GameState ( ID Integer PRIMARY KEY AUTO_INCREMENT, user VARCHAR(24), node_id BIGINT, distance DOUBLE);";
  $mysqli->query($sql);
}

function add_candidate_array($user, $n, $d){
  global $mysqli;

    $node_id = $n;
    $distance = $d;
    $sql = "INSERT INTO GameState (user, node_id, distance) VALUES ('$user', '$node_id', '$distance');";
    $mysqli->query($sql);
}

function remove_candidate_array($user){
  global $mysqli;
  $sql = "DELETE FROM GameState WHERE ID IN (  SELECT temp.ID FROM (    SELECT ID FROM GameState WHERE user ='$user'  ) AS temp) LIMIT 200;";
  $mysqli->query($sql);
}

function change_player_status($user, $status){
  global $mysqli;
  $sql = "UPDATE Spelers SET status='$status' WHERE user='$user';";
  $mysqli->query($sql);
}

function get_players(){
  global $mysqli;
  $table = array();
  $sql = "SELECT *  FROM Spelers";
  $retval = $mysqli->query($sql);
  while($row = $retval->fetch_assoc()){
      $table[$row['user']] = array('status' => $row['status'], 'start_point' => $row['start_point']);
      print_r($table);
  }
  return $table;
}

function get_candidate(){
  global $mysqli;
  $table = array();
  $sql = "SELECT *  FROM GameState";
  if($retval = $mysqli->query($sql)){
    while($row = $retval->fetch_assoc()){
      $table[$row['user']][] = array('node_id' => $row['node_id'], 'distance' => $row['distance']);
    }
  }
  return $table;
}
/*function getShortestPathDijkstra($from_node){
  global $mysqli;
  $lats=array();
  $lons=array();
  $sql = "SELECT lat,lon
  FROM `resaurants`
  WHERE 1";
  if($retval = $mysqli->query($sql)){
    while($row = $retval->fetch_assoc()){
      array_push($lats,$row['lat']);
      array_push($lons,$row['lon']);
    }
  }
  $output_limit = 30;
  $output = array();
  $output_len = 0;
  $end = 'true';

  //Huis, Buur, Afstand
  $visited = array();
  $possible = new PQtest();  // B->(H,A)
  $sql_array = array(); // H,B,A

  $sql = "SELECT node_id, neighbour_id, distance,node_lat,node_lon FROM osm_node_neighbours_latlon ORDER BY node_id;";
  if(!$retval = $mysqli->query($sql)){
    throw new Exception("  Unable to execute '$sql'");
  }else{
    while($row = $retval->fetch_assoc()){
      $sql_array[$row['node_id']][] = array('neighbour_id' => $row['neighbour_id'], 'distance' => $row['distance'], 'node_lat' => $row['node_lat'], 'node_lon' => $row['node_lon']);
    }
  }
  //vult de waarden van de neighbour tabel in in een acciotative table
  //heeft deze structuur: node => (0=> (buur, dist), 1=> (buur2, dist2), ...), node2 =>(...),...
  //initialisatie
  $current_node = $from_node;
  //echo $current_node;
  $current_dist = 0.0;
  $new = true;
  $end = false;
  $timer = 0;
  while(!$end and $timer < 50000 and $output_len < $output_limit){
    $timer = $timer + 1;
    //zoek buur van current_node als we een nieuwe current node hebben
    if($new){
      if(array_key_exists($current_node,$sql_array)){
        $buren = $sql_array[$current_node];
        foreach ($buren as $value) {
          $temp = $value;
          $temp['node_id'] = $current_node;
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
      //echo $current_node;
      $new = true;
      if(in_array($kortste['node_lat'],$lats)&&in_array($kortste['node_lon'],$lons)){
        $output[] = array('node_id' => $current_node, 'distance' => $current_dist);
        $output_len = $output_len + 1;
      }
    }
  }
  return $output;
}*/
function getShortestPathDijkstra($from_node, $to_node){
	global $mysqli;
	$acces = 1;
  $sql = "SELECT node_id, neighbour_id, distance FROM osm_node_neighbours_latlon ORDER BY node_id";// WHERE '$transport' = '1' ";
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
			if(array_key_exists((string)$current_node,$sql_array)){
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
function json_dijkstra($from_lat, $from_lon){
	global $mysqli;
  $from_node=getNodeId($from_lat, $from_lon);
   $sql = "SELECT lat,lon from resaurants
	where lat + 0.01 > '$from_lat'
	and   lat - 0.01 < '$from_lat'
	and   lon + 0.01 > '$from_lon'
	and   lon - 0.01 < '$from_lon'
	order by ('$from_lat'-lat)*('$from_lat'-lat) + ('$from_lon'-lon)*('$from_lon'-lon)";
	$retval = $mysqli->query($sql);
  $i=0;
  while($row = $retval->fetch_assoc()){

    $to_lat[$i]=$row['lat'];
    $to_lon[$i]=$row['lon'];
    $to_node[$i]=getNodeId($row['lat'],$row['lon']);
    $i++;
  /*  array_push($to_lat,$row['lat']);
    array_push($to_lon,$row['lon']);
    array_push($to_node,getNodeId($row['lat'],$row['lon']));*/

  }

  for($i=0;$i<count($to_node);$i++){
  list($distance,$path) = getShortestPathDijkstra($from_node, $to_node[$i]); // complete implementation in func.php
  $distansie[$i]=$distance;
  $paden[$i]=$path;
  $output = array(
      "node_id" => $to_node[$i],
      "distance" => $distance
  );
  $allePaden[$i]=$output;

//echo(PHP_EOL."Let's find the shortest way ...".PHP_EOL);
}
  return json_encode($allePaden);
}
require_once "config.php";
require_once "func.php";

$mysqli = initialize_mysql_connection();
creat_players_table();
create_candidate_table();
$players = get_players();
if(array_key_exists($_GET['user'], $players)){
	$result['extra'] = "player already exists";
	change_player_status($_GET['user'], "startup");
	remove_candidate_array($_GET['user']);
	$players = get_players();
}else {
	new_player($_GET['user']);
	$result['extra'] = "player added";
}
$start_node = getNodeId($_GET['lat'], $_GET['lon']);
$cand = json_decode(json_dijkstra($_GET['lat'], $_GET['lon']),true);
for($i=0;$i<count($cand);$i++){
  if(isset($cand[$i])){
  add_candidate_array($_GET['user'], $cand[$i]['node_id'],$cand[$i]['distance']);}

}


change_player_status($_GET['user'], "ready");

//$result['players'] = $players;

$pionen=get_players();
print_r($pionen);
$ready_players = 0;
foreach($pionen as $player) {
	if($player['status'] == "ready") {
		$ready_players += 1;
	}

}
echo $ready_players;
if($ready_players >= 4 ){
	$candidate = get_candidate();
  print_r($candidate);
	$mapping = array();
	$index = 0;
	$furthest_dist = 0;
	$furthest_player = 0;
	$nodes = array();
	foreach ($candidate as $key => $value) {
		$mapping[$index] = $key;
		$nodes[$index] = $candidate[$key][0]['node_id'];
    //print_r($nodes);
		$index += 1;
		$dist = $candidate[$key][0]['distance'];
		if($dist > $furthest_dist) {
			$furthest_dist = $dist;
			$furthest_player = $index-1;
		}
	}
	print_r('hello?');
	$equal = 0;
	while($equal == 0){
		$equal = 1;
		for($i = 0; $i < 4; $i++){
			$dist = $candidate[$mapping[$i]][0];
			if($i != $furthest_player) {
				$nodes[$i] += 1;
				while(($dist['distance']+0.5 < $furthest_dist or in_array($candidate[$mapping[$i]][0]['node_id'], $nodes))) {
					$try = array_shift($candidate[$mapping[$i]]);
          //print_r($mapping);
					$dist = $candidate[$mapping[$i]][0];
          //print_r($dist);
					$equal = 0;
					$nodes[$i] = $candidate[$mapping[$i]][0]['node_id']+1;
          //print_r($nodes);
				}
				$nodes[$i] -= 1;
				if($dist['distance'] > $furthest_dist) {
					$furthest_dist = $dist['distance'];
					$furthest_player = $i;
          //echo $furthest_dist;
				}
			}
		}
	}
print_r($nodes);


}







/*
$player = get_player($_GET['user']);
$user = $player['user'];

$result['user'] = $user;
$result['lat'] = $_GET['lat'];
$result['lon'] = $_GET['lon'];
*/
echo json_encode($result);
close_mysql_connection();
?>
