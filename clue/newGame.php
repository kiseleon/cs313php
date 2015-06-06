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
<link href="/css/signin.css" rel="stylesheet" />
<title>Clue - Create Game</title>

<script>
function toggle(player_id) {
	var dom = document.getElementById("check" + player_id);
	var option = document.getElementById("option" + player_id);
	if (dom.checked === true) {
		option.removeAttribute("disabled");
	} else {
		option.setAttribute("disabled", true);
	}

}

</script>

</head>

<body>

<div class="container" >

<form name="login" method="POST" action="createGame.php" class="form-signin">
<h2 class="form-signin-heading">Please select players</h2>
<?php


// Check if there was an failed attempt
if (isset($_GET['valid'])) {
	echo '<div class="alert alert-danger alert-dismissible" role="alert">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>There can be only one of each character in a game.</div>' . "\n";
}



///////////////////////////////////////////////////////////////////////////////////////////

// Create the checkbox list


echo '<div class="list-group" >';

// get the list of players from the db
$listQuery = "SELECT id, username FROM players ORDER BY username";
$listStatement = $db->prepare($listQuery);
$listStatement->execute();



foreach($listStatement->fetchAll() as $listRow) {
	echo '<div class="list-group-item">' . 
	'<input type="checkbox" id="check' . $listRow["id"] . '" name="userid[]" value="' . $listRow["id"] . 
	'" onClick="toggle(' . $listRow["id"] . ')" />' . $listRow["username"] .
	'<select  name="character[]" id="option' . $listRow["id"] . '" disabled required>';

	$suspectQuery = "SELECT id, name FROM suspect ORDER BY name";
	$suspectStatement = $db->prepare($suspectQuery);
	$suspectStatement->execute();

	foreach($suspectStatement->fetchAll() as $suspectRow) {
		echo '<option value="' . $suspectRow['id'] . '" >' . $suspectRow['name'] . "</option>\n";
	}

	echo '</select></div>';
}

echo '</div>';
?>

<input class="btn btn-block btn-primary" type="submit" value="Create Game With Specified Players" />	
<a href="/clue/manageGames.php" class="btn btn-lg btn-success">Back</a>
<a href="/clue/clue.php" class="btn btn-lg btn-warning">Home</a>
</form>

</div>
<?php
require './include/bootstrapFooter.php';
?>

</body>

</html>
