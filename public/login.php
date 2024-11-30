<?php
// Inkluderer databasetilkoblingsfilen
include '../db_connect.php'; 

// Inkluderer header-komponenten for å legge til standard toppseksjon
include '../Components/header.php';

// Sjekker om en økt allerede er startet, hvis ikke starter en ny økt
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Definerer en variabel for å vise utloggingsmelding
$logout_message = '';

// Sjekker om det finnes en utloggingsmelding sendt via URL-en (GET-parameter)
if (isset($_GET['logout_message'])) {
    $logout_message = $_GET['logout_message'];
}

// Funksjon for å logge inn en bruker basert på brukernavn og passord
function loginUser($username, $password) {
    global $conn; // Gjør databasetilkoblingen tilgjengelig i funksjonen

    // Henter brukerdetaljer basert på brukernavn
    $sql = "SELECT * FROM users WHERE username='$username'";
    $result = $conn->query($sql);

    // Sjekker om brukeren finnes i databasen
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc(); // Henter resultat som en assosiativ array

        // Verifiserer passordet oppgitt av brukeren mot det som er lagret i databasen
        if (password_verify($password, $row['password'])) {
            // Starter en økt hvis den ikke allerede er startet
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }

            // Lagrer brukerinformasjon i økten
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['role'] = $row['role'];

            // Omdirigerer til profilsiden
            header("Location: profile.php");
            exit(); // Stopper videre utførelse av koden etter omdirigering
        } else {
            echo "Ugyldig passord"; // Feilmelding for feil passord
        }
    } else {
        echo "Ingen bruker funnet"; // Feilmelding hvis brukeren ikke finnes
    }
}

// Håndterer POST-forespørsler fra innloggingsskjemaet
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    loginUser($_POST['username'], $_POST['password']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"> <!-- Angir tegnsett for å støtte spesialtegn -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Responsiv design -->
    <link rel="stylesheet" href="../css/login.css"> <!-- Kobler til CSS for innloggingssiden -->
    <title>Login</title> <!-- Tittel på siden -->
</head>
<body>
    <div class="container"> <!-- Wrapper for innhold -->
        <h1>Logg Inn</h1> <!-- Overskrift for siden -->

        <!-- Viser utloggingsmeldingen hvis den er satt -->
        <?php if ($logout_message): ?>
            <p><?php echo htmlspecialchars($logout_message); ?></p>
        <?php endif; ?>

        <!-- Skjema for innlogging -->
        <form method="post" action="login.php"> <!-- Sender data via POST til samme fil -->
            <label for="username">Brukernavn:</label> <!-- Label for brukernavn -->
            <input type="text" id="username" name="username" required> <!-- Input for brukernavn -->

            <label for="password">Passord:</label> <!-- Label for passord -->
            <input type="password" id="password" name="password" required> <!-- Input for passord -->

            <input type="submit" value="Logg Inn"> <!-- Innloggingsknapp -->
        </form>
    </div>
</body>
</html>
