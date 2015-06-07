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

// Offsets and size for grid
$yoff = 2;
$xoff = 25;
$gridsize = 43;


// Make a class for holding the color, xpos, and ypos
class Piece {
	private $name  = 0; // name of character
	private $color = 0; // HTML color code
	private $xpos  = 0; // x position
	private $ypos  = 0; // y position 

	public function setVars ($n, $c, $x, $y) {
		$this->name = str_replace(" ", "_", $n);
		$this->color = $c;
		$this->xpos  = $x;
		$this->ypos  = $y;
	}

	public function getName() {
		return $this->name;
	}
	public function getColor() {
		return $this->color;
	}
	public function getX() {
		return $this->xpos;
	}
	public function getY() {
		return $this->ypos;
	}
}

// Get the location data for all players in the game
$query = "SELECT s.name, s.color, l.x_pos, l.y_pos FROM location l " .
		"JOIN games g ON l.game_id=g.id " .
		"JOIN suspect s ON g.player_character=s.id " .
		"WHERE g.game_number=:game_number";
$statement = $db->prepare($query);
$statement->bindValue(':game_number', $_GET['game_number']);
$statement->execute();

$players = array();

// save them into the players array as Piece objects
foreach ($statement->fetchAll() as $row) {
	$instance = new Piece();
	$instance->setVars($row["name"], $row['color'], $row['x_pos'], $row['y_pos']);
	$players[] = $instance;
}

//var_dump($players);

// generate CSS styles for the locations
if (count($players) > 0) {
	echo '<style>' . "\n";
	//generate CSS styles for the locations
	foreach ($players as $player) {
		echo "#circle-" . $player->getName() . " {" . "\n";
		
		echo "\t" . 'width: ' . $gridsize . "px;" . "\n"; 
		echo "\t" . 'height: ' . $gridsize . "px;" . "\n"; 
		echo "\t" . 'background: #' . $player->getColor() . ";" . "\n"; 
		echo "\t" . '-moz-border-radius: 50%;' . "\n";
		echo "\t" . '-webkit-border-radius: 50%;' . "\n";
		echo "\t" . 'border-radius: 50%;' . "\n";
		echo "\t" . 'position: absolute;' . "\n";

		$x = ($player->getX() * $gridsize) + $xoff;
		$y = ($player->getY() * $gridsize) + $yoff;

		echo "\t" . 'left: ' . $x . "px;" . "\n";
		echo "\t" . 'top: ' . $y . "px;" . "\n";
		echo "\t" . 'z-index: 200;' . "\n";
		echo "\t" . 'pointer-events: none;' . "\n";

		echo "}\n";
	}
	echo "</style>\n";
}



/* base circle info for reference
#circle { // this is the id... maybe make it a class by changing it to .circle-scarlet, .circle-green?
			// OR keep it as an id and use the suspect name (with _'s) as the id
			// These should be generated here, but used AFTER the 
	width: 43px; // this is the grid size
	height: 43px; // this is the grid size
	background: red; // this is the player color
	-moz-border-radius: 50%; // for making it a circle
	-webkit-border-radius: 50%; // for making it a circle
	border-radius: 50%; // for making it a circle
	position: relative; // for making it line up with the image
	left: 00px; // the distance from the left edge to the edge of the circle
	top: 00px; // the distance from the top edge to the top of the circle
}
*/

?>
<title>Clue - Board</title>

<script> 
var selectedX = null;
var selectedY = null;
var yoff = <?php echo $yoff ?>;
var xoff = <?php echo $xoff ?>;
var gridsize = <?php echo $gridsize ?>;
var game_number = <?php echo htmlspecialchars($_GET["game_number"]) ?>;


function selectSpot (xpos, ypos) {
	// if this is the first click
	if (selectedX === null || selectedY === null) {
		selectedX = xpos;
		selectedY = ypos;
		markSelected(xpos, ypos);
	}

	// if this is the second click
	else {
		// check if they are the same --> unselect
		if (selectedX == xpos && selectedY == ypos) {
			selectedX = null;
			selectedY = null;
			unmarkSelected(xpos, ypos);
		}
		// else, make the actual function call
		else {
			// make actual swap request to the server
			window.location='/clue/swap.php?game_number=' + game_number + '&x1=' + selectedX  + '&y1=' + selectedY + '&x2=' + xpos + '&y2=' + ypos;
		}
	}

}

function markSelected(xpos, ypos) {
	// this will select the bounds of the imagemap square so you can tell it has been selected
	//alert("Marking (" + xpos + ", " + ypos + ") as selected!");
	var dom = document.getElementById('selector');

	//dom.setAttribute('style', "left: " + getXPosition(xpos) + "; top: " + getYPosition(ypos));
	
	dom.style.left = getXPosition(xpos) + 'px';
	dom.style.top  = getYPosition(ypos) + 'px'; 
	dom.style.visibility = "";
	console.log('style', "left: " + getXPosition(xpos) + "; top: " + getYPosition(ypos) + ";");
}	

function unmarkSelected(xpos, ypos) {
	// this is used for removing the bounds of the square that indicate selection
	// don't bother setting them to anything specific, just make it invisible
	//alert("Unmarking (" + xpos + ", " + ypos + ") as selected!");
	var dom = document.getElementById('selector');
	dom.style.visibility = 'hidden';
}

// Get location in pixels instead of grid notation
function getXPosition(gridX) {
	return gridX * gridsize + xoff;
}

// Get location in pixels instead of grid notation
function getYPosition(gridY) {
	return gridY * gridsize + yoff;
}
</script>

<style>

.selector {
	position: absolute;
	width: 43px;
	height: 43px;
	background: blue;
	opacity: .4;
	pointer-events: none;
	z-index: 250;
}


</style>

</head>

<body>

<div>


<!-- The actual image for the board -->
<img src="./img/board.png" usemap="#clueMap" />


<?php

// make the map
echo '<map name="clueMap">' . "\n";
// Offsets
$yoff = 2;
$xoff = 25;
$gridsize = 43;
for ($y = 0; $y < 25; $y++) {
	echo '<!-- ROW ' . $y . ' -->' . "\n";
	for ($x = 0; $x < 24; $x++) {
		$xstart = $x * $gridsize + $xoff;
		$ystart = $y * $gridsize + $yoff;
		echo '<area shape="rect" coords="' . $xstart . ',' . $ystart . ',' . ($xstart + $gridsize) . ',' . ($ystart + $gridsize) . '"' . 
		' title="' . "$x, $y" . '" onClick="selectSpot(\'' . $x . "', '" . $y . '\')" />' . "\n";
	}
}

echo '</map>' . "\n";

// mark the player locations on the grid



?>

<!-- Make the player circles! -->
<?php

// for each player, make a circle!
foreach ($players as $player) {
	//make the div element
	echo '<div id="circle-' . $player->getName() . '"></div>' . "\n";
}


?>
<div id="selector" class="selector" style="visibility:hidden; top: 0px; left: 0px;"></div>
</div>


<button class="btn btn-lg btn-primary" id="rollDiceBtn">Roll Dice!</button>
<div id="dice"></div>
<br /><br />
<a href="/clue/nexus.php" class="btn btn-lg btn-success">Back</a>
<a href="/clue/clue.php" class="btn btn-lg btn-warning">Home</a>

<?php
require './include/bootstrapFooter.php';
?>

<!-- This script uses JQuery, so it has to come after the footer where JQuery is linked -->
<script>
$(document).ready(function(){
    $("#rollDiceBtn").click(function(){
        $.ajax({url: "./diceRoll.php", success: function(result){
            $("#dice").html(result);
        }});
    });
});
</script>
</body>

</html>
