<?php
// Start session and include database connection

session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include 'db_connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    die("User not logged in");
}

$user_id = $_SESSION['user_id'];

// Fetch the user's full name
$sql = "SELECT CONCAT(fname, ' ', lname) AS full_name FROM people WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $full_name = $user['full_name'];
} else {
    die("User not found");
}

// Fetch all recipes created by this user
$sql = "SELECT recipe_id, title, created_at FROM recipes WHERE created_by = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$recipes = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recipe Management</title>
    <link rel="stylesheet" href="../assets/css/manage_recipes.css">
    
</head>
<body>
    <h1>Welcome, <?= htmlspecialchars($full_name); ?>!</h1>

    <?php if ($recipes->num_rows === 0): ?>
        <!-- If no recipes exist, show a large "Add New Recipe" button -->
        <a href="add_recipe.php" class="add-btn">Add New Recipe</a>
        <a href="dashboard.php" class="add-btn">Go to Admin Dashboard(Only for Admin)</a>
    <?php else: ?>
        <h2>Your Recipes</h2>
        <!-- Display recipes in a table -->
        <table>
            <thead>
                <tr>
                    <th>Recipe Name</th>
                    <th>Time Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $recipes->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['title']); ?></td>
                        <td><?= $row['created_at']; ?></td>
                        <td class="actions">
                            <a href="view_recipe.php?id=<?= $row['recipe_id']; ?>"><button>View</button></a>
                            <a href="edit_recipe.php?id=<?= $row['recipe_id']; ?>"><button>Edit</button></a>
                            <a href="delete_recipe.php?id=<?= $row['recipe_id']; ?>" onclick="return confirm('Are you sure you want to delete this recipe?');">
                                <button>Delete</button>
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <!-- Add New Recipe button -->
        <a href="add_recipe.php"><button class="add-btn">Add New Recipe</button></a>
        <a href="index.php"><button class="add-btn">View All Edziban Recipes</button></a>
        <a href="dashboard.php" class="add-btn">Go to Admin Dashboard(Only for Admin)</a>

    <?php endif; ?>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
