<?php
$host = "localhost";
$username = "cpad";
$password = "cpadPassword";
$database = "cpad_03_strange";

try {
    // Create PDO connection
    $db_conn = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    echo "<p>Successful connecting to <b>$database</b> database</p>";
    
    echo "<p>Successful connecting to <b>$database</b> database using PDO.</p>";
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
