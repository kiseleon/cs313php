<?php 

require dirname( __FILE__ ) . './include/clueDbHeader.php'; 
session_start();

?>

<!DOCTYPE html>

<html>

<head>
<?php
require dirname( __FILE__ ) . './include/bootstrapHeader.php';
?>

<script>
function removeGame(game_number) {
	// confirm dialog
	if (confirm("Remove game number " + game_number + "?\nThis cannot be undone.") == true) {

		// actually do the delete here

		// reload the page so the php script runs again
		window.location = "/clue/manageGames.php";
	}
}

</script>

<title>Clue - Game Management</title>
</head>

<body>
<div class="container">
<h1>Manage Games <a href="/clue/newGame.php" class="btn btn-lg btn-success">Create Game</a></h1>

<?php

if ($status === true) {
	try {

	$query = 'SELECT game_number FROM games GROUP BY game_number';

	$statement = $db->prepare($query);
	// $statement->bindValue(':name', $actor);
	$statement->execute();

	foreach ($statement->fetchAll() as $row) {
		//echo $row['game_number'] . '<br />';

		echo '<div class="list-group">';
		echo '<div class="list-group-item list-group-item-success">Game number ' . $row['game_number'] . ': </div>';

		$playerQuery = 'SELECT username FROM players p JOIN games g ON p.id=g.player_id WHERE g.game_number=' .
			$row['game_number'] . ' AND p.id=g.player_id;';
		$playerStatement = $db->prepare($playerQuery);
		$playerStatement->execute();

		foreach ($playerStatement->fetchAll() as $playerRow) {
			echo '<li class="list-group-item">' . $playerRow['username'] . "</li>\n";
		}
		echo '<button class="btn btn-block list-group-item-danger list-group-item" onClick="removeGame(' . $row['game_number'] . ')" >Remove Game #' . $row['game_number'] . '</button>' . "\n";
		echo "</div>\n";
	}

	} catch (PDOEXCEPTION $ex) {
		echo "Something bad happened, details are: " . $ex;
	}


} else {
	echo '<h1>Database connection failed!</h1>' . "\n";
}


?>

</div>
<?php
require dirname( __FILE__ ) . './include/bootstrapFooter.php';
?>

</body>

</html>