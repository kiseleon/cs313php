<?php
// Start the session; this has to be FIRST.
session_start();

// check if the user has already submitted a survey and redirect if necessary
if (isset($_SESSION['voted'])) {
	header( 'Location:/results.php' );
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

<title>PHP Survey</title>
</head>

<body>
<div class="centered">
<form id="survey" action="results.php" method="POST">
<div class="form-group">

	<div class="panel panel-primary">
		<div class="panel-heading">Where do you park on campus?</div>
		<div class="panel-body"><input type="text" name="where" required class="form-control" /></div>
	</div>
	<div class="panel panel-primary">
		<div class="panel-heading">At what time do you park on campus?</div>
		<div class="panel-body"><input type="text" name="time" required class="form-control" /></div>
	</div>
	</div>
	<div class="panel panel-primary">
		<div class="panel-heading">Do you park on campus more than once a day?</div>
		<div class="panel-body">
			<ul class="list-group">
			<li class="list-group-item"><input type="radio" name="number" value="No" required />No</li>
			<li class="list-group-item"><input type="radio" name="number" value="2 times per day" required />2 times per day</li>
			<li class="list-group-item"><input type="radio" name="number" value="3 times per day" required />3 times per day</li>
			<li class="list-group-item"><input type="radio" name="number" value="4+ times per day" required />4+ times per day</li>
			</ul> <!-- list-group --> 
		</div>
	</div>
	<div class="panel panel-primary">
		<div class="panel-heading">Would you use a system that shows you where available parking spots are?</div>
		<div class="panel-body"><input type="text" name="thoughts" required class="form-control" /></div>
	</div>
	<div class="panel panel-primary">
		<div class="panel-heading">Do you see or have a problem with parking here on campus?</div>
		<div class="panel-body"><input type="text" name="problem" required class="form-control" /></div>
	</div>
	<div class="panel panel-primary">
		<div class="panel-heading">Is there anything useful that you think might help with the parking situation?</div>
		<div class="panel-body"><input type="text" name="ideas" required class="form-control" /></div>
	</div>
</div> <!-- form-group -->
<div class="centered">
<input type="submit" value="Submit Answers" class="btn btn-lg btn-success" />
<input type="reset" value="Reset Answers" class="btn btn-lg btn-danger" />
<a href="results.php"><button class="btn btn-lg btn-info">Go to Results</button></a>
</div>
</form>
</div> <!-- centered -->
</body>
</html>