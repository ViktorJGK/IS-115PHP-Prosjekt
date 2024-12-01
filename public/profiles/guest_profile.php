<?php
// Sikrer at bruker er admin
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
        <ul>
            <?php foreach ($bookings as $booking): ?>
                <li>
                    Room: <?php echo htmlspecialchars($booking['room_number']); ?>
                    <?php echo htmlspecialchars($booking['room_type']); ?>
                    (<?php
                        // Convert check_in and check_out to dd-mm-yyyy format
                        $check_in_date = new DateTime($booking['check_in']);
                        $check_out_date = new DateTime($booking['check_out']);

                        echo $check_in_date->format('d-m-Y') . ' - ' . $check_out_date->format('d-m-Y');
                        ?>)
                </li>
            <?php endforeach; ?>

        </ul>
    <?php else: ?>
        <p> Du har ingen bookings for øyeblikket. Gå til forsiden for å se hvilke rom som er ledige. </p>
    <?php endif; ?>
</div>