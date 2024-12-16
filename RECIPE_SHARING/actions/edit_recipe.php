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

// Fetch existing recipe details
$sql = "SELECT title, description, ingredients, instructions, cooking_time, serving_size, category, difficulty_level, image_url 
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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $ingredients = $_POST['ingredients'];
    $instructions = $_POST['instructions'];
    $cooking_time = $_POST['cooking_time'];
    $serving_size = $_POST['serving_size'];
    $category = $_POST['category'];
    $difficulty_level = $_POST['difficulty_level'];
    $image_url = $_POST['image_url'];

    // Update the recipe in the database
    $sql = "UPDATE recipes 
            SET title = ?, description = ?, ingredients = ?, instructions = ?, cooking_time = ?, serving_size = ?, category = ?, difficulty_level = ?, image_url = ? 
            WHERE recipe_id = ? AND created_by = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "ssssiiissii",
        $title, $description, $ingredients, $instructions, $cooking_time, $serving_size, $category, $difficulty_level, $image_url, $recipe_id, $user_id
    );

    if ($stmt->execute()) {
        header("Location: manage_recipes.php");
        exit();
    } else {
        $error = "Error updating recipe: " . $conn->error;
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
    <title>Edit Recipe</title>
</head>
<body>
    <h1>Edit Recipe</h1>
    <?php if (isset($error)): ?>
        <p style="color: red;"><?= htmlspecialchars($error); ?></p>
    <?php endif; ?>
    <form method="POST">
        <div>
            <label for="title">Title</label>
            <input type="text" name="title" id="title" value="<?= htmlspecialchars($recipe['title']); ?>" required>
        </div>
        <div>
            <label for="description">Description</label>
            <textarea name="description" id="description" required><?= htmlspecialchars($recipe['description']); ?></textarea>
        </div>
        <div>
            <label for="ingredients">Ingredients</label>
            <textarea name="ingredients" id="ingredients" required><?= htmlspecialchars($recipe['ingredients']); ?></textarea>
        </div>
        <div>
            <label for="instructions">Instructions</label>
            <textarea name="instructions" id="instructions" required><?= htmlspecialchars($recipe['instructions']); ?></textarea>
        </div>
        <div>
            <label for="cooking_time">Cooking Time (in minutes)</label>
            <input type="number" name="cooking_time" id="cooking_time" value="<?= htmlspecialchars($recipe['cooking_time']); ?>" required>
        </div>
        <div>
            <label for="serving_size">Serving Size</label>
            <input type="number" name="serving_size" id="serving_size" value="<?= htmlspecialchars($recipe['serving_size']); ?>">
        </div>
        <div>
            <label for="category">Category</label>
            <select name="category" id="category" required>
                <option value="breakfast" <?= $recipe['category'] === 'breakfast' ? 'selected' : ''; ?>>Breakfast</option>
                <option value="lunch" <?= $recipe['category'] === 'lunch' ? 'selected' : ''; ?>>Lunch</option>
                <option value="dinner" <?= $recipe['category'] === 'dinner' ? 'selected' : ''; ?>>Dinner</option>
                <option value="snack" <?= $recipe['category'] === 'snack' ? 'selected' : ''; ?>>Snack</option>
                <option value="dessert" <?= $recipe['category'] === 'dessert' ? 'selected' : ''; ?>>Dessert</option>
            </select>
        </div>
        <div>
            <label for="difficulty_level">Difficulty Level</label>
            <select name="difficulty_level" id="difficulty_level" required>
                <option value="easy" <?= $recipe['difficulty_level'] === 'easy' ? 'selected' : ''; ?>>Easy</option>
                <option value="medium" <?= $recipe['difficulty_level'] === 'medium' ? 'selected' : ''; ?>>Medium</option>
                <option value="hard" <?= $recipe['difficulty_level'] === 'hard' ? 'selected' : ''; ?>>Hard</option>
            </select>
        </div>
        <div>
            <label for="image_url">Image URL</label>
            <input type="text" name="image_url" id="image_url" value="<?= htmlspecialchars($recipe['image_url']); ?>">
        </div>
        <button type="submit">UPDATE</button>
    </form>
    <a href="manage_recipes.php">Back to Recipe Management</a>
</body>
</html>
