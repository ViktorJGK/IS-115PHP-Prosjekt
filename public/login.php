<?php
include '../db_connect.php'; // Include your database connection file
include '../Components/header.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$error = ''; // Initialiserer variabel for feilmeldinger

// Sjekk om det er en logout-melding i URLen
$logout_message = '';
if (isset($_GET['logout_message'])) {
    $logout_message = $_GET['logout_message'];
}

function loginUser($username, $password)
{
    global $conn, $error;

    // Sjekk om brukeren finnes i databasen
    $sql = "SELECT user_id, password, role, failed_attempts, lockout_until FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($user_id, $password_hash, $role, $failed_attempts, $lockout_until);
        $stmt->fetch();

        // Sjekk om brukeren er låst ute
        if ($lockout_until && strtotime($lockout_until) > time()) {
            $remaining_time = strtotime($lockout_until) - time();
            $minutes_left = ceil($remaining_time / 60);
            $error = "Du har for mange feilede forsøk. Du kan prøve igjen om $minutes_left minutter.";
        } else {
            // Hvis passordet er riktig
            if (password_verify($password, $password_hash)) {
                // Tilbakestill feilede forsøk og lås ut ifølge
                $stmt_reset = $conn->prepare("UPDATE users SET failed_attempts = 0, lockout_until = NULL WHERE username = ?");
                $stmt_reset->bind_param("s", $username);
                $stmt_reset->execute();
                $stmt_reset->close();

                // Lagre brukerinfo i økten
                $_SESSION['user_id'] = $user_id;
                $_SESSION['username'] = $username;
                $_SESSION['role'] = $role;

                header("Location: profile.php");
                exit();
            } else {
                // Øk antall feilede forsøk
                $failed_attempts++;
                $last_failed_attempt = date("Y-m-d H:i:s");

                if ($failed_attempts >= 3) {
                    // Lås ut brukeren i 1 time
                    $lockout_until = date("Y-m-d H:i:s", strtotime("+1 hour"));
                    $stmt_update = $conn->prepare("UPDATE users SET failed_attempts = ?, lockout_until = ?, last_failed_attempt = ? WHERE username = ?");
                    $stmt_update->bind_param("isss", $failed_attempts, $lockout_until, $last_failed_attempt, $username);
                } else {
                    // Oppdater antall feilede forsøk og tidspunkt for siste forsøk
                    $stmt_update = $conn->prepare("UPDATE users SET failed_attempts = ?, last_failed_attempt = ? WHERE username = ?");
                    $stmt_update->bind_param("iss", $failed_attempts, $last_failed_attempt, $username);
                }

                $stmt_update->execute();
                $stmt_update->close();

                $error = "Feil brukernavn eller passord.";
            }
        }
    } else {
        $error = "Feil brukernavn eller passord.";
    }

    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    loginUser($_POST['username'], $_POST['password']);
}
?>

<!DOCTYPE html>
<html lang="no">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/login.css">
    <title>Logg Inn</title>
</head>

<body>
    <div class="container">
        <h1>Logg Inn</h1>

        <!-- Vis logout-melding hvis tilgjengelig -->
        <?php if ($logout_message): ?>
            <p style="color: green;"><?php echo htmlspecialchars($logout_message); ?></p>
        <?php endif; ?>

        <!-- Feilmelding hvis noen -->
        <?php if (!empty($error)): ?>
            <p style="color: red;"><?php echo $error; ?></p>
        <?php endif; ?>

        <!-- Innloggingsskjema -->
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