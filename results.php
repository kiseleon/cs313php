<?php
	// Start the session
	session_start();

	$filename = 'results.txt';

	// If they haven't voted and they submitted the form
	if (!isset($_SESSION['voted']) && isset($_POST['where'])) {
		
		// to determine if you need a newline to start, since ftell() is broken with a+
		$extraline = false;
		if (file_exists($filename)) {
			$extraline = true;
		}

		// open the file [a+ means to open for writing at the end of the file, creating a file if it doesn't exist]
		$fileToWrite = fopen($filename, "a+b") or die("Unable to open file for writing!");
		
		if ($extraline) {
			fwrite($fileToWrite, "\n");
		}
		

		// write the data to the file in this order:
		// where, time, number, thoughts, problem, ideas
		fwrite($fileToWrite, htmlspecialchars($_POST['where']) . "\n");
		fwrite($fileToWrite, htmlspecialchars($_POST['time']) . "\n");
		fwrite($fileToWrite, htmlspecialchars($_POST['number']) . "\n");
		fwrite($fileToWrite, htmlspecialchars($_POST['thoughts']) . "\n");
		fwrite($fileToWrite, htmlspecialchars($_POST['problem']) . "\n");
		fwrite($fileToWrite, htmlspecialchars($_POST['ideas']));
		
		// close the file
		fclose($fileToWrite);
		
		// set voted session variable so they can't vote again - enable once done testing file
		$_SESSION['voted'] = true;
	}

	$hasdata = false;	

	if (file_exists($filename)) {
		$hasdata = true;

		// read the file and parse the data into sections
		$where    = array();
		$time     = array();
		$number   = array();
		$thoughts = array();
		$problem  = array();
		$ideas    = array();

		$reader = fopen($filename, "rb") or die("Unable to open file for reading!");

		while (!feof($reader)) { // there is a line
			$where[]    = fgets($reader);
			$time[]     = fgets($reader);
			$number[]   = fgets($reader);
			$thoughts[] = fgets($reader);
			$problem[]  = fgets($reader);
			$ideas[]    = fgets($reader);
		}

		fclose($reader);
	}	

?>
<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->

    <!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="js/ie10-viewport-bug-workaround.js"></script>
    
    <style>
    	.centered {
    		text-align: centered;
    		margin-left: 40px;
    		margin-right: 40px;
    		margin-top: 40px;
    		margin-bottom: 40px;
    		min-width: 500px;
    		max-width: 800px;
    	}
    </style>
	<title>Survey Results</title>
</head>

<body>
<div class="centered">
<?php

if (!$hasdata) {
	echo '<div class="alert alert-danger" role="alert"><strong>Error:</strong> No data to display!</div>' . "\n";
} else {
	// WHERE
	echo '<div class="panel panel-primary">' . "\n";
	echo '<div class="panel-heading">Where do you park on campus?</div>' . "\n";
	echo '<div class="panel-body">' . "\n";
	echo '<ul class="list-group">' . "\n";
	// loop through and display as a list
	foreach ($where as $key => $value) {
		echo '<li class="list-group-item">' . $value . '</li>' . "\n";
	}
	echo '</ul></div>';
	echo '</div> <!-- where -->';

	// TIME

	echo '<div class="panel panel-primary">' . "\n";
	echo '<div class="panel-heading">At what time do you park on campus?</div>' . "\n";
	echo '<div class="panel-body">' . "\n";
	echo '<ul class="list-group">' . "\n";
	// loop through and display as a list
	foreach ($time as $key => $value) {
		echo '<li class="list-group-item">' . $value . '</li>' . "\n";
	}
	echo '</ul></div>';
	echo '</div> <!-- time -->';


	// NUMBER

	echo '<div class="panel panel-primary">' . "\n";
	echo '<div class="panel-heading">Do you park on campus more than once a day?</div>' . "\n";
	echo '<div class="panel-body">' . "\n";
	echo '<ul class="list-group">' . "\n";

	$once = 0;
	$twice = 0;
	$thrice = 0;
	$many = 0;
	foreach ($number as $key => $value) {
		if (strcmp($value, "No\n") == 0)
			$once++;
		else if (strcmp($value, "2 times per day\n") == 0)
			$twice++;
		else if (strcmp($value, "3 times per day\n") == 0)
			$thrice++;
		else if (strcmp($value, "4+ times per day\n") == 0)
			$many++;
	}

	echo '<ul class="list-group">' . "\n";

	// do calculations
	$total = $once + $twice + $thrice + $many;
	$onepercent = round($once / $total * 100.0, 2);
	$twopercent = round($twice / $total * 100.0, 2);
	$threepercent = round($thrice / $total * 100.0, 2);
	$manypercent = round($many / $total * 100.0, 2);

	// make the progress bar(s)
	echo '<div class="progress">' . "\n";
	
	echo '<div class="progress-bar progress-bar-info" style="width: ' . $onepercent . '%">' . "\n";
	echo '<span class="sr-only">' . $onepercent . '% Complete </span>' . "\n";
	echo '</div>' . "\n";

	echo '<div class="progress-bar progress-bar-warning" style="width: ' . $twopercent . '%">' . "\n";
	echo '<span class="sr-only">' . $twopercent . '% Complete </span>' . "\n";
	echo '</div>' . "\n";

	echo '<div class="progress-bar progress-bar-success" style="width: ' . $threepercent . '%">' . "\n";
	echo '<span class="sr-only">' . $threepercent . '% Complete </span>' . "\n";
	echo '</div>' . "\n";

	echo '<div class="progress-bar progress-bar-danger" style="width: ' . $manypercent . '%">' . "\n";
	echo '<span class="sr-only">' . $manypercent . '% Complete </span>' . "\n";
	echo '</div>' . "\n";

	echo '</div> <!-- progress -->' . "\n";

	echo '<li class="list-group-item">';
	echo '<div class="progress">' . "\n";
	echo '<div class="progress-bar progress-bar-info" style="width: ' . $onepercent . '%">' . "\n";
	echo '<span class="sr-only">' . $onepercent . '% Complete </span>' . "\n";
	echo '</div>' . "\n";
	echo '</div> <!-- progress -->' . "\n";
	echo  "Once per day - $once (" . $onepercent . "%)</li>";

	echo '<li class="list-group-item">';
	echo '<div class="progress">' . "\n";
	echo '<div class="progress-bar progress-bar-warning" style="width: ' . $twopercent . '%">' . "\n";
	echo '<span class="sr-only">' . $twopercent . '% Complete </span>' . "\n";
	echo '</div>' . "\n";
	echo '</div> <!-- progress -->' . "\n";
	echo  "Twice per day - $twice (" . $twopercent . "%)</li>";

	echo '<li class="list-group-item">';
	echo '<div class="progress">' . "\n";
	echo '<div class="progress-bar progress-bar-success" style="width: ' . $threepercent . '%">' . "\n";
	echo '<span class="sr-only">' . $threepercent . '% Complete </span>' . "\n";
	echo '</div>' . "\n";
	echo '</div> <!-- progress -->' . "\n";
	echo  "Three times per day - $thrice (" . $threepercent . "%)</li>";

	echo '<li class="list-group-item">';
	echo '<div class="progress">' . "\n";
	echo '<div class="progress-bar progress-bar-danger" style="width: ' . $manypercent . '%">' . "\n";
	echo '<span class="sr-only">' . $manypercent . '% Complete </span>' . "\n";
	echo '</div>' . "\n";
	echo '</div> <!-- progress -->' . "\n";
	echo  "Four+ times per day - $many (" . $manypercent . "%)</li>";

	echo '</ul></div>';
	echo '</div> <!-- number -->';


	// THOUGHTS
	echo '<div class="panel panel-primary">' . "\n";
	echo '<div class="panel-heading">Would you use a system that shows you where available parking spots are?</div>' . "\n";
	echo '<div class="panel-body">' . "\n";
	echo '<ul class="list-group">' . "\n";
	// loop through and display as a list
	foreach ($thoughts as $key => $value) {
		echo '<li class="list-group-item">' . $value . '</li>' . "\n";
	}
	echo '</ul></div>';
	echo '</div> <!-- thoughts -->';

	// PROBLEMS
	echo '<div class="panel panel-primary">' . "\n";
	echo '<div class="panel-heading">Do you see or have a problem with parking here on campus?</div>' . "\n";
	echo '<div class="panel-body">' . "\n";
	echo '<ul class="list-group">' . "\n";

	// loop through and display as a list
	foreach ($problem as $key => $value) {
		echo '<li class="list-group-item">' . $value . '</li>' . "\n";
	}
	echo '</ul></div>';
	echo '</div> <!-- problems -->';

	// IDEAS
	echo '<div class="panel panel-primary">' . "\n";
	echo '<div class="panel-heading">Is there anything useful that you think might help with the parking situation?</div>' . "\n";
	echo '<div class="panel-body">' . "\n";
	echo '<ul class="list-group">' . "\n";

	// loop through and display as a list
	foreach ($ideas as $key => $value) {
		echo '<li class="list-group-item">' . $value . '</li>' . "\n";
	}
	echo '</ul></div>';
	echo '</div> <!-- ideas -->';
}
?>
</div> <!-- centered -->
</body>

</html>