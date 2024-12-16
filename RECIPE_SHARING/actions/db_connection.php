<?php
// db_connection.php
$host = "localhost"; 
$username = "samuel.ninson";  
$password = "Sam@Ashesi2021";       // Your database password (default for XAMPP is an empty string)
$database = "webtech_fall2024_samuel_ninson";  // The name of your database

// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// Connection successful
?>
