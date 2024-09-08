<?php
include 'db_connect.php';

function registerUser($username, $password, $role) {
    global $conn;
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $sql = "INSERT INTO users (username, password, role) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $username, $hashed_password, $role);
    
    if ($stmt->execute()) {
        echo "Brukerregistrering vellykket";
    } else {
        echo "Feil: " . $stmt->error;
    }
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    registerUser($_POST['username'], $_POST['password'], $_POST['role']);
} else {
    echo "Ingen data mottatt.";
}
?>
