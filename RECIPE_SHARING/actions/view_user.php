<?php
require_once 'db_connection.php'; // Include database connection
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    // Fetch user details
    $stmt = $conn->prepare("SELECT * FROM people WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
    } else {
        echo "User not found!";
        exit;
    }
} else {
    echo "Invalid user ID!";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View User</title>
    <link rel="stylesheet" href="../assets/css/users.css">
</head>
<body>
    <h1>User Details</h1>
    <table>
        <tr><th>User ID:</th><td><?php echo $user['user_id']; ?></td></tr>
        <tr><th>First Name:</th><td><?php echo $user['fname']; ?></td></tr>
        <tr><th>Last Name:</th><td><?php echo $user['lname']; ?></td></tr>
        <tr><th>Email:</th><td><?php echo $user['email']; ?></td></tr>
        <tr><th>Role:</th><td><?php echo $user['role'] == 1 ? "Super Admin" : "Regular Admin"; ?></td></tr>
    </table>
    <a href="../view/users.html" class="button-link">Back to User Management</a>
</body>
</html>
