<?php
include_once("config.php");
extract($_REQUEST);
function showForm($error="LOGIN"){
	?>
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
						  "DTD/xhtml1-transitional.dtd">
	<html>
	<body>
	<?php echo $error; ?>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
	<table>
	<tr>
		<td>Username:</td>
		<td><input name="user" type="text"/></td>
	</tr>
	<tr>
		<td>Password:</td>
		<td><input name="passwd" type="password"/></td>
	</tr>
	<tr>
		<td colspan="2"><input type="submit" name="submit" value="Login"/></td>
	</tr>
	</table>  
	</form>
	</body>       
	<?php   
}
// check the login form if submit is pushed
if (isset($submit) && $submit == "Login"){
	// check the submitted form for the values if good set the cookie
	$pass = isset($_POST['passwd']) ? $_POST['passwd'] : '';
    $user = isset($_POST['user']) ? $_POST['user'] : '';
	if ($user != $main_username) {
		showForm("Wrong Username");
		exit();     
	}	
	if ($pass != $main_password) {
		showForm("Wrong password");
		exit();     
	}
	// all good so set the cookie
	setcookie("site_username",$user, time()+3600*24,"/");
	setcookie("site_password",$pass, time()+3600*24,"/");
	header("location:".$_SERVER['PHP_SELF']);
} 
// submit is not pushed, just check the cookied stuff
if(isset($_COOKIE['site_username']) AND isset($_COOKIE['site_password'])){
	// check the cookie if it exists
	if($_COOKIE['site_username'] != $main_username){
		showform("Wrong Username");
		exit;
	}
	if($_COOKIE['site_password'] != $main_password){
		showform("Wrong Password");
		exit;
	}
	// all good don't do anything else
} else {
	// show the form to login
   showForm();
   exit();
}
?>