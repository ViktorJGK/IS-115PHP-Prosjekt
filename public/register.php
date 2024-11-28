<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include '../db_connect.php';
if (!$conn) {
    die("Database connection failed: " . $conn->connect_error);
}

include '../Components/header.php';

// Variable to store error/success messages
$message = "";

// Ensure only an authorized user can register as an admin
function registerUser($username, $password, $email, $role)
{
    global $conn, $message;

    // Restrict direct admin role assignment to authorized users
    if ($role == 1 && (!isset($_SESSION['admin']) || !$_SESSION['admin'])) {
        $message = "Du har ikke tillatelse til å registrere deg som administrator.";
        return;
    }

    // Check if username or email already exists
    $sql = "SELECT * FROM users WHERE username = ? OR email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $message = "Feil: Brukernavn eller e-post eksisterer allerede";
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
        $message = "Brukerregistrering vellykket";
    } else {
        $message = "Feil: " . $stmt->error;
    }
    $stmt->close();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    $role = 0;

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
        <!-- Display the message if it exists -->
        <?php if (!empty($message)): ?>
            <div>
                <?php echo $message;?>
                <br>
                <br>        
            </div>
        <?php endif; ?>
        <form method="post" action="register.php">
            <label for="username">Brukernavn:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Passord:</label>
            <input type="password" id="password" name="password" required>

            <label for="email">E-post:</label>
            <input type="email" id="email" name="email" required>

            </select>

            <input type="submit" value="Registrer">
        </form>
    </div>
</body>

</html>