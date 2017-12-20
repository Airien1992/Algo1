<?php
// Include config file
require_once "config.php";
require_once "func.php";

?>
<html lang="en">
<head>
		<title>Registreren</title>
		<link href="css/bootstrap" rel="stylesheet">

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
		 .btn_register{
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
	<h2>Register</h2>
<div class = "container form-signin">

<?php 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Validate username
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter a username.";
    } 
	else{
		$msg = '';
            if (isset($_POST['register']) && !empty($_POST['username'])
               && !empty($_POST['password']) && !empty ($_POST['email']) && !empty($_POST['name'])) {
				global $Pass;
				global $Naam;
				global $Email;
				global $mysqli;
				$User = $_POST['username'];
				$Pass = $_POST['password'];
				$Naam = $_POST['name'];
				$Email = $_POST['email'];
				$mysqli = initialize_mysql_connection();
				// chek if username is used
				$sql="SELECT * FROM members WHERE username='$User' ";
				$result=$mysqli->query($sql);
				$row=$result->fetch_assoc();
				if( $row['username']==$User){
					echo"this username is taken";
			   }
			   else{
				   if($_POST['password']==$_POST['confirm']){
					   $sql="INSERT INTO members (naam,username,password,email) VALUES ('$Naam','$User','$Pass','$Email')";
					   $result=$mysqli->query($sql);
					   header("location: login.php");
				   }
				   else {
					   echo "Password and confirm password aren't matching!";
				   }
			   }
        }
    }
	close_mysql_connection();
}
?>
 
</div>

	<div class="container">	
	
		<form class = "form-signin" role = "form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
		
						
			<div class="form-group">
				<label>Your name</label>							
				<input type="text" class="form-control" name="name" id="name"  placeholder="Enter your name"/>						
			</div>

			<div class="form-group">
				<label>Your email</label>							
				<input type="text" class="form-control" name="email" id="email"  placeholder="Enter your email"/>
			</div>

			<div class="form-group">
				<label>Username</label>
				<input type="text" class="form-control" name="username" id="username"  placeholder="Enter your username"/>
			</div>
							

			<div class="form-group">
				<label>Password</label>
				<input type="password" class="form-control" name="password" id="password"  placeholder="Enter your password"/>
			</div>

			<div class="form-group">
				<label>Confirm password</label>
				<input type="password" class="form-control" name="confirm" id="confirm"  placeholder="Confirm your password"/>
			</div>

			<div class="form-group ">
				<button class="btn_register" type="submit" name="register">Register</button>
				<button class = "btn" type = "submit" ><a href="login.php">Go back to login page</a></button>
			</div>
						
		</form>
	</div>
</body>
</html>