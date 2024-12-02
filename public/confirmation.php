<?php
include "../Components/header.php";

//bekrefter booking
if (isset($_GET['room_id'], $_GET['status']) && $_GET['status'] === 'success') {
    $room_id = htmlspecialchars($_GET['room_id']);
    echo "<h2>Booking bekreftet!</h2>";
    echo "<p>Rom $room_id er nå booket. Takk for din reservasjon!</p>";
} else {
    echo "<p>Ingen bookingdetaljer funnet.</p>";
}
?>
