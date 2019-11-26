<?php
// Database credentials
$hostname = "sql201.epizy.com";
$username = "epiz_24388369";
$password = "weberstudent1";
$db_name = "epiz_24388369_cs3750_names";

//// Create connection
$conn = new mysqli($hostname, $username, $password, $db_name);// mysqli_connect($hostname, $username, $password, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection to database failed: " . $conn->connect_error);
} else {
    // Debugging
    //echo "Connected to database successfully";
}
