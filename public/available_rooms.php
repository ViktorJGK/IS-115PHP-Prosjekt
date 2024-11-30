<?php
// Inkluderer filen som kobler til databasen
include "../db_connect.php";

// Inkluderer header-komponenten (HTML-header, metadata, eller navigasjonsfelt)
include "../Components/header.php";

// Starter en ny eller eksisterende sesjon
session_start();

// Aktiverer visning av PHP-feil for feilsøking
ini_set('display_errors', 1); // Viser alle PHP-feilmeldinger
ini_set('display_startup_errors', 1); // Viser oppstartsfeil i PHP
error_reporting(E_ALL); // Rapporterer alle typer feil

// Dumper innholdet i $_POST for å hjelpe med feilsøking
var_dump($_POST);

// Sjekker om nødvendige felt i skjemaet er sendt via POST-metoden
if (isset($_POST['check_in'], $_POST['check_out'], $_POST['adults'], $_POST['children'])) {
    // Henter innsjekkingsdato fra POST-data
    $check_in = $_POST['check_in'];

    // Henter utsjekkingsdato fra POST-data
    $check_out = $_POST['check_out'];

    // Konverterer antall voksne fra POST til et heltall (sikrer riktig datatype)
    $adults = isset($_POST['adults']) ? intval($_POST['adults']) : null;

    // Konverterer antall barn fra POST til et heltall (sikrer riktig datatype)
    $children = isset($_POST['children']) ? intval($_POST['children']) : null;

    // Viser innsjekkingsdato og utsjekkingsdato (for feilsøking)
    echo "Innsjekkingsdato: " . $check_in . "<br>";
    echo "Utsjekkingsdato: " . $check_out . "<br>";

    // Dumper variablene for feilsøking
    var_dump($check_in);
    var_dump($check_out);
    var_dump($adults);
    var_dump($children);

    // SQL-spørring for å finne tilgjengelige rom
    $sql = "SELECT r.room_id, rt.type_name, rt.max_adults, rt.max_children 
            FROM rooms r
            JOIN room_types rt ON r.room_type_id = rt.room_type_id
            WHERE r.room_id NOT IN (
                SELECT room_id FROM bookings 
                WHERE (? < check_out AND ? > check_in)
            )
            AND rt.max_adults >= ? 
            AND rt.max_children >= ?
            AND r.is_available = 1";

    // Forbereder SQL-spørringen for å forhindre SQL-injeksjon
    $stmt = $conn->prepare($sql);

    // Binder parametere til den forberedte spørringen (plassholderne i spørringen)
    $stmt->bind_param("ssii", $check_in, $check_out, $adults, $children);

    // Utfører den forberedte spørringen
    $stmt->execute();

    // Henter resultatet av spørringen
    $result = $stmt->get_result();

    // Sjekker om noen tilgjengelige rom ble funnet
    if ($result->num_rows > 0) {
        // Viser en overskrift for tilgjengelige rom
        echo "<h2>Tilgjengelige rom:</h2>";

        // Starter en tabell for å vise rominformasjon
        echo "<table>";
        echo "<tr><th>Romnummer</th><th>Type</th><th>Maks voksne</th><th>Maks barn</th></tr>";

        // Itererer gjennom alle resultatene fra spørringen
        while ($row = $result->fetch_assoc()) {
            // Starter en ny rad i tabellen
            echo "<tr>";
            // Viser romnummer, sikret mot XSS-angrep med htmlspecialchars
            echo "<td>" . htmlspecialchars($row['room_id']) . "</td>";
            // Viser romtypen (med en fallback for 'Ikke tilgjengelig' hvis verdien er null)
            echo "<td>" . htmlspecialchars($row['type_name'] ?? 'Ikke tilgjengelig') . "</td>";
            // Viser maks antall voksne
            echo "<td>" . htmlspecialchars($row['max_adults']) . "</td>";
            // Viser maks antall barn
            echo "<td>" . htmlspecialchars($row['max_children']) . "</td>";
            // Lukker raden
            echo "</tr>";
        }
        // Lukker tabellen
        echo "</table>";
    } else {
        // Viser en melding hvis ingen rom er tilgjengelige
        echo "<p>Ingen rom tilgjengelig for den angitte perioden.</p>";
    }

    // Lukker den forberedte spørringen
    $stmt->close();
} else {
    // Viser en melding hvis forespørselen er ugyldig (felter mangler)
    echo "<p>Ugyldig forespørsel.</p>";
}

// Lukker databaseforbindelsen
$conn->close();
?>
