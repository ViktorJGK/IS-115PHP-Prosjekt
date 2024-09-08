<?php
function addRoomType($type_name, $max_adults, $max_children) {
    global $conn;
    $sql = "INSERT INTO room_types (type_name, max_adults, max_children) VALUES ('$type_name', '$max_adults', '$max_children')";
    if ($conn->query($sql) === TRUE) {
        echo "Room type added successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

function addRoom($room_number, $type_id, $status) {
    global $conn;
    $sql = "INSERT INTO rooms (room_number, type_id, status) VALUES ('$room_number', '$type_id', '$status')";
    if ($conn->query($sql) === TRUE) {
        echo "Room added successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>
