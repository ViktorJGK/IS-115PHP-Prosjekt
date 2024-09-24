<?php
include '../db_connect.php'; // Include your database connection file
include '../Components/header.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$logout_message = '';

if (isset($_GET['logout_message'])) {
    $logout_message = $_GET['logout_message'];
}

function loginUser($username, $password) {
    global $conn;
    $sql = "SELECT * FROM users WHERE username='$username'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['role'] = $row['role'];
            header("Location: profile.php"); // Redirect to the desired page
            exit(); // Ensure no further code is executed
        } else {
            echo "Invalid password";
        }
    } else {
        echo "No user found";
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    loginUser($_POST['username'], $_POST['password']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/login.css">
    <title>Login</title>
</head>
<body>
    <div class="container">
        <h1>Logg Inn</h1>
        <?php if ($logout_message): ?>
            <p><?php echo htmlspecialchars($logout_message); ?></p>
        <?php endif; ?>
        <form method="post" action="login.php">
            <label for="username">Brukernavn:</label>
            <input type="text" id="username" name="username" required>
            
            <label for="password">Passord:</label>
            <input type="password" id="password" name="password" required>
            
            <input type="submit" value="Logg Inn">
        </form>
    </div>
</body>
</html>
