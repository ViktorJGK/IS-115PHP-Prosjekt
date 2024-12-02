<?php
// Inkluderer tilkoblingen til databasen
include 'db_connect.php';

// Sjekker om skjemaet er sendt via POST-metoden
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Henter verdiene fra skjemaet
    $room_id = $_POST['room_id'];
    $unavailable_from = $_POST['unavailable_from'];
    $unavailable_to = $_POST['unavailable_to'];

    // SQL-spørring for å oppdatere rommets tilgjengelighet
    $sql = "UPDATE rooms 
            SET unavailable_from = ?, unavailable_to = ?, is_available = 0 
            WHERE room_id = ?";
    
    // Forbereder spørringen
    $stmt = $conn->prepare($sql);
    // Binder de hentede verdiene til spørringen
    $stmt->bind_param("ssi", $unavailable_from, $unavailable_to, $room_id);

    // Utfører spørringen og håndterer resultatet
    if ($stmt->execute()) {
        // Hvis spørringen lykkes, omdirigerer til admin-siden med en suksessmelding
        header("Location: admin_page.php?message=Room updated successfully");
        exit;
    } else {
        // Hvis det oppstår en feil, vises feilmeldingen
        echo "Error: " . $conn->error;
    }
}
?>
