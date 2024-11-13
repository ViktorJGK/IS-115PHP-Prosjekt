<?php
include '../db_connect.php'; // Include the database connection
include '../Components/header.php'; // Include the header
include '../Components/functions/user_functions.php'; // Include the file with User, Admin, and Guest classes

// Initialize $userProfile based on session data
$userProfile = null;

if (isset($_SESSION['user_id'])) { // Check if the user is logged in
    $user_id = $_SESSION['user_id'];
    
    // Check the user's role from the database
    $sql = "SELECT role FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $userRole = $result->fetch_assoc()['role'] ?? null;
    
    // Initialize user as Admin or Guest based on role
    if ($userRole === 1) {
        $userProfile = new Admin($user_id, $conn);
    } elseif ($userRole === 0) {
        $userProfile = new Guest($user_id, $conn);
    }
}

$isAdmin = $userProfile instanceof Admin; // Check if the user is an Admin
?>

<div class="container">
    <div class="profile">
        <!-- Logic for which profile to display -->
        <?php if ($userProfile): ?>
            <?php if ($isAdmin): ?>
                <?php include 'profiles/admin_dashboard.php'; ?>
                <link rel="stylesheet" href="../css/admin.css">
            <?php else: ?>
                <?php include 'profiles/guest_profile.php'; ?>
            <?php endif; ?>
        <?php else: ?>
            <p>Please <a href="login.php">log in</a> to view your profile.</p>
        <?php endif; ?>
    </div>
</div>