<?php

require './include/clueDbHeader.php'; 
session_start();

// check to see if the userid, game number, x1, x2, y1, and y2 are present
// Boot them back to the new game page if no players are chosen
if (!isset($_POST["userid"]) || !isset($_GET["game_number"]) || !isset($_GET['x1']) || !isset($_GET['x2'])
	 || !isset($_GET['y1']) || !isset($_GET['y2'])) {
	//header ( 'Location: /clue/nexus.php');
}

$game_number = htmlspecialchars($_GET['game_number']);
$x1 = htmlspecialchars($_GET['x1']);
$x2 = htmlspecialchars($_GET['x2']);
$y1 = htmlspecialchars($_GET['y1']);
$y2 = htmlspecialchars($_GET['y2']);

echo "X1 = " . var_dump($x1);
echo "Y1 = " . var_dump($y1);
echo "X2 = " . var_dump($x2);
echo "Y2 = " . var_dump($y2);

function checkForPlayer($xpos, $ypos) {
	// check for players in the current game in the position
	$query = "SELECT l.id FROM location l " .
			"JOIN games g ON l.game_id=g.id " .
			"WHERE l.x_pos=:xpos AND l.y_pos=:ypos " .
			"AND g.game_number=:game_number";
	global $db, $game_number;
	$statement = $db->prepare($query);
	$statement->bindValue(':xpos', $xpos);
	$statement->bindValue(':ypos', $ypos);
	$statement->bindValue(':game_number', $game_number);
	$statement->execute();

	// NOTE: This will grab the FIRST player at the spot
	foreach ($statement->fetchAll() as $row) {
		return $row["id"];
	}


	return null;
}


// check the first spot to see if there is a player there
$player1 = checkForPlayer($x1, $y1);

// check the second spot to see if there is a player there
$player2 = checkForPlayer($x2, $y2);


// both are empty (abort)
if ($player1 === null && $player2 === null) {
	header ( 'Location: /clue/board.php?game_number=' . $game_number);
}
// first is present, second is empty (move first to second)
else if (isset($player1) && $player2 === null) {
	$query = "UPDATE location SET x_pos=:xpos, y_pos=:ypos WHERE id=:player1";
	$statement = $db->prepare($query);
	$statement->bindValue(':xpos', $x2);
	$statement->bindValue(':ypos', $y2);
	$statement->bindValue(':player1', $player1);
	$statement->execute();
	
	// Redirect back to the board when finished
	header ( 'Location: /clue/board.php?game_number=' . $game_number);
}

// first is empty, second is present (move second to first)
else if ($player1 === null && isset($player2)) {
	$query = "UPDATE location SET x_pos=:xpos, y_pos=:ypos WHERE id=:player2";
	$statement = $db->prepare($query);
	$statement->bindValue(':xpos', $x1);
	$statement->bindValue(':ypos', $y1);
	$statement->bindValue(':player2', $player2);
	$statement->execute();
	
	// Redirect back to the board when finished
	header ( 'Location: /clue/board.php?game_number=' . $game_number);}

// both are present (swap first and second)
else if (isset($player1) && isset($player2)) {
	$query = "UPDATE location SET x_pos=:xpos, y_pos=:ypos WHERE id=:player1";
	$statement = $db->prepare($query);
	$statement->bindValue(':xpos', $x2);
	$statement->bindValue(':ypos', $y2);
	$statement->bindValue(':player1', $player1);
	$statement->execute();

	$query = "UPDATE location SET x_pos=:xpos, y_pos=:ypos WHERE id=:player2";
	$statement = $db->prepare($query);
	$statement->bindValue(':xpos', $x1);
	$statement->bindValue(':ypos', $y1);
	$statement->bindValue(':player2', $player2);
	$statement->execute();

	// Redirect back to the board when finished
	header ( 'Location: /clue/board.php?game_number=' . $game_number);
}
else {
	// ERROR, don't redirect
	echo "It broke!";
	var_dump($player1);
	var_dump($player2);

}







// NOTE: All HTML parts are to be deleted once the script works. It is just for debugging.
?>

