<?php
// For error handling direkte i nettleser
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include "../Components/header.php";
include '../db_connect.php';
?>

<!DOCTYPE html>
<html lang="no">

<head>
    <meta charset="UTF-8">
    <title>Rombooking System</title>
    <link rel="stylesheet" href="../css/index.css">
    <link rel="stylesheet" href="../css/footer.css"> <!-- Legg til footer-styling -->
</head>

<body>
    <div class="container">
        <h1>Velkommen til vårt motell</h1>
        <h2>Søk etter tilgjengelige rom</h2>
        <form action="available_rooms.php" method="POST">
            <label for="check_in">Innsjekkingsdato:</label>
            <input type="date" name="check_in" id="check_in" required>

            <label for="check_out">Utsjekkingsdato:</label>
            <input type="date" name="check_out" id="check_out" required>

            <label for="adults">Antall voksne:</label>
            <input type="number" name="adults" id="adults" required min="1">

            <label for="children">Antall barn:</label>
            <input type="number" name="children" id="children" required min="0">

            <input type="submit" value="Sjekk tilgjengelighet">
        </form>


        <br><br>

        <!-- Du kan inkludere mer dynamisk innhold her hvis ønskelig -->
    </div>
    <br>
    <br>
    <div class="container">
        <?php

        $sql = "SELECT type_name, description, max_adults, max_children FROM room_types";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            // Output data i en HTML-tabell
            echo "<table border='1'><tr><th>Romtype</th><th>Beskrivelse</th><th>Maks Voksne</th><th>Maks Barn</th></tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr><td>" . $row["type_name"] . "</td><td>" . $row["description"] . "</td><td>" . $row["max_adults"] . "</td><td>" . $row["max_children"] . "</td></tr>";
            }
            echo "</table>";
        } else {
            echo "Ingen romtyper funnet.";
        }

        ?>
    </div>

    <?php include '../Components/footer.php'; ?> <!-- Inkluder footeren etter innholdet -->
</body>

</html>