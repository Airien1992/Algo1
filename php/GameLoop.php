<?php


$points = array();
$points[260023817] = array('lat' => 51.0587264,'lon' =>3.7283658);
$points[2396044690] = array('lat' => 51.0517072,'lon' => 3.7119787);
$points[3192105304] = array('lat' => 51.0474806,'lon' => 3.7245179);
$points[4802246599] = array('lat' => 51.0553086,'lon' => 3.7393644);

$sequentie = array();
$sequentie[260023817] = 2396044690;
$sequentie[2396044690] = 3192105304;
$sequentie[3192105304] = 4802246599;
$sequentie[4802246599] = 260023817;


if($_POST['user_dest']==0){
	$current_destination = 260023817;
}
else {
	$current_destination = $_POST['user_dest'];
}





$lat_u = $_POST['user_lat'];
$lon_u = $_POST['user_lon'];
$lat_d = $points[$current_destination]['lat'];
$lon_d = $points[$current_destination]['lon'];

$lonGem = deg2rad($lon_d) - deg2rad($lon_u);
$a=pow(cos(deg2rad($lat_d)) * sin($lonGem), 2) +
	pow(cos(deg2rad($lat_u)) * sin(deg2rad($lat_d)) - sin(deg2rad($lat_u)) * cos(deg2rad($lat_d)) * cos($lonGem), 2);
$b = sin(deg2rad($lat_u)) * sin(deg2rad($lat_d)) + cos(deg2rad($lat_u)) * cos(deg2rad($lat_d)) * cos($lonGem);
$hoek = atan2(sqrt($a), $b);
$distance =  $hoek*6371000;

if($distance <100){
	$current_destination = $sequentie[$current_destination];
}
$result = array();
$result['destination_node'] = $current_destination;
$result['destination_lat'] = $lat_d;
$result['destination_lon'] = $lon_d;
$result['user_destination'] = $current_destination;
$result['user_distance'] = $distance;

//$result = array('destination_node' => $current_destination, 'destination_lat' =>$lat_d, 'destination_lon' =>$lon_d);
echo json_encode($result);

/*
if(isset($_POST['user'])){
  //Do something
  echo "user = ", $_POST['user'], " lat = ", $_POST['user_lat'], " lon = ", $_POST['user_lon'], " distance = ", $distance;
}
*/

 ?>
