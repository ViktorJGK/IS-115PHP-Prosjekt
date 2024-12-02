<?php
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $room_id = $_POST['room_id'];
    $unavailable_from = $_POST['unavailable_from'];
    $unavailable_to = $_POST['unavailable_to'];

    $sql = "UPDATE rooms 
            SET unavailable_from = ?, unavailable_to = ?, is_available = 0 
            WHERE room_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $unavailable_from, $unavailable_to, $room_id);

    if ($stmt->execute()) {
        header("Location: admin_page.php?message=Room updated successfully");
        exit;
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
