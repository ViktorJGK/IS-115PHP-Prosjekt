<?php
// Starter en ny sesjon eller fortsetter en eksisterende sesjon
// Dette brukes for å holde styr på brukerens innloggingsstatus og annen informasjon gjennom prosjektet
session_start();
?>
<!DOCTYPE html>
<html>

<head>
    <!-- Kobler til en CSS-fil som brukes for styling av navigasjonsbaren -->
    <link rel="stylesheet" href="../css/navbar.css">
    <title>Guest Profile</title> <!-- Setter tittelen på nettsiden -->
</head>

<body>
    <!-- Oppretter navigasjonsbaren som vises øverst på siden -->
    <div class="navbar">
        <!-- Lenke til hjemmesiden -->
        <a href="index.php">Hjem</a>

        <!-- Viser forskjellige alternativer i navigasjonsbaren basert på om brukeren er logget inn eller ikke -->
        <?php if (!isset($_SESSION['user_id'])): ?>
            <!-- Hvis brukeren IKKE er logget inn, vises lenker for å registrere seg eller logge inn -->
            <a href="register.php">Registrer Bruker</a>
            <a href="login.php">Logg Inn</a>
        <?php else: ?>
            <!-- Hvis brukeren ER logget inn, vises en lenke til brukerens profil -->
            <a href="profile.php">Profil</a>
        <?php endif; ?>

        <!-- Viser en logg ut-knapp som bare er tilgjengelig hvis brukeren er logget inn -->
        <?php if (isset($_SESSION['user_id'])): ?>
            <!-- Skjema for å logge ut brukeren -->
            <form class="navbar" id="logout-form" action="../logout.php" method="POST" style="display:inline;">
                <!-- Lenke som fører til logout.php, hvor utloggingslogikken håndteres -->
                <a href="../logout.php" type="submit" id="logout">Logg Ut</a>
            </form>
        <?php endif; ?>
    </div>
    <br> <!-- Litt mellomrom under navigasjonsbaren -->
</body>

</html>
