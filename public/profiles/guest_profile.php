<?php
// Ekstra sikkerhet mot uautorisert tilgang: Hvis brukeren ikke er logget inn som en User, sendes de til logout-siden.
if (!$userProfile instanceof User) {
    header("Location: ../../logout.php"); // Redirect til logg-ut siden
    exit(); // Stopper videre kjøring av skriptet
}

// Henter bookings (bestillinger) for den innloggede brukeren
$bookings = $userProfile->getUserBookings(); 
?>

<div>
    <div>
        <h2>Your Home Page</h2> <!-- Tittel for brukerens hjemside -->
        <p>Velkommen, <?php echo htmlspecialchars($userProfile->getUsername()); ?>!</p> <!-- Viser brukernavnet til den innloggede brukeren -->
        <p>Email: <?php echo htmlspecialchars($userProfile->getEmail()); ?></p> <!-- Viser e-posten til den innloggede brukeren -->
    </div>

    <h3>Dine Bookings</h3> <!-- Tittel for bookings seksjonen -->
    <?php if (!empty($bookings)): ?> <!-- Sjekker om brukeren har noen bestillinger -->
        <table border="1" cellpadding="5" cellspacing="0"> <!-- Lager en tabell for å vise bestillingsdata -->
            <thead>
                <tr>
                    <th>Booking ID</th> <!-- Kolonne for booking ID -->
                    <th>Rom</th> <!-- Kolonne for romnummer -->
                    <th>Rom Type</th> <!-- Kolonne for romtype -->
                    <th>Innsjekk</th> <!-- Kolonne for innsjekkingsdato -->
                    <th>Utsjekk</th> <!-- Kolonne for utsjekkingsdato -->
                    <th>Kvittering</th> <!-- Kolonne for lenke til kvittering -->
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bookings as $booking): ?> <!-- Looper gjennom alle bestillinger -->
                    <tr>
                        <td><?php echo htmlspecialchars($booking['booking_id']); ?></td> <!-- Viser booking ID -->
                        <td><?php echo htmlspecialchars($booking['room_number']); ?></td> <!-- Viser romnummer -->
                        <td><?php echo htmlspecialchars($booking['room_type']); ?></td> <!-- Viser romtype -->
                        <td><?php
                            $check_in_date = new DateTime($booking['check_in']); // Oppretter et DateTime objekt for innsjekkingsdato
                            echo $check_in_date->format('d-m-Y'); // Viser innsjekkingsdato i formatet dd-mm-yyyy
                        ?></td>
                        <td><?php
                            $check_out_date = new DateTime($booking['check_out']); // Oppretter et DateTime objekt for utsjekkingsdato
                            echo $check_out_date->format('d-m-Y'); // Viser utsjekkingsdato i formatet dd-mm-yyyy
                        ?></td>
                       
                        <td><a href="receipt.php?booking_id=<?php echo $booking['booking_id']; ?>">Vis Kvittering</a></td> <!-- Lenke for å vise kvittering for den spesifikke bookingen -->
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?> <!-- Hvis brukeren ikke har noen bookings -->
        <p>Du har ingen bookings for øyeblikket. Gå til forsiden for å se hvilke rom som er ledige.</p> <!-- Vist melding hvis ingen bestillinger er funnet -->
    <?php endif; ?>
</div>
