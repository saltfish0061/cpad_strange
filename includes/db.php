<?php
$host = "localhost";
$username = "cpad";
$password = "cpadPassword";
$database = "cpad_03_strange";

// Create connection
$db_conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($db_conn->connect_error) {
  // FIXED: Changed $conn to $db_conn
  die("Connection failed: " . $db_conn->connect_error);
}

echo "<p>Successful connecting to <b>$database</b> database.</p>";
?>