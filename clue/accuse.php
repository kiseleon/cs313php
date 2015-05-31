<?php 

require './include/clueDbHeader.php'; 
session_start();

// make sure you're signed in, otherwise get booted.
if (!isset($_SESSION["userid"]) || !isset($_GET["game_number"])) {
	header ( 'Location:/clue/nexus.php');
}

// get the lists of rooms, suspects, and weapons

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

?>

<!DOCTYPE html>

<html>

<head>
<?php
require './include/bootstrapHeader.php';
?>


<title>Clue - Final Accusation</title>
</head>

<body>
<div class="container">
<h1>Make an Accusation</h1>
<div class="table-responsive">
<?php
echo '<form name="accuse" method="POST" action="/clue/makeAccusation.php?game_number=' . $_GET["game_number"] . '">';
?>
<table class="table">
<thead>
	<tr>
		<th>Room:</th>
		<th>Suspect:</th>
		<th>Weapon:</th>
	</tr>
</thead>
<tbody>
	<tr>
<?php
echo '<td><div class="form-group"><select required name="room" class="form-control">' . "\n";

foreach ($roomList as $id => $room) {
	echo '<option value="' . $id . '">' . $room . '</option>' . "\n";
}
echo '</div></td>';

echo '<td><div class="form-group"><select required name="suspect" class="form-control">' . "\n";

foreach ($suspectList as $id => $suspect) {
	echo '<option value="' . $id . '">' . $suspect . '</option>' . "\n";
}
echo '</div></td>';

echo '<td><div class="form-group"><select required name="weapon" class="form-control">' . "\n";

foreach ($weaponList as $id => $weapon) {
	echo '<option value="' . $id . '">' . $weapon . '</option>' . "\n";
}
echo '</div></td>';

?>
	</tr>
</tbody>
</table>

<input type="submit" class="btn btn-lg btn-danger" value="I accuse ... !" />
</form>
</div>
<?php 
// make the back button go back to the current game
echo '<a class="btn btn-lg btn-success" href="/clue/play.php?game_number=' . 
		$_GET["game_number"] . '">Back</a>' . "\n";
?>
<a href="/clue/clue.php" class="btn btn-lg btn-warning">Home</a>
</div>
<?php
require './include/bootstrapFooter.php';
?>

</body>

</html>
