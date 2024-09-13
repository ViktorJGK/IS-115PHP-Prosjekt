<?php
include '../db_connect.php';

function registerUser($username, $password, $email) {
    global $conn;

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

    // If not, proceed with registration
    $stmt->close();
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $role = 0; // Automatically set role to 'guest'
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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    registerUser($_POST['username'], $_POST['password'], $_POST['email']);
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
    <div class="navbar">
        <a href="index.php">Hjem</a>
        <a href="register.php">Registrer Bruker</a>
        <a href="login.php">Logg Inn</a>
        <a href="profile.php">Profil</a>
    </div>
    <div class="container">
        <h1>Registrer Bruker</h1>
        <form method="post" action="register.php">
            <label for="username">Brukernavn:</label>
            <input type="text" id="username" name="username" required>
            
            <label for="password">Passord:</label>
            <input type="password" id="password" name="password" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <input type="submit" value="Registrer">
        </form>
    </div>
</body>
</html>