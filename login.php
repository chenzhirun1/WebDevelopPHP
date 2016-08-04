<?php
/********************************************************************************
INT322 D 
ZHIRUN CHEN
2014 NOV 29

Student Declaration

I/we declare that the attached assignment is my/our own work in accordance 
with Seneca Academic Policy. No part of this assignment has been copied manually 
or electronically from any other source (including web sites) or distributed to 
other students.

		
Name ------ZHIRUN CHEN----------------------------

Student ID -----076-898-121--------------------

************************************************************************************/?>
<?php
ob_start();
session_start();

if(isset($_SESSION['username'])){
	header("Location: view.php");
	exit();
}

require_once("library.php");

if(isset($_POST['username'])){
	$link = new DBLink();
	$username=trim($_POST['username']);
	$sql = "SELECT * from users WHERE username='$username'";
	$ref = $link->runQuery($sql);
	if($link->isLastEmpty()){
		$invalid = true;
	}else{
		$data = $link->fetchNextRecord($ref);
		if($data['password'] == crypt($_POST['password'], $data['password'])){
			$_SESSION['username'] = $data['username'];
			$_SESSION['role'] = $data['role'];
			header("Location: view.php");
			exit();
		} else {
			$invalid = true;
		}
	}
}
	if(isset($_POST['forgotPassword'])){
		$link = new DBLink();
		$username = $link->sanitize($_POST['forgotPassword']);
		$ref = $link->runQuery("SELECT * FROM `users` WHERE `username` = '$username'");
		//Check whether the email exists in DB
		if(!$link->isLastEmpty()){
			$u = $link->fetchNextRecord($ref);
			$to = $username;
			$subject = "Password Hint from Paradise Games";
			$message = "Your username: " . $username . "\nYour password hint: " . $u['passwordHint'];
			mail($to, $subject, $message);
		}
	}
	

	generate_header("PG | Login");

	?>
	<p class='pageTitle'>Login</p>
	<?php
	if(isset($_GET['forgot'])){
	?>
	<form action="login.php" method="POST" class="loginForm">
		<label>Email address: </label><input type='text' name='forgotPassword'/><br>
		<input type='submit' value='Submit'/>
	</form>
	<?php
	} else {
	?>
	<form action="" method="POST" class="loginForm">
		<P style="color:red;"><?=isset($invalid)?"Invalid username or password.":""?></P>
		<label>User name:</label><input type='text' name='username' value='<?=isset($_POST['username'])?$_POST['username']:""?>' /><br/>
		<label>Password:</label><input type='password' name='password' /><br/>
		<input type='submit' value='Login'/>
	</form>
	<br>
	<a href='?forgot' style="margin-left:10px; text-decoration:underline;">Forgot your password?</a>
	<?php
	}
	generate_footer();
?>