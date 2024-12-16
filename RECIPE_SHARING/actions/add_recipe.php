<?php
// Start session and include database connection
session_start();
include 'db_connection.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    die("User not logged in");
}

$user_id = $_SESSION['user_id'];

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $title = $_POST['title'];
    $description = $_POST['description'];
    $ingredients = $_POST['ingredients'];
    $instructions = $_POST['instructions'];
    $cooking_time = $_POST['cooking_time'];
    $serving_size = $_POST['serving_size'];
    $category = $_POST['category'];
    $difficulty_level = $_POST['difficulty_level'];
    $image_url = $_POST['image_url'];

    // Insert recipe into the database
    $sql = "INSERT INTO recipes (title, description, ingredients, instructions, cooking_time, serving_size, category, difficulty_level, image_url, created_by) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "ssssiiissi",
        $title,
        $description,
        $ingredients,
        $instructions,
        $cooking_time,
        $serving_size,
        $category,
        $difficulty_level,
        $image_url,
        $user_id
    );

    if ($stmt->execute()) {
        // Redirect back to the management page after successful submission
        header("Location: manage_recipes.php");
        exit();
    } else {
        $error = "Error adding recipe: " . $conn->error;
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Recipe</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background: url('../assets/images/add_recipe.avif') no-repeat center center fixed;
            background-size: cover;
            color: whitesmoke
        }
        form {
            max-width: 600px;
            margin: 0 auto;
        }
        form div {
            margin-bottom: 15px;
        }
        form label {
            display: block;
            font-weight: bold;
        }
        form input, form select, form textarea {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        form button {
            background-color: #007BFF;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .error {
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h1>Add New Recipe</h1>

    <?php if (isset($error)): ?>
        <p class="error"><?= htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <div>
            <label for="title">Recipe Title</label>
            <input type="text" name="title" id="title" required>
        </div>
        <div>
            <label for="description">Description</label>
            <textarea name="description" id="description" rows="4"></textarea>
        </div>
        <div>
            <label for="ingredients">Ingredients</label>
            <textarea name="ingredients" id="ingredients" rows="4" required></textarea>
        </div>
        <div>
            <label for="instructions">Instructions</label>
            <textarea name="instructions" id="instructions" rows="4" required></textarea>
        </div>
        <div>
            <label for="cooking_time">Cooking Time (in minutes)</label>
            <input type="number" name="cooking_time" id="cooking_time">
        </div>
        <div>
            <label for="serving_size">Serving Size</label>
            <input type="number" name="serving_size" id="serving_size">
        </div>
        <div>
            <label for="category">Category</label>
            <select name="category" id="category" required>
                <option value="breakfast">Breakfast</option>
                <option value="lunch">Lunch</option>
                <option value="dinner">Dinner</option>
                <option value="snack">Snack</option>
                <option value="dessert">Dessert</option>
            </select>
        </div>
        <div>
            <label for="difficulty_level">Difficulty Level</label>
            <select name="difficulty_level" id="difficulty_level" required>
                <option value="easy">Easy</option>
                <option value="medium">Medium</option>
                <option value="hard">Hard</option>
            </select>
        </div>
        <div>
            <label for="image_url">Image URL</label>
            <input type="text" name="image_url" id="image_url">
        </div>
        <div>
            <button type="submit">Add Recipe</button>
        </div>
    </form>
</body>
</html>
