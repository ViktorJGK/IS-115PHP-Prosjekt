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

    if (isset($_POST['save_booking'], $_POST['booking_id'], $_POST['check_in'], $_POST['check_out'], $_POST['room_type'])) {
        $booking_id = (int) $_POST['booking_id'];
        $check_in = $_POST['check_in'];
        $check_out = $_POST['check_out'];
        $room_type = $_POST['room_type'];

        $stmt = $conn->prepare("
            UPDATE bookings 
            JOIN rooms ON bookings.room_id = rooms.room_id 
            JOIN room_types ON rooms.room_type_id = room_types.room_type_id 
            SET bookings.check_in = ?, bookings.check_out = ?, room_types.type_name = ? 
            WHERE bookings.booking_id = ?
        ");
        $stmt->bind_param("sssi", $check_in, $check_out, $room_type, $booking_id);
        if (!$stmt->execute()) {
            die("Error updating booking: " . $stmt->error);
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
                            <?php if (isset($_POST['edit_booking_id']) && $_POST['edit_booking_id'] == $booking['booking_id']): ?>
                                <tr>
                                    <form action="" method="post">
                                        <td><?php echo htmlspecialchars($booking['booking_id']); ?></td>
                                        <td><?php echo htmlspecialchars($booking['username']); ?></td>
                                        <td>
                                            <select name="room_type">
                                                <?php
                                                $roomTypes->getRoomTypes();
                                                foreach ($roomTypes as $roomType) {
                                                    $selected = $booking['room_type'] === $roomType['type_name'] ? 'selected' : '';
                                                    echo "<option value='{$roomType['type_name']}' $selected>{$roomType['type_name']}</option>";
                                                }
                                                ?>
                                            </select>
                                        </td>
                                        <td><input type="date" name="check_in" value="<?php echo htmlspecialchars($booking['check_in']); ?>"></td>
                                        <td><input type="date" name="check_out" value="<?php echo htmlspecialchars($booking['check_out']); ?>"></td>
                                        <td>
                                            <input type="hidden" name="booking_id" value="<?php echo htmlspecialchars($booking['booking_id']); ?>">
                                            <button type="submit" name="save_booking">Save</button>
                                            <button type="submit" name="cancel_edit" value="1">Cancel</button>
                                        </td>
                                    </form>
                                </tr>
                            <?php else: ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($booking['booking_id']); ?></td>
                                    <td><?php echo htmlspecialchars($booking['username']); ?></td>
                                    <td><?php echo htmlspecialchars($booking['room_type']); ?></td>
                                    <td><?php echo htmlspecialchars($booking['check_in']); ?></td>
                                    <td><?php echo htmlspecialchars($booking['check_out']); ?></td>
                                    <td>
                                        <form action="" method="post" style="display:inline-block;">
                                            <input type="hidden" name="edit_booking_id" value="<?php echo htmlspecialchars($booking['booking_id']); ?>">
                                            <button type="submit">Edit</button>
                                        </form>
                                        <form action="" method="post" style="display:inline-block;">
                                            <input type="hidden" name="delete_booking_id" value="<?php echo htmlspecialchars($booking['booking_id']); ?>">
                                            <button type="submit">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6">No bookings found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>