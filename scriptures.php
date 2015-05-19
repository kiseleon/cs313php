<?php

$openShiftVar = getenv('OPENSHIFT_MYSQL_DB_HOST');

// set environment specific variables
if ($openShiftVar === null || $openShiftVar == "") {
	// Not in openshift
	$dbHost = "localhost";
	$dbUser = "php";
	$dbpassword = "password"
} else {
	$dbHost = getenv('OPENSHIFT_MYSQL_DB_HOST');
	$dbUser = getenv('OPENSHIFT_MYSQL_DB_USERNAME');
	$dbPassword = getenv('OPENSHIFT_MYSQL_DB_PASSWORD');
}

$myDB = 'scriptures';
$status = false;

try {
    $conn = new PDO("mysql:host=$dbHost;dbname=$myDB", $dbUser, $dbPassword);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $status = true;
    }
catch(PDOException $e)
    {
    //echo "Connection failed: " . $e->getMessage();
    }
?>

<!DOCTYPE html>
<html>
<head>
<title>Scripture Resources</title>
</head>


<body>


<h1>Scripture Resources</h1>
<?php
if ($status == true) {
	$sql = 'SELECT * FROM scriptures';

	if (isset($_POST["book"])) {
		$sql = $sql . ' WHERE book="' . $_POST["book"] . '"';
	}

	$result = $conn->query($sql);

	// output data of each row
	while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
		echo '<strong>' . $row["book"] . ' ' . $row["chapter"] . ':' .
			$row["verse"] . '</strong> - "' . $row["content"] . '"<br />' . "\n";
	} 

} else {
	echo '<h3>Database connection failed!</h3>' . "\n";
}


?>


</body>

</html>
