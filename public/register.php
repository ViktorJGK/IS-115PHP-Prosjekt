<?php
// Aktiverer visning av feil for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Inkluderer tilkobling til databasen
include '../db_connect.php';
// Sjekker om tilkoblingen til databasen er vellykket
if (!$conn) {
    die("Database tilkobling Feilet: " . $conn->connect_error);
}

// Inkluderer header-komponenten
include '../Components/header.php';

// Variabel for å lagre feilmeldinger eller suksessmeldinger
$message = "";

// Funksjon for å registrere en ny bruker
function registerUser($username, $password, $email, $role)
{
    global $conn, $message;

    // Sjekker om administrator-rollen skal tildeles, og om brukeren har nødvendige rettigheter
    if ($role == 1 && (!isset($_SESSION['admin']) || !$_SESSION['admin'])) {
        $message = "Du har ikke tillatelse til å registrere deg som administrator.";
        return;
    }

    // Sjekker om brukernavnet eller e-posten allerede finnes i databasen
    $sql = "SELECT * FROM users WHERE username = ? OR email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Hvis brukernavnet eller e-posten allerede eksisterer, vis feilmelding
    if ($result->num_rows > 0) {
        $message = "Feil: Brukernavn eller e-post eksisterer allerede";
        $stmt->close();
        return;
    }

    // Fortsetter med registreringen hvis brukernavnet og e-posten er unike
    $stmt->close();
    // Krypterer passordet før lagring
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    // SQL-spørring for å sette inn ny bruker i databasen
    $sql = "INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $username, $hashed_password, $email, $role);

    // Utfører spørringen og viser en suksess- eller feilmelding
    if ($stmt->execute()) {
        $message = "Brukerregistrering vellykket";
    } else {
        $message = "Feil: " . $stmt->error;
    }
    $stmt->close();
}

// Behandler form-data når skjemaet blir sendt
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Henter data fra skjemaet
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    // Setter standard rolle til 0 (vanlig bruker)
    $role = 0;

    // Kaller registerUser-funksjonen for å registrere brukeren
    registerUser($username, $password, $email, $role);
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Linker til stilark for registreringsskjema -->
    <link rel="stylesheet" href="../css/register.css">
    <title>Registrer Bruker</title>
</head>

<body>
    <div class="container">
        <h1>Registrer Bruker</h1>
        <!-- Vist feilmelding eller suksessmelding, hvis den finnes -->
        <?php if (!empty($message)): ?>
            <div>
                <?php echo $message;?>  <!-- Vist melding -->
                <br>
                <br>        
            </div>
        <?php endif; ?>
        <!-- Skjema for registrering av ny bruker -->
        <form method="post" action="register.php">
            <label for="username">Brukernavn:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Passord:</label>
            <input type="password" id="password" name="password" required>

            <label for="email">E-post:</label>
            <input type="email" id="email" name="email" required>

            <input type="submit" value="Registrer">  <!-- Submit-knapp for skjemaet -->
        </form>
    </div>
</body>

</html>
