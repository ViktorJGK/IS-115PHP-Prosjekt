<?php
include 'db_connect.php'; // Include your database connection file

function loginUser($username, $password) {
    global $conn;
    $sql = "SELECT * FROM users WHERE username='$username'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            session_start();
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['role'] = $row['role'];
            echo "Login successful";
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
