<?php 

require './include/clueDbHeader.php'; 
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
require './include/bootstrapHeader.php';
?>
<title>Clue - Board</title>
</head>

<body>

<div class="container">

<?php
// Get the location data for all players in the game

$locationQuery = "SELECT p.username, l.x_pos, l.y_pos FROM location l " .
	"JOIN games g ON l.game_id=g.id " .
	"JOIN players p ON p.id=g.player_id " .
	"WHERE g.game_number=:game_number " .
	"ORDER BY p.username";
$locationStatement = $db->prepare($locationQuery);
$locationStatement->bindValue(':game_number', $_GET['game_number']);
$locationStatement->execute();

echo '<div class="list-group">';
echo '<div class="list-group-item active">Player: (x, y)</div>';
foreach ($locationStatement->fetchAll() as $locationRow) {
		echo '<li class="list-group-item">' . $locationRow["username"] . ': (' . $locationRow['x_pos'] . ', ' . $locationRow['y_pos'] .  ')</li>';
}
echo '</div>';



?>




</div>


<?php
require './include/bootstrapFooter.php';
?>
</body>

</html>
