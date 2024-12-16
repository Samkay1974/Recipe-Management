<?php
// Start session and include database connection
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
include 'db_connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    die("User not logged in");
}

// Get recipe ID from the query parameter
if (!isset($_GET['id'])) {
    die("Recipe ID not provided.");
}

$recipe_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

// Fetch recipe details from the database
$sql = "SELECT title, instructions, cooking_time, serving_size, difficulty_level 
        FROM recipes 
        WHERE recipe_id = ? AND created_by = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $recipe_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Recipe not found or access denied.");
}

$recipe = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Recipe</title>
</head>
<style>
    body{
        background-color: black;
        color:white;
    }
    a{
        display: inline-block;
        margin-bottom: 20px;
        padding: 10px 15px;
        background-color: #007BFF;
        color: white;
        text-decoration: none;
        font-size: 16px;
        font-weight: bold;
        border-radius: 5px;
        transition: background-color 0.3s;
    }
</style>
<body>
    <h1>View Recipe</h1>
    <p><strong>Title:</strong> <?= htmlspecialchars($recipe['title']); ?></p>
    <p><strong>Instructions:</strong> <?= nl2br(htmlspecialchars($recipe['instructions'])); ?></p>
    <p><strong>Cooking Time:</strong> <?= htmlspecialchars($recipe['cooking_time']); ?> minutes</p>
    <p><strong>Serving Size:</strong> <?= htmlspecialchars($recipe['serving_size']); ?></p>
    <p><strong>Difficulty Level:</strong> <?= htmlspecialchars($recipe['difficulty_level']); ?></p>
    <a href="manage_recipes.php">Back to Recipe Management</a>
</body>
</html>
