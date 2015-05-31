<?php 

require './include/clueDbHeader.php'; 
session_start();

?>

<!DOCTYPE html>

<html>

<head>
<?php
require './include/bootstrapHeader.php';
?>

<script>
function removeGame(game_number) {
	// confirm dialog
	if (confirm("Remove game number " + game_number + "?\nThis cannot be undone.") == true) {

		// redirect to the deletion page, which will redirect back to here when it is done
		window.location = "/clue/removeGame.php?gamenumber=" + game_number;
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
		echo '<div class="list-group-item active">Game number ' . $row['game_number'] . ': </div>';

		$playerQuery = 'SELECT username, name, color FROM players p ' .
			'JOIN games g ON p.id=g.player_id ' .
			'JOIN suspect s ON g.player_character=s.id ' .
			'WHERE g.game_number=' . $row['game_number'] . ' ' .
			'AND p.id=g.player_id ' . 
			'ORDER BY username';
		$playerStatement = $db->prepare($playerQuery);
		$playerStatement->execute();

		foreach ($playerStatement->fetchAll() as $playerRow) {
			$isWhite = "";
			if ($playerRow["color"] == "F8F8FF") {
				$isWhite = "text-shadow: 0 0 6px #000000, 0 0 1px #000000, 0 0 3px #000000;";
			}


			echo '<li class="list-group-item">' . $playerRow['username'] . 
			 '<p style="' . $isWhite . 'font-weight: bold; color:#' . $playerRow["color"] . '" >' . $playerRow["name"] . '</p>' . "</li>\n";
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
<a href="/clue/clue.php" class="btn btn-lg btn-success">Back</a>
<a href="/clue/clue.php" class="btn btn-lg btn-warning">Home</a>
</div>
<?php
require './include/bootstrapFooter.php';
?>

</body>

</html>
