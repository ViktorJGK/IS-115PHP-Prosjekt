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
        <?php
        if (!$userProfile) {
            // Redirect guests to login
            header('Location: login.php');
            exit();
        }
        
        if ($isAdmin) {
            // Redirect admins to the admin dashboard
            include "profiles/admin_dashboard.php";
        } else {
            // Redirect guests to their profile
            include "profiles/guest_profile.php";
            exit();
        }
        ?>

    </div>
</div>