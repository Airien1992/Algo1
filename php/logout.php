<?php
ini_set('session.save_path', '/var/www/html/project3/Production/resources/');
session_start();
if($_SESSION['username']!=null){
	unset($_SESSION['username']);
	header('Location:login.php');
}
?>