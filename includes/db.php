<?php
$host = "localhost";
$username = "cpad";
$password = "cpadPassword";
$database = "cpad_03_strange";

try {
    // Create PDO connection
    $db_conn = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
