<?php
require_once "config.php";
require_once "func.php";
$mysqli = initialize_mysql_connection();

$result['timestamp'] = round(microtime(true) * 1000);
$result['data'] = "success";
usleep(5000000);

echo json_encode($result);

close_mysql_connection();
?>
