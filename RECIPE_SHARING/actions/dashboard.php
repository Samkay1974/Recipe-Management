<?php
// Database connection

session_start();
include 'db_connection.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../view/login.html");
    exit();
}

// Fetch user role from the database
$userId = $_SESSION['user_id'];
$userQuery = $conn->query("SELECT role FROM people WHERE user_id = $userId");
$userData = $userQuery->fetch_assoc();

if (!$userData || $userData['role'] != 1) {
    echo "<h1>Access Denied</h1>";
    echo "<p>You are not authorized to access this page.</p>";
    exit();
}


// Fetch total users
$userResult = $conn->query("SELECT COUNT(*) as total_users FROM people");
$totalUsers = $userResult->fetch_assoc()['total_users'];

// Fetch total recipes
$recipeResult = $conn->query("SELECT COUNT(*) as total_recipes FROM recipes");
$totalRecipes = $recipeResult->fetch_assoc()['total_recipes'];

// Fetch recipes per month for the last 12 months
$currentDate = new DateTime();
$months = [];
$recipeCounts = [];

for ($i = 0; $i < 12; $i++) {
    $month = $currentDate->format('Y-m');
    $months[] = $currentDate->format('M Y');
    
    $monthStart = $month . "-01";
    $monthEnd = $currentDate->format('Y-m-t');
    
    $monthlyQuery = $conn->query("
        SELECT COUNT(*) as recipe_count 
        FROM recipes 
        WHERE created_at >= '$monthStart' AND created_at <= '$monthEnd'
    ");
    $recipeCounts[] = $monthlyQuery->fetch_assoc()['recipe_count'];
    
    $currentDate->modify('-1 month');
}

$months = array_reverse($months);
$recipeCounts = array_reverse($recipeCounts);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="../assets/css/new_dash.css">
</head>
<body>
    <h1>SUPER ADMIN DASHBOARD </h1>
    <div class="container my-5">
        <div class="row">
            <!-- Total Users Card -->
            <div class="col-md-6 mb-4">
                <div class="card text-center shadow">
                    <div class="card-body">
                        <h5 class="card-title">Total Users</h5>
                        <p class="card-text fs-3 fw-bold"><?= $totalUsers ?></p>
                    </div>
                </div>
            </div>
            <!-- Total Recipes Card -->
            <div class="col-md-6 mb-4">
                <div class="card text-center shadow">
                    <div class="card-body">
                        <h5 class="card-title">Total Recipes</h5>
                        <p class="card-text fs-3 fw-bold"><?= $totalRecipes ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recipes Per Month Chart -->
        <div class="card shadow">
            <div class="card-body">
                <h5 class="card-title text-center">Recipes Per Month</h5>
                <canvas id="recipeChart"></canvas>
            </div>
        </div>
    </div>

    <script>
        const ctx = document.getElementById('recipeChart').getContext('2d');
        const recipeChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?= json_encode($months) ?>,
                datasets: [{
                    label: 'Recipes',
                    data: <?= json_encode($recipeCounts) ?>,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
    <div class="button-link">
        <a href="../view/users.html">Go to User Management</a>
    </div>
    <div class="button-link">
        <a href="manage_recipes.php">Go to Recipe Management</a>
    </div>

    
</body>
</html>
