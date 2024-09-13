<?php
include '../../db_connect.php'; // Include your database connection file

function addLoyaltyPoints($user_id, $points) {
    global $conn;
    $sql = "UPDATE loyalty_program SET points = points + $points WHERE user_id = '$user_id'";
    if ($conn->query($sql) === TRUE) {
        echo "Points added successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

function getGuestProfile($user_id) {
    global $conn;
    $sql = "SELECT * FROM users WHERE user_id = '$user_id'"; // Updated column name
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo "Username: " . $row["username"] . "<br>";
        // Fetch and display other profile details like reservation history, preferences, etc.
    } else {
        echo "No user found";
    }
}

session_start();
if (isset($_SESSION['user_id'])) {
    getGuestProfile($_SESSION['user_id']);
} else {
    echo "Please log in to view your profile.";
}
?>
