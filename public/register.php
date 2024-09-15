<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include '../db_connect.php';
if (!$conn) {
    die("Database connection failed: " . $conn->connect_error);
}

include '../Components/header.php';

// Ensure only an authorized user can register as an admin
function registerUser($username, $password, $email, $role) {
    global $conn;

    // Restrict direct admin role assignment to authorized users
    if ($role == 1 && (!isset($_SESSION['admin']) || !$_SESSION['admin'])) {
        echo "Du har ikke tillatelse til å registrere deg som administrator.";
        return;
    }

    // Check if username or email already exists
    $sql = "SELECT * FROM users WHERE username = ? OR email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "Feil: Brukernavn eller e-post eksisterer allerede";
        $stmt->close();
        return;
    }

    // Proceed with registration
    $stmt->close();
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $sql = "INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $username, $hashed_password, $email, $role);
    
    if ($stmt->execute()) {
        echo "Brukerregistrering vellykket";
    } else {
        echo "Feil: " . $stmt->error;
    }
    $stmt->close();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    // Validate role input
    if (!in_array($role, [0, 1])) {
        die("Ugyldig rolle valgt.");
    }

    registerUser($username, $password, $email, $role);
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/register.css">
    <title>Registrer Bruker</title>
</head>
<body>
    <div class="container">
        <h1>Registrer Bruker</h1>
        <form method="post" action="register.php">
            <label for="username">Brukernavn:</label>
            <input type="text" id="username" name="username" required>
            
            <label for="password">Passord:</label>
            <input type="password" id="password" name="password" required>

            <label for="email">E-post:</label>
            <input type="email" id="email" name="email" required>

            <label for="role">Rolle:</label>
            <select id="role" name="role">
                <option value="0">Gjest</option>
                <option value="1">Administrator</option>
            </select>

            <input type="submit" value="Registrer">
        </form>
    </div>
</body>
</html>
