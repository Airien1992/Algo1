<?php
/*
author Maarten Slembrouck <maarten.slembrouck@gmail.com>
made by:
Andriyanova Anzhelina Aleksandrovna
Umba Angèle
*/
function initialize_mysql_connection(){
  global $servername;
  global $username;
  global $password;
  global $dbname;
  global $User;
  global $_SESSION;
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
?>
