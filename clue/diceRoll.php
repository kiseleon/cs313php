<?php

// This php script will return two dice in the form of html that displays the image of the dice.
// (All you have to do is insert the returned code into your document)

// Roll two dice.

for ($i = 0; $i < 2; $i++) {
	$roll = mt_rand(1, 6);

	echo '<img src="./img/dieWhite_border' . $roll . '.png" />';
}
?>