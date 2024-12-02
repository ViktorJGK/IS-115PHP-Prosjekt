<?php
include "../db_connect.php";
include "../Components/header.php";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (isset($_POST['room_id'], $_POST['check_in'], $_POST['check_out'], $_POST['adults'], $_POST['children'])) {

    // Sanitize input
    $room_id = intval($_POST['room_id']);
    $check_in = $_POST['check_in'];
    $check_out = $_POST['check_out'];
    $adults = intval($_POST['adults']);
    $children = intval($_POST['children']);
    $user_id = $_SESSION['user_id'];

    // Sjekker gyldig dato
    if (strtotime($check_in) >= strtotime($check_out)) {
        die("Ugyldig dato: Innsjekking må være før utsjekking.");
    }

    try {
        $conn->begin_transaction();

        // Insert booking inn i databasen
        $sql = "INSERT INTO bookings (user_id, room_id, check_in, check_out, adults, children, created_at)
                VALUES (?, ?, ?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iissii", $user_id, $room_id, $check_in, $check_out, $adults, $children);
        $stmt->execute();

        // oppdaterer room availability
        $sql_update = "UPDATE rooms SET is_available = 0 WHERE room_id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("i", $room_id);
        $stmt_update->execute();

        $conn->commit();

        // Hvis godkjent sender til siden confirmation.php, med melding
        header("Location: confirmation.php?room_id=$room_id&status=success");
        exit;
    } catch (Exception $e) {
        $conn->rollback();
        die("Feil under booking: " . $e->getMessage());
    }
} else {
    echo "<p>Ugyldig forespørsel.</p>";
}
$conn->close();
