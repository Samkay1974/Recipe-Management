<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once 'db_connection.php';

if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    // Delete user
    $stmt = $conn->prepare("DELETE FROM people WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        header("Location: ../view/users.html");
        exit;
    } else {
        echo "Error deleting user!";
    }
} else {
    echo "Invalid user ID!";
}
?>
