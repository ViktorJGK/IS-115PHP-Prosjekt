<?php
// ekstra sikkerhet mot uautorisert tilgang
if (!$userProfile instanceof User) {
    header("Location: ../../logout.php");
    exit;
}

// Henter bookings for innlogget bruker
$bookings = $userProfile->getUserBookings();
?>

<div>
    <div>
        <h2>Your Home Page</h2>
        <p>Velkommen, <?php echo htmlspecialchars($userProfile->getUsername()); ?>!</p>
        <p>Email: <?php echo htmlspecialchars($userProfile->getEmail()); ?></p>
    </div>

    <h3>Dine Bookings</h3>
    <?php if (!empty($bookings)): ?>
        <table border="1" cellpadding="5" cellspacing="0">
            <thead>
                <tr>
                    <th>Booking ID</th>
                    <th>Rom</th>
                    <th>Rom Type</th>
                    <th>Innsjekk</th>
                    <th>Utsjekk</th>
                    <th>Kvittering</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bookings as $booking): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($booking['booking_id']); ?></td>
                        <td><?php echo htmlspecialchars($booking['room_number']); ?></td>
                        <td><?php echo htmlspecialchars($booking['room_type']); ?></td>
                        <td><?php
                            $check_in_date = new DateTime($booking['check_in']);
                            echo $check_in_date->format('d-m-Y');
                        ?></td>
                        <td><?php
                            $check_out_date = new DateTime($booking['check_out']);
                            echo $check_out_date->format('d-m-Y');
                        ?></td>
                       
                        <td><a href="receipt.php?booking_id=<?php echo $booking['booking_id']; ?>">Vis Kvittering</a></td>

                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Du har ingen bookings for øyeblikket. Gå til forsiden for å se hvilke rom som er ledige.</p>
    <?php endif; ?>
</div>
