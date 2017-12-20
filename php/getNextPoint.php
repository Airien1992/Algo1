<?php

function get_next_target($node_src){
	  global $mysqli;
	$sql = "SELECT node_dest, lat, lon, seed  FROM  seq WHERE node_src='$node_src';";
	if($retval = $mysqli->query($sql)){
		if($row = $retval->fetch_assoc()){
			return $row;
		} else{echo "invalid target";return null;}
	}	else{echo "error get_next_target ", "</br>";return null;}
}

require_once "config.php";
require_once "func.php";
$mysqli = initialize_mysql_connection();
$result = get_next_target($_GET['node']);
echo json_encode($result);
close_mysql_connection();
 ?>
