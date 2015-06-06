<?php
$openShiftVar = getenv('OPENSHIFT_MYSQL_DB_HOST');
$dbHost = "";
$dbUser = "";
$dbPassword = "";
// set environment specific variables
if ($openShiftVar === null || $openShiftVar == "") {
	// Not in openshift
	$dbHost = "localhost";
	$dbUser = "php";
	$dbPassword = "password";
} else {
	$dbHost = getenv('OPENSHIFT_MYSQL_DB_HOST');
	$dbUser = getenv('OPENSHIFT_MYSQL_DB_USERNAME');
	$dbPassword = getenv('OPENSHIFT_MYSQL_DB_PASSWORD');
}
$myDB = 'clue';
$status = false;
$db = null;
try {
    $db = new PDO("mysql:host=$dbHost;dbname=$myDB", $dbUser, $dbPassword);
    // set the PDO error mode to exception
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $status = true;
    }
catch(PDOException $e)
    {
    echo "Connection failed: " . $e->getMessage();
    }
?>