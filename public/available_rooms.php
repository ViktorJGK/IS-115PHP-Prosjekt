<?php
function getAvailableRooms($check_in, $check_out, $adults, $children) {
    global $conn;
    $sql = "SELECT * FROM rooms WHERE id NOT IN (SELECT room_id FROM reservations WHERE check_in <= '$check_out' AND check_out >= '$check_in') AND type_id IN (SELECT id FROM room_types WHERE max_adults >= '$adults' AND max_children >= '$children')";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo "Room number: " . $row["room_number"]. " - Type: " . $row["type_id"]. " - Status: " . $row["status"]. "<br>";
        }
    } else {
        echo "No available rooms";
    }
}
?>
