<?php
include "../db_connect.php";
include "../Components/header.php";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

($_POST);

if (isset($_POST['check_in'], $_POST['check_out'], $_POST['adults'], $_POST['children'])) {
    $check_in = $_POST['check_in'];
    $check_out = $_POST['check_out'];
    $adults = isset($_POST['adults']) ? intval($_POST['adults']) : null;
    $children = isset($_POST['children']) ? intval($_POST['children']) : null;

    echo "Innsjekkingsdato: " . $check_in . "<br>";
    echo "Utsjekkingsdato: " . $check_out . "<br>";

    echo"Voksne" . $adults . "<br>" ;
    echo"barn" . $children . "<br>";

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

    if ($result->num_rows > 0) {
        echo "<h2>Tilgjengelige rom:</h2>";
        echo "<table>";
        echo "<tr><th>Romnummer</th><th>Type</th><th>Maks voksne</th><th>Maks barn</th></tr>";

        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['room_number']) . "</td>";
            echo "<td>" . htmlspecialchars($row['type_name'] ?? 'Ikke tilgjengelig') . "</td>";
            echo "<td>" . htmlspecialchars($row['max_adults']) . "</td>";
            echo "<td>" . htmlspecialchars($row['max_children']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>Ingen rom tilgjengelig for den angitte perioden.</p>";
    }

    $stmt->close();
} else {
    echo "<p>Ugyldig forespørsel.</p>";
};

$conn->close();
?>
