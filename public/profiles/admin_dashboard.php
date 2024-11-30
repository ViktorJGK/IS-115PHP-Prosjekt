<?php
// This script is for the admin dashboard, displaying all users and bookings from the database.

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Inkluder databasekonfigurasjonen her (antatt at $conn er definert riktig)
require_once '../db_connect.php'; // Ensure this path is correct

// Test databaseforbindelsen
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
} else {
    echo "Database connection successful!<br>";
}

// Funksjon for å hente alle brukere
function getAllUsers($limit, $offset)
{
    global $conn;
    $sql = "SELECT user_id, username, email, role, created_at FROM users LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("SQL error: " . $conn->error);
    }

    $stmt->bind_param("ii", $limit, $offset);
    if (!$stmt->execute()) {
        die("Execution error: " . $stmt->error);
    }
    $result = $stmt->get_result();
    if ($result && $result->num_rows > 0) {
        return $result->fetch_all(MYSQLI_ASSOC);
    } else {
        return [];
    }
}

// Funksjon for å hente alle bookinger
function getAllBookings($limit, $offset)
{
    global $conn;
    $sql = "SELECT bookings.booking_id, bookings.check_in, bookings.check_out, 
                   bookings.room_id, bookings.user_id, users.username 
            FROM bookings 
            JOIN rooms ON bookings.room_id = rooms.room_id 
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

    if ($result && $result->num_rows > 0) {
        return $result->fetch_all(MYSQLI_ASSOC);
    } else {
        return [];
    }
}



$role_mapping = [
    'admin' => 'Administrator',
    'user' => 'User',
    'guest' => 'Guest'
];

$limit = 10; // Set your desired limit
$offset = 0; // Set your desired offset
$allUsers = getAllUsers($limit, $offset);
$allBookings = getAllBookings($limit, $offset);

/* Funksjon for debugging
function debugData($allUsers, $allBookings)
{
    echo '<h3>Debugging: All Users</h3>';
    echo '<pre>';
    print_r($allUsers);
    echo '</pre>';

    echo '<h3>Debugging: All Bookings</h3>';
    echo '<pre>';
    print_r($allBookings);
    echo '</pre>';
}

// Kall funksjonen for debugging
debugData($allUsers, $allBookings);*/
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        table, th, td {
            border: 1px solid black;
            border-collapse: collapse;
            padding: 8px;
        }
    </style>
</head>
<body>
    <div>
        <h2>Admin Dashboard</h2>

        <!-- Seksjon for brukere -->
        <h3>All Users</h3>
        <table>
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($allUsers)): ?>
                    <?php foreach ($allUsers as $user): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['user_id']); ?></td>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo htmlspecialchars($role_mapping[$user['role']] ?? 'Unknown'); ?></td>
                            <td><?php echo htmlspecialchars($user['created_at']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">Ingen brukere funnet.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Seksjon for bookinger -->
        <h3>All Bookings</h3>
        <table>
            <thead>
                <tr>
                    <th>Booking ID</th>
                    <th>Username</th>
                    <th>Room Type</th>
                    <th>Check-in</th>
                    <th>Check-out</th>
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
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">Ingen bookinger funnet.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php
$users = getAllUsers(10, 0); // Example call to fetch the first 10 users

if (!empty($users)) {
    foreach ($users as $user) {
        echo "User ID: " . htmlspecialchars($user['user_id']) . "<br>";
        echo "Username: " . htmlspecialchars($user['username']) . "<br>";
        echo "Email: " . htmlspecialchars($user['email']) . "<br>";
        echo "Role: " . htmlspecialchars($user['role']) . "<br>";
        echo "Created At: " . htmlspecialchars($user['created_at']) . "<br><br>";
    }
} else {
    echo "No users found.";
}
?>
