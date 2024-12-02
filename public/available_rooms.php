<?php
include "../db_connect.php"; // Inkluderer tilkobling til databasen
include "../Components/header.php"; // Inkluderer headeren for siden

ini_set('display_errors', 1); // Aktiverer visning av feil
ini_set('display_startup_errors', 1); // Aktiverer visning av oppstartfeil
error_reporting(E_ALL); // Viser alle feil

// Sjekker om nødvendige data er sendt via POST-metoden
if (isset($_POST['check_in'], $_POST['check_out'], $_POST['adults'], $_POST['children'])) {

    // Hent og valider brukerinput
    $check_in = $_POST['check_in'];
    $check_out = $_POST['check_out'];
    $adults = isset($_POST['adults']) ? intval($_POST['adults']) : null;
    $children = isset($_POST['children']) ? intval($_POST['children']) : null;

    // Sørger for at innsjekking er før utsjekking
    if (strtotime($check_in) >= strtotime($check_out)) {
        die("<p>Ugyldig dato: Innsjekking må være før utsjekking.</p>");
    }

    // Søker etter ledige rom i perioden basert på tilgjengelighet og bookingdata
    $sql_available = "
        SELECT r.room_id, r.room_number, rt.type_name, rt.description, rt.max_adults, rt.max_children, rt.price 
        FROM rooms r
        JOIN room_types rt ON r.room_type_id = rt.room_type_id
        WHERE r.is_available = 1
        AND (
            (r.unavailable_from IS NULL AND r.unavailable_to IS NULL)
            OR ('$check_in' NOT BETWEEN r.unavailable_from AND r.unavailable_to)
            OR ('$check_out' NOT BETWEEN r.unavailable_from AND r.unavailable_to)
        )
        AND rt.max_adults >= ? 
        AND rt.max_children >= ?
        AND r.room_id NOT IN (
            SELECT room_id FROM bookings 
            WHERE NOT (? >= check_out OR ? <= check_in)
        )
    ";

    // Forbereder og utfører spørringen
    $stmt_available = $conn->prepare($sql_available);
    $stmt_available->bind_param("iiss", $adults, $children, $check_in, $check_out);
    $stmt_available->execute();
    $result_available = $stmt_available->get_result();

    // Vis ledige rom i tabellen
    echo "<h2>Ledige rom:</h2>";
    if ($result_available->num_rows > 0) {
        echo "<form method='POST' action='book_room.php'>";
        echo "<table>";
        echo "<tr>
                <th>Romnummer</th>
                <th>Type</th>
                <th>Beskrivelse</th>
                <th>Maks voksne</th>
                <th>Maks barn</th>
                <th>Pris</th>
                <th>Velg</th>
              </tr>";
        // Loop gjennom resultatene og vis rommene
        while ($row = $result_available->fetch_assoc()) {
            echo "<tr>
                    <td>" . htmlspecialchars($row['room_number']) . "</td>
                    <td>" . htmlspecialchars($row['type_name'] ?? 'Ikke tilgjengelig') . "</td>
                    <td>" . htmlspecialchars($row['description'] ?? 'Ikke tilgjengelig') . "</td>
                    <td>" . htmlspecialchars($row['max_adults']) . "</td>
                    <td>" . htmlspecialchars($row['max_children']) . "</td>
                    <td>" . htmlspecialchars($row['price']) . " NOK</td>
                    <td><button type='submit' name='room_id' value='" . htmlspecialchars($row['room_id']) . "'>Book</button></td>
                  </tr>";
        }
        echo "</table>";

        // Skjulte felt for å sende data videre til book_room.php
        echo "<input type='hidden' name='check_in' value='" . htmlspecialchars($check_in) . "'>";
        echo "<input type='hidden' name='check_out' value='" . htmlspecialchars($check_out) . "'>";
        echo "<input type='hidden' name='adults' value='" . htmlspecialchars($adults) . "'>";
        echo "<input type='hidden' name='children' value='" . htmlspecialchars($children) . "'>";
        echo "</form>";
    } else {
        echo "<p>Ingen rom tilgjengelig for den angitte perioden.</p>";
    }
    $stmt_available->close();

    // Søker etter opptatte rom i perioden
    $sql_occupied = "
        SELECT r.room_number, rt.type_name 
        FROM rooms r
        JOIN room_types rt ON r.room_type_id = rt.room_type_id
        WHERE r.room_id IN (
            SELECT room_id FROM bookings 
            WHERE NOT (? >= check_out OR ? <= check_in)
        )
    ";

    // Forbereder og utfører spørringen for opptatte rom
    $stmt_occupied = $conn->prepare($sql_occupied);
    $stmt_occupied->bind_param("ss", $check_in, $check_out);
    $stmt_occupied->execute();
    $result_occupied = $stmt_occupied->get_result();

    // Vis opptatte rom i tabellen
    echo "<h2>Opptatte rom:</h2>";
    if ($result_occupied->num_rows > 0) {
        echo "<table>";
        echo "<tr><th>Romnummer</th><th>Type</th></tr>";
        // Loop gjennom resultatene og vis opptatte rom
        while ($row = $result_occupied->fetch_assoc()) {
            echo "<tr>
                    <td>" . htmlspecialchars($row['room_number']) . "</td>
                    <td>" . htmlspecialchars($row['type_name']) . "</td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>Ingen rom er opptatt i den angitte perioden.</p>";
    }
    $stmt_occupied->close();
} else {
    echo "<p>Ugyldig forespørsel. Sjekk alle nødvendige data.</p>";
}

// Lukk databaseforbindelsen
$conn->close();
?>

<style>
    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }

    th, td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: left;
    }

    th {
        background-color: #f4f4f4;
        font-weight: bold;
    }

    td:nth-child(3) {
        width: 40%; /* Øker bredden for beskrivelsen */
    }

    td:nth-child(4), td:nth-child(5), td:nth-child(6) {
        width: 10%; /* Begrenser bredden for maks voksne, barn og pris */
        text-align: center; /* Sentrerer tallverdier */
    }

    button {
        padding: 5px 10px;
        background-color: #007bff;
        color: #fff;
        border: none;
        border-radius: 3px;
        cursor: pointer;
    }

    button:hover {
        background-color: #0056b3;
    }
</style>
