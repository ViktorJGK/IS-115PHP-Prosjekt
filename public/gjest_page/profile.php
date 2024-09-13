<?php
include '../../db_connect.php'; // Include your database connection file
include '../../Components/header.php'; // Include the header

function addLoyaltyPoints($user_id, $points) {
    global $conn;
    $sql = "UPDATE loyalty_program SET points = points + $points WHERE user_id = '$user_id'";
    if ($conn->query($sql) === TRUE) {
        echo "Points added successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

function getGuestProfile($user_id) {
    global $conn;
    $sql = "SELECT * FROM users WHERE user_id = '$user_id'"; // Updated column name
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    } else {
        return null;
    }
}

$userProfile = null;
if (isset($_SESSION['user_id'])) {
    $userProfile = getGuestProfile($_SESSION['user_id']);
}
?>

<div class="container">
    <div class="profile">
        <?php if ($userProfile): ?>
            <h2>Velkommen, <?php echo htmlspecialchars($userProfile['username']); ?>!</h2>
            <p>Email: <?php echo htmlspecialchars($userProfile['email']); ?></p>
            <!-- Add more profile details here -->
        <?php else: ?>
            <p>Vennligst <a href="login.php">logg inn</a> for å se din profil.</p>
        <?php endif; ?>
    </div>
</div>