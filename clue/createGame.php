<?php

require './include/clueDbHeader.php'; 
session_start();


// check to see if players were chosen as POST data
// Boot them back to the new game page if no players are chosen
if (!isset($_POST["userid"]) || !isset($_POST["character"])) {
	header ( 'Location: /clue/newGame.php');
}


// create the game itself - needs game_number, player_id's, and player_characters

// array of players
$players = $_POST["userid"];

// get the list of characters
$characters = $_POST["character"];

// find the highest game_number and add 1
$maxQuery = "SELECT MAX(game_number) AS game_number FROM games;";
$maxStatement = $db->prepare($maxQuery);
$maxStatement->execute();

$gameNumber = 0;
foreach ($maxStatement->fetchAll() as $row) {
	$gameNumber = $row['game_number'];
}
$gameNumber++;


// actually add the players to the game
for ($i = 0; $i < count($players); $i++) {
	$addPlayerQuery = "INSERT INTO games (game_number, player_id, player_character) " .
 				"VALUES (" . $gameNumber . ", :playerID, :character);";
 	$addPlayerStatement = $db->prepare($addPlayerQuery);
 	$addPlayerStatement->bindValue(':playerID', $players[$i]);
	$addPlayerStatement->bindValue(':character', $characters[$i]);
	$addPlayerStatement->execute();
}


// declare the card class used for keeping track of each card type
class Card {
	private $type = 0; // 1 = room, 2=suspect, 3=weapon
	private $id   = 0; // this is the card id

	public function setVars ($t, $i) {
		$this->type = $t;
		$this->id = $i;
	}

	public function getType() {
		return $this->type;
	}
	public function getId() {
		return $this->id;
	}
}

// get the list of ids for rooms
$roomIDQuery = "SELECT id FROM room ORDER BY id";
$roomIDStatement = $db->prepare($roomIDQuery);
$roomIDStatement->execute();

$array = array();
$roomList = array();

foreach ($roomIDStatement->fetchAll() as $row) {
	$instance = new Card();
	$instance->setVars(1, $row['id']);
	$array[] = $instance;
	$roomList[] = $row['id'];
}

// get the list of ids for suspects
$suspectIDQuery = "SELECT id FROM suspect ORDER BY id";
$suspectIDStatement = $db->prepare($suspectIDQuery);
$suspectIDStatement->execute();

$suspectList = array();

foreach ($suspectIDStatement->fetchAll() as $row) {
	$instance = new Card();
	$instance->setVars(2, $row['id']);
	$array[] = $instance;
	$suspectList[] = $row['id'];
}

// get the list of ids for weapons
$weaponIDQuery = "SELECT id FROM weapon ORDER BY id";
$weaponIDStatement = $db->prepare($weaponIDQuery);
$weaponIDStatement->execute();

$weaponList = array();

foreach ($weaponIDStatement->fetchAll() as $row) {
	$instance = new Card();
	$instance->setVars(3, $row['id']);
	$array[] = $instance;
	$weaponList[] = $row["id"];
}

// At this point, you have an array with all three sets of cards.
// You now need to get the cards for Mr. Body.
$bodyRoom = 0;
$bodySuspect = 0;
$bodyWeapon = 0;
$numberOfCards = count($array);

while ($bodyRoom === 0) {
	$random = rand(0, $numberOfCards - 1);
	$card = $array[$random];
	if ($card->getType() === 1) {
		$bodyRoom = $card->getId();
		unset($array[$random]);
		$array = array_values($array);
		$numberOfCards--;
	}
}

while ($bodySuspect === 0) {
	$random = rand(0, $numberOfCards - 1);
	$card = $array[$random];
	if ($card->getType() === 2) {
		$bodySuspect = $card->getId();
		unset($array[$random]);
		$array = array_values($array);
		$numberOfCards--;
	}
}

while ($bodyWeapon === 0) {
	$random = rand(0, $numberOfCards - 1);
	$card = $array[$random];
	if ($card->getType() === 3) {
		$bodyWeapon = $card->getId();
		unset($array[$random]);
		$array = array_values($array);
		$numberOfCards--;
	}
}

// insert body cards into database
$bodyQuery = "INSERT INTO body_cards (game_number, room_id, suspect_id, weapon_id) " .
			"VALUES (" . $gameNumber . ", " . $bodyRoom . ", " . $bodySuspect . ", " . $bodyWeapon . ");";
$bodyStatement = $db->prepare($bodyQuery);
$bodyStatement->execute();


$numPlayers = count($players);

// create array of arrays of cards

$playerCards = array();

// make an array players that hold arrays of cards.
foreach($players as $player) {
	$playerCards[] = array();
}

// distribute the cards
while ($numberOfCards != 0) {
	foreach ($playerCards as $key => $player) {
		if ($numberOfCards != 0) {
			$random = rand(0, $numberOfCards - 1);
			$playerCards[$key][] = $array[$random];
			unset($array[$random]);
			$array = array_values($array);
			$numberOfCards--;

		}
	}
}

$index = 1;


// add each player's cards to the database
foreach ($playerCards as $key => $player) {
	// get the game_id for the player and game_number
	$playerQuery = "SELECT id FROM games " .
		"WHERE game_number=" . $gameNumber . " " .
		"AND player_id=" . $players[$key];

	$playerStatement = $db->prepare($playerQuery);
	$playerStatement->execute();

	$game_id = 0;

	foreach ($playerStatement->fetchAll() as $row) {
		$game_id = $row['id'];
	}


	foreach ($player as $card) {
		$query = "";
		if ($card->getType() == 1) {
			$query = "INSERT INTO player_rooms (game_id, room_id) VALUES (" . $game_id . ", " . $card->getId() . ")";
		} else if ($card->getType() == 2) {
			$query = "INSERT INTO player_suspects (game_id, suspect_id) VALUES (" . $game_id . ", " . $card->getId() . ")";
		} else if ($card->getType() == 3) {
			$query = "INSERT INTO player_weapons (game_id, weapon_id) VALUES (" . $game_id . ", " . $card->getId() . ")";
		} else {
			// You should never be here...
			assert(false);
		}
		$statement = $db->prepare($query);
		$statement->execute();
	}

	// while you're here and have the game id, add the board info
	// For now, it will start everyone at (1,1)
	$boardQuery = "INSERT INTO location (game_id, x_pos, y_pos) VALUES (" . $game_id . ", 1, 1);";
	$boardStatement = $db->prepare($boardQuery);
	$boardStatement->execute();


	// Again, while you have the game id, let's make use of it
	// Let's fill in the tracking tables.
	foreach ($roomList as $room_id) {
		$query = "INSERT INTO track_rooms (game_id, room_id, status) VALUES (" . $game_id . ", " .
			$room_id . ', "")';
		$statement = $db->prepare($query);
		$statement->execute();
	}
	foreach ($suspectList as $suspect_id) {
		$query = "INSERT INTO track_suspects (game_id, suspect_id, status) VALUES (" . $game_id . ", " .
			$suspect_id . ', "")';
		$statement = $db->prepare($query);
		$statement->execute();
	}
	foreach ($weaponList as $weapon_id) {
		$query = "INSERT INTO track_weapons (game_id, weapon_id, status) VALUES (" . $game_id . ", " .
			$weapon_id . ', "")';
		$statement = $db->prepare($query);
		$statement->execute();
	}


}




// at this point, the players should have been inserted into games, 
// cards into the three player_cardtypes, and body cards into body_cards,
// and inserted players at locations on the board.
// You *should* now be safe to redirect back to game management.

header ( 'Location: /clue/manageGames.php');




// NOTE: All HTML parts are to be deleted once the script works. It is just for debugging.
?>

