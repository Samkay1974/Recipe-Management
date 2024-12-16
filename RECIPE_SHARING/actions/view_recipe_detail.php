<?php
// Start session and include database connection
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include 'db_connection.php';

// Get the recipe ID from the URL
if (isset($_GET['id'])) {
    $recipe_id = $_GET['id'];

    // Fetch the recipe details from the database
    $sql = "SELECT r.title, r.ingredients, r.instructions, r.cooking_time, r.category, r.difficulty_level, r.created_at, u.fname, u.lname 
            FROM recipes r 
            JOIN people u ON r.created_by = u.user_id 
            WHERE r.recipe_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $recipe_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $recipe = $result->fetch_assoc();
    } else {
        $error_message = "Recipe not found.";
    }
} else {
    $error_message = "No recipe ID specified.";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Recipe</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color:lightgrey;
        }
        .container {
            width: 80%;
            margin: 20px auto;
        }
        .recipe-detail {
            border: 1px solid #ccc;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .recipe-detail h2 {
            font-size: 1.8rem;
        }
        .recipe-detail p {
            font-size: 1rem;
            color: #555;
        }
        .creator-info {
            font-size: 1rem;
            color: #777;
            font-style: italic;
        }
    </style>
</head>
<body>

<div class="container">
    <?php if (isset($error_message)): ?>
        <p style="color: red;"><?= $error_message ?></p>
    <?php else: ?>
        <div class="recipe-detail">
            <h2><?= htmlspecialchars($recipe['title']) ?></h2>
            <p><strong>Category:</strong> <?= htmlspecialchars($recipe['category']) ?></p>
            <p><strong>Difficulty Level:</strong> <?= htmlspecialchars($recipe['difficulty_level']) ?></p>
            <p><strong>Cooking Time:</strong> <?= htmlspecialchars($recipe['cooking_time']) ?> minutes</p>
            <p><strong>Ingredients:</strong> <?= htmlspecialchars($recipe['ingredients']) ?></p>
            <p><strong>Instructions:</strong> <?= nl2br(htmlspecialchars($recipe['instructions'])) ?></p>
            <p><strong>Created by:</strong> <?= htmlspecialchars($recipe['fname']) . ' ' . htmlspecialchars($recipe['lname']) ?></p>
            <p><strong>Created on:</strong> <?= htmlspecialchars($recipe['created_at']) ?></p>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
