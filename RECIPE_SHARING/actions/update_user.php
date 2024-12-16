<?php
require_once 'db_connection.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    // Fetch existing user details
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

    // Update user details
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $fname = $_POST['fname'];
        $lname = $_POST['lname'];
        $email = $_POST['email'];
        $role = $_POST['role'];

        $stmt = $conn->prepare("UPDATE people SET fname = ?, lname = ?, email = ?, role = ? WHERE user_id = ?");
        $stmt->bind_param("sssii", $fname, $lname, $email, $role, $user_id);

        if ($stmt->execute()) {
            header("Location: ../view/users.html");
            exit;
        } else {
            echo "Error updating user!";
        }
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
    <title>Update User</title>
    <link rel="stylesheet" href="../assets/css/users.css">
</head>
<body>
    <h1>Update User Details</h1>
    <form method="POST">
        <label>First Name:</label>
        <input type="text" name="fname" value="<?php echo $user['fname']; ?>" required>
        <label>Last Name:</label>
        <input type="text" name="lname" value="<?php echo $user['lname']; ?>" required>
        <label>Email:</label>
        <input type="email" name="email" value="<?php echo $user['email']; ?>" required>
        <label>Role:</label>
        <select name="role">
            <option value="1" <?php echo $user['role'] == 1 ? "selected" : ""; ?>>Super Admin</option>
            <option value="2" <?php echo $user['role'] == 2 ? "selected" : ""; ?>>Regular Admin</option>
        </select>
        <button type="submit">Update</button>
    </form>
    <a href="../view/users.html" class="button-link">Back to User Management</a>
</body>
</html>
