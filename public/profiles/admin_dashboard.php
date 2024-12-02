<?php
ob_start(); // Starter output buffering. Dette gjør at vi kan kontrollere hva som sendes til nettleseren før det faktisk vises.

include_once '../Components/functions/user_functions.php'; // Inkluderer nødvendige funksjoner som kan være relatert til brukerhåndtering og autentisering.

// Starter en ny sesjon og sjekker at brukeren er autentisert som en Admin.
if (!isset($userProfile) || !$userProfile instanceof Admin) {
    header("Location: ../../logout.php"); // Hvis brukeren ikke er logget inn som admin, sendes de til logg-ut-siden.
    exit; // Stopp videre utførelse av skriptet etter redirect.
}

?>

<html lang="en"> <!-- Setter språket for HTML-dokumentet til engelsk -->
<head>
    <link rel="stylesheet" href="../css/admin.css"> <!-- Kobler til administrasjonsstilarket for å style dashboardet -->
    <title>Admin Dashboard</title> <!-- Tittel på nettsiden som vises i nettleserfanen -->
</head>
<body>
    <div>
        <div>
            <h2>Admin Dashboard</h2> <!-- Overskrift for admin-dashboardet -->
            <p>Welcome, <?php echo htmlspecialchars($userProfile->getUsername()); ?>!</p> <!-- Viser brukernavnet til den innloggede adminen -->
            <p>Email: <?php echo htmlspecialchars($userProfile->getEmail()); ?></p> <!-- Viser e-posten til den innloggede adminen -->
            <br>

            <?php
            include 'admin_components/all_user_tables.php'; // Inkluderer brukerrelaterte tabeller for å vise brukerinformasjon
            echo "<br>";
            ?>

            <?php
            include 'admin_components/all_booking_tables.php'; // Inkluderer booking-relaterte tabeller for å vise bookinginformasjon
            echo "<br>";
            ?>

            <?php
            include 'admin_components/all_roomtypes_tables.php'; // Inkluderer romtype-relaterte tabeller for å vise informasjon om romtyper
            echo "<br>";
            ?>

            <?php 
            include 'admin_components/all_rooms.php'; // Inkluderer romrelaterte tabeller for å vise rominformasjon
            echo "<br>";
            ?>

        </div>
    </div>
</body>
</html>

<?php ob_end_flush(); // Sender alt innhold som er samlet i output bufferet til nettleseren. ?>
