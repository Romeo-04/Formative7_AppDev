<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$database = "user_management";

// Function to get database connection
function getDBConnection() {
    global $servername, $username, $password, $database;
    
    $conn = new mysqli($servername, $username, $password, $database);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    return $conn;
}
?>
