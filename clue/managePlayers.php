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
<title>Clue - Player Management</title>
<script>
function removePlayer(id_number, username) {
	// confirm dialog
	if (confirm("Remove player " + username + "?\nThis cannot be undone.") == true) {

		// actually do the delete here

		// reload the page so the php script runs again
		window.location = "/clue/managePlayers.php";
	}
}

</script>

</head>

<body>
<div class="container">
<h1>Manage Players <a href="/clue/newPlayer.php" class="btn btn-lg btn-success">Add New Player</a></h1>

<?php

if ($status === true) {
	try {

	// Query for the list of users
	$query = "SELECT id, username FROM players ORDER BY username";
	
	$statement = $db->prepare($query);
	$statement->execute();

	foreach ($statement->fetchAll() as $row) {
		echo '<div class="list-group">';
		echo '<li class="list-group-item active">' . $row['username'] . "</li>\n";

		// get the list of games they are currently in
		$gameQuery = "SELECT game_number FROM games g JOIN players p ON g.player_id=p.id WHERE p.id=:id ORDER BY game_number";
		$gameStatement = $db->prepare($gameQuery);
		$gameStatement->bindValue(':id', $row['id']);

		$gameStatement->execute();

		foreach ($gameStatement->fetchAll() as $gameRow) {
			echo '<li class="list-group-item" >Game #' . $gameRow["game_number"] . '</li>';
		}


		echo '<button class="btn btn-block list-group-item list-group-item-danger" onClick="removePlayer(\'' . $row['id'] . '\', \'' . $row['username'] . '\')" >Delete ' . $row['username'] . '</button>' . "\n";
		echo '</div>';	
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
require './include/bootstrapFooter.php';
?>
</body>

</html>
