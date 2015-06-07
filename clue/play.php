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
<title>Clue - Play</title>
<link href="/css/clue-play.css" type="text/css" rel="stylesheet" />
</head>

<body>

<div class="container">
	<div class="container" maxwidth="160px"> <!-- For the modal stuff -->
<?php
// Generate the list of cards: First Rooms, then Suspects, then Weapons
$roomQuery = "SELECT r.id, r.name, r.picture FROM room r " . 
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
	echo '<ul class="row">';
	foreach ($roomStatement->fetchAll() as $roomRow) {
		echo '<li class="col-lg-2 col-md-2 col-sm-3 col-xs-4 img-responsive"><img src="' . $roomRow["picture"] . '" /></li>';
	}
}

$suspectQuery = "SELECT s.id, s.name, s.picture FROM suspect s " . 
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
	foreach ($suspectStatement->fetchAll() as $suspectRow) {
		echo '<li class="col-lg-2 col-md-2 col-sm-3 col-xs-4 img-responsive"><img src="' . $suspectRow["picture"] . '" /></li>';
	}
}

$weaponQuery = "SELECT w.id, w.name, w.picture FROM weapon w " . 
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
	foreach ($weaponStatement->fetchAll() as $weaponRow) {
		echo '<li class="col-lg-2 col-md-2 col-sm-3 col-xs-4 img-responsive"><img src="' . $weaponRow["picture"] . '" /></li>';
	}
}
echo '</ul></div>';
?>




<?php

echo '<form name="cluesheet" action="updateCluesheet.php?game_number=' . $_GET['game_number'] .'" method="POST">';

// get the current game id for the form
$query = "SELECT g.id FROM games g JOIN players p ON g.player_id=p.id WHERE p.id=:userid AND g.game_number=:game_number";
$statement = $db->prepare($query);
$statement->bindValue(':userid', $_SESSION['userid']);
$statement->bindValue(':game_number', $_GET['game_number']);
$statement->execute();

$game_id = "";

foreach ($statement->fetchAll() as $row) {
	$game_id = $row["id"];
}

echo '<input type="hidden" name="game_id" value="' . $game_id . '" />';

// Get the current guessing information from the database
$statusList = array();
$statusList[] = "yes";
$statusList[] = "maybe";
$statusList[] = "no";
$statusList[] = "";

echo '<table class="table">' . "\n";

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
	echo '<tr class="active">' . "\n";
	echo '<th>Rooms:</th>' . "\n";
	echo "<th>Yes</th>\n";
	echo "<th>Maybe</th>\n";
	echo "<th>No</th>\n";
	echo "</tr>\n";
	foreach ($roomStatement->fetchAll() as $roomRow) {
		echo '<tr>' . "\n";
		echo '<td>' . $roomRow["name"] . ': </td>';
		foreach ($statusList as $checkedStatus) {
			$check = "";
			if ($checkedStatus === $roomRow["status"]) {
				$check = "checked ";
			}
			if ($checkedStatus === '') {
				$check = $check . 'style="display:none" ';
			}
			echo '<td><input type="radio" name="' . str_replace(' ', '_', $roomRow["name"]) . '" value="' . $checkedStatus . '" ' .
				$check . " /> </td> \n";
		}
		echo "</tr>\n";
	}
	
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
	echo '<tr class="active">' . "\n";
	echo '<th>Suspects:</th>' . "\n";
	echo "<th>Yes</th>\n";
	echo "<th>Maybe</th>\n";
	echo "<th>No</th>\n";
	echo "</tr>\n";

	foreach ($suspectStatement->fetchAll() as $suspectRow) {
		echo "<tr>\n";
		echo '<td>' . $suspectRow["name"] . ':</td>';
		foreach ($statusList as $checkedStatus) {
			$check = "";
			if ($checkedStatus === $suspectRow["status"]) {
				$check = "checked ";
			}
			if ($checkedStatus === '') {
				$check = $check . 'style="display:none" ';
			}
			echo '<td><input type="radio" name="' . str_replace(' ', '_', $suspectRow["name"]) . '" value="' . $checkedStatus . '" ' .
				$check . " /></td>\n";
		}
		echo "</tr>\n";
	}
	
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
	echo '<tr class="active">' . "\n";
	echo '<th>Weapons:</th>' . "\n";
	echo "<th>Yes</th>\n";
	echo "<th>Maybe</th>\n";
	echo "<th>No</th>\n";
	echo "</tr>\n";
	foreach ($weaponStatement->fetchAll() as $weaponRow) {
		echo "<tr>\n";
		echo '<td>' . $weaponRow["name"] . ':</td>';
		foreach ($statusList as $checkedStatus) {
			$check = "";
			if ($checkedStatus === $weaponRow["status"]) {
				$check = "checked ";
			}
			if ($checkedStatus === '') {
				$check = $check . 'style="display:none" ';
			}
			echo '<td><input type="radio" name="' . str_replace(' ', '_', $weaponRow["name"]) . '" value="' . $checkedStatus . '" ' .
				$check . " /> </td>  \n";
		}
		echo "</tr>\n";
	}
	
}
echo "</table>\n";

?>
<input class="btn btn-lg btn-primary" type="submit" value="Save Changes" />
<?php
echo '<a href="/clue/accuse.php?game_number=' . $_GET["game_number"] . '" class="btn btn-lg btn-danger">Make Accusation</a>';
?>
</form>
<br />
<a href="/clue/nexus.php" class="btn btn-lg btn-success">Back</a>
<a href="/clue/clue.php" class="btn btn-lg btn-warning">Home</a>
</div>

<!-- This is the code for the fading stuff -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">         
        	<div class="modal-body">                
          	</div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<?php
require './include/bootstrapFooter.php';
?>
<script>
$(document).ready(function(){
    $('li img').on('click',function(){
        var src = $(this).attr('src');
        var img = '<img src="' + src + '" class="img-responsive"/>';
        $('#myModal').modal();
        $('#myModal').on('shown.bs.modal', function(){
        	$('#myModal .modal-body').html(img);
        });
        $('#myModal').on('hidden.bs.modal', function(){
            $('#myModal .modal-body').html('');
        });
    });  
})
</script>
</body>

</html>
