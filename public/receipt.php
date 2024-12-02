<?php
ini_set('display_errors', 1);  // Aktiverer visning av feil for debugging
error_reporting(E_ALL);  // Viser alle typer feil i PHP

// Inkluderer tilkoblingen til databasen
include "../db_connect.php";

// Definerer databaseinnstillingene
$servername = "localhost";  // Navnet på serveren der databasen er plassert (ofte localhost)
$dbname = "bookingsystem";  // Navnet på databasen vi skal koble til
$username = "root";  // Brukernavnet for å koble til databasen (her er det 'root' for lokal utvikling)
$password = "";  // Passordet for databasen (tomt passord i dette tilfellet)
$charset = 'utf8mb4';  // Tegnsettet som brukes for å håndtere tegn på forskjellige språk

// DSN (Data Source Name) som brukes til å definere tilkoblingen
$dsn = "mysql:host=$servername;dbname=$dbname;charset=$charset";

// Opsjoner for PDO-tilkoblingen
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,  // Slår på feilhåndtering med unntak
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,  // Henter resultatene som assosiative arrays
    PDO::ATTR_EMULATE_PREPARES   => false,  // Slår av emulering av SQL-spørringer (bruke ekte forberedte utsagn)
];

try {
    // Forsøker å koble til databasen ved hjelp av PDO (PHP Data Objects)
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (\PDOException $e) {
    // Hvis tilkoblingen mislykkes, kastes et unntak med feilmeldingen
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}

// Henter booking-ID fra URL (via GET-parameter)
$booking_id = $_GET['booking_id'];

// Henter detaljene om bookingen fra databasen ved hjelp av en SQL-spørring
$query = "SELECT b.*, r.room_number, rt.type_name, rt.price FROM bookings b
          JOIN rooms r ON b.room_id = r.room_id
          JOIN room_types rt ON r.room_type_id = rt.room_type_id
          WHERE b.booking_id = ?";
$stmt = $pdo->prepare($query);  // Forbereder SQL-spørringen
$stmt->execute([$booking_id]);  // Kjører spørringen med booking-ID som parameter
$booking = $stmt->fetch(PDO::FETCH_ASSOC);  // Henter resultatet som et assosiativt array

// Beregner totalprisen for bookingen basert på innsjekking og utsjekking
$check_in_date = new DateTime($booking['check_in']);  // Konverterer innsjekkingsdatoen til DateTime-objekt
$check_out_date = new DateTime($booking['check_out']);  // Konverterer utsjekkingsdatoen til DateTime-objekt
$diff = $check_in_date->diff($check_out_date);  // Beregner forskjellen mellom innsjekking og utsjekking
$days = $diff->days;  // Antall dager mellom innsjekking og utsjekking
$total_price = $days * $booking['price'];  // Beregner totalpris ved å multiplisere antall dager med pris per dag
?>

<!-- HTML for å vise kvittering for bookingen -->
<div>
    <h2>Kvittering for Booking ID: <?php echo $booking['booking_id']; ?></h2>  <!-- Viser booking-ID -->
    <p>Rom: <?php echo $booking['room_number']; ?> (<?php echo $booking['type_name']; ?>)</p>  <!-- Viser romnummer og romtype -->
    <p>Innsjekk: <?php echo $check_in_date->format('d-m-Y'); ?></p>  <!-- Viser innsjekkingsdato -->
    <p>Utsjekk: <?php echo $check_out_date->format('d-m-Y'); ?></p>  <!-- Viser utsjekkingsdato -->
    <p>Antall voksne: <?php echo $booking['adults']; ?></p>  <!-- Viser antall voksne gjester -->
    <p>Antall barn: <?php echo $booking['children']; ?></p>  <!-- Viser antall barn -->
    <p>Totalpris: <?php echo number_format($total_price, 2); ?> NOK</p>  <!-- Viser totalprisen i NOK med to desimaler -->
</div>
