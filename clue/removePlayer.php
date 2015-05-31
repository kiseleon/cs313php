<?php
require './include/clueDbHeader.php'; 
session_start();

// First, make sure you have the http GET id
if (!isset($_GET["userid"])) {
	header ( 'Location: /clue/managePlayers.php');
}

// Second, check and make sure that the player id is valid for removal
// This can be done by checking what games are associated with this id
// if there are associated games, redirect to player management without doing anything
$checkQuery = "SELECT g.game_number FROM games g " .
			"WHERE g.player_id=:userid";
$checkStatement = $db->prepare($checkQuery);
$checkStatement->bindValue(':userid', htmlspecialchars($_GET['userid']));
$checkStatement->execute();

if ($checkStatement->rowCount() > 0) {
	header ( 'Location: /clue/managePlayers.php'); 
}

// Third, actually delete them.

$deleteQuery = "DELETE FROM players WHERE id=:userid";
$deleteStatement = $db->prepare($deleteQuery);
$deleteStatement->bindValue(':userid', $_GET['userid']);
$deleteStatement->execute();

header ( 'Location: /clue/managePlayers.php');

?>