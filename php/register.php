<?php
// Include config file
require_once "config.php";
require_once "func.php";

 
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
 
<!DOCTYPE html>
<html lang="en">
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1">


	<!-- Website CSS style -->
	<link href="css/bootstrap" rel="stylesheet">

	<!-- Website Font style -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.1/css/font-awesome.min.css">
	<link rel="stylesheet" href="../css/style.css">
		
	<!-- Google Fonts -->
	<link href='https://fonts.googleapis.com/css?family=Passion+One' rel='stylesheet' type='text/css'>
	<link href='https://fonts.googleapis.com/css?family=Oxygen' rel='stylesheet' type='text/css'>
    <title>Sign Up</title>
	<!-- 
    <style type="text/css">
        body{ font: 14px sans-serif; }
        .wrapper{ width: 350px; padding: 20px; }
    </style> -->
</head>
<body>
	<div class="container">
			<div class="row main">
				<div class="main-login main-center">
				<h5>Sign up once and watch any of our free demos.</h5>
					<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
						
						<div class="form-group">
							<label for="name" class="cols-sm-2 control-label">Your Name</label>
							<div class="cols-sm-10">
								<div class="input-group">
									<span class="input-group-addon"><i class="fa fa-user fa" aria-hidden="true"></i></span>
									<input type="text" class="form-control" name="name" id="name"  placeholder="Enter your Name"/>
								</div>
							</div>
						</div>

						<div class="form-group">
							<label for="email" class="cols-sm-2 control-label">Your Email</label>
							<div class="cols-sm-10">
								<div class="input-group">
									<span class="input-group-addon"><i class="fa fa-envelope fa" aria-hidden="true"></i></span>
									<input type="text" class="form-control" name="email" id="email"  placeholder="Enter your Email"/>
								</div>
							</div>
						</div>

						<div class="form-group">
							<label for="username" class="cols-sm-2 control-label">Username</label>
							<div class="cols-sm-10">
								<div class="input-group">
									<span class="input-group-addon"><i class="fa fa-users fa" aria-hidden="true"></i></span>
									<input type="text" class="form-control" name="username" id="username"  placeholder="Enter your Username"/>
								</div>
							</div>
						</div>

						<div class="form-group">
							<label for="password" class="cols-sm-2 control-label">Password</label>
							<div class="cols-sm-10">
								<div class="input-group">
									<span class="input-group-addon"><i class="fa fa-lock fa-lg" aria-hidden="true"></i></span>
									<input type="password" class="form-control" name="password" id="password"  placeholder="Enter your Password"/>
								</div>
							</div>
						</div>

						<div class="form-group">
							<label for="confirm" class="cols-sm-2 control-label">Confirm Password</label>
							<div class="cols-sm-10">
								<div class="input-group">
									<span class="input-group-addon"><i class="fa fa-lock fa-lg" aria-hidden="true"></i></span>
									<input type="password" class="form-control" name="confirm" id="confirm"  placeholder="Confirm your Password"/>
								</div>
							</div>
						</div>

						<div class="form-group ">
						<input type="submit" name="register" class="btn btn-primary" value="Submit">
							<!--<a target="_blank" type="button" id="button" class="btn btn-primary btn-lg btn-block login-button">Register</a> -->
						</div>
						
					</form>
				</div>
			</div>
		</div>

		 <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="bootstrap"></script>
    <!--<div class="wrapper">
        <h2>Sign Up</h2>
        <p>Please fill this form to create an account.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>Username:<sup>*</sup></label>
                <input type="text" name="username"class="form-control">
            </div>    
            <div class="form-group">
                <label>Password:<sup>*</sup></label>
                <input type="password" name="password" class="form-control" >
            </div>
            <div class="form-group">
                <label>Confirm Password:<sup>*</sup></label>
                <input type="password" name="confirm_password" class="form-control">
            </div>
            <div class="form-group">
                <input type="submit" name="register" class="btn btn-primary" value="Submit">
                <input type="reset" class="btn btn-default" value="Reset">
            </div>
            <p>Already have an account? <a href="login.php">Login here</a>.</p>
        </form>
    </div>    -->
</body>
</html>