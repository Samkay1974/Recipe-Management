<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
include 'db_connection.php';


if (!isset($_SESSION['user_id'])) {
    die("User not logged in");
}


if (!isset($_GET['id'])) {
    die("Recipe ID not provided.");
}

$recipe_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

// Delete the recipe from the database
$sql = "DELETE FROM recipes WHERE recipe_id = ? AND created_by = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $recipe_id, $user_id);

if ($stmt->execute()) {
    header("Location: manage_recipes.php");
    exit();
} else {
    die("Error deleting recipe: " . $conn->error);
}

$stmt->close();
$conn->close();
?>
