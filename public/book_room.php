<?php
function bookRoom($room_id, $user_id, $check_in, $check_out, $adults, $children) {
    global $conn;
    $sql = "INSERT INTO reservations (room_id, user_id, check_in, check_out, adults, children) VALUES ('$room_id', '$user_id', '$check_in', '$check_out', '$adults', '$children')";
    if ($conn->query($sql) === TRUE) {
        echo "Room booked successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>
