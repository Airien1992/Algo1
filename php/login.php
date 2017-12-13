<?php
   ob_start();
   require_once "config.php";
   require_once "func.php";
   $_SESSION['valid']=false;
   
global $names;
$names=[];
?>

<?
   // error_reporting(E_ALL);
   // ini_set("display_errors", 1);
?>

<html lang = "en">

   <head>
      <title>Login project</title>
      <link href = "css/bootstrap.min.css" rel = "stylesheet">

      <style>
         body {
            padding-top: 40px;
            padding-bottom: 40px;
            background-image: url("../resources/hotel.jpg");
			background-position: center;
			background-repeat: no-repeat;
			background-size: cover;
         }

         .form-signin {
            max-width: 330px;
            padding: 30px;
            margin: 0 auto;
            color: #017572;
         }

         .form-signin .form-signin-heading,
         .form-signin .checkbox {
            margin-bottom: 10px;
         }

         .form-signin .checkbox {
            font-weight: normal;
         }

         .form-signin .form-control {
            position: relative;
            height: auto;
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            box-sizing: border-box;
            padding: 10px;
            font-size: 16px;
         }

         .form-signin .form-control:focus {
            z-index: 2;
         }

         .form-signin input[type="email"] {
            margin-bottom: -1px;
            border-bottom-right-radius: 0;
            border-bottom-left-radius: 0;
            border-color:#017572;
         }

         .form-signin input[type="password"] {
            margin-bottom: 100px;
            border-top-left-radius: 0;
            border-top-right-radius: 0;
            border-color:#017572;
         }

         h2{
			font-size: 50px;
            text-align: center;
            color: #5F9EA0;
         }
      </style>

   </head>

   <body>

      <h2>The pursuit of happines</h2>
      <div class = "container form-signin">
		 <?php
            $msg = '';
			
            if (isset($_POST['login']) && !empty($_POST['username'])
               && !empty($_POST['password'])) {

				global $Pass;
				global $mysqli;
				$names=array();
				$User = $_POST['username'];
				$Pass = $_POST['password'];
				$mysqli = initialize_mysql_connection();
				$sql="SELECT * FROM members WHERE username='$User' and password='$Pass'";
				$result=$mysqli->query($sql);
				$row=$result->fetch_assoc();
				if( $row['username']==$User){
				  ini_set('session.save_path', '/var/www/html/project3/Production/resources/');
				  session_id($User);
				  array_push($names,$User);
				  session_start();
				  $_SESSION['username']=$User;
				  $_SESSION['Id'] = session_id();
                  $_SESSION['valid'] = true;
                  $_SESSION['timeout'] = time();
				  
				  foreach($_SESSION AS $key => $value) {
					  
				  echo "$key -> $value";
}
                  
				  
				  close_mysql_connection();
				  

		echo $names[0];
		echo 'You have entered valid use name and password';
        }
                else {
                  $msg = 'Wrong username or password';
                  close_mysql_connection();
               }
            }
         ?>
		 <script type="text/javascript">
			if(<?php echo $_SESSION['valid']; ?>){
				alert("ju");
				var position=navigator.geolocation.getCurrentPosition(function(location) {
				var lat = location.coords.latitude;
				var lon = location.coords.longitude; 
				window.location.href = "map.php?from_lat="+lat +"&from_lon="+lon+"&transport=foot";	
						
				});
			}
			</script>
      </div> <!-- /container -->

      <div class = "container">

         <form class = "form-signin" role = "form" action = "<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method = "post">
            <h4 class = "form-signin-heading"><?php echo $msg; ?></h4>
            <div class="form-group">
				<label >Username:</label>
				<input type = "text" class = "form-control" name = "username" placeholder = "username" required autofocus></br>
			</div>
			<div class="form-group">
				<label>Password:</label>
				<input type = "password" class = "form-control" name = "password" placeholder = "password" required>
			</div>
			<div class="form-group">
				<button class = "btn btn-lg btn-primary btn-block" type = "submit" name = "login" Id="log">Login</button>
				<a href="register.php">Registreren</a>
				<a href="recovery.php">Recovery</a>
				<a href="logout.php">logout</a>
			</div>
			   
         </form>


      </div>

   </body>
</html>
