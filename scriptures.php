<?php
$servername = "localhost";
$username = "php";
$password = "password";
$myDB = 'scriptures';
$status = false;

try {
    $conn = new PDO("mysql:host=$servername;dbname=$myDB", $username, $password);
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
