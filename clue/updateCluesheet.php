<?php

require './include/clueDbHeader.php'; 
session_start();

// make sure you're signed in, otherwise get booted.
if (!isset($_SESSION["userid"]) || !isset($_POST["game_id"])) {
	header ( 'Location:/clue/login.php');
}
$game_id = htmlspecialchars($_POST["game_id"]);
// get lists of each item type

// get the list of names/ids for rooms
$query = "SELECT name, id FROM room ORDER BY name";
$statement = $db->prepare($query);
$statement->execute();

$roomList = array();

foreach ($statement->fetchAll() as $row) {
	$roomList[$row['id']] = $row['name'];
}

// get the list of names/ids for suspects
$query = "SELECT name, id FROM suspect ORDER BY name";
$statement = $db->prepare($query);
$statement->execute();

$suspectList = array();

foreach ($statement->fetchAll() as $row) {
	$suspectList[$row['id']] = $row['name'];
}

// get the list of names/ids for weapons
$query = "SELECT name, id FROM weapon ORDER BY name";
$statement = $db->prepare($query);
$statement->execute();

$weaponList = array();

foreach ($statement->fetchAll() as $row) {
	$weaponList[$row['id']] = $row['name'];
}

/////////////////////////////////////////////////////////////////
// go through each of the rooms, suspects, and weapons and check for a POST variable named after it

// ROOMS
foreach($roomList as $key => $room) {
	$converted = str_replace(' ', "_", $room);
	if (isset($_POST[$converted])) {
		$query = "UPDATE track_rooms SET status=:status WHERE game_id=:gameid AND room_id=:roomid";
		$statement = $db->prepare($query);
		$statement->bindValue(':status', $_POST[$converted]);
		$statement->bindValue(':gameid', $game_id);
		$statement->bindValue(':roomid', $key);
		$statement->execute();
	}
}

// SUSPECTS
foreach($suspectList as $key => $suspect) {
	$converted = str_replace(' ', "_", $suspect);
	if (isset($_POST[$converted])) {
		$query = "UPDATE track_suspects SET status=:status WHERE game_id=:gameid AND suspect_id=:suspectid";
		$statement = $db->prepare($query);
		$statement->bindValue(':status', $_POST[$converted]);
		$statement->bindValue(':gameid', $game_id);
		$statement->bindValue(':suspectid', $key);
		$statement->execute();
	}
}

// WEAPONS
foreach($weaponList as $key => $weapon) {
	$converted = str_replace(' ', "_", $weapon);
	if (isset($_POST[$converted])) {
		$query = "UPDATE track_weapons SET status=:status WHERE game_id=:gameid AND weapon_id=:weaponid";
		$statement = $db->prepare($query);
		$statement->bindValue(':status', $_POST[$converted]);
		$statement->bindValue(':gameid', $game_id);
		$statement->bindValue(':weaponid', $key);
		$statement->execute();
	}
}

// Now, redirect back to the game

header ( 'Location: /clue/play.php?game_number=' . $_GET['game_number']);

?>