<?php 

require './include/clueDbHeader.php'; 
session_start();

// make sure you're signed in, otherwise get booted.
if (!isset($_SESSION["userid"]) || !isset($_GET["game_number"])) {
	header ( 'Location:/clue/nexus.php');
}

// check for all three pieces of data...
if (!isset($_POST["room"]) || !isset($_POST["suspect"]) || !isset($_POST["weapon"])) {
	header ( 'Location:/clue/play.php?game_number=' . $_GET["game_number"]);
}

// check to see if your answer was correct
$query = "SELECT id FROM body_cards " .
		"WHERE game_number=:gamenumber " .
		"AND room_id=:room " .
		"AND suspect_id=:suspect " .
		"AND weapon_id=:weapon";
$statement = $db->prepare($query);
$statement->bindValue(':gamenumber', $_GET['game_number']);
$statement->bindValue(':room', $_POST['room']);
$statement->bindValue(':suspect', $_POST["suspect"]);
$statement->bindValue(':weapon', $_POST['weapon']);
$statement->execute();
$correct = false;
if ($statement->rowCount() > 0 ) {
	// SUCCESS
	$correct = true;
}

?>

<!DOCTYPE html>
<html>
<head>
<?php
require './include/bootstrapHeader.php';
?>
<title>I accuse...!</title>
</head>

<body>
<div class="container">
<?php

// Check if there was an unsuccessful login attempt
if ($correct == false) {
	echo '<div class="alert alert-danger alert-dismissible" role="alert">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>You have guessed incorrectly. Better luck next time.</div>' . "\n";
} else {
	echo '<div class="alert alert-success alert-dismissible" role="alert">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
  <strong>Congratulations!</strong> You got it!</div>' . "\n";
}

?>
</div>
<?php
require './include/bootstrapFooter.php';
?>
</body>
</html>