<?php
require './include/clueDbHeader.php'; 
session_start();

// First, make sure you have the http GET id
if (!isset($_GET["gamenumber"])) {
	header ( 'Location: /clue/manageGames.php');
}

// second, delete the game

// THIS IS NOT GOING TO WORK UNTIL LATER
//DO NOT UNCOMMENT UNTIL THE ADD NEW GAME WORKS FULLY - THIS DATA TOOK FOREVER TO TYPE BY HAND


//First, delete the body cards, tracked cards, and player cards.
//THEN you can delete the rows from the games table.

// BODY CARDS
$query = "DELETE FROM body_cards WHERE game_number=:gamenumber";
$statement = $db->prepare($query);
$statement->bindValue(':gamenumber', $_GET['gamenumber']);
$statement->execute();

/////////////////////////////////////////////////////////////////////////////////////////////////////
// For the next few, you need to do a delete for each of the game_id's associated with the game_number
// Therefore, we will construct an array to hold the game_id's so we can reuse them.
////////////////////////////////////////////////////////////////////////////////////////////////////

$query = "SELECT id FROM games WHERE game_number=:gamenumber";
$statement = $db->prepare($query);
$statement->bindValue(':gamenumber', $_GET['gamenumber']);
$statement->execute();

// if there are no game rows to delete... boot them!
if ($statement->rowCount() == 0) {
	header ( 'Location: /clue/manageGames.php' );
}

$gameIDs = array();

foreach ($statement->fetchAll() as $row) {
	$gameIDs[] = $row["id"];
}

// Now, $gameIDs holds all the game IDs we need to delete.

// LOCATION
foreach($gameIDs as $game_id) {
	$query = "DELETE FROM location WHERE game_id=" . $game_id;
	$statement = $db->prepare($query);
	$statement->execute();
}

// PLAYER_ROOMS
foreach($gameIDs as $game_id) {
	$query = "DELETE FROM player_rooms WHERE game_id=" . $game_id;
	$statement = $db->prepare($query);
	$statement->execute();
}

// PLAYER_SUSPECTS
foreach($gameIDs as $game_id) {
	$query = "DELETE FROM player_suspects WHERE game_id=" . $game_id;
	$statement = $db->prepare($query);
	$statement->execute();
}

// PLAYER_WEAPONS
foreach($gameIDs as $game_id) {
	$query = "DELETE FROM player_weapons WHERE game_id=" . $game_id;
	$statement = $db->prepare($query);
	$statement->execute();
}

// TRACK_ROOMS
foreach($gameIDs as $game_id) {
	$query = "DELETE FROM track_rooms WHERE game_id=" . $game_id;
	$statement = $db->prepare($query);
	$statement->execute();
}

// TRACK_SUSPECTS
foreach($gameIDs as $game_id) {
	$query = "DELETE FROM track_suspects WHERE game_id=" . $game_id;
	$statement = $db->prepare($query);
	$statement->execute();
}

// TRACK_WEAPONS
foreach($gameIDs as $game_id) {
	$query = "DELETE FROM track_weapons WHERE game_id=" . $game_id;
	$statement = $db->prepare($query);
	$statement->execute();
}

// and finally,
// GAMES
foreach($gameIDs as $game_id) {
	$query = "DELETE FROM games WHERE id=" . $game_id;
	$statement = $db->prepare($query);
	$statement->execute();
}



header ( 'Location: /clue/manageGames.php' );



?>