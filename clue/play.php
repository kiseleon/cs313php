<?php 

require dirname( __FILE__ ) . './include/clueDbHeader.php'; 
session_start();

// make sure you're signed in, otherwise get booted.
if (!isset($_SESSION["userid"]) || !isset($_GET["game_number"])) {
	header ( 'Location:/clue/login.php');
}

// make sure the game number is valid and that the player is part of it
$validateQuery = "SELECT game_number, player_id FROM games g JOIN players p ON g.player_id=p.id WHERE p.id=:userid AND g.game_number=:game_number";
$validateStatement = $db->prepare($validateQuery);
$validateStatement->bindValue(':userid', $_SESSION['userid']);
$validateStatement->bindValue(':game_number', $_GET['game_number']);
$validateStatement->execute();

$validated = false;

foreach ($validateStatement->fetchAll() as $validateRow) {
	$validated = true;
}

// Either you aren't a valid player for this game or the game doesn't exist
if ($validated === false) {
	// boot back to login page
	header( 'Location:/clue/login.php' );
}

?>

<!DOCTYPE html>

<html>

<head>
<?php
require dirname( __FILE__ ) . './include/bootstrapHeader.php';
?>
<title>Clue - Play</title>
</head>

<body>

<div class="container">
<?php
// Generate the list of cards: First Rooms, then Suspects, then Weapons
$roomQuery = "SELECT r.id, r.name FROM room r " . 
			"JOIN player_rooms pr ON r.id=pr.room_id " .
			"JOIN games g ON g.id=pr.game_id " .
			"JOIN players p ON p.id=g.player_id " .
			"WHERE p.id=:userid AND g.game_number=:game_number " .
			"ORDER BY r.id";
$roomStatement = $db->prepare($roomQuery);
$roomStatement->bindValue(':userid', $_SESSION['userid']);
$roomStatement->bindValue(':game_number', $_GET['game_number']);
$roomStatement->execute();

if ($roomStatement->rowCount() > 0) {
	echo '<div class="list-group">';
	echo '<li class="list-group-item active">Rooms:</li>';
	foreach ($roomStatement->fetchAll() as $roomRow) {
		echo '<li class="list-group-item">' . $roomRow["name"] . '</li>';
	}
	echo '</div>';
}

$suspectQuery = "SELECT s.id, s.name FROM suspect s " . 
			"JOIN player_suspects ps ON s.id=ps.suspect_id " .
			"JOIN games g ON g.id=ps.game_id " .
			"JOIN players p ON p.id=g.player_id " .
			"WHERE p.id=:userid AND g.game_number=:game_number " .
			"ORDER BY s.id";
$suspectStatement = $db->prepare($suspectQuery);
$suspectStatement->bindValue(':userid', $_SESSION['userid']);
$suspectStatement->bindValue(':game_number', $_GET['game_number']);
$suspectStatement->execute();

if ($suspectStatement->rowCount() > 0) {
	echo '<div class="list-group">';
	echo '<li class="list-group-item active">Rooms:</li>';
	foreach ($suspectStatement->fetchAll() as $suspectRow) {
		echo '<li class="list-group-item">' . $suspectRow["name"] . '</li>';
	}
	echo '</div>';
}

$weaponQuery = "SELECT w.id, w.name FROM weapon w " . 
			"JOIN player_weapons pw ON w.id=pw.weapon_id " .
			"JOIN games g ON g.id=pw.game_id " .
			"JOIN players p ON p.id=g.player_id " .
			"WHERE p.id=:userid AND g.game_number=:game_number " .
			"ORDER BY w.id";
$weaponStatement = $db->prepare($weaponQuery);
$weaponStatement->bindValue(':userid', $_SESSION['userid']);
$weaponStatement->bindValue(':game_number', $_GET['game_number']);
$weaponStatement->execute();

if ($weaponStatement->rowCount() > 0) {
	echo '<div class="list-group">';
	echo '<li class="list-group-item active">Rooms:</li>';
	foreach ($weaponStatement->fetchAll() as $weaponRow) {
		echo '<li class="list-group-item">' . $weaponRow["name"] . '</li>';
	}
	echo '</div>';
}

///////////////////////////////////////////////////////////////////////


// Get the current guessing information from the database

// Generate the list of tracked cards: First Rooms, then Suspects, then Weapons
$roomQuery = "SELECT r.id, r.name, tr.status FROM room r " . 
			"JOIN track_rooms tr ON r.id=tr.room_id " .
			"JOIN games g ON g.id=tr.game_id " .
			"JOIN players p ON p.id=g.player_id " .
			"WHERE p.id=:userid AND g.game_number=:game_number " .
			"ORDER BY r.id";
$roomStatement = $db->prepare($roomQuery);
$roomStatement->bindValue(':userid', $_SESSION['userid']);
$roomStatement->bindValue(':game_number', $_GET['game_number']);
$roomStatement->execute();

if ($roomStatement->rowCount() > 0) {
	echo '<div class="list-group">';
	echo '<li class="list-group-item list-group-item-warning">Rooms:</li>';
	foreach ($roomStatement->fetchAll() as $roomRow) {
		echo '<li class="list-group-item">' . $roomRow["name"] . ': ' . $roomRow['status'] . '</li>';
	}
	echo '</div>';
}

$suspectQuery = "SELECT s.id, s.name, ts.status FROM suspect s " . 
			"JOIN track_suspects ts ON s.id=ts.suspect_id " .
			"JOIN games g ON g.id=ts.game_id " .
			"JOIN players p ON p.id=g.player_id " .
			"WHERE p.id=:userid AND g.game_number=:game_number " .
			"ORDER BY s.id";
$suspectStatement = $db->prepare($suspectQuery);
$suspectStatement->bindValue(':userid', $_SESSION['userid']);
$suspectStatement->bindValue(':game_number', $_GET['game_number']);
$suspectStatement->execute();

if ($suspectStatement->rowCount() > 0) {
	echo '<div class="list-group">';
	echo '<li class="list-group-item list-group-item-warning">Rooms:</li>';
	foreach ($suspectStatement->fetchAll() as $suspectRow) {
		echo '<li class="list-group-item">' . $suspectRow["name"] . ': ' . $suspectRow['status'] . '</li>';
	}
	echo '</div>';
}

$weaponQuery = "SELECT w.id, w.name, tw.status FROM weapon w " . 
			"JOIN track_weapons tw ON w.id=tw.weapon_id " .
			"JOIN games g ON g.id=tw.game_id " .
			"JOIN players p ON p.id=g.player_id " .
			"WHERE p.id=:userid AND g.game_number=:game_number " .
			"ORDER BY w.id";
$weaponStatement = $db->prepare($weaponQuery);
$weaponStatement->bindValue(':userid', $_SESSION['userid']);
$weaponStatement->bindValue(':game_number', $_GET['game_number']);
$weaponStatement->execute();

if ($weaponStatement->rowCount() > 0) {
	echo '<div class="list-group">';
	echo '<li class="list-group-item list-group-item-warning">Rooms:</li>';
	foreach ($weaponStatement->fetchAll() as $weaponRow) {
		echo '<li class="list-group-item">' . $weaponRow["name"] . ': ' . $weaponRow['status'] . '</li>';
	}
	echo '</div>';
}


?>

</div>


<?php
require dirname( __FILE__ ) . './include/bootstrapFooter.php';
?>
</body>

</html>