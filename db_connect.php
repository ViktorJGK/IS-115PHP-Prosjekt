<?php
$servername = "localhost";
$username = "root"; // Default username for XAMPP
$password = ""; // Default password for XAMPP
$dbname = "bookingsystem"; // database name

// Lager tilkobling
$conn = new mysqli($servername, $username, $password, $dbname);

// Sjekker tilkobling
if ($conn->connect_error) {
    die("Tilkobling feilet: " . $conn->connect_error . "Database: " .$dbname);
} else {
    
}
?>
