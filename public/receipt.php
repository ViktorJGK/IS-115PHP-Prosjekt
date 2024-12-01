<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
include "../db_connect.php";

// Sjekk at PDO er definert
$servername = "localhost";  // eller din databasetjener
$dbname = "bookingsystem";  // databasenavnet
$username = "root";  // brukernavn
$password = "";  // passord
$charset = 'utf8mb4';

$dsn = "mysql:host=$servername;dbname=$dbname;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    // Initialiser PDO
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}



$booking_id = $_GET['booking_id'];

// Hent bookingdetaljer
$query = "SELECT b.*, r.room_number, rt.type_name, rt.price FROM bookings b
          JOIN rooms r ON b.room_id = r.room_id
          JOIN room_types rt ON r.room_type_id = rt.room_type_id
          WHERE b.booking_id = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$booking_id]);
$booking = $stmt->fetch(PDO::FETCH_ASSOC);

// Beregn totalpris (hvis du ikke har det i databasen)
$check_in_date = new DateTime($booking['check_in']);
$check_out_date = new DateTime($booking['check_out']);
$diff = $check_in_date->diff($check_out_date);
$days = $diff->days;
$total_price = $days * $booking['price'];
?>

<div>
    <h2>Kvittering for Booking ID: <?php echo $booking['booking_id']; ?></h2>
    <p>Rom: <?php echo $booking['room_number']; ?> (<?php echo $booking['type_name']; ?>)</p>
    <p>Innsjekk: <?php echo $check_in_date->format('d-m-Y'); ?></p>
    <p>Utsjekk: <?php echo $check_out_date->format('d-m-Y'); ?></p>
    <p>Antall voksne: <?php echo $booking['adults']; ?></p>
    <p>Antall barn: <?php echo $booking['children']; ?></p>
    <p>Totalpris: <?php echo number_format($total_price, 2); ?> NOK</p>
</div>
