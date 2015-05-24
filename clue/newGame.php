<?php 

require './include/clueDbHeader.php'; 
session_start();

$createfailed = NULL;

// check if there is POST data from a creation attempt
if (isset($_POST["userid"]) && $status === true) {
	// verify that the user ids exist
	$playerQuery = "SELECT id FROM players";
	$playerStatement = $db->prepare($playerQuery);
	$playerStatement->execute();

	$foundall = true;

	foreach($playerStatement->fetchAll() as $playerRow) {
		$tempfound = false;

		foreach($_POST["userid"] as $id) {
			if ($playerRow["id"] == $id) {
				$tempfound = true;
			}
		}

		if ($tempfound == false) {
			$foundall = false;
		}
		
	}


	if ($foundall == true) {
		// create the game here


		// set created flag
		$createfailed = true;
	} else {
		// set created flag
		$createfailed = false;
	}
}


?>

<!DOCTYPE html>

<html>

<head>
<?php
require './include/bootstrapHeader.php';
?>
<link href="/css/signin.css" rel="stylesheet" />
<title>Clue - Create Player</title>
</head>

<body>

<div class="container" >

<form name="login" method="POST" action="" class="form-signin">
<h2 class="form-signin-heading">Please select players</h2>
<?php

// Check if there was an unsuccessful creation attempt
if (isset($createfailed)) {
if ($createfailed == true) {
	echo '<div class="alert alert-danger alert-dismissible" role="alert">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
  <strong>Warning!</strong> Game failed to create.</div>' . "\n";
}

if ($createfailed == false) {
	echo '<div class="alert alert-success alert-dismissible" role="alert">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
  <strong>Congratulations!</strong> Game created.</div>' . "\n";
}
}

///////////////////////////////////////////////////////////////////////////////////////////

// Create the checkbox list


echo '<div class="list-group" >';

// get the list of players from the db
$listQuery = "SELECT id, username FROM players ORDER BY username";
$listStatement = $db->prepare($listQuery);
$listStatement->execute();

foreach($listStatement->fetchAll() as $listRow) {
	echo '<div class="list-group-item"><input type="checkbox" name="userid[]" value="' . $listRow["id"] . '" />' . $listRow["username"] . '</div>';
}

echo '</div>';
?>

<input class="btn btn-block btn-primary" type="submit" value="Create Game With Specified Players" />	

</form>
</div>
<?php
require './include/bootstrapFooter.php';
?>

</body>

</html>
