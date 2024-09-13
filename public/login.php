<?php
include '../db_connect.php'; // Include your database connection file

function loginUser($username, $password) {
    global $conn;
    $sql = "SELECT * FROM users WHERE username='$username'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            session_start();
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['role'] = $row['role'];
            header("Location: gjest_page/velkommen.php"); // Redirect to the desired page
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
    <style>
       
    </style>
</head>
<body>
    <div class="navbar">
        <a href="index.php">Hjem</a>
        <a href="register.php">Registrer Bruker</a>
        <a href="login.php">Logg Inn</a>
        <a href="profile.php">Profil</a>
    </div>
    <div class="container">
        <h1>Logg Inn</h1>
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
