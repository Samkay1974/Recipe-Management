<?php
// Include the database connection file
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include 'db_connection.php';

// Initialize search and filter variables
$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';
$difficulty = $_GET['difficulty'] ?? '';
$sort = $_GET['sort'] ?? 'newest';

// Build the SQL query
$sql = "SELECT recipes.*, people.fname, people.lname 
        FROM recipes 
        LEFT JOIN people ON recipes.created_by = people.user_id 
        WHERE 1=1";

// Apply search filter
if (!empty($search)) {
    $sql .= " AND recipes.title LIKE '%" . $conn->real_escape_string($search) . "%'";
}

// Apply category filter
if (!empty($category)) {
    $sql .= " AND recipes.category = '" . $conn->real_escape_string($category) . "'";
}

// Apply difficulty filter
if (!empty($difficulty)) {
    $sql .= " AND recipes.difficulty_level = '" . $conn->real_escape_string($difficulty) . "'";
}

// Apply sorting
if ($sort == 'oldest') {
    $sql .= " ORDER BY recipes.created_at ASC";
} else {
    $sql .= " ORDER BY recipes.created_at DESC";
}

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Recipes</title>
    <link rel="stylesheet" href="../assets/css/index.css">
    
</head>


<body>
    <div>
    <h1>EDZIBAN RECIPES PAGE</h1>
    <p>View all edziban recipes here posted by other users!!!ENJOY</p>
    </div>
    <!-- Filter Form -->
    <form method="GET" class="filter-form">
        <input type="text" name="search" placeholder="Search by title" value="<?= htmlspecialchars($search) ?>">
        <select name="category">
            <option value="">All Categories</option>
            <option value="breakfast" <?= $category == 'breakfast' ? 'selected' : '' ?>>Breakfast</option>
            <option value="lunch" <?= $category == 'lunch' ? 'selected' : '' ?>>Lunch</option>
            <option value="dinner" <?= $category == 'dinner' ? 'selected' : '' ?>>Dinner</option>
            <option value="snack" <?= $category == 'snack' ? 'selected' : '' ?>>Snack</option>
            <option value="dessert" <?= $category == 'dessert' ? 'selected' : '' ?>>Dessert</option>
        </select>
        <select name="difficulty">
            <option value="">All Difficulty Levels</option>
            <option value="easy" <?= $difficulty == 'easy' ? 'selected' : '' ?>>Easy</option>
            <option value="medium" <?= $difficulty == 'medium' ? 'selected' : '' ?>>Medium</option>
            <option value="hard" <?= $difficulty == 'hard' ? 'selected' : '' ?>>Hard</option>
        </select>
        <select name="sort">
            <option value="newest" <?= $sort == 'newest' ? 'selected' : '' ?>>Newest</option>
            <option value="oldest" <?= $sort == 'oldest' ? 'selected' : '' ?>>Oldest</option>
        </select>
        <button type="submit">Filter</button>
    </form>
    

    <!-- Recipe Cards -->
    <div class="recipe-container">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="recipe-card">
                    <!-- Display the recipe image -->
                    <?php if (!empty($row['image_url'])): ?>
                        <img src="<?= htmlspecialchars($row['image_url']) ?>" alt="<?= htmlspecialchars($row['title']) ?>">
                    <?php else: ?>
                        <img src="default-image.jpg" alt="Default Recipe Image">
                    <?php endif; ?>

                    <!-- Display the recipe details -->
                    <h3><?= htmlspecialchars($row['title']) ?></h3>
                    <!-- <p><strong>Category:</strong> <?= htmlspecialchars($row['category']) ?></p> -->
                    <p><strong>Difficulty Level:</strong> <?= htmlspecialchars($row['difficulty_level']) ?></p>
                    <p><strong>Created by:</strong> <?= htmlspecialchars($row['fname']) . ' ' . htmlspecialchars($row['lname']) ?></p>
                    <p><strong>Created on:</strong> <?= htmlspecialchars($row['created_at']) ?></p>
                    <!-- View recipe button -->
                    <a href="view_recipe_detail.php?id=<?= $row['recipe_id'] ?>" class="view-btn">View Recipe</a>
                </div>
                
            <?php endwhile; ?>
        <?php else: ?>
            <p>No recipes found!</p>
        <?php endif; ?>
    </div>
    <a href="manage_recipes.php"><button class="add-btn">Go to recipe management page</button></a>

<div class="account">
<a href="../view/login.html">Log In</a>
<a href="../view/register.html">Register Now</a>
</div>
    

      
    
    
</body>
</html>
