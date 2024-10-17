<?php
include "../db_connect.php";
include "../Components/header.php";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

($_POST);

if (isset($_POST['check_in'], $_POST['check_out'], $_POST['adults'], $_POST['children'])) {

    // Henter verdier fra POST-requesten og konverterer til passende datatyper
    $check_in = $_POST['check_in'];
    $check_out = $_POST['check_out'];
    $adults = isset($_POST['adults']) ? intval($_POST['adults']) : null;
    $children = isset($_POST['children']) ? intval($_POST['children']) : null;

    // Viser innsjekkingsdato, utsjekkingsdato, antall voksne og barn
    echo "Innsjekkingsdato: " . $check_in . "<br>";
    echo "Utsjekkingsdato: " . $check_out . "<br>";
    echo "Voksne" . $adults . "<br>";
    echo "barn" . $children . "<br>";


    // SQL-spørring for å finne tilgjengelige rom
    // Finner rom som ikke er booket i den angitte perioden
    // og som har plass til det angitte antall voksne og barn

    $sql = "SELECT r.room_number, rt.type_name, rt.max_adults, rt.max_children 
            FROM rooms r
            JOIN room_types rt ON r.room_type_id = rt.room_type_id
            WHERE r.room_id NOT IN (
                SELECT room_id FROM bookings 
                WHERE (? < check_out AND ? > check_in)
            )
            AND rt.max_adults >= ? 
            AND rt.max_children >= ?
            AND r.is_available = 1";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssii", $check_in, $check_out, $adults, $children);
    $stmt->execute();

    $result = $stmt->get_result();

    // Sjekker om det finnes tilgjengelige rom
    if ($result->num_rows > 0) {
        // Viser en tabell over tilgjengelige rom
        echo "<h2>Tilgjengelige rom:</h2>";
        echo "<table>";
        echo "<tr><th>Romnummer</th><th>Type</th><th>Maks voksne</th><th>Maks barn</th><th>Pris</th></tr>";

        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['room_number']) . "</td>";
            echo "<td>" . htmlspecialchars($row['type_name'] ?? 'Ikke tilgjengelig') . "</td>";
            echo "<td>" . htmlspecialchars($row['max_adults']) . "</td>";
            echo "<td>" . htmlspecialchars($row['max_children']) . "</td>";
            echo "<td>" . (isset($row['price']) ? htmlspecialchars($row['price']) . " NOK" : 'Pris ikke tilgjengelig') . "</td>";            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>Ingen rom tilgjengelig for den angitte perioden.</p>";
    }    

    $stmt->close();
} else {
    echo "<p>Ugyldig forespørsel.</p>";
};

//spørring for vise om noen rom er opptatte

$sql_occupied = "SELECT r.room_number, rt.type_name 
                 FROM rooms r
                 JOIN room_types rt ON r.room_type_id = rt.room_type_id
                 WHERE r.room_id IN (
                     SELECT room_id FROM bookings 
                     WHERE (? < check_out AND ? > check_in)
                 )";
                 
$stmt_occupied = $conn->prepare($sql_occupied);
$stmt_occupied->bind_param("ss", $check_in, $check_out);
$stmt_occupied->execute();
$result_occupied = $stmt_occupied->get_result();

if ($result_occupied->num_rows > 0) {
    echo "<h2>Opptatte rom:</h2>";
    echo "<table>";
    echo "<tr><th>Romnummer</th><th>Type</th></tr>";
    while ($row = $result_occupied->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['room_number']) . "</td>";
        echo "<td>" . htmlspecialchars($row['type_name']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>Ingen rom er opptatt i den angitte perioden.</p>";
}
$stmt_occupied->close();


$conn->close();
?>