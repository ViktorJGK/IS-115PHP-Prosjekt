<?php
include "../db_connect.php";
include "../Components/header.php";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (isset($_POST['check_in'], $_POST['check_out'], $_POST['adults'], $_POST['children'])) {

    // Retrieve and sanitize input
    $check_in = $_POST['check_in'];
    $check_out = $_POST['check_out'];
    $adults = isset($_POST['adults']) ? intval($_POST['adults']) : null;
    $children = isset($_POST['children']) ? intval($_POST['children']) : null;

    // Ensure valid date range
    if (strtotime($check_in) >= strtotime($check_out)) {
        die("Ugyldig dato: Innsjekking må være før utsjekking.");
    }

    // Query to find available rooms
    $sql = "SELECT r.room_id, r.room_number, rt.type_name, rt.max_adults, rt.max_children, rt.price 
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

    // Display available rooms
    if ($result->num_rows > 0) {
        echo "<h2>Tilgjengelige rom:</h2>";
        echo "<form method='POST' action='book_room.php'>";
        echo "<table>";
        echo "<tr><th>Romnummer</th><th>Type</th><th>Maks voksne</th><th>Maks barn</th><th>Pris</th><th>Velg</th></tr>";

        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['room_number']) . "</td>";
            echo "<td>" . htmlspecialchars($row['type_name'] ?? 'Ikke tilgjengelig') . "</td>";
            echo "<td>" . htmlspecialchars($row['max_adults']) . "</td>";
            echo "<td>" . htmlspecialchars($row['max_children']) . "</td>";
            echo "<td>" . htmlspecialchars($row['price']) . " NOK</td>";
            echo "<td><button type='submit' name='room_id' value='" . htmlspecialchars($row['room_id']) . "'>Book</button></td>";
            echo "</tr>";
        }
        echo "</table>";

        // Hidden inputs to pass data to the next step
        echo "<input type='hidden' name='check_in' value='" . htmlspecialchars($check_in) . "'>";
        echo "<input type='hidden' name='check_out' value='" . htmlspecialchars($check_out) . "'>";
        echo "<input type='hidden' name='adults' value='" . htmlspecialchars($adults) . "'>";
        echo "<input type='hidden' name='children' value='" . htmlspecialchars($children) . "'>";
        echo "</form>";
    } else {
        echo "<p>Ingen rom tilgjengelig for den angitte perioden.</p>";
    }
    $stmt->close();

    // Query to show occupied rooms
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
} else {
    echo "<p>Ugyldig forespørsel.</p>";
}
$conn->close();
