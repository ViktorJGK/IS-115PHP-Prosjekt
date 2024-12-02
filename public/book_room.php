<?php
include "../db_connect.php";  // Inkluderer databasen tilkoblingsfilen
include "../Components/header.php";  // Inkluderer header-komponenten

// Konfigurerer PHP for å vise alle feil for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Sjekker om nødvendige data er sendt via POST
if (isset($_POST['room_id'], $_POST['check_in'], $_POST['check_out'], $_POST['adults'], $_POST['children'])) {

    // Saniterer brukerinput (gjør den trygg for bruk i SQL)
    $room_id = intval($_POST['room_id']);  // Rom-ID
    $check_in = $_POST['check_in'];  // Innsjekkingsdato
    $check_out = $_POST['check_out'];  // Utsjekkingsdato
    $adults = intval($_POST['adults']);  // Antall voksne
    $children = intval($_POST['children']);  // Antall barn
    $user_id = $_SESSION['user_id'];  // Bruker-ID fra sesjonen

    // Sjekker at innsjekkingsdato er før utsjekkingsdato
    if (strtotime($check_in) >= strtotime($check_out)) {
        die("Ugyldig dato: Innsjekking må være før utsjekking.");
    }

    try {
        // Starter en database-transaksjon for å sikre at begge operasjonene (booking og oppdatering) skjer samtidig
        $conn->begin_transaction();

        // Legger inn booking i databasen
        $sql = "INSERT INTO bookings (user_id, room_id, check_in, check_out, adults, children, created_at)
                VALUES (?, ?, ?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);  // Forbereder SQL-spørring
        $stmt->bind_param("iissii", $user_id, $room_id, $check_in, $check_out, $adults, $children);  // Binder parametre
        $stmt->execute();  // Kjører spørringen

        // Oppdaterer rommets tilgjengelighet i databasen (setter rommet som ikke tilgjengelig)
        $sql_update = "UPDATE rooms SET is_available = 0 WHERE room_id = ?";
        $stmt_update = $conn->prepare($sql_update);  // Forbereder SQL-spørring
        $stmt_update->bind_param("i", $room_id);  // Binder rom-ID som parameter
        $stmt_update->execute();  // Kjører spørringen

        // Bekrefter transaksjonen, det betyr at både booking og romoppdatering er vellykket
        $conn->commit();

        // Hvis alt er vellykket, omdirigerer vi til bekreftelsessiden med suksessmelding
        header("Location: confirmation.php?room_id=$room_id&status=success");
        exit;  // Stopper videre utførelse av skriptet

    } catch (Exception $e) {
        // Hvis det oppstår en feil, ruller vi tilbake transaksjonen og viser feilmeldingen
        $conn->rollback();
        die("Feil under booking: " . $e->getMessage());
    }
} else {
    // Hvis ikke alle nødvendige data er sendt, vis en feilmelding
    echo "<p>Ugyldig forespørsel.</p>";
}

$conn->close();  // Lukker tilkoblingen til databasen
?>
