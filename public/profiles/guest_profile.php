<?php
// Ensure the user is logged in as a User (not an admin)
if (!$userProfile instanceof User) {
    header("Location: ../../logout.php"); // Redirect to an error page if not a user
    exit;
}

// Fetch bookings for the logged-in user
$bookings = $userProfile->getUserBookings(); // No need to pass user ID explicitly now
?>

<div>
    <div>
        <h2>Your Home Page</h2>
        <p>Velkommen, <?php echo htmlspecialchars($userProfile->getUsername()); ?>!</p>
        <p>Email: <?php echo htmlspecialchars($userProfile->getEmail()); ?></p>
    </div>
    
    <!-- Display user's bookings -->
    <h3>Your Bookings</h3>
    <?php if (!empty($bookings)): ?>
        <ul>
            <?php foreach ($bookings as $booking): ?>
                <li>
                    Room: <?php echo htmlspecialchars($booking['room_number']); ?> 
                    (<?php echo htmlspecialchars($booking['check_in']); ?> - <?php echo htmlspecialchars($booking['check_out']); ?>)
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>You have no bookings at the moment.</p>
    <?php endif; ?>
</div>
