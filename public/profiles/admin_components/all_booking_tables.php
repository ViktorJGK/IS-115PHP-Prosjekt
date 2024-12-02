<?php
// Set limits for bookings and use the Admin's method to get bookings
$limit = 10;
$offset = 0;
$allBookings = $userProfile->getAllBookings($limit, $offset);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['cancel_edit'])) {
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
    if (isset($_POST['update_room'])) {
        $room_id = (int) $_POST['room_id'];
        $is_available = (int) $_POST['is_available'];
        $unavailable_from = $_POST['unavailable_from'] ?: null;
        $unavailable_to = $_POST['unavailable_to'] ?: null;

        $stmt = $conn->prepare("UPDATE rooms 
                                SET is_available = ?, unavailable_from = ?, unavailable_to = ? 
                                WHERE room_id = ?");
        $stmt->bind_param("issi", $is_available, $unavailable_from, $unavailable_to, $room_id);
        if (!$stmt->execute()) {
            die("Error updating room: " . $stmt->error);
        }

        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }

    if (isset($_POST['delete_booking_id'])) {
        $booking_id = (int) $_POST['delete_booking_id'];

        // Begin a transaction
        $conn->begin_transaction();

        try {
            // Get the room ID associated with the booking
            $stmt = $conn->prepare("SELECT room_id FROM bookings WHERE booking_id = ?");
            $stmt->bind_param("i", $booking_id);
            if (!$stmt->execute()) {
                throw new Exception("Error fetching room ID: " . $stmt->error);
            }
            $result = $stmt->get_result();
            $room_id = $result->fetch_assoc()['room_id'];

            // Delete the booking
            $stmt = $conn->prepare("DELETE FROM bookings WHERE booking_id = ?");
            $stmt->bind_param("i", $booking_id);
            if (!$stmt->execute()) {
                throw new Exception("Error deleting booking: " . $stmt->error);
            }

            // Update the room's availability
            $stmt = $conn->prepare("UPDATE rooms SET is_available = 1 WHERE room_id = ?");
            $stmt->bind_param("i", $room_id);
            if (!$stmt->execute()) {
                throw new Exception("Error updating room availability: " . $stmt->error);
            }

            // Commit the transaction
            $conn->commit();
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;
        } catch (Exception $e) {
            // Rollback the transaction on error
            $conn->rollback();
            die("Error processing deletion: " . $e->getMessage());
        }
    }
}
?>

<h3>All Bookings</h3>
<table>
    <thead>
        <tr>
            <th>Booking ID</th>
            <th>Username</th>
            <th>Room Type</th>
            <th>Check-in</th>
            <th>Check-out</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($allBookings)): ?>
            <?php foreach ($allBookings as $booking): ?>
                <tr>
                    <td><?php echo htmlspecialchars($booking['booking_id']); ?></td>
                    <td><?php echo htmlspecialchars($booking['username']); ?></td>
                    <td><?php echo htmlspecialchars($booking['room_type']); ?></td>
                    <td><?php echo htmlspecialchars($booking['check_in']); ?></td>
                    <td><?php echo htmlspecialchars($booking['check_out']); ?></td>
                    <td>
                        <form action="" method="post" style="display:inline-block;">
                            <input type="hidden" name="delete_booking_id" value="<?php echo htmlspecialchars($booking['booking_id']); ?>">
                            <button type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="6">No bookings found.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
