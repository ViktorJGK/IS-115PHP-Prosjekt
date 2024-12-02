<?php
ob_start(); // Start output buffering

// include "../Admin/.php";

if (!isset($userProfile) || !$userProfile instanceof Admin) {
    header("Location: ../../logout.php");
    exit;
}

// Get all users from the database using the Admin class method
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

function getAllBookings($limit, $offset)
{
    global $conn;
    $sql = "SELECT bookings.booking_id, bookings.check_in, bookings.check_out, 
                   users.username, room_types.type_name AS room_type 
            FROM bookings 
            JOIN rooms ON bookings.room_id = rooms.room_id 
            JOIN room_types ON rooms.room_type_id = room_types.room_type_id 
            JOIN users ON bookings.user_id = users.user_id
            LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("SQL error: " . $conn->error);
    }

    $stmt->bind_param("ii", $limit, $offset);
    if (!$stmt->execute()) {
        die("Execution error: " . $stmt->error);
    }
    $result = $stmt->get_result();

    return $result && $result->num_rows > 0 ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

function getRoomTypes()
{
    global $conn;
    $result = $conn->query("SELECT type_name FROM room_types");
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

function getAllRooms() {
    global $conn;
    $sql = "SELECT room_id, room_number, type_name, is_available, unavailable_from, unavailable_to 
    FROM rooms 
    JOIN room_types ON rooms.room_type_id = room_types.room_type_id";
$result = $conn->query($sql);
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}



$limit = 10;
$offset = 0;
$allBookings = getAllBookings($limit, $offset);

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
                                                $roomTypes = getRoomTypes();
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

<h3>Alle rom</h3>
<table>
    <thead>
        <?php

        // Hent alle rom og vis dem i tabellen
        $allRooms = getAllRooms();
        
        // Håndtere forminnsending for oppdatering av rom
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['update_room'])) {
                $room_id = (int) $_POST['room_id'];
                $is_available = (int) $_POST['is_available'];
                $unavailable_from = $_POST['unavailable_from'] ?: null;
                $unavailable_to = $_POST['unavailable_to'] ?: null;
                $room_type_id = (int) $_POST['room_type_id'];
                $room_type_name = $_POST['room_type_name'];
                $room_description = $_POST['room_description'];
        
                // Start transaksjon
                $conn->begin_transaction();
        
                try {
                    // Oppdater tilgjengelighet og datoer for rommet
                    $stmt1 = $conn->prepare("UPDATE rooms 
                                             SET is_available = ?, unavailable_from = ?, unavailable_to = ? 
                                             WHERE room_id = ?");
                    $stmt1->bind_param("issi", $is_available, $unavailable_from, $unavailable_to, $room_id);
                    if (!$stmt1->execute()) {
                        throw new Exception("Error updating room availability: " . $stmt1->error);
                    }
        
                    // Oppdater romtype (navn og beskrivelse)
                    $stmt2 = $conn->prepare("UPDATE room_types 
                                             SET type_name = ?, description = ? 
                                             WHERE room_type_id = ?");
                    $stmt2->bind_param("ssi", $room_type_name, $room_description, $room_type_id);
                    if (!$stmt2->execute()) {
                        throw new Exception("Error updating room type: " . $stmt2->error);
                    }
        
                    // Commit transaksjonen
                    $conn->commit();
                    header('Location: ' . $_SERVER['PHP_SELF']);
                    exit;
        
                } catch (Exception $e) {
                    // Rollback hvis noe går galt
                    $conn->rollback();
                    die("Error: " . $e->getMessage());
                }
            }
        }
        ?>
        
        <!-- HTML for å vise rom i en tabell og oppdatere -->
        <table>
            <thead>
                <tr>
                    <th>Romnummer</th>
                    <th>Romtype</th>
                    <th>Beskrivelse</th>
                    <th>Tilgjengelighet</th>
                    <th>Endre</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($allRooms as $room): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($room['room_number']); ?></td>
                        <td><?php echo htmlspecialchars($room['type_name']); ?></td>
                        <td><?php echo htmlspecialchars($room['description']); ?></td>
                        <td>
                            <form method="post">
                                <input type="hidden" name="room_id" value="<?php echo $room['room_id']; ?>">
                                <select name="is_available">
                                    <option value="1" <?php echo $room['is_available'] == 1 ? 'selected' : ''; ?>>Tilgjengelig</option>
                                    <option value="0" <?php echo $room['is_available'] == 0 ? 'selected' : ''; ?>>Utilgjengelig</option>
                                </select>
                                <input type="date" name="unavailable_from" value="<?php echo $room['unavailable_from']; ?>">
                                <input type="date" name="unavailable_to" value="<?php echo $room['unavailable_to']; ?>">
                                <input type="hidden" name="room_type_id" value="<?php echo $room['room_type_id']; ?>">
                                <input type="text" name="room_type_name" value="<?php echo htmlspecialchars($room['type_name']); ?>" placeholder="Romtype navn">
                                <textarea name="room_description" placeholder="Beskrivelse"><?php echo htmlspecialchars($room['description']); ?></textarea>
                                <button type="submit" name="update_room">Oppdater Rom</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        


    </div>
</body>

</html>