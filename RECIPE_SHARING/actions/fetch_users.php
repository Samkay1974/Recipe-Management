<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include 'db_connection.php';

// Create a connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch users from the database
$sql = "SELECT user_id, fname, lname, email, role, created_at FROM people";
$result = $conn->query($sql);

$users = [];
if ($result->num_rows > 0) {
    // Fetch all rows
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}

// Return the data as JSON
header('Content-Type: application/json');
echo json_encode($users);

// Close connection
$conn->close();
?>
