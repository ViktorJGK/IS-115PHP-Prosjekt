<?php
include '../db_connect.php'; // Include your database connection file
include '../Components/header.php'; // Include the header

function getUserProfile($user_id) {
    global $conn;
    $sql = "SELECT * FROM users WHERE user_id = '$user_id'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    } else {
        return null;
    }
}

$userProfile = null;
if (isset($_SESSION['user_id'])) {
    $userProfile = getUserProfile($_SESSION['user_id']);
}

$isAdmin = $userProfile && $userProfile['role'] == 1; // 'role' column indicates admin (1) or guest (0)
?>

<div class="container">
    <div class="profile">
        <?php if ($userProfile): ?>
            <?php if ($isAdmin): ?>
                <?php include 'profiles/admin_dashboard.php'; ?>
            <?php else: ?>
                <?php include 'profiles/guest_profile.php'; ?>
            <?php endif; ?>
        <?php else: ?>
            <p>Please <a href="login.php">log in</a> to view your profile.</p>
        <?php endif; ?>
    </div>
</div>

