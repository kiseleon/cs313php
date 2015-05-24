<?php 

require dirname( __FILE__ ) . './include/clueDbHeader.php'; 
session_start();

$createfailed = NULL;

// check if there is POST data from a creation attempt
if (isset($_POST["username"]) && isset($_POST["pin"]) && $status === true) {
	// verify that the username/pin combination is right
	$userQuery = "SELECT username FROM players WHERE username=:username";
	$userStatement = $db->prepare($userQuery);
	$userStatement->bindValue(':username', $_POST["username"]);

	$userStatement->execute();

	$login;

	$createfailed = false;


	// check if it is there
	foreach($userStatement->fetchAll() as $row) {
		$login = $row["username"]; // it is taken!
		$createfailed = true;
		
	}
 
	// if it fails, you're still here. Set this to true to clear the flag for the error message
	if ($createfailed == false) {
		//add the user here!
		//header ( 'Location:/clue/managePlayers.php');
	}
}


?>

<!DOCTYPE html>

<html>

<head>
<?php
require dirname( __FILE__ ) . './include/bootstrapHeader.php';
?>
<link href="/css/signin.css" rel="stylesheet" />
<title>Clue - Create Player</title>
</head>

<body>

<div class="container" >

<form name="login" method="POST" action="" class="form-signin">
<h2 class="form-signin-heading">Please create a player</h2>
<?php

// Check if there was an unsuccessful login attempt
if (isset($createfailed)) {
if ($createfailed == true) {
	echo '<div class="alert alert-danger alert-dismissible" role="alert">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
  <strong>Warning!</strong> Username "' . htmlspecialchars($_POST["username"]) . '" already in use. Try another.</div>' . "\n";
}

if ($createfailed == false) {
	echo '<div class="alert alert-success alert-dismissible" role="alert">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
  <strong>Congratulations!</strong> Username "' . htmlspecialchars($_POST["username"]) . '" created.</div>' . "\n";
}
}
?>
	<label for="username" class="sr-only">Username:</label>
	<input type="input" class="form-control" size="40" maxlength="40" name="username" placeholder="Username" required autofocus />
	<label for="pin" class="sr-only">PIN#:</label>
	<input type="password" class="form-control" size="4" maxlength="4" name="pin" placeholder="PIN #" required /> 
	<p><input class="btn btn-lg btn-default btn-block" type="submit" value="Create User" /></p>


</form>
</div>
<?php
require dirname( __FILE__ ) . './include/bootstrapFooter.php';
?>

</body>

</html>