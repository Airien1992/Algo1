<?php
ob_start();
session_start();
require_once "config.php";
require_once "func.php";
$_SESSION['valid']=false;
global $names;
$names=[];
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
  form{
    border: 3px solid #f1f1f1;
  }
  .form-signin {
    max-width: 330px;
    padding: 30px;
    margin: 0 auto;
    color: #017572;
  }
  .form-signin .form-group{
    font-size: 25px;
    align: center;
    color: white;
  }
  .form-signin .form-signin-heading,
  .form-signin .checkbox {
    margin-bottom: 10px;
    font-size: 10px;
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
  .login_btn{
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
    color: white;
  }
  h3{
    font-size: 30px;
    color: white;
  }
  </style>
</head>
<body>
  <h2>The pursuit of happiness</h2>
  <div class = "container form-signin">
    <?php
    if (isset($_POST['login']) && !empty($_POST['username']) && !empty($_POST['password'])) {
      global $Pass;
      global $mysqli;
      $User = $_POST['username'];
      $Pass = $_POST['password'];
      $mysqli = initialize_mysql_connection();
      $sql="SELECT * FROM members WHERE username='$User' and password='$Pass'";
      $result=$mysqli->query($sql);
      $row=$result->fetch_assoc();
      if( $row['username']==$User){
        $_SESSION['username']=$User;
        $_SESSION['Id'] = session_id();
        $_SESSION['valid'] = true;
        $_SESSION['timeout'] = time();
        echo 'You have entered valid use name and password';
        close_mysql_connection();
        header('Location: map.php');
      }
      else {
        $msg = 'Wrong username or password';
        close_mysql_connection();
      }
    }
    ?>
  </div> <!-- /container -->
  <div class = "container">
    <form class = "form-signin" role = "form" action = "<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method = "post">
      <h4 class = "form-signin-heading"></h4>
      <div class="form-group">
        <label >Username</label>
        <input type = "text" class = "form-control" name = "username" placeholder = "username" required autofocus></br>
      </div>
      <div class="form-group">
        <label>Password</label>
        <input type = "password" class = "form-control" name = "password" placeholder = "password" required>
      </div>
      <div class="form-group">
        <button class = "login_btn" type = "submit" name = "login" Id="log">Login</button>
        <button class = "btn" type = "submit"><a href="register.php">Registreren</a></button>
        <button class = "btn" type = "submit" ><a href="recovery.php">Recovery</a></button>
        <button class = "btn" type = "submit"><a href="login.php">Logout</a></button>
      </div>
    </form>
  </div>
</body>
</html>
