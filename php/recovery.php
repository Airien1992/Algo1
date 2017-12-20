<?php
   ob_start();
   session_start();
   require_once "config.php";
   require_once "func.php";
?>

<?
   // error_reporting(E_ALL);
   // ini_set("display_errors", 1);
?>

<html lang = "en">

   <head>
      <title>Recover your credentials</title>
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
		 
		 form{
			 border: 3px solid #f1f1f1;
		 }

         .form-signin {
            max-width: 330px;
            padding: 15px;
            margin: 0 auto;
            color: #017572;
         }

         .form-signin .form-signin-heading,
         .form-signin .checkbox {
            margin-bottom: 10px;
         }
		 
		 .form-signin .form-group{
			 font-size: 25px;
			 align: center;
			 color: white;
		 }

         .form-signin .checkbox {
            font-weight: normal;
         }

         .form-signin .form-control {
            width: 100%;
			padding: 12px 20px;
			margin: 8px 0;
			display: inline-block;
			border: 1px solid #ccc;
			box-sizing: border-box;
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
            margin-bottom: 10px;
            border-top-left-radius: 0;
            border-top-right-radius: 0;
            border-color:#017572;
         }

         h2{
            font-size: 50px;
            text-align: center;
            color: white;
         }
		 
		 h4{
			 font-size: 30px;
			 color: white;
		 }
		 .btn_home{
			 background-color: #5F9EA0;
			 color: white;
			 padding: 14px 20px;
			 margin: 8px 0;
			 border: none;
			 cursor: pointer;
			 width: 100%;
		 }
		 .btn{
			 background-color: white;
			 color: #5F9EA0;
			 padding: 14px 5px;
			 margin: 8px 0;
			 border: none;
			 cursor: pointer;
			 width: 100%;
		 }
		 
		 
		 
      </style>

   </head>

   <body>

      <h2>Enter validation data</h2>
      <div class = "container form-signin">

         <?php
            $msg = '';
            if (isset($_POST['recover']) && !empty($_POST['username'])
               && !empty($_POST['email']) && !empty($_POST['naam'])) {

				global $mysqli;
				$User = $_POST['username'];
				$Email = $_POST['email'];
				$Name = $_POST['naam'];
				$mysqli = initialize_mysql_connection();
				$sql="SELECT * FROM members WHERE username='$User' and email='$Email' and naam='$Name'";
				$result=$mysqli->query($sql);
				$row=$result->fetch_assoc();
				if( $row['username']==$User && $row['email']==$Email){
					echo "Uw naam is:";echo $row['naam']; echo "\n<br />\n<br />";
					echo "Uw gebruikersnaam is: ";echo $row['username']; echo "\n<br />\n<br />";
					echo "Uw passwoord is: ";echo $row['password']; echo "\n<br />\n<br />";
					echo "Uw email is: ";echo $row['email']; echo "\n<br />\n<br />";
                  close_mysql_connection();
        }
                else {
                  $msg = 'Wrong username or email or name';
                  close_mysql_connection();
               }
            }
         ?>
      </div> <!-- /container -->

      <div class = "container">

         <form class = "form-signin" role = "form" action = "<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method = "post">
            <h4 class = "form-signin-heading"><?php echo $msg; ?></h4>
            <div class="form-group">
				<label >Username:</label>
				<input type = "text" class = "form-control" name = "username" placeholder = "username" required autofocus></br>
			</div>
			<div class="form-group">
				<label>Email:</label>
				<input type = "text" class = "form-control" name = "email" placeholder = "email" required>
			</div>
			<div class="form-group">
				<label>Naam:</label>
				<input type = "text" class = "form-control" name = "naam" placeholder = "naam" required>
			</div>
			<div class="form-group">
				<button class = "btn_home" type = "submit" name = "recover">Recover</button>
				<button class = "btn" type = "submit" ><a href="login.php">Go back to Login page</a></button>
			</div>
			   
         </form>


      </div>

   </body>
</html>
