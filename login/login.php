<!-- CSCI311 | Names: Tiffany & Ning | Final Project -->
<?php
session_start();/* Starts the session */
//include helper files
require_once("../private/inputHelper.php");
require_once("../private/loginHelpers.php");

if(isset($_SESSION['url'])) 
   $url = $_SESSION['url']; // holds url for last page visited.
else 
   $url = "../index.php"; // default page for 

//declare variables
//check if already logged in, and set status
$status;
$err;
if(isset($_SESSION['UserData']['userid'])){
	$status=2;	
}
//check if submit button pressed, and if so, get data from form
if(isset($_POST['submit'])){
	$form_id=$_POST['user'];
	$form_pw=$_POST['pw'];
	//validate data
	if(!has_presence($form_id) || !has_presence($form_pw)){
		$err = "Sorry, userid and password cannot be empty";
	}
	//attempt login with submitted data
	if(!isset($err) && attempt_login($form_id, $form_pw)){
		$_SESSION['UserData']['userid'] = $form_id;
		$status = 1;
	}else{
		$status = 0;
		$err = "Sorry, login failed, please try again.";
	}
}
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
   <meta charset=UTF-8" />
   <link rel="stylesheet" type="text/css" href="./css/loginStyles.css">
</head>

<body>
<!-- Viu Logo Header -->	
	<header>
		<div class="viuLogo"><a><img id="viuLogo" src="../Home/media/viu-logo.png"/></a></div>
	    <h1 class="head"><b>BOOK BUY/SELL/TRADE</b></h1>
<div class="registercontainer">
<?php 
	//check status, and either show logged in, or show register link
	if($status === 1 || $status ===2){
		htmlspecialchars($_SESSION['UserData']['userid']);
		header("Location: $url");
		echo "<a class=\"logout\" href=\"logout.php\">Logout</a>";
	}else{
		echo "<a class=\"register\" href=\"register.php\">Register</a>";
	}
?>
</div> <!-- end register container-->
	</header>

  <!-- login form -->

<div id="main">

    <!-- Container to display text books-->
    <div class="signIn">
    	<h2 id="subhead"> Sign In </h2>
		<p> Please sign in to access your account </p>
	<div id="signInRow">
		<form action="login.php" method="POST">
			<label class="myLabel"  for="user">User ID: </label>
			<input class="myInput" type="text" id="user" name="user" placeholder="Please enter your student or instructor ID" required="required"\>
			<label class="myLabel" for="pw">Password: </label>
			<input class="myInput" type="password" id="pw" name="pw" placeholder="Please enter your password" required="required"\>
			<input type="submit" id="submit" name="submit" value="login"\>
		</form>
			<?php
			if(isset($err)){
				echo "<br/><span>$err</span><br/>";
			}
			?>
	</div>    
    </div>

</div>
  </body>
  <footer><small> Tiffany & Ning Copyright &copy; 2019 </small></footer>
</html>
