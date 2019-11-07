<?php
// Database credentials
    $hostname = "localhost";
    $username = "root";
    $password = "";
    $db_name = "Cs3750";

// Create connection
$conn = new mysqli($hostname, $username, $password, $db_name);// mysqli_connect($hostname, $username, $password, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection to database failed: " . $conn->connect_error);
} else {
    // Debugging
    //echo "Connected to database successfully";
}
