<?php
ob_start(); // Start output buffering

include_once '../Components/functions/user_functions.php';

// Start session and ensure user authentication
if (!isset($userProfile) || !$userProfile instanceof Admin) {
    header("Location: ../../logout.php");
    exit;
}

// Fetch all users using Admin's method
$allUsers = $userProfile->getAllUsers();

// Handle form submission for saving or canceling user edits
$edit_user_id = isset($_POST['edit_user_id']) ? $_POST['edit_user_id'] : null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['cancel'])) {
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }

    if (isset($_POST['username'], $_POST['role'], $_POST['user_id'])) {
        $username = $_POST['username'];
        $role = (int) $_POST['role'];
        $user_id = (int) $_POST['user_id'];
        $userProfile->updateUser($user_id, $username, $role);
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
}

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

ob_end_flush(); // Send all output
?>


<html lang="en">

<head>
    <link rel="stylesheet" href="../css/admin.css">
    <title>Admin Dashboard</title>
</head>

<body>
    <div>
        <div>
            <h2>Admin Dashboard</h2>
            <p>Welcome, <?php echo htmlspecialchars($userProfile->getUsername()); ?>!</p>
            <p>Email: <?php echo htmlspecialchars($userProfile->getEmail()); ?></p>
            <br>

            <h3>All Users</h3>
            <table>
                <thead>
                    <tr>
                        <th>User ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($allUsers as $user): ?>
                        <?php if ($edit_user_id == $user['user_id']): ?>
                            <tr>
                                <form action="" method="post">
                                    <td><?php echo htmlspecialchars($user['user_id']); ?></td>
                                    <td><input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>"></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td>
                                        <select name="role">
                                            <option value="1" <?php echo $user['role'] == 1 ? 'selected' : ''; ?>>Admin</option>
                                            <option value="0" <?php echo $user['role'] == 0 ? 'selected' : ''; ?>>Guest</option>
                                        </select>
                                    </td>
                                    <td><?php echo htmlspecialchars($user['created_at']); ?></td>
                                    <td>
                                        <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user['user_id']); ?>">
                                        <button type="submit">Save</button>
                                        <button type="submit" name="cancel" value="1">Cancel</button>
                                    </td>
                                </form>
                            </tr>
                        <?php else: ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user['user_id']); ?></td>
                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><?php echo $user['role'] == 1 ? 'Admin' : 'Guest'; ?></td>
                                <td><?php echo htmlspecialchars($user['created_at']); ?></td>
                                <td>
                                    <form action="" method="post">
                                        <input type="hidden" name="edit_user_id" value="<?php echo htmlspecialchars($user['user_id']); ?>">
                                        <button type="submit">Edit</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>

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
        </div>

        </tr>
        </tbody>
        </table>

        <h3>All Roomtypes</h3>
        <?php
        // Check if the room type is being edited
        $edit_roomtype_id = isset($_POST['edit_roomtype_id']) ? (int)$_POST['edit_roomtype_id'] : null;

        // Fetch all room types
        $sql = "SELECT * FROM room_types";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            echo "<table border='1'>
            <tr>
                <th>Room Type</th>
                <th>Description</th>
                <th>Max Adults</th>
                <th>Max Children</th>
                <th>Actions</th>
            </tr>";

            // Loop through room types and display them
            while ($row = $result->fetch_assoc()) {
                if ($edit_roomtype_id == $row['room_type_id']) {
                    // Edit form
                    echo "<tr>
                    <form action='' method='post'>
                        <td><input type='text' name='type_name' value='" . htmlspecialchars($row['type_name']) . "'></td>
                        <td><input type='text' name='description' value='" . htmlspecialchars($row['description']) . "'></td>
                        <td><input type='number' name='max_adults' value='" . htmlspecialchars($row['max_adults']) . "' min='1'></td>
                        <td><input type='number' name='max_children' value='" . htmlspecialchars($row['max_children']) . "' min='0'></td>
                        <td>
                            <input type='hidden' name='room_type_id' value='" . htmlspecialchars($row['room_type_id']) . "'>
                            <button type='submit' name='update_roomtype'>Save</button>
                            <button type='submit' name='cancel_edit_roomtype' value='1'>Cancel</button>
                        </td>
                    </form>
                </tr>";
                } else {
                    // Display room type in normal view
                    echo "<tr>
                    <td>" . htmlspecialchars($row['type_name']) . "</td>
                    <td>" . htmlspecialchars($row['description']) . "</td>
                    <td>" . htmlspecialchars($row['max_adults']) . "</td>
                    <td>" . htmlspecialchars($row['max_children']) . "</td>
                    <td>
                        <form action='' method='post' style='display:inline-block;'>
                            <input type='hidden' name='edit_roomtype_id' value='" . htmlspecialchars($row['room_type_id']) . "'>
                            <button type='submit'>Edit</button>
                        </form>
                    </td>
                </tr>";
                }
            }
            echo "</table>";
        } else {
            echo "No room types found.";
        }
        ?>

        <?php
        // Handle form submissions for updating room type
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // Handle room type update
            if (isset($_POST['update_roomtype'], $_POST['room_type_id'], $_POST['type_name'], $_POST['description'], $_POST['max_adults'], $_POST['max_children'])) {
                $room_type_id = (int)$_POST['room_type_id'];
                $type_name = $_POST['type_name'];
                $description = $_POST['description'];
                $max_adults = (int)$_POST['max_adults'];
                $max_children = (int)$_POST['max_children'];

                // Update the room type in the database
                $stmt = $conn->prepare("UPDATE room_types 
                                SET type_name = ?, description = ?, max_adults = ?, max_children = ? 
                                WHERE room_type_id = ?");
                $stmt->bind_param("ssiii", $type_name, $description, $max_adults, $max_children, $room_type_id);

                if ($stmt->execute()) {
                    
                } else {
                    die("Error updating room type: " . $stmt->error);
                }
            }
        }
        ?>

        <h3>All Rooms</h3>
        <table>
            <thead>
                <tr>
                    <th>Room ID</th>
                    <th>Room Number</th>
                    <th>Type</th>
                    <th>Available</th>
                    <th>Unavailable From</th>
                    <th>Unavailable To</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $allRooms = $userProfile->getAllRooms();
                foreach ($allRooms as $room): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($room['room_id']); ?></td>
                        <td><?php echo htmlspecialchars($room['room_number']); ?></td>
                        <td><?php echo htmlspecialchars($room['type_name']); ?></td>
                        <td><?php echo $room['is_available'] ? 'Yes' : 'No'; ?></td>
                        <td><?php echo $room['unavailable_from'] ?: 'N/A'; ?></td>
                        <td><?php echo $room['unavailable_to'] ?: 'N/A'; ?></td>
                        <td>
                            <form action="" method="post">
                                <input type="hidden" name="room_id" value="<?php echo htmlspecialchars($room['room_id']); ?>">
                                <label for="is_available">Available:</label>
                                <select name="is_available">
                                    <option value="1" <?php echo $room['is_available'] == 1 ? 'selected' : ''; ?>>Yes</option>
                                    <option value="0" <?php echo $room['is_available'] == 0 ? 'selected' : ''; ?>>No</option>
                                </select><br>
                                <label for="unavailable_from">Unavailable From:</label>
                                <input type="date" name="unavailable_from" value="<?php echo htmlspecialchars($room['unavailable_from']); ?>"><br>
                                <label for="unavailable_to">Unavailable To:</label>
                                <input type="date" name="unavailable_to" value="<?php echo htmlspecialchars($room['unavailable_to']); ?>"><br>
                                <button type="submit" name="update_room">Update</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>


    </div>
</body>

</html>