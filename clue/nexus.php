<?php 

require dirname( __FILE__ ) . './include/clueDbHeader.php'; 
session_start();

// make sure you're signed in, otherwise get booted.
if (!isset($_SESSION["userid"])) {
	header ( 'Location:/clue/login.php');
}

?>

<!DOCTYPE html>

<html>

<head>
<?php
require dirname( __FILE__ ) . './include/bootstrapHeader.php';
?>

<script>

function playGame(game_number) {

	window.location="/clue/play.php?game_number=" + game_number;
}

function viewBoard(game_number) {
	window.location="/clue/board.php?game_number=" + game_number;
}


</script>


<title>Clue - Nexus</title>
</head>

<body>
<div class="container">
<h1>Nexus</h1>
<?php
// Choose a game
if ($status === true) {
	try {

	// Query for the list of games the user is in
	$query = "SELECT game_number, username FROM games g JOIN players p ON g.player_id=p.id WHERE p.id=:userid";
	
	$statement = $db->prepare($query);
	$statement->bindValue(':userid', $_SESSION['userid']);
	$statement->execute();

	foreach ($statement->fetchAll() as $row) {
		//echo $row['game_number'] . '<br />';
		echo '<div class="list-group">';
		echo '<li class="list-group-item active">Game #' . $row['game_number'] . ':</li>';

		$playerQuery = 'SELECT username FROM players p JOIN games g ON p.id=g.player_id WHERE g.game_number=' .
			$row['game_number'] . ' AND p.id=g.player_id;';
		$playerStatement = $db->prepare($playerQuery);
		$playerStatement->execute();

		foreach ($playerStatement->fetchAll() as $playerRow) {
			echo '<li class="list-group-item">' . $playerRow['username'] . "</li>\n";
		}

		echo '<button class="list-group-item btn-block list-group-item-success" onClick="playGame(' . $row['game_number'] . ')" >Play Game #' . $row['game_number'] . '</button>' . "\n";
		echo '<button class="list-group-item btn-block list-group-item-info" onClick="viewBoard(' . $row['game_number'] . ')" >View Board for Game #' . $row['game_number'] . '</button>' . "\n";
		echo '</div>';
	}

	} catch (PDOEXCEPTION $ex) {
		echo "Something bad happened, details are: " . $ex;
	}

} else {
	echo '<h1>Database connection failed!</h1>' . "\n";
}



?>
<a href="/clue/clearSession.php" class="btn btn-lg btn-primary">Log out</a>
</div>

<?php
require dirname( __FILE__ ) . './include/bootstrapFooter.php';
?>
</body>

</html>