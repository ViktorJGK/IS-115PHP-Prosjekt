<?php
// Aktiverer visning av feil i nettleseren for debugging-formål
ini_set('display_errors', 1); // Viser alle feilmeldinger
ini_set('display_startup_errors', 1); // Viser feil som oppstår under PHP-oppstart
error_reporting(E_ALL); // Rapporterer alle typer feil

// Starter en ny eller eksisterende sesjon, nødvendig for funksjoner som innlogging
session_start(); 

// Inkluderer header-komponenten som vanligvis inneholder sideoverskrifter og meny
include "../Components/header.php";

// Inkluderer databasenkoblingen for å kunne hente eller sende data til databasen
include '../db_connect.php';
?>

<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8"> <!-- Angir tegnsett for å støtte norske bokstaver -->
    <title>Rombooking System</title> <!-- Tittel som vises i nettleserfanen -->
    <link rel="stylesheet" href="../css/index.css"> <!-- Kobler til CSS for hovedinnhold -->
    <link rel="stylesheet" href="../css/footer.css"> <!-- Kobler til CSS for footer-styling -->
</head>
<body>
    <div class="container"> <!-- En container for å strukturere innholdet på siden -->
        <h1>Velkommen til vårt motell</h1> <!-- Hovedoverskrift for siden -->
        <h2>Søk etter tilgjengelige rom</h2> <!-- Underoverskrift for søkefunksjonen -->
        
        <!-- Form for å sende innsjekkingsdetaljer til "available_rooms.php" via POST -->
        <form action="available_rooms.php" method="POST"> 
            <label for="check_in">Innsjekkingsdato:</label> <!-- Label for innsjekkingsdato -->
            <input type="date" name="check_in" id="check_in" required> <!-- Dato-input for innsjekk -->

            <label for="check_out">Utsjekkingsdato:</label> <!-- Label for utsjekkingsdato -->
            <input type="date" name="check_out" id="check_out" required> <!-- Dato-input for utsjekk -->

            <label for="adults">Antall voksne:</label> <!-- Label for antall voksne -->
            <input type="number" name="adults" id="adults" required min="1"> <!-- Nummer-input for voksne, minst 1 -->

            <label for="children">Antall barn:</label> <!-- Label for antall barn -->
            <input type="number" name="children" id="children" required min="0"> <!-- Nummer-input for barn, minst 0 -->

            <input type="submit" value="Sjekk tilgjengelighet"> <!-- Sendeknapp for skjemaet -->
        </form>

        <br><br> <!-- Linjeskift for å skape litt avstand i designet -->

        <!-- Her kan vi inkludere mer dynamisk innhold om nødvendig -->
    </div>

    <?php include '../Components/footer.php'; ?> <!-- Inkluderer footeren nederst på siden -->
</body>
</html>
