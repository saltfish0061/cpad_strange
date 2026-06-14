<?php
$host = "localhost";
$username = "cpad";
$password = "cpadPassword";
$database = "cpad_03_strange";

try {
    $db_conn = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $db_conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

return $db_conn;
?>
