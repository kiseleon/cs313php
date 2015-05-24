<?php 


require getenv('OPENSHIFT_HOMEDIR') . './include/clueDbHeader.php'; 
session_start();

// check if they are already logged in and the database connection is working
if (isset($_SESSION["userid"]) && $status == true) {
	// make sure the username still exists
	$checkQuery = 'SELECT id FROM players WHERE id=' . $_SESSION["userid"];
	$checkStatement = $db->prepare($checkQuery);
	$checkStatement->execute();

	$loggedIn = false;

	foreach($checkStatement->fetchAll() as $row) {
		$loggedIn = true;
	}

	// if the username is valid, redirect instead of going to login screen
	if ($loggedIn === true) {
		header( 'Location:/clue/nexus.php');
	}

	// Otherwise, clear your session to get rid of the invalid data
	if ($loggedIn === false) {
		session_destroy();
	}
}

// check if there is POST data from a login attempt
if (isset($_POST["username"]) && isset($_POST["pin"]) && $status === true) {
	// verify that the username/pin combination is right
	$userQuery = "SELECT id FROM players WHERE username=:username AND pin=:pin";
	$userStatement = $db->prepare($userQuery);
	$userStatement->bindValue(':username', $_POST["username"]);
	$userStatement->bindValue(':pin', $_POST["pin"]);

	$userStatement->execute();

	$loginID = -1;

	// check if it is there
	foreach($userStatement->fetchAll() as $row) {
		$loginID = $row["id"]; // it is! set the loginID
		$_SESSION["userid"] = $row["id"];
		header( 'Location:/clue/nexus.php');
	}
 
	// if it fails, you're still here. Set this to true to set the flag for the error message
	$loginfailed = true;
}


?>

<!DOCTYPE html>

<html>

<head>
<?php
require getenv('OPENSHIFT_HOMEDIR') . './include/bootstrapHeader.php';
?>
<link href="/css/signin.css" rel="stylesheet" />
<title>Clue - Login</title>
</head>

<body>

<div class="container" >

<form name="login" method="POST" action="" class="form-signin">
<h2 class="form-signin-heading">Please log in</h2>
<?php

// Check if there was an unsuccessful login attempt
if (isset($loginfailed)) {
	echo '<div class="alert alert-danger alert-dismissible" role="alert">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
  <strong>Warning!</strong> Incorrect username/password combination.</div>' . "\n";
}
?>
	<label for="username" class="sr-only">Username:</label>
	<input type="input" class="form-control" size="40" maxlength="40" name="username" placeholder="Username" required autofocus />
	<label for="pin" class="sr-only">PIN#:</label>
	<input type="password" class="form-control" size="4" maxlength="4" name="pin" placeholder="PIN #" required /> 
	<p><input class="btn btn-lg btn-default btn-block" type="submit" value="Log In" /></p>


</form>
</div>
<?php
require getenv('OPENSHIFT_HOMEDIR') . './include/bootstrapFooter.php';
?>

</body>

</html>
