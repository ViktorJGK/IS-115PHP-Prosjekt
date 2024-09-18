<?php

$sql = "SELECT type_name, description, max_adults, max_children FROM room_types";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Output data i en HTML-tabell
    echo "<table border='1'><tr><th>Romtype</th><th>Beskrivelse</th><th>Maks Voksne</th><th>Maks Barn</th></tr>";
    while($row = $result->fetch_assoc()) {
        echo "<tr><td>" . $row["type_name"]. "</td><td>" . $row["description"]. "</td><td>" . $row["max_adults"]. "</td><td>" . $row["max_children"]. "</td></tr>";
    }
    echo "</table>";
} else {
    echo "Ingen romtyper funnet.";
}

?>