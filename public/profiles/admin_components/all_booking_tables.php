<?php
ob_start(); // Start output buffering

include_once '../Components/functions/user_functions.php'; // Inkluderer nødvendige funksjoner som kan være relatert til brukerhåndtering og autentisering

// Sett grense og offset for paginering av bookingene
$limit = 10; // Maksimalt antall bookingoppføringer per side
$offset = 0; // Startpunkt for oppføringer
$allBookings = $userProfile->getAllBookings($limit, $offset); // Henter alle bookingene

// Sjekk om skjemaet er sendt via en POST-forespørsel
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Hvis brukeren klikker på "Avbryt" når de redigerer, oppdaterer vi siden
    if (isset($_POST['cancel_edit'])) {
        header('Location: ' . $_SERVER['PHP_SELF']); // Sender brukeren tilbake til samme side
        exit;
    }

    // Hvis brukeren oppdaterer et roms tilgjengelighet
    if (isset($_POST['update_room'])) {
        $room_id = (int) $_POST['room_id']; // ID for rommet som skal oppdateres
        $is_available = (int) $_POST['is_available']; // Ny tilgjengelighetsstatus
        $unavailable_from = $_POST['unavailable_from'] ?: null; // Startdato for utilgjengelighet
        $unavailable_to = $_POST['unavailable_to'] ?: null; // Sluttdato for utilgjengelighet

        // SQL-spørring for å oppdatere rommets tilgjengelighet
        $stmt = $conn->prepare("UPDATE rooms SET is_available = ?, unavailable_from = ?, unavailable_to = ? WHERE room_id = ?");
        $stmt->bind_param("issi", $is_available, $unavailable_from, $unavailable_to, $room_id);
        if (!$stmt->execute()) {
            die("Feil ved oppdatering av rom: " . $stmt->error); // Avbryt hvis en feil oppstår
        }

        // Oppdater siden etter vellykket endring
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }

    // Hvis brukeren sletter en booking
    if (isset($_POST['delete_booking_id'])) {
        $booking_id = (int) $_POST['delete_booking_id']; // ID for booking som skal slettes

        // Starter en transaksjon for å sikre dataintegritet
        $conn->begin_transaction();

        try {
            // Hent rom-ID knyttet til bookingen
            $stmt = $conn->prepare("SELECT room_id FROM bookings WHERE booking_id = ?");
            $stmt->bind_param("i", $booking_id);
            if (!$stmt->execute()) {
                throw new Exception("Feil ved henting av rom-ID: " . $stmt->error);
            }
            $result = $stmt->get_result();
            $room_id = $result->fetch_assoc()['room_id'];

            // Slett selve bookingen
            $stmt = $conn->prepare("DELETE FROM bookings WHERE booking_id = ?");
            $stmt->bind_param("i", $booking_id);
            if (!$stmt->execute()) {
                throw new Exception("Feil ved sletting av booking: " . $stmt->error);
            }

            // Oppdater rommets tilgjengelighet til "ledig"
            $stmt = $conn->prepare("UPDATE rooms SET is_available = 1 WHERE room_id = ?");
            $stmt->bind_param("i", $room_id);
            if (!$stmt->execute()) {
                throw new Exception("Feil ved oppdatering av romtilgjengelighet: " . $stmt->error);
            }

            // Fullfør transaksjonen
            $conn->commit();
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;
        } catch (Exception $e) {
            // Rull tilbake transaksjonen ved feil
            $conn->rollback();
            die("Feil ved behandling av sletting: " . $e->getMessage());
        }
    }
}

ob_end_flush(); // Send all output
?>

<h3>Alle Bookinger</h3>
<table>
    <thead>
        <tr>
            <!-- Overskrifter for bookingtabellen -->
            <th>Booking ID</th>
            <th>Brukernavn</th>
            <th>Romtype</th>
            <th>Innsjekk</th>
            <th>Utsjekk</th>
            <th>Handlinger</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($allBookings)): ?>
            <!-- Hvis det finnes bookingoppføringer, vis dem i tabellen -->
            <?php foreach ($allBookings as $booking): ?>
                <tr>
                    <td><?php echo htmlspecialchars($booking['booking_id']); ?></td> <!-- Booking-ID -->
                    <td><?php echo htmlspecialchars($booking['username']); ?></td> <!-- Brukernavn -->
                    <td><?php echo htmlspecialchars($booking['room_type']); ?></td> <!-- Romtype -->
                    <td><?php echo htmlspecialchars($booking['check_in']); ?></td> <!-- Innsjekksdato -->
                    <td><?php echo htmlspecialchars($booking['check_out']); ?></td> <!-- Utsjekksdato -->
                    <td>
                        <!-- Skjema for å slette en booking -->
                        <form action="" method="post" style="display:inline-block;">
                            <input type="hidden" name="delete_booking_id" value="<?php echo htmlspecialchars($booking['booking_id']); ?>">
                            <button type="submit">Slett</button> <!-- Sletteknapp -->
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <!-- Hvis det ikke finnes noen bookingoppføringer, vis en melding -->
            <tr>
                <td colspan="6">Ingen booking funnet.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
